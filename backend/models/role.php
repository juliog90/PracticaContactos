<?php

require_once('connection.php');
require_once('exception/recordnotfoundexception.php');

class Role
{

    private $id;
    private $name;

    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        return $this->name = $name;
    }

    public function __construct()
    {
        if(func_num_args())
        {
            $this->id = 0;
            $this->name = "";
        }

        
        if(func_num_args() == 1)
        {
            $connection = MySqlConnection::getConnection();
            $query = "select id, name from roles where id = :id";
            $command = $connection->prepare($query);
            $id = $this->id;
            $command->prepare(':id', $id);
            $command->execute();
            $role = $command->fetch(PDO_FETCH_ASSOC);

            $command = null;
            $connection = null;

            $this->id = $role['id'];
            $this->name = $role['name'];
        }

        if(func_num_args() == 2)
        {
            $this->id = func_get_arg(0);
            $this->name = func_get_arg(1);
        }
    }

    public function toJson()
    {
        return json_encode(array(
            'id' => $this->id,
            'name' => $this->name
        ));
    }

    public static function getAll()
    {
        $connection = MySqlConnection::getConnection();
        $query = "select id, name from roles";
        $command = $connection->prepare($query);
        $command->execute();
        $roles = $command->fetchAll(PDO_FETCH_ASSOC);

        $command = null;
        $connection = null;
    }

    public function add()
    {
            $connection = MySqlConnection::getConnection();
            $query = "insert into role(id, name) values(:id, :name)";
            $command = $connection->prepare($query);
            $id = $this->id;
            $name = $this->name;
            $command->prepare(':id', $id);
            $command->prepare(':name', $name);
            $command->execute();

            $command = null;
            $connection = null;
    }

    public function delete()
    {
            $connection = MySqlConnection::getConnection();
            $query = "delete from role where id = :id";
            $command = $connection->prepare($query);
            $id = $this->id;
            $command->prepare(':id', $id);
            $command->execute();
            $command = null;
            $connection = null;
    }

    public function update()
    {
            $connection = MySqlConnection::getConnection();
            $query = "update role set id = :id, name = :name";
            $command = $connection->prepare($query);
            $id = $this->id;
            $name = $this->name;
            $command->prepare(':id', $id);
            $command->prepare(':name', $name);
            $command->execute();
            $command = null;
            $connection = null;
    }
}
?>
