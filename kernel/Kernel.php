<?php

namespace Kernel;

use Kernel\DB;

class Kernel
{
    private $url;
    private $data;
    private $database;

    static $env;
    static $global_errors;

    public function __construct()
    {
        Self::$env = (new EnvParser(base_path(".env")))->parse();

        if(env_get('DEBUG_MODE',false)) {
            ini_set('display_errors',1);
            ini_set('display_startup_errors',1);
            error_reporting(E_ALL);
        }
        else {
//            ini_set('display_errors',0);
//            ini_set('display_startup_errors',0);
            @error_reporting(0);
        }

        @session_start();

        $this->connectDatabase(
            env_get('DB_USER'),
            env_get('DB_PASS'),
            env_get('DB_NAME'),
            env_get('DB_HOST', 'localhost'),
            env_get('DB_PORT', 3306),
        );

        $this->data["request_type"] = $_SERVER['REQUEST_METHOD'];

        $this->data["get_data"] = $_GET;
        $this->data["post_data"] = $_POST;

        //TODO: using HTTP_HOST & REQUEST_URI has security problem, change this as soon as possible
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = explode('/', parse_url(strtolower(trim($url)))['path']);
        array_shift($url);

        $tmp = array_shift($url);
        $this->url['class'] = (!empty($tmp)) ? $tmp : 'home';
        $tmp = array_shift($url);
        $this->url['method'] = (!empty($tmp)) ? $tmp : 'index';
        $this->url['params'] = $url ?? [];

        $this->data["url"] = $this->url;
    }

    private function connectDatabase($user, $pass, $db_name, $host = 'localhost', $port = '3306')
    {
        $this->database = new DB($host, $port, $user, $pass, $db_name);
        $this->database->connect();
        if($this->database->error){
            Self::$global_errors[] = "Database Connection Failed!";
        }
    }

    public function run()
    {
        $controller = "\\App\\Controllers\\" . ucwords($this->url['class']) . 'Controller';

        if (!class_exists($controller)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            return;
        }

        $page = new $controller($this->data, $this->database);
        $ret = $page->run();

        if (is_object($ret)) {
            switch (get_class($ret)) {
                case 'Kernel\View':
                    $ret->getContent();
                    break;
                case 'Kernel\Redirect':
                    $ret->go();
                    break;
                default:
                    print("Kernel Error: Class " . get_class($ret) . " is not cased yet.");
            }
        } elseif (is_string($ret)) {
            @ob_start();
            print($ret);
            @ob_flush();
        } elseif (empty($ret)) {
            print('Kernel Warning: Content is Empty!');
        }

    }
}