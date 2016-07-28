<?php
	require '../vendor/autoload.php';

	$app = new Slim\Slim();
	$app->config(array( 'view' => new Slim\Views\Twig(), 'cookies.encrypt' => true ));



	
		define('BASE_URL', \Techstack\Techstack::DEBUG_BASE_URL);
	


	//base styles/scripts that will apply to all pages
	$app->js_scripts = array(BASE_URL . '/bower_components/jquery/dist/jquery.js',
							BASE_URL . '/bower_components/handlebars/handlebars.js',
							BASE_URL . '/bower_components/ember/ember.js',
							BASE_URL . '/javascript/app/app.min.js'
							);
	
	$app->css_links = array(BASE_URL . '/css/style.css');

	$app->icon_links = array(BASE_URL . '/icons/sprite/icons.svg');

	//this sets up Slim and Twig to be able to work together
	$view = $app->view();
	$view->twigTemplateDirs = array(Techstack\Techstack::TEMPLATE_PATH);
	$view->parserExtensions = array(new Slim\Views\TwigExtension());
	
	//add the DefaultStyles and CAS
	
	$app->add(new Techstack\Middleware\DefaultStyles());
	$app->add(new \Slim\Middleware\SessionCookie(array(
	    'expires' => '1 day',
	    'path' => '/',
	    'domain' => null,
	    'secure' => true,
	    'httponly' => true,
	    'name' => 'vcul_session',
	    'secret' => "dfsdfsdfsf",
	    'cipher' => MCRYPT_RIJNDAEL_256,
	    'cipher_mode' => MCRYPT_MODE_CBC
	)));

	
	/* Default error handler, pass in a Techstack Exception to get this going */
	$app->error(function(\Techstack\Exception $e) use($app){
		$app->render('error.html', array('msg' => $e->getMessage()));
	});

	
	if(\Techstack\Techstack::DB_ENABLED){
		
		/* add the db configuration here

			an example would be \ORM::configure('mysql:host=dbhost...');
		*/
		if($_SERVER['APP_ENV'] == "Debug"){
			//  \ORM::configure(development variables)
		}else{
			// \ORM::configure(production variables)
		}

	}



	/**********************************************************************
	*	Begin route declarations below this point						  *	
	*																	  *			
	***********************************************************************/				

	/*
	specify if any routes should be private, 
	these will be protected via CAS. By default,
	all requests that start with '/api/' will be private
	*/
	


	/*
	include api routes here
	this part can grow according to your application needs
	just remember to include the routes. It is better
	to break them up by function (i.e., all routes pertaining to 
	session management go in the SessionRoutes.php file)
	*/
	
	require '../src/Techstack/App/ServerRoutes.php';
	require '../src/Techstack/App/ApiRoutes.php';



	$app->run();
?>