<?php

require_once('connection.php');
require_once('phonenumber.php');
require_once('email.php');
require_once('exceptions/recordnotfoundexception.php');
    
class Contact
{
    private $id;
    private $firstName;
    private $lastName;

    public function getId()
    {
        return $this->id;
    }
    public function getFirstName()
    {
        return $this->firstName;
    }
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName=$lastName;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function __construct()
    {
        if (func_num_args() == 0) {
            $this->id = 0;
            $this->firstName = '';
            $this->lastName = '';
        }

        if (func_num_args() == 1) {
            $connection = MySqlConnection::getConnection();
            $query = 'select id, firstName, lastName from contacts where id = :id';
            $command = $connection->prepare($query);
            $id = func_get_arg(0);
            $command->bindParam(':id', $id);
            $command->execute();
            $result = $command->fetch(PDO::FETCH_ASSOC);

            if ($result != null) {
                $this->id = $result['id'];
                $this->firstName = $result['firstName'];
                $this->lastName = $result['lastName'];
            } else {
                throw new RecordNotFoundException(func_get_arg(0));
            }
            
            $connection = null;
            $command = null;
        }

        if (func_num_args() == 3) {
            $this->id = func_get_arg(0);
            $this->firstName = func_get_arg(1);
            $this->lastName = func_get_arg(2);
        }
    }

    public function toJson()
    {
        return json_encode(array(
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'fullName' => $this->getFullName()
        ));
    }

    public function toJsonFull()
    {
        $phones = array();

        foreach ($this->getPhoneNumbers() as $item) {
            array_push($phones, json_decode($item->toJson()));
        }

        $emails = array();
        foreach ($this->getEmailAddresses() as $item) {
            array_push($emails, json_decode($item->toJson()));
        }

        return json_encode(array(
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'fullName' => $this->getFullName(),
            'phoneNumber' => $phones,
            'emailAddressses' => $emails
            ));
    }

    public function add()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'insert into contacts(firstName, lastName) values(:firstName, :lastName)';
        $command = $connection->prepare($statement);
        $command->bindParam(':firstName', $this->firstName);
        $command->bindParam(':lastName', $this->lastName);
        $result = $command->execute();

        $command = null;
        $connection = null;

