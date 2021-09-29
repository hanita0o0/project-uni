<?php
use app\core\Application;
class M01_Create_Products_Table
{
    public function up(){
        $db = Application::$app->db;
        $SQL = "CREATE TABLE products(
              id INT AUTO_INCREMENT PRIMARY KEY,
              name VARCHAR(50) NOT NULL,
              model VARCHAR(50) ,
              description  VARCHAR (255),   
              image VARCHAR(50),
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
            ) ENGINE=INNODB;";
        $db->pdo->exec($SQL);
    }

    public function down(){
        $db = Application::$app->db;
        $SQL ="DROP TABLE products;";
        $db->pdo->exec($SQL);
    }
}