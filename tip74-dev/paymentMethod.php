<?php include('header.php'); ?>
<!--Main container sec start-->
<div class="mainContainer" ng-controller="headerController" ng-cloak >
    <div class="common_bg t-burger">
        <div class="container text-center">
            <div class="top-header-title mt-4">
                <h3> Select Payment Method </h3>
                <p> Pay for {{moneyFormat(amount)}} </>
            </div>
            <div class="paymentSec mt-5">
                <div class="paymentBox">
                    <div class="paymentHead bg-gradient">
                        Pay Via
                    </div>
                    <div class="paymentBody">
                        <p>After selecting a payment method, you will be directed to a secure gateway for payment.</p>
                        <div class="">
                            
                                <button id="payButton" class="btn_primary" ng-click="StripeReq(amount)"> <i class="fa fa-cc-mastercard"></i> Stripe </button>
                                <input type="hidden" id="payProcess" value="0"/>
                           
                        </div>
                        <!-- <p>By proceeding, you have read and agreed to FSL11 <a target="_blank" href="TermConditions">Terms and Conditions</a> and <a target="_blank" href="privacyPolicy">Privacy Policy</a></p>  -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Main container sec end-->
<?php include('footerHome.php'); ?>