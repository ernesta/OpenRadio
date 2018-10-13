<?php
	class Song {
		// Radio-specific song ID.
		public $ID = "";
		// Song's title.
		public $title = "";
		// Song's artist (Artist.php).
		public $artist = NULL;
		// Song's album (Album.php).
		public $album = NULL;
		// Song's tag array (Tag.php).
		public $tags = NULL;
		// Song's start timestamp.
		public $start = 0;
		// Link to song's page on Last.fm.
		public $URL = "";
		// Song's Last.fm listener count.
		public $listeners = 0;
		// Song's user play count.
		public $plays = 0;
	}