        return $result;
    }

    public function update()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'update contacts set firstName = :firstName, lastName = :lastName where id = :id';
        $command = $connection->prepare($statement);
        $command->bindParam(':id', $this->id);
        $command->bindParam(':firstName', $this->firstName);
        $command->bindParam(':lastName', $this->lastName);
        $result = $command->execute();

        $command = null;
        $connection = null;

        return $result;
    }

    public function updatePhoneNumber($updatePhone)
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'update phoneNumbers set numbers = :numbers, phone_number_type = :phoneType where id = :phoneId';
        $command = $connection->prepare($statement);
        $number = $updatePhone->getNumber();
        $command->bindParam(':numbers', $number);
        $phoneType = $updatePhone->getType()->getId();
        $command->bindParam(':phoneType', $phoneType);
        $phoneId = $updatePhone->getId();
        $command->bindParam(':phoneId', $phoneId);
        $result = $command->execute();

        $command = null;
        $connection = null;

        return $result;
    }

    public function updateEmail($updateEmail)
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'update emails set address = :address, email_type_id = :emailType where id = :email_id';
        $command = $connection->prepare($statement);
        $address = $updateEmail->getAddress();
        $command->bindParam(':address', $address);
        $emailType = $updateEmail->getEmailType()->getId();
        $command->bindParam(':emailType', $emailType);
        $email_id = $updateEmail->getId();
        $command->bindParam(':email_id', $email_id);
        $result = $command->execute();

        $command = null;
        $connection = null;

        return $result;
    }


    public function delete()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'delete from contacts where id = :id';
        $command = $connection->prepare($statement);
        $command->bindParam(':id', $this->id);
        $result = $command->execute();
        $command = null;
        $connection = null;

        return $result;
    }

    public function deletePhoneNumber($deletePhone)
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'delete from phoneNumbers where id = :id';
        $command = $connection->prepare($statement);
        $pId = $deletePhone->getId();
        $command->bindParam(':id', $pId);
        $result = $command->execute();
        $command = null;
        $connection = null;

        return $result;
    }

    public function deleteEmail($deleteEmail)
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'delete from emails where id = :id';
        $command = $connection->prepare($statement);
        $eId = $deleteEmail->getId();
        $command->bindParam(':id', $eId);
        $result = $command->execute();
        $command = null;
        $connection = null;

        return $result;
    }

    public function addPhoneNumber($phoneNumber)
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'insert into phoneNumbers(contact_id, phone_number_type, numbers) values(:idContact, :idType,:number)';
        $command = $connection->prepare($statement);
        $contact = $this->id;
        $pType = $phoneNumber->getType()->getId();
        $number = $phoneNumber->getNumber();
        $command -> bindParam(':idContact', $contact);
        $command -> bindParam(':idType', $pType);
        $command -> bindParam(':number', $number);
        
        $result = $command->execute();
        $command = null;
        $connection = null;

        return $result;
    }

    public function addEmail($email)
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'insert into emails(address, email_type_id, contact_id)
                      values(:address, :type, :contact)';
        $command = $connection->prepare($statement);
        $addAddress = $email->getAddress();
        $command->bindParam(':address', $addAddress);
        $emailType = $email->getEmailType()->getId();
        $command->bindParam(':type', $emailType);
        $contact = $this->id;
        $command->bindParam(':contact', $contact);

        $result = $command->execute();

        $command = null;
        $connection = null;

        return $result;
    }


    public function getPhoneNumbers() {
        $list = array();
        $connection = MySqlConnection::getConnection();

        /* $query = 'select * from emails'; */
        /* $query = 'select n.id as id, t.id as tid, t.description, n.numbers, n.contact_id from phoneNumbers as n join phoneNumberTypes as t on n.phone_number_type = t.id  where contact_id = :id'; */
        $query = 'select phone.id as Eid from phoneNumbers as phone inner join phoneNumberTypes as ptype on phone.phone_number_type = ptype.id where phone.contact_id = :id';
  
        
        $command = $connection->prepare($query);
        $command->bindParam(':id', $this->id);

        $command->execute();

        $phones = $command->fetchAll(PDO::FETCH_ASSOC);

            foreach ($phones as $phone) {
                array_push($list, new PhoneNumber($phone['Eid']));
            }
            $command = null;
            $connection = null;

            return $list;
    }
                
    public function getEmailAddresses()
    {
        $list = array();
        $connection = MySqlConnection::getConnection();

        $query ='select email.id as Eid, email.contact_id, types.description, email.address from email_types as types inner join emails as email on email.email_type_id = types.id where email.contact_id = :id';

        $command = $connection->prepare($query);
        $command->bindParam(':id', $this->id);
        $command->execute();

        $emails = $command->fetchAll(PDO::FETCH_ASSOC);

        if ($emails != null) {
            foreach ($emails as $email) {
                array_push($list, new Email($email['Eid']));
            }
        }
            
        $command = null;
        $connection = null;

        return $list;
    }

    public static function getAll()
    {
        $list = array();

        $connection = MySqlConnection::getConnection();
        $query = 'select id, firstName, lastName from contacts';
        $command = $connection->prepare($query);
        $command->execute();
        $allContacts = $command->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allContacts as $contact) {
            array_push($list, new Contact($contact['id'], $contact['firstName'], $contact['lastName']));
        }

        return $list;
    }

    public static function getAllToJson()
    {
        $jsonArray = array();
        foreach (self::getAll() as $item) {
            array_push($jsonArray, json_decode($item->toJson()));
        }

        return json_encode($jsonArray);
    }
}
