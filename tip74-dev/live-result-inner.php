<?php include('header.php') ?>

<div class="main-content" ng-controller="liveResultController" ng-cloak ng-init="getMatches()">
    <div class="dashboard live-result-inner">
        <div class="container">
            <div class="row align-items-center mb-4">
                <div class="col-md-12">
                    <ul class="top-bar">
                        <li>
                            <span>Entries : {{UserEntriesBalance.PurchasedEntries}}</span>
                            <span>|</span>
                            <span class="themeClr"> {{moneyFormat(profileDetails.WalletAmount)}}</span>
                        </li>
                        <li>
                            <span><span
                                    class="themeClr">{{UserEntriesBalance.ConsumedPredictions}}</span>/{{UserEntriesBalance.AllowedPredictions}}
                                Prediction</span>
                        </li>
                        <li>
                            <span><span
                                    class="themeClr">{{UserEntriesBalance.ConsumeDoubleUps}}</span>/{{UserEntriesBalance.TotalPurchasedDoubleUps}}
                                Double Up</span>
                        </li>
                        <button class="btn bg-gradient back-btn-position" ng-click="goBack()">Go Back</button>
                    </ul>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="bg-gradient top-head">
                        <div class="row w-100">
                            <h6 class="col-sm-5">{{MatchInfo.LeagueName}}</h6>
                            <div class="col-sm-7 px-0 right-site">
                                <a class="javascript:void(0)" ng-click="getMatchTeamLineUp(MatchInfo)"><i
                                        class="fa fa-user pr-2"></i>Team Lineups</a>
                                <!-- <a class="javascript:void(0)"><i class="fa fa-user pr-2"></i>Performance</a>
									<a class="javascript:void(0)"><img src="assets/img/info.png" alt="" class="pr-2">Info</a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-12">
						<div class="setting-bar">
							<div class="row align-items-center">
								<div class=" col-lg-7 col-xl-6 d-flex">
									<select id="inputState" class="form-control">
										<option selected>Vivacious giraffes 17/18</option>
										<option>option2</option>
										<option>option3</option>
										<option>option4</option>
										<option>option5</option>
									</select>
									<select id="inputState" class="form-control ml-3">
										<option selected>Bayern Munich Fans 258/2,860</option>
										<option>option2</option>
										<option>option3</option>
										<option>option4</option>
										<option>option5</option>
									</select>
								</div>
								<div class="col-lg-4 col-xl-5 pl-lg-0 mt-2 mt-lg-0 col-10 ">
									<ul class="pools">
										<li>
											<span>Add a Pool</span>
											<a href="javascript:void(0)" class="addPool">+</a>
										</li>
									</ul>
								</div>
								<div class="col-lg-1 pl-0 mt-2 mt-lg-0 text-right col-2">
									<a href="javascript:void(0)"><i class="fa fa-cog"></i></a>
								</div>
							</div>
						</div>
					</div> -->
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="matchStatus">
                        <div class="row align-items-center">
                            <div class="col-12 show-status">
                                <h6><b>MATCH COMPLETED</b></h6>
                            </div>
                            <div class="col-4">
                                <img ng-src="{{MatchInfo.TeamFlagLocal}}" alt="{{MatchInfo.TeamNameShortLocal}}"
                                    class="img-fluid m-0">
                                <p class="mb-0">{{MatchInfo.TeamNameLocal}}</p>
                                <!-- <p>44%</p>	 -->
                            </div>
                            <div class="col-4 d-flex justify-content-between liveCenterResult">
                                <h2 class="mb-0">{{MatchInfo.MatchScoreDetails.LocalTeamScore}}</h2>
                                <div>
                                    <!-- <p>12 Jul 02:35</p> -->
                                    <span ng-if="MatchInfo.IsPredicted == 'No'">No pick</span>
                                </div>
                                <h2 class="mb-0">{{MatchInfo.MatchScoreDetails.VisitorTeamScore}}</h2>

                            </div>
                            <div class="col-4">
                                <img ng-src="{{MatchInfo.TeamFlagVisitor}}" alt="{{MatchInfo.TeamNameShortVisitor}}"
                                    class="img-fluid m-0">
                                <p class="mb-0">{{MatchInfo.TeamNameVisitor}}</p>
                                <!-- <p>19%</p> -->
                            </div>
                        </div>

                        <div class="row align-items-center" ng-if="MatchInfo.IsPredicted == 'Yes'">
                            <div class="col-sm-4 col-6 pr-1 pr-sm-3 order-1 order-sm-1">
                                <div class="timeLimit"
                                    ng-if="MatchInfo.PredictionDetails.hasOwnProperty('PredictionStatus')">
                                    <div class="fullTime">
                                        <p>FULL TIME</p>
                                        <div class="d-flex">
                                            <form><input type="text" readonly
                                                    value="{{MatchInfo.PredictionDetails.TeamScoreLocalFT}}"></form>
                                            <span>-</span>
                                            <form><input type="text" readonly
                                                    value="{{MatchInfo.PredictionDetails.TeamScoreVisitorFT}}"></form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 order-3 order-sm-2 timeLimit">
                                <div class="checkBox" ng-if="MatchInfo.IsDoubleUps">
                                    <input type="checkbox" ng-model="MatchInfo.IsDoubleUps" class="styled-checkbox mt-5"
                                        id="styled-checkbox">
                                    <label> Double Up </label>
                                </div>
                                <p class="lockedTime" ng-if="MatchInfo.PredictionDetails.PredictionStatus == 'Lock'">
                                    Locked {{MatchInfo.PredictionDetails.LockedDateTime | date :'dd MMM HH:mm'}}</p>
                                <p class="lockedTime" ng-if="MatchInfo.PredictionDetails.PredictionStatus == 'Save'">You
                                    didn't locked prediction</p>
                            </div>
                            <div class="col-sm-4 col-6 pl-1 pl-sm-3 order-2 order-sm-3">
                                <div class="timeLimit"
                                    ng-if="MatchInfo.PredictionDetails.hasOwnProperty('PredictionStatus')">
                                    <div class="fullTime">
                                        <p>HALF TIME</p>
                                        <div class="d-flex">
                                            <form><input type="text" readonly
                                                    value="{{MatchInfo.PredictionDetails.TeamScoreLocalHT}}"></form>
                                            <span>-</span>
                                            <form><input type="text" readonly
                                                    value="{{MatchInfo.PredictionDetails.TeamScoreVisitorHT}}"></form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="creditedScore table-responsive mt-3">
                        <h5 style="color: var(--primaryClr);text-align: center;font-weight: 600;">Leaderboard</h5>
                        <table class="table leaderboard_table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
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
                                    <td>
                                        <img ng-src="{{player.ProfilePic}}" class="mr-2">{{player.FirstName}}
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
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footerHome.php') ?>