<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="luckyWheelController" ><!-- Body -->

    <!-- Top container -->
    <div class="clearfix mt-2 mb-2 d_flex">
        <span class="float-left records hidden-sm-down flex_1">
            <span ng-if="data.dataList.length" class="h5">Total Records: {{data.totalRecords}}</span>
        </span>

        <!-- <div>
            <div class="float-right">
                <form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter()" class="ng-pristine ng-valid">
                    <input type="text" class="form-control" name="Keyword" placeholder="Search">
                </form>
            </div>
            <div class="float-right">
                <button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>&nbsp;
            </div>
            <div class="float-right">
                <button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
            </div>
            <div class="float-right">
                <button class="btn theme_btn btn-secondary btn-sm ng-scope" ng-click="ExportList()">Export</button>&nbsp;
            </div>
        </div>	 -->
    </div>
    <!-- Top container/ -->



    <!-- Data table -->
    <div class="table-responsive block_pad_md" ng-init="getListWheel()"> 

        <!-- loading -->
        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>
        <form name="records_form" id="records_form">
            <!-- data table -->
            <div class="all-table-scroll">
                <table class="table table-striped table-hover all-table-scroll-item coupon_pad" ng-if="data.dataList.length">
                    <!-- table heading -->
                    <thead>
                        <tr>
                                <!-- <th style="width: 50px;" class="text-center" ng-if="data.dataList.length>1"><input type="checkbox" name="select-all" id="select-all" class="mt-1" ></th> -->	
                            <th>User</th>
                            <!-- <th>Contact No.</th> -->
                            <!-- <th>Gender</th>
                            <th>Date of Birth</th> -->
                            <th>Points</th>
                            <th>Entry Date</th>
                            <!-- <th style="width: 200px;">Role</th> -->
                            <!-- <th class="sort" ng-click="applyOrderedList('E.EntryDate', 'ASC')">Registered On <span class="sort_deactive">&nbsp;</span></th>
                            <th class="text-center">Last Login</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th> -->

                        </tr>
                    </thead>
                    <!-- table body -->
                    <tbody>
                        <tr scope="row" ng-repeat="(key, row) in data.dataList">

                            <!-- <td class="listed sm clearfix table_list">
                                <a target="_blank" href="userdetails?UserGUID={{row.UserGUID}}"><img class="rounded-circle float-left" ng-src="{{row.ProfilePic}}"></a>

                                <div class="content float-left user_table"><strong><a target="_blank" href="userdetails?UserGUID={{row.UserGUID}}">{{row.FullName}}</a></strong>

                                    <div ng-if="row.Email || row.EmailForChange" class="user_table"><a href="mailto:{{row.Email == '' ? row.EmailForChange : row.Email}}" target="_top">{{row.Email == "" ? row.EmailForChange : row.Email}}</a></div><div ng-if="!row.Email && !row.EmailForChange">-</div>
                                    <span ng-if="row.Email || row.EmailForChange" ng-class="{Pending:'text - danger', Verified:'text - success',Deleted:'text - danger',Blocked:'text - danger'}[row.EmailStatus]">({{row.EmailStatus}})</span>
                                </div>

                            </td>  -->

                            <!-- <td><span>{{row.PhoneNumber == "" ? row.PhoneNumberForChange : row.PhoneNumber }}<br></span><span ng-if="row.Email || row.EmailForChange" ng-class="{Pending:'text - danger', Verified:'text - success',Deleted:'text - danger',Blocked:'text - danger'}[row.PhoneStatus]">({{row.PhoneStatus}})</span></td>  -->
                            <!-- <td><span ng-if="row.Gender">{{row.Gender}}</span><span ng-if="!row.Gender">-</span></td> 
                            <td><span ng-if="row.BirthDate">{{row.BirthDate}}</span><span ng-if="!row.BirthDate">-</span></td>  -->
                            <!-- <td class="text-center"><span ng-if="row.ReferredCount"><a class="text-success" href="javascript:void(0)" ng-click="loadFormReferredUsersList(key, row.UserGUID)" >{{row.ReferredCount}}</span><span ng-if="!row.ReferredCount">-</span></td>  -->
                            <!-- <td ng-bind="row.UserTypeName"></td>  -->
                            <td>{{row.FirstName}}</td>
                            <td>{{row.Value}}</td>  
                            <td>{{row.EntryDate}}</td>  

                            <!-- <td><span ng-if="row.LastLoginDate">{{row.LastLoginDate}}</span><span ng-if="!row.LastLoginDate">-</span></td> 
                            <td class="text-center"><span ng-class="{Pending:'text - danger', Verified:'text - success',Deleted:'text - danger',Blocked:'text - danger'}[row.Status]">{{row.Status}}</span><br><button class="btn theme_btn btn-secondary btn-sm action" type="button" ng-if="row.EmailForChange != '' || row.Status == 'Pending'" ng-click="ResendVerificationMail(row.UserGUID)">Resend Verify</button></td> 
                            <td class="text-center">
                                <div class="dropdown action_toggle">
                                    <button class="btn btn-secondary  btn-sm action" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-if="data.UserGUID != row.UserGUID"><i class="fa fa-ellipsis-h"></i></button>
                                    <div class="dropdown-menu dropdown-menu-left">

                                        <a class="dropdown-item" href="" ng-click="loadFormAddCash(key, row.UserGUID)">Add Cash Bonus</a>
                                        <a class="dropdown-item" href="" ng-click="loadFormAddCashDeposit(key, row.UserGUID)">Add Cash</a>

                                        <a class="dropdown-item" target="_blank" href="transactions?UserGUID={{row.UserGUID}}" >Transactions</a>
                                        <a class="dropdown-item" target="_blank" href="joinedcontests?UserGUID={{row.UserGUID}}" >Joined Contests</a>
                                        <a class="dropdown-item" target="_blank" href="privatecontests?UserGUID={{row.UserGUID}}" >Private Contests</a>
                                        <a class="dropdown-item" href="javascript:void(0)" ng-click="loadFormChangePassword(key, row.UserGUID)">Change Password</a>
                                        <a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.UserGUID)">Edit</a>
                                        <a class="dropdown-item" href="" ng-click="loadFormDelete(key, row.UserGUID)">Delete</a>
                                    </div>
                                </div>
                            </td> -->
                        </tr>
                    </tbody>
                </table>
            </div>    
        </form>
        <!-- no record -->
        <p class="no-records text-center" ng-if="data.noRecords">
            <span ng-if="data.dataList.length">No more records found.</span>
            <span ng-if="!data.dataList.length">No records found.</span>
        </p>
    </div>
    <!-- Data table/ -->


    <div class="modal fade" id="filter_model"  >
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Filters</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <!-- Filter form -->
                <form id="filterForm1" role="form" autocomplete="off" class="ng-pristine ng-valid">
                    <div class="modal-body">
                        <div class="form-area">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="filter-col" for="Status">Status</label>
                                        <select id="Status" name="Status" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option value="Verified" ng-selected ="(st == 'verified') ? true : false">Verified</option>
                                            <option value="Pending" ng-selected ="(st == 'pending') ? true : false">Pending</option>
                                            <option value="Deleted">Deleted</option>
                                            <option value="Blocked">Blocked</option>
                                            <option value="Hidden">Hidden</option>
                                        </select>   
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="filter-col" for="isWithdrawal">Withdrawal Accept</label>
                                        <select name="isWithdrawal" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>   
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="filter-col">Wallet Type</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select name="WalletType" class="form-control">
                                                    <option value="">Please Select</option>
                                                    <option value="WinningAmount">Winning</option>
                                                    <option value="WalletAmount">Deposit</option>
                                                    <option value="CashBonus">Bonus</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 pt-0 pr-1 pl-1">
                                                <select name="Operator" class="form-control">
                                                    <option value="">Please Select</option>
                                                    <option value=">">> (greater)</option>
                                                    <option value="=">= (equal to)</option>
                                                    <option value="<">< (less)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="Amount" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="filter-col" for="IsPrivacyNameDisplay">Private Contest Name Display</label>
                                        <select name="IsPrivacyNameDisplay" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>   
                                    </div>
                                </div>
                            </div>

                        </div> <!-- form-area /-->
                    </div> <!-- modal-body /-->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm1').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button>
                        <button type="submit" class="btn btn-success btn-sm" data-dismiss="modal" ng-disabled="editDataLoading" ng-click="applyFilter()">Apply</button>
                    </div>

                </form>
                <!-- Filter form/ -->
            </div>
        </div>
    </div>


    <!-- edit Modal -->
    <div class="modal fade" id="edit_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Edit <?php echo $this->ModuleData['ModuleName']; ?></h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="edit_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeUserPassword_form" >
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
                                        <input type="password" name="Password" class="form-control" placeholder="New Password">
                                        <input type="hidden" name="UserGUID" class="form-control" value="{{ChangePasswordformData.UserGUID}}">
                                    </div>
                                </div>
                            </div>
                        </div> <!-- form-area /-->
                    </div> <!-- modal-body /-->

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm"  ng-disabled="changeCP" ng-click="changeUserPassword()">Submit</button>
                    </div>

                </form>
                <!-- Filter form/ -->
            </div>
        </div>
    </div>
    <!-- Verification Modal -->
    <div class="modal fade" id="Verification_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Verirification</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="Verification_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>
    <!-- Add cash bonus Modal -->
    <div class="modal fade" id="AddCashBonus_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Add Cash Bonus</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="addCash_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>
    <!-- Add cash bonus Modal -->
    <div class="modal fade" id="AddCashBonusDeposit_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Add Cash </h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="addCashDeposit_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>
    
     <div class="modal fade" id="verifyOtp_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Verify OTP </h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="verify_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>

    <!-- Add referral users list Modal -->
    <div class="modal fade" id="referralUserList_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Referral Users List</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="referralUserList_form" name="referralUserList_form" autocomplete="off" ng-include="templateURLEdit">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>


    <!-- delete Modal -->
    <div class="modal fade" id="delete_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Delete <?php echo $this->ModuleData['ModuleName']; ?></h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="edit_form" name="edit_form" autocomplete="off" ng-include="templateURLDelete">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>


</div><!-- Body/ -->



