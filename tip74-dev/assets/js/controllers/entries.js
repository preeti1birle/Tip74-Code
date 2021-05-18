'use strict';

app.controller('myEntriesController', ['$scope', '$rootScope', '$location', 'environment', '$localStorage', '$filter', 'appDB', '$timeout', '$http', function ($scope, $rootScope, $location, environment, $localStorage, $filter, appDB, $timeout, $http) {
    $scope.env = environment;
    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        //do something
        $scope.pageSize = 15;
        $scope.pageNo = 1;
        $scope.activeTab = 'unAssignEntries';
        $scope.ChangeTab = function (tab) {
            $scope.activeTab = tab;
            if (tab == 'unAssignEntries') {
                $scope.Filter = 'UnAssigned';
            } else if (tab === 'assignedEntries') {
                $scope.Filter = 'Assigned';
            }
            $scope.getAssignedEntries(true);
        }
        $scope.NextData = true;
        $scope.Filter = 'UnAssigned'
        $scope.getAssignedEntries = function (status) {
            if (status) {
                $scope.TotalWithdrawTransactionCount = 0;
                $scope.pageNo = 1;
                $scope.unAssignedEntriesList = [];
                $scope.assignedEntriesList = [];
                $scope.LoadMoreFlag = true;
                $scope.data.noRecords = false;
            }
            if ($scope.LoadMoreFlag == false || $scope.data.noRecords == true || $scope.NextData == false) {
                return false
            }
            if ($scope.NextData) {
                $scope.NextData = false;
                $rootScope.loader.isLoading = false;
                var $data = {};
                $data.Filter = $scope.Filter;
                // $data.UserGUID = $localStorage.user_details.UserGUID;
                $data.SessionKey = $localStorage.user_details.SessionKey;
                $data.Params = "WeekGUID,WeekCount,EntryNo,AllowedPredictions,ConsumedPredictions,AllowedPurchaseDoubleUps,TotalPurchasedDoubleUps,ConsumeDoubleUps";
                $data.PageNo = $scope.pageNo;
                $data.PageSize = $scope.pageSize;
                $http.post($scope.env.api_url + 'entries/list', $.param($data), contentType).then(function (response) {
                    var response = response.data;
                    $scope.NextData = true;
                    $rootScope.loader.isLoading = true;
                    if ($scope.checkResponseCode(response) && response.Data.hasOwnProperty('Records')) {
                        if ($scope.Filter == 'UnAssigned') {
                            $scope.TotalRecords = response.Data.TotalRecords;
                            response.Data.Records.forEach(e => {
                                $scope.unAssignedEntriesList.push(e);
                            });
                        } else {
                            $scope.UnAssignedTotalRecords = response.Data.TotalRecords;
                            response.Data.Records.forEach(e => {
                                $scope.assignedEntriesList.push(e);
                            });
                        }
                        $scope.pageNo++;
                    } else {
                        $scope.data.noRecords = true;
                    }
                });
            }
        }
    } else {
        window.location.href = base_url;
    }
}]);