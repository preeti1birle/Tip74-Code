
</section>
</div> <!-- container/ -->
</div> <!-- mainFrame/ -->

<div class="modal fade" id="changePassword_modal" >
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title h5">Change Password</h3>     	
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>

			<!-- Filter form -->
			<form id="changePassword_form" role="form" name="changePassword_form" autocomplete="off" class="ng-pristine ng-valid">
				<div class="modal-body">
					<div class="form-area">

						<div class="row">
							<div class="col-md-8">
								<div class="form-group">
									<input type="password" name="CurrentPassword" class="form-control"  placeholder="Current Password">
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<input type="password" name="Password" class="form-control" placeholder="New Password">
								</div>
							</div>
						</div>

					</div> <!-- form-area /-->
				</div> <!-- modal-body /-->

				<div class="modal-footer">
					<button type="submit" class="btn btn-success btn-sm"  ng-disabled="changeCP" ng-click="changePassword()">Submit</button>
				</div>

			</form>
			<!-- Filter form/ -->
		</div>
	</div>
</div>

<!-- Page Loader -->
<div ng-if="data.pageLoading" class="text-center page-loader"><span>Loading&#8230;</span></div>


<!-- FOOTER -->
<!-- <footer>
	<div class="container-fluid">
		<p class="text-muted float-right small mb-0">© <?php //echo SITE_NAME;?> ‐ Powered by <a href="https://exact11.com/" target="_blank">Exact fantasy sports pvt. ltd.</a>.</p>
	</div>
</footer> -->

<!-- BOOTSTRAP JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<!-- App.js -->
<script src="asset/js/app.js"></script>
<!-- Alertify JS -->
<script src="asset/plugins/alertify/alertify.min.js"></script>

<!-- Other Plugins JS -->
<?php if(!empty($js)){foreach($js as $value){ ?>
<script src="<?php echo $value; ?>"></script>
<?php }} ?>

<!-- Add icon library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script>
$(document).ready(function(){

  $(".menu-toggel").click(function(){
	$(".main-navbar").toggleClass("sidebar_hide");
  });

});
</script>

</body>
</html>