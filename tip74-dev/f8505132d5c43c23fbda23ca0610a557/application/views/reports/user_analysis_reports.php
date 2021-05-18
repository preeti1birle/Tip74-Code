<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="PageController"><!-- Body -->
    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model">Filter <img src="asset/img/filter.svg"></button>&nbsp;
        </div>
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
        </div>
        <b> Filters </b><br>
        <span> User Type : </span> <span id="User_Type"> </span><br>
        <span> Date FIlter : <span id="Data_Filter"> </span><br>
        <span> Start Date : <span id="Start_Date"> </span><br>
        <span> End Date : <span id="End_Date"> </span><br>
        <div class="row w-100 pt-4 m-0">
            <div class="col-lg-12  mb-4">
                <div class="panel_grp block" ng-if="UserResult.UserType">
                    <div class="panel_title"> User Type: <strong>{{UserResult.UserType}}</strong>({{UserResult.FromTo}})</div>
                    <div class="d-flex justify-content-between py-3 px-4">
                        <table class="table table-striped table-condensed table-hover table-sortable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Team Name</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Total {{(UserResult.UserType=='Top 50 Winners') ? 'Winning': "Losses"}}</th>
                                </tr>
                            </thead>
                            <tbody id="tabledivbody">
                                <tr ng-repeat="User in UserResult.UsersList">
                                    <td>#{{$index + 1}}</td>
                                    <td>{{User.Username}}</td>
                                    <td>{{User.FirstName}}</td>
                                    <td>{{User.Email}}</td>
                                    <td>{{User.PhoneNumber}}</td>
                                    <td>Rs. {{(UserResult.UserType=='Top 50 Winners') ? User.TotalWinning: User.LosingAmount}}</td>
                            </tbody>
                            </tr>
                        </table>
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
    <div class="modal fade" id="filter_model">
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
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="filter-col" for="CategoryTypeName">User Type</label>
                                        <select id="UserType" name="UserType" ng-model="UserType" class="form-control">
                                            <option value="">Please Select</option>
                                            <option value="TopWinners">Top Winners</option>
                                            <option value="TopLosers">Top Losers</option>
                                        </select>   
                                    </div>
                                </div>
                            </div>
                            <div class="row">
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

                        </div> <!-- form-area /-->
                    </div> <!-- modal-body /-->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm1').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button>
                        <button type="submit" class="btn btn-success btn-sm" data-dismiss="modal" onclick="check()" ng-disabled="editDataLoading" ng-click="getUserReport()">Apply</button>
                    </div>

                </form>
                <!-- Filter form/ -->
            </div>
        </div>
    </div>

</div><!-- Body/ -->

<script>
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
</script>