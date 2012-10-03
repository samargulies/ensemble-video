<?php
/*
Plugin Name: Ensemble Video Shortcodes
Description: Add [ensemblevideo], [ensembleplayist], and [ensembleshowcase] plugins
Version: 1.0
Author: Scott Nadzan
*/

// replace this with your Ensemble Video url
update_site_option('ensemble_url', 'https://ensemble.temple.edu/ensemble');

add_shortcode('ensemblevideo', 'ensemblevideo');

function ensemblevideo($atts){
	$atts = shortcode_atts(
		array(
		'contentid' => '',
		'width' => 640,
		'height' => 360,
		'iframe' => 'true',
		'title' => 'false',
		'autoplay' => 'false',
		'hidecontrols' => 'false',
		'showcaptions' => 'false',
		'url' => get_site_option('ensemble_url'),
		), $atts);
	
return '
<div id="ensembleEmbeddedContent' . $atts['contentid'] . '" class="ensembleEmbeddedContent" style="width: ' . $atts['width'] . 'px; height: ' . $atts['height'] . 'px;"><script type="text/javascript" src="' . $atts['url'] . '/app/plugin/plugin.aspx?contentID=' . $atts['contentid'] .'&useIFrame=' . $atts['iframe'] . '&embed=true&displayTitle=' . $atts['title'] . '&startTime=0&autoPlay=' . $atts['autoplay'] . '&hideControls=' . $atts['hidecontrols'] . '&showCaptions=' . $atts['showcaptions'] . '&width=' . $atts['width'] . '&height=' . $atts['height'] . '"></script></div>';
}

add_shortcode('ensembleplaylist', 'ensembleplaylist');

function ensembleplaylist($atts){
	$atts = shortcode_atts(
		array(
		'destinationid' => '',
		'embedcode' => 'true',
		'url' => get_site_option('ensemble_url'),
		), $atts);
	
return '
<div id="ensembleContentContainer_' . $atts['destinationid'] . '" class="ensembleContentContainer">
   <script type="text/javascript" src="' . $atts['url'] . '/app/plugin/plugin.aspx?DestinationID=' . $atts['destinationid'] . '&displayEmbedCode=' . $atts['embedcode'] . '"></script>
</div>
';
}

add_shortcode('ensembleshowcase', 'ensembleshowcase');

function ensembleshowcase($atts){
	$atts = shortcode_atts(
		array(
		'destinationid' => '',
		'showcase' => 'true',
		'categorylist' => 'true',
		'embedcode' => 'true',
		'url' => get_site_option('ensemble_url'),
		), $atts);
	
return '
<div id="ensembleContentContainer_' . $atts['destinationid'] . '" class="ensembleContentContainer">
   <script type="text/javascript" src="' . $atts['url'] . '/app/plugin/plugin.aspx?DestinationID=' . $atts['destinationid'] . '&displayShowcase=' . $atts['showcase'] . '&featuredRandom=true&displayCategoryList=' . $atts['categorylist'] . '&categoryOrientation=horizontal&displayEmbedCode=' . $atts['embedcode'] . '"></script>
</div>
';
}
