jQuery(document).ready(function($) {

	$('<div />')
		.attr('id', 'ensemble-video')
		.append("<div id='ensemble-video-inner'>\
		<div id='shortcode-type-header'>\
		<ul>\
			<li><a id='embed-video-link' class='active' data-shortcode-name='ensemblevideo'>Add Video</a></li>\
			<li><a id='embed-showcase-link' data-shortcode-name='ensembleshowcase'>Add Showcase</a></li>\
			<li><a id='embed-playlist-link' data-shortcode-name='ensembleplaylist'>Add Playlist</a></li>\
		</ul>\
		</div>\
		<form>\
		<p id='content-id'>\
		<label>Content ID <input id='content-id-input' /></label></p>\
		<p id='destination-id' class='hidden'><label>Destination ID <input id='destination-id-input' /></label></p>\
		<p><label>Autoplay <input type='checkbox' id='autoplay' /></label></p>\
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
	
	$('#embed-showcase-link, #embed-playlist-link').click(function(){
		$('#ensemble-video-inner a').removeClass('active');
		$(this).addClass('active');
		$('#content-id').hide();
		$('#destination-id').show();
	});
	
	$('#embed-video-link').click(function(){
		$('#ensemble-video-inner a').removeClass('active');
		$(this).addClass('active');
		$('#destination-id').hide();
		$('#content-id').show();
	});
	
	function insertEnsembleShortcode() {
		
		var shortcode_name = $('#shortcode-type-header a.active').attr('data-shortcode-name');
		
		var shortcode = "[" + shortcode_name + " contentid='" + 	$('#content-id-input').val() + "'";
		
		if( $('#autoplay').is(':checked') ){
			shortcode += " autoplay='true'";
		}
		
		shortcode += ']';
		
		tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcode);	
	}
	
});
