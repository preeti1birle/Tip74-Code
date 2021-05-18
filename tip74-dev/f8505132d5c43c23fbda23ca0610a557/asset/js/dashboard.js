app.controller('PageController', function ($scope, $http, $timeout) {
    $scope.data.pageSize = 15;
    $scope.data.pageNo = 1;
    /*----------------*/

    $scope.applyFilter = function ()
    {
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.TotalDepositsList();
    }

    /*list append*/
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey;
        $http.post(API_URL + 'utilities/dashboardStatics', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.dataList = response.Data;
                $scope.data.pageNo++;

            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
            // setTimeout(function(){ tblsort(); }, 1000);
            // $scope.getMatchesList();
        });
    }

    /*match list append*/
    $scope.getMatchesList = function ()
    {
        $scope.matches = [];
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        /*  */
        var data = 'SessionKey=' + SessionKey + '&OrderBy=MatchStartDateTime&Sequence=ASC&existingContests=2&Params=SeriesName,MatchType,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation,Status&PageNo=1&PageSize=5&Status=Running';
        $http.post(API_URL + 'sports/getMatches', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.matches = response.Data;
                $scope.data.pageNo++;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
            // $scope.getMatchesUpcomingList();    
            // setTimeout(function(){ tblsort(); }, 1000);
        });
    }

    $scope.getMatchesUpcomingList = function ()
    {   
        $scope.matchesUpcoming = [];
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        /*  */
        var data = 'SessionKey=' + SessionKey + '&Filter=TodayMatch&existingContests=2&Params=SeriesName,MatchType,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation,Status&PageNo=1&PageSize=5&Status=Pending';
        $http.post(API_URL + 'sports/getMatches', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.matchesUpcoming = response.Data;
                $scope.data.pageNo++;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
            $scope.getMatchesRuningListFootball();  
            // setTimeout(function(){ tblsort(); }, 1000);
        });
    }

    $scope.getMatchesRuningListFootball = function ()
    {   
        $scope.matchesRuningFootball = [];
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        /*  */
        var data = 'SessionKey=' + SessionKey + '&Filter=Today&existingContests=2&Params=SeriesName,MatchType,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation,Status&PageNo=1&PageSize=5&StatusID=2';
        $http.post(API_URL + 'football/admin/matches/getMatches', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.matchesRuningFootball = response.Data;
                $scope.data.pageNo++;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
            $scope.getMatchesUpcomingListFootball();
            // setTimeout(function(){ tblsort(); }, 1000);
        });
    }

    $scope.getMatchesUpcomingListFootball = function () {
        $scope.matchesUpcomingFootball = [];
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        /*  */
        var data = 'SessionKey=' + SessionKey + '&Filter=Today&existingContests=2&Params=SeriesName,MatchType,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation,Status&PageNo=1&PageSize=5&StatusID=1';
        $http.post(API_URL + 'football/admin/matches/getMatches', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.matchesUpcomingFootball = response.Data;
                $scope.data.pageNo++;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
            // setTimeout(function(){ tblsort(); }, 1000);
        });
    }

    $scope.usersList = function ()
    {
    }

    $scope.LoadDepositsList = function (Type)
    {
        window.open(BASE_URL + 'depositHistory?Type=' + Type);
    }

    $scope.LoadUserList = function (Type)
    {
        if (Type == 'Today') {
            window.open(BASE_URL + 'user?Type=Today');
        } else {
            window.open(BASE_URL + 'user');
        }
    }

    $scope.LoadUnverifiedUserList = function (Type)
    {
        if (Type == 'Today') {
            window.open(BASE_URL + 'user?Type=Today&pending=pending');
        } else {
            window.open(BASE_URL + 'user?pending=pending');
        }
    }

    $scope.withdrawalsList = function (Type)
    {
        if (Type == 'Pending') {
            window.open(BASE_URL + 'withdrawals?Type=Pending');
        } else {
            window.open(BASE_URL + 'withdrawals');
        }
    }

    /* List */
    $scope.TotalDepositsList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;

        var values = $('#filterForm1').serializeArray();       
        if (getQueryStringValue('Type')) {
            var Type = getQueryStringValue('Type');
        } else {
            var Type = '';
        }

        if(values[0].value != '' || values[1].value != ''){
            var Type = 'All';
        }


        $http.post(API_URL + 'admin/wallet/getTotalDeposits', 'OrderBy=EntryDate&Sequence=DESC&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize + '&SessionKey=' + SessionKey + '&Type=' + Type + '&' + $('#filterForm1').serialize() + "&" + $('#filterForm').serialize(), contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records.length > 0) {
                /* success case */
                $scope.totalRecords = response.Data.Records.length;
                for (var i in response.Data.Records) {
                    $scope.data.dataList.push(response.Data.Records[i]);
                }
                $scope.data.pageNo++;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }

    $scope.loadFormEdit = function (Position, MatchGUID)
    {
      
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE+module+'/edit_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL+'admin/matches/getMatch', 'MatchGUID='+MatchGUID+'&Params=Status,MatchClosedInMinutes,SeriesName,MatchType,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation&SessionKey='+SessionKey, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#edit_model').modal({show:true});
                $timeout(function(){            
                   $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
               }, 200);
            }
        });
    }



});
