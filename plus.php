<?php
	require("classes/Album.php");
	require("classes/Artist.php");
	require("classes/Song.php");
	require("classes/Tag.php");
	require("classes/Last.php");
	
	//Secrets: USERNAME, API_KEY, API_SECRET, SESSION_KEY.
	require("secrets.php");
	require("constants.php");
	
	
	
	
	$previousSong = NULL;
	
	
	$gearman = new GearmanClient();
	$gearman->addServer();
	
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
	
	
	function fetchSong($xml) {
		$params = array(
			"method" => "track.getInfo",
			"artist" => (string)$xml->artist,
			"track" => (string)$xml->title,
			"autocorrect" => "1",
			"username" => USERNAME
		);
		
		$params = Last::prepareRequest($params);
		$lfm = Last::sendRequest($params, "GET");
		
		$artistID = (string)$lfm->track->artist->mbid;
		
		if ($artistID === "") {
			return null;
		}
		
		//New song
		$song = new Song();
		
		//All the song attributes
		$song->ID = (string)$xml->titleid;
		$song->title = (string)$lfm->track->name;
		$song->start = substr((string)$xml->start, 0, 10);
		$song->URL = (string)$lfm->track->url;
		$song->listeners = (string)$lfm->track->listeners;
		$song->plays = (string)$lfm->track->playcount;
		$song->userplays = (string)$lfm->track->userplaycount;
		
		//Artist
		$artist = new Artist();
		$artist->mbID = $artistID;
		$artist->name = (string)$lfm->track->artist->name;
		$artist->URL = (string)$lfm->track->artist->url;
		
		//Album
		$album = new Album();
		$album->name = (string)$lfm->track->album->title;
		$album->URL = (string)$lfm->track->album->url;
		
		$thumbnail = (string)$lfm->track->album->image[2];
		
		if ((strpos($thumbnail, "noimage")) || ($thumbnail == "")) {
			$album->thumbnail = "img/fire.png";
		} else {
			$album->thumbnail = $thumbnail;
		}
		
		//Tags
		$tags = array();
		
		foreach ($lfm->track->toptags->tag as $entry) {
			$tag = new Tag();
			
			$tag->name = (string)$entry->name;
			$tag->URL = (string)$entry->url;
			
			$tags[] = $tag;
		}
		
		//Finalizing song
		$song->artist = $artist;
		$song->album = $album;
		$song->tags = $tags;
		
		return $song;
	}
	
	
	function getXML($xmlURL) {
		global $contextGET;
		
		$content = file_get_contents($xmlURL, false, $contextGET);
		$xml = simplexml_load_string($content);
		
		return $xml;
	}
	
	
	function saveJSON($song) {
		$json = json_encode($song);
		file_put_contents("song.json", $json);
	}
	
	
	function scrobble($song) {
		global $gearman;
		
		$serializedSong = serialize($song);
		$gearman->doBackground("scrobbling", $serializedSong);
	}