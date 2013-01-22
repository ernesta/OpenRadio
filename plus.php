<?php
	require("classes/Album.php");
	require("classes/Artist.php");
	require("classes/Song.php");
	require("classes/Tag.php");
	require("classes/Last.php");
	
	//Secrets: USERNAME, API_KEY, API_SECRET, SESSION_KEY.
	require("secrets.php");
	require("constants.php");
	
	
	// Gearman.
	$gearman = new GearmanClient();
	$gearman->addServer();
	
	
	// Previous song for tracking if a new song has started.
	$previousSong = NULL;
	
	
	// Looping indefinitely to fetch the currently playing song.
	while (true) {
		sleep(SLEEP_TIME);
		
		$xml = getXML(RADIO_DATA_URL);
		if (!$xml) {
			continue;
		}
		
		$song = fetchSong($xml);
		if (!$song) {
			continue;
		}
		
		if ($previousSong->ID !== $song->ID) {
			saveJSON($song);
			scrobble($song);
		}
		
		$previousSong = $song;
	}
	
	
	// Use data from the radio and Last.fm to create a valid Song object.
	function fetchSong($xml) {
		$lfm = getLastFMData((string)$xml->artist, (string)$xml->title);
		
		$artistID = (string)$lfm->track->artist->mbid;
		if ($artistID === "") {
			return NULL;
		}
		
		$song = createNewSong($xml, $lfm);
		$artist = createNewArtist($lfm);
		$album = createNewAlbum($lfm);
		$tags = createNewTags($lfm);
		
		$song->artist = $artist;
		$song->album = $album;
		$song->tags = $tags;
		
		return $song;
	}
	
	
	// Fetch additional track information from Last.fm.
	function getLastFMData($artist, $title) {
		$params = array(
			"method" => "track.getInfo",
			"artist" => $artist,
			"track" => $title,
			"autocorrect" => "1",
			"username" => USERNAME
		);
		
		$params = Last::prepareRequest($params);
		$lfm = Last::sendRequest($params, "GET");
		
		return $lfm;
	}
	
	
	// Parse radio and Last.fm responses to populate a Song object.
	function createNewSong($xml, $lfm) {
		$song = new Song();
		
		$song->ID = (string)$xml->titleid;
		$song->title = (string)$lfm->track->name;
		$song->start = (int)substr((string)$xml->start, 0, 10);
		$song->URL = (string)$lfm->track->url;
		$song->listeners = (string)$lfm->track->listeners;
		$song->plays = (int)$lfm->track->playcount;
		$song->userplays = (int)$lfm->track->userplaycount;
		
		return $song;
	}
	
	
	// Parse Last.fm response to populate an Artist object.
	function createNewArtist($lfm) {
		$artist = new Artist();
		
		$artist->mbID = (string)$lfm->track->artist->mbid;
		$artist->name = (string)$lfm->track->artist->name;
		$artist->URL = (string)$lfm->track->artist->url;
		
		return $artist;
	}
	
	
	// Parse Last.fm response to populate an Album object.
	function createNewAlbum($lfm) {
		$album = new Album();
		
		$album->name = (string)$lfm->track->album->title;
		$album->URL = (string)$lfm->track->album->url;
		
		$thumbnail = (string)$lfm->track->album->image[2];
		if ((strpos($thumbnail, "noimage")) || ($thumbnail == "")) {
			$album->thumbnail = "img/fire.png";
		} else {
			$album->thumbnail = $thumbnail;
		}
		
		return $album;
	}
	
	
	// Parse Last.fm response to populate an array of Tags.
	function createNewTags($lfm) {
		$tags = array();
		
		foreach ($lfm->track->toptags->tag as $entry) {
			$tag = new Tag();
			
			$tag->name = (string)$entry->name;
			$tag->URL = (string)$entry->url;
			
			$tags[] = $tag;
		}
		
		return $tags;
	}
	
	
	// Fetch an XML file.
	function getXML($xmlURL) {
		global $contextGET;
		
		$content = file_get_contents($xmlURL, false, $contextGET);
		$xml = simplexml_load_string($content);
		
		return $xml;
	}
	
	
	// Save a Song object to a file (accessed by scripts.js).
	function saveJSON($song) {
		$json = json_encode($song);
		file_put_contents("song.json", $json);
	}
	
	
	// Add a serialized Song object to a scrobbling queue.
	function scrobble($song) {
		global $gearman;
		
		$serializedSong = serialize($song);
		$gearman->doBackground("scrobbling", $serializedSong);
	}