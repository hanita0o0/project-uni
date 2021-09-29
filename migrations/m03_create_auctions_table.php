<?php
use app\core\Application;
class M03_Create_Auctions_Table
{
    public function up(){
        $db = Application::$app->db;
        $SQL = "CREATE TABLE auctions(
              id INT AUTO_INCREMENT PRIMARY KEY,
              code VARCHAR (50) NOT NULL,
              price integer NOT NULL,
              wage integer NOT NULL,
              base_price integer NOT NULL,
              start_at  TIMESTAMP NOT NULL,
              winner VARCHAR (50),
              registration_cost integer,
              capacity_participants integer NOT NULL,
              final_price integer,
              final_time VARCHAR(50),
              product_id integer, FOREIGN KEY(product_id) REFERENCES products(id),
              status_id  integer, FOREIGN KEY(status_id) REFERENCES status(id),
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
            ) ENGINE=INNODB;";
        $db->pdo->exec($SQL);
    }

    public function down(){
        $db = Application::$app->db;
        $SQL ="DROP TABLE auctions;";
        $db->pdo->exec($SQL);
    }
}