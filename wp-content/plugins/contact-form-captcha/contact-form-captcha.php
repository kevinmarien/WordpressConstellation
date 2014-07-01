<?php
/*
Plugin Name: Contact Form with Captcha
Plugin URI: http://www.eazeenet.in/
Version: 1.00
Author: Vinayak
Author URI: http://www.eazeenet.in
Description: Simple Contact Form with Captcha Enabled
*/

function include_js_file()
{
    echo '<script language="JavaScript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/contact-form-captcha/cfc.js" type="text/javascript"></script>' . "\n";
    echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/contact-form-captcha/contact.css" />' . "\n";
   
}

function display_contact_form($content)
{
  if(false !== strpos($content, '<!--contact form-->'))
  {
  echo $content;
    include('contactformcaptcha.php');
  }
  else
  {
    return $content;
  }
}

function contact_form_captcha_options()
{
	include('contactformcaptchaoptions.php');
}

function contactform_captcha()
{
  add_options_page('Contact Form with Captcha Options', 'Contact Form Captcha', 8, __FILE__, 'contact_form_captcha_options');
}

add_action('wp_head','include_js_file');
add_action('admin_menu', 'contactform_captcha');
add_filter('the_content','display_contact_form');
?>
