<?php
namespace Techstack\Middleware;


require __DIR__ . '/../../../vendor/autoload.php';

class DefaultStyles extends \Slim\Middleware
{

	public function call(){

		//reference to the current app
		$app = $this->app;
		$env = $app->environment();
		$req = $app->request();
		$res = $app->response();

		
		$app->view->appendData(array(
			'js_scripts' => $app->js_scripts,
			'css_links' => $app->css_links,
			'base_url' => BASE_URL
		));

		
		//call the next middleware
		$this->next->call();
	}
}


?>