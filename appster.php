<?php
/*
Plugin Name: Appster
Plugin URI: http://github.com/chrismccoy
Description: Apple Appstore Helper
Version: 1.0
Author: Chris McCoy
Author URI: http://github.com/chrismccoy
*/

include dirname(__FILE__) . '/appstoreapi.php';

add_action('admin_menu', "appstore_admin_init");

function appstore_admin_init() {
	add_options_page('Appster', 'Appster', 8, 'appster', 'appstore_menu');
}

function appstore_menu() {?>

<div class="wrap">

<h2>Appster v1.0</h2>
<br/>
<b>Bringing the AppStore into WordPress.</b>
<p>This plugin fetches all the meta data from Apples App Store in a simple way. </p>
<p>You can get all relevant information right from the App Store API with a simple shortcode. </p>
<p>The shortcode brings you loads of flexibility, display only what you want by use of our template tags.</p>
<b>Example:</b><br/><br/>
<div style="width:900px;">
<script src="https://gist.github.com/1122907.js"></script>
</div>
</div>

<?php
}

function parse_shortcode_content($content) {
    $content = trim(wpautop(do_shortcode($content)));
    if (substr( $content,0,4) == '</p>' )
        $content = substr( $content,4);
    if (substr( $content, -3,3) == '<p>')
        $content = substr($content,0, -3);
    $content = str_replace(array('<p></p>'),'',$content);
    return $content;
}

add_shortcode('appstore','appstore_func');

function appstore_func($atts, $content) {
	extract(shortcode_atts(array(
		'id' => '284882215' // default
	), $atts));
		
	$appid = $atts['id'];

	$app = new appstore($appid);

 	if ($app->isFree) $app_price = "Free";
	else $app_price = '$' . $app->price;

	foreach($app->iPhoneScreenshots as $screenshot)
	$screenshots.= '<img src="'.$screenshot.'" width="180px"/>';

	$replace_strings = array(
		 $app->description, $app->name, $app->version , 
		 $app->genre , $app->size(true) , $app->seller , 
		 $app->releasenotes , $app->released('M.D.Y') , 
		 $app->iTunesWebUrl, $app->sellerurl, $app->smallThumbnail , 
		 $app->developerUrl, $app->numberOfRatings , $app->averageRating , 
		 $app_price, $screenshots
	 );
	 
	 $search_strings = array(
		 "%description%", "%name%", "%version%", "%genre%",
		 "%size%", "%seller%", "%releasenotes%", "%released%",
		 "%appstoreurl%", "%sellerurl%", "%thumbnail%",
		 "%developerurl%", "%numratings%", "%avgrating%",
		 "%price%", "%screenshots%"
	 );
		 
 	$content =  parse_shortcode_content($content);
	$content = str_replace($search_strings,$replace_strings,$content);
        return $content;
}
