<?php 
require_once('models/contact.php');
require_once('models/exceptions/recordnotfoundexception.php');

// get
if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if(isset($_GET['id']))
    {
        try
        {
            $c = new Contact($_GET['id']);

            echo json_encode(array(
                'status' => 0,
                'contact' => json_decode($c->toJsonFull())
            ));
        }       
        catch(RecordNotFoundException $ex)
        {
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Invalid contact id',
                'details' => $ex->getMessage()
            ));
        }

    }
    else
    {
        echo json_encode(array(
            'status' => 0,
            'contacts' => json_decode(Contact::getAllToJson())
        ));
    }
}

// post
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $parametersOk = false;

    if(isset($_POST['firstname']) && isset($_POST['lastname']))
    {
        $parametersOk = true;

        $c = new Contact();

        $c->setFirstName($_POST['firstname']);
        $c->setLastName($_POST['lastname']);

        if($c->add())
        {       
            echo json_encode(array(
                'status' => 0,
                'message' => 'Contact added successfully'
            ));
        }       
        else
        {   
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Could not add contact'
            ));
        }   
    }

    if(isset($_POST['contact_id']) &&
       isset($_POST['type_id']) &&
       isset($_POST['number']))
    {   
       $parametersOk = true;
       $valuesOk = true;

       try
       {
           $c = new Contact($_POST['contact_id']);
       }
       catch(RecordNotFoundException $ex)
       {
           $valuesOk = false;
           echo json_encode(array(
               'status' => 2,
               'errorMessage' => 'Invalid contact id',
               'details' => $ex.getMessage()
           ));
       }
    

       if($valuesOk)
       {
           try
           {
               $t = new PhoneNumberType($_POST['type_id']);
           }
           catch (RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 3,
                   'errorMessage' => 'Invalid phone type id',
                   'details' => $ex->getMessage()
               ));
           }
       }
    
       if($valuesOk)
       {
           if($c->addPhoneNumber(new PhoneNumber($t, $_POST['number'])))
           {    
               echo json_encode(array(
                   'status' => 0,
                   'message' => 'Phone number added successfully'
               ));
           }    
           else
           {    
               echo json_encode(array(
                   'status' => 4,
                   'errorMessage' => 'Could not add phone number'
               ));
           }
       }   
    }
       
       if(isset($_POST['contact_id']) &&
          isset($_POST['email_type_id']) &&
          isset($_POST['address']))       
       {
           $parametersOk = true;
           $valuesOk = true;
           try
           {
               $c = new Contact($_POST['contact_id']);
           }
           catch(RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 2,
                   'errorMessage' => 'Invalid contact id',
                   'details' => $ex->getMessage()
               ));
           }

           if($valuesOk)
           {
               try
               {
                   $emailType = new EmailType($_POST['email_type_id']);
               }
               catch(RecordNotFoundException $ex)
               {
                   echo json_encode(array(
                       'status' => 5,
                       'errorMessage' => 'Invalid email type',
                       'details' => $ex->getMessage()
                   ));
               }
           }
            
           if($valuesOk)
           {
               if($c->addEmail(new Email($emailType, $_POST['address'])))
               {
                   echo json_encode(array(
                       'status' => 0,
                       'message' => 'Email added successfully'
                   ));
               }    
               else
               {
                   echo json_encode(array(
                       'status' => 4,
                       'message' => 'Could not add Email Address'
                   ));
               }
           }
       }

       if(!$parametersOk)
       {
           echo json_encode(array(
               'status' => 1,
               'errorMessage' => 'Missing parameters'
           ));
       }

}   

