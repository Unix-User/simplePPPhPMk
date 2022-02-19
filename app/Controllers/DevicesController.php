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
        if ($this->setCliTarget($this->view->device->address, $this->view->device->user, $this->view->device->password) == true) {
            $this->util->setMenu('/ppp secret');
            $this->view->users = $this->util->getAll();
            $this->util->setMenu('/ppp active');
            $this->view->active = $this->util->getAll();
            $this->setPageTitle("{$this->view->device->name}");
            return $this->renderView("devices/show", "layout");
        }
        return Redirect::route('/devices', [
            'errors' => ['Dispositivo '. $this->view->device->name .' não esta conectado']
        ]);
    }

    public function create()
    {
        $this->setPageTitle('Novo Dispositivo');
        return $this->renderView("devices/create", 'layout');
    }

    public function store($request)
    {
        $data = [
            'user_id' => Auth::id(),
            'name' => $request->post->device,
            'address' => $request->post->address
        ];
        if (Validator::make($data, $this->device->rules())) {
            return Redirect::route('/device/create');
        }
        try {
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
            'name' => $request->post->device,
            'address' => $request->post->address,
            'user' => $request->post->user,
            'password' => $request->post->password
        ];

        if (Validator::make($data, $this->device->rules())) {
            return Redirect::route("/device/{$id}/edit");
        }

        try {
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
