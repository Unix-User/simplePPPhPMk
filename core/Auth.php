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

/**
 * Description of Auth
 *
 * @author Weverton
 */
class Auth {

    private static $id = null;
    private static $url = null;
    private static $name = null;
    private static $email = null;

    public function __construct() {
        if (Session::get('user')) {
            $user = Session::get('user');
            self::$id = $user['id'];
            self::$name = $user['name'];
            self::$url = $user['url'];
            self::$email = $user['email'];
        }
    }

    public static function id() {
        return self::$id;
    }

    public static function name() {
        return self::$name;
    }

    public static function url() {
        return self::$url;
    }

    public static function email() {
        return self::$email;
    }

    public static function check() {
        if (self::$id == null || self::$name == null || self::$email == null) {
            return false;
        }
        return true;
    }

}
