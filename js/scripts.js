(function($) {
	refresh();
	window.setInterval(refresh, 20000);
	
	function refresh() {
		$.ajax({
			url: "song.json",
			dataType: "json"
		}).done(function(song) {
			console.log(song);
			
			$("#player img")
				.attr("src", song.album.thumbnail)
				.attr("alt", song.title);
				
			$("#song")
				.attr("href", song.URL)
				.text(song.title);
			
			$("#artist")
				.attr("href", song.artist.URL)
				.text(song.artist.name);
			
			tags = $("#tags").empty();
			
			for (var i = 0; i < song.tags.length; i++) {
				element = $(
					"<span class='label'>#" +
					song.tags[i].name +
					"</span>");
					
				tags.append(element);
			}
		});
	}
})(jQuery);