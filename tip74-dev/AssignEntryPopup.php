<div class="modal fade site_modal AddCashModal pr-0" popup-handler id="assignPopup" tabindex="-1" role="dialog" aria-labelledby="modalLabelSmall" aria-hidden="true" >
        <div class="modal-dialog modal_small">
            <!-- <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Assign Entries</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body text-center  clearfix comon_body ammount_popup" ng-init="mode = 'Entry'">
                    <form name="EntryForm" novalidate="true">
                        <div class="mb-3 text-center">
                            <div class="custom-control  custom-control-inline">
                            UnAssigned Entries: {{UnAssignedEntries.TotalRecords}}</div>
                        </div>
                        <div class="mb-3 text-center">
                           
                        </div>
                        <div class="form-group" ng-if="UnAssignedEntries.TotalRecords>0">
                            <label>Which entry you would like to assign?</label>
                            <div class="custom_select">
								<select class="customReadOnlyField" ng-required="true" name="assigningEntries" ng-model="assigningEntries" ng-change="getAssignEnt(assigningEntries)">
									<option ng-repeat="n in (UnAssignedEntries.Records) track by $index" value="{{n.GameEntryID}}" >Entry No. {{n.EntryNo}}</option>
                                </select>
							</div>
                        </div>
                        <div class="">
                            <div class="button_right text-center">
                                <button class="btn_gradient px-4 py-2" ng-click="assignEntry(WeekGUID, assigningEntries)">Assign</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Purchase Entry </h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body text-center  clearfix comon_body ammount_popup" ng-init="mode = 'Entry'">
                    <form name="EntryForm" novalidate="true">
                        <div class="mb-3 text-center">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="mode" ng-model="mode"  value="Entry" class="custom-control-input" id="Entry" checked/> <label class="custom-control-label" for="Entry">Entries</label>
                            </div>
                        </div>
                        <!-- <div class="mb-3 text-center">
                           <label>Upcoming Weeks</label>
                            <div  class="custom_select">
								<select name="week" ng-model="SelectedWeekGUID" ng-required="true" ng-change="getUserBalance(SelectedWeekGUID)">
									<option ng-repeat="week in UpcomingWeekList" value="{{week.WeekGUID}}">
                                    <p>Week {{week.WeekCount}}</p>
                                    <p>({{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}})</p>
                                    </option>
								</select>
							</div>
                        </div> -->
                        <div class="form-group">
                            <!-- <label>How many entries you would like to purchase?</label> -->
                            <div class="custom_select">
								<select class="customReadOnlyField" ng-required="true" name="entry" ng-model="EntriesID" ng-change="showEntryInfo(EntriesID)" disabled >
									<option ng-repeat="entry in EntryList" value="{{entry.EntriesID}}" >{{entry.NoOfEntries}} Entries</option>
								</select>
							</div>
                        </div>
                        <!-- <div class="mb-2 mt-4 custom-control custom-checkbox" style="display:none">
							<input type="checkbox"  name="DoubleUp" ng-model="Info.DoubleUps" ng-change="calcuateAmount()"  id="customCheck1" style="height: 19px;width: 19px;position: relative;
                        top: 4px;">
                            <label  for="customCheck1">Want to purchase Double Ups</label>
                        </div> -->
                        <div class="form-group">
                            <ul class="select_list">
                                <li>No. Of Prediction : <span>{{EntryInfo.NoOfPrediction}}</span></li>
                                <!-- <li>No. Of Double Ups : <span>{{EntryInfo.NoOfDoubleUps}}</span></li> -->
                                <li>Entries Amount : <span>{{moneyFormat(EntryInfo.EntriesAmount)}}</span></li>
                                <!-- <li>Per Double Ups Amount : <span>{{moneyFormat(Info.PerDoubleUpPrice)}}</span></li> -->
                                <!-- <li ng-if="Info.DoubleUps">Total Double Ups Amount : <span>{{moneyFormat(EntryInfo.NoOfDoubleUps*Info.PerDoubleUpPrice)}}</span></li> -->
                                <!-- <li>Total Purchase Amount : <span>{{moneyFormat(calcuateAmount())}}</span></li> -->
                                <li>Total Wallet Amount : <span>{{moneyFormat(profileDetails.WalletAmount)}}</span></li>
                            </ul>
                        </div>
                        <!-- <div class="form-group" ng-if="mode == 'Double'">
                            <label ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps > 0">You have {{UserEntriesBalance.RemainingPurchaseDoubleUps}} Double ups are remained to purchase.</label>
                            <label ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps == 0">Sorry, you don't have any remain double ups.</label>
                            <input ng-if="UserEntriesBalance.RemainingPurchaseDoubleUps > 0" placeholder="Double Ups" class="form-control numeric" name="NoOfDoubles" type="number" ng-model="Info.NoOfDoubles"  ng-required="true" min="1" max="{{UserEntriesBalance.RemainingPurchaseDoubleUps}}"  >
                        </div>
                        <div class="form-group" ng-if="mode == 'Double' && UserEntriesBalance.RemainingPurchaseDoubleUps > 0">
                            <ul class="select_list">
                                <li>No. Of Double Ups : {{Info.NoOfDoubles}}</li>
                                <li>Per Double Ups Amount : <span>{{moneyFormat(Info.PerDoubleUpPrice)}}</span></li>
                                <li>Total Purchase Amount : <span>{{moneyFormat(Info.NoOfDoubles*Info.PerDoubleUpPrice)}}</span></li>
                                <li>Total Wallet Amount : <span>{{moneyFormat(profileDetails.WalletAmount)}}</span></li>
                            </ul>
                        </div> -->
                        <div class="">
                            <div class="button_right text-center">
                                <button class="btn_gradient px-4 py-2"  ng-click="purchaseEntryFromHeader(WeekGUID)">Purchase</button>
                                <!-- <button class="btn_gradient px-4 py-2" ng-if="mode == 'Double' && UserEntriesBalance.RemainingPurchaseDoubleUps > 0" ng-click="purchaseDouble(SelectedWeekGUID)">Purchase</button> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>