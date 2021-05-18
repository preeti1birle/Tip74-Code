"use strict";
app.controller("predictionController", [
  "$scope",
  "$rootScope",
  "environment",
  "$localStorage",
  "$filter",
  "appDB",
  "$timeout",
  "$window",
  function ($scope, $rootScope, environment, $localStorage, $filter, appDB, $timeout, $window) {
    $scope.env = environment;
    $scope.data.pageSize = 20;
    $scope.data.pageNo = 1;
    $scope.coreLogic = Mobiweb.helpers;
    $scope.GamesType = $localStorage.GamesType;
    $scope.MatchGUID = getQueryStringValue("MatchGUID");
    if ($localStorage.hasOwnProperty("user_details") && $localStorage.isLoggedIn == true) {
      $scope.user_details = $localStorage.user_details;
     // function to get Competions
     $scope.CompetitionList = [];
     $scope.getCompetitionList = function() {
       var $data = {};
       $data.SessionKey = $scope.user_details.SessionKey;
       appDB.callPostForm($rootScope.apiPrefix + "football/getCompetitions", $data, contentType).then(function successCallback(data) {
         if ($scope.checkResponseCode(data)) {
           $scope.CompetitionList = data.Data.Records;
           $scope.CompetitionGUID = getQueryStringValue("CompetitionGUID")? getQueryStringValue("CompetitionGUID")
           : $scope.CompetitionList[0].CompetitionGUID;
           $scope.getLeagues($scope.CompetitionGUID, getQueryStringValue("LeagueGUID"));
         }
       }, function errorCallback(data) {
         $scope.checkResponseCode(data);
       });
     };
      /* Function to get all league */
      $scope.LeagueList = [];
      $scope.SelectedLeagueInfo = {};
      $scope.getLeagues = function (CompetitionGUID, customChange) {
        if(customChange == 'custom') {
          customChange = "";
        }
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.Params = "LeagueFlag,Status";
        $data.Filter = "CurrentSeasonLeagues";
        $data.CompetitionGUID = CompetitionGUID;
        appDB.callPostForm($rootScope.apiPrefix + "football/getLeagues", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $scope.LeagueList = data.Data.Records;
            $scope.LeagueGUID = customChange == getQueryStringValue("LeagueGUID")
              ? getQueryStringValue("LeagueGUID")
              : $scope.LeagueList[0].LeagueGUID;
            var index = $scope.LeagueList.map(e => {
              return e.LeagueGUID;
            }).indexOf($scope.LeagueGUID);
            $scope.SelectedLeagueInfo = $scope.LeagueList[index];
            $scope.getWeekList();
            $scope.getStanding();
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };

      $scope.goBack = function () {
        $localStorage.activeTab = getQueryStringValue("activeTab");
        $localStorage.WeekGUID = getQueryStringValue("WeekGUID");
        $localStorage.LeagueGUID = getQueryStringValue("LeagueGUID");
        $window.history.back();
      };

      $scope.getUserBalance = function (SelectedWeekGUID) {
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.WeekGUID = SelectedWeekGUID;
        appDB.callPostForm($rootScope.apiPrefix + "entries/getUserBalance", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $scope.UserEntriesBalance = data.Data;
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
     
      /**
             * get weeks list
             */
      $scope.getWeekList = function () {
        $scope.WeekStatus = false;
        var $data = {};
        $data.Params = "LeagueFlag,WeekStartDate,WeekEndDate,Status";
        $data.SessionKey = $scope.user_details.SessionKey;
        appDB.callPostForm($rootScope.apiPrefix + "football/getWeeks", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty("Records")) {
            $scope.WeekList = data.Data.Records;
            if (getQueryStringValue("WeekGUID")) {
              $scope.WeekGUID = getQueryStringValue("WeekGUID");
              var index = $scope.WeekList.map(e => {
                e.WeekStartDate = new Date($filter("convertIntoUserTimeZone")(e.WeekStartDate));
                e.WeekEndDate = new Date($filter("convertIntoUserTimeZone")(e.WeekEndDate));
                return e.WeekGUID;
              }).indexOf($scope.WeekGUID);
              $scope.WeekInfo = $scope.WeekList[index];
            } else {
              var index = $scope.WeekList.map(e => {
                e.WeekStartDate = new Date($filter("convertIntoUserTimeZone")(e.WeekStartDate));
                e.WeekEndDate = new Date($filter("convertIntoUserTimeZone")(e.WeekEndDate));
                return e.Status;
              }).indexOf("Running");
              $scope.WeekGUID = $scope.WeekList[index].WeekGUID;
              $scope.WeekInfo = $scope.WeekList[index];
              $scope.getUserBalance($scope.WeekGUID);
            }
            $scope.WeekStatus = true;
            $scope.getMatches();
            $timeout(function () {
              $(".round-slider").slick("slickGoTo", index);
            }, 1000);
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
      /**
             * get matches list date wise
             */
       $scope.MatchesList = [];
      $scope.getMatches = function () {
        $scope.MatchesList = [];
        var $data = {
          "Filters[0]": "DateWiseMatches"
        };
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.LeagueGUID = $scope.LeagueGUID;
        $data.WeekGUID = $scope.WeekGUID;
        $data.Params = "FullTimePredictionStatics,HalfTimePredictionStatics,IsPredicted,canPredict,PredictionDetails,MatchStartDateTime,MatchScoreDetails,TeamLastThreeMatchesVisitor,TeamLastThreeMatchesLocal,TeamGUIDLocal,TeamGUIDVisitor,LeagueName,LeagueFlag,MatchDate,MatchTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamColorLocal,TeamColorVisitor,TeamFlagLocal,TeamFlagVisitor,TeamStandingsLocal,TeamStandingsVisitor,VenueName,VenueAddress,VenueCity,VenueCapicity,VenueImage,Status,MatchDate";
        $data.OrderBy = "MatchStartDateTime";
        $data.Sequence = "ASC";
        $data.Status = $scope.activeTab == "Live"
          ? "Completed"
          : "Pending";
        $data.TimeZone = $scope.getTimeZone();
        appDB.callPostForm($rootScope.apiPrefix + "football/getMatches", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty("Records")) {
            $scope.MatchesList = data.Data.Records;
            $scope.MatchesList.forEach(e => {
              if (e.Matches.length > 0) {
                e.Matches.forEach(e1 => {
                  e1.MatchTime = e1.MatchTime.slice(0, -3);
                  if (e1.Status == "Completed" && e1.IsPredicted == "No") {
                    e1.PredictionAdded = false;
                  }
                   else {
                    e1.PredictionAdded = true;
                    if (e1.IsPredicted == "No") {
                      e1.PredictionDetails.TeamScoreLocalFT = "";
                      e1.PredictionDetails.TeamScoreVisitorFT = "";
                      e1.PredictionDetails.IsDoubleUps = false;
                      e1.PredictionDetails.TeamScoreLocalHT = "";
                      e1.PredictionDetails.TeamScoreVisitorHT = "";
                    } else {
                      e1.PredictionDetails.IsDoubleUps = e1.PredictionDetails.IsDoubleUps == "Yes"
                        ? true
                        : false;
                      e1.PredictionDetails.SavedDateTime = e1.PredictionDetails.SavedDateTime != ""
                        ? new Date($filter("convertIntoUserTimeZone")(e1.PredictionDetails.SavedDateTime))
                        : "";
                      e1.PredictionDetails.LockedDateTime = e1.PredictionDetails.LockedDateTime != ""
                        ? new Date($filter("convertIntoUserTimeZone")(e1.PredictionDetails.LockedDateTime))
                        : "";
                    }
                  }
                });
              }
            });
            $timeout(function () {
              $('[data-toggle="tooltip"]').tooltip();
            }, 1000);
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
      /**
             * change league
             */
      $scope.changeLeague = function () {
        var index = $scope.LeagueList.map(e => {
          return e.LeagueGUID;
        }).indexOf($scope.LeagueGUID);
        $scope.SelectedLeagueInfo = $scope.LeagueList[index];
        $scope.getMatches();
        if ($scope.getPageName() == "prediction") {
          $scope.getStanding();
        }
      };
      /**
             * change week
             */
      $scope.changeWeek = function (WeekGUID) {
        $scope.WeekGUID = WeekGUID;
        let index = $scope.WeekList.map(e => {
          return e.WeekGUID;
        }).indexOf(WeekGUID);
        $scope.WeekInfo = $scope.WeekList[index];
        $scope.getMatches();
        $scope.getUserBalance($scope.WeekGUID);
      };
      /**
             * get match standing
             */
      $scope.getStanding = function () {
        $scope.MatchStanding = [];
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.LeagueGUID = $scope.LeagueGUID;
        $data.Params = "TeamNameShort,TeamFlag,TeamStandings,TeamColor";
        $data.PageNo = 1;
        $data.PageSize = 20;
        $data.OrderBy = "TeamPosition";
        $data.Sequence = "ASC";
        appDB.callPostForm($rootScope.apiPrefix + "football/getTeams", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty("Records")) {
            $scope.MatchStanding = data.Data.Records;
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
      /**
             * get team historic result
             */
      $scope.getTeamHistoricalResult = function (TeamGUIDLocal, TeamGUIDVisitor) {
        $scope.TeamHistoricalResult = [];
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.TeamGUIDVisitor = TeamGUIDVisitor;
        $data.TeamGUIDLocal = TeamGUIDLocal;
        appDB.callPostForm($rootScope.apiPrefix + "football/getTeamsHistoricResults", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $scope.TeamHistoricalResult = data.Data;
            $scope.TimeDuration = [];
            if ($scope.TeamHistoricalResult.Matches.length > 0) {
              $scope.TeamHistoricalResult.Matches.Records.forEach(e => {
                e.NewMatchDate = new Date($filter("convertIntoUserTimeZone")(e.MatchDate + " " + e.MatchTime));
                $scope.TimeDuration.push(new Date(e.MatchDate).getFullYear());
                e.localTeamChanges = false;
                if (e.TeamNameLocal != $scope.TeamHistoricalResult.TeamDetailsLocal.TeamName) {
                  e.localTeamChanges = true;
                }
                e.MatchStatus = "";
                e.WinningTeamName = "";
                e.WinningTeamColor = "";
                if (e.MatchScoreDetails.LocalTeamScore == e.MatchScoreDetails.VisitorTeamScore) {
                  e.MatchStatus = "Draw";
                } else if (e.MatchScoreDetails.LocalTeamScore > e.MatchScoreDetails.VisitorTeamScore && e.localTeamChanges == true) {
                  e.MatchStatus = "Win";
                  e.WinningTeamName = e.TeamNameVisitor;
                } else if (e.MatchScoreDetails.LocalTeamScore > e.MatchScoreDetails.VisitorTeamScore && !e.localTeamChanges) {
                  e.MatchStatus = "Win";
                  e.WinningTeamName = e.TeamNameLocal;
                } else if (e.MatchScoreDetails.VisitorTeamScore > e.MatchScoreDetails.LocalTeamScore && e.localTeamChanges) {
                  e.MatchStatus = "Win";
                  e.WinningTeamName = e.TeamNameLocal;
                } else if (e.MatchScoreDetails.VisitorTeamScore > e.MatchScoreDetails.LocalTeamScore && !e.localTeamChanges) {
                  e.MatchStatus = "Win";
                  e.WinningTeamName = e.TeamNameVisitor;
                }
                if (e.WinningTeamName == $scope.TeamHistoricalResult.TeamDetailsLocal.TeamName) {
                  e.WinningTeamColor = $scope.TeamHistoricalResult.TeamDetailsLocal.TeamColor;
                } else if (e.WinningTeamName == $scope.TeamHistoricalResult.TeamDetailsVisitor.TeamName) {
                  e.WinningTeamColor = $scope.TeamHistoricalResult.TeamDetailsVisitor.TeamColor;
                }
              });
            }
            $scope.TimeDuration = $filter("orderBy")($scope.TimeDuration);
            $scope.openPopup("HistoricModal");
            $(function () {
              $('[data-toggle="tooltip"]').tooltip();
            });
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
      /**
             * get full standing
             */
      $scope.getFullStanding = function () {
        if (getQueryStringValue("LeagueGUID") == "") {
          window.location.href = base_url + "dashboard";
        }
        $scope.MatchFullStanding = [];
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.LeagueGUID = getQueryStringValue("LeagueGUID");
        $data.Params = "TeamNameShort,TeamFlag,TeamStandings,TeamColor";
        $data.OrderBy = "TeamPosition";
        $data.Sequence = "ASC";
        appDB.callPostForm($rootScope.apiPrefix + "football/getTeams", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty("Records")) {
            $scope.MatchFullStanding = data.Data.Records;
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
      /**
             * get signle league details
             */
      $scope.getLeague = function () {
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.Params = "LeagueFlag,Status";
        $data.Filter = "CurrentSeasonLeagues";
        $data.LeagueGUID = getQueryStringValue("LeagueGUID");
        appDB.callPostForm($rootScope.apiPrefix + "football/getLeagues", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $scope.League = data.Data;
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
      /**
             * open Team modal
             */
      $scope.teamModal = function (MatchInfo, TeamGUID, Team) {
        $rootScope.TeamMatchInfo = MatchInfo;
        $rootScope.ShowingTeam = Team;
        $rootScope.TeamMatchList = [];
        var $data = {
          "Filters[0]": "TeamCompletedMatches"
        };
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.TeamGUID = TeamGUID;
        $data.Params = "MatchStartDateTime,HomeAway,ResultStatus,MatchDate,MatchTime,OpponentTeamName,MatchScoreDetails,canPredict";
        $data.OrderBy = "MatchStartDateTime";
        $data.Sequence = "DESC";
        $data.Status = "Completed";
        $data.TimeZone = $scope.getTimeZone();
        appDB.callPostForm($rootScope.apiPrefix + "football/getMatches", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $rootScope.TeamMatchList = data.Data.Records;
            // $rootScope.TeamMatchList.forEach(e => {
            //   e.MatchStartDateTime = new Date($filter("convertIntoUserTimeZone")(e.MatchStartDateTime));
            // });
            if (Team == "local") {
              $rootScope.TeamMatchInfo.WiningPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandingsLocal.Overall.Won / $rootScope.TeamMatchInfo.TeamStandingsLocal.Overall.GamePlayed) * 100);
              $rootScope.TeamMatchInfo.DrawPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandingsLocal.Overall.Draw / $rootScope.TeamMatchInfo.TeamStandingsLocal.Overall.GamePlayed) * 100);
              $rootScope.TeamMatchInfo.LostPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandingsLocal.Overall.Lost / $rootScope.TeamMatchInfo.TeamStandingsLocal.Overall.GamePlayed) * 100);
            } else if (Team == "visitor") {
              $rootScope.TeamMatchInfo.WiningPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandingsVisitor.Overall.Won / $rootScope.TeamMatchInfo.TeamStandingsVisitor.Overall.GamePlayed) * 100);
              $rootScope.TeamMatchInfo.DrawPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandingsVisitor.Overall.Draw / $rootScope.TeamMatchInfo.TeamStandingsVisitor.Overall.GamePlayed) * 100);
              $rootScope.TeamMatchInfo.LostPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandingsVisitor.Overall.Lost / $rootScope.TeamMatchInfo.TeamStandingsVisitor.Overall.GamePlayed) * 100);
            } else {
              $rootScope.TeamMatchInfo.WiningPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandings.Overall.Won / $rootScope.TeamMatchInfo.TeamStandings.Overall.GamePlayed) * 100);
              $rootScope.TeamMatchInfo.DrawPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandings.Overall.Draw / $rootScope.TeamMatchInfo.TeamStandings.Overall.GamePlayed) * 100);
              $rootScope.TeamMatchInfo.LostPercent = Math.round(($rootScope.TeamMatchInfo.TeamStandings.Overall.Lost / $rootScope.TeamMatchInfo.TeamStandings.Overall.GamePlayed) * 100);
            }
            $scope.openPopup("TeamModal");
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };


      $scope.updatePrediction = function(MatchInfo, ev, type) {
        if($scope.UserEntriesBalance.AllowedPurchaseDoubleUps == $scope.UserEntriesBalance.ConsumeDoubleUps) {
          ev.target.checked = false;
          $scope.openPopup("doubleupPopup");
        } else {
          if(type == 'doubleup'){
            $scope.NewPrediction = {
              IsDoubleUps:( ev.target.checked == true)?"Yes":"No"
            }
          } else {
            $scope.NewPrediction = {
              MatchPredictionID: MatchInfo.PredictionDetails[index].MatchPredictionID,
              TeamScoreLocalFT: MatchInfo.PredictionDetails[index].TeamScoreLocalFT,
              TeamScoreLocalHT: MatchInfo.PredictionDetails[index].TeamScoreLocalHT,
              TeamScoreVisitorFT: MatchInfo.PredictionDetails[index].TeamScoreVisitorFT,
              TeamScoreVisitorHT: MatchInfo.PredictionDetails[index].TeamScoreVisitorHT,
              IsDoubleUps:(MatchInfo.PredictionDetails[index].IsDoubleUps === "Yes")?"Yes":"No"
            }
          }
          $scope.lockPopupData.selectedPrediction['IsDoubleUps'] = $scope.NewPrediction.IsDoubleUps
          console.log($scope.lockPopupData.selectedPrediction);
          $scope.IsDoubleUps = $scope.IsDoubleUps;
          // $scope.savePrediction(MatchInfo, $scope.NewPrediction);
        }
      }
      /**
         * save match prediction
      */
      $scope.disableLockButton = false;
      $scope.savePrediction = function (MatchInfo, NewPrediction) {
        $scope.MatchGUID = MatchInfo.MatchGUID;

        $scope.disableLockButton = true;
        if (MatchInfo.Status != "Pending") {
          return false;
        }

        if (!$scope.checkPredictionValidation(MatchInfo,NewPrediction)) {
          return false;
        }

        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.MatchGUID = MatchInfo.MatchGUID;
        $data.PredictionStatus = "Save";
        $data.MatchPredictionID = (NewPrediction.MatchPredictionID != "") ? NewPrediction.MatchPredictionID : "";
        $data.TeamScoreLocalFT = NewPrediction.TeamScoreLocalFT;
        $data.TeamScoreVisitorFT = NewPrediction.TeamScoreVisitorFT;
        $data.TeamScoreLocalHT = NewPrediction.TeamScoreLocalHT;
        $data.TeamScoreVisitorHT = NewPrediction.TeamScoreVisitorHT;
        $data.IsDoubleUps = NewPrediction.IsDoubleUps === "Yes"
          ? "Yes"
          : "No";
        $data.WeekGUID = $scope.WeekGUID;
        appDB.callPostForm($rootScope.apiPrefix + "football/matchPrediction", $data, contentType).then(function successCallback(data) {
          if (data.ResponseCode == 200) {
            $scope.disableLockButton = true;
            // MatchInfo.NewPrediction.PredictionStatus = "Save";
            $scope.NewPrediction = {
              MatchPredictionID:"",
              TeamScoreLocalFT: "",
              TeamScoreLocalHT: "",
              TeamScoreVisitorFT: "",
              TeamScoreVisitorHT: "",
              IsDoubleUps:""
            };
            // MatchInfo.NewPrediction.SavedDateTime = new Date($filter("convertIntoUserTimeZone")(data.Data.PredictionDetails.SavedDateTime));
            //console.log("MatchInfo.NewPrediction.SavedDateTime",MatchInfo.NewPrediction.SavedDateTime)
            // $scope.getEntriesUpdate($scope.WeekGUID);
            // $scope.getMatches();
            // $scope.getUserBalance($scope.WeekGUID);

            // update match list instead of calling API and update page
            let index1  = $scope.MatchesList.map(e => {
              return e.MatchDate;
            }).indexOf(MatchInfo.MatchDate);
            let index = $scope.MatchesList[index1].Matches.map(e => {
              return e.MatchGUID;
            }).indexOf(MatchInfo.MatchGUID);
            $scope.MatchesList[index1].Matches[index].PredictionDetails = data.Data.PredictionDetails;
            
            MatchInfo.selectedPrediction = data.Data.PredictionDetails[data.Data.PredictionDetails.length-1]

            $scope.lockPickPrediction(MatchInfo)
          } else if (data.ResponseCode == 402) {
            $scope.MatchGUID = MatchInfo.MatchGUID;
            $scope.disableLockButton = false;
            $scope.getEntryList();
            if(data.Data.BalanceType == "Prediction") {
              // $scope.openPopup("redirectEntryPopup");
              $scope.closePopup("LockModal");
              $scope.NewPrediction = {
                MatchPredictionID:"",
                TeamScoreLocalFT: "",
                TeamScoreLocalHT: "",
                TeamScoreVisitorFT: "",
                TeamScoreVisitorHT: "",
                IsDoubleUps:""
              };              
              $scope.lockPopupData = "";
              $scope.openPopup("LockPredictionErrorModal");

              // window.location.href = base_url + "myEntries";

            } else if(data.Data.BalanceType == "DoubleUps") {
              $scope.openPopup("doubleupPopup");
            }              
          }
        }, function errorCallback(data) {
          if (data.ResponseCode == 402) {
            $scope.UserEntriesBalance.ConsumedPredictions = 10;
            $scope.UserEntriesBalance.AllowedPredictions = 10;
            document.getElementsByClassName('stickybtn')[0].style.display = 'inline-block';
            document.getElementById('stickybtn').classList.add('fixed');
            $scope.MatchGUID = MatchInfo.MatchGUID;
            $scope.getUserBalance($scope.WeekGUID);
            $scope.disableLockButton = false;
            $scope.getEntryList();
            if(data.Data.BalanceType == "Prediction") {
              // $scope.openPopup("redirectEntryPopup");
              $scope.closePopup("LockModal");
              $scope.NewPrediction = {
                MatchPredictionID:"",
                TeamScoreLocalFT: "",
                TeamScoreLocalHT: "",
                TeamScoreVisitorFT: "",
                TeamScoreVisitorHT: "",
                IsDoubleUps:""
              };              
              $scope.lockPopupData = "";
              $scope.MatchGUID = MatchInfo.MatchGUID;
              $scope.openPopup("LockPredictionErrorModal");

              // window.location.href = base_url + "myEntries";
            } else if(data.Data.BalanceType == "DoubleUps") {
              $scope.openPopup("doubleupPopup");
            }
          } else {
            $scope.checkResponseCode(data);
            $scope.disableLockButton = false;
          }
        });
      };
      /**
             * check prediction validation
             */
      $scope.checkPredictionValidation = function (MatchInfo, NewPrediction) {
        /*if (MatchInfo.PredictionDetails.TeamScoreLocalFT.length == 0 || MatchInfo.PredictionDetails.TeamScoreVisitorFT.length == 0) {
          return false;
        } else if (MatchInfo.PredictionDetails.TeamScoreLocalFT > 12 || MatchInfo.PredictionDetails.TeamScoreVisitorFT > 12) {
          $scope.errorMessageShow("Please choose 12 or fewer goals for each team.");
          return false;
        } else if (MatchInfo.PredictionDetails.TeamScoreLocalHT.length == 0 || MatchInfo.PredictionDetails.TeamScoreVisitorHT.length == 0) {
          return false;
        } else if (MatchInfo.PredictionDetails.TeamScoreLocalHT > 12 || MatchInfo.PredictionDetails.TeamScoreVisitorHT > 12) {
          $scope.errorMessageShow("Please choose 12 or fewer goals for each team.");
          return false;
        } else {
          return true;
        }*/

        if (NewPrediction.TeamScoreLocalFT.length == 0 || NewPrediction.TeamScoreVisitorFT.length == 0) {
          console.log("LENGTH")
          return false;
        } else if (NewPrediction.TeamScoreLocalFT > 12 || NewPrediction.TeamScoreVisitorFT > 12) {
          console.log("TeamScoreLocalFT")
          $scope.errorMessageShow("Please choose 12 or fewer goals for each team.");
          return false;
        } else if (NewPrediction.TeamScoreLocalHT.length == 0 || NewPrediction.TeamScoreVisitorHT.length == 0) {
          console.log("NewPrediction.TeamScoreLocalHT.length == 0")
          return false;
        } else if (NewPrediction.TeamScoreLocalHT > 12 || NewPrediction.TeamScoreVisitorHT > 12) {
          console.log("NewPrediction.TeamScoreLocalHT")
          $scope.errorMessageShow("Please choose 12 or fewer goals for each team.");
          return false;
        } /*else if (NewPrediction.TeamScoreLocalFT < NewPrediction.TeamScoreLocalHT ) {
          $scope.errorMessageShow("Home Team full time prediction should be greater than or equals to half time prediction.");
          return false;
        } else if (NewPrediction.TeamScoreVisitorFT < NewPrediction.TeamScoreVisitorHT ) {
          $scope.errorMessageShow("Away Team full time prediction should be greater than or equals to half time prediction.");
          return false;
        }*/ else {
          return true;
        }
      };
      /**
             * lock pick
             */
      $scope.lockPickPrediction = function (MatchInfo) {
        // if (!$scope.checkPredictionValidation(MatchInfo)) {
        //   return false;
        // }
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.MatchGUID = MatchInfo.MatchGUID;
        $data.MatchPredictionID = MatchInfo.selectedPrediction.MatchPredictionID;
        $data.PredictionStatus = "Lock";
        $data.TeamScoreLocalFT = MatchInfo.selectedPrediction.TeamScoreLocalFT;
        $data.TeamScoreVisitorFT = MatchInfo.selectedPrediction.TeamScoreVisitorFT;
        $data.TeamScoreLocalHT = MatchInfo.selectedPrediction.TeamScoreLocalHT;
        $data.TeamScoreVisitorHT = MatchInfo.selectedPrediction.TeamScoreVisitorHT;
        $data.IsDoubleUps = MatchInfo.selectedPrediction.IsDoubleUps == "Yes"
          ? "Yes"
          : "No";
        $data.WeekGUID = $scope.WeekGUID;

        appDB.callPostForm($rootScope.apiPrefix + "football/matchPrediction", $data, contentType).then(function successCallback(data) {
          if (data.ResponseCode == 200) {
            $scope.NewPrediction = {
              MatchPredictionID:"",
              TeamScoreLocalFT: "",
              TeamScoreLocalHT: "",
              TeamScoreVisitorFT: "",
              TeamScoreVisitorHT: ""
            };

            $scope.closePopup("LockModal");
            $scope.disableLockButton = false;
            $rootScope.predictionMessage = data.Message;
            MatchInfo.PredictionDetails.PredictionStatus = "Lock";
            MatchInfo.PredictionDetails.LockedDateTime = new Date($filter("convertIntoUserTimeZone")(data.Data.PredictionDetails.LockedDateTime));
            MatchInfo.FullTimePredictionStatics = data.Data.FullTimePredictionStatics;
            MatchInfo.HalfTimePredictionStatics = data.Data.HalfTimePredictionStatics;
            // $scope.getEntriesUpdate($scope.WeekGUID);
            $scope.getUserBalance($scope.WeekGUID);
            $scope.openPopup("matchPredictionPopup");
            // $scope.getMatches();
            let index1  = $scope.MatchesList.map(e => {
              return e.MatchDate;
            }).indexOf(MatchInfo.MatchDate);
            let index = $scope.MatchesList[index1].Matches.map(e => {
              return e.MatchGUID;
            }).indexOf(MatchInfo.MatchGUID);
            $scope.MatchesList[index1].Matches[index].PredictionDetails = data.Data.PredictionDetails;

          } else if (data.ResponseCode == 402) {
            $scope.disableLockButton = false;
            $scope.getEntryList();
            if(data.Data.BalanceType == "Prediction") {
              // $scope.openPopup("redirectEntryPopup");
              // window.location.href = base_url + "myEntries";
            } else if(data.Data.BalanceType == "DoubleUps") {
              $scope.openPopup("doubleupPopup");
            }              }
        }, function errorCallback(data) {
          $scope.disableLockButton = false;
          if (data.ResponseCode == 402) {
            $scope.getEntryList();
            if(data.Data.BalanceType == "Prediction") {
              // $scope.openPopup("redirectEntryPopup");
              // window.location.href = base_url + "myEntries";
            } else if(data.Data.BalanceType == "DoubleUps") {
              $scope.openPopup("doubleupPopup");
            }          
          } else {
            $scope.checkResponseCode(data);
          }
        });
      };
      /**
             * open lock popup
             */
      $scope.openLockModal = function (MatchInfo, index) {
        $scope.lockPopupData = MatchInfo;
        $scope.MatchGUID = MatchInfo.MatchGUID;
        $scope.lockPopupData.selectedPrediction = MatchInfo.PredictionDetails[index];
        if($scope.UserEntriesBalance.ConsumedPredictions >= 10) {
   
          $scope.openPopup("LockPredictionErrorModal");
          // $scope.checkResponseCode(data);
        } else {
          $scope.openPopup("LockModal");
        }

      };

      $scope.NewPrediction = {
        MatchPredictionID:"",
        TeamScoreLocalFT: "",
        TeamScoreLocalHT: "",
        TeamScoreVisitorFT: "",
        TeamScoreVisitorHT: ""
      };
      /**
             * select value
      */
      $scope.selectValue = function (match, TeamType, PredictionType, value, NewPrediction) {
        if (match.Status != "Pending") {
          return false;
        }

        if (PredictionType == "Full") {
          if (TeamType == "local") {
            // match.PredictionDetails[index].TeamScoreLocalFT = value;
            NewPrediction.TeamScoreLocalFT = value;
          } else if (TeamType == "visitor") {
            // match.PredictionDetails[index].TeamScoreVisitorFT = value;
            NewPrediction.TeamScoreVisitorFT = value;
          }
          // $scope.savePrediction(match,NewPrediction);
        } else {
          if (TeamType == "local") {
            // match.PredictionDetails[index].TeamScoreLocalHT = value;
            NewPrediction.TeamScoreLocalHT = value;
          } else if (TeamType == "visitor") {
            // match.PredictionDetails[index].TeamScoreVisitorHT = value;
            NewPrediction.TeamScoreVisitorHT = value;
            // $scope.NewPrediction = NewPrediction;
            // $scope.match = match;
          }
          
          // $scope.savePrediction(match,NewPrediction);
        }
        if(NewPrediction.TeamScoreLocalFT != "" && NewPrediction.TeamScoreVisitorFT !=""
          && NewPrediction.TeamScoreLocalHT!= "" && NewPrediction.TeamScoreVisitorHT !="") {
            $scope.lockPopupData = match;
            $scope.lockPopupData.selectedPrediction = NewPrediction;
            $scope.IsDoubleUps = 'No';
            $scope.disableLockButton = false;
            $scope.NewPrediction = {
              MatchPredictionID:"",
              TeamScoreLocalFT: "",
              TeamScoreLocalHT: "",
              TeamScoreVisitorFT: "",
              TeamScoreVisitorHT: ""
            };
            $scope.openPopup("LockModal");
          }
      };

      $scope.closeSaveModal= function(matchInfo) {
        $scope.NewPrediction = {
          MatchPredictionID:"",
          TeamScoreLocalFT: "",
          TeamScoreLocalHT: "",
          TeamScoreVisitorFT: "",
          TeamScoreVisitorHT: "",
          IsDoubleUps:""
        };
        $scope.lockPopupData = "";
      };

      /**
             * get Match line-up
             */
      $scope.getMatchTeamLineUp = function (Match) {
        $rootScope.LineUpMatchInfo = Match;
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.MatchGUID = Match.MatchGUID; //da672cac-c8fd-81e0-6fff-d5ae17698c1d
        appDB.callPostForm($rootScope.apiPrefix + "football/getTeamLineup", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $rootScope.TeamLineup = data.Data;
            $scope.openPopup("LineUpModal");
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
    } else {
      window.location.href = base_url;
    }
  }
]);

app.directive('scrollIf', function () {
  return function (scope, element, attributes) {
    setTimeout(function () {
      if (scope.$eval(attributes.scrollIf)) {
        window.scrollTo(0, element[0].offsetTop - 10);
      }
    });
  }
});

app.directive("slickWeekCustomCarousel", [
  "$timeout",
  function ($timeout) {
    return {
      restrict: "A",
      link: {
        post: function (scope, elem, attr) {
          $timeout(function () {
            $(".round-slider").slick({
              slidesToShow: 9,
              slidesToScroll: 2,
              autoplay: false,
              dots: false,
              speed: 1000,
              responsive: [
                {
                  breakpoint: 992,
                  settings: {
                    slidesToShow: 6,
                    slidesToScroll: 1
                  }
                }, {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1
                  }
                }, {
                  breakpoint: 576,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                  }
                }, {
                  breakpoint: 360,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                  }
                }
              ]
            });
          }, 1);
        }
      }
    };
  }
]);
