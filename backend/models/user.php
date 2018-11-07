<?php

require_once('connection.php');
require_once('role.php');
require_once('exception/recordnotfoundexception.php');

class User
{
    private $id;
    private $name;
    private $password;
    private $photo;
    private $role;

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

    public function getPassword()
    {
        return $this->password;
    }
    
    public function getPhoto()
    {
        return $this->photo;
    }
    
    public function setPhoto($photo)
    {
        return $this->photo = $photo;
    }

    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole($role)
    {
        return $this->role = $role;
    }

    public function __construct()
    {
        if(func_num_args())
        {
            $this->id = 0;
            $this->name = "";
            $this->password = "";
            $this->photo = "";
            $this->role = "";
        }

        
        if(func_num_args() == 2)
        {
            $connection = MySqlConnection::getConnection();
            $query = "select u.id, u.name, u.photo, r.id, r.name from users as u inner join roles as r on u.idRole = r.id where u.id = ':id' and u.password = sha1(:password)";
            $command = $connection->prepare($query);
            $id = $this->id;
            $command->prepare(':id', $id);
            $command->prepare(':password', $password);
            $command->execute();

            $user = $command->fetch(PDO_FETCH_ASSOC);

            $command = null;
            $connection = null;

            if($role)
            {
                $this->id = $user['id'];
                $this->name = $user['name'];
                $this->password = $user['password'];
                $this->photo = $user['photo'];
                $this->role = new Role($user['idRole']);
            }
            else
            {

       
            }
        }

        if(func_num_args() == 5)
        {
            $this->id = func_get_arg(0);
            $this->name = func_get_arg(1);
            $this->password = func_get_arg(2);
            $this->photo = func_get_arg(3);
            $this->role = func_get_arg(4);
        }
    }

    public function toJson()
    {
        return json_encode(array(
            'id' => $this->id,
            'name' => $this->name
        ));
    }

}
?>
