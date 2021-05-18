<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul >
                    <li><a class="{{PageName == 'AboutUs'?'showActiveTab':''}}" href="AboutUs">About Us</a></li>
                    <li><a class="{{PageName == 'ContactUs'?'showActiveTab':''}}" href="ContactUs">Contact Us</a></li>
                    <li><a class="{{PageName == 'PrivacyPolicy'?'showActiveTab':''}}" href="PrivacyPolicy">Privacy Policy</a></li>
                    <li><a class="{{PageName == 'TermAndCondition'?'showActiveTab':''}}" href="TermAndCondition">Terms & Conditions</a></li>
                    <li><a class="{{PageName == 'Disclaimer'?'showActiveTab':''}}" href="Disclaimer">Disclaimer</a></li>
                    <li ng-if="GamesType == 'Soccer'"><a class="{{PageName == 'SoccerRules'?'showActiveTab':''}}" href="SoccerRules">Rules</a></li>
                    <li ng-if="GamesType == 'Horse Racing'"><a class="{{PageName == 'HorseRacingRules'?'showActiveTab':''}}" href="HorseRacingRules">Rules</a></li>
                    <li ng-if="GamesType == 'Soccer'"><a class="{{PageName == 'SoccerPoints'?'showActiveTab':''}}" href="SoccerPoints">Points</a></li>
                    <li ng-if="GamesType == 'Horse Racing'"><a class="{{PageName == 'HorseRacingPoints'?'showActiveTab':''}}" href="HorseRacingPoints">Points</a></li>
                    <li><a  href="#RaceDubbleup" data-toggle="modal" >Double Up</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="copyright">
        Copyright &copy; Tip74 . All rights resrved.
    </div>
</footer>
<?php include('modal.php') ?>
<!-- <div loading class="loderBG flex-container" id="loderBG">
    <img src="assets/img/loader.svg" alt="loader">
</div>  -->
<add-cash></add-cash>
<add-assign-entry></add-assign-entry>
<add-purchase-request></add-purchase-request>
<redirect-purchase-request></redirect-purchase-request>
<add-doubleup-request></add-doubleup-request>
<add-withdrawal-request></add-withdrawal-request>
<script type="text/javascript" src="assets/js/jquery-3.5.1.slim.min.js"></script>
<script type="text/javascript" src="assets/js/popper.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/slick.min.js"></script>
<script type="text/javascript" src="assets/js/custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js"></script>
<!-- load angular -->
<script type="text/javascript" src="assets/js/angular-modules/angular.min.js" ></script>
<!-- angular storage -->
<script type="text/javascript" src="assets/js/angular-modules/ngStorage.min.js" ></script>
<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular-cookies.min.js" ></script> -->
<!-- MAIN CONTROLLER -->
<script type="text/javascript" src="assets/js/app.js?version=<?= VERSION ?>"></script>
<script type="text/javascript">
    var base_url = "<?php echo $base_url;?>";
    var UserGUID, UserTypeID, ParentCategoryGUID = '';
    app.constant('environment', {
        base_url: "<?php echo $base_url;?>",
        api_url: "<?php echo $api_url;?>",
        image_base_url: '<?php echo $base_url; ?>assets/img/',
        brand_name: 'TIP74'
    });
    app.config(function(socialProvider){
        // socialProvider.setGoogleKey("911285713331-5epvisg4tpjoa41bopkfs1aip1gpjoaf.apps.googleusercontent.com");
        // socialProvider.setFbKey({appId: "426400607971745", apiVersion: "v2.11"});
    });
</script>

<!-- common service -->
<script type="text/javascript" src="assets/js/services/database.fac.js?version=<?= VERSION ?>"></script>
<!-- common directive -->
<script type="text/javascript" src="assets/js/directive/design-directive.lib.js?version=<?= VERSION ?>"></script>
<!-- helper -->
<script type="text/javascript" src="assets/js/helper/helper.js?version=<?= VERSION ?>"></script>
<!-- validations -->
<script type="text/javascript" src="assets/js/directive/validation.lib.js?version=<?= VERSION ?>"></script>
<!-- social ligin library -->
<script type="text/javascript" src="assets/js/angularjs-social-login/angularjs-social-login.js?version=<?= VERSION ?>" ></script>
<!-- Angular animate js -->
<script type="text/javascript" src="assets/js/angular-animate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-sanitize/1.5.7/angular-sanitize.min.js"></script>
<!-- toaster message -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/angular-toastr@2/dist/angular-toastr.tpls.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/angular-toastr@2/dist/angular-toastr.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.25.3/moment.min.js"></script>
<script type="text/javascript" src="assets/js/angularjs-datetimepicker/datetimepicker.js"></script>
<script type="text/javascript" src="assets/js/angularjs-datetimepicker/datetimepicker.templates.js"></script>
<script type="text/javascript" src="assets/js/select2.js"></script>
<!-- Header controller -->
<script type="text/javascript" src="assets/js/controllers/header.js?version=<?= VERSION ?>"></script>
<?php if($PathName == ''){?>
    <script type="text/javascript" src="assets/js/controllers/home.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'profile'){ ?>
<!-- profile controller -->
<script type="text/javascript" src="assets/js/controllers/profile.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'dashboard'){ ?>
<!-- dashboard controller -->
<script type="text/javascript" src="assets/js/controllers/dashboard.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'myAccount'){ ?>
    <script type="text/javascript" src="assets/js/controllers/account.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'myEntries'){ ?>
    <script type="text/javascript" src="assets/js/controllers/entries.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'leaderboard'){ ?>
    <script type="text/javascript" src="assets/js/controllers/leaderboard.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'live-result-inner'){ ?>
    <script type="text/javascript" src="assets/js/controllers/live_result.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'prediction' || $PathName == 'FullStanding'){ ?>
    <script type="text/javascript" src="assets/js/controllers/prediction.js?version=<?= VERSION ?>"></script>
<?php }else if($PathName == 'myPrediction'){ ?>
    <script type="text/javascript" src="assets/js/controllers/myPrediction.js?version=<?= VERSION ?>"></script>
<?php } ?>
<!-- Stripe JavaScript library -->
<script src="https://checkout.stripe.com/checkout.js"></script>
</body>
</html>


