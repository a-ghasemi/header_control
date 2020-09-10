<?php


namespace App\Controllers;

use Kernel\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CheckController extends Controller
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
        return view('home.check');
    }

    protected function post_index()
    {
        $urls = $this->data['post_data']['asghar'];
        $urls = explode("\n", trim($urls));
        $urls = array_map('trim', $urls);

////// Messaging to Consumer
        foreach($urls as $url){
            $msg = new AMQPMessage($url);
            $this->channel->basic_publish($msg, '', 'hello');
        }

        return view('home.check', [
//            'akbar' => $result,
//            'input' => array_keys($result)
        ]);
    }

    private function get_http_code($url){
        $headers = get_headers($url);
        return $headers[0];//hello
    }
}