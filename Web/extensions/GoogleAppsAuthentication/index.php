<?php
/**
 * @date Created on Feb 22, 2011
 * @author		Constantin Bosneaga <ameoba32@gmail.com>
 * 
 */

// Include files
require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/AX.php";
require_once "Auth/OpenID/google_discovery.php";
require_once "Auth/OpenID/FileStore.php";
require_once "Auth/OpenID/SReg.php";
require_once "Auth/OpenID/PAPE.php";

// Init login
$tmp = dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR."tmp";
if (!file_exists($tmp)) die('Temp path '.$tmp.' does not exists'); 
if (!is_writable($tmp)) die('Temp path '.$tmp.' is not writable');
$config['tmp_path'] = $tmp;

// Return URL
$config['return_server'] = ($_SERVER["HTTPS"]?'https://':'http://').$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
$config['return_url'] = $config['return_server'].$_SERVER['REQUEST_URI']."?module=return";

// Cache for google discovery (much faster)
$config['cache'] = new FileCache($config['tmp_path']);

// Open id lib has many warnig and notices
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_USER_NOTICE);

$module = $_GET['module']?$_GET['module']:$_POST['module'];
switch ($module) {
	
	/**
	 * Login form
	 * 
	 * 
	 */	
	case '':
	?>
	<h1>Login with Google Apps</h1>
	<form method="POST">
	<input type="hidden" name="module" value="login">
	Your Google Apps domain:
	<input type="text" name="domain" value="">
	<input type="submit" name="submit" value="Login">
	</form>	

	<h1>Login with Google</h1>
	<form method="POST">
	<input type="hidden" name="module" value="login">
	<input type="hidden" name="domain" value="https://www.google.com/accounts/o8/id">
	<input type="submit" name="submit" value="Login">
	</form>

	<h1>Login with Yahoo</h1>
	<form method="POST">
	<input type="hidden" name="module" value="login">
	<input type="hidden" name="domain" value="http://me.yahoo.com">
	<input type="submit" name="submit" value="Login">
	</form>
		<?	
	break;	


	/**
	 * Process login 
	 */
	case 'login':
		$store = new Auth_OpenID_FileStore($config['tmp_path']);
		$consumer = new Auth_OpenID_Consumer($store);
		new GApps_OpenID_Discovery($consumer, null, $config['cache']);

		try {
			$auth_request = $consumer->begin($_POST['domain']);
			if (!is_object($auth_request)) die('Auth request object error. Try again');
		} catch (Exception $error) {
			die($error->getMessage());
		}

		/// Request additional parameters
		$ax = new Auth_OpenID_AX_FetchRequest;
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email',2,1,'email') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first',1,1, 'firstname') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last',1,1, 'lastname') );
		
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/friendly',1,1,'friendly') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson',1,1,'fullname') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/birthDate',1,1,'dob') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/person/gender',1,1,'gender') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/postalCode/home',1,1,'postcode') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/country/home',1,1,'country') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/language',1,1,'language') );
		$ax->add( Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/timezone',1,1,'timezone') );
		                                                        
		$auth_request->addExtension($ax);

		// Request URL for auth dialog url 
		$redirect_url = $auth_request->redirectURL(
			$config['return_server'],
			$config['return_url']
		);

		if (Auth_OpenID::isFailure($redirect_url)) {
			die('Could not redirect to server: ' . $redirect_url->message);
		} else {
			header('Location: '.$redirect_url);
		}	
	break;
	
	
	/**
	 * Return URL, google redirects here after login
	 */
	case 'return':
		$store = new Auth_OpenID_FileStore($config['tmp_path']);
		$consumer = new Auth_OpenID_Consumer($store);
		new GApps_OpenID_Discovery($consumer, null, $config['cache']);

		$response = $consumer->complete($config['return_url']);

		// Check the response status.
		if ($response->status == Auth_OpenID_CANCEL) die('Verification cancelled.');
		if ($response->status == Auth_OpenID_FAILURE) die("OpenID authentication failed: " . $response->message);
		if ($response->status != Auth_OpenID_SUCCESS) die('Other error');

		// Successful login

		// Extract returned information
		$openid = $response->getDisplayIdentifier();
		$ax = new Auth_OpenID_AX_FetchResponse();
		if ($ax) $ax = $ax->fromSuccessResponse($response);
		
		$sreg = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
		if ($sreg ) $sreg = $sreg->contents();

		# print response
		?>
		<h1>OK</h1>
		You have successfully verified <a href="<?=$openid?>"><?=$openid?></a> as your identity.<br/><br/>
		<p>The following AX response received:</p>
		<pre><?=nl2br(print_r($ax->data,true))?></pre>

		<p>The following sreg response received:</p>
		<pre><?=nl2br(print_r($sreg,true))?></pre>
		<?
	break;	
	
}
 
 
class FileCache {
	var $cache_file;

	function __construct($tmp_path) {
		$this->cache_file = $tmp_path.DIRECTORY_SEPARATOR."google.tmp";
	}
	
	function get($name) {
		$cache = unserialize(file_get_contents($this->cache_file));
		return $cache[$name];
	}
	
	function set($name, $value) {
		$cache = unserialize(file_get_contents($this->cache_file));
		$cache[$name] = $value;
		file_put_contents($this->cache_file, serialize($cache));
	}
	
}
 
 
?>
