<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$client = new SoapClient("http://example.com/webservices?wsdl");
var_dump($client->__getFunctions()); 
var_dump($client->__getTypes()); 
?>