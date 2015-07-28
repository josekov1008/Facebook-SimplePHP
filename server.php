<?php

	//For debugging purposes only, shows all errors for this document on the browser
	ini_set('display_errors', 'On');
 	error_reporting(E_ALL);

	//Import the required files from Facebook's PHP SDK
	require_once('facebook-sdk/src/Facebook/FacebookSession.php');
	require_once('facebook-sdk/src/Facebook/HttpClients/FacebookHttpable.php');
	require_once('facebook-sdk/src/Facebook/HttpClients/FacebookStreamHttpClient.php');
	require_once('facebook-sdk/src/Facebook/HttpClients/FacebookStream.php');
	require_once('facebook-sdk/src/Facebook/HttpClients/FacebookStream.php');
	require_once('facebook-sdk/src/Facebook/HttpClients/FacebookCurl.php');
	require_once('facebook-sdk/src/Facebook/HttpClients/FacebookCurlHttpClient.php');
	require_once('facebook-sdk/src/Facebook/FacebookRedirectLoginHelper.php');
	require_once('facebook-sdk/src/Facebook/FacebookRequest.php');
	require_once('facebook-sdk/src/Facebook/FacebookResponse.php');
	require_once('facebook-sdk/src/Facebook/FacebookSDKException.php');
	require_once('facebook-sdk/src/Facebook/FacebookRequestException.php');
	require_once('facebook-sdk/src/Facebook/FacebookAuthorizationException.php');
	require_once('facebook-sdk/src/Facebook/GraphObject.php');
	require_once('facebook-sdk/src/Facebook/GraphUser.php');
	require_once('facebook-sdk/src/Facebook/GraphLocation.php');
	require_once('facebook-sdk/src/Facebook/GraphSessionInfo.php');
	require_once('facebook-sdk/src/Facebook/Entities/AccessToken.php');
	require_once('facebook-sdk/src/Facebook/Entities/SignedRequest.php');
	
	require_once('facebook-sdk/src/Facebook/FacebookSignedRequestFromInputHelper.php');
	require_once('facebook-sdk/src/Facebook/FacebookJavaScriptLoginHelper.php');

	use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	use Facebook\FacebookRedirectLoginHelper;
	use Facebook\GraphUser;
	use Facebook\GraphLocation;
	use Facebook\FacebookRequestException;
	use Facebook\GraphSessionInfo;
	use Facebook\GraphObject;
	use Facebook\FacebookHttpable;
	use Facebook\FacebookJavaScriptLoginHelper;

	//Beginning of the server code
	session_start();

	//App identifiers for facebook, need to be registered at developers.facebook.com
	$app_id = '1406146336374834'; //<-Replace
	$app_secret = '70ef24197cbdf5768b49ea4af9d55e62'; //<-Replace
	//$redirect_url = 'http://192.168.0.112/daw/server.php';	//Local test server redirection, where to be redirected once login has been achieved

	FacebookSession::setDefaultApplication($app_id, $app_secret);

	//See if we have a session in $_Session[]
	//Function to get the client IP address since Facebook's SDK doesn't seem to get the current user location right
	function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	//When a GET request is received
	if (isset($_GET)) {
		$peticion = $_GET['tipo']; //Type of petition (in spanish)
		//Petition types:
		//validar = validate = check if session is open (user logged in)
		//datos = data = returns the Graph User data on JSON format (name, birthday, email, sex, age calculated from birthdate on user's profile)
		//foto = picture = returns a link for the profile picture of the user
		//ip = returns the client's public ip address, for location tracking use
		//$respuestaJSON = JSON object with the response data
	
		if ($peticion == "validar") {
			
			$helper = new FacebookJavaScriptLoginHelper();
			try {
	    		$session = $helper->getSession();
			} catch(FacebookRequestException $ex) {
	    		// When Facebook returns an error
	    		echo $ex;
			} catch(\Exception $ex) {
	    		// When validation fails or other local issues
	    		echo $ex;
			}
			
			if (isset($session)) {
				$_SESSION['token'] = $session->getToken();
	  			$loginStatus = true;
			}
			else {
				$loginStatus = false;
			}
	
			$response = array('loginStatus' => $loginStatus);
	
			$respuestaJSON = json_encode($response);
	
			echo $respuestaJSON;
		}
	
	
		if ($peticion == "datos") {
			
			$helper = new FacebookJavaScriptLoginHelper();
			
			if( isset($_SESSION['token']))
			{
	    		// We have a token, is it valid? 
	    		$session = new FacebookSession($_SESSION['token']); 
	    		try
	    		{
	        		$session->Validate($app_id ,$app_secret);
	    		}
	    		catch( FacebookAuthorizationException $ex)
	    		{
	        		// Session is not valid any more, get a new one.
	        		$session ='';
	    		}
			}
	
			if (isset($session)) {
	
				$request = new FacebookRequest($session, 'GET', '/me?fields=name,email,gender,age_range,birthday'); //Data to be collected, not need to specify if just using standard data
	  			$response = $request->execute();
	  			$graphObject = $response->getGraphObject();
	  			$user = $response->getGraphObject(GraphUser::className());
	
				//Response object is created
	  			$nombre = $graphObject->getProperty('name');
	  			$cumple = $graphObject->getProperty('birthday');
	  			$email = $graphObject->getProperty('email');
	  			$genero = $graphObject->getProperty('gender');
	  			$hoy = new DateTime();
	  			$dateNacimiento = new DateTime($cumple);
	  			$edad = $hoy->diff($dateNacimiento)->y; 
	
	  			$response = array('nombre' => $nombre, 'cumpleanos' => $cumple, 'email' => $email, 'genero' => $genero, 'edad' => $edad);
			}
			else {
				$response = array('error' => 'error');
			}
	
			$respuestaJSON = json_encode($response);
	
			echo $respuestaJSON;
		}
	
	
		if ($peticion == "foto") {
			$helper = new FacebookJavaScriptLoginHelper();
			
			if( isset($_SESSION['token']))
			{
	    		// We have a token, is it valid? 
	    		$session = new FacebookSession($_SESSION['token']); 
	    		try
	    		{
	        		$session->Validate($app_id ,$app_secret);
	    		}
	    		catch( FacebookAuthorizationException $ex)
	    		{
	        		// Session is not valid any more, get a new one.
	        		$session ='';
	    		}
			}
	
			if (isset($session)) {
	
				$request = new FacebookRequest($session, 'GET', '/me');
	  			$response = $request->execute();
	  			$graphObject = $response->getGraphObject();
	  			$user = $response->getGraphObject(GraphUser::className());
	
	  			$userID = $user->getId();
	
	  			$link = 'http://graph.facebook.com/'.$userID.'/picture?type=large';
	
	  			$response = array('linkFoto' => $link);
			}
			else {
				$response = array('error' => 'error');
			}
	
			$respuestaJSON = json_encode($response);
	
			echo $respuestaJSON;
		}
	
	
		if ($peticion == "ip") {
			$clientIp = get_client_ip();
			$respuestaJSON = json_encode(array('clientIp' => $clientIp));
	
			echo $respuestaJSON;
		}
	}