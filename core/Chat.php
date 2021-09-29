<?php
namespace app\core;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    private static int $bid =0 ;
    private static  int $target;
    private static string $winner = "";
    private static int  $auction_id = 0;
    private static int $current =0;
    protected $clients;
    protected Database $database;
    protected JwtHandler $jwt;

    public function __construct() {
        self::$target = time()+60;
        echo self::$target.PHP_EOL;
        $config = [
            'db'=>[
                'dsn' => $_ENV['DB_DSN'],
                'user'=> $_ENV['DB_USERNAME'],
                'password'=>$_ENV['DB_PASSWORD'],
            ],

        ];
        $this->jwt = new JwtHandler();
        $this->clients = new \SplObjectStorage;
        $this->database = new Database($config['db']);
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }


    public function onMessage(ConnectionInterface $from, $msg)
    {
           self::$current = time();
           echo "target before if : ".self::$target .PHP_EOL;
           echo "now before if : ".self::$current.PHP_EOL;
           echo "moment : ".self::$target - self::$current.PHP_EOL ;
           //if sender periods of 10 seconds send message,accepted after expired not accepted
           if(self::$target - self::$current >=0) {
               echo "xxxxxxxxxxx".PHP_EOL;
               $data = json_decode($msg);
               $token = $this->jwt->_jwt_decode_data(trim($data->token));
               $user_id = $token->id;
               $auction_id = $data->auction_id;
               $query = "SELECT * FROM participants WHERE user_id = :user_id AND
                         auction_id = :auction_id";
               $stm = $this->database->pdo->prepare($query);
               $stm->bindParam(":user_id", $user_id, \PDO::PARAM_STR);
               $stm->bindParam(":auction_id", $auction_id, \PDO::PARAM_STR);
               $stm->execute();
               $result = $stm->fetch(\PDO::FETCH_ASSOC);
               $bid_capacity = $result['bid_capacity'];
               echo $bid_capacity;
               if ($bid_capacity > 0) {
                   self::$target = self::$current + 20;
                   echo "new target after if : " . self::$target . "......" . PHP_EOL . PHP_EOL;
                   self::$bid = self::$bid + $data->base_price;
                   $ret = [
                       'username' => $token->username,
                       'image' => $token->image,
                       'time' => date("H:i:s",self::$current),
                       'bid' => self::$bid
                   ];
                   $data = json_encode($ret);
                   //reduce bid_capacity of participants
                   $bid = $bid_capacity - 1;
                   $sql = "UPDATE participants SET bid_capacity = :bid_capacity
                                WHERE auction_id = :auction_id AND user_id = :user_id";
                   $stmt = $this->database->pdo->prepare($sql);
                   $stmt->bindParam(":bid_capacity", $bid, \PDO::PARAM_INT);
                   $stmt->bindParam(":auction_id", $auction_id, \PDO::PARAM_INT);
                   $stmt->bindParam(":user_id", $user_id, \PDO::PARAM_INT);
                   $result = $stmt->execute();
//                   echo var_dump($result);
                   self::$winner = $token->mobile;
                   self::$auction_id = $auction_id;

                   foreach ($this->clients as $client) {
                       $client->send($data);

                   }

               }
           }
           else{
               //insert winner in auction
               $name = "برگزارشده";
               $qq = "SELECT * FROM status WHERE name = :name";
               $row = $this->database->pdo->prepare($qq);
               $row->bindParam(":name", $name, \PDO::PARAM_STR);
               $row->execute();
               $result = $row->fetch(\PDO::FETCH_ASSOC);
               $status_id = $result['id'];
               echo $status_id.PHP_EOL;
               $query = "UPDATE auctions SET winner = :winner,final_price=:final_price,
                    final_time=:final_time,status_id = :status_id WHERE id = :auction_id ";
               $stm = $this->database->pdo->prepare($query);
               $final_time =date('Y-m-d H:i:s',self::$current);
               $stm->bindParam(":winner", self::$winner, \PDO::PARAM_STR);
               $stm->bindParam(":auction_id", self::$auction_id, \PDO::PARAM_STR);
               $stm->bindParam(":final_price", self::$bid, \PDO::PARAM_STR);
               $stm->bindParam(":final_time",$final_time , \PDO::PARAM_STR);
               $stm->bindParam(":status_id",$status_id , \PDO::PARAM_STR);
               $result = $stm->execute();
               if ($result) {
                   echo self::$winner.PHP_EOL;
               }

           }
    }
//

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
