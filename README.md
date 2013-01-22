# [Open Radio](http://radijas.opendata.lt/)
Open Radio tracks music listening habits of [M1+](http://pliusas.fm/), a Lithuanian radio station with a good taste. The [Open Radio website](http://radijas.opendata.lt/) displays whatever is currently on air, while [Last.fm](http://www.last.fm/user/m1plius) is used for storing historical data. Open Radio is created and maintained by [Ernesta Orlovaitė](http://ernes7a.lt) and [Aurimas Račas](http://aurimas.eu).

##Code
**Backend**

+ _plus.php_ retrieves the currently playing song from M1+, fetches additional track information from Last.fm, and saves everything to a JSON file.
+ _scrobbler.php_ submits new tracks to Last.fm.
+ _classes_ defines several simple data structures (Album, Artist, Song, and Tag); _Last.php_ is used for preparing and sending requests to Last.fm.
+ _constants.php_ is used for storing global constants, while _secrets.php_ contains Last.fm API_KEY, API_SECRET, and SESSION_KEY.

**Frontend**

+ _scripts.js_ periodically processes the JSON file and updates the webpage.

## Dependencies
+ Scrobbling to Last.fm is asynchronously performed by [Gearman](http://gearman.org/).
+ JavaScript becomes much nicer when used with [jQuery](http://jquery.com/).
+ The no-hassle frontend framework of choice: [Twitter Bootstrap](http://twitter.github.com/bootstrap/).

## Authors
**Ernesta Orlovaitė**

+ [ernes7a.lt](http://ernes7a.lt)
+ [@ernes7a](http://twitter.com/ernes7a)

**Aurimas Račas**

+ [aurimas.eu](http://aurimas.eu)
+ [@Aurimas](http://twitter.com/Aurimas)