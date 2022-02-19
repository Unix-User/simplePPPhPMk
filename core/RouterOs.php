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
 * Description of Auth
 *
 * @author Weverton
 */
class RouterOs
{

    private static $username = null;
    private static $mac = null;
    private static $ip = null;
    private static $linklogin = null;
    private static $linkorig = null;
    private static $error = null;
    private static $chapid = null;
    private static $chapchallenge = null;
    private static $linkloginonly = null;
    private static $linkorigesc = null;
    private static $macesc = null;

    public function __construct()
    {
        if (Session::get('hotspot')) {
            $hotspot = Session::get('hotspot');
            self::$mac = $hotspot['mac'];
            self::$ip = $hotspot['ip'];
            self::$linklogin = $hotspot['linklogin'];
            self::$linkorig = $hotspot['linkorig'];
            self::$error = $hotspot['error'];
            self::$chapid = $hotspot['chapid'];
            self::$chapchallenge = $hotspot['chapchallenge'];
            self::$linkloginonly = $hotspot['linkloginonly'];
            self::$linkorigesc = $hotspot['linkorigesc'];
            self::$macesc = $hotspot['macesc'];
            self::$username = $hotspot['username'];
        }
    }

    public static function mac()
    {
        return self::$mac;
    }

    public static function ip()
    {
        return self::$ip;
    }

    public static function linklogin()
    {
        return self::$linklogin;
    }
    public static function linkorig()
    {
        return self::$linkorig;
    }

    public static function error()
    {
        return self::$error;
    }

    public static function chapid()
    {
        return self::$chapid;
    }
    public static function chapchallenge()
    {
        return self::$chapchallenge;
    }

    public static function linkloginonly()
    {
        return self::$linkloginonly;
    }

    public static function linkorigesc()
    {
        return self::$linkorigesc;
    }

    public static function macesc()
    {
        return self::$macesc;
    }

    public static function username()
    {
        return self::$username;
    }

    public static function checkin()
    {
        if (self::$linklogin == null) {
            return false;
        }
        return true;
    }
}
