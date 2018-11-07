<?php
require_once('connection.php');
require_once('phone_number_type.php');


    class PhoneNumber {
        //attributes
        private $id;
        private $type;
        private $number;
        private $contactId;

        //getters and setters
        public function getId() { return $this->id; }
        public function getType() { return $this->type; }
        public function setType($type) { $this->type = $type; }
        public function getNumber() { return $this->number; }
        public function setNumber($number) { $this->number = $number; }
        public function getContactId()
        {
            return $this->contactId;
        }

        public function setContactId($contactId)
        {
            $this->contactId = $contactId;
        }

        //constructor
        public function __construct() {
            //0 arguments received
            if (func_num_args()) {
                $this->id = 0;
                $this->type = new PhoneNumberType();
                $this->number = 0;
                $this->contactId = 0;
            }
            
            if (func_num_args() == 3) {
                $this->id = func_get_arg(0);
                $this->number = func_get_arg(1);
                $this->type = func_get_arg(2);
                $this->contactId = 0;
            }
            
            //4 arguments received
            if (func_num_args() == 4) {
                $this->id = func_get_arg(0);
                $this->type =  new PhoneNumberType(func_get_arg(1));
                $this->number = func_get_arg(2);
                $this->contactId = func_get_arg(3);
            }


	    if(func_num_args() == 1)
	    {
                $connection = MySqlConnection::getConnection();
                $query = 'select id, numbers, phone_number_type, contact_id from phoneNumbers where id = :id';

                $id = func_get_arg(0);
                $command = $connection->prepare($query);
                $command->bindParam(':id', $id); 
                $command->execute();
                $result = $command->fetch(PDO::FETCH_ASSOC);

                if($result != null)
                {
                    $this->id = $result['id'];
                    $this->number = $result['numbers'];
                    $this->type = new PhoneNumberType($result['phone_number_type']);
                    $this->contactId = $result['contact_id'];
                }
                else
                    throw new RecordNotFoundException(func_get_arg(0));
                $connection = null;
                $command = null;
            }

            if(func_num_args() == 2)
            {
                $this->id = 0;
                $this->type = func_get_arg(0);
                $this->number = func_get_arg(1);
            }
        }

        //instance methods
        
        //represents the object in JSON format
        public function toJson() {
            return json_encode(array(
                'id' => $this->id,
                'type' => json_decode($this->type->toJson()),
                'number' => $this->number,
                'contact' => $this->contactId
            ));
        }

        public static function getAll()
        {
            $phones = array();
            $connection = MySqlConnection::getConnection();
            $query = 'select id, number, phone_number_type, contact_id from phoneNumbers';
            $command = $connection->prepare($query);
            $command->execute();
            $allPhoneNumbers = $command->fetchAll(PDO::FETCH_ASSOC);
            $command = null;
            $connection = null;

            foreach($allPhoneNumbers as $phoneNumber)
            {
                array_push($phones, $temp = new PhoneNumber(
                                    $phoneNumber['id'],
                                    $phoneNumber['number'],
                                    $phoneNumber['phone_number_type'],
                                    $phoneNumber['contact_id']));

            }

            return $phones;
        }

        public static function getAllToJson()
        {
            $jsonArray = array();
            foreach(self::getAll() as $phone)
                array_push($jsonArray, json_decode($phone->toJson()));
        }
    }

?>
