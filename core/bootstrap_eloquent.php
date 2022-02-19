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

use Illuminate\Database\Capsule\Manager as Capsule;

$conf = require_once __DIR__ . "/../app/database.php";

    $capsule = new Capsule;

    if ($conf['driver'] == 'mysql') {
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $conf['mysql']['host'],
            'database' => $conf['mysql']['database'],
            'username' => $conf['mysql']['user'],
            'password' => $conf['mysql']['pass'],
            'charset' => $conf['mysql']['charset'],
            'collation' => $conf['mysql']['collation'],
            'prefix' => '',
        ]);
    } elseif ($conf['driver'] == 'sqlite') {
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__ . "/../storage/database/" . $conf['sqlite']['database']
        ]);
    }
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
