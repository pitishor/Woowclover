<?php
/*
Plugin Name: WOOWClover
Plugin URI: http://www.woowclover.com
Description: Contains a collection of functions used for WOOWClover
Version: 0.1
Author: Adrian Anghelescu
Author URI: http://www.woowclover.com
License: GPL2
*/

/**
 * Copyright (c) 2017 Adrian Anghelescu (email: adrian@BusinessSoftwareDesign.com). All rights reserved.
 *
 *
 * This is a private use add-on for WordPress 
 * http://wordpress.org/
 *
 * **********************************************************************
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;


function getCUPaypalAddress(){

  $user_id = bp_loggedin_user_id(); 
  $paypal_address = xprofile_get_field_data( 'Paypal address', $user_id );
	if (strlen($paypal_address)>0) {
		return  $paypal_address;
	}else
  	{
  		return '';  
  	}

}


function emailLog( $message , $source){
  $to      = 'Adrian@BusinessSoftwareDesign.com';
  $subject = "WOOWClover Log: $source";
  $headers = 'From: adrian@businesssoftwaredesign.com' . "\r\n" .
	  'Reply-To:  adrian@businesssoftwaredesign.com' . "\r\n" .
	  'X-Mailer: PHP/' . phpversion();
  
  $result  = mail($to, $subject, $message, $headers);
  echo "email result = ".var_dump($result);

}

function woowloginfooter_function (){
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
	if ( SITECOOKIEPATH != COOKIEPATH ) setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie("E_Alex_Bolovan", 'da',  30 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
}  
  
add_shortcode('woowloginfooter','woowloginfooter_function');


function woowinfo_function (){
  
  
  echo  '<form">
  First name:<br>
  <input type="text" name="redirect" value="Mickey"><br>
  Last name:<br>
  <input type="text" name="lastname" value="Mouse"><br><br>
  <input type="submit" value="Submit">
</form> ';
	$pagereq =$_SERVER['REQUEST_URI'];
  

  	echo "<br><br><br>server_uri:$pagereq<br>Langdir:".WP_LANG_DIR."<br>";
  // phpinfo();
  echo "<br><br>Mofile:$mofile<br>bp_language:".BPLANG ;
  echo "<br><br><br>wp_language:".WPLANG ."<br><br><br>Language attributes:".language_attributes()."<br><br><br>Features:";

_e('Submit feature','feature_request');

}


add_shortcode( 'woowinfo', 'woowinfo_function' );


function debug_load_textdomain( $domain , $mofile  ){
    echo "Trying ".$domain." at ".$mofile."<br />\n";
	$mofile = WP_LANG_DIR."plugins/buddypress-en_US.mo";
}
//add_action('load_textdomain','debug_load_textdomain');


add_shortcode( 'SiteSearch', 'SiteSearch_function' );

function SiteSearch_function (){
  echo "<br>";
  get_search_form( );
  echo "<br>Suggested Friends:";
  bp_show_friend_suggestions_list(50);
}  
  
function woow_pay_button (){
  
	global $bp;
  $excludedCategories = '|bp_activity_share | bp_activity_share | friendship_created|';
  $current_user = wp_get_current_user();
  $activityID =bp_get_activity_id();
  $activityType= bp_get_activity_type();
  $woowprice = bp_activity_get_meta($activityID ,'woowprice', true);
  $paypal_address = xprofile_get_field_data( 'Paypal address', bp_get_activity_user_id() );

  //  echo '<br><br><br><br><br>|'.$activityType.'|<br><br>excludedCategories|'.$excludedCategories.'|<br>|'.var_dump(strpos($excludedCategories, $activityType ) === false).'|<br><br>';
 
  if (strpos($excludedCategories, $activityType ) === false){
  
	if (bp_get_activity_user_id() == get_current_user_id() && strlen($paypal_address)>0 ){
	
	  echo "<div class='woowBuyDiv'> <p class='WOOWInputPrice WOOWInputPriceLabel'>Price(USD):</p>&nbsp; <input list='SggPrices' class='WOOWInputPrice' id=woow1 name='".$activityID."' min='0.01' step='0.01' max='2500'  title='Enter a price in US Dollars' value= '".$woowprice."' onblur='AsyncWOOWPriceUpdate(this, ".$activityID.")' /></div> <p id=lblStatus".$activityID." class='woowPriceStatusLabel' ></p>";
	}
	
	elseif (bp_get_activity_user_id() == get_current_user_id() ) {
	  echo "<a href='https://". $_SERVER['HTTP_HOST'] . "/members/" . $current_user->user_login . "/profile/edit' name='woowPaypalReminder' class='woowNoPaypalLabel'>To set pricing for your content tap here add your paypal address to your profile</a>"; 
	}  
	elseif (strlen($woowprice)>0 && strlen($paypal_address)>0 ) {
	  echo '<div class="woowDivBuyForm"> <form class="woowBuyForm" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			  <input type="hidden" name="cmd" value="_xclick">
			  <input type="hidden" name="business" value="'.$paypal_address.'">
			  <input type="hidden" name="lc" value="BM">
			  <input type="hidden" name="item_name" value="WOOWClover ActivityID:'.$activityID.'">
			  <input type="hidden" name="item_number" value="'.$activityID.'">
			  <input type="hidden" name="amount" value="'.$woowprice.'">
			  <input type="hidden" name="currency_code" value="USD">
			  <input type="hidden" name="button_subtype" value="services">
			  <input type="hidden" name="no_note" value="0">
			  <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">
			  <input class="WOOWBuyButton" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" data-alt="'.$woowprice.'" border="0" name="submit'.$activityID.'" alt="PayPal">
			  <img  alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form><a class="woowprice">$'.$woowprice.'</a></div>';
	  
	
  		}
	}
	
	if (strpos('I need a space at the beging not to return a false 0 bp_activity_share new_blog_post', $activityType )) { 
	global $wpdb;
	
	// if the activity type is a bp_activity_share i need to get the original post's id which is in the secondary_item_id field in a  bp_activity_share type post
	//	if (strpos('I need a space at the beging not to return a false 0 bp_activity_share', $activityType) ){
	//	$activityID = bp_get_activity_secondary_item_id();
	
	//	}
	$postID = getBlogPostID($activityID, 0 );
	if ($postID != 0) {
	  
	  $query = "
	  SELECT
		  count( * ) AS 'ISDonation'
	  FROM
		  ".$wpdb->base_prefix."term_taxonomy tt
		  INNER JOIN ".$wpdb->base_prefix."terms t
		   ON tt.term_id = t.term_id
		  INNER JOIN ".$wpdb->base_prefix."term_relationships tr
		   ON tt.term_taxonomy_id = tr.term_taxonomy_id
	  WHERE
		  tt.taxonomy = 'category'
		  and t.name = 'WOOWDonations'
		  AND tr.object_id = ".$postID;
	  //echo $query;
	  
	  if ( $wpdb->get_var($query))  {
		  makeDonationButton($activityID);
	  }
	}
}
} 
add_action( 'bp_activity_entry_meta' , 'woow_pay_button',15 );



//this function gets a blog post id from activities using recursive calls.

function getBlogPostID($activityID , $depth){
	$postID = 0;
  // this prevents infinite loop or excessive loops. it will give up after a set amount of loops
    if ($depth >100) {	
	  emailLog ("Vezi k function getBlogPostID($activityID , $depth) a depasit " .$depth." la sirul care include ActivityID=".$activityID, " O mica problema pe WOOWClover"); 	  
	  return;
	
	}
  	global $wpdb;
	$query = "SELECT id, type, secondary_item_id  FROM ".$wpdb->base_prefix."bp_activity where `id` = ".$activityID;
	
      if ($wpdb->get_var($query,1,0 ) == "bp_activity_share" ){
	$depth +=1;
	$postID = getBlogPostID($wpdb->get_var($query,2,0 ) , $depth);

  } elseif ($wpdb->get_var($query,1,0 ) == "new_blog_post"){
  	$postID = $wpdb->get_var($query,2,0 );
  }
 return $postID;
}

function record_woow_plusone_sale(){
  $plusone = 0;
  $activity_id = isset( $_POST['actvity_id'] ) ? $_POST['actvity_id']  : false;  
  if (strlen ($activity_id)>0) {
	$activity_id= filter_var($activity_id, FILTER_SANITIZE_NUMBER_INT);
	$field = isset( $_POST['metaname'] ) ? $_POST['metaname'] : false;  
    $plusone =  null !== bp_activity_get_meta( $activity_id, $field) ? bp_activity_get_meta( $activity_id, $field) : 0 ;
	$plusone +=1 ;
	$result = 	bp_activity_update_meta( $activity_id, $field, $plusone) ;
	$result = 	bp_activity_update_meta( $activity_id, "woowLastPlusOneDate", date("Y-m-d H:i:s")) ;
	}
  
 wp_die();
}


//this function sets up the price using a callback from ajax
function woow_update_price(){
  $actvity_id = isset( $_POST['actvity_id'] ) ? $_POST['actvity_id'] : false;  
  $field = isset( $_POST['metaname'] ) ? $_POST['metaname'] : false;  
  $value = isset( $_POST['metavalue'] ) ? $_POST['metavalue'] : false;  
  
  bp_activity_update_meta( $actvity_id, $field, $value) ;
 wp_die();
}

function woowclover_ajax(){
       //optional 
       wp_enqueue_script( 
          'woow_script', 
           plugin_dir_url( __FILE__ ) . 'js/woowclover_ajax.js', // path to js file for ajax operations
           array( 'jquery' ), false
       );
       //end optional
       wp_localize_script( 
          'ajax_script', // the name of your global.js registered file
          'ajax_object', // name 
           array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) // you can add other items for example for using a translated string in javascript/jquery context
       ); 
	  
	 
    }

function WOOWDonation(  ) { 
 global $post;
  if (in_category("WOOWDonations", $post)){
	makeDonationButton($post->ID);
  }
}



function makeDonationButton($activityID){

  echo '<br><select style="max-width:125px;" name="hidden'.$activityID.'" class="donationAmtSelect">
	<option value="1">$1.00 USD</option>
	<option value="2">$2.00 USD</option>
	<option value="5">$5.00 USD</option>
	<option value="10">$10.00 USD</option>
	<option value="35">$35.00 USD</option>
	<option value="50">$50.00 USD</option>
	<option value="75">$75.00 USD</option>
	<option value="100">$100.00 USD</option>
	<option value="150">$150.00 USD</option>
	<option value="1000">$1,000.00 USD</option>
</select> 

            <form   action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="business" value="woowclover@woowclover.com">
			<input type="hidden" name="lc" value="BM">
			<input type="hidden" name="item_name" value="WOOWClover ActivityID:'.$activityID.'">
			<input type="hidden" name="item_number" value="'.$activityID.'">
			<input id = "hidden'.$activityID.'"   type="hidden" name="amount" value="1.00">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="button_subtype" value="services">
			<input type="hidden" name="no_note" value="0">
			<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">
			<input type="image" src="'.site_url("/wp-content/uploads/2017/01/DLogo.jpg").'" border="0" name="submit'.$activityID.'" alt="PayPal" class ="BuyWoowPaypalButton">
			</form>';


  

}

add_action('template_redirect', 'woowclover_ajax');
// TAGUL PENTRU AJAX TREBUIE SA INCEAPA CU WP_AJAX CA ALTFEL NU LE EXECUTA 
add_action('wp_ajax_woow_update_price', 'woow_update_price');
add_action('wp_ajax_record_woow_plusone_sale','record_woow_plusone_sale');

add_action( 'rtp_hook_post_meta_bottom', 'WOOWDonation' ); // Post Meta Top

add_shortcode('woowGotPaidPage','gotpaid');


//copied from wp-content\themes\inspirebook\buddypress\activity\activityloop
function gotpaid(){
  
  
  	global $wpdb;
//get activity ids which have been clicked on the Buy or Donate button
    $dbquery = "select activity_id from " . $wpdb->base_prefix . "bp_activity_meta where meta_key = 'woowLastPlusOneDate' order by `meta_value` desc limit 20";
	$queryresult =   $wpdb->get_results($dbquery);
	$ids = " " ;

  foreach($queryresult as $row){
 		$ids = $ids  .",". $row->activity_id;
  }
  $ids = substr ($ids, 2);

  $postID = get_the_ID();
   // I have to add "buddypress" class to body of the html because there is a css selector that works only on the main body tag. I could do it with a filter but it will get called on every single page and return nothing except on this page.
    echo 
	  
	'<script>
		document.body.className += " buddypress";
		document.getElementById("post-'.$postID.'").className = "bp_activity type-bp_activity clearfix rtp-post-box post-0 page type-page status-publish hentry";

	</script>';

    echo '<div id="buddypress">';
	do_action( 'bp_before_directory_activity_list' ); 
  	echo '	<div class="activity" role="main"  aria-live="polite" aria-atomic="true" aria-relevant="all">' ;

	do_action( 'bp_before_activity_loop' );
	if ( bp_has_activities( bp_ajax_querystring( 'activity' )  .'&include='.$ids) ) {
	echo '	<noscript>
			<div class="pagination">
				<div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
				<div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
			</div>
	</noscript>';
	
  if ( empty( $_POST['page'] ) ) {
	echo '<ul id="activity-stream" class="activity-list item-list rtp-list woowgotpaidlist">';
	}

	while ( bp_activities() ) {
		bp_the_activity();
		bp_get_template_part( 'activity/entry' );
	}

	  /*	if ( bp_activity_has_more_items() ) {
		$activity_class = apply_filters( 'rtp_set_activity_class', '' );
		echo '<li class="load-more<'.esc_attr( $activity_class ).'">
		<a class="rtp-button" href="#more">'. _e( 'Load More', 'InspireBook' ).' </a>
		</li>';

} */

	if ( empty( $_POST['page'] ) ) {
		echo	'	</ul>';
		
	}
} else {
	echo '

	<div class="rtp-box-style">
		<div id="message" class="info">
			' ._e( 'Sorry, there was no activity found. Please try a different filter.', 'InspireBook' ).'
		</div>
	</div>';

}

do_action( 'bp_after_activity_loop' ); 

echo '<form action="" name="activity-loop-form" id="activity-loop-form" method="post">';

wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); 

echo '</form>' ; 


}


add_filter ( 'auth_cookie_expiration', 'wpdev_login_session' );
 
function wpdev_login_session( $expire ) { // Set login session limit in seconds
    return YEAR_IN_SECONDS;
    // return MONTH_IN_SECONDS;
    // return DAY_IN_SECONDS;
    // return HOUR_IN_SECONDS;
}




/*
function bp_after_has_activities_parse_args_func($r)
{
global $wpdb;
$sql = "SELECT activity_id FROM " . $wpdb->base_prefix . "bp_activity_meta WHERE meta_key = 'woowLastPlusOneDate' order by `meta_value` desc";

$ids = array_values($wpdb->get_col($sql));
  emailLog ("acum:".var_dump($ids), "de aici"); 
 
if (empty($ids)) {
emailLog ("acum:$ids", "de aici"); 
  
  
$r['in'] = '-1';
} else {
$r['in'] = "979,936,652,650,643,651";
}
  var_dump($r);
return $r;
}

add_filter('bp_after_has_activities_parse_args', 'bp_after_has_activities_parse_args_func');

*/
