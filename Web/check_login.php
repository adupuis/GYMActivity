<?php

//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
//  http://www.genymobile.com
// 
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 3 of the License, or
//  (at your option) any later version.
// 
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
// 
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the
//  Free Software Foundation, Inc.,
//  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

session_start();

include_once 'classes/GenyWebConfig.php';
include_once 'classes/GenyProfile.php';
include_once 'classes/GenyAccessLog.php';
include_once 'classes/GenyPropertyValue.php';
include_once 'classes/GenyPropertyOption.php';
include_once 'classes/GenyProperty.php';
include_once 'classes/GenyRightsGroup.php';

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

$web_config = new GenyWebConfig();
$gal = new GenyAccessLog();

if(isset($_GET['module']) || $_POST['module']) {
	
	require_once 'Auth/OpenID/Consumer.php';
	require_once 'Auth/OpenID/AX.php';
	require_once 'Auth/OpenID/google_discovery.php';
	require_once 'Auth/OpenID/FileStore.php';
	require_once 'Auth/OpenID/SReg.php';
	require_once 'Auth/OpenID/PAPE.php';
	
	//Init login
	$tmp = dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR."tmp";
	if (!file_exists($tmp)) die('Temp path '.$tmp.' does not exists');
	if (!is_writable($tmp)) die('Temp path '.$tmp.' is not writable');
	$config['tmp_path'] = $tmp;
	
	//Return URL
	$config['return_server'] = ($_SERVER["HTTPS"]?'https://':'http://').$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
	$config['return_url'] = $config['return_server'].$_SERVER['REQUEST_URI']."?module=return";
	
	//Cache for google discovery (much faster)
	$config['cache'] = new FileCache($config['tmp_path']);
	
	//Open id lib has many warnig and notices
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_USER_NOTICE);
	
	$module = $_GET['module']?$_GET['module']:$_POST['module'];
	
	switch ($module) {
		
		/**
		 * Process login
		 */
		case 'login' :
			$store = new Auth_OpenID_FileStore ( $config ['tmp_path'] );
			$consumer = new Auth_OpenID_Consumer ( $store );
			new GApps_OpenID_Discovery ( $consumer, null, $config ['cache'] );
			
			try {
				$auth_request = $consumer->begin ($web_config->googleapps_domain);
				if (! is_object ( $auth_request ))
					die ( 'Auth request object error. Try again' );
			} catch ( Exception $error ) {
				die ( $error->getMessage () );
			}
			
			// / Request additional parameters
			$ax = new Auth_OpenID_AX_FetchRequest ();
			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/contact/email', 2, 1, 'email' ) );

			// Additional info we don't need at the moment
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/namePerson/first', 1, 1, 'firstname' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/namePerson/last', 1, 1, 'lastname' ) );			
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/namePerson/friendly', 1, 1, 'friendly' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/namePerson', 1, 1, 'fullname' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/birthDate', 1, 1, 'dob' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/person/gender', 1, 1, 'gender' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/contact/postalCode/home', 1, 1, 'postcode' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/contact/country/home', 1, 1, 'country' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/pref/language', 1, 1, 'language' ) );
// 			$ax->add ( Auth_OpenID_AX_AttrInfo::make ( 'http://axschema.org/pref/timezone', 1, 1, 'timezone' ) );
			
			$auth_request->addExtension ( $ax );
			
			// Request URL for auth dialog url
			$redirect_url = $auth_request->redirectURL ( $config ['return_server'], $config ['return_url'] );
			
			if (Auth_OpenID::isFailure ( $redirect_url )) {
				die ( 'Could not redirect to server: ' . $redirect_url->message );
			} else {
				header ( 'Location: ' . $redirect_url );
			}
			break;
		
		/**
		 * Return URL, google redirects here after login
		 */
		case 'return' :
			$store = new Auth_OpenID_FileStore ( $config ['tmp_path'] );
			$consumer = new Auth_OpenID_Consumer ( $store );
			new GApps_OpenID_Discovery ( $consumer, null, $config ['cache'] );
			
			$response = $consumer->complete ( $config ['return_url'] );
			
			// Check the response status.
			if ($response->status == Auth_OpenID_CANCEL)
				die ( 'Verification cancelled.' );
			if ($response->status == Auth_OpenID_FAILURE)
				die ( "OpenID authentication failed: " . $response->message );
			if ($response->status != Auth_OpenID_SUCCESS)
				die ( 'Other error' );
				
			// Successful login
				
			// Extract returned information
			$openid = $response->getDisplayIdentifier ();
			$ax = new Auth_OpenID_AX_FetchResponse ();
			if ($ax)
				$ax = $ax->fromSuccessResponse ( $response );
			
			$sreg = Auth_OpenID_SRegResponse::fromSuccessResponse ( $response );
			if ($sreg)
				$sreg = $sreg->contents ();
			
			$email = $_GET ['openid_ext1_value_email'];
			
			break;
	}
 
}

