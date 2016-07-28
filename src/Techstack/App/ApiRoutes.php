<?php
//this will check whether the api requests are coming through 
//an Ajax request
function checkAjax(){
	$app = \Slim\Slim::getInstance();
	if(!$app->request->isXhr()){
		$app->redirect(BASE_URL);
	}
}


$app->get('/api/v1/all', 'checkAjax', function(){
	echo "hello";
});




?>