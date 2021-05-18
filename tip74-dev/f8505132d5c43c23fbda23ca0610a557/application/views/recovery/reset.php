<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="container" ng-controller="PageController"> 
 <div id="logo" class="text-center"><img src="<?php echo API_URL;?>asset/img/emailer/logo.png"></div> 
  <!-- Form -->
  <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5 login-block">
    <h1 class="h3">Reset password</h1>
    <br>
    <form method="post" id="recovery_reset_form" name="recovery_reset_form"  autocomplete='off'>
      <div class="form-group">
        <input type="text" id="OTP" name="OTP" class="form-control form-control-lg" placeholder="OTP"  autofocus="" maxlength="6">
      </div>

      <div class="form-group">
        <input type="password" id="Password" name="Password" class="form-control form-control-lg" placeholder="Password">
      </div>

      <div class="form-group">
       <input type="password" id="retype" name="retype" class="form-control form-control-lg" placeholder="Retype New Password">
     </div>

     <div class="form-group">
      <button type="button" class="btn btn-success  btn-sm" ng-disabled="processing" ng-click="reset()">Reset</button>
      <span class="float-right"><a href="signin" class="a">Sign in?</a></span>
    </div>

  </form>
</div>
</div><!-- / container -->