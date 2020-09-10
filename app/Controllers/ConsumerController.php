<?php


namespace App\Controllers;

use Kernel\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumerController extends Controller
{
    private $connection;
    private $channel;

    public function __construct($data = null, $database = null)
    {
        parent::__construct($data, $database);
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();

        $this->channel->queue_declare('hello', false, false, false, false);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    protected function get_index()
    {
//        $result = [];
//        foreach ($urls as $url) {
//            $result[$url] = $this->get_http_code($url);
//        }

        $callback = function($url){
            $headers = get_headers($url);
//            return $headers[0];
        };


        $this->channel->basic_consume('hello', '', false, true, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

    }

}

