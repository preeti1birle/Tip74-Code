<div class="modal fade purchase_modal site_modal" popup-handler id="doubleupPopup" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true" >
        <div class="modal-dialog modal_small">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Purchase Double ups</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body text-center  clearfix comon_body ammount_popup" ng-init="mode = 'Double'">
                    <label> Sorry you have no remaining double ups left for this entry.
                    </label>

                    <!-- <form name="EntryForm" novalidate="true">
                        <div class="mb-3 text-center">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="mode" ng-model="mode" checked value="Double" class="custom-control-input" id="Double-ups"><label class="custom-control-label" for="Double-ups"> Double ups</label>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps > 0">You have {{UserEntriesBalance.RemainingPurchaseDoubleUps}} Double ups are remaining to purchase.</label>
                            <label ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps == 0">Sorry, you don't have any remaining double ups.</label>
                            <input ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps > 0" placeholder="Double Ups" class="form-control numeric" name="NoOfDoubles" type="number" ng-model="Info.NoOfDoubles"  ng-required="true" min="1" max="{{UserEntriesBalance.RemainingPurchaseDoubleUps}}"  >
                        </div>
                        <div class="form-group" ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps > 0">
                            <ul class="select_list"> -->
                                <!-- <li>No. Of Double Ups : {{Info.NoOfDoubles}}</li> -->
                                <!-- <li>Per Double Ups Amount : <span>{{moneyFormat(Info.PerDoubleUpPrice)}}</span></li> -->
                                <!-- <li>Total Purchase Amount : <span>{{moneyFormat(Info.NoOfDoubles*Info.PerDoubleUpPrice)}}</span></li> -->
                                <!-- <li>Total Wallet Amount : <span>{{moneyFormat(profileDetails.WalletAmount)}}</span></li>
                            </ul>
                        </div>
                        <div class="">
                            <div class="button_right text-center">
                                <button class="btn_gradient px-4 py-2" ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps > 0" ng-click="purchaseDouble(SelectedWeekGUID)">Purchase</button>
                            </div>
                        </div>
                    </form> -->
                </div>
            </div>
        </div>
    </div>