<?php


$app->get('/', function() use ($app){

	$app->render('index.html');
});


$app->post("/process", function() use ($app){
	$id = rand(0, 5);
	$app->redirect("/details/" . $id);
});


$app->get("/details/:id", function($id) use ($app){
	if($id == 1){
		$data = array("hash" => "https://files.complictor.com/files/QmPL9M1anAGkgGrc8s613m6Dyhfosk3DSwR6FMRzWS33aj/");
		$data['title'] = "Warp Drive is Possible";
		$data['name'] = "Prof. Blastoff";
		$data['subject'] = "space exploration";
		$data['school'] = "VCU";
	}else{
		$data = array("hash" => "https://files.complictor.com/files/QmbZf4VJkkCTfjmFYfxKRHDB8rQ9L4JS86sG7Wnw5a74bC/");
		$data['title'] = "Science Fiction inspires real-life science";
		$data['name'] = "Dr. Kilgore Trout";
		$data['subject'] = "space exploration";
		$data['school'] = "University of Richmond";
	}
	$app->render("details.html", $data);
});




?>