"use strict";
app.controller("dashboardController", [
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
    if ($localStorage.hasOwnProperty("user_details") && $localStorage.isLoggedIn == true) {
      $scope.user_details = $localStorage.user_details;
      /* To manage Tabs */
      $scope.activeTab = $localStorage.activeTab
        ? $localStorage.activeTab
        : "Pending";
      $scope.gotoTab = function (tab) {
        $scope.activeTab = tab;
        $localStorage.WeekGUID = "";
        $localStorage.activeTab = "";
        $scope.getWeekList();
        // $scope.getMatches();
      };


      //function to calculate no. of prediction
      $scope.getSavedPredictions= function(details) {
        let newArray = details.filter((e) => {
          return e.PredictionStatus == 'Lock'
        });
        return (newArray)
      }

      //function to calculate no. of prediction
      $scope.getLockedPredictions= function(details) {
        let newArray1 = details.filter((e) => {
          return e.PredictionStatus != 'Lock'
        });
        return (newArray1)
      }

      // function to get Competions
      $scope.CompetitionList = [];
      $scope.getCompetitionList = function() {
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        appDB.callPostForm($rootScope.apiPrefix + "football/getCompetitions", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $scope.CompetitionList = data.Data.Records;
            $scope.CompetitionGUID = $scope.CompetitionList[0].CompetitionGUID;
            $scope.getLeagues($scope.CompetitionGUID);
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };

      /* Function to get all league on selected competition*/
      $scope.LeagueList = [];
      $scope.SelectedLeagueInfo = {};
      $scope.getLeagues = function (CompetitionGUID) {
        var $data = {};
        $data.SessionKey = $scope.user_details.SessionKey;
        $data.CompetitionGUID = CompetitionGUID;
        $data.Params = "LeagueFlag,Status";
        $data.Filter = "CurrentSeasonLeagues";
        appDB.callPostForm($rootScope.apiPrefix + "football/getLeagues", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data)) {
            $scope.LeagueList = data.Data.Records;
            $scope.LeagueGUID = $localStorage.LeagueGUID ? $localStorage.LeagueGUID
              : $scope.LeagueList[0].LeagueGUID;
            var index = $scope.LeagueList.map(e => {
              return e.LeagueGUID;
            }).indexOf($scope.LeagueGUID);
            $scope.SelectedLeagueInfo = $scope.LeagueList[index];
            $scope.getWeekList();
            if ($scope.getPageName() == "prediction") {
              $scope.getStanding();
            }
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
        $data.Params = "LeagueFlag,WeekStartDate,WeekEndDate,Status,WeekCount";
        $data.SessionKey = $scope.user_details.SessionKey;
        if ($scope.activeTab == "Pending") {
          //   $data.Status = "Pending";
          $data.UpcomingWeekStatus = "Pending";
        } else if ($scope.activeTab == "Live") {
          $data.Status = "Running";
        } else {
          $data.UpcomingWeekStatus = "Completed";
        }
        appDB.callPostForm($rootScope.apiPrefix + "football/getWeeks", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty("Records")) {
            $scope.WeekList = data.Data.Records;
            let LiveWeek = "Running";
            if ($scope.activeTab == "Pending" || $scope.activeTab == "Live") {
              LiveWeek = "Running";
            }
            var index = $scope.WeekList.map(e => {
              e.WeekStartDate = new Date($filter("convertIntoUserTimeZone")(e.WeekStartDate));
              e.WeekEndDate = new Date($filter("convertIntoUserTimeZone")(e.WeekEndDate));
              return e.Status;
            }).lastIndexOf(LiveWeek);
            if (index != -1) {
              $scope.WeekGUID = $localStorage.WeekGUID
                ? $localStorage.WeekGUID
                : $scope.WeekList[index].WeekGUID;
              $scope.WeekInfo = $scope.WeekList[index];
              $scope.getUserBalance($scope.WeekGUID);
            }
            $scope.WeekStatus = true;
            $scope.getMatches();
            if (index > 9) {
              $(".round-slider").slick("slickGoTo", index);
              $timeout(function () {}, 1000);
            }
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
        $data.Params = "IsPredicted,PredictionDetails,MatchStartDateTime,MatchScoreDetails,TeamGUIDLocal,TeamGUIDVisitor,LeagueName,LeagueFlag,MatchDate,MatchTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,Status";
        $data.OrderBy = "MatchStartDateTime";
        $data.Sequence = "ASC";
        // $data.Status = ($scope.activeTab == 'Live') ? 'Completed' : 'Pending';
        $data.Status = $scope.activeTab == "Live"
          ? "Running"
          : $scope.activeTab == "Pending"
            ? "Pending"
            : "Completed";
        $data.TimeZone = $scope.getTimeZone();
        appDB.callPostForm($rootScope.apiPrefix + "football/getMatches", $data, contentType).then(function successCallback(data) {
          if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty("Records")) {
            $scope.MatchesList = data.Data.Records;
            $scope.MatchesList.forEach(e => {
              e.Matches.forEach(e1 => {
                e1.MatchTime = e1.MatchTime.slice(0, -3);
              });
            });
          }
        }, function errorCallback(data) {
          $scope.checkResponseCode(data);
        });
      };
      /**
             * change league
             */
      $scope.changeLeague = function () {
        $localStorage.LeagueGUID = "";
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
    } else {
      window.location.href = base_url;
    }
  }
]);
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
