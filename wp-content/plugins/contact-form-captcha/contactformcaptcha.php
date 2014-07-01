<?php

if(strlen($_POST[cName])> 1)
{

  
  $recipient = get_option('contact_captcha_email');
  $mail_body = $_POST[cMsg]; //mail body
  $subject = "Message from your Blog"; 
  $header = "From: ". $_POST[cName]."<".$_POST[cEmail].">"; 
  mail($recipient, $subject, $mail_body, $header); 
  $errormessage = "<span class='contactmsg'>Mail Sent Successfully</span>";
 
 
}

?>

<form name='conForm' id='conForm' method="POST" action="" onsubmit="return validateform(this)">
<div id="errormsg"><?php echo $errormessage; ?></div>
<p><label class='cLabel'> Name</label></p><p><input class="democlass" type='text' size="33" name='cName' id='cName' value="<?php echo $_POST[cName]; ?>"/></p>
<p><label class='cLabel'> Email</label></p><p><input type='text' size="33" name='cEmail' id='cEmail' value="<?php echo $_POST[cEmail]; ?>"/></p>
<p><label class='cLabel'> Security</label></p><table><tr><td>
<a href="http://eazeenet.in"><img id="capimg" src="<?php echo get_bloginfo('wpurl') . '/wp-content/plugins/contact-form-captcha/image1.php';?>"/></a> </td>
<td><input type='text' size="10" name='spam' id='spam' value="<?php echo $_POST[spam]; ?>"/></td><td><img src="<?php echo get_bloginfo('wpurl') . '/wp-content/plugins/contact-form-captcha/reload.gif';?>" onclick="spam1();" /></td>
</table>
<script type="text/javascript">
spam1();
</script>
<p><label class='cLabel'> Message</label></p><p><textarea  rows="10"  cols="55" name='cMsg' id='cMsg'><?php echo $_POST[cMsg]; ?></textarea></p>
<p><input type="submit" name="cSubmit" id="cSubmit" value="Submit" />
</form>
