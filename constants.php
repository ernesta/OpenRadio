<?php
	define("RADIO_DATA_URL", "http://pliusas.fm/xml/onair.xml");
	define("SCROBBLE_URL", "http://ws.audioscrobbler.com/2.0/");
	
	define("SLEEP_TIME", 5);
	
	// file_get_contents() context.
	$contextGET = stream_context_create(array(
		"http" => array(
			"timeout" => 5,
			"method" => "GET")
		)
	);
	
	$contextPOST = stream_context_create(array(
		"http" => array(
			"timeout" => 10,
			"method" => "POST")
		)
	);