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
}
