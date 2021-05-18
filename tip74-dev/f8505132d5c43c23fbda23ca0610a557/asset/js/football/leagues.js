app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 15;

    $scope.SeasonList = [];
    $scope.CompetitionList = [];
    $scope.getSeasonList = function ()
    {
        var data = 'SessionKey='+SessionKey+'&Params=SeasonID,IsCurrentSeason';
        $http.post(API_URL_FOOTBALL+'getSeasons', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                for (var i in response.Data.Records) {
                    if (response.Data.Records[i].IsCurrentSeason == 'Yes') {
                        $scope.SeasonID = response.Data.Records[i].SeasonID;
                    }
                    $scope.SeasonList.push(response.Data.Records[i]);
                }
                $timeout(function(){
                    $("select.chosen-select").chosen({ width: '100%',"disable_search_threshold": 8}).trigger("chosen:updated");
                    $scope.applyFilter($scope.SeasonID, 'season');
                }, 200);       
            }
        });
    }

    $scope.getCompetitionList = function() {
        var data = 'SessionKey='+SessionKey+'&Params=CompetitionGUID,CompetitionName,CompetitionID';
        $http.post(API_URL_FOOTBALL+'getCompetitions', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                for (var i in response.Data.Records) {
                    // if (response.Data.Records[i].IsCurrentSeason == 'Yes') {
                        // $scope.CompetitionGUID = response.Data.Records[0].CompetitionGUID;
                    // }
                    $scope.CompetitionList.push(response.Data.Records[i]);
                }
            }
        });
    }

    /*list*/
    $scope.applyFilter = function (id, type)
    {
        if(type == 'comp') {
            $scope.CompetitionGUID = id
        } else {
            $scope.SeasonID = id;
        }
        // $scope.CompetitionGUID = CompetitionGUID;
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList();
    }

    /*list append*/
    // $scope.Status = "Active";
    $scope.getList = function ()
    {
        if ($scope.SeasonID == "" || $scope.SeasonID == null || $scope.SeasonID == "undefined") return;
        if (!$scope.CompetitionGUID || $scope.CompetitionGUID == "" || $scope.CompetitionGUID == null) {
            $scope.CompetitionGUID = ""
        };
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&SeasonID='+$scope.SeasonID+'&CompetitionGUID='+$scope.CompetitionGUID+'&Params=CompetitionGUID,LeagueFlag,TotalRounds,Status&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize+'&'+$('#filterForm').serialize();
        $http.post(API_URL_FOOTBALL+'getLeagues', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                 $scope.data.dataList.push(response.Data.Records[i]);
                }
             $scope.data.pageNo++;               
            }else{
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }
    
    $scope.loadRoundList = function ()
    {
        if (getQueryStringValue('LeagueGUID')) {
            var LeagueGUID = getQueryStringValue('LeagueGUID');
        } else {
            var LeagueGUID = '';
        }
        $scope.data.pageLoading = true;
        $http.post(API_URL_FOOTBALL + 'getRounds', 'SessionKey=' + SessionKey + '&LeagueGUID=' + LeagueGUID + '&Params=RoundID,LeagueID,RoundStartDate,RoundEndDate,TotalMatches,Status'+"&OrderBy=Today", contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.RoundformData = response.Data;
            }
        });
        $http.post(API_URL_FOOTBALL+'getLeagues','LeagueGUID='+LeagueGUID+'&Params=&SessionKey='+SessionKey, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.LeagueData = response.Data
                $timeout(function(){            
                   $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
               }, 200);
            }
        });
        $('.table').removeProp('min-height');
    }

            /**
     * load add form
     */
    $scope.loadFormAdd = function(){
        $scope.formData = {};
        $scope.templateURLadd = PATH_TEMPLATE + module + '/add_form.htm?' + Math.random();
        $('#add_model').modal({ show: true });
    }

    /**
     * add 
     */
    $scope.addData = function(){
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $("form[name='add_form']").serialize();
        $http.post(API_URL + 'admin/Football/addLeague', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.applyFilter($scope.SeasonID);
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*load edit form*/
    $scope.loadFormEdit = function (Position, LeagueGUID)
    {
        $scope.editDataLoading = false;
        $scope.formData={};
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE+module+'/edit_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL_FOOTBALL+'getLeagues','LeagueGUID='+LeagueGUID+'&Params=CompetitionID,LeagueFlag,Status&SessionKey='+SessionKey, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                if(response.Data.CompetitionID) {
                    for (var i in $scope.CompetitionList) {
                        if(response.Data.CompetitionID == $scope.CompetitionList[i].CompetitionID) {
                            response.Data['CompetitionGUID'] = $scope.CompetitionList[i].CompetitionGUID
                        }
                    }
                }
                $scope.formData = response.Data;
                $('#edit_model').modal({show:true});
            }
        });
    }

    // **
    //  * delete venue 
    //  */
    $scope.deleteLeague = function (LeagueGUID) {
        var status = confirm('Are you sure, do you want to delete?');
        if (status) {
            var data = 'SessionKey=' + SessionKey + '&LeagueGUID='+LeagueGUID;
            $http.post(API_URL + 'admin/Football/deleteLeague', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) {
                    $scope.applyFilter($scope.SeasonID);
                }
            });
        }
    }
     /*edit data*/
    $scope.editData = function() {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $('#edit_form').serialize();
        $http.post(API_URL_FOOTBALL + 'updateLeague', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.data.dataList[$scope.data.Position] = response.Data;
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*edit data*/
    $scope.changeStatus = function(RoundID,SeriesStartDateUTC,SeriesEndDateUTC,AuctionDraftIsPlayed,SeriesType,SeriesID) {        
        var data = 'SessionKey=' + SessionKey +'&RoundID='+RoundID+'&SeriesID='+SeriesID+'&SeriesType='+SeriesType+'&RoundStartDate='+SeriesStartDateUTC+'&RoundEndDate='+SeriesEndDateUTC+ '&AuctionDraftIsPlayed=' +AuctionDraftIsPlayed;
        $http.post(API_URL_FOOTBALL + 'admin/series/updateRounds', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
            } else {
                alertify.error(response.Message);
            }
        });
    }

}); 