if($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $parametersOk = false;

    parse_str(file_get_contents("php://input"), $jsonData);
    $post_vars = json_decode($jsonData['data'], true);

    if(isset($post_vars['idContact']) && isset($post_vars['lastName']) && isset($post_vars['firstName']))
    {
        $parametersOk = true;

        $c = new Contact($post_vars['idContact']);

        $c->setFirstName($post_vars['firstName']);
        $c->setLastName($post_vars['lastName']);

        if($c->update())
        {       
            echo json_encode(array(
                'status' => 0,
                'message' => 'Contact updated successfully'
            ));
        }       
        else
        {   
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Could not update contact'
            ));
        }   
    }

    if(isset($post_vars['contact_id']) && isset($post_vars['id']) && isset($post_vars['number']) && isset($post_vars['type']))
    {
        $parametersOk = true;
        $valuesOk = true;

        if($valuesOk)
        {
           try
           {
               $c = new Contact($post_vars['contact_id']);
           }
           catch(RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 2,
                   'errorMessage' => 'Invalid contact id',
               'details' => $ex.getMessage()
               ));
           }
        }

        if($valuesOk)
        {
           try
           {
               $phone = new PhoneNumber($post_vars['id']);
           }
           catch(RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 2,
                   'errorMessage' => 'Invalid phone id',
               'details' => $ex->getMessage()
               ));
           }
        }

       if($valuesOk)
       {
           try
           {
               $t = new PhoneNumberType($post_vars['type']);
           }
           catch (RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 3,
                   'errorMessage' => 'Invalid phone type id',
                   'details' => $ex->getMessage()
               ));
           }
       }
    
       if($valuesOk)
       {
           $phone->setNumber($post_vars['number']);
           $phone->setType($t);
           if($c->updatePhoneNumber($phone))
           {    
               echo json_encode(array(
                   'status' => 0,
                   'message' => 'Phone updated successfully'
               ));
           }    
           else
           {    
               echo json_encode(array(
                   'status' => 4,
                   'errorMessage' => 'Could not updated phone number'
               ));
           }
       }
    }
       if(isset($post_vars['contact_id']) &&
          isset($post_vars['email_type_id']) &&
          isset($post_vars['address']) &&
          isset($post_vars['email_id']))       
       {
           $parametersOk = true;
           $valuesOk = true;
        if($valuesOk)
        {
           try
           {
               $email = new Email($post_vars['email_id']);
           }
           catch(RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 2,
                   'errorMessage' => 'Invalid email id',
               'details' => $ex.getMessage()
               ));
           }
        }       
           
           if($valuesOk)
           {
               try
               {
                   $c = new Contact($post_vars['contact_id']);
               }
               catch(RecordNotFoundException $ex)
               {
                   $valuesOk = false;
                   echo json_encode(array(
                       'status' => 2,
                       'errorMessage' => 'Invalid contact id',
                   'details' => $ex->getMessage()

                   ));
               }
           }

           if($valuesOk)
           {
               try
               {
                   $emailType = new EmailType($post_vars['email_type_id']);
               }
               catch(RecordNotFoundException $ex)
               {
                   echo json_encode(array(
                       'status' => 5,
                       'errorMessage' => 'Invalid email type',
                       'details' => $ex->getMessage()
                   ));
               }
           }
            
           if($valuesOk)
           {
               if($c->updateEmail(new Email($post_vars['email_id'], $post_vars['address'], $emailType)))
               {
                   echo json_encode(array(
                       'status' => 0,
                       'message' => 'Email updated successfully'
                   ));
               }    
               else
               {
                   echo json_encode(array(
                       'status' => 4,
                       'message' => 'Could not update Email Address'
                   ));
               }
           }
       }

       if(!$parametersOk)
       {
           echo json_encode(array(
               'status' => 1,
               'errorMessage' => 'Missing parameters'
           ));
       }
}

if($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
    parse_str(file_get_contents("php://input"), $jsonData);
    $post_vars = json_decode($jsonData['data'], true);

    $parametersOk = false;

    if(isset($post_vars['id']))
    {
        $parametersOk = true;

        $c = new Contact($post_vars['id']);

        if($c->delete())
        {       
            echo json_encode(array(
                'status' => 0,
                'message' => 'Contact deleted successfully'
            ));
        }       
        else
        {   
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Could not delete contact'
            ));
        }   
    }
    
    if(isset($post_vars['idContact']) && isset($post_vars['idPhone'])) 
    {
        $parametersOk = true;
        $valuesOk = true;

        if($valuesOk)
        {
           try
           {
               $c = new Contact($post_vars['idContact']);
           }
           catch(RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 2,
                   'errorMessage' => 'Invalid contact id',
               'details' => $ex->getMessage()
               ));
           }
        }

        if($valuesOk)
        {
           try
           {
               $phone = new PhoneNumber($post_vars['idPhone']);
           }
           catch(RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 2,
                   'errorMessage' => 'Invalid phone id',
               'details' => $ex->getMessage()
               ));
           }
        }

    
       if($valuesOk)
       {
           if($c->deletePhoneNumber($phone))
           {    
               echo json_encode(array(
                   'status' => 0,
                   'message' => 'Phone deleted successfully'
               ));
           }    
           else
           {    
               echo json_encode(array(
                   'status' => 4,
                   'errorMessage' => 'Could not delete phone number'
               ));
           }
       }
    }
       if(isset($post_vars['idContact']) &&
          isset($post_vars['idEmail']))       
       {
           $parametersOk = true;
           $valuesOk = true;
        if($valuesOk)
        {
           try
           {
               $email = new Email($post_vars['idEmail']);
           }
           catch(RecordNotFoundException $ex)
           {
               $valuesOk = false;
               echo json_encode(array(
                   'status' => 2,
                   'errorMessage' => 'Invalid email id',
               'details' => $ex.getMessage()
               ));
           }
        }       
           
           if($valuesOk)
           {
               try
               {
                   $c = new Contact($post_vars['idContact']);
               }
               catch(RecordNotFoundException $ex)
               {
                   $valuesOk = false;
                   echo json_encode(array(
                       'status' => 2,
                       'errorMessage' => 'Invalid contact id',
                   'details' => $ex->getMessage()

                   ));
               }
           }

           if($valuesOk)
           {
               if($c->deleteEmail(new Email($post_vars['id'])))
               {
                   echo json_encode(array(
                       'status' => 0,
                       'message' => 'Email deleted successfully'
                   ));
               }    
               else
               {
                   echo json_encode(array(
                       'status' => 4,
                       'message' => 'Could not delete Email Address'
                   ));
               }
           }
       }

       if(!$parametersOk)
       {
           echo json_encode(array(
               'status' => 1,
               'errorMessage' => 'Missing parameters'
           ));
       }
}
?>
