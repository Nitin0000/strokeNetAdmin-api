<?php
date_default_timezone_set("Asia/Kolkata");

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Acme\Views\Renderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// database
$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $database = new medoo($settings);      
    return $database;
};

$container['db_settings'] = function ($c) {
    $settings = $c->db->select("settings", "*");
    return $settings;
};

// sengrid
$container['sendgrid'] = function ($c) {
    $settings = $c->get('settings')['sendgrid'];
    $sendgrid = new SendGrid($settings['apikey']);
    return $sendgrid;
};

$container['email'] = function ($c) {
   $email = new SendGrid\Email();
   return $email;
};
//
//$container['googlemaps'] = function ($c) {
//   $settings = $c->get('settings')['googlemaps'];
//   $smsdata = array(
//      'apiKey' => $settings['apiKey']
//   );
//   return $smsdata; 
//};
//
$container['sms'] = function ($c) {
  $settings = $c->get('settings')['sms'];
  $smsdata = array(
     'authId' => $settings['authId'],
     'authToken' => $settings['authToken'] 
  );
  return $smsdata; 
};