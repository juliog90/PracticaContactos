<?php

require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');
require_once('email_type.php');

class Email
{
    private $id;
    private $address;
    private $email_type;
    private $contact_id;

    public function getId()
    {
        return $this->id;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddresss($address)
    {
        $this->address = $address;
    }

    public function getEmailType()
    {
        return $this->email_type;
    }

    public function setEmailType($email_type)
    {
        $this->email_type = $email_type;
    }

    public function __construct()
    {
        if(func_num_args() == 0)
        {
            $this->id = 0;
            $this->address = "";
            $this->email_type = 0;
        }

        if(func_num_args() == 1)
        {
            $connection = MySqlConnection::getConnection();
            $query = 'select id, address, email_type_id, contact_id from emails where id = :id';
            $command = $connection->prepare($query);
            $id = func_get_arg(0);
            $command->bindParam(':id',$id);
            $command->execute();
            $result = $command->fetch(PDO::FETCH_ASSOC);

            if($result)
            {
                $this->id = $result['id'];
                $this->address = $result['address'];
                $this->email_type = new EmailType($result['email_type_id']);
                $this->contact_id = $result['contact_id'];
            }
            else
                throw new RecordNotFoundException(func_get_arg(0));
            $connection = null;
            $command = null;
        }

        if(func_num_args() == 4)
        {
            $this->id = func_get_arg(0);
            $this->address = func_get_arg(1);
            $this->email_type = new EmailType(func_get_arg(2));
            $this->contact_id = func_get_arg(3);
        }

        if(func_num_args() == 3)
        {
            $this->id = func_get_arg(0);
            $this->address = func_get_arg(1);
            $this->email_type = func_get_arg(2);
            $this->contact_id = 0;
        }

        if(func_num_args() == 2)
        {
            $this->id = 0;
            $this->email_type = func_get_arg(0);
            $this->address = func_get_arg(1);
        }
    }

    public function toJson()
    {
        return json_encode(array(
            'id' => $this->id,
            'address' => $this->address,
            'email_type' => json_decode($this->email_type->toJson())
        ));
    }

    public static function getAll()
    {
        $emails = array();
        $connection = MySqlConnection::getConnection();
        $query = 'select id, address, email_type_id, contact_id from emails';
        $command = $connection->prepare($query);
        $command->execute();
        $result = $command->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;
        $command = null;

        if($result)
        {
            foreach($result as $email)
                {
                    array_push($emails, new Email($email['id'], $email['address'], $email['email_type_id'], $email['contact_id']));
                }
            }
        else
            throw new RecordNotFoundException(func_get_arg(0));

        return $emails;
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
