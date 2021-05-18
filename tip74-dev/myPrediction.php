<?php include('header.php') ?>
	
	<div class="main-content" ng-controller="myPredictionController" ng-cloak ng-init="getLeagues();getAssignedEntries()">
		<div class="dashboard">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-9">
								<ul class="top-bar">
									<li>
										<span>Entry : {{UserEntriesBalance.EntryNo}}</span>
										<span>|</span>
										<span class="themeClr"> {{moneyFormat(profileDetails.WalletAmount)}}</span>
									</li>
									<li>
										<span><span class="themeClr">{{UserEntriesBalance.ConsumedPredictions ? UserEntriesBalance.ConsumedPredictions:'0'}}</span> / {{UserEntriesBalance.AllowedPredictions ? UserEntriesBalance.AllowedPredictions:'0'}} Prediction</span>
									</li>
									<li>
										<span><span class="themeClr">{{UserEntriesBalance.ConsumeDoubleUps ? UserEntriesBalance.ConsumeDoubleUps : '0'}}</span> / {{UserEntriesBalance.AllowedPurchaseDoubleUps ? UserEntriesBalance.AllowedPurchaseDoubleUps : '0'}} Double Up </span>
									</li>
								</ul>
							</div>
							<div class="col-md-3">
								<!-- <a href="javascript:void(0)" ng-click="getEntryList();getUserBalance(SelectedWeekGUID);openPopup('entryPopup')" class="btn btn_primary"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Purchase Entry </a> -->
								<button class="btn bg-gradient " ng-click="goBack()">Go Back</button>
							</div>
						</div>
					</div>
				</div>
				<div class="row align-items-center">
					<!-- <div class="col-md-4 col-sm-6 px-0 mt-2">
						<div class="custom_select ml-">
							<select class="form-control customReadOnlyField" ng-model="LeagueGUID" ng-change="changeLeague()" >
								<option ng-repeat="league in LeagueList" value="{{league.LeagueGUID}}" >{{league.LeagueName}}</option>
							</select>
						</div>
					</div> -->
					
				</div>
				<div class="row">
					<div class="col-md-12">
						<div ng-if="WeekStatus" class="round-slider" slick-week-custom-carousel>
							<div ng-repeat="week in WeekList" ng-click="changeWeek(week.WeekGUID)" >
								<div class="round {{week.Status == 'Completed'?'slide_disabled':''}} {{(week.WeekGUID == WeekGUID )?'active':''}}">
									<p>Week {{$index+1}}</p>
									<p>{{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-sm-6 px-0 mt-2 ml-3 custom_select_n">
						<div class="custom_select">
						     <select class="form-control chosen-select" ng-model="EntryNo" ng-change="changeEntries(EntryNo)" style="width:100%">
								<option value="" disabled selected>Please Select Entry No</option>
								<option ng-repeat="entries in EntriesList" value="{{entries.EntryNo}}" >{{entries.EntryNo}}</option>
							 </select>
						</div>
					</div>
				</div>
                <div class="row mt-2">
                    <div class="col-sm-12 order-2 order-sm-1">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{(activeTab == 'Pending')?'active':''}}" ng-click="gotoTab('Pending')" id="Pending-tab" href="javascript:void(0)" role="tab" aria-controls="Pending" aria-selected="true">Upcoming</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{(activeTab == 'Running')?'active':''}}" ng-click="gotoTab('Running')" id="Running-tab" href="javascript:void(0)" role="tab" aria-controls="Running" aria-selected="false">Live</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{(activeTab == 'Completed')?'active':''}}" ng-click="gotoTab('Completed')" id="Completed-tab" href="javascript:void(0)" role="tab" aria-controls="Completed" aria-selected="false">Completed</a>
                            </li>
                        </ul>
                    </div>
                </div>
				<div class="row mt-2">
                    <div class="col-md-12">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade liveSec {{(activeTab == 'Pending')?'show active':''}}" id="Pending" role="tabpanel" aria-labelledby="Pending-tab">
                                <div class="match-details">
									<div class="date-wise" ng-repeat="match in MatchesList" scrolly>
										<ul>
											<li>
												<div class="teamAndTime">
													<img ng-src="{{match.LeagueFlag}}" class="mx-4">
													<span>{{match.LeagueName}}</span>
													<span>{{match.MatchStartDateTime | date:'dd MMM'}}</span>
												</div>
												<div class="bgClr">
													<div class="teams">
														<div><img ng-src="{{match.TeamFlagLocal}}">{{match.TeamNameLocal}}</div>
														<div class="inputBox">
															<form><input type="text" id="localname_{{match.MatchGUID}}" name="localname_{{match.MatchGUID}}" value="{{match.MatchScoreDetails.LocalTeamScore}}" ng-readOnly="true"></form>
															<span>-</span>
															<form><input type="text" id="visitorname_{{match.MatchGUID}}" name="visitorname_{{match.MatchGUID}}" value="{{match.MatchScoreDetails.VisitorTeamScore}}" ng-readOnly="true"></form>
														</div>
														<div class="text-right">{{match.TeamNameVisitor}}<img ng-src="{{match.TeamFlagVisitor}}"></div>
													</div>
													<div class="info text-center">
													<i data-toggle="tooltip" data-placement="top" title="Locked Prediction" class="fa fa-lock text-danger pr-1" style="font-size: large;" aria-hidden="true"></i> 
														<span ng-if="match.hasOwnProperty('TeamScoreLocalFT')" class="pr-1"> Full Time ({{match.TeamScoreLocalFT}} - {{match.TeamScoreVisitorFT}})</span>
														<span ng-if="match.hasOwnProperty('TeamScoreLocalHT')" class="pr-1"> Half Time ({{match.TeamScoreLocalHT}} - {{match.TeamScoreVisitorHT}})</span>
														<!-- <span class="">|</span> -->
														<!-- <p class="results col-4 col-sm-6">1.5 Results</p> -->
														<!-- <p class="wrong col-4 col-sm-6">0 Wrong</p> -->
													</div>
													<div class="enter">
														<!-- <a href="leaderboard?LeagueGUID={{SelectedLeagueInfo.LeagueGUID}}&MatchGUID={{match.MatchGUID}}&WeekGUID={{WeekGUID}}"><img src="assets/img/arrow-right-whitebg.png"></a> -->
													</div>
												</div>
											</li>
										</ul>
									</div>
									<div class="text-white text-center mt-2" ng-if="MatchesList.length == 0">
										<p>No Upcoming Match Available.</p>
									</div>
								</div>
							</div>

							<div class="tab-pane fade liveSec {{(activeTab == 'Running')?'show active':''}}" id="Running" role="tabpanel" aria-labelledby="Running-tab">
                            <div class="match-details">
									<div class="date-wise" ng-repeat="match in MatchesList" scrolly>
										<ul>
											<li >
												<div class="teamAndTime">
													<img ng-src="{{match.LeagueFlag}}" class="mx-4">
													<span>{{match.LeagueName}}</span>
													<span>{{match.MatchStartDateTime | date:'dd MMM'}}</span>
												</div>
												<div class="bgClr">
													<div class="teams">
														<div><img ng-src="{{match.TeamFlagLocal}}">{{match.TeamNameLocal}}</div>
														<div class="inputBox">
															<form><input type="text" id="localname_{{match.MatchGUID}}" name="localname_{{match.MatchGUID}}" value="{{match.MatchScoreDetails.LocalTeamScore}}" ng-readOnly="true"></form>
															<span>-</span>
															<form><input type="text" id="visitorname_{{match.MatchGUID}}" name="visitorname_{{match.MatchGUID}}" value="{{match.MatchScoreDetails.VisitorTeamScore}}" ng-readOnly="true"></form>
														</div>
														<div class="text-right">{{match.TeamNameVisitor}}<img ng-src="{{match.TeamFlagVisitor}}"></div>
													</div>
													<div class="info text-center">
														<i data-toggle="tooltip" data-placement="top" title="Locked Prediction" class="fa fa-lock text-danger pr-1" style="font-size: large;" aria-hidden="true"></i>
														<span ng-if="match.hasOwnProperty('TeamScoreLocalFT')" class="pr-1"> Full Time ({{match.TeamScoreLocalFT}} - {{match.TeamScoreVisitorFT}})</span>
														<span ng-if="match.hasOwnProperty('TeamScoreLocalHT')" class="pr-1"> Half Time ({{match.TeamScoreLocalHT}} - {{match.TeamScoreVisitorHT}})</span>
														<!-- <span class="">|</span> -->
														<!-- <p class="results col-4 col-sm-6">1.5 Results</p> -->
														<!-- <p class="wrong col-4 col-sm-6">0 Wrong</p> -->
													</div>
													<div class="enter">
														<!-- <a href="leaderboard?LeagueGUID={{SelectedLeagueInfo.LeagueGUID}}&MatchGUID={{match.MatchGUID}}&WeekGUID={{WeekGUID}}"><img src="assets/img/arrow-right-whitebg.png"></a> -->
													</div>
												</div>
											</li>
										</ul>
									</div>
									<div class="text-white text-center mt-2" ng-if="MatchesList.length == 0">
										<p>No Live Match Available.</p>
									</div>
								</div>
							</div>

                            <div class="tab-pane fade liveSec {{(activeTab == 'Completed')?'show active':''}}" id="Completed" role="tabpanel" aria-labelledby="Completed-tab">
								<div class="match-details">
									<div class="date-wise" ng-repeat="match in MatchesList" scrolly>
										<ul>
											<li>
												<div class="teamAndTime">
													<img ng-src="{{match.LeagueFlag}}" class="mx-4">
													<span>{{match.LeagueName}}</span>
													<span>{{match.MatchStartDateTime | date:'dd MMM'}}</span>
												</div>
												<div class="bgClr">
													<div class="teams">
														<div><img ng-src="{{match.TeamFlagLocal}}">{{match.TeamNameLocal}}</div>
														<div class="inputBox">
															<form><input type="text" id="localname_{{match.MatchGUID}}" name="localname_{{match.MatchGUID}}" value="{{match.MatchScoreDetails.LocalTeamScore}}" ng-readOnly="true"></form>
															<span>-</span>
															<form><input type="text" id="visitorname_{{match.MatchGUID}}" name="visitorname_{{match.MatchGUID}}" value="{{match.MatchScoreDetails.VisitorTeamScore}}" ng-readOnly="true"></form>
														</div>
														<div class="text-right">{{match.TeamNameVisitor}}<img ng-src="{{match.TeamFlagVisitor}}"></div>
													</div>
													<div class="info text-center">
													<i data-toggle="tooltip" data-placement="top" title="Locked Prediction" class="fa fa-lock text-danger pr-1" style="font-size: large;" aria-hidden="true"></i>
														<span ng-if="match.hasOwnProperty('TeamScoreLocalFT')" class="pr-1"> Full Time ({{match.TeamScoreLocalFT}} - {{match.TeamScoreVisitorFT}})</span>
														<span ng-if="match.hasOwnProperty('TeamScoreLocalHT')" class="pr-1"> Half Time ({{match.TeamScoreLocalHT}} - {{match.TeamScoreVisitorHT}})</span>
														<!-- <span class="">|</span> -->
														<!-- <p class="results col-4 col-sm-6">1.5 Results</p> -->
														<!-- <p class="wrong col-4 col-sm-6">0 Wrong</p> -->
													</div>
													<div class="enter">
														<a href="leaderboard?LeagueGUID={{SelectedLeagueInfo.LeagueGUID}}&MatchGUID={{match.MatchGUID}}&WeekGUID={{WeekGUID}}&activeTab={{activeTab}}"><img src="assets/img/arrow-right-whitebg.png"></a>
													</div>
												</div>
											</li>
										</ul>
									</div>
									<div class="text-white text-center mt-2" ng-if="MatchesList.length == 0">
										<p>No Completed Match Available.</p>
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
<script>
	$('.entries').select2();
	</script>