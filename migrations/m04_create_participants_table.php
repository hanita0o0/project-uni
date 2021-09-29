<?php
use app\core\Application;
class M04_Create_Participants_Table
{
    public function up(){
        $db = Application::$app->db;
        $SQL = "CREATE TABLE participants(
              id INT AUTO_INCREMENT PRIMARY KEY,
              bid_capacity INTEGER NOT NULL, 
              user_id INTEGER, FOREIGN KEY(user_id) REFERENCES users(id),
              auction_id INTEGER, FOREIGN KEY(auction_id) REFERENCES auctions(id),
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
            ) ENGINE=INNODB;";
        $db->pdo->exec($SQL);
    }

    public function down(){
        $db = Application::$app->db;
        $SQL ="DROP TABLE participants;";
        $db->pdo->exec($SQL);
    }
}