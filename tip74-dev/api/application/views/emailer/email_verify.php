
<?php if(empty($Error)){?>
<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">
Hi <?php echo $UserData['FirstName'];?>,</p>

<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">Your account has been successfully verified, please login to get access your account.</p>

<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">Thank you for registering with <span><?php echo SITE_NAME;?></span>.</p>

<?php }else{ ?>

<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;"><?php echo $Error ;?></p>

<?php } ?>




