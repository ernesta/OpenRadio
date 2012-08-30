<?php
	class Song {
		//Radio-specific song ID
		public $ID = "";
		//Song's title
		public $title = "";
		//Song's artist (Artist.php)
		public $artist = null;
		//Song's album (Album.php)
		public $album = null;
		//Song's tag array (Tag.php)
		public $tags = array();
		//Song's start timestamp
		public $start = "";
		//Link to song's page on Last.fm
		public $URL = "";
		//Song's Last.fm listener count
		public $listeners = "";
		//Song's user play count
		public $plays = "";
	}