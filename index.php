<?php

include('config.php');

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;


phpFastCache::setup("path", dirname(__FILE__).'/cache'); // Path For Files



//User Whitelist Check
if( !in_array($username, $user_whitelist) ){
	echo "Clever girl... But you're not on the list. <a href='http://goo.gl/forms/0wgJeVpIaI'>Request Access</a>";
	exit;
}




// Set Caching
$cache = phpFastCache();

// Try to get $content from Caching First
// product_page is "identity keyword";
$link = $cache->get($username);

if($link == null) {
	
	$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
	$content = $connection->get(API_KIND, array(
		"screen_name"			=> $username,
		"count"					=> intval(POSTS_COUNT),
	));
	
	if( empty( $content->errors ) ){//All is dandy
		
		
		foreach( $content as $tweet_object ){
			
			$urls = $tweet_object->entities->urls;//Get URLS
			foreach( $urls as $url ){
				//$debug = $url->expanded_url;
				if (strpos($url->display_url,'periscope.tv') !== false)//Find periscope link
					$link = $url->expanded_url; break;//Use that
			}
			
			if( $link !== null ) break;
			
		}
		
		if( $link !== null ) {
			
			$cache->set( $username , $link , CACHE_TIME );
			
		} else {
			echo "No Periscopes found in the last ".POSTS_COUNT." tweets";
		}
		
		
	} else {//it's GDFR
		
		//So what's going on? What are the error messages
		
		$error_codes = array();
		
		foreach( $content->errors as $error ){
			
			array_push($error_codes, $error->code );
			
			$cache->set( 'er_'.$username.'_'.$error->code , $error , CACHE_TIME );
			
			debug( $error );
		}
		
		//34: Bad request
		//88: Rate Limit Exceeded
		
		//Let's try a few things
		
/*
		if( $cache->isExisting($username) ){//Use cached version
			
			debug( $cache->getInfo($username) );
			
			//$cache->get($username)
						
		}
*/
		
		
		
		//No good? Time to analyze
		
		echo "Something is wrong...";
		
		//Log/Display Errors
		
	}
		
} else {
	//echo "Used Cache <br><br>";
}



if ( headers_sent() ) exit;//Already headers? Then don't redirect

header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
header('Location: '.$link, true, 303);