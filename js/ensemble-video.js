jQuery(document).ready(function($) {

	$('<div />')
		.attr('id', 'ensemble-video')
		.append("<div id='ensemble-video-inner'>\
		<div id='shortcode-type-header'>\
		<ul>\
			<li><a id='embed-video-link' data-display-class='for-video'>Add Video</a></li>\
			<li><a id='embed-audio-link' data-display-class='for-video'>Add Audio</a></li>\
			<li><a id='embed-destination-link' data-display-class='for-web-destination'>Add Playlist</a></li>\
		</ul>\
		</div>\
		<form>\
		<p id='content-id' class='for-video'>\
		<label>Content ID <input id='content-id-input' /></label></p>\
		<p id='destination-id' class='for-web-destination'>\
		<label>Destination ID <input id='destination-id-input' /></label></p>\
		<p class='for-video for-web-destination'>\
		<label><input type='checkbox' id='autoplay' /> Autoplay</label> &nbsp;&nbsp;&nbsp;\
		<label><input type='checkbox' id='show-captions' /> Show Captions</label></p>\
		<h3 class='for-web-destination'>Choose Layout</h3>\
		<p class='for-web-destination'><label><input type='checkbox' id='display-showcase' /> Display Showcase</label></p>\
		<input class='button-primary' type=submit value='Add video' /> \
		<input type='button' class='button' onclick='tb_remove(); return false;' value='Cancel' />\
		</form>\
		</div>")
		.hide()
		.appendTo('body');

	$('#ensemble-video-inner form').submit(function(e){
		e.preventDefault();
		
		insertEnsembleShortcode();
		
		// closes Thickbox
		tb_remove();
		
		return false;		
	});
	
	$('#shortcode-type-header a').click(function(){
		
		$('#ensemble-video-inner a').removeClass('active');
		$(this).addClass('active');
			
		// set insert button text based on tab text
		$("#ensemble-video-inner .button-primary").val( $(this).text() );
		
		// toggle display of form elements
		$('#ensemble-video-inner p, #ensemble-video-inner h3').hide();
		$( '#ensemble-video-inner .' + $(this).attr('data-display-class') ).show();
		
	});
	
	$('#embed-video-link').click();


	function insertEnsembleShortcode() {
				
		var shortcode = generateEnsembleShortcode();
		
		window.send_to_editor( shortcode );	
	}
	
	function generateEnsembleShortcode() {
		var shortcode = "[ensemblevideo ";
		
		if( $('#ensemble-video-inner a#embed-video-link').is('.active') || $('#ensemble-video-inner a#embed-audio-link').is('.active') ) {
			
			shortcode += 'contentid=' + $('#content-id-input').val();
			
			if( $('#ensemble-video-inner a#embed-audio-link').is('.active') ){
				shortcode += ' audio=true';
			}
			
		} else {
			
			shortcode += 'destinationid=' + $('#destination-id-input').val();
			
			if( $('#display-showcase').is(':checked') ){
				shortcode += ' displayshowcase=true';
			}
			
		}
		
		if( $('#autoplay').is(':checked') ){
			shortcode += ' autoplay=true';
		}
		
		if( $('#show-captions').is(':checked') ){
			shortcode += ' showcaptions=true';
		}
		
		
		shortcode += ']';
		
		return shortcode;
	}
	
});
