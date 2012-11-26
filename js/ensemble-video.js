jQuery(document).ready(function($) {

	$('<div />')
		.attr('id', 'ensemble-video')
		.append("<div id='ensemble-video-inner'>\
		<div id='shortcode-type-header'>\
		<ul>\
			<li><a id='embed-video-link'>Add Video</a></li>\
			<li><a id='embed-destination-link'>Add Web Destination</a></li>\
		</ul>\
		</div>\
		<form>\
		<p id='content-id' class='for-video'>\
		<label>Content ID <input id='content-id-input' /></label></p>\
		<p id='destination-id' class='for-web-destination'>\
		<label>Destination ID <input id='destination-id-input' /></label></p>\
		<p class='for-video for-web-destination'><label>Autoplay <input type='checkbox' id='autoplay' /></label></p>\
		<p class='for-video for-web-destination'><label>Show Captions <input type='checkbox' id='show-captions' /></label></p>\
		<p class='for-video'><label>Audio <input type='checkbox' id='audio' /></label></p>\
		<h3 class='for-web-destination'>Choose Layout</h3>\
		<p class='for-web-destination'><label>Display Showcase <input type='checkbox' id='display-showcase' /></label></p>\
		<input class='button-primary' type=submit value='Add video' /> \
		<input type='button' class='button' onclick='tb_remove(); return false;' value='Cancel' />\
		</form>\
		<div id='ensemble-video-preview'></div>\
		<div id='ensemble-video-library-videos'></div>\
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
	
/*
	$('#ensemble-video-inner form').change(function(){
	
		var shortcode = generateEnsembleShortcode();
		
		var data = {
			action: 'ensemblevideo_render_shortcode',
			shortcode: generateEnsembleShortcode()
		};
		
		$.post(ajaxurl, data, function(data){
			$('#ensemble-video-preview').html(data);
		});
				
	})
*/

	function ajax_test(){
		
		var data = {
			action: 'ensemblevideo_proxy_api',
			api_call: 'Content',
			username: 'apiDemo',
			password: 'demo123'
			
		};
		
		$.post(ajaxurl, data, function(response){
			console.log(response);
			
			$.each(response.Data, function(i, video){
				$("<div class='ensemble-video-item' />")
					.attr('data-content-id', video.ID)
					.append("<h2>" + video.Title + "</h2>" )
					.append("<img src='" + video.ThumbnailUrl + "' />")
					.appendTo('#ensemble-video-library-videos');
			})
			
		}, 'json');
	}
	ajax_test();
	
	$('.ensemble-video-item').live('click', function(){
		var content_id = $(this).attr('data-content-id');
		$('#content-id-input').val( content_id );
	});
	
	$('#embed-destination-link').click(function(){
	
		$('#ensemble-video-inner a').removeClass('active');
		$(this).addClass('active');
		$('#ensemble-video-inner .for-video').hide();
		$('#ensemble-video-inner .for-web-destination').show();
		
	});
	
	$('#embed-video-link').click(function(){
	
		$('#ensemble-video-inner a').removeClass('active');
		$(this).addClass('active');
		$('#ensemble-video-inner .for-web-destination').hide();
		$('#ensemble-video-inner .for-video').show();
		
	});
	
	$('#embed-video-link').click();


	function insertEnsembleShortcode() {
				
		var shortcode = generateEnsembleShortcode();
		
		tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcode);	
	}
	
	function generateEnsembleShortcode() {
		var shortcode = "[ensemblevideo ";
		
		if( $('#ensemble-video-inner a#embed-video-link').is('.active') ) {
			
			shortcode += 'contentid=' + $('#content-id-input').val();
			
			if( $('#audio').is(':checked') ){
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
