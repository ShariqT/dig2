<?php
namespace Techstack\Libraries;

require __DIR__ . '/../../../vendor/autoload.php';

/**
* @file libraries/Auth.php
*   Provides integration with VCU's CAS system
*/

/**
* Authentication logic which integrates with VCU CAS
*/
class AuthCAS {
	/**
	 * CodeIgniter instance, used for loading other resources from the system.
	 * @usage self::$_CI instead of $this (such as in controllers)
	 */
    private static $_SLIM;
	
	/**
	 * URI for VCU's CAS instance
	 */
	private $_cas_uri = \Techstack\Techstack::CAS_SERVER;
	
	/**
	 * Whether the current user has been authenticated
	 */
    private $_is_authenticated = FALSE;
		
	/**
	 * Current user
	 */
	private $_user = NULL;
	

	/**
	*  User attributes
	*/
	private $_attributes = array();
    
    /**
     * Constructor, grabs an instance of the CI object for session handling and automatically processes CAS responses
     */
    public function __construct()
    { 
    	if(!isset(self::$_SLIM)) self::$_SLIM = \Slim\Slim::getInstance();
		
	}
    public function convert(){
		// Create an instance of Code Igniter
		
		// Load session data
		$request = self::$_SLIM->request();			
		$ticket = array_key_exists('ticket', $_GET) ? $_GET['ticket'] : false;
		$session = $_SESSION;
		$this->_is_authenticated = FALSE;
		$this->_user = NULL;
		if(!isset($session['is_authenticated']) )
		{

			$username = false;
			if($ticket !== false)
			{
				// Create a CURL request by communicate Server-to-Server to validate the supplied ticket
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->_cas_uri .'/serviceValidate?ticket='. $ticket .'&service='. $this->get_service_uri());
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$content = curl_exec($ch);
				$response = curl_getinfo($ch);
				curl_close( $ch );
				
				// Check the response code of the request
				if ($response['http_code'] == 200)
				{
					// Process the XML returned by CAS
					$doc = new \DOMDocument();
					$doc->loadXML($content);
					
					if ($doc->getElementsByTagName('authenticationSuccess')->length == 1)
					{ // Success
						
						
						$attr = $doc->getElementsByTagName('attributes')->item(0);
						
						foreach ($attr->childNodes as $child) {
								switch($child->localName){
									case "groupMembership":
										$this->_attributes["memberships"][] = $this->formatName($child->textContent);
									break;
									default:
										$this->_attributes[$child->localName] = $child->textContent;
									break;
								}
						}
						
						$username = $this->_attributes['uid'];
					} else if ($doc->getElementsByTagName('authenticationFailure')->length == 1)
					{ // Failure, ticket already used, if this happens the application should redirect back to non-authenticated OR 401 page.
					} else
					{ // Attempt to validate ticket through CAS has failed
					}
				}
			}
			
			if($username !== false)
			{
				$this->_is_authenticated = true;
				$this->_user = $username;
				
				
				
				
				
				$_SESSION['user'] = $username;
				$_SESSION['is_authenticated'] = $this->_is_authenticated = true;
				$_SESSION['uatt'] = $this->_attributes;
				
				// Redirect the user back to the root service URI to clear the GET variable
				$final_url = $this->get_service_uri();
				session_write_close();
				if($ticket !== false){
					self::$_SLIM->redirect($final_url);
				} 

			}
		} else
		{
			if( (!(array_key_exists('is_authenticated', $session) 
				   && array_key_exists('user', $session)
				   && array_key_exists('uatt', $session))) ) 
			{
				$this->deauthenticate();
				echo "Error authenticating. Please refresh the page; you may be asked to authenticate again.";
				exit();
			}
			// Session information is available, process it into this Auth instance
			$this->_is_authenticated = $session['is_authenticated'];
			$this->_user			 = $session['user'];
			$this->_attributes	 = $session['uatt'];
 			
		}
    }


    /**
    * Strips out everything from the ldap name except for the uid
    */
    function formatName($name){
    	preg_match("/uid=([a-zA-Z0-9_\-]+),/", $name, $matches);
    	
    	return $matches[1];
    }
	
    /**
     * Removes authentication and destroys session information
     */
    function deauthenticate() {
		// Remove instance authentication
		$this->_is_authenticated = FALSE;
		$this->_user = NULL;
    }
    
    /**
     * Returns whether the current user is authenticated
     * 
     * @return bool
     *   TRUE if the current instance is authenticated, FALSE otherwise
     */
    function is_authenticated() {
		$this->convert();
		return $this->_is_authenticated;
    }
    
    /**
     * The current username that is authenticated
	 * 
	 * @return string
	 *  The username returned by CAS
     */
    function user() {
		return $this->_user;
    }
	
	/**
	 * Retrieve an anchor tag linking to CAS
	 * 
	 * @return string 
	 *   An anchor tag linking to the VCU CAS implementation
	 */
	function get_cas_link() {
		return '<a href="'. $this->_cas_uri .'/login?service='. $this->get_service_uri() .'" class="CAS-link">Login through VCU Central Authentication Service (CAS)</a>';
	}
	
	/**
	 * Retrieve the URI for the current application
	 * 
	 * @return string
	 *   URI for the currently accessed service
	 */
	function get_service_uri() {
		// Build the URI from the current location
		$uri = ($_SERVER['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		
		// Strip out the ticket variable and trailing '?'
		if (!empty($_GET['ticket'])) {
			$uri = preg_replace('/ticket='. $_GET['ticket'] .'/i', '', $uri);
		}
		$uri = rtrim($uri, '?');
		
		return $uri;
	}
	
	/**
	 * Redirects the user to CAS
	 */
	function redirect_to_cas() {
		
		header('Location: '. $this->_cas_uri .'/login?service='. $this->get_service_uri());
		exit();
	}
	
	function cas_uri(){
		return $this->_cas_uri;
	}
	/**
	 * Redirects the user back tto the service (built from $this->get_service_uri()), this is useful for clearing the ticket 
	 * from the user's browser (to prevent bookmarking
	 */
	function redirect_to_service() {
		return $this->get_service_uri();
	}
	
	/**
	 * Redirects the user to the 401 error of the site.
	 */
	function redirect_to_401() {
		header('Location: https://apps.library.vcu.edu/forbidden.html');
		exit();
	}
	
	
	function user_attributes()
	{
		return $this->_attributes;
	}
  }