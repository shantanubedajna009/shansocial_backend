<?php

// libraries initialization
require  __DIR__ .  '/../vendor/autoload.php';

// creating an instance of slim framework
$app = new \Slim\App();


// setup done code starts here

include __DIR__ .'/../app/user.php';
include  __DIR__.'/../app/post.php';
include __DIR__.'/../app/friends.php';


?>