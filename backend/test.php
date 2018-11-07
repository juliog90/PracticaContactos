<?php

require_once('models/connection.php');
require_once('models/contact.php');
require_once('models/email.php');
require_once('models/email_type.php');
require_once('models/phone_number_type.php');
require_once('models/phonenumber.php');

/* $c = new Contact(1); */
/* $phones = PhoneNumberType::getAllToJson(); */
/* echo $phones; */
/* echo $c->toJsonFull(); */
/* $lel = $c->getPhoneNumbers(); */
/* var_dump($lel); */
$c = new Contact(10);
$emails = $c->getEmailAddresses();
$phones = $c->getPhoneNumbers();
var_dump($phones);
?>

