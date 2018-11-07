<?php 
require_once('models/user.php');

// get

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // receive headers
    $headers = getallheaders();
    // authenticate
    if(isset($headers['username']) && isset($headers['username']))
    {
        try
        {
            $u = new User($headers['username'], $headers['password']);

            echo json_encode(array(
                'status' => 0,
                'user' => json_decode($u->toJson())
            ));
        }
        catch(AccessDeniedException $ex)
        {
            echo json_encode(array(
                'status' => 1,
                'errorMessage' =>$ex->getMessage()
            ));
        }
    }
}



