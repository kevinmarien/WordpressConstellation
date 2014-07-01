<h2>Contact Form Captcha Options</h2>
<form name="coolformoption" id="coolformoption" method="POST" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<p>Enter the email address, you want to recieve emails from the contact page</p>
<label>Email Address: </label>
<input type="text" name="contact_captcha_email" size="30" value="<?php echo get_option('contact_captcha_email'); ?>" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="contact_captcha_email" />
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
