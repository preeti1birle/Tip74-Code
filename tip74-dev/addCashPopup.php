<!--addmoney-->
<div class="modal fade site_modal AddCashModal pr-0" popup-handler id="add_money" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true" >
    <div class="modal-dialog modal_small">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add funds to Your Account</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
                
            </div>
            <div class="modal-body text-center clearfix comon_body ammount_popup">
                <form ng-submit="selectPaymentMode(amount,addCashForm)" name="addCashForm" novalidate="true">
                    <div class="form-group">
                        <label>How much you would like to add.</label>
                        <input placeholder="Enter amount." class="form-control numeric" name="amount" type="text" ng-model="amount" numbers-only  ng-required="true" ng-readonly="isPromoCode">
                        <div style="color:red" ng-show="cashSubmitted && addCashForm.amount.$error.required" class="form-error">
                            *Amount is Required
                        </div>
                        <div class="text-danger" ng-if="errorAmount">{{errorAmountMsg}}</div>
                    </div>
                    <div class="add_money text-center">
                        <h6 class="">ADD MORE CASH</h6>
                        <div class="mb-3 mt-2 ">
                            <button type="button" class="btn btn-submit theme_bgclr" ng-click="addExtraCash(250)" ng-disabled="isPromoCode" >{{Currency}} 250 </button>
                            <button type="button" class="btn btn-submit theme_bgclr" ng-click="addExtraCash(500)" ng-disabled="isPromoCode" >{{Currency}} 500</button>
                            <button type="button" class="btn btn-submit theme_bgclr" ng-click="addExtraCash(1000)" ng-disabled="isPromoCode" >{{Currency}} 1000</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="customCheckbox">
                            <input type="checkbox" name="promoCode" ng-model="isPromoCode" ng-click="resetPromo(isPromoCode)">
                             <label> Have a promo code.</label>
                        </div>
                    </div>
                    <div class="form-group applyBox" ng-if="isPromoCode && !PromoCodeFlag">
                        <input type="text" class="form-control" name="promocode" ng-model="PromoCode">
                        <a href="javascript:void(0)" class="btn_primary " ng-click="applyPromoCode(PromoCode,amount)" >Apply</a>
                    </div>
                    <div class="promocodeList" ng-if="isPromoCode" >
                    <p ng-if="PromoCodeFlag" class="h6"><span>Coupon Code </span>: {{PromoCode}} <a href="javascript:void(0)" ng-click="removeCoupon()"><i class="fa fa-trash"></i></a></p>
                    <p class="h6" ng-if="GotCashBonus>0"><span>Cash Bonus </span>: ₹. {{GotCashBonus}}</p>
                    </div>
                    <div class="button_right text-center"><!-- href="paymentMethod?amount={{amount}}" -->
                        <button class="btn_gradient px-4 py-2"> ADD CASH </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--addmoney