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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

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

        $this->adapter = new LocalFilesystemAdapter(
            __DIR__ . '/../../',
            PortableVisibilityConverter::fromArray([
                'file' => [
                    'public' => 0640,
                    'private' => 0604,
                ],
                'dir' => [
                    'public' => 0740,
                    'private' => 7604,
                ],
            ]),
            LOCK_EX,
            LocalFilesystemAdapter::DISALLOW_LINKS
        );
        $this->filesystem = new Filesystem($this->adapter);
        $this->manager = new MountManager([
            'local' => $this->filesystem
        ]);
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

    protected function checkCliTarget($address)
    {
        $ports = array(8728, 8729);
        foreach ($ports as $port) {
            $connection = @fsockopen($address, $port, $errno, $errstr, 1);
            if (is_resource($connection)) {
                fclose($connection);
                return true;
            } else {
                return false;
            }
        }
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

    protected function cliCert($action, $client, $address)
    {
        $this->process = new Process(['sudo', '-u', 'www-data', 'sudo', '/usr/bin/ikev2.sh',  $action, $client]);
        $this->process1 = new Process(['sudo', '-u', 'www-data', 'sudo', '/usr/bin/certutil', '-F', '-d', 'sql:/etc/ipsec.d', '-n', $client]);
        $this->process2 = new Process(['sudo', '-u', 'www-data', 'sudo', '/usr/bin/certutil', '-D', '-d', 'sql:/etc/ipsec.d', '-n', $client, '2>/dev/null']);
        $this->process3 = new Process(['sudo', '-u', 'www-data', 'sudo', 'systemctl', 'restart', 'ipsec.service',]);
        $content = "conn $client" . PHP_EOL. "  rightid=@$client" . PHP_EOL. "  rightaddresspool=$address-$address" . PHP_EOL. "  also=ikev2-cp" . PHP_EOL;
        try {
            $this->process->run();
            $this->process->getOutput();
            if ($action == "--revokeclient") {
                $content == '';
                $this->process1->run();
                $this->process1->getOutput();
                $this->process2->run();
                $this->process2->getOutput();
            }
            $this->process3->run();
            $this->process3->getOutput();
            $this->manager->delete('local://backend/storage/' . $client . '.p12');
            $this->manager->delete('local://' . $client . '.sswan');
            $this->manager->delete('local://' . $client . '.mobileconfig');
            $this->manager->write('local://backend/storage/' . $client . '.conf', $content);
            $this->manager->move('local://' . $client . '.p12', 'local://backend/storage/' . $client . '.p12');
        } catch (ProcessFailedException $exception) {
            return $exception->getMessage();
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
