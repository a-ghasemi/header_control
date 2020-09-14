<?php


namespace App\Support;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Pusher\Pusher;

class CheckHeader
{
    private $connection;
    private $channel;
    private $pusher;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();

        $this->channel->queue_declare('hello', false, false, false, false);


        $options = array(
            'cluster' => 'ap1',
            'useTLS'  => true
        );
        $this->pusher = new Pusher(
            '46c790f03cdc593b6f3f',
            '2841fae1fd7f71053b0a',
            '1072770',
            $options
        );

        echo "Checker is ready to receive ...\n";
    }

//    private function __destruct()
//    {
//        $this->channel->close();
//        $this->connection->close();
//    }

    public function main()
    {

/*
https://google.com
https://google.com/wer
https://onlinebootcamp.maktabsharif.ir
https://trello.com
http://asghar.com
https://fb.com
*/

        $callback = function(AMQPMessage $url){

            try{
                $headers = get_headers($url->getBody());
            }catch(\Exception $e){
            }

            $message = $url->getBody() . ' : ' . ($headers[0] ?? 'timeout');
            $this->pusher->trigger('my-channel', 'my-event', $message);
        };


        $this->channel->basic_consume('hello', '', false, true, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

    }

}

