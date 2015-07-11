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
		//$debug = $urls;
		foreach( $urls as $url ){
			//$debug = $url->expanded_url;
			if (strpos($url->display_url,'periscope.tv') !== false)//Find periscope link
				$link = $url->expanded_url; break;//Use that
		}
		
		if( $link !== null ) break;
		
	}
	
	$cache->set( $username , $link , CACHE_TIME );
	
} else {
	//echo "Used Cache <br><br>";
}

//echo $link;

//$link = 'http://www.periscope.tv/w/aHBscDEyNTA5NTZ8Mzc1ODM2MjktDJlwi1H05ca09AWh--vC91Aq-440CeHPsXzKhtyFeQ==';

//$debug = $link;

//if( $_GET['debug'] ) debug($debug); exit;

header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
header('Location: '.$link, true, 303);
exit;