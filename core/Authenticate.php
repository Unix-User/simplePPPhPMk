<<<<<<< HEAD
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

namespace Core;

use App\Models\User;

trait Authenticate
{

    public function login()
    {
        if (isset($_SESSION['user'])) {
            return Redirect::route('/');
        }
        $this->setPageTitle('Login');
        return $this->renderView('users/login', 'layout');
    }

    public function auth($request)
    {
        $result = User::where('email', $request->post->email)->first();
        if ($result && password_verify($request->post->password, $result->password)) {
            $user = [
                'id' => $result->id,
                'name' => $result->name,
                'url' => $result->url,
                'email' => $result->email,
                'owner_id' => $result->owner_id,
            ];
            Session::set('user', $user);
            return Redirect::route('/');
        }
        return Redirect::route('/login', [
            'errors' => ['Úsuário ou senha incorretos'],
            'inputs' => ['email' => $request->post->email]
        ]);
    }

    public function logout()
    {
        Session::destroy('hotspot');
        Session::destroy('user');
        Session::destroy('cli');
        return Redirect::route('/login');
    }
}
=======
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

namespace Core;

use App\Models\User;

trait Authenticate
{

    public function login()
    {
        if (isset($_SESSION['user'])) {
            return Redirect::route('/');
        }
        $this->setPageTitle('Login');
        return $this->renderView('users/login', 'layout');
    }

    public function auth($request)
    {
        $result = User::where('email', $request->post->email)->first();
        if ($result && password_verify($request->post->password, $result->password)) {
            $user = [
                'id' => $result->id,
                'name' => $result->name,
                'url' => $result->url,
                'email' => $result->email,
                'owner_id' => $result->owner_id,
            ];
            Session::set('user', $user);
            return Redirect::route('/');
        }
        return Redirect::route('/login', [
            'errors' => ['Úsuário ou senha incorretos'],
            'inputs' => ['email' => $request->post->email]
        ]);
    }

    public function logout()
    {
        Session::destroy('hotspot');
        Session::destroy('user');
        Session::destroy('cli');
        return Redirect::route('/login');
    }
}
>>>>>>> b0472aa6aa2ca3a79035f2b8ecfec1c3f4e267c0
