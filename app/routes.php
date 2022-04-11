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

$routes[] = ['/', 'HomeController@index'];
$routes[] = ['/about', 'HomeController@about'];

$routes[] = ['/devices', 'DevicesController@index', 'auth'];
$routes[] = ['/device/{id}/show', 'DevicesController@show'];
$routes[] = ['/device/create', 'DevicesController@create', 'auth'];
$routes[] = ['/device/store', 'DevicesController@store', 'auth'];
$routes[] = ['/device/{id}/edit', 'DevicesController@edit', 'auth'];
$routes[] = ['/device/{id}/getCert', 'DevicesController@getCert', 'auth'];
$routes[] = ['/device/{id}/update', 'DevicesController@update', 'auth'];
$routes[] = ['/device/{id}/delete', 'DevicesController@delete', 'auth'];
$routes[] = ['/device/{id}/sync', 'DevicesController@sync'];

$routes[] = ['/users', 'UsersController@index', 'auth'];
$routes[] = ['/user/{id}/shedule', 'UsersController@shedule', 'auth'];
$routes[] = ['/user/{id}/sync', 'UsersController@sync'];
$routes[] = ['/user/create', 'UsersController@create'];
$routes[] = ['/user/store', 'UsersController@store'];
$routes[] = ['/user/{id}/show', 'UsersController@show', 'auth'];
$routes[] = ['/user/{id}/edit', 'UsersController@edit', 'auth'];
$routes[] = ['/user/{id}/getCert', 'UsersController@getCert', 'auth'];
$routes[] = ['/user/{id}/update', 'UsersController@update', 'auth'];
$routes[] = ['/user/{id}/delete', 'UsersController@delete', 'auth'];
$routes[] = ['/user/{id}/disconnect', 'UsersController@disconnect', 'auth'];
$routes[] = ['/user/{id}/disable', 'UsersController@disable', 'auth'];
$routes[] = ['/user/{id}/enable', 'UsersController@enable', 'auth'];
$routes[] = ['/user/{id}/remove', 'UsersController@remove', 'auth'];

$routes[] = ['/login', 'UsersController@login'];
$routes[] = ['/login/auth', 'UsersController@auth'];
$routes[] = ['/logout', 'UsersController@logout'];

return $routes;