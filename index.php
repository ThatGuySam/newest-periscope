<?php

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

include('config.php');

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
	
	
	foreach( $content as $tweet_object ){
		
		$urls = $tweet_object->entities->urls;//Get URLS
		//$debug = $urls;
		foreach( $urls as $url ){
			//$debug = $url->expanded_url;
			if (strpos($url->display_url,'periscope.tv') !== false)//Find periscope link
				$link = $url->expanded_url; break;//Use that
		}
		
		if( $link !== null ) break;
		
	}
	
	$cache->set( $username , $link , CACHE_TIME );
/*
	if( $link ){
		
	}
*/
	
	if( $link !== null ) {
		
		$cache->set( $username , $link , CACHE_TIME );
		
	} else {
		echo "No Periscopes found in the last ".POSTS_COUNT." tweets";
	}
		
} else {
	//echo "Used Cache <br><br>";
}

if ( headers_sent() ) exit;//Already headers? Then don't redirect


header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
header('Location: '.$link, true, 303);