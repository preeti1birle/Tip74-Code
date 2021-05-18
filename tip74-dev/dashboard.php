<?php include('header.php') ?>
	
	<div class="main-content" ng-controller="dashboardController" ng-cloak ng-init="getUserEntriesBalance(SelectedWeekGUID);getCompetitionList()">
		<div class="dashboard">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-9">
								<ul ng-class="{true:'top-bar stickybtn', false:'top-bar'}[UserEntriesBalance.ConsumedPredictions != UserEntriesBalance.AllowedPredictions]">
									<li>
										<span>Entry : {{UserEntriesBalance.EntryNo}}</span>
										<span>|</span>
										<span class="themeClr"> {{moneyFormat(profileDetails.WalletAmount)}}</span>
									</li>
									<li>
										<span><span class="themeClr">{{UserEntriesBalance.ConsumedPredictions ? UserEntriesBalance.ConsumedPredictions : '0'}}</span> / {{UserEntriesBalance.AllowedPredictions ? UserEntriesBalance.AllowedPredictions : '0'}} Prediction</span>
									</li>
									<li>
										<span><span class="themeClr">{{UserEntriesBalance.ConsumeDoubleUps ? UserEntriesBalance.ConsumeDoubleUps : '0'}}</span> / {{UserEntriesBalance.AllowedPurchaseDoubleUps ? UserEntriesBalance.AllowedPurchaseDoubleUps : '0'}} Double Up </span>
									</li>
								</ul>
							</div>
							<div class="col-md-3">
								<div class="stickybtn" ng-if="UserEntriesBalance.ConsumedPredictions==UserEntriesBalance.AllowedPredictions">
									<a href="javascript:void(0)" ng-click="getEntryList(WeekGUID);getUserBalance(WeekGUID);" class="btn_primary btn"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Purchase Entry </a>
									<!-- <a ng-if="UnAssignedEntries.Records.length<=0 || !UnAssignedEntries.Records" href="javascript:void(0)" ng-click="openPopup('entryPopup');getEntryList();getUserBalance(WeekGUID)" class="btn_primary btn"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Purchase Entry </a> -->
									<!-- <a ng-if="UnAssignedEntries.Records.length>0" href="javascript:void(0)" ng-click="openPopup('assignPopup');getUserEntriesBalance(WeekGUID);getEntryList();getUserBalance(WeekGUID)" class="btn_primary btn"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Assign Entry </a> -->
								</div>
							</div>
						</div>
								
					
						
						<div class="row  px-0 mt-2">
							<div class="custom_select col-4 mt-3 pr-0">
								<select class="form-control customReadOnlyField" ng-model="CompetitionGUID" ng-change="getLeagues(CompetitionGUID)" >
									<option ng-repeat="Competition in CompetitionList" value="{{Competition.CompetitionGUID}}" >{{Competition.CompetitionName}}</option>
								</select>
							</div>
							<div class="custom_select col-4 mt-3 pr-0">
								<select class="form-control customReadOnlyField" ng-model="LeagueGUID" ng-change="changeLeague()" >
									<option ng-repeat="league in LeagueList" value="{{league.LeagueGUID}}" >{{league.LeagueName}}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-6 col-md-8 col-lg-9 order-2 order-sm-1">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<li class="nav-item">
								<a class="nav-link {{(activeTab == 'Pending')?'active':''}}" ng-click="gotoTab('Pending')" id="Pending-tab" href="javascript:void(0)" role="tab" aria-controls="Pending" aria-selected="true">Upcoming</a>
							</li>
							<li class="nav-item">
								<a class="nav-link {{(activeTab == 'Live')?'active':''}}" ng-click="gotoTab('Live')" id="Live-tab" href="javascript:void(0)" role="tab" aria-controls="Live" aria-selected="false">Live</a>
							</li>
							<li class="nav-item">
								<a class="nav-link {{(activeTab == 'Completed')?'active':''}}" ng-click="gotoTab('Completed')" id="Completed-tab" href="javascript:void(0)" role="tab" aria-controls="Completed" aria-selected="false">Completed</a>
							</li>
						</ul>
					</div>
					<!-- <div class="col-sm-6 col-md-4 col-lg-3 order-1 order-sm-2 switchBox">
						<p>only show unmade picks 
							<label class="switch-toggle">
						  		<input type="checkbox">
								<span class="slider"></span>
							</label>
						</p>
					</div> -->
				</div>
				<div class="row">
					<div class="col-md-12">
						<div ng-if="WeekStatus" class="round-slider" slick-week-custom-carousel>
							<div ng-if = "(activeTab !== 'Live')" ng-repeat="week in WeekList" ng-click="changeWeek(week.WeekGUID)">
								<div class="round {{(((week.Status == 'Completed' || week.Status == 'Pending' ) && (activeTab == 'Pending'))||((week.Status == 'Completed')&&(activeTab == 'Live')))?'slide_disabled':''}} {{(week.WeekGUID == WeekGUID)?'active':''}}">
									<p>Week {{week.WeekCount}}</p>
									<p>{{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row mt-2">
					<div class="col-md-12">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade {{(activeTab == 'Pending')?'show active':''}}" id="Pending" role="tabpanel" aria-labelledby="Pending-tab">
								<!-- <div class="time">
									<div class="col-md-4 col-sm-6">
										<span><i class="fa fa-clock-o"></i> 24 Hours</span>
										<span>|</span>
										<span> 0 Picks Needed</span>
									</div>
									<div class="col-md-4 col-sm-6 mt-3 mt-sm-0">
										<span><i class="fa fa-clock-o"></i> 24 Hours</span>
										<span>|</span>
										<span> 0 Picks Needed</span>
									</div>
									<div class="col-md-4 col-sm-6 mt-3 mt-md-0">
										<span><i class="fa fa-clock-o"></i> 72 Hours</span>
										<span>|</span>
										<span> 0 Picks Needed</span>
									</div>
								</div> -->

								<div class="match-details">
									<div class="date-wise" ng-repeat="matchInfo in MatchesList" ng-if="matchInfo.Matches.length>0">
										<div class="date"><i class="fa fa-calendar"></i> {{matchInfo.MatchDate | date: 'EEE d MMMM'}}</div>
										<ul >
											<li ng-repeat="match in matchInfo.Matches">
												<div><img ng-src="{{match.LeagueFlag}}"  class="mr-3">{{match.LeagueName}}</div>
												<div class="teams">
													<div><img ng-src="{{match.TeamFlagLocal}}" alt="{{match.TeamNameShortLocal}}">{{match.TeamNameShortLocal}}</div>
													<div class="text-center">{{(match.Status == 'Completed')?match.MatchScoreDetails.FullTimeScore: match.MatchTime }}</div>
													<div class="text-right">{{match.TeamNameShortVisitor}}<img ng-src="{{match.TeamFlagVisitor}}" alt="{{match.TeamNameShortVisitor}}"></div>
												</div>
												<div class="info text-right" ng-if="match.IsPredicted == 'No'">
													<a href="javascript:void(0)"><img src="assets/img/info.png" alt=""></a>
													<a href="prediction?CompetitionGUID={{CompetitionGUID}}&LeagueGUID={{LeagueGUID}}&MatchGUID={{match.MatchGUID}}&WeekGUID={{WeekGUID}}&activeTab={{activeTab}}">{{match.Status == 'Completed'?'No Pick':'Pick Now'}}</a>
												</div>
												<div class="info text-right" ng-if="match.IsPredicted == 'Yes'">
													<a href="prediction?LeagueGUID={{LeagueGUID}}&MatchGUID={{match.MatchGUID}}&WeekGUID={{WeekGUID}}&activeTab={{activeTab}}">
														<span ng-if="match.PredictionDetails.length && getSavedPredictions(match.PredictionDetails).length>0">
															<i data-toggle="tooltip" data-placement="top" title="{{(getSavedPredictions(match.PredictionDetails)[0].PredictionStatus == 'Lock')?' Locked':' Saved'}}" class="fa fa-{{(getSavedPredictions(match.PredictionDetails)[0].PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(getSavedPredictions(match.PredictionDetails)[0].PredictionStatus == 'Lock')?'danger':'danger'}}" style="font-size: large;" aria-hidden="true"></i> {{getSavedPredictions(match.PredictionDetails)[0].PredictionStatus == 'Lock'? 'Locked': 'Saved'}} {{getSavedPredictions(match.PredictionDetails).length}}</span>
														</span>
														<span ng-if="match.PredictionDetails.length && getLockedPredictions(match.PredictionDetails).length>0">
															<i data-toggle="tooltip" data-placement="top" title="{{(getLockedPredictions(match.PredictionDetails)[0].PredictionStatus != 'Lock')?' Saved':' Locked'}}" class="fa fa-{{(getLockedPredictions(match.PredictionDetails)[0].PredictionStatus != 'Lock')?'unlock-alt':'lock'}} text-{{(getLockedPredictions(match.PredictionDetails)[0].PredictionStatus != 'Lock')?'success':'success'}}" style="font-size: large;" aria-hidden="true"></i> {{getLockedPredictions(match.PredictionDetails)[0].PredictionStatus != 'Lock'? ' Saved':' Locked'}} {{getLockedPredictions(match.PredictionDetails).length}} Prediction</span>
														</span>

														<!-- <span ng-if="match.PredictionDetails[0].hasOwnProperty('PredictionStatus')"><i data-toggle="tooltip" data-placement="top" title="{{(match.PredictionDetails[0].PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}" class="fa fa-{{(match.PredictionDetails[0].PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(match.PredictionDetails[0].PredictionStatus == 'Lock')?'danger':'success'}}" style="font-size: large;" aria-hidden="true"></i> Full Time ({{match.PredictionDetails[0].TeamScoreLocalFT}} - {{match.PredictionDetails[0].TeamScoreVisitorFT}})</span>
														<span ng-if="match.PredictionDetails[0].hasOwnProperty('PredictionStatus')"><i data-toggle="tooltip" data-placement="top" title="{{(match.PredictionDetails[0].PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}" class="fa fa-{{(match.PredictionDetails[0].PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(match.PredictionDetails[0].PredictionStatus == 'Lock')?'danger':'success'}}" style="font-size: large;" aria-hidden="true"></i> Half Time ({{match.PredictionDetails[0].TeamScoreLocalHT}} - {{match.PredictionDetails[0].TeamScoreVisitorHT}})</span> -->
													</a>
												</div>
												<div class="enter">
													<a href="prediction?CompetitionGUID={{CompetitionGUID}}&LeagueGUID={{LeagueGUID}}&MatchGUID={{match.MatchGUID}}&WeekGUID={{WeekGUID}}&activeTab={{activeTab}}"><img src="assets/img/arrow-right-whitebg.png"></a>
												</div>
											</li>
										</ul>
									</div>
									<div class="text-white text-center mt-2" ng-if="MatchesList.length == 0">
										<p>No Match Available.</p>
									</div>
								</div>
							</div>

							<div class="tab-pane fade liveSec {{(activeTab == 'Live')?'show active':''}}" id="Live" role="tabpanel" aria-labelledby="Live-tab">
								<div class="live-result">
									<div class="col-6">
										<p class="live">Live ( Week {{WeekList[0].WeekCount}},  {{WeekList[0].WeekStartDate | date:'dd'}}-{{WeekList[0].WeekEndDate | date:'dd MMM'}} )</p>
									</div>
									<div class="col-6 text-right" >
										<a href="javascript:void(0)" class="refershAnimation"  ng-click="getMatches()" >
											<i class="fa fa-refresh mr-2" ></i>
											Refresh
										</a>
									</div>
								</div>
								<div class="match-details">
									<div class="date-wise" ng-repeat="Date in MatchesList">
										<div class="date"><i class="fa fa-calendar"></i> {{Date.MatchDate  | date: 'EEE d MMMM'}}</div>
										<ul>
											<li ng-repeat="match in Date.Matches">
												<div class="teamAndTime">
													<img ng-src="{{match.LeagueFlag}}" class="mx-4">
													<span>{{match.LeagueName}}</span>
													<span>{{match.MatchStartDateTime | date: 'EEE d MMMM' }}</span>
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
														<p class="col-4 col-sm-6" ng-if="match.IsPredicted == 'No'">No Pick</p>
														<span ng-if="match.PredictionDetails.hasOwnProperty('PredictionStatus')" class="pr-1"><i data-toggle="tooltip" data-placement="top" title="{{(match.PredictionDetails.PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}" class="fa fa-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'danger':'success'}}" style="font-size: large;" aria-hidden="true"></i> Full Time ({{match.PredictionDetails.TeamScoreLocalFT}} - {{match.PredictionDetails.TeamScoreVisitorFT}})</span>
														<span ng-if="match.PredictionDetails.hasOwnProperty('PredictionStatus')" class="pr-1"><i data-toggle="tooltip" data-placement="top" title="{{(match.PredictionDetails.PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}" class="fa fa-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'danger':'success'}}" style="font-size: large;" aria-hidden="true"></i> Half Time ({{match.PredictionDetails.TeamScoreLocalHT}} - {{match.PredictionDetails.TeamScoreVisitorHT}})</span>
														<span class="">|</span>
														<!-- <p class="results col-4 col-sm-6">1.5 Results</p> -->
														<!-- <p class="wrong col-4 col-sm-6">0 Wrong</p> -->
													</div>
													<div class="enter">
														<a href="live-result-inner?MatchGUID={{match.MatchGUID}}&LeagueGUID={{LeagueGUID}}&WeekGUID={{WeekGUID}}&activeTab={{activeTab}}">{{activeTab}}<img src="assets/img/arrow-right-whitebg.png"></a>
													</div>
												</div>
											</li>
										</ul>
									</div>
									<div class="text-white text-center mt-2" ng-if="MatchesList.length == 0">
										<p>No Match Available.</p>
									</div>
								</div>
							</div>
							<div class="tab-pane fade liveSec {{(activeTab == 'Completed')?'show active':''}}" id="Completed" role="tabpanel" aria-labelledby="Completed-tab">
								<div class="match-details">
									<div class="date-wise" ng-repeat="Date in MatchesList">
										<div class="date"><i class="fa fa-calendar"></i> {{Date.MatchDate  | date: 'EEE d MMMM'}}</div>
										<ul>
											<li ng-repeat="match in Date.Matches">
												<div class="teamAndTime">
													<img ng-src="{{match.LeagueFlag}}" class="mx-4">
													<span>{{match.LeagueName}}</span>
													<span>{{match.MatchTime}}</span>
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
														<p class="col-4 col-sm-6" ng-if="match.IsPredicted == 'No'">No Pick</p>
														<span ng-if="match.PredictionDetails.hasOwnProperty('PredictionStatus')" class="pr-1"><i data-toggle="tooltip" data-placement="top" title="{{(match.PredictionDetails.PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}" class="fa fa-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'danger':'success'}}" style="font-size: large;" aria-hidden="true"></i> Full Time ({{match.PredictionDetails.TeamScoreLocalFT}} - {{match.PredictionDetails.TeamScoreVisitorFT}})</span>
														<span ng-if="match.PredictionDetails.hasOwnProperty('PredictionStatus')" class="pr-1"><i data-toggle="tooltip" data-placement="top" title="{{(match.PredictionDetails.PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}" class="fa fa-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(match.PredictionDetails.PredictionStatus == 'Lock')?'danger':'success'}}" style="font-size: large;" aria-hidden="true"></i> Half Time ({{match.PredictionDetails.TeamScoreLocalHT}} - {{match.PredictionDetails.TeamScoreVisitorHT}})</span>
														<span class="">|</span>
														<!-- <p class="results col-4 col-sm-6">1.5 Results</p> -->
														<!-- <p class="wrong col-4 col-sm-6">0 Wrong</p> -->
													</div>
													<div class="enter">
														<a href="live-result-inner?MatchGUID={{match.MatchGUID}}&LeagueGUID={{LeagueGUID}}&WeekGUID={{WeekGUID}}&activeTab={{activeTab}}"><img src="assets/img/arrow-right-whitebg.png"></a>
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
	$('.refershAnimation').on('click', function(){
		$(this).addClass('rotating');
		setTimeout(function(){ $('.refershAnimation').removeClass('rotating') }, 550);		
	});
</script>