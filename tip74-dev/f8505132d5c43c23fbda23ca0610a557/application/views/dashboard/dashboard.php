<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="PageController" ng-init="getList()"><!-- Body -->
    <div class="">
        <div class="wrapper wrapper-content">
            <div class="row mb-3 align-items-stretch">
                <?php if ($this->session->userdata('UserData')['UserTypeID'] == 1) { ?>
                    <div class="col-xl-3 col-sm-6 py-2">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-user font_icon"></i>
                                </div>
                                <div class="card-info col-9" ng-click="LoadUserList('')">
                                    <h6> Verified Users </h6>
                                    <h4>{{data.dataList.TotalUsers}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-user font_icon"></i>
                                </div>
                                <div class="card-info col-9" ng-click="LoadUnverifiedUserList('')">
                                    <h6> Unverified Users </h6>
                                    <h4>{{data.dataList.TotalUnverifiedUsers}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2" ng-click="LoadDepositsList('All')">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-dollar font_icon"></i>
                                </div>
                                <div class="card-info col-9">
                                    <h6>Total Deposits</h6>
                                    <h4>{{data.dataList.TotalDeposits| number : 2 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2" ng-click="withdrawalsList()">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-reply-all font_icon"></i>
                                </div>
                                <div class="card-info col-9">
                                    <h6>Total withdrawls</h6>
                                    <h4>{{data.dataList.TotalWithdraw| number : 2 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2">
                        <div class="card">
                            <div class="card-body custom-card-body" ng-click="withdrawalsList('Pending')">
                                <div class="rotate col-3">
                                    <i class="fa fa-share font_icon"></i>
                                </div>
                                <div class="card-info col-9">
                                    <h6>Pending Withdrawls</h6>
                                    <h4>{{data.dataList.PendingWithdraw| number : 2 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2" ng-click="LoadUserList('Today')">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-user font_icon"></i>
                                </div>
                                <div class="card-info col-9">
                                    <h6> New Users </h6>
                                    <h4>{{data.dataList.NewUsers}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2" ng-click="LoadDepositsList('Today')">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-dollar font_icon"></i>
                                </div>
                                <div class="card-info col-9">
                                    <h6> Today Deposits </h6>
                                    <h4>{{data.dataList.TodayDeposit| number : 2 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <!-- <div class="col-xl-3 col-sm-6 py-2">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-user font_icon"></i>
                                </div>
                                <div class="card-info col-9">
                                    <h6> Total Users </h6>
                                    <h4>{{data.dataList.TotalUsers}}</h4>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-xl-3 col-sm-6 py-2">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-user font_icon"></i>
                                </div>
                                <div class="card-info col-9" ng-click="LoadUserList('')">
                                    <h6> Verified Users </h6>
                                    <h4>{{data.dataList.TotalUsers}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-user font_icon"></i>
                                </div>
                                <div class="card-info col-9" ng-click="LoadUnverifiedUserList('')">
                                    <h6> Unverified Users </h6>
                                    <h4>{{data.dataList.TotalUnverifiedUsers}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 py-2">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="rotate col-3">
                                    <i class="fa fa-user font_icon"></i>
                                </div>
                                <div class="card-info col-9">
                                    <h6> New Users </h6>
                                    <h4>{{data.dataList.NewUsers}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="col-xl-3 col-sm-6 py-2">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <div class="rotate col-3">
                                <i class="fa fa-globe font_icon"></i>
                            </div>
                            <div class="card-info col-9">
                                <h6>Total Web Users</h6>
                                <h4>{{data.dataList.TotalWebUsers}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 py-2">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <div class="rotate col-3">
                                <i class="fa fa-mobile font_icon"></i>
                            </div>
                            <div class="card-info col-9">
                                <h6>Total Andorid Users</h6>
                                <h4>{{data.dataList.TotalAndoridUsers}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 py-2">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <div class="rotate col-3">
                                <i class="fa fa-apple font_icon"></i>
                            </div>
                            <div class="card-info col-9">
                                <h6>Total iOS Users</h6>
                                <h4>{{data.dataList.TotalIosUsers}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- 
    <hr/> -->

    <nav class="matches_tab">
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <!-- <a class="nav-item nav-link active" id="nav-cricket-tab" data-toggle="tab" href="#cricket" role="tab" aria-controls="cricket" aria-selected="true"> Cricket </a> -->
            <a class="nav-item nav-link active" id="nav-football-tab" data-toggle="tab" href="#football" role="tab" aria-controls="nav-profile" aria-selected="true"> Football </a>
        </div>
    </nav>

    <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
        <!-- <div class="tab-pane fade show active" id="cricket" role="tabpanel" aria-labelledby="nav-cricket-tab">
            <div class="">
                <div class="table-responsive block_pad_md">
                    <h3 class="heading_h3"> Running Matches </h3>
                    <table class="table table-striped table-condensed table-hover table-sortable mt-3 all-table-scroll dashboard_table" ng-if="matches.Records.length">
                        <thead>
                            <th>Series Name</th>
                            <th></th>
                            <th>Team Local</th>
                            <th></th>
                            <th>Team Visitor</th>
                            <th>Match Type</th>
                            <th>Match Started At</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            <tr ng-repeat="row in matches.Records">
                                <td class="user_table">
                                    <a target="_blank" href="contests?MatchGUID={{row.MatchGUID}}"><strong>{{row.SeriesName}}</strong></a>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagLocal}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameLocal}} <br><small>( {{row.TeamNameShortLocal}} )</small></p>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagVisitor}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameVisitor}} <br><small>( {{row.TeamNameShortVisitor}} )</small></p>
                                </td>
                                <td>
                                    <p class="user_table">{{row.MatchType}} at {{row.MatchLocation}} </p>
                                </td>
                                
                                <td>
                                    <p>{{row.MatchStartDateTime}}</p>
                                </td>
                                
                                <td class="text-center"><span ng-class="{Pending:'text-secondary', Completed:'text-success',Cancelled:'text-danger',Running:'text-primary'}[row.Status]">{{row.Status}}</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="no-records text-center" ng-if="!matches.Records.length">
                        <span ng-if="!matches.Records.length">No records found.</span>
                    </p>
                </div>
            </div>

            <div class="">
                <div class="table-responsive block_pad_md">
                    <h3 class="heading_h3"> Upcoming Matches </h3>
                    <table class="table table-striped table-condensed table-hover table-sortable mt-3 all-table-scroll dashboard_table" ng-if="matchesUpcoming.Records.length">
                        <thead>
                            <th>Series Name</th>
                            <th></th>
                            <th>Team Local</th>
                            <th></th>
                            <th>Team Visitor</th>
                            <th>Match Type</th>
                            <th>Match Started At</th>
                            <th>Status</th>
                            <th>Action</th>

                        </thead>
                        <tbody>
                            <tr ng-repeat="row in matchesUpcoming.Records">
                                    
                                <td class="user_table">
                                    <a target="_blank" href="contests?MatchGUID={{row.MatchGUID}}"><strong>{{row.SeriesName}}</strong></a>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagLocal}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameLocal}} <br><small>( {{row.TeamNameShortLocal}} )</small></p>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagVisitor}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameVisitor}} <br><small>( {{row.TeamNameShortVisitor}} )</small></p>
                                </td>
                                <td>
                                    <p class="user_table">{{row.MatchType}} at {{row.MatchLocation}} </p>
                                </td>
                                
                                <td>
                                    <p>{{row.MatchStartDateTime}}</p>
                                </td>
                                
                                <td class="text-center"><span ng-class="{Pending:'text-secondary', Completed:'text-success',Cancelled:'text-danger',Running:'text-primary'}[row.Status]">{{row.Status}}</span></td>
                                <td class="text-center">
                                    <div class="dropdown action_toggle">
                                        <button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
                                        <div class="dropdown-menu dropdown-menu-left">
                                            <a class="dropdown-item" target="_blank" href="players?MatchGUID={{row.MatchGUID}}" >Players</a>
                                            <a class="dropdown-item" href="contests?MatchGUID={{row.MatchGUID}}" target="_blank">Contests</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="no-records text-center" ng-if="!matchesUpcoming.Records.length">
                        <span ng-if="!matchesUpcoming.Records.length">No records found.</span>
                    </p>
                </div>
            </div>
        </div> -->
        <div class="tab-pane fade show active" id="football" role="tabpanel" aria-labelledby="nav-football-tab">
            <div class="">
                <div class="table-responsive block_pad_md">
                    <h3 class="heading_h3"> Running Matches </h3>
                    <table class="table table-striped table-condensed table-hover table-sortable mt-3 all-table-scroll dashboard_table" ng-if="matchesRuningFootball.Records.length">
                        <thead>
                            <th>Series Name</th>
                            <th></th>
                            <th>Team Local</th>
                            <th></th>
                            <th>Team Visitor</th>
                            <th>Match Type</th>
                            <th>Match Started At</th>
                            <th>Status</th>
                            <!-- <th>Action</th> -->

                        </thead>
                        <tbody>
                            <tr ng-repeat="row in matchesRuningFootball.Records">
                                    
                                <td class="user_table">
                                    <a target="_blank" href="contests?MatchGUID={{row.MatchGUID}}"><strong>{{row.SeriesName}}</strong></a>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagLocal}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameLocal}} <br><small>( {{row.TeamNameShortLocal}} )</small></p>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagVisitor}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameVisitor}} <br><small>( {{row.TeamNameShortVisitor}} )</small></p>
                                </td>
                                <td>
                                    <p class="user_table">{{row.MatchType}} at {{row.MatchLocation}} </p>
                                </td>
                                
                                <td>
                                    <p>{{row.MatchStartDateTime}}</p>
                                </td>
                                
                                <td class="text-center"><span ng-class="{Pending:'text-secondary', Completed:'text-success',Cancelled:'text-danger',Running:'text-primary'}[row.Status]">{{row.Status}}</span></td>
                                <!-- <td class="text-center">
                                    <div class="dropdown action_toggle">
                                        <button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
                                        <div class="dropdown-menu dropdown-menu-left">
                                            <a class="dropdown-item" href="javascript:void(0)" ng-click="loadFormEdit(key, row.MatchGUID)">Edit</a>
                                            <a class="dropdown-item" target="_blank" href="players?MatchGUID={{row.MatchGUID}}" >Players</a>
                                            <a class="dropdown-item" href="contests?MatchGUID={{row.MatchGUID}}" target="_blank">Contests</a>
                                        </div>
                                    </div>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                    <p class="no-records text-center" ng-if="!matchesRuningFootball.Records.length">
                        <span ng-if="!matchesRuningFootball.Records.length">No records found.</span>
                    </p>
                </div>
            </div>
            <div class="">
                <div class="table-responsive block_pad_md">
                    <h3 class="heading_h3"> Upcoming Matches </h3>
                    <table class="table table-striped table-condensed table-hover table-sortable mt-3 all-table-scroll dashboard_table" ng-if="matchesUpcomingFootball.Records.length">
                        <thead>
                            <th>Series Name</th>
                            <th></th>
                            <th>Team Local</th>
                            <th></th>
                            <th>Team Visitor</th>
                            <th>Match Type</th>
                            <th>Match Started At</th>
                            <th>Status</th>
                             <th>Action</th> 

                        </thead>
                        <tbody>
                            <tr ng-repeat="row in matchesUpcomingFootball.Records">
                                    
                                <td class="user_table">
                                    <a target="_blank" href="contests?MatchGUID={{row.MatchGUID}}"><strong>{{row.SeriesName}}</strong></a>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagLocal}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameLocal}} <br><small>( {{row.TeamNameShortLocal}} )</small></p>
                                </td>
                                <td class="text-center">
                                    <img class="float-left" ng-src="{{row.TeamFlagVisitor}}" width="70px" height="45px;">
                                </td>
                                <td>
                                    <p>{{row.TeamNameVisitor}} <br><small>( {{row.TeamNameShortVisitor}} )</small></p>
                                </td>
                                <td>
                                    <p class="user_table">{{row.MatchType}} at {{row.MatchLocation}} </p>
                                </td>
                                
                                <td>
                                    <p>{{row.MatchStartDateTime}}</p>
                                </td>
                                
                                <td class="text-center"><span ng-class="{Pending:'text-secondary', Completed:'text-success',Cancelled:'text-danger',Running:'text-primary'}[row.Status]">{{row.Status}}</span></td>
                                 <td class="text-center">
                                    <div class="dropdown action_toggle">
                                        <button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
                                        <div class="dropdown-menu dropdown-menu-left">
<!--                                            <a class="dropdown-item" href="javascript:void(0)" ng-click="loadFormEdit(key, row.MatchGUID)">Edit</a>-->
                                            <a class="dropdown-item" target="_blank" href="football/players?MatchGUID={{row.MatchGUID}}" >Players Management</a>
                                            <a class="dropdown-item" href="football/contests?MatchGUID={{row.MatchGUID}}" target="_blank">Contests</a>
                                        </div>
                                    </div>
                                </td> 
                            </tr>
                        </tbody>
                    </table>
                    <p class="no-records text-center" ng-if="!matchesUpcomingFootball.Records.length">
                        <span ng-if="!matchesUpcomingFootball.Records.length">No records found.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>





</div><!-- Body/ -->

