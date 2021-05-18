
<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">
Hi <?php echo $Name;?>,</p>

<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">Welcome to <?php  echo SITE_NAME; ?>.</p>

<?php if(!empty($Token)){ ?>

<?php if($DeviceTypeID!=1){?>
<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">Please verify your email address using the code given below.</p>
<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:600; margin-bottom:30px;">
	<?php echo $Token; ?>
<?php }else{?>
<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">Verify your email address by clicking below URL</p>
<a href="<?php echo BASE_URL.'Signup/verify/?otp='?><?php echo $Token; ?>"><?php echo BASE_URL.'Signup/verify/?otp='?><?php echo $Token; ?></a>
<?php } ?>
</p>
<?php } ?>

<p style="font-family: 'Roboto', sans-serif; font-size:16px; color:#444444; font-weight:500; margin-bottom:30px;">Thank you for registering with <span><?php echo SITE_NAME;?></span>.</p>