<?php include('header.php') ?>
	<div class="main-content" ng-controller="predictionController" ng-init="getUserEntriesBalance(SelectedWeekGUID);getCompetitionList()" ng-cloak>
		<div class="dashboard dashboard-inner">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-12">
						<ul class='top-bar-list'>
							<span ng-class="{true:'top-bar stickybtn', false:'top-bar'}[UserEntriesBalance.ConsumedPredictions != UserEntriesBalance.AllowedPredictions]">
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
							</span>
							<button  class="btn bg-gradient back-btn-position" ng-click="goBack()">Go Back</button>
						</ul>
						<div class="row">
							<div class="custom_select col-4 mt-3 mb-3 pr-0">
								<select class="form-control customReadOnlyField" ng-model="CompetitionGUID" ng-change="getLeagues(CompetitionGUID, 'custom')" >
									<option ng-repeat="Competition in CompetitionList" value="{{Competition.CompetitionGUID}}" >{{Competition.CompetitionName}}</option>
								</select>
							</div>
							<div class="custom_select col-4 mt-3 pr-0">
								<select class="form-control customReadOnlyField" ng-model="LeagueGUID" ng-change="changeLeague()" >
									<option ng-repeat="league in LeagueList" value="{{league.LeagueGUID}}" >{{league.LeagueName}}</option>
								</select>
							</div>
							<div class="col-4 mt-3 mb-3 pr-0" ng-if="UserEntriesBalance.ConsumedPredictions==UserEntriesBalance.AllowedPredictions">
								<div id="stickybtn" class="stickybtn">
									<a href="javascript:void(0)" ng-click="getEntryList(WeekGUID, LeagueGUID, MatchGUID);getUserBalance(WeekGUID);changeWeek(WeekGUID)" class="btn_primary btn"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Purchase Entry </a>
									<!-- <a ng-if="UnAssignedEntries.ToralRecords==0" href="javascript:void(0)" ng-click="openPopup('entryPopup');getEntryList();getUserBalance(WeekGUID)" class="btn_primary btn"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Purchase Entry </a>
									<a ng-if="UnAssignedEntries.TotalRecords >= 0" href="javascript:void(0)" ng-click="openPopup('assignPopup');getUserEntriesBalance(WeekGUID);getEntryList();getUserBalance(WeekGUID)" class="btn_primary btn"> <i class="fa fa-money fa-1x mr-2"aria-hidden="true"></i> Assign Entry </a> -->
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="bg-gradient top-head">
							<div class="row w-100">
								<h6 class="col-sm-5">{{SelectedLeagueInfo.LeagueName}}</h6>
								<div class="col-sm-7 px-0 right-site">
									<!-- <a class="javascript:void(0)"><i class="fa fa-users pr-2"></i>Pools</a>
									<a class="javascript:void(0)"><i class="fa fa-user pr-2"></i>Performance</a>
									<a class="javascript:void(0)"><img src="assets/img/info.png" alt="" class="pr-2">Info</a> -->
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="col-md-12">
						<div class="setting-bar">
							<div class="row align-items-center">
								<div class="col-sm-6 col-md-4 col-lg-3">
									<select id="inputState" class="form-control">
										<option selected>Vivacious giraffes 17/18</option>
										<option>option2</option>
										<option>option3</option>
										<option>option4</option>
										<option>option5</option>
									</select>
								</div>
								<div class="col-sm-5 col-md-7 col-lg-8 px-sm-0 mt-2 mt-sm-0 col-10">
									<ul class="pools">
										<li>
											<span>Add a Pool</span>
											<a href="javascript:void(0)" class="addPool">+</a>
										</li>
									</ul>
								</div>
								<div class="col-sm-1 pl-0 mt-2 mt-sm-0 text-right col-2">
									<a href="javascript:void(0)"><i class="fa fa-cog"></i></a>
								</div>
							</div>
						</div>
					</div> -->
				</div>

				<div class="row">
					<div class="col-md-12">
						<div ng-if="WeekStatus" class="round-slider" slick-week-custom-carousel>
							<div ng-repeat="week in WeekList" ng-click="changeWeek(week.WeekGUID)">
								<div class="round {{ (week.Status == 'Completed' || week.Status == 'Pending')?'slide_disabled':''}} {{(week.WeekGUID == WeekGUID)?'active':''}}">
									<p>Week {{$index+1}}</p>
									<p>{{week.WeekStartDate | date:'dd'}}-{{week.WeekEndDate | date:'dd MMM'}}</p>
								</div>
								
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-8">
						<div class="scrollbar">	
							<div class="player-details" ng-if="matchInfo.Matches.length>0" ng-repeat="matchInfo in MatchesList">
								<div class="date"><i class="fa fa-calendar"></i> {{matchInfo.MatchDate | date: 'EEE d MMMM'}}</div>
								<ul class="player-details-list" ng-repeat="match in matchInfo.Matches" scroll-If="match.MatchGUID == MatchGUID">
									<li class="top-bar">
										<div class="col-md-1 col-3 order-1 order-md-1 px-0">
											<img ng-src="{{match.VenueImage}}" style="width: 100%;height: 50px;">
										</div>
										<p class="col-md-8 col-sm-12 order-3 order-md-2 pl-0 pl-md-3 mt-2 mt-md-0">
											<span>{{match.VenueName}}, {{match.VenueCity}}</span><br>
											<span>Capacity: {{numberFormat(match.VenueCapicity)}}</span>
										</p>
										<p class="col-md-3 col-9 order-2 order-md-3 pr-0 text-right" ng-if="match.Status !='Completed'">{{match.MatchDate | date: 'EEE d MMMM,' }}  {{ match.MatchTime }}</p>
										<p class="col-md-3 col-9 order-2 order-md-3 pr-0 text-right" ng-if="match.Status =='Completed'">{{match.MatchScoreDetails.FullTimeScore}}</p>
									</li>
									<li class="mid-bar">
										<div class="col-sm-4">
											<img ng-src="{{match.TeamFlagLocal}}" alt="{{match.TeamNameShortLocal}}" class="img-fluid m-0">
											<p>{{match.TeamNameLocal}}</p>
											<p>
												P <span class="mr-3">{{match.TeamStandingsLocal.Points}}</span>
												W <span class="mr-3">{{match.TeamStandingsLocal.Overall.Won}}</span>
												L <span class="">{{match.TeamStandingsLocal.Overall.Lost}}</span>
											</p>
											<div class="match-status">
												<p ng-repeat="stat in match.TeamLastThreeMatchesLocal track by $index" class="{{stat == 'Won'?Colors[0]:(stat == 'Lost')?Colors[2]:Colors[1]}}" ng-click="teamModal(match,match.TeamGUIDLocal,'local')">{{getUserNameFirstLetter(stat,'First')}}</p>
											</div>
										</div>
										<div class="col-sm-4 timeLimit">
											<div ng-if="!match.PredictionAdded">
												<p>No Pick</p>
											</div>
											<div ng-if="match.PredictionAdded">
												<div class="fullTime">
													<p>FULL TIME</p>
													<div class="d-flex">
														<form>
															<div id="textDropdown"  class="dropdown">
																<input class="dropdown-toggle" type="text" numbers-only  ng-model="NewPrediction.TeamScoreLocalFT"  name="FullTime_TeamScoreLocal_{{match.MatchGUID}}"  data-toggle="dropdown">
																<ul class="list-unstyled dropdown-menu" ng-if="match.Status == 'Pending'">
																	<li ng-click="selectValue(match,'local','Full','0',NewPrediction)">0</li>
																	<li ng-click="selectValue(match,'local','Full','1',NewPrediction)">1</li>
																	<li ng-click="selectValue(match,'local','Full','2',NewPrediction)">2</li>
																	<li ng-click="selectValue(match,'local','Full','3',NewPrediction)">3</li>
																	<li ng-click="selectValue(match,'local','Full','4',NewPrediction)">4</li>
																	<li ng-click="selectValue(match,'local','Full','5',NewPrediction)">5</li>
																	<li ng-click="selectValue(match,'local','Full','6',NewPrediction)">6</li>
																	<li ng-click="selectValue(match,'local','Full','7',NewPrediction)">7</li>
																	<li ng-click="selectValue(match,'local','Full','8',NewPrediction)">8</li>
																	<li ng-click="selectValue(match,'local','Full','9',NewPrediction)">9</li>
																	<li ng-click="selectValue(match,'local','Full','10',NewPrediction)">10</li>
																	<li ng-click="selectValue(match,'local','Full','11',NewPrediction)">11</li>
																	<li ng-click="selectValue(match,'local','Full','12',NewPrediction)">12</li>
																</ul>
															</div>
														</form>
														<span>-</span>
														<form>
															<div id="textDropdown" class="dropdown">
																<input type="text" numbers-only  ng-model="NewPrediction.TeamScoreVisitorFT" name="FullTime_TeamScoreVisitor_{{match.MatchGUID}}"  data-toggle="dropdown">
																<ul class="list-unstyled dropdown-menu" ng-if="match.Status == 'Pending'">
																	<li ng-click="selectValue(match,'visitor','Full','0',NewPrediction)">0</li>
																	<li ng-click="selectValue(match,'visitor','Full','1',NewPrediction)">1</li>
																	<li ng-click="selectValue(match,'visitor','Full','2',NewPrediction)">2</li>
																	<li ng-click="selectValue(match,'visitor','Full','3',NewPrediction)">3</li>
																	<li ng-click="selectValue(match,'visitor','Full','4',NewPrediction)">4</li>
																	<li ng-click="selectValue(match,'visitor','Full','5',NewPrediction)">5</li>
																	<li ng-click="selectValue(match,'visitor','Full','6',NewPrediction)">6</li>
																	<li ng-click="selectValue(match,'visitor','Full','7',NewPrediction)">7</li>
																	<li ng-click="selectValue(match,'visitor','Full','8',NewPrediction)">8</li>
																	<li ng-click="selectValue(match,'visitor','Full','9',NewPrediction)">9</li>
																	<li ng-click="selectValue(match,'visitor','Full','10',NewPrediction)">10</li>
																	<li ng-click="selectValue(match,'visitor','Full','11',NewPrediction)">11</li>
																	<li ng-click="selectValue(match,'visitor','Full','12',NewPrediction)">12</li>
																</ul>
															</div>
														</form>
													</div>
												</div>
											</div>
											<div ng-if="match.PredictionAdded">
												<div class="halfTime" >
													<p>HALF TIME</p>
													<div class="d-flex">
														<form>
															<div id="textDropdown" class="dropdown">
																<input type="text" numbers-only ng-model="NewPrediction.TeamScoreLocalHT"  name="HalfTime_TeamScoreLocal_{{match.MatchGUID}}"  data-toggle="dropdown">
																<ul class="list-unstyled dropdown-menu" ng-if="match.Status == 'Pending' ">
																	<li ng-click="selectValue(match,'local','Half','0',NewPrediction)">0</li>
																	<li ng-click="selectValue(match,'local','Half','1',NewPrediction)">1</li>
																	<li ng-click="selectValue(match,'local','Half','2',NewPrediction)">2</li>
																	<li ng-click="selectValue(match,'local','Half','3',NewPrediction)">3</li>
																	<li ng-click="selectValue(match,'local','Half','4',NewPrediction)">4</li>
																	<li ng-click="selectValue(match,'local','Half','5',NewPrediction)">5</li>
																	<li ng-click="selectValue(match,'local','Half','6',NewPrediction)">6</li>
																	<li ng-click="selectValue(match,'local','Half','7',NewPrediction)">7</li>
																	<li ng-click="selectValue(match,'local','Half','8',NewPrediction)">8</li>
																	<li ng-click="selectValue(match,'local','Half','9',NewPrediction)">9</li>
																	<li ng-click="selectValue(match,'local','Half','10',NewPrediction)">10</li>
																	<li ng-click="selectValue(match,'local','Half','11',NewPrediction)">11</li>
																	<li ng-click="selectValue(match,'local','Half','12',NewPrediction)">12</li>
																</ul>
															</div>
														</form>
														<span>-</span>
														<form>
															<div id="textDropdown" class="dropdown">
																<input type="text" numbers-only  ng-model="NewPrediction.TeamScoreVisitorHT"  name="HalfTime_TeamScoreVisitor_{{match.MatchGUID}}"  data-toggle="dropdown">
																<ul class="list-unstyled dropdown-menu" ng-if="match.Status == 'Pending' ">
																	<li ng-click="selectValue(match,'visitor','Half','0',NewPrediction)">0</li>
																	<li ng-click="selectValue(match,'visitor','Half','1',NewPrediction)">1</li>
																	<li ng-click="selectValue(match,'visitor','Half','2',NewPrediction)">2</li>
																	<li ng-click="selectValue(match,'visitor','Half','3',NewPrediction)">3</li>
																	<li ng-click="selectValue(match,'visitor','Half','4',NewPrediction)">4</li>
																	<li ng-click="selectValue(match,'visitor','Half','5',NewPrediction)">5</li>
																	<li ng-click="selectValue(match,'visitor','Half','6',NewPrediction)">6</li>
																	<li ng-click="selectValue(match,'visitor','Half','7',NewPrediction)">7</li>
																	<li ng-click="selectValue(match,'visitor','Half','8',NewPrediction)">8</li>
																	<li ng-click="selectValue(match,'visitor','Half','9',NewPrediction)">9</li>
																	<li ng-click="selectValue(match,'visitor','Half','10',NewPrediction)">10</li>
																	<li ng-click="selectValue(match,'visitor','Half','11',NewPrediction)">11</li>
																	<li ng-click="selectValue(match,'visitor','Half','12',NewPrediction)">12</li>
																</ul>
															</div>
														</form>
													</div>
												</div>
											</div>
											<!-- <div class="fullTime mt-2" ng-if="match.PredictionDetails.hasOwnProperty('PredictionStatus')">
												<i class="fa fa-lock text-danger" style="font-size: large;" ng-if="match.PredictionDetails.PredictionStatus == 'Lock'" aria-hidden="true"></i>
												<a href="javascript:void(0)" class="text-success" style="font-size: large;" ng-if="match.canPredict == 'Yes'" ng-click="openLockModal(match)">
												<i class="fa fa-unlock-alt" aria-hidden="true"></i></a></br>
												<p ng-if="match.PredictionDetails.PredictionStatus == 'Save'" class="lockedTime text-success">Saved {{match.PredictionDetails.SavedDateTime | date :'dd MMM HH:mm'}}</p>
												<p ng-if="match.PredictionDetails.PredictionStatus == 'Lock'" class="text-danger">Locked {{match.PredictionDetails.LockedDateTime | date :'dd MMM HH:mm'}}</p>
												<a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Lock to see other players' picks before kick-off." ng-if="match.PredictionDetails.PredictionStatus == 'Save'" ng-click="openLockModal(match)" class="text-success">Lock Pick</a>
											</div> -->
											<!-- <div class="fullTime mt-2">
											<i class="fa fa-lock text-danger" style="font-size: large;" ng-if="match.canPredict != 'Yes'" aria-hidden="true"></i>
											<p  class="lockedTime text-success" ng-click="savePrediction(match)" ng-if="match.canPredict == 'Yes'">Saved</p>
											<p ng-if="match.canPredict != 'Yes'" class="text-danger">Locked</p>
											</div> -->
											<!-- <div class="form-check pl-0" ng-if="match.PredictionAdded">
												<input type="checkbox" class="styled-checkbox" ng-if="match.PredictionDetails.PredictionStatus != 'Lock'" ng-disabled="match.PredictionDetails.PredictionStatus == 'Lock'" ng-model="match.PredictionDetails.IsDoubleUps" id="half_IsDoubleUps_{{match.MatchGUID}}" name="half_IsDoubleUps_{{match.MatchGUID}}" ng-click="savePrediction(match)" value="">
												<label for="half_IsDoubleUps_{{match.MatchGUID}}" class="{{match.PredictionDetails.IsDoubleUps && match.PredictionDetails.PredictionStatus == 'Lock'?'text-danger':'text-success'}}"> {{(match.PredictionDetails.PredictionStatus != 'Lock')?'Double Up':(match.PredictionDetails.IsDoubleUps)?'Double Up Applied':''}}  </label>
											</div> -->
										</div>
										<div class="col-sm-4">
											<img ng-src="{{match.TeamFlagVisitor}}" alt="{{match.TeamNameShortVisitor}}" class="img-fluid m-0">
											<p><!-- <i class="fa fa-lock mr-2"></i> --> {{match.TeamNameVisitor}}</p>
											<p>
												P <span class="mr-3">{{match.TeamStandingsVisitor.Points}}</span>
												W <span class="mr-3">{{match.TeamStandingsVisitor.Overall.Won}}</span>
												L <span class="">{{match.TeamStandingsVisitor.Overall.Lost}}</span>
											</p>
											<div class="match-status">
												<p ng-repeat="stat in match.TeamLastThreeMatchesVisitor track by $index" class="{{stat == 'Won'?Colors[0]:(stat == 'Lost')?Colors[2]:Colors[1]}}" ng-click="teamModal(match,match.TeamGUIDVisitor,'visitor')">{{getUserNameFirstLetter(stat,'First')}}</p>
											</div>
										</div>
									</li>
									<li ng-if="match.PredictionDetails.length > 0">
										<div class="liveSec bgClr text-white">
										<b  class="text-white">Prediction details:-</b>
											<div class="teams mt-2" ng-repeat="prediction in match.PredictionDetails track by $index">
												<div class="inputBox">
													<span ng-if="prediction.hasOwnProperty('PredictionStatus')">
														<i data-toggle="tooltip" data-placement="top"
															title="{{(prediction.PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}"
															class="fa fa-{{(prediction.PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(prediction.PredictionStatus == 'Lock')?'danger':'success'}}"
															style="font-size: large;" aria-hidden="true">
														</i>
													</span>
													<form>
														Full Time
														<input type="text" name="FullTime_TeamScoreLocal_{{match.MatchGUID}}" value="{{prediction.TeamScoreLocalFT}}"  ng-model="prediction.TeamScoreLocalFT" ng-readOnly="(prediction.PredictionStatus == 'Lock' ? true : false)" min="0" max="12"
														ng-blur="prediction.PredictionStatus == 'Lock' || updatePrediction(match, $index, 'prediction')" > -
														<input type="text" name="FullTime_TeamScoreVisitor_{{match.MatchGUID}}" value="{{prediction.TeamScoreVisitorFT}}" ng-model="prediction.TeamScoreVisitorFT" ng-readOnly="(prediction.PredictionStatus == 'Lock' ? true : false)"  min="0" max="12"
														ng-blur="prediction.PredictionStatus == 'Lock' || updatePrediction(match, $index, 'prediction')">
														</form>
														<span>Half Time</span>
														<form>
														<input type="text" name="HalfTime_TeamScoreLocal_{{match.MatchGUID}}" value="{{prediction.TeamScoreLocalHT}}" ng-model="prediction.TeamScoreLocalHT" ng-readOnly="(prediction.PredictionStatus == 'Lock' ? true : false)"  min="0" max="12"
														ng-blur="prediction.PredictionStatus == 'Lock' || updatePrediction(match, $index, 'prediction')"> -
														<input type="text" name="HalfTime_TeamScoreVisitor_{{match.MatchGUID}}" value="{{prediction.TeamScoreVisitorHT}}" ng-model="prediction.TeamScoreVisitorHT" ng-readOnly="(prediction.PredictionStatus == 'Lock' ? true : false)"  min="0" max="12"
														ng-blur="prediction.PredictionStatus == 'Lock' || updatePrediction(match, $index, 'prediction')">
													</form>
													<span ng-if="prediction.hasOwnProperty('PredictionStatus')">
														<span ng-if="prediction.PredictionStatus == 'Save'" class="lockedTime text-success">{{prediction.SavedDateTime | convertIntoUserTimeZone}}</span>
														<span ng-if="prediction.PredictionStatus == 'Lock'" class="text-danger">{{prediction.LockedDateTime | convertIntoUserTimeZone}}</span>
														<i data-toggle="tooltip" data-placement="top"
															title="{{(prediction.PredictionStatus == 'Lock')?'Locked Prediction':'Saved Prediction'}}"
															class="fa fa-{{(prediction.PredictionStatus == 'Lock')?'lock':'unlock-alt'}} text-{{(prediction.PredictionStatus == 'Lock')?'danger':'success'}}"
															style="font-size: large;" aria-hidden="true" ng-if="prediction.PredictionStatus == 'Save'">
														</i>
														<a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Lock to see other players' picks before kick-off." ng-if="prediction.PredictionStatus == 'Save'" ng-click="openLockModal(match, $index)" class="text-success">Lock Pick</a>
													</span>
													<input ng-if="prediction.PredictionStatus != 'Lock'" ng-disabled="prediction.PredictionStatus == 'Lock'" ng-model="prediction.IsDoubleUps" id="half_IsDoubleUps_{{match.MatchGUID}}" name="half_IsDoubleUps_{{match.MatchGUID}}" type="checkbox" ng-model="prediction.IsDoubleUps" ng-true-value="'Yes'" ng-false-value="'NO'" ng-click="updatePrediction(match,$index, 'doubleup')">&nbsp;
													<label for="half_IsDoubleUps_{{match.MatchGUID}}" class="{{prediction.IsDoubleUps && prediction.PredictionStatus == 'Lock'?'text-danger':'text-success'}}">{{(prediction.PredictionStatus != 'Lock')?'Double Up':(prediction.IsDoubleUps == 'Yes')?'Double Up Applied':''}}  </label>
												</div>
											</div>
										</div>
									</li>
									<li class="mb-2" ng-if="match.FullTimePredictionStatics.hasOwnProperty('TeamWinPercentVisitor')">
										<div class="text-center">
											<p class="text-white" ng-if="match.FullTimePredictionStatics.TeamWinPercentLocal == match.FullTimePredictionStatics.TeamWinPercentVisitor"> Players are expecting both teams to win (Full Time).</p>
											<p class="text-white" ng-if="match.FullTimePredictionStatics.TeamWinPercentLocal != match.FullTimePredictionStatics.TeamWinPercentVisitor">{{match.FullTimePredictionStatics.TeamWinPercentLocal > match.FullTimePredictionStatics.TeamWinPercentVisitor?match.FullTimePredictionStatics.TeamWinPercentLocal:match.FullTimePredictionStatics.TeamWinPercentVisitor}}% of players are expecting {{match.FullTimePredictionStatics.TeamWinPercentLocal > match.FullTimePredictionStatics.TeamWinPercentVisitor?match.TeamNameLocal:match.TeamNameVisitor}} to win (Full Time)</p>
											<div class="progress">
												<div class="progress-bar" role="progressbar" aria-valuenow="{{match.FullTimePredictionStatics.TeamWinPercentLocal}}" aria-valuemin="0" aria-valuemax="100">{{match.FullTimePredictionStatics.TeamWinPercentLocal}}%</div>

												<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="{{match.FullTimePredictionStatics.TeamWinPercentLocal}}% of players picked a win for {{match.TeamNameLocal}}" role="progressbar" style="width: {{match.FullTimePredictionStatics.TeamWinPercentLocal}}%; background: {{match.TeamColorLocal}}" aria-valuenow="{{match.FullTimePredictionStatics.TeamWinPercentLocal}}" aria-valuemin="0" aria-valuemax="100"></div>
												<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="{{match.FullTimePredictionStatics.DrawPercent}}% of players predicted a draw" role="progressbar" style="width: {{match.FullTimePredictionStatics.DrawPercent}}%; background: #c3d7d9; color: #000" aria-valuenow="{{match.FullTimePredictionStatics.DrawPercent}}" aria-valuemin="0" aria-valuemax="100">{{match.FullTimePredictionStatics.DrawPercent}}%</div>
												<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="{{match.FullTimePredictionStatics.TeamWinPercentVisitor}}% of players picked a win for {{match.TeamNameVisitor}}" role="progressbar" style="width: {{match.FullTimePredictionStatics.TeamWinPercentVisitor}}%; background: {{match.TeamColorVisitor}}" aria-valuenow="{{match.FullTimePredictionStatics.TeamWinPercentVisitor}}" aria-valuemin="0" aria-valuemax="100"></div>

												<div class="progress-bar" role="progressbar" aria-valuenow="{{match.FullTimePredictionStatics.TeamWinPercentVisitor}}" aria-valuemin="0" aria-valuemax="100">{{match.FullTimePredictionStatics.TeamWinPercentVisitor}}%</div>
											</div>
										</div>
									</li>
									<li class="mb-2" ng-if="match.HalfTimePredictionStatics.hasOwnProperty('TeamWinPercentVisitor')">
										<div class="text-center">
											<p class="text-white" ng-if="match.HalfTimePredictionStatics.TeamWinPercentLocal == match.HalfTimePredictionStatics.TeamWinPercentVisitor"> Players are expecting both teams to win (Half Time).</p>
											<p class="text-white" ng-if="match.HalfTimePredictionStatics.TeamWinPercentLocal != match.HalfTimePredictionStatics.TeamWinPercentVisitor">{{match.HalfTimePredictionStatics.TeamWinPercentLocal > match.HalfTimePredictionStatics.TeamWinPercentVisitor?match.HalfTimePredictionStatics.TeamWinPercentLocal:match.HalfTimePredictionStatics.TeamWinPercentVisitor}}% of players are expecting {{match.HalfTimePredictionStatics.TeamWinPercentLocal > match.HalfTimePredictionStatics.TeamWinPercentVisitor?match.TeamNameLocal:match.TeamNameVisitor}} to win (Half Time)</p>
											<div class="progress">
												<div class="progress-bar" role="progressbar" aria-valuenow="{{match.HalfTimePredictionStatics.TeamWinPercentLocal}}" aria-valuemin="0" aria-valuemax="100">{{match.HalfTimePredictionStatics.TeamWinPercentLocal}}%</div>

												<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="{{match.HalfTimePredictionStatics.TeamWinPercentLocal}}% of players picked a win for {{match.TeamNameLocal}}" role="progressbar" style="width: {{match.HalfTimePredictionStatics.TeamWinPercentLocal}}%; background: {{match.TeamColorLocal}}" aria-valuenow="{{match.HalfTimePredictionStatics.TeamWinPercentLocal}}" aria-valuemin="0" aria-valuemax="100"></div>
												<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="{{match.HalfTimePredictionStatics.DrawPercent}}% of players predicted a draw" role="progressbar" style="width: {{match.HalfTimePredictionStatics.DrawPercent}}%; background: #c3d7d9; color: #000" aria-valuenow="{{match.HalfTimePredictionStatics.DrawPercent}}" aria-valuemin="0" aria-valuemax="100">{{match.HalfTimePredictionStatics.DrawPercent}}%</div>
												<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="{{match.HalfTimePredictionStatics.TeamWinPercentVisitor}}% of players picked a win for {{match.TeamNameVisitor}}" role="progressbar" style="width: {{match.HalfTimePredictionStatics.TeamWinPercentVisitor}}%; background: {{match.TeamColorVisitor}}" aria-valuenow="{{match.HalfTimePredictionStatics.TeamWinPercentVisitor}}" aria-valuemin="0" aria-valuemax="100"></div>

												<div class="progress-bar" role="progressbar" aria-valuenow="{{match.HalfTimePredictionStatics.TeamWinPercentVisitor}}" aria-valuemin="0" aria-valuemax="100">{{match.HalfTimePredictionStatics.TeamWinPercentVisitor}}%</div>
											</div>
										</div>
									</li>
									<li class="btm-bar">
										<div class="col-md-4 px-0">
											<a href="javascript:void(0)" ng-click="getTeamHistoricalResult(match.TeamGUIDLocal,match.TeamGUIDVisitor)" >Historic Results</a>
										</div>
										<div class="col-md-4 offset-md-4 px-0">
											<a href="javascript:void(0)" ng-click="getMatchTeamLineUp(match)">Team Lineups</a>
										</div>
										<div class="col-md-4 offset-md-4 px-0">
											<!-- <a href="leaderboard.php">Pool Picks</a> -->
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="standings">
							<div><h6>Standings</h6></div>
							<div class="details">
								<table>
									<thead>
										<tr>
											<th></th>
											<th></th>
											<th>W</th>
											<th>L</th>
											<th>D</th>
											<th>Pts</th>
										</tr>
									</thead>
									<tbody class="scrollbar">
										<tr ng-repeat="state in MatchStanding" ng-click="teamModal(state,state.TeamGUID,'')" >
											<td>{{$index+1}}</td>
											<td>{{state.TeamName}}</td>
											<td>{{state.TeamStandings.Overall.Won}}</td>
											<td>{{state.TeamStandings.Overall.Lost}}</td>
											<td>{{state.TeamStandings.Overall.Draw}}</td>
											<td>{{state.TeamStandings.Points}}</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="text-center mt-3"><a  class="btn_primary" href="FullStanding?LeagueGUID={{LeagueGUID}}">Full Standings</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Historic Result Modal  -->
		<div class="modal fade site_modal historic_result_modal" id="HistoricModal"  popup-handler tabindex="-1" role="dialog" aria-labelledby="HistoricModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="HistoricModalLabel">Historic Results</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
						<p class="text-center">Only fixtures covered by Tip74 are included</p>
						<h5 class="text-center">Last {{TeamHistoricalResult.Matches.TotalRecords}} results</h5>
						<ul class="list-unstyled team_results historic_result">
							<li style="background: {{TeamHistoricalResult.TeamDetailsLocal.TeamColor}}; color: #ffffff;">
								<p>{{TeamHistoricalResult.TeamDetailsLocal.TeamName}}</p>
								<small>Won</small>
								<h4>{{TeamHistoricalResult.TeamDetailsLocal.WonMatches}}</h4>
								<span>{{TeamHistoricalResult.TeamDetailsLocal.HomeMatches}} Home, {{TeamHistoricalResult.TeamDetailsLocal.AwayMatches}} Away </span>
							</li>
							<li class="bg-light">
								<p>Draw</p>
								<h4>{{TeamHistoricalResult.DrawMatches}}</h4>
							</li>
							<li style="background: {{TeamHistoricalResult.TeamDetailsVisitor.TeamColor}}; color: #ffffff;">
								<p>{{TeamHistoricalResult.TeamDetailsVisitor.TeamName}}</p>
								<small>Won</small>
								<h4>{{TeamHistoricalResult.TeamDetailsVisitor.WonMatches}}</h4>
								<span>{{TeamHistoricalResult.TeamDetailsVisitor.HomeMatches}} Home, {{TeamHistoricalResult.TeamDetailsVisitor.AwayMatches}} Away </span>
							</li>
						</ul>
						<div class="mt-3 d-flex align-items-center justify-content-center  ">
							<span ng-if="TimeDuration.length == 1 || TimeDuration.length > 1">{{TimeDuration[0]}}</span>
							<i ng-if="TimeDuration.length > 1"class="fa fa-long-arrow-right mx-1" aria-hidden="true"></i>
							<span ng-if="TimeDuration.length > 1">{{TimeDuration[TimeDuration.length -1]}}</span>
						</div>
						<div class="box_grid mb-3 text-center">
							<span ng-repeat="match in TeamHistoricalResult.Matches.Records | orderBy:'MatchDate'" class="mr-2" data-toggle="tooltip" data-placement="top" title="{{match.TeamNameLocal}} {{match.localTeamChanges?match.MatchScoreDetails.VisitorTeamScore+ ' - '+match.MatchScoreDetails.LocalTeamScore:match.MatchScoreDetails.LocalTeamScore+ ' - '+match.MatchScoreDetails.VisitorTeamScore}} {{match.TeamNameVisitor}} ({{match.NewMatchDate | myDateFormat}})" style="background:{{match.WinningTeamColor}}; color:{{match.MatchStatus == 'Win'?'#ffffff':''}}">{{(match.MatchStatus == 'Draw')?'D':getUserNameFirstLetter(match.WinningTeamName,'First')}}</span>
						</div>
						<!-- <p>only show unmade picks 
							<label class="switch-toggle">
								<input type="checkbox">
								<span class="slider"></span>
							</label>
						</p> -->
						<ul class="list-unstyled result_list">
							<li ng-repeat="match in TeamHistoricalResult.Matches.Records">
								<span class="date">{{match.NewMatchDate | date:'dd MMM y'}}</span>
								<div class="score_wrapr">
									<span class="{{match.MatchStatus == 'Draw'?'text-warning':(match.MatchStatus == 'Win' && match.WinningTeamName == match.TeamNameLocal)?'text-success':'text-danger'}}">{{match.TeamNameLocal}}</span>
									<strong class="score"ng-if="match.localTeamChanges">{{match.MatchScoreDetails.VisitorTeamScore}} - {{match.MatchScoreDetails.LocalTeamScore}}</strong>
									<strong class="score"ng-if="!match.localTeamChanges">{{match.MatchScoreDetails.LocalTeamScore}} - {{match.MatchScoreDetails.VisitorTeamScore}}</strong>
									<span class="{{match.MatchStatus == 'Draw'?'text-warning':(match.MatchStatus == 'Win' && match.WinningTeamName == match.TeamNameVisitor)?'text-success':'text-danger'}}">{{match.TeamNameVisitor}}</span>
								</div>
							</li>
						</ul>
					</div>

					<!-- <div class="modal-footer justify-content-center">
						<button type="button" class="btn btn-light m-0 px-4" data-dismiss="modal">Close</button>
					</div> -->
				</div>
			</div>
		</div>
		<!-- Historic Result Modal End  -->
		<!-- Lock Modal  -->
		<div class="modal fade site_modal lock_modal"  popup-handler id="LockModal" tabindex="-1" role="dialog" aria-labelledby="LockModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-danger text-white">
						<h5 class="modal-title" id="LockModalLabel"><span class="mr-3"><i class="fa fa-lock"></i></span> Are you sure?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body text-center">
						<p>Lock to see other players' picks before kick-off.</p>
						<h4><img ng-src="{{lockPopupData.TeamFlagLocal}}" alt="{{lockPopupData.TeamNameShortLocal}}" class="img-fluid m-0" style="width: 58px;"> {{lockPopupData.TeamNameLocal}} Vs<img ng-src="{{lockPopupData.TeamFlagVisitor}}" alt="{{lockPopupData.TeamNameShortLocal}}" class="img-fluid m-0" style="width: 58px;"> {{lockPopupData.TeamNameVisitor}}</h5>
						<h5>Full Time : {{lockPopupData.selectedPrediction.TeamScoreLocalFT}} - {{lockPopupData.selectedPrediction.TeamScoreVisitorFT}} </h5>
						<h5>Half Time : {{lockPopupData.selectedPrediction.TeamScoreLocalHT}} - {{lockPopupData.selectedPrediction.TeamScoreVisitorHT}} </h5>
						<!-- <span class="text-danger">You're picking quite an upset!</span> -->
						<input id="half_IsDoubleUps_{{lockPopupData.MatchGUID}}" name="half_IsDoubleUps_{{lockPopupData.MatchGUID}}" type="checkbox" ng-model="IsDoubleUps" ng-true-value="'Yes'" ng-false-value="'NO'" ng-click="updatePrediction(lockPopupData, $event, 'doubleup')">&nbsp;
						<label for="half_IsDoubleUps_{{lockPopupData.MatchGUID}}" class="{{IsDoubleUps && prediction.PredictionStatus == 'Lock'?'text-danger':'text-success'}}">Double Up  </label>

						<p class="mt-3">Once you lock, <strong>YOU CAN'T CHANGE YOUR PICK</strong></p>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn_gray px-4" data-dismiss="modal" ng-click="closeSaveModal(lockPopupData)">Cancel</button>
						<button type="button" class="btn_primary px-4" ng-disabled="disableLockButton"  ng-click="savePrediction(lockPopupData, lockPopupData.selectedPrediction)">Lock</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Save Modal  -->
		<!-- <div class="modal fade site_modal save_modal"  popup-handler id="SaveModal" tabindex="-1" role="dialog" aria-labelledby="SaveModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-danger text-white">
						<h5 class="modal-title" id="SaveModalLabel"><span class="mr-3"><i class="fa fa-lock"></i></span> Are you sure?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body text-center">
						<p>Save to see other players' picks before kick-off.</p>
						<h4><img ng-src="{{lockPopupData.TeamFlagLocal}}" alt="{{lockPopupData.TeamNameShortLocal}}" class="img-fluid m-0" style="width: 58px;"> {{lockPopupData.TeamNameLocal}} Vs<img ng-src="{{lockPopupData.TeamFlagVisitor}}" alt="{{lockPopupData.TeamNameShortLocal}}" class="img-fluid m-0" style="width: 58px;"> {{lockPopupData.TeamNameVisitor}}</h5>
						<h5>Full Time : {{lockPopupData.selectedPrediction.TeamScoreLocalFT}} - {{lockPopupData.selectedPrediction.TeamScoreVisitorFT}} </h5>
						<h5>Half Time : {{lockPopupData.selectedPrediction.TeamScoreLocalHT}} - {{lockPopupData.selectedPrediction.TeamScoreVisitorHT}} </h5>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn_gray px-4" data-dismiss="modal" ng-click="closeSaveModal(lockPopupData)">Cancel</button>
						<button type="button" class="btn_primary px-4" ng-click="savePrediction(lockPopupData, lockPopupData.selectedPrediction)">Save</button>
					</div>
				</div>
			</div>
		</div> -->
	</div>
<?php include('footerHome.php') ?>
