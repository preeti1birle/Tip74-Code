<?php include('header.php'); ?>
<div class="mainContainer myentries_wrapr my_account" ng-controller="myEntriesController" ng-init="getAssignedEntries(true);getSetting()"
    ng-cloak>
    <div class="common_bg t-burger">
        <div class="container  ">
            <h1 class="text-center pb-3"> My Entries </h1>
            <div class="row">
                <div class="col-md-12 res_account">
                    <div class="site_box">
                        <div class="accountContent">
                            <div class="accountHolder col-md-12">
                                <div class="accountHolder col-md-6">
                                    <div class="col-md-3">
                                        <img ng-src="{{profileDetails.ProfilePic}}"
                                            on-error-src="assets/img/profile.svg" class="">
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="themeClr">{{profileDetails.FirstName}}</h5>
                                        <span class="ng-binding">{{profileDetails.Email}}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom_select">
                                        <select name="week" ng-model="SelectedWeekGUID" ng-required="true"
                                            ng-change="getUserBalance(SelectedWeekGUID)">
                                            <option ng-repeat="week in UpcomingWeekList" value="{{week.WeekGUID}}">
                                                <p>Week {{week.WeekCount}}</p>
                                                <p>({{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}})
                                                </p>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="clreafix"></div>
                            <div class="row myentryMetaWrapr">
                                <ul class="col-md-8 account_grid">
                                    <li class="text-center">
                                        <div>
                                            <strong> Entry </strong>
                                            <span>{{UserEntriesBalance.EntryNo}}</span>
                                        </div>
                                    </li>
                                    <li class="text-center">
                                        <strong> Prediction </strong>
                                        <span>
                                            {{UserEntriesBalance.ConsumedPredictions ? UserEntriesBalance.ConsumedPredictions : '0'}}
                                            /
                                            {{UserEntriesBalance.AllowedPredictions ? UserEntriesBalance.AllowedPredictions : '0'}}
                                        </span>
                                    </li>
                                    <li class="text-center">
                                        <strong> Double Ups </strong>
                                        <span>
                                            {{UserEntriesBalance.ConsumeDoubleUps ? UserEntriesBalance.ConsumeDoubleUps : '0'}}
                                            /
                                            {{UserEntriesBalance.AllowedPurchaseDoubleUps ? UserEntriesBalance.AllowedPurchaseDoubleUps : '0'}}
                                        </span>
                                    </li>
                                </ul>
                                <div class="col-md-4">
                                    <a href="javascript:void(0)" ng-click="getEntryList();getUserBalance(SelectedWeekGUID);openPopup('entryPopup')" class="btn_primary px-4  w-100"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Purchase Entry </a>
                                </div>
                             </div>
                            <div class="addAndWithdrawCash d-flex justify-content-center border-top py-3 border-bottom">
                                <div class="addCash col-lg-6 col-md-6 px-0 pr-md-1 px-lg-3">
                                    
                                </div>
                                <div class="col-lg-6 col-md-6 px-0 pl-md-1 px-lg-3">
                                   <!--  <a href="javascript:void(0)"
                                        ng-click="getEntryList();getUserBalance(SelectedWeekGUID);openPopup('doubleupPopup')"
                                        class="btn_gray px-4 w-100"> <i class="fa fa-credit-card fa-1x mr-2"
                                            aria-hidden="true"></i> Purchase Double ups </a> -->
                                </div>
                            </div>
                            <div class="transictionOption my_account">
                                <ul class="nav nav-tabs">
                                    
                                    <li class="nav-item">
                                        <a class="nav-link {{(activeTab == 'unAssignEntries')?'active':''}}" 
                                            data-toggle="tab" href="javascript:void(0)"
                                            ng-click="ChangeTab('unAssignEntries');getEntryList();">Un-Assigned Entries ({{TotalRecords}})</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{(activeTab == 'assignedEntries')?'active':''}}" 
                                            data-toggle="tab" href="javascript:void(0)"
                                            ng-click="ChangeTab('assignedEntries');">Assigned Entries</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="withdrawal"
                                        class="tab-pane {{(activeTab == 'assignedEntries')?'show active':''}}">
                                        <h5 class="pull-left p-2">Assigned Entries ({{UnAssignedTotalRecords}})</h5>
                                        <div class="table-responsive table-striped" style="height: 400px;overflow: auto;overflow-x: hidden;" scrolly>
                                            <table class="mt-2 table table-borderless common_table text-white">
                                                <thead>
                                                    <tr>
                                                        <th> Entry No </th>
                                                        <th> Week No </th>
                                                        <th title="Consumed Predictions/Allowed Predictions"> Predictions </th>
                                                        <th title="Consumed DoubleUps/Allowed DoubleUps"> Doubleups </th>
                                                    </tr>
                                                </thead>
                                                <tbody >
                                                    <tr ng-repeat="entries in assignedEntriesList">
                                                        <td>{{entries.EntryNo}}</td>
                                                        <td>Week {{entries.WeekCount}}</td>
                                                        <td>{{entries.ConsumedPredictions}}/{{entries.AllowedPredictions}}</td>
                                                        <td>{{entries.ConsumeDoubleUps}}/{{entries.AllowedPurchaseDoubleUps}}</td>
                                                    </tr>
                                                    <tr ng-if="assignedEntriesList.length == 0">
                                                        <td colspan="4" class="text-center">No Entries found.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="transaction"
                                        class="tab-pane {{(activeTab == 'unAssignEntries')?'show active':''}}">
                                        <div class="row" ng-if="TotalRecords>0">
                                            <div class="col">
                                                <h5 class="pull-left p-2" >UnAssigned Entries: {{TotalRecords}} </h5>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive" style="height: 400px;overflow: auto;overflow-x: hidden;" scrolly>
                                            <table class="mt-2 table table-borderless common_table text-white"
                                                style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th> Entry No </th>
                                                        <th> Predictions </th>
                                                        <th> Doubleups </th>
                                                        <th> Assign </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="entries in unAssignedEntriesList">
                                                        <td>{{entries.EntryNo}}</td>
                                                        <td>{{entries.AllowedPredictions}}</td>
                                                        <td>{{entries.AllowedPurchaseDoubleUps}}</td>
                                                        <td>                                            
                                                            <form name="EntryForm" novalidate="true">
                                                            <div class=row>
                                                                <div class="col-6  text-center">
                                                                    <div  class="custom_select">
                                                                        <select name="week" ng-model="SelectedWeekGUID" ng-required="true" ng-change="getUserBalance(SelectedWeekGUID)">
                                                                            <option ng-repeat="week in UpcomingWeekList" value="{{week.WeekGUID}}">
                                                                            <p>Week {{week.WeekCount}}</p>
                                                                            <p>({{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}})</p>
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 form-group" style="display:none;">
                                                                    <label>How many entries you would like to assign?</label>
                                                                    <div class="custom_select">
                                                                        <select class="customReadOnlyField" ng-required="true" name="assigningEntries" ng-model="assigningEntries" ng-change="getAssignEnt(assigningEntries)">
                                                                            <!-- <option ng-repeat="n in [].constructor(UnAssignedEntries) track by $index" value="{{$index+1}}" >{{$index+1}} Entries</option> -->
                                                                            <option value="1" >1 Entry</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6  text-center">
                                                                    <div class="button_right text-center">
                                                                        <button class="btn_gradient px-4 py-2" ng-click="assignEntry(SelectedWeekGUID, entries.GameEntryID)">Assign Entry</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <tr ng-if="assignedEntriesList.length == 0">
                                                        <td colspan="4" class="text-center">No Entries found.</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div ng-if="TotalRecords == 0">
                                                <h5 colspan="4" class="mt-4 text-center">Sorry, you don't have any entries to assign.</h5>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footerHome.php'); ?>