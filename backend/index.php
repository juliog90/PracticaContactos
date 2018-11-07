<?php 

$requestUri = $_SERVER['REQUEST_URI'];

$uriParts = explode('/', $requestUri);

$controller = $uriParts[sizeof[$uriParts] - 2];

$parameters =$uriParts(sizeof($uriParts) -2)

if($controller == strtolower('user'))
{
    require_once('usercontroller.php');
}
else
{
    switch ($controller)
    {
    case strtolower('role') : require_once('usercontroller.php'); break;
    default
        echo json_encode(array(
            'status' => 999,
            'errorMessage' => 'Invalid Controller'
        ));
    }
} 
?>
