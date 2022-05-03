<?php
    use \Ratchet\Server\IoServer;
    use \Ratchet\Http\HttpServer;
    use \Ratchet\WebSocket\WsServer;
    use \Ratchet\MessageComponentInterface;
    use \Ratchet\ConnectionInterface;
    
    require  __DIR__ .  '/../vendor/autoload.php';


    class Chat implements MessageComponentInterface {

        private $clients;
    
        public function __construnct() {
            $this->clients = array();
        }
    
        public function onOpen(ConnectionInterface $conn) {
            $this->clients[] = $conn;
    
            echo "New Connection";
        }
    
        public function onMessage(ConnectionInterface $from, $msg) {

            echo "onMessage";
    
            foreach($this->clients as $client) {
                if ($client != $from) {
                    $client->send($msg);
                }
            }
    
        }
    
        public function onClose(ConnectionInterface $conn) {
    
            echo "Connection closed";
    
        }
    
        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo $e->getMessage();
        }
    }




    
    $server = IoServer::factory(
                                new HttpServer(
                                               new WsServer(
                                                            new Chat()
                                                            )
                                               ),
                                8080
                                );
    
    $server->run();
?>