<?php include('header.php') ?>

<div class="main-content" ng-controller="leaderboardController" ng-cloak ng-init="getLeagues();gotoTab('Week')">
    <div class="dashboard leaderboard">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-10">
                            <ul class="top-bar">
                                <div ng-if="activeTab == 'Overall'" class="custom_select">
                                    <select name="week" ng-model="SelectedWeekGUID" ng-required="true"
                                        ng-change="getUserBalance(SelectedWeekGUID)">
                                        <option ng-repeat="week in CompletedWeekList" value="{{week.WeekGUID}}">
                                            <p>Week {{week.WeekCount}}</p>
                                            <p>({{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}})
                                            </p>
                                        </option>
                                    </select>
                                </div>
                                <li>
                                    <span>Entry :
                                        {{UserEntriesBalance.EntryNo}}
                                    </span>
                                    <span>|</span>
                                    <span class="themeClr"> {{moneyFormat(profileDetails.WalletAmount)}} </span>
                                </li>
                                <li>
                                    <span><span
                                            class="themeClr">{{UserEntriesBalance.ConsumedPredictions ? UserEntriesBalance.ConsumedPredictions : '0'}}</span>
                                        /
                                        {{UserEntriesBalance.AllowedPredictions ? UserEntriesBalance.AllowedPredictions : '0'}}
                                        Prediction</span>
                                </li>
                                <li>
                                    <span><span class="themeClr">{{UserEntriesBalance.ConsumeDoubleUps ? UserEntriesBalance.ConsumeDoubleUps : '0'}}</span> / {{UserEntriesBalance.AllowedPurchaseDoubleUps ? UserEntriesBalance.AllowedPurchaseDoubleUps: '0'}} Double Up</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-2"><button class="w-100 btn bg-gradient" ng-click="goBack()">Go Back</button></div>
                    </div>
                </div>
                <!-- <div class="col-md-12">
						<div class="bg-gradient top-head">
							<div class="row w-100">
								<h6 class="col-sm-5">La liga</h6>
								<div class="col-sm-7 px-0 right-site">
									<a class="javascript:void(0)"><i class="fa fa-users pr-2"></i>Pools</a>
									<a class="javascript:void(0)"><i class="fa fa-user pr-2"></i>Performance</a>
									<a class="javascript:void(0)"><img src="assets/img/info.png" alt="" class="pr-2">Info</a>
								</div>
							</div>
						</div>
					</div> -->
                <!-- <div class="col-md-12">
						<div class="setting-bar">
							<div class="row align-items-center">
								<div class=" col-lg-7 col-xl-6 d-flex">
									
								</div>
								<div class="col-lg-4 col-xl-5 pl-lg-0 mt-2 mt-lg-0 col-10">
									<ul class="pools">
										<li>
											<span>Add a Pool</span>
											<a href="javascript:void(0)" class="addPool">+</a>
											<a href="javascript:void(0)" class="minusPool">-</a>
										</li>
									</ul>
								</div>
								<div class="col-lg-1 mt-2 mt-lg-0 text-right col-2">
									<a href="javascript:void(0)"><i class="fa fa-cog"></i></a>
								</div>
							</div>
						</div>
					</div> -->
                <!-- <div class="col-md-12 mt-4">
						<img src="assets/img/leaderboard-banner.png">
					</div> -->
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="nav-tabs-bg">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <!-- <li class="nav-item">
                                <a class="nav-link {{(activeTab == 'Overall')?'active':''}}"
                                    ng-click="gotoTab('Overall')" href="javascript:void(0)" role="tab"
                                    aria-controls="Leaderboard" aria-selected="true">Overall</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{(activeTab == 'Match')?'active':''}}" ng-click="gotoTab('Match')"
                                    href="javascript:void(0)" role="tab" aria-controls="Matches"
                                    aria-selected="false">Matches</a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link {{(activeTab == 'Week')?'active':''}}" ng-click="gotoTab('Week')"
                                    href="javascript:void(0)" role="tab" aria-controls="Weeks"
                                    aria-selected="true">Weeks</a>
                            </li>
                        </ul>
                        <!-- <span>
								<a href="javascript:void(0)" class=""><img src="assets/img/dot.png"></a>
							</span> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade {{(activeTab == 'Overall')?'show active':''}}" role="tabpanel"
                            aria-labelledby="Leaderboard-tab">
                            <!-- <a class="btn" data-toggle="collapse" href="#leaderboardTable" role="button" aria-expanded="false" aria-controls="leaderboardTable">
									<span>Round 32</span>
									<span>16-17 Jun</span>
									<span><i class="fa fa-caret-down"></i></span>
								</a> -->
                            <div class="col-md-4 col-sm-6 px-0 mt-2">
                                <div class="custom_select">
                                    <select class="form-control customReadOnlyField" ng-model="LeagueGUID"
                                        ng-change="changeLeague()">
                                        <option ng-repeat="league in LeagueList" value="{{league.LeagueGUID}}">
                                            {{league.LeagueName}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="collapse show" id="leaderboardTable">
                                <div class="table-responsive scrollbar">
                                    <table class="table leaderboard_table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <!-- <th></th> -->
                                                <th>Player</th>
                                                <th>Exact Score Points</th>
                                                <th>Correct Result Points</th>
                                                <th>Both Team Score Points</th>
                                                <th>Longest Odds Score Points</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody scrolly class="custom_scroll">
                                            <tr ng-repeat="player in PlayerListSeasonwise">
                                                <td>{{$index+1}}</td>
                                                <!-- <td class="upload"><i class="fa fa-upload"></i> 3</td> -->
                                                <!-- <td class="download"><i class="fa fa-download"></i> 1</td> -->
                                                <td>
                                                    <img ng-src="{{player.ProfilePic}}"
                                                        class="mr-2">{{player.FirstName}}
                                                </td>
                                                <td>{{player.ExactScorePoints}}</td>
                                                <td>{{player.CorrectResultPoints}}</td>
                                                <td>{{player.BothTeamScorePoints}}</td>
                                                <td>{{player.LongestOddsScorePoints}}</td>
                                                <td>{{player.TotalPoints}}</td>
                                            </tr>
                                            <tr ng-if="PlayerListSeasonwise.length == 0">
                                                <td colspan="7">No Players available.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{(activeTab == 'Match')?'show active':''}}" role="tabpanel"
                            aria-labelledby="Matches-tab">
                            <div class="col-md-4 col-sm-6 px-0 mt-2">
                                <div class="custom_select">
                                    <select class="form-control customReadOnlyField" ng-model="LeagueGUID"
                                        ng-change="changeLeague()">
                                        <option ng-repeat="league in LeagueList" value="{{league.LeagueGUID}}">
                                            {{league.LeagueName}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- <div ng-if="WeekStatus" class="round-slider" slick-week-custom-carousel>
                                        <div ng-repeat="week in WeekList" ng-click="changeWeek(week.WeekGUID)"
                                            ng-if="week.Status == 'Completed'">
                                            <div class="round {{(week.WeekGUID == WeekGUID)?'active':''}}">
                                                <p>Week {{$index+1}}</p>
                                                <p>{{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}}
                                                </p>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 px-0 mt-2">
                                <div class="custom_select">
                                    <select class="form-control customReadOnlyField" ng-model="MatchGUID"
                                        ng-change="changeMatch(MatchGUID)">
                                        <option ng-repeat="match in MatchesList" value="{{match.MatchGUID}}">
                                            {{match.TeamNameLocal}} Vs {{match.TeamNameVisitor}}
                                            ({{match.MatchStartDateTime | date:'EEE d MMMM HH:mm'}})</option>
                                    </select>
                                </div>
                            </div>
                            <div class="collapse show" id="leaderboardTable">
                                <div class="table-responsive scrollbar">
                                    <table class="table leaderboard_table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <!-- <th></th> -->
                                                <th>Player</th>
                                                <th>Exact Score Points</th>
                                                <th>Correct Result Points</th>
                                                <th>Both Team Score Points</th>
                                                <th>Longest Odds Score Points</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody scrolly class="custom_scroll">
                                            <tr ng-repeat="player in PlayerListMatchwise">
                                                <td>{{$index+1}}</td>
                                                <!-- <td class="upload"><i class="fa fa-upload"></i> 3</td> -->
                                                <!-- <td class="download"><i class="fa fa-download"></i> 1</td> -->
                                                <td>
                                                    <img ng-src="{{player.ProfilePic}}"
                                                        class="mr-2">{{player.FirstName}}
                                                </td>
                                                <td>{{player.ExactScorePoints}}</td>
                                                <td>{{player.CorrectResultPoints}}</td>
                                                <td>{{player.BothTeamScorePoints}}</td>
                                                <td>{{player.LongestOddsScorePoints}}</td>
                                                <td>{{player.TotalPoints}}</td>
                                            </tr>
                                            <tr ng-if="PlayerListMatchwise.length == 0">
                                                <td colspan="7">No Players available.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{(activeTab == 'Week')?'show active':''}}" role="tabpanel"
                            aria-labelledby="Weeks-tab">
                            <!-- <div class="col-md-4 col-sm-6 px-0 mt-2">
                                <div class="custom_select">
                                    <select class="form-control customReadOnlyField" ng-model="LeagueGUID"
                                        ng-change="changeLeague()">
                                        <option ng-repeat="league in LeagueList" value="{{league.LeagueGUID}}">
                                            {{league.LeagueName}}</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div ng-if="WeekStatus" class="round-slider" slick-week-custom-carousel>
                                        <div ng-repeat="week in WeekList" ng-click="changeWeek(week.WeekGUID)"
                                            ng-if="week.Status == 'Completed' || week.Status == 'Running'">
                                            <div class="round {{(week.WeekGUID == WeekGUID)?'active':''}}">
                                                <p>Week {{$index+1}}</p>
                                                <p>{{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse show" id="leaderboardTable">
                                <div class="table-responsive scrollbar">
                                    <table class="table leaderboard_table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Entry No.</th>
                                                <th>Player</th>
                                                <th>Exact Score Points</th>
                                                <th>Correct Result Points</th>
                                                <th>Both Team Score Points</th>
                                                <th>Longest Odds Score Points</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody scrolly class="custom_scroll">
                                            <tr ng-repeat="player in PlayerListWeekwise">
                                                <td>{{$index+1}}</td>
                                                <td>{{player.EntryNo}}</td>
                                                <!-- <td class="upload"><i class="fa fa-upload"></i> 3</td> -->
                                                <!-- <td class="download"><i class="fa fa-download"></i> 1</td> -->
                                                <td>
                                                    <img ng-src="{{player.ProfilePic}}"
                                                        class="mr-2">{{player.FirstName}}
                                                </td>
                                                <td>{{player.ExactScorePoints}}</td>
                                                <td>{{player.CorrectResultPoints}}</td>
                                                <td>{{player.BothTeamScorePoints}}</td>
                                                <td>{{player.LongestOddsScorePoints}}</td>
                                                <td>{{player.TotalPoints}}</td>
                                            </tr>
                                            <tr ng-if="PlayerListWeekwise.length == 0">
                                                <td colspan="7">No Players available.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footerHome.php') ?>