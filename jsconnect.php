<?php
/* DokuWiki setup */
if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'jsConnect/functions.jsconnect.php');

if(!isset($_SERVER['REMOTE_USER'])) die('');
if(!exportUserDoku()) die('hmm');

// Get DokuWiki data
function exportUserDoku() {
	global $auth;
	if (!$auth) return false;
	
	// 1. Get your client ID and secret here. These must match those in your jsConnect settings.
	$clientID = "1241223712";
	$secret = "fc933690e06383999be64f5917e782ef";

	// 2. Grab the current user from your session management system or database here.
	$signedIn = true; // this is just a placeholder	
	
	$user_id   = $_SERVER['REMOTE_USER'];
		
	$userinfo = $auth->getUserData($user_id);
	if(!$userinfo['mail']) {
		return false;
	}
	
	$user_name = $userinfo['name'];
	$user_mail = $userinfo['mail'];
		
	// 3. Fill in the user information in a way that Vanilla can understand.
	$user = array();

	if ($signedIn) {
	   // CHANGE THESE FOUR LINES.
	   $user['uniqueid'] = $user_id;
	   $user['name'] = $user_id;
	   $user['email'] = $user_mail;
	   $user['photourl'] = '';
	}

	// 4. Generate the jsConnect string.

	// This should be true unless you are testing. 
	// You can also use a hash name like md5, sha1 etc which must be the name as the connection settings in Vanilla.
	$secure = true; 
	WriteJsConnect($user, $_GET, $clientID, $secret, $secure);
	
	
	return true;
}
