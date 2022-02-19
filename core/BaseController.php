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

use PEAR2\Net\RouterOS;


/**
 * Description of BaseController
 *
 * @author Weverton
 */


abstract class BaseController
{

    protected $view;
    protected $auth;
    protected $inputs;
    protected $success;
    protected $util;
    protected $client;
    private $viewPath;
    private $layoutPath;
    private $pageTitle = null;


    public function __construct()
    {
        $this->view = new \stdClass;
        $this->auth = new Auth;


        if (Session::get('errors')) {
            $this->errors = Session::get('errors');
            Session::destroy('errors');
        }
        if (Session::get('inputs')) {
            $this->inputs = Session::get('inputs');
            Session::destroy('inputs');
        }
        if (Session::get('success')) {
            $this->success = Session::get('success');
            Session::destroy('success');
        }
        if (Session::get('cli')) {
            $data = Session::get('cli');
            Session::destroy('cli');
            if (isset($data)) {
                $this->util = new RouterOS\Util(
                    $this->client = new RouterOS\Client($data['address'], $data['user'], $data['pass'])
                );
            }
        }
    }

    protected function renderView($viewPath, $layoutPath = null)
    {
        $this->viewPath = $viewPath;
        $this->layoutPath = $layoutPath;
        if ($layoutPath) {
            return $this->layout();
        } else {
            return $this->content();
        }
    }

    protected function content()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->viewPath}.phtml")) {
            return require_once __DIR__ . "/../app/Views/{$this->viewPath}.phtml";
        } else {
            echo "Error: view path not found";
        }
    }

    protected function layout()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->layoutPath}.phtml")) {
            return require_once __DIR__ . "/../app/Views/{$this->layoutPath}.phtml";
        } else {
            echo "Error: layout path not found";
        }
    }

    protected function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    protected function setCliTarget($address, $user, $pass)
    {
        Session::destroy('cli');
        $data = [
            'address' => $address,
            'user' => $user,
            'pass' => $pass
        ];

        try {
            $this->util = new RouterOS\Util(
                $this->client = new RouterOS\Client($data['address'], $data['user'], $data['pass'])
            );
            Session::set('cli', $data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    protected function getPageTitle($separator = null)
    {
        if ($separator) {
            return $this->pageTitle . " " . $separator . " ";
        } else {
            return $this->pageTitle;
        }
    }

    public function forbiden()
    {
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

use PEAR2\Net\RouterOS;


/**
 * Description of BaseController
 *
 * @author Weverton
 */


abstract class BaseController
{

    protected $view;
    protected $auth;
    protected $inputs;
    protected $success;
    protected $util;
    protected $client;
    private $viewPath;
    private $layoutPath;
    private $pageTitle = null;


    public function __construct()
    {
        $this->view = new \stdClass;
        $this->auth = new Auth;


        if (Session::get('errors')) {
            $this->errors = Session::get('errors');
            Session::destroy('errors');
        }
        if (Session::get('inputs')) {
            $this->inputs = Session::get('inputs');
            Session::destroy('inputs');
        }
        if (Session::get('success')) {
            $this->success = Session::get('success');
            Session::destroy('success');
        }
        if (Session::get('cli')) {
            $data = Session::get('cli');
            Session::destroy('cli');
            if (isset($data)) {
                $this->util = new RouterOS\Util(
                    $this->client = new RouterOS\Client($data['address'], $data['user'], $data['pass'])
                );
            }
        }
    }

    protected function renderView($viewPath, $layoutPath = null)
    {
        $this->viewPath = $viewPath;
        $this->layoutPath = $layoutPath;
        if ($layoutPath) {
            return $this->layout();
        } else {
            return $this->content();
        }
    }

    protected function content()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->viewPath}.phtml")) {
            return require_once __DIR__ . "/../app/Views/{$this->viewPath}.phtml";
        } else {
            echo "Error: view path not found";
        }
    }

    protected function layout()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->layoutPath}.phtml")) {
            return require_once __DIR__ . "/../app/Views/{$this->layoutPath}.phtml";
        } else {
            echo "Error: layout path not found";
        }
    }

    protected function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    protected function setCliTarget($address, $user, $pass)
    {
        Session::destroy('cli');
        $data = [
            'address' => $address,
            'user' => $user,
            'pass' => $pass
        ];

        try {
            $this->util = new RouterOS\Util(
                $this->client = new RouterOS\Client($data['address'], $data['user'], $data['pass'])
            );
            Session::set('cli', $data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    protected function getPageTitle($separator = null)
    {
        if ($separator) {
            return $this->pageTitle . " " . $separator . " ";
        } else {
            return $this->pageTitle;
        }
    }

    public function forbiden()
    {
        return Redirect::route('/login');
    }
}
>>>>>>> b0472aa6aa2ca3a79035f2b8ecfec1c3f4e267c0
