<?php
use app\core\Application;
class M02_Create_Status_Table
{
    public function up(){
        $db = Application::$app->db;
        $SQL = "CREATE TABLE status(
              id INT AUTO_INCREMENT PRIMARY KEY,
              name VARCHAR(50) NOT NULL
            ) ENGINE=INNODB;";
        $db->pdo->exec($SQL);
    }

    public function down(){
        $db = Application::$app->db;
        $SQL ="DROP TABLE status;";
        $db->pdo->exec($SQL);
    }
}