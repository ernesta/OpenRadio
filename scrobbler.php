<?php
	require("classes/Artist.php");
	require("classes/Song.php");
	require("classes/Last.php");
	
	//Constants: USERNAME, API_KEY, API_SECRET, SESSION_KEY, SCROBBLE_URL
	require("constants.php");
	
	
	$worker = new GearmanWorker();
	
	$worker->addServer();
	$worker->addFunction("scrobbling", "process");
	
	while ($worker->work()) {}
	
	
	function process($job) {
		$song = unserialize($job->workload());
		
		//Scrobble
		$params = array(
			"method" => "track.scrobble",
			"artist" => $song->artist->name,
			"track" => $song->title,
			"timestamp" => $song->start
		);
		
		$params = Last::prepareRequest($params);
		Last::sendRequest($params, "POST");
		
		//Now playing
		$params = array(
			"method" => "track.updateNowPlaying",
			"artist" => $song->artist->name,
			"track" => $song->title
		);
		
		$params = Last::prepareRequest($params);
		Last:: sendRequest($params, "POST");
	}