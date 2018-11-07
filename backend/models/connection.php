<?php
    class MysqlConnection
    {
        public static function getConnection()
        {
            $data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/web4a2018/classes07/config/connection.json');
            $config = json_decode($data, true);

            /* if (isset($config['server'])) { */
            /*     $server = $config['server']; */
            /* } else { */
            /*     echo "Configuration error : Mysql server name not found"; */
            /*     die; */
            /* } */

            if (isset($config['user'])) {
                $user=$config['user'];
            } else {
                echo "Configuration error: Mysql user name not found";
                die;
            }

            if (isset($config['password'])) {
                $contra=$config['password'];
            } else {
                echo "Configuration error: Mysql user password not found";
                die;
            }

            /* if (isset($config['database'])) { */
            /*     $database=$config['database']; */
            /* } else { */
            /*     echo "Configuration error : Mysql database not found"; */
            /*     die; */
            /* } */

            try {
                $connection = new PDO("mysql:host=localhost;dbname=webClass2", $user, $contra);
            } catch (PDOException $e) {
                echo "Error ".$e->getMessage() ."<br/>";
                die();
            }
            

            return $connection;
        }
    }
?>




