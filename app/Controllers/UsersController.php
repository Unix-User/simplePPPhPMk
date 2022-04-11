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
use Core\BaseController;
use Core\Auth;
use Core\Redirect;
use Core\Validator;
use Core\Authenticate;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of ProductController
 *
 * @author Weverton
 */
class UsersController extends BaseController
{

    use Authenticate;

    private $user;
    private $devices;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User;
        $this->device = new Device;
    }

    public function index()
    {
        $this->setPageTitle("Administradores");
        $this->view->users = $this->user->All();
        if (Auth::check()) {
            $this->view->user = $this->user->find(Auth::id());
            $this->view->devices = $this->device->all();
            return $this->renderView("users/index", 'layout');
        }
        return Redirect::route('/login', ['errors' => ['Acesso administrativo requisitado.']]);
    }

    public function show($id)
    {
        $this->view->user = $this->user->find($id);
        $this->view->devices = $this->device->all();
        $this->setPageTitle("{$this->view->user->name}");
        return $this->renderView("users/show", "layout");
    }

    public function create()
    {
        $this->setPageTitle('Novo usuário');
        return $this->renderView("users/create", 'layout');
    }

    public function getCert($id)
    {
        $client = $this->user->find($id)->name;
        $downloadable_file_stream = $this->filesystem->readStream('/backend/storage/' . $client . '.p12');
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
            'name' => $request->post->name,
            'email' => $request->post->email,
            'password' => $request->post->password,
            'url' => $request->post->url
        ];
        if (Validator::make($data, $this->user->rulesCreate())) {
            return Redirect::route('/user/create');
        }

        $data['password'] = password_hash($request->post->password, PASSWORD_BCRYPT);

        try {
            $this->user->create($data);
            $this->cliCert('--addclient', $data['name'], null);
            return Redirect::route('/users', [
                'success' => ['Usuário cadastrado com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/users', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function edit($id)
    {
        $this->view->user = $this->user->find($id);
        if (Auth::id() == $this->view->user->id) {
            $this->setPageTitle('Editar dados - ' . $this->view->user->name);
            return $this->renderView('users/edit', 'layout');
        }
        return Redirect::route('/users', ['errors' => ['Esses dados estão indisponiveis no momento.']]);
    }

    public function update($id, $request)
    {
        $data = [
            'name' => $request->post->name,
            'email' => $request->post->email,
            'password' => $request->post->password,
            'url' => $request->post->url,
        ];

        if (Validator::make($data, $this->user->rulesUpdate($id))) {
            return Redirect::route("/user/{$id}/edit");
        }
        $data['password'] = password_hash($request->post->password, PASSWORD_BCRYPT);
        if ($request->post->password == "") {
            $data['password'] = $this->user->find($id)->password;
        }

        try {
            $this->user->find($id)->update($data);
            $this->cliCert('--exportclient', $data['name'], null);
            return Redirect::route('/users', [
                'success' => ['Dados do usuário foram atualizados com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/users', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $user = $this->user->find($id);
            $this->manager->write('local://backend/storage/' . $user->name . '.conf', '');
            $this->cliCert('--revokeclient', $user->name, null);
            $this->manager->delete('local://backend/storage/' . $user->name . '.conf');
            $this->user->find($id)->delete();
            return Redirect::route('/users', [
                'success' => ['Usuário excluido com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/users', [
                'errors' => [$ex->getMessage()]
            ]);
        }
    }

    public function disconnect($name)
    {
        $this->util->setMenu('/ppp active');
        try {
            $this->util->remove(\PEAR2\Net\RouterOS\Query::where('name', $name));
            return Redirect::route('/devices', [
                'success' => ['Usuário desconectado com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/devices', [
                'errors' => ['Erro ao remover usuário: ' . $name . ' - msg:' . $ex->getMessage()]
            ]);
        }
    }

    public function enable($name)
    {
        $this->util->setMenu('/ppp secret');
        try {
            $this->util->enable(\PEAR2\Net\RouterOS\Query::where('name', $name));
            return Redirect::route('/devices', [
                'success' => ['Usuário habilitado com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/devices', [
                'errors' => ['Erro ao habilitar usuário: ' . $name . ' - msg:' . $ex->getMessage()]
            ]);
        }
    }

    public function disable($name)
    {
        $this->util->setMenu('/ppp secret');
        try {
            $this->util->disable(\PEAR2\Net\RouterOS\Query::where('name', $name));
            return Redirect::route('/devices', [
                'success' => ['Usuário desabilitado com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/devices', [
                'errors' => ['Erro ao desabilitar usuário ' . $name . ' - msg:' . $ex->getMessage()]
            ]);
        }
    }

    public function remove($name)
    {
        $this->util->setMenu('/ppp secret');
        try {
            $this->util->remove(\PEAR2\Net\RouterOS\Query::where('name', $name));
            return Redirect::route('/devices', [
                'success' => ['Usuário removido com sucesso']
            ]);
        } catch (\Exception $ex) {
            return Redirect::route('/devices', [
                'errors' => ['Erro ao remover usuário: ' . $name . ' - msg:' . $ex->getMessage()]
            ]);
        }
    }

    public function shedule($id, $request)
    {
        if (Auth::id() != $id) {
            return Redirect::route('/users', [
                'errors' => ['Não foi possivel solicitar o agendamento de conexão.']
            ]);
        }
        $this->view->user = $this->user->find($id);
        if ((isset($request)) && ($request->post != '')) {
            $data = [
                'password' => $request->post->password,
                'time' => $request->post->time,
                'device' => $request->post->device
            ];
            $this->view->device = $this->device->find($data['device']);
            $output = shell_exec('crontab -l');
            $lines = explode(PHP_EOL, $output);
            $new_string = ' curl -X POST https://srv.unixlocal.ml/user/' . $this->view->device->id . '/sync  -d "password=' . $data['password'] . '"';
            $new_crontab = '';
            foreach ($lines as $line) {
                if (strpos($line, $new_string) === false) {
                    $new_crontab .= $line . PHP_EOL;
                }
            }
            $new_task = $data['time'] . $new_string;
            $new_crontab .= $new_task . PHP_EOL;
            $this->manager->write('local://backend/storage/crontab.txt', $new_crontab);
            $this->runCommand(['crontab', '/var/www/backend/storage/crontab.txt']);
            return Redirect::route('/devices', [
                'success' => ['Agendamento de conexão realizado com sucesso']
            ]);            
        } else {
            $this->view->devices = $this->device->all();
            $this->setPageTitle('Agendamento de conexão - ' . $this->view->user->name);
            return $this->renderView('users/shedule', 'layout');
        }
    }

    public function sync($id, $request)
    {
        header('Content-Type: application/json');
        //get device data from device id
        $device = $this->device->find($id);
        //get user data from device
        $user = $this->user->find($device->user_id);
        if ($user && password_verify($request->post->password, $user->password)) {
            //get url from user
            $url = $user->url;
            //send post request to url
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=' . $user->email . '&password=' . $request->post->password);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = explode('|', substr(curl_exec($ch), 0, -1));
            curl_close($ch);
            //get data from response
            foreach ($output as $record) {
                $url = "https://srv.unixlocal.ml/device/". $id ."/sync";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $headers = array(
                   "Content-Type: application/x-www-form-urlencoded",
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $record);
                curl_exec($curl);
                curl_close($curl);
            }
        } else {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
    }
}
