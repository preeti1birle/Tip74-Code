'use strict';
app.controller('myPredictionController', ['$scope', '$rootScope', 'environment', '$localStorage', '$filter', 'appDB', '$timeout', '$window', function ($scope, $rootScope, environment, $localStorage, $filter, appDB, $timeout, $window) {
    $scope.env = environment;
    $scope.data.pageSize = 20;
    $scope.data.pageNo = 1;
    $scope.coreLogic = Mobiweb.helpers;
    $scope.GamesType = $localStorage.GamesType;
    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.user_details = $localStorage.user_details;
        /*To manage Tabs*/
        $scope.activeTab = $localStorage.activeTab ? $localStorage.activeTab : 'Pending';
        $scope.gotoTab = function (tab) {
            $scope.activeTab = tab;
            $scope.getPredictedMatches(true);
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
                            $scope.getWeekList();
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
        $scope.getAssignedEntries = function () {
            var $data = {};
            $data.Filter = 'Assigned';
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.Params = "WeekGUID,WeekCount,EntryNo,AllowedPredictions,ConsumedPredictions,AllowedPurchaseDoubleUps,TotalPurchasedDoubleUps,ConsumeDoubleUps";
            appDB.callPostForm($rootScope.apiPrefix + 'entries/list', $.param($data), contentType).then(function (response) {
                $scope.EntriesList = response.Data.Records;
            });
        }
        $scope.changeEntries = function (eNo) {
            let index1 = $scope.EntriesList.map(e => {
                return e.EntryNo;
            }).indexOf(eNo);
            if(index1 != -1) {
                $scope.entries = $scope.EntriesList[index1];
                $scope.EntryNo = $scope.EntriesList[index1].EntryNo;

                var selectedEntry = ($scope.entries)
            
                $scope.GameEntryID = selectedEntry.GameEntryID;
                $scope.WeekGUID = selectedEntry.WeekGUID;
                $scope.EntryNo = selectedEntry.EntryNo;
                $timeout(function () {
                    $(".round-slider").slick('slickGoTo', selectedEntry.WeekCount-1)
                }, 1000);
                $scope.getUserBalance($scope.WeekGUID, $scope.EntryNo);
                $scope.getPredictedMatches(true);
            }
            
        }
        /**
         * get weeks list
         */
        $scope.getWeekList = function () {
            $scope.WeekStatus = false;
            var $data = {};
            $data.Params = 'LeagueFlag,WeekStartDate,WeekEndDate,Status';
            $data.SessionKey = $scope.user_details.SessionKey;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getWeeks', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty('Records')) {
                            $scope.WeekList = data.Data.Records;
                            $scope.index = '';
                            $scope.WeekList.map((e, index) => {
                                if (e.Status == 'Running') {
                                    $scope.index = index;
                                }
                                e.WeekStartDate = new Date($filter('convertIntoUserTimeZone')(e.WeekStartDate));
                                e.WeekEndDate = new Date($filter('convertIntoUserTimeZone')(e.WeekEndDate));
                            });
                            $scope.WeekInfo = $scope.WeekList[$scope.index];
                            $scope.WeekGUID = $scope.WeekInfo.WeekGUID;
                            $scope.WeekStatus = true;
                            let index1 = $scope.EntriesList.map(e => {
                                return e.WeekGUID;
                            }).indexOf($scope.WeekGUID);
                            if(index1 != -1) {
                                $scope.entries = $scope.EntriesList[index1];
                                $scope.EntryNo = $scope.EntriesList[index1].EntryNo;
                            }
                            $scope.getUserBalance($scope.WeekGUID);
                            $timeout(function () {
                                $(".round-slider").slick('slickGoTo', $scope.index)
                            }, 1000);
                            $scope.getPredictedMatches(true);
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        $scope.goBack = function () {
            $window.history.back();
        }
        /**
         * get matches list date wise
         */
        $scope.MatchesList = [];
        $scope.getPredictedMatches = function (status) {
            if (status) {
                $scope.data.pageNo = 1;
                $scope.MatchesList = [];
                $scope.LoadMoreFlag = true;
                $scope.data.noRecords = false;
            }
            if ($scope.LoadMoreFlag == false || $scope.data.noRecords == true) {
                return false
            }
            var $data = {};
            $data.SessionKey = $scope.user_details.SessionKey;
            //$data.LeagueGUID = $scope.LeagueGUID;
            $data.WeekGUID = $scope.WeekGUID;
            $data.GameEntryID = $scope.GameEntryID;
            $data.Params = 'LeagueFlag,LeagueName,TeamScoreLocalHT,TeamScoreVisitorHT,TeamScoreLocalFT,TeamScoreVisitorFT,IsDoubleUps,PredictionDate,ExactScorePoints,CorrectResultPoints,BothTeamScorePoints,LongestOddsScorePoints,MatchScoreDetails,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchStartDateTime';
            $data.PredictionStatus = 'Lock';
            $data.PageNo = $scope.data.pageNo;
            $data.PageSize = $scope.data.pageSize;
            $data.Status = $scope.activeTab;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getPredictions', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            if (data.Data.hasOwnProperty('Records') && data.Data.Records != '') {
                                data.Data.Records.forEach(e1 => {
                                    e1.MatchStartDateTime = new Date($filter('convertIntoUserTimeZone')(e1.MatchStartDateTime));
                                    $scope.MatchesList.push(e1);
                                });
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
         * change league
         */
        $scope.changeLeague = function () {
            var index = $scope.LeagueList.map(e => {
                return e.LeagueGUID;
            }).indexOf($scope.LeagueGUID);
            $scope.SelectedLeagueInfo = $scope.LeagueList[index];
            $scope.getPredictedMatches(true);
        }
        /**
         * change week
         */
        $scope.changeWeek = function (WeekGUID) {
            $scope.WeekGUID = WeekGUID;
            let index = $scope.WeekList.map(e => {
                return e.WeekGUID;
            }).indexOf(WeekGUID);
            let index1 = $scope.EntriesList.map(e => {
                return e.WeekGUID;
            }).indexOf($scope.WeekGUID);
            if(index1 != -1) {
                $scope.entries = $scope.EntriesList[index1];
                $scope.EntryNo = $scope.EntriesList[index1].EntryNo;
            }
            $scope.WeekInfo = $scope.WeekList[index];
            // $timeout(function () {
            //     $(".round-slider").slick('slickGoTo', selectedEntry.WeekCount-1)
            // }, 1000);
            $scope.getPredictedMatches(true);
            $scope.getUserBalance($scope.WeekGUID);
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
