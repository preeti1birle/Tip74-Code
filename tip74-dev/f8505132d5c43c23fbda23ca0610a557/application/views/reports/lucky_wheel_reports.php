<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="PageController" ng-init="getListWheel()"><!-- Body -->
    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model">Filter <img src="asset/img/filter.svg"></button>&nbsp;
        </div>
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
        </div>
        <!-- <b> Filters </b><br> -->
        <!-- <span> User Type : </span> <span id="User_Type"> </span><br> -->
        <!-- <span> Date FIlter : <span id="Data_Filter"> </span><br>
        <span> Start Date : <span id="Start_Date"> </span><br>
        <span> End Date : <span id="End_Date"> </span><br> -->
        
        <div class="row w-100 pt-4 m-0">
            <div class="col-lg-4  mb-4">
                <div class="panel_grp block">
                    <div class="panel_title">Total Users </div>
                    <div class="d-flex justify-content-between py-3 px-4">
                        <ul class="list_style">
                            <li>On Date :</li>
                            <li>Distributed Amount: </li>
                            <li>Total Users: </li>

                            <li>Remaining: </li>
                            <li>TotalDistributedAmount: </li>
                        </ul>
                        <ul class="list_style">
                            <li>{{list.OnDate}}</li>
                            <li ng-if="!list.DistributedAmount"> - </li>
                            <li ng-if="list.DistributedAmount">{{list.DistributedAmount}}</li>
                            <li>{{list.TotalUser}}</li>

                            <li>{{list.Remaining}}</li>
                            <li>{{list.TotalDistributedAmount}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Data table -->
    <div class="table-responsive block_pad_md" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
        <!-- loading -->
        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

        <!-- data table -->
    </div>
    <!-- Data table/ -->

    <!-- Filter Modal -->
    <div class="modal fade" id="filter_model"  ng-init="getListWheel()">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Get Reports</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <!-- Filter form -->
                <form id="filterForm1" role="form" autocomplete="off" class="ng-pristine ng-valid">
                    <div class="modal-body">
                        <div class="form-area">
                            <div class="row">
                                <div class="col-md-8" id="DataFilter">
                                    <div class="form-group">
                                        <label class="filter-col">Date</label>
                                        <input type="date" id="StartDate" name="Date" class="form-control txtDate"> 
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="filter-col" for="CategoryTypeName">Date Filter</label>
                                        <select id="DataFilter" name="DataFilter" ng-model="DataFilter" class="form-control">
                                            <option value="">Please Select</option>
                                            <option value="Today">Today</option>
                                            <option value="Last7Days">Last 7 days</option>
                                            <option value="Last15Days">Last 15 Days</option>
                                            <option value="Last30Days">Last 30 Days</option>
                                            <option value="Last3Months">3 Months</option>
                                            <option value="DateRange">Date Range</option>
                                        </select>   
                                    </div>
                                </div>
                            </div>
                            <div class="row" ng-if="DataFilter == 'DateRange'">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="filter-col">Start Date</label>
                                        <input id="StartDate" type="date" name="FromDate" class="form-control txtDate"> 
                                    </div>
                                </div> 
                            </div>
                            <div class="row" ng-if="DataFilter == 'DateRange'">
                                <div class="col-md-8">
                                    <div class="form-group"> 
                                        <label class="filter-col">End Date</label>
                                        <input id="EndDate" type="date" name="ToDate" class="form-control txtDate"> 
                                    </div>
                                </div> </div>
                        </div> --> <!-- form-area --> 
                    </div> <!-- modal-body --> 

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm1').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button>
                        <button type="submit" class="btn btn-success btn-sm" data-dismiss="modal" onclick="check()" ng-disabled="editDataLoading" ng-click="getListWheel()">Apply</button>
                    </div>

                </form>
                <!-- Filter form -->
            </div>
        </div>
    </div>

    <!-- Data table -->
    <!-- <div class="table-responsive block_pad_md" >  -->

        <!-- loading -->
        <!-- <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>
        <form name="records_form" id="records_form"> -->
            <!-- data table -->
            <!-- <table class="table table-striped table-hover" ng-if="data.dataList.length"> -->
                <!-- table heading -->
                <!-- <thead>
                    <tr> -->
                            <!-- <th style="width: 50px;" class="text-center" ng-if="data.dataList.length>1"><input type="checkbox" name="select-all" id="select-all" class="mt-1" ></th> -->	
                        <!-- <th>User</th> -->
                        <!-- <th>Contact No.</th> -->
                        <!-- <th>Gender</th>
                        <th>Date of Birth</th> -->
                        <!-- <th>Points</th> -->
                        <!-- <th>Entry Date</th> -->
                        <!-- <th style="width: 200px;">Role</th> -->
                        <!-- <th class="sort" ng-click="applyOrderedList('E.EntryDate', 'ASC')">Registered On <span class="sort_deactive">&nbsp;</span></th>
                        <th class="text-center">Last Login</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th> -->

                    <!-- </tr>
                </thead> -->
                <!-- table body -->
                <!-- <tbody> -->
                    <!-- <tr scope="row" ng-repeat="(key, row) in data.dataList"> -->

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
                        <!-- <td>{{row.FirstName}}</td>
                        <td>{{row.Value}}</td>  
                        <td>{{row.EntryDate}}</td>   -->

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
                    <!-- </tr>
                </tbody>
            </table>
        </form> -->
        <!-- no record -->
        <!-- <p class="no-records text-center" ng-if="data.noRecords">
            <span ng-if="data.dataList.length">No more records found.</span>
            <span ng-if="!data.dataList.length">No records found.</span>
        </p> -->
    <!-- </div> -->
    <!-- Data table/ -->

</div><!-- Body/ -->
<!-- <script>
function check(){
    var DataFilter = $("#DataFilter option:selected");
    $('#Data_Filter').text((DataFilter.text() == "Please Select") ? '' : DataFilter.text());
    var UserType = $("#UserType option:selected");
    $('#User_Type').text((UserType.text() == "Please Select") ? '' : UserType.text());
    $('#Start_Date').text($('#StartDate').val());
    $('#End_Date').text($('#EndDate').val());
}

$(document).ready(function(){
    $("#DataFilter").change(function(){
        console.log($("#DataFilter").val());
        if($("#DataFilter").val()== "DateRange"){
            var dtToday = new Date();

            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();

            if(month < 10)
                month = '0' + month.toString();
            if(day < 10)
                day = '0' + day.toString();

            var maxDate = year + '-' + month + '-' + day;    
            $('.txtDate').attr('max', maxDate);
        }    
    });
    
});
</script> -->