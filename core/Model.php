<?php


namespace app\core;


class Model
{

    public function loadData($data){
        foreach($data as $key =>$value){
            if (property_exists($this,$key)){
                $this->{$key} = $value;
            }
        }
    }
    /*
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */

    public function checkRow($tblName,$conditions = []){
        $sql = 'SELECT * FROM '.$tblName;
        if(!empty($conditions) && is_array($conditions)){
            $sql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $sql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }

        $db = Application::$app->db;
        $stmt = $db->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

         if($result){
             return $result;
         }else{
             return false;
         }

    }

//        // set the resulting array to associative
//        $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
//        var_dump($result);

    /*
    * Insert data into the database
    * @param string name of the table
    * @param array the data for inserting into the table
    */
    public function insert($tblName,$data)
    {
        $db = Application::$app->db;
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values = '';
            $i = 0;
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values .= $pre . "'" . $val . "'";
                $i++;
            }

            $sql = "INSERT INTO " . $tblName . " (" . $columns . ") VALUES (" . $values . ")";
            $stmt = $db->pdo->prepare($sql);
            $result = $stmt->execute();
            //$result is bool even if fetch that
//            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result) {
                return true;
            } else {
                return false;
            }

        }
    }
    /*
    * Update data into the database
    * @param string name of the table
    * @param array the data for updating into the table
    * @param array where condition on updating data
    */
    public function update($tblName,$data,$conditions)
    {
        if (!empty($data) && is_array($data)) {
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $colvalSet .= $pre . $key . "='" . $val . "'";
                $i++;
            }

            if (!empty($conditions) && is_array($conditions)) {
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach ($conditions as $key => $value) {
                    $pre = ($i > 0) ? ' AND ' : '';
                    $whereSql .= $pre . $key . " = '" . $value . "'";
                    $i++;
                }
            }

            $db = Application::$app->db;
            $sql = "UPDATE " . $tblName . " SET " . $colvalSet . $whereSql;
            $stmt = $db->pdo->prepare($sql);
            $result = $stmt->execute();
            //$result is bool even if fetch that
//            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($result) {
                return true;
            } else {
                return false;
            }

        }
    }

    public function checkAllRow($tblName,$conditions = []){
        $sql = 'SELECT * FROM '.$tblName;
        if(!empty($conditions) && is_array($conditions)){
            $sql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $sql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }

        $db = Application::$app->db;
        $stmt = $db->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result){

            return $result;
        }else{
            return false;
        }

    }
}