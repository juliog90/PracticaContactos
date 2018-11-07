<?php

require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');

class PhoneNumberType 
{
	private $id;
	private $description;

	public function getId() { return $this->id; }
	public function getDescription() { return $this->description; }
	public function setDescription($description) { $this->description = $description; }

	// constructor

	public function __construct()
	{
		if(func_num_args() == 0)
		{
			$this->id = 0;
			$this->description ='';
		}

		if(func_num_args() == 1) 
		{
			$connection = MySqlConnection::getConnection();
			$query = 'select id, description from phoneNumberTypes where id = :id';
			$command = $connection->prepare($query);
                        $id = func_get_arg(0);
			$command->bindParam(':id', $id);
			$command->execute(); $result = $command->fetch(PDO::FETCH_ASSOC);
			if($result != null) {
				$this->id = $result['id'];
				$this->description = $result['description'];
			}

			else
				throw new RecordNotFoundException(func_get_arg(0));

			$command = null;
                }

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
			$query = 'select id, description from phoneNumberTypes';
			$command = $connection->prepare($query);
			$command->execute();
			$dblist = $command->fetchAll(PDO::FETCH_ASSOC);

			foreach($dblist as $type)
				array_push($list, new PhoneNumberType($type['id'], $type['description']));

			return $list;
		}

		// json array
		public static function getAllToJson()
		{
                    $jsonArray = array();
                    foreach(self::getAll() as $item) { array_push($jsonArray, json_decode($item->toJson()));
			}

			return json_encode($jsonArray);
		}
                
                public function toJson()
                {
                    return json_encode(array('id' => $this->id,
                                               'description' => $this->description
                                   ));
                }
}
?>
