<?php


namespace app\core;


class Database
{
    public \PDO $pdo;


    /**
     * Database constructor.
     */
    public function __construct(array $conf)
    {
        // connect to database
        $dsn = $conf['dsn'] ?? '';
        $user = $conf['user'] ?? '';
        $password = $conf['password'] ?? '';
        $this->pdo = new \PDO($dsn,$user,$password);
        //when connection to database has problem throw error
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

    }
    // apply files in migration folder to the database
    public function applyMigrations(){
        $this->createMigrationsTable();
        $appliedMigrations =  $this->getAppliedMigrations();
        $newMigrations = [];
        //files list of migrations folder to apply
        $files = scandir(Application::$root_dir . "/migrations");
        $toApplyMigrations = array_diff($files,$appliedMigrations);

        foreach ($toApplyMigrations as $migration){
            if($migration === "." || $migration === ".."){
                continue;
            }
            require_once Application::$root_dir.'/migrations/'.$migration;
            $className = pathinfo($migration,PATHINFO_FILENAME);
            $instance = new $className();
            $this->log('applying migration '.$migration);
            $instance->up();
            $this->log('applied migration '.$migration);
            $newMigrations[] = $migration;
        }
        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else{
            $this->log("all migrations are applied");
        }

    }
    //if one file apply to the database this function control to not re-apply to the database
    public function createMigrationsTable(){
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations(
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255),
    created_at TIMESTAMP DEFAULT  CURRENT_TIMESTAMP
    )ENGINE=INNODB;");
    }
    public function getAppliedMigrations(){
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }
    public function saveMigrations(array $migrations){

        $str =implode(",", array_map(fn($m) => "('$m')",$migrations));

        $statement = $this->pdo->prepare("INSERT INTO migrations(migration) VALUES
               $str 
               ");
        $statement->execute();
    }
    protected function log($message){
        echo '['.date('Y-m-d H:i:s').']-'.$message.PHP_EOL;
    }
}