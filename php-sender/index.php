<?php
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;


class rabbit{
    public $channel;
    public $connection;
    public static $instance;
    public static function getInstance(){
        if(self::$instance){
            return self::$instance;
        }
        new rabbit();
        return self::$instance;
    }
    public function __construct() {        
        if(self::$instance){
            return self::$instance;
        }
        $exchange = 'hernya';
        $queue = 'kek';
        $this->connection = new AMQPStreamConnection("localhost", "5672", "rmuser", "rmpassword", "/");
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($queue, true, true, false, true);
        $this->channel->exchange_declare($exchange, 'fanout', false, true, true);

        $this->channel->queue_bind($queue, $exchange);
        self::$instance = $this;
    }
     public function send($str){
        $exchange = 'hernya';

        $messageBody = $str;
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->channel->basic_publish($message, $exchange);

        $this->channel->close();
        $this->connection->close();
    }
}

rabbit::getInstance()->send("hello world");
