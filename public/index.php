<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// libraries initialization
require  __DIR__ .  '/../vendor/autoload.php';
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

// creating an instance of slim framework
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

// $app->error(function (\Exception $e) use ($app) {
//     echo "pooki";
//     //enter manipulation of $e->getTrace()
//     //echo var_dump($e->getTrace());// but the format would be chaotic
// });


// setup done code starts here


// api endpoints
include __DIR__ .'/../app/user.php';
include  __DIR__.'/../app/post.php';
include __DIR__.'/../app/friends.php';
include __DIR__.'/../app/comment.php';


// helper methods
include __DIR__.'/../app/helpers/friendhelper.php';
include __DIR__.'/../app/helpers/likehelper.php';
include __DIR__.'/../app/helpers/commenthelper.php';

// after everything is included app is run from here
$app->run();


?>