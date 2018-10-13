<?php
	require("classes/Artist.php");
	require("classes/Song.php");
	require("classes/Last.php");
	
	//Secrets: USERNAME, API_KEY, API_SECRET, SESSION_KEY.
	require("secrets.php");
	require("constants.php");
	
	
	// Gearman.
	$worker = new GearmanWorker();
	
	$worker->addServer();
	$worker->addFunction("scrobbling", "process");
	
	while ($worker->work()) {}
	
	
	// Use Last.fm to scrobble tracks.
	function process($job) {
		$song = unserialize($job->workload());
		
		//Scrobble the track.
		$params = array(
			"method" => "track.scrobble",
			"artist" => $song->artist->name,
			"track" => $song->title,
			"timestamp" => $song->start
		);
		
		$params = Last::prepareRequest($params);
		Last::sendRequest($params, "POST");
		
		//Set the track as now playing.
		$params = array(
			"method" => "track.updateNowPlaying",
			"artist" => $song->artist->name,
			"track" => $song->title
		);
		
		$params = Last::prepareRequest($params);
		Last::sendRequest($params, "POST");
	}