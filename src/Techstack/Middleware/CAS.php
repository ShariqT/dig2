<?php
namespace Techstack\Middleware;

require __DIR__ . '/../../../vendor/autoload.php';

class CAS extends \Slim\Middleware
{
	public function call(){

		//reference to the current app
		$app = $this->app;
		$env = $app->environment();
		$req = $app->request();
		$res = $app->response();

		$app->container->singleton('auth', function(){
			return new \Techstack\Libraries\AuthCAS();
		});
		
		

		$app->hook('slim.before.dispatch', function() use ($app){
			if(\Techstack\Techstack::CAS_ENABLED){
				$currentRoute = $app->router()->getCurrentRoute()->getPattern();
				if($app->private_routes != ''){
					if(in_array($currentRoute, $app->private_routes)){
						if(!$app->auth->is_authenticated()){
							header("Location: " . $app->auth->redirect_to_cas());
						}
					}
				}

				if(empty($app->private_routes)){
					$app->error(new \Techstack\Exception('No private routes are specified'));
				}
				if(preg_match('/api\//', $currentRoute) >= 1 ){
					if(!$app->auth->is_authenticated()){
						$app->response->setStatus(401);
						$app->response->headers->set('Content-Type', 'application/json');
						$app->response->write(json_encode(array('error'=>$app->auth->cas_uri() . '/login?service=' . \Techstack\Techstack::BASE_URL)));
						$app->stop();
					}
				}
			}
		});

		$this->next->call();

		
		

	}
}





?>