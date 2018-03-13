#! /usr/bin/env php
<?php

require 'vendor\autoload.php';

use Symfony\Component\Console\Application;
use Acme\NewCommand;
use Acme\RenderCommand;
	
/*$app = new Application('Laracasts Demo', '1.0');
$app->add(new NewCommand(new GuzzleHttp\Client)); // must be an instance of client interface
$app->run();*/

$renderapp = new Application('Laracasts Demo', '1.0');
$renderapp->add(new Acme\RenderCommand); // must be an instance of client interface
$renderapp->run();









/*
$app = new Application('Laracasts Demo', '1.0');
$app->add(new SayHelloCommand);
$app->run();
*/