if(isset($email) || (isset($_POST['geny_username']) && isset($_POST['geny_password'])) ){
	trim($_POST['geny_username']);
	trim($_POST['geny_password']);

	$username = md5($_POST["geny_username"]);
	$passwd = md5($_POST["geny_password"]);
	
	if (! isset ( $email )) {
		if (! preg_match ( "/^[-a-z0-9 ']{4,12}+$/i", $_POST ['geny_username'] )) {
			echo "Username error";
			$gal->insertSimpleAccessLog ( BAD_USERNAME_FORMAT );
			exit ();
		}
	}

	$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
	mysql_select_db($web_config->db_name);
	
	if (isset ( $email )) {
		$query = "SELECT profile_id,profile_login FROM Profiles WHERE profile_email='$email';";
	} else {
		$query = "SELECT profile_id,profile_login FROM Profiles WHERE md5(profile_login)='$username' AND profile_password='$passwd';";
	}
			
	$result = mysql_query($query, $handle);

	if (mysql_num_rows($result)!=0) {
		//mark as valid user
		session_regenerate_id();
		$sqldata = mysql_fetch_assoc($result);
		if (isset ( $email )) {
			$username = md5 ( $sqldata ['profile_login'] );
		}
		$_SESSION['USERID'] = $username;
		$_SESSION['LOGGEDIN'] = true;
		$_SESSION['EMAIL'] = $email;
		if(file_exists("styles/".$_POST['geny_theme']."/main.css"))
			$_SESSION['THEME'] = $_POST['geny_theme'];
		else if(file_exists("styles/genymobile-2012/main.css"))
			$_SESSION['THEME'] = "genymobile-2012";
		else
			$_SESSION['THEME'] = 'default';
		$tmp_profile = new GenyProfile( $sqldata['profile_id'] );
		$tmp_group   = new GenyRightsGroup( $tmp_profile->rights_group_id );
		$prop = new GenyProperty();
		$prop->loadPropertyByName("PROP_APP_STATE");
		$pvs = $prop->getPropertyValues();
		$pv = new GenyPropertyValue();
		$state_pv = $pv->getPropertyValuesByPropertyId(3);
		if( count($pvs) >= 1 ){
			$state_pv = $pvs;
		}
		$s = array_shift($state_pv);
		$popt = new GenyPropertyOption($s->content);
		error_log("[GYMActivity::DEBUG] check_login.php: \$s->content: $s->content",0);
		error_log("[GYMActivity::DEBUG] check_login.php: \$popt->content: $popt->content",0);
		if(($popt->content == 'Inactive - Upgrade' || $popt->content == 'Inactive - Maintenance' || $popt->content == 'Inactive') && $tmp_group->shortname != 'ADM' ){
			session_destroy();
			header("Location: index.php");
			exit();
		}
		if( $tmp_profile->needs_password_reset )
			header('Location: user_admin_password_change.php');
		else{
			if( isset($_POST['referer']) ){
				header("Location: ".$_POST['referer']);
			}
			else {
				header("Location: loader.php?module=home");
			}
		}
		exit;
	}

	//if the code reaches this part then the login failed
	//wrong username/password
	$gal->insertSimpleAccessLog(BAD_CREDENTIALS);
	header("Location: index.php?reason=badcredentials");
}
else
	echo "Error: Form variables undefined.";

?>