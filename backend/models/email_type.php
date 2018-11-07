<?php
require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');

class EmailType
{
    private $id;
    private $description;

    public function getid()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function __construct()
    {
        if(func_num_args())
        {
           $this->id = 0;
           $this->description = ""; 
        }

        if(func_num_args() == 1) 
        {
            $connection = MySqlConnection::getConnection();
            $query = 'select id, description from email_types where id = :id';
            $command = $connection->prepare($query);
            $id = func_get_arg(0);
            $command->bindParam(':id', $id);
            $command->execute();
            $type = $command->fetch(PDO::FETCH_ASSOC);

            $this->id = $type['id'];
            $this->description = $type['description'];

        }

       /* if(func_num_args(3)) */
       /* { */
       /*     $this->id = func_get_arg(0); */
       /*     $this->description = func_get_arg(1); */
       /* } */

       if(func_num_args() == 2)
       {
           $this->id = func_get_arg(0);
           $this->description = func_get_arg(1);
       }

    }

    public static function getAll()
    {
        $list = array();
        $connection = MySqlConnection::getConnection();
        $query = 'select id, description from email_types';
        $command = $connection->prepare($query);
        $command->execute();
        $types = $command->fetchAll(PDO::FETCH_ASSOC);
        $command = null;
        $connection = null;

        foreach($types as $type)
            array_push($list, new EmailType($type['id'], $type['description']));

        return $list;
    }

    public function toJson()
    {
        return json_encode(array('id' => $this->id,
                                  'description' => $this->description
                          ));
    }

    public static function getAllToJson()
    {
        $jsonArray = array();
        foreach(self::getAll() as $item) 
        {
            array_push($jsonArray, json_decode($item->toJson()));
        }

        return json_encode($jsonArray);
    }
}
?>
