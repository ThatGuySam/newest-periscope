<?php

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

include('config.php');

phpFastCache::setup("path", dirname(__FILE__).'/cache'); // Path For Files

// simple Caching with:
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
		
		foreach( $urls as $url ){
			if (strpos($url->display_url,'periscope.tv') !== false)//Find periscope link
				$link = $url->url; break;//Use that
		}
		
		if( $link !== null ) break;
		
	}
	
	//$content = "DB QUERIES | FUNCTION_GET_PRODUCTS | ARRAY | STRING | OBJECTS";
	$cache->set( $username , $link , CACHE_TIME );

	//echo "Used API <br><br>";

} else {
	//echo "Used Cache <br><br>";
}


header('Location: '.$link, true, 302);