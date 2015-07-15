<?php
	
	if( $_GET['debug'] ){
	
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(-1);
		
	}
	
	function debug( $thing ) {
		$output_data = $thing;
		if( !isset( $thing ) ) $output_data = 'That\'s not a set value';
		if( $thing === null ) $output_data = 'He\'s Null Jim';
		if( $thing === false ) $output_data = 'Returned False';
		if( $thing === 0 ) $output_data = 'Returned Zero';
		ob_start(); ?><pre><?php var_dump($thing); ?></pre><?php $output = ob_get_clean();
		echo $output;
	}
	
	$whitelist = array(
		'127.0.0.1',
		'::1'
	);
	
	$request_url = parse_url($_SERVER['REQUEST_URI']);
	$tokens = explode('/', $request_url['path']);
	$username = $tokens[sizeof($tokens)-1];
	
	$query = 'from%3A'.$username;
	
	$config_vars = array(
	    "TWITTER_CONSUMER_KEY"		=> 0,
	    "TWITTER_CONSUMER_SECRET"	=> 0,
	    "OAUTH_TOKEN"				=> 0,
	    "OAUTH_SECRET"				=> 0,
	    "POSTS_COUNT"				=> 50,//Only get last 20 tweets
	    "API_KIND"					=> 'statuses/user_timeline',
	    "CACHE_TIME"				=> 1 * 60,
	    "RL_RESOURCES"				=> 'help,users,search,statuses'
	);
	
	//Dev Values
	if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)) include('config-dev.php');
	
	//Steup Config Vars as Constants
	foreach( $config_vars as $key => $default ){

		$val = getenv($key);
		
		if( empty( $val ) ) $val = $default;
		
		define($key, $val);
		
	}