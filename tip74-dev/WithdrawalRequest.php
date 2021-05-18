<!--withdraw money modal-->
<div class="modal fade site_modal AddCashModal pr-0" popup-handler id="withdrawPopup" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true" >
    <div class="modal-dialog small_popup">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Withdraw Funds</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body clearfix comon_body ammount_popup">
                <form name="withdrawForm" novalidate="true">
                    <!-- <div class="form-group text-center">
                        <div class="customCheckbox">
                            <input type="radio" name="mode" ng-model="mode" value="Bank"> <label>Bank</label>
                        </div>
                    </div> -->
                    <div class="form-group" >
                        <label>How much you would like to withdraw ?</label>
                        <input placeholder="{{Currency}}50" class="form-control numeric" name="amount" type="text" ng-model="test.amount" numbers-only ng-required="true"  >
                        <div ng-show="withdrawSubmitted && withdrawForm.amount.$error.required" class="text-danger form-error">
                            *Amount is Required.
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="button_right text-center">
                            <button class="btn_gradient px-4 py-2" ng-click="withdrawRequest(withdrawForm, test.amount,'Bank')">Withdraw</button>
                        </div>
                        <ul class="mt-2">
                            <p style="white-space: pre-line;">{{profileDetails.Message}}</p>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>