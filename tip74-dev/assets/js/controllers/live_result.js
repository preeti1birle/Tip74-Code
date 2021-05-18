'use strict';
app.controller('liveResultController', ['$scope', '$rootScope', 'environment', '$localStorage', '$filter', 'appDB', '$timeout','$window', function ($scope, $rootScope, environment, $localStorage, $filter, appDB, $timeout, $window) {
    $scope.env = environment;
    $scope.data.pageSize = 20;
    $scope.data.pageNo = 1;
    $scope.coreLogic = Mobiweb.helpers;
    $scope.GamesType = $localStorage.GamesType;
    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.user_details = $localStorage.user_details;
        $scope.MatchGUID = getQueryStringValue('MatchGUID');
        if(getQueryStringValue('MatchGUID') == ''){
            window.location.href = base_url+'dashboard';
        }
        /**
         * get match info
         */
        $scope.MatchInfo = [];
        $scope.getMatches = function () {
            $scope.MatchInfo = [];
            var $data = { 'Filters[0]': 'DateWiseMatches' };
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.MatchGUID = $scope.MatchGUID;
            $data.Params = 'IsPredicted,PredictionDetails,MatchStartDateTime,MatchScoreDetails,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,LeagueName,Status,TeamGUIDLocal,TeamGUIDVisitor';
            $data.Status = 'Completed';
            $data.TimeZone = $scope.getTimeZone();
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getMatches', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.MatchInfo = data.Data;
                            $scope.MatchInfo.IsDoubleUps = false;
                            if ($scope.MatchInfo.IsPredicted == 'Yes') {
                                if ($scope.MatchInfo.PredictionDetails.hasOwnProperty('PredictionStatus')) {
                                    if ($scope.MatchInfo.PredictionDetails.IsDoubleUps == 'Yes') {
                                        $scope.MatchInfo.IsDoubleUps = true;
                                    }
                                    $scope.MatchInfo.PredictionDetails.LockedDateTime = new Date($filter('convertIntoUserTimeZone')($scope.MatchInfo.PredictionDetails.LockedDateTime));
                                }
                            }
                            $scope.getLeaderBoardMatchWise(true);
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        $scope.goBack = function () {
            $localStorage.activeTab = getQueryStringValue('activeTab');
            $localStorage.WeekGUID = getQueryStringValue('WeekGUID');
            $localStorage.LeagueGUID = getQueryStringValue('LeagueGUID')
            $window.history.back();
        }
        /**
         * get match wise leaderboard
         */
        $scope.getLeaderBoardMatchWise = function (status) {
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
         * get Match line-up
         */
        $scope.getMatchTeamLineUp = function(Match){
            $rootScope.LineUpMatchInfo = Match;
            var $data = {};
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.MatchGUID = $scope.MatchGUID; //da672cac-c8fd-81e0-6fff-d5ae17698c1d
            appDB.callPostForm($rootScope.apiPrefix + 'football/getTeamLineup', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $rootScope.TeamLineup = data.Data;
                            $scope.openPopup('LineUpModal');
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
    } else {
        window.location.href = base_url;
    }
}]);