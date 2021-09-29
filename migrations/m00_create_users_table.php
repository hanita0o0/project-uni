<?php
use app\core\Application;
class M00_Create_Users_Table
{
    public function up(){
        $db = Application::$app->db;
        $SQL = "CREATE TABLE users(
              id INT AUTO_INCREMENT PRIMARY KEY,
              mobile VARCHAR(15) NOT NULL,
              verification_code VARCHAR(10),
              verified  TINYINT(1) NOT NULL DEFAULT 0,
              username  VARCHAR(50),
              firstname VARCHAR(50),
              lastname VARCHAR(50),
              email VARCHAR(50) ,
              introduced_no VARCHAR(15), 
              wallet integer DEFAULT 0 ,
              image VARCHAR(50),
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
            ) ENGINE=INNODB;";
        $db->pdo->exec($SQL);
    }


    public function down(){
        $db = Application::$app->db;
        $SQL ="DROP TABLE users;";
        $db->pdo->exec($SQL);
    }
}