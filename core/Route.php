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

/**
 * Description of Route
 *
 * @author Weverton
 */

namespace Core;

class Route {

    private $routes;

    public function __construct(array $routes) {
        $this->setRoute($routes);
        $this->run();
    }

    private function setRoute($routes) {
        foreach ($routes as $route){
            $explode = explode('@', $route[1]);
            if(isset($route[2])){
                $r = [$route[0], $explode[0], $explode[1], $route[2]];
            }else{
                $r = [$route[0], $explode[0], $explode[1]];
            }
            $newRoutes[] = $r;
        }
        $this->routes = $newRoutes;
    }

    private function getRequest() {
        $obj = new \stdClass;
        foreach ($_GET as $key => $value) {
            @$obj->get->$key = $value;
        }
        foreach ($_POST as $key => $value) {
            @$obj->post->$key = $value;
        }
        return $obj;
    }

    private function getUrl() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function run() {
        $url = $this->getUrl();
        $urlArray = explode('/', $url);
        foreach ($this->routes as $route) {
            $routeArray = explode('/', $route[0]);
            $param = [];
            for ($i = 0; $i < count($routeArray); $i++) {
                if ((strpos($routeArray[$i], '{') !== FALSE) && (count($urlArray) == count($routeArray))) {
                    $routeArray[$i] = $urlArray[$i];
                    $param[] = $urlArray[$i];
                }
                $route[0] = implode('/', $routeArray);
            }
            if($url == $route[0]){
                $found = true;
                $controller = $route[1];
                $action = $route[2];
                $auth = new Auth;
                if(isset($route[3]) && $route[3] == 'auth' && !$auth->check()){
                    $action = 'forbiden';
                }
                break;
            }
        }
        if (isset($found)) {
            $controller = Container::newController($controller);
            switch (count($param)) {
                case 1:
                    $controller->$action($param[0], $this->getRequest());
                    break;
                case 2:
                    $controller->$action($param[0], $param[1], $this->getRequest());
                    break;
                case 3:
                    $controller->$action($param[0], $param[1], $param[2], $this->getRequest());
                    break;
                default :
                    $controller->$action($this->getRequest());
                    break;
            }
        } else {
            Container::pageNotFound();
        }
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

/**
 * Description of Route
 *
 * @author Weverton
 */

namespace Core;

class Route {

    private $routes;

    public function __construct(array $routes) {
        $this->setRoute($routes);
        $this->run();
    }

    private function setRoute($routes) {
        foreach ($routes as $route){
            $explode = explode('@', $route[1]);
            if(isset($route[2])){
                $r = [$route[0], $explode[0], $explode[1], $route[2]];
            }else{
                $r = [$route[0], $explode[0], $explode[1]];
            }
            $newRoutes[] = $r;
        }
        $this->routes = $newRoutes;
    }

    private function getRequest() {
        $obj = new \stdClass;
        foreach ($_GET as $key => $value) {
            @$obj->get->$key = $value;
        }
        foreach ($_POST as $key => $value) {
            @$obj->post->$key = $value;
        }
        return $obj;
    }

    private function getUrl() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function run() {
        $url = $this->getUrl();
        $urlArray = explode('/', $url);
        foreach ($this->routes as $route) {
            $routeArray = explode('/', $route[0]);
            $param = [];
            for ($i = 0; $i < count($routeArray); $i++) {
                if ((strpos($routeArray[$i], '{') !== FALSE) && (count($urlArray) == count($routeArray))) {
                    $routeArray[$i] = $urlArray[$i];
                    $param[] = $urlArray[$i];
                }
                $route[0] = implode('/', $routeArray);
            }
            if($url == $route[0]){
                $found = true;
                $controller = $route[1];
                $action = $route[2];
                $auth = new Auth;
                if(isset($route[3]) && $route[3] == 'auth' && !$auth->check()){
                    $action = 'forbiden';
                }
                break;
            }
        }
        if (isset($found)) {
            $controller = Container::newController($controller);
            switch (count($param)) {
                case 1:
                    $controller->$action($param[0], $this->getRequest());
                    break;
                case 2:
                    $controller->$action($param[0], $param[1], $this->getRequest());
                    break;
                case 3:
                    $controller->$action($param[0], $param[1], $param[2], $this->getRequest());
                    break;
                default :
                    $controller->$action($this->getRequest());
                    break;
            }
        } else {
            Container::pageNotFound();
        }
    }

}
>>>>>>> b0472aa6aa2ca3a79035f2b8ecfec1c3f4e267c0
