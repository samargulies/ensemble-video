(function($) {

	tinymce.create('tinymce.plugins.ensemblevideo', {
		init : function(ed, url) {
			
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('Ensemble_Video', function() {

				tb_show( 'Ensemble video', '#TB_inline?width=480&height=auto&inlineId=ensemble-video' );
				
			});
			
			// Register example button
			ed.addButton('ensemblevideo', {
				title : 'Ensemble video',				
				image : url + '/img/ensemble-button.png',
				cmd : 'Ensemble_Video'
			});
		
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Ensemble Video",
				author : '',
				authorurl : '',
				infourl : '',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('ensemblevideo', tinymce.plugins.ensemblevideo);

	$("<div />")
		.attr("id", "ensemble-video")
		.append("<form><label>Content ID</label><input id='content-id' /><input class='button-primary' type=submit value='Add video' /></form>")
		.hide()
		.appendTo("body");

	$('#ensemble-video form').submit(function(){
		
		insertEnsembleShortcode();
		
		// closes Thickbox
		tb_remove();
				
	});
	
	function insertEnsembleShortcode() {
		var content_id = $("#content-id").val();
		tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[ensemblevideo contentid="' + 			content_id + '"]');	
	}
	
})(jQuery);
