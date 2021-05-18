'use strict';
app.controller('leaderboardController', ['$scope', '$rootScope', 'environment', '$localStorage', '$filter', 'appDB', '$timeout', '$window',function ($scope, $rootScope, environment, $localStorage, $filter, appDB, $timeout, $window) {
    $scope.env = environment;
    $scope.data.pageSize = 20;
    $scope.data.pageNo = 1;
    $scope.coreLogic = Mobiweb.helpers;
    $scope.GamesType = $localStorage.GamesType;
    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.user_details = $localStorage.user_details;
        $scope.activeTab = 'Week';
        if (getQueryStringValue('MatchGUID')) {
            $scope.activeTab = 'Match';
        }
        /*To manage Tabs*/
        $scope.gotoTab = function (tab) {
            $scope.activeTab = tab;
            if (tab == 'Overall') {
                $scope.getLeaderBoardLeagueSeasonWise(true);
            } else {
                $scope.getWeekList();
            }
        }
        $scope.goBack = function () {
            $localStorage.activeTab = getQueryStringValue('activeTab')
            $window.history.back();
        }
        /*Function to get all league*/
        $scope.LeagueList = [];
        $scope.SelectedLeagueInfo = {};
        $scope.getLeagues = function () {
            var $data = {};
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.Params = 'LeagueFlag,Status,SeasonID';
            $data.Filter = 'CurrentSeasonLeagues';
            appDB.callPostForm($rootScope.apiPrefix + 'football/getLeagues', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.LeagueList = data.Data.Records;
                            $scope.LeagueGUID = (getQueryStringValue('LeagueGUID')) ? getQueryStringValue('LeagueGUID') : $scope.LeagueList[0].LeagueGUID;
                            var index = $scope.LeagueList.map(e => {
                                return e.LeagueGUID;
                            }).indexOf($scope.LeagueGUID);
                            $scope.SelectedLeagueInfo = $scope.LeagueList[index];
                            if (getQueryStringValue('MatchGUID')) {
                                $scope.getWeekList();
                            } else {
                                $scope.getLeaderBoardLeagueSeasonWise(true);
                            }
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
        /**
         * get weeks list
         */
        $scope.getWeekList = function () {
            $scope.WeekStatus = false;
            var $data = {};
            $data.Params = 'LeagueFlag,WeekStartDate,WeekEndDate,Status,WeekCount';
            $data.SessionKey = $scope.user_details.SessionKey;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getWeeks', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty('Records')) {
                            $scope.WeekList = data.Data.Records;
                            $scope.index = '';
                            $scope.WeekList.map((e, index) => {
                                if (getQueryStringValue('WeekGUID') && getQueryStringValue('WeekGUID') == e.WeekGUID) {
                                    $scope.index = index;
                                } else if (e.Status == 'Running') {
                                    $scope.index = index;
                                }
                                e.WeekStartDate = new Date($filter('convertIntoUserTimeZone')(e.WeekStartDate));
                                e.WeekEndDate = new Date($filter('convertIntoUserTimeZone')(e.WeekEndDate));
                            });
                            $scope.WeekInfo = $scope.WeekList[$scope.index];
                            $scope.WeekGUID = $scope.WeekInfo.WeekGUID;
                            $scope.WeekStatus = true;
                            $scope.getUserBalance($scope.WeekGUID);
                            $timeout(function () {
                                $(".round-slider").slick('slickGoTo', $scope.index)
                            }, 1000);
                            if ($scope.activeTab == 'Week') {
                                $scope.getLeaderBoardWeekWise(true);
                            } else if ($scope.activeTab == 'Match') {
                                $scope.getMatches();
                            }
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        /**
         * get weeks list
         */
        $scope.getLeaderboardWeekList = function () {
            var $data = {};
            $data.Params = 'WeekStartDate,WeekEndDate,WeekCount';
            $data.UpcomingWeekStatus = 'Completed';
            $data.SessionKey = $scope.user_details.SessionKey;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getWeeks', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty('Records')) {
                            $scope.CompletedWeekList = data.Data.Records;
                            if(getQueryStringValue("WeekGUID")) {
                                $scope.SelectedWeekGUID = $scope.WeekGUID ? $scope.WeekGUID : getQueryStringValue("WeekGUID");
                            } else {
                                $scope.SelectedWeekGUID = $scope.WeekGUID ? $scope.WeekGUID : $scope.UpcomingWeekList[0].WeekGUID;
                            }
                            $scope.getUserBalance($scope.SelectedWeekGUID)
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        /**
         * get matches list date wise
         */
        $scope.MatchesList = [];
        $scope.getMatches = function () {
            $scope.MatchesList = [];
            $scope.PlayerListMatchwise = [];
            var $data = { 'Filters[0]': 'DateWiseMatches' };
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.LeagueGUID = $scope.LeagueGUID;
            $data.WeekGUID = $scope.WeekGUID;
            $data.Params = 'MatchStartDateTime,MatchScoreDetails,TeamGUIDLocal,TeamGUIDVisitor,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,Status,MatchDate';
            $data.OrderBy = 'MatchStartDateTime'
            $data.Sequence = 'ASC';
            $data.Status = 'Completed';
            $data.TimeZone = $scope.getTimeZone();
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getMatches', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty('Records')) {
                            data.Data.Records.forEach(e => {
                                e.Matches.forEach(e1 => {
                                    e1.MatchStartDateTime = new Date($filter('convertIntoUserTimeZone')(e1.MatchStartDateTime));
                                    $scope.MatchesList.push(e1);
                                })
                            });
                            $scope.MatchGUID = (getQueryStringValue('MatchGUID')) ? getQueryStringValue('MatchGUID') : $scope.MatchesList[0].MatchGUID;
                            $scope.getLeaderBoardMatchWise(true);
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
        /**
         * change league
         */
        $scope.changeLeague = function () {
            var index = $scope.LeagueList.map(e => {
                return e.LeagueGUID;
            }).indexOf($scope.LeagueGUID);
            $scope.SelectedLeagueInfo = $scope.LeagueList[index];
            if ($scope.activeTab == 'Overall') {
                $scope.getLeaderBoardLeagueSeasonWise(true);
            } else if ($scope.activeTab == 'Week') {
                $scope.getLeaderBoardWeekWise(true);
            } else {
                $scope.getMatches();
            }
        }
        /**
         * change week
         */
        $scope.changeWeek = function (WeekGUID) {
            $scope.WeekGUID = WeekGUID;
            let index = $scope.WeekList.map(e => {
                return e.WeekGUID;
            }).indexOf(WeekGUID);
            $scope.WeekInfo = $scope.WeekList[index];
            if ($scope.activeTab == 'Week') {
                $scope.getLeaderBoardWeekWise(true);
            } else if ($scope.activeTab == 'Match') {
                $scope.getMatches();
            }
            $scope.getUserBalance($scope.WeekGUID);
        }
        /**
         * get league list season wise
         */
        $scope.getLeaderBoardLeagueSeasonWise = function (status) {
            if ($scope.activeTab != 'Overall') {
                return false;
            }
            if (status) {
                $scope.data.pageNo = 1;
                $scope.PlayerListSeasonwise = [];
                $scope.LoadMoreFlag = true;
                $scope.data.noRecords = false;
            }
            if ($scope.LoadMoreFlag == false || $scope.data.noRecords == true) {
                return false
            }
            var $data = {};
            $data.LeagueGUID = $scope.SelectedLeagueInfo.LeagueGUID;
            $data.SeasonID = $scope.SelectedLeagueInfo.SeasonID;
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.PageNo = $scope.data.pageNo;
            $data.PageSize = $scope.data.pageSize;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getLeaderBoardLeagueSeasonWise', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            if (data.Data.hasOwnProperty('Records') && data.Data.Records != '') {
                                $scope.LoadMoreFlag = true;
                                for (var i in data.Data.Records) {
                                    $scope.PlayerListSeasonwise.push(data.Data.Records[i]);
                                }
                                $scope.data.pageNo++;
                            } else {
                                $scope.LoadMoreFlag = false;
                            }
                        } else {
                            $scope.data.noRecords = true;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
        /**
         * get match wise leaderboard
         */
        $scope.getLeaderBoardMatchWise = function (status) {
            if ($scope.activeTab != 'Match') {
                return false;
            }
            if (status) {
                $scope.data.pageNo = 1;
                $scope.PlayerListMatchwise = [];
                $scope.LoadMoreFlag = true;
                $scope.data.noRecords = false;
            }
            if ($scope.LoadMoreFlag == false || $scope.data.noRecords == true) {
                return false
            }
            var $data = {};
            $data.MatchGUID = $scope.MatchGUID;
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.PageNo = $scope.data.pageNo;
            $data.PageSize = $scope.data.pageSize;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getLeaderBoardMatchWise', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            if (data.Data.hasOwnProperty('Records') && data.Data.Records != '') {
                                $scope.LoadMoreFlag = true;
                                for (var i in data.Data.Records) {
                                    $scope.PlayerListMatchwise.push(data.Data.Records[i]);
                                }
                                $scope.data.pageNo++;
                            } else {
                                $scope.LoadMoreFlag = false;
                            }
                        } else {
                            $scope.data.noRecords = true;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
        /**
         * get week wise leaderboard
         */
        $scope.getLeaderBoardWeekWise = function (status) {
            if ($scope.activeTab != 'Week') {
                return false;
            }
            if (status) {
                $scope.data.pageNo = 1;
                $scope.PlayerListWeekwise = [];
                $scope.LoadMoreFlag = true;
                $scope.data.noRecords = false;
            }
            if ($scope.LoadMoreFlag == false || $scope.data.noRecords == true) {
                return false
            }
            var $data = {};
            // $data.LeagueGUID = $scope.SelectedLeagueInfo.LeagueGUID;
            $data.WeekGUID = $scope.WeekGUID;
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.PageNo = $scope.data.pageNo;
            $data.PageSize = $scope.data.pageSize;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getLeaderBoardLeagueWeekWise', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            if (data.Data.hasOwnProperty('Records') && data.Data.Records != '') {
                                $scope.LoadMoreFlag = true;
                                for (var i in data.Data.Records) {
                                    $scope.PlayerListWeekwise.push(data.Data.Records[i]);
                                }
                                $scope.data.pageNo++;
                            } else {
                                $scope.LoadMoreFlag = false;
                            }
                        } else {
                            $scope.data.noRecords = true;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
        /**
         * change match
         */
        $scope.changeMatch = function (MatchGUID) {
            $scope.MatchGUID = MatchGUID;
            $scope.getLeaderBoardMatchWise(true);
        }
    } else {
        window.location.href = base_url;
    }
}]);
app.directive('slickWeekCustomCarousel', ["$timeout", function ($timeout) {
    return {
        restrict: "A",
        link: {
            post: function (scope, elem, attr) {
                $timeout(function () {
                    $('.round-slider').slick({
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
                            },
                            {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: 4,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 576,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 360,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });

                }, 10);

            }
        }
    }
}]);
