app.controller('PageController', function ($scope, $http, $timeout,$filter) {
    $scope.Keyword = '';
    /**
     * search filter
     */
    $scope.applyFilter = function () { 
        $scope.data = angular.copy($scope.orig);
        $scope.getList();
    }
    if (getQueryStringValue('UserGUID')) {
        $scope.UserGUID = getQueryStringValue('UserGUID');
    }
    /**
     * get user list
     */
    $scope.userInfo = function () {
        var data = 'SessionKey=' + SessionKey + '&UserGUID=' + $scope.UserGUID + '&Params=FullName';

        $http.post(API_URL + 'admin/users', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records) {
                $scope.userData = response.Data.Records[0];
            }
        });
    }
    /**
     * prediction list
     */
    $scope.getList = function () {
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        var data = 'Keyword='+$scope.Keyword+'&SessionKey=' + SessionKey + '&UserGUID=' + $scope.UserGUID + '&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize + '&OrderBy=PredictionDate&Sequence=DESC&Params=LeagueName,LeagueFlag,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,TeamScoreLocal,TeamScoreVisitor,PredictionStatus,PredictionType,PredictionDate,Status,MatchStartDateTime';

        $http.post(API_URL + 'admin/users/getPredictions', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records) { /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                    response.Data.Records[i].MatchStartDateTime = new Date($filter('convertIntoUserTimeZone')(response.Data.Records[i].MatchStartDateTime));
                    $scope.data.dataList.push(response.Data.Records[i]);
                }
                $scope.data.pageNo++;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }

});