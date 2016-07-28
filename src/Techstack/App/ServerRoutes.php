<?php


$app->get('/', function() use ($app){
	$app->render('index.html');
});

$app->get("/hello/:name", function() use ($app){
	$app->render('private.html');
});



?>