<?php
namespace Techstack;

require __DIR__ . '/../../vendor/autoload.php';



class Techstack
{


	//base url
	const DEBUG_BASE_URL = 'http://localhost';
	const PROD_BASE_URL = 'http://localhost';

	//path to where the Twig templates reside
	const TEMPLATE_PATH = '../php_templates';

	//turns on and off CAS
	const CAS_ENABLED = true;

	const CAS_SERVER = 'https://login.vcu.edu/cas';


	//DB config
	const DB_ENABLED = false;


}


?>