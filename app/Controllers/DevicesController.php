<?php

/*
 * The MIT License
 *
 * Copyright 2020 Weverton.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace App\Controllers;

use App\Models\Device;
use App\Models\User;
use Core\Auth;
use Core\BaseController;
use Core\Redirect;
use Core\Validator;
use PEAR2\Net\RouterOS;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 *
 * @author Weverton
 */
class DevicesController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->device = new Device;
        $this->user = new User;
    }

    public function index()
    {
        $this->setPageTitle("Dispositivos");
        $this->view->user = $this->user->find(Auth::id());
        $this->view->devices = $this->device->All();
        return $this->renderView("devices/index", 'layout');
    }

    public function show($id)
    {
        $this->view->device = $this->device->find($id);
        if ($this->checkCliTarget($this->view->device->address)){
            $this->setCliTarget($this->view->device->address, $this->view->device->user, $this->view->device->password);
            $this->util->setMenu('/ppp secret');
            $this->view->users = $this->util->getAll();
            $this->util->setMenu('/ppp active');
            $this->view->active = $this->util->getAll();
            $this->setPageTitle("{$this->view->device->name}");
            return $this->renderView("devices/show", "layout");
        }
        return Redirect::route('/devices', [
            'errors' => ['Dispositivo ' . $this->view->device->name . ' não esta conectado']
        ]);
    }

    public function create()
    {
        $this->setPageTitle('Novo Dispositivo');
        return $this->renderView("devices/create", 'layout');
    }

    public function getCert($id)
    {
        $client = $this->device->find($id)->name;
        $downloadable_file_stream = $this->filesystem->readStream('/' . $client . '.p12');
        $this->downloadable_file_stream_contents = stream_get_contents($downloadable_file_stream);
        $response = new Response($this->downloadable_file_stream_contents);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $client . '.p12'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->send();
    }


    public function store($request)
    {
        $data = [
            'user_id' => Auth::id(),
            'name' => $request->post->name,
            'address' => $request->post->address,
            'user' => $request->post->user,
            'password' => $request->post->password,
            'ikev2' => $request->post->ikev2
        ];

        if (Validator::make($data, $this->device->rulesCreate())) {
            return Redirect::route('/device/create');
        }

        try {
            if ((isset($request->post->ikev2)) && ($request->post->ikev2 == true)) {
                $this->cliCert('--addclient', $data['name'], $data['address']);
            }
            $device = $this->device->create($data);
            if (isset($request->post->category_id)) {
                $device->category()->attach($request->post->category_id);
            }
            return Redirect::route('/devices', [
                'success' => ['Dados inseridos com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/devices', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }


    public function edit($id)
    {
        $this->view->device = $this->device->find($id);
        if (Auth::id() != $this->view->device->user_id) {
            return Redirect::route('/devices', [
                'errors' => ['Esses dados estão indisponiveis no momento.']
            ]);
        }
        $this->setPageTitle('Editar dados - ' . $this->view->device->device);
        return $this->renderView('devices/edit', 'layout');
    }

    public function update($id, $request)
    {
        $data = [
            'name' => $request->post->name,
            'address' => $request->post->address,
            'user' => $request->post->user,
            'password' => $request->post->password,
            'ikev2' => $request->post->ikev2
        ];

        if (Validator::make($data, $this->device->rulesUpdate($id))) {
            return Redirect::route("/device/{$id}/edit");
        }

        try {
            if ((isset($data['ikev2'])) && ($data['ikev2'] == true)) {
                echo $data['ikev2'];
                $this->cliCert('--addclient', $data['name'], $data['address']);
            }
            $device = $this->device->find($id);
            $device->update($data);
            $this->device->find($id)->update($data);
            return Redirect::route('/devices', [
                'success' => ['Dados atualizados com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/devices', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $device = $this->device->find($id);
            if (Auth::id() != $device->user_id) {
                return Redirect::route('/devices', [
                    'errors' => ['Você não pode excluir dispositivos de outros usuários.']
                ]);
            }
            if ((isset($device->ikev2)) && ($device->ikev2 == true)) {
                $this->manager->write('local://html/storage/' . $device->name . '.conf', '');
                $this->cliCert('--revokeclient', $device->name, $device->address);
            }
            $device->delete();
            return Redirect::route('/devices', [
                'success' => ['Dispositivo excluído com sucesso!']
            ]);
        } catch (\Exception $e) {
            return Redirect::route('/devices', [
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function sync($id, $data)
    {
        $device = $this->device->find($id);
        $this->setCliTarget($device->address, $device->user, $device->password);
        try {
            foreach ($data->post as $key => $value) {
                $user = json_decode($key);
            }
            $expires = strtotime($user->expires);
            $disable = strtotime('+5 days', $expires);
            if ($expires < strtotime(date('Y-m-d'))) {
                $profile = 'notificar';
                if (strtotime(date('Y-m-d')) >= $disable) {
                    $profile = 'bloqueio';
                }
            } else {
                $profile = $user->name;
            }
            $this->util->setMenu('/ppp profile');
            $this->util->remove(RouterOS\Query::where('name', $user->name));
            $this->util->add(
                array(
                    'name' => $user->name,
                    'rate-limit' => $user->rate
                )
            );
            $this->util->setMenu('/ppp secret');
            $this->util->remove(RouterOS\Query::where('name', $user->name));
            $this->util->add(
                array(
                    'name' => $user->name,
                    'password' => $user->install_password,
                    'profile' => $profile
                )
            );
            echo 'Usuário ' . $user->name . ' criado com sucesso';
        } catch (\Exception $ex) {
            echo 'Erro ao criar usuário ' . $user->name . ' - msg:' . $ex->getMessage();
        }
    }
}
