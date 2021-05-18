app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 15;
    /*----------------*/
    $scope.LeagueData = [];
    $scope.ManualLeagueData =  [];
    $scope.CompetitionList = [];
    $scope.SeasonList = [];
    $scope.LeagueGUID = "";
    
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
                    $scope.getFilterData();
                }, 200);       
            }
        });
    }

    $scope.getCompetitionList = function() {
        var data = 'SessionKey='+SessionKey+'&Params=CompetitionGUID,CompetitionName';
        $http.post(API_URL_FOOTBALL+'getCompetitions', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                for (var i in response.Data.Records) {
                    // if (response.Data.Records[i].IsCurrentSeason == 'Yes') {
                    // }
                    $scope.CompetitionList.push(response.Data.Records[i]);
                }
                $timeout(function(){
                    $("select.chosen-select").chosen({ width: '100%',"disable_search_threshold": 8}).trigger("chosen:updated");
                    $scope.applyFilter($scope.SeasonID, $scope.CompetitionGUID);
                }, 200);       
            }
        });
    }

    $scope.getFilterData = function ()
    {
        var data = 'SessionKey='+SessionKey+"&SeasonID="+$scope.SeasonID;
        $http.post(API_URL_FOOTBALL+'getLeagues', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                for (var i in response.Data.Records) {
                    $scope.LeagueData = response.Data.Records;
                }
            }
            $timeout(function(){
                $(".chosen-select").chosen({ search_contains: true, width: '100%',"disable_search_threshold": 8}).trigger("chosen:updated");
                $scope.applyFilter($scope.SeasonID,"");
            }, 200);   
        });
    }


    /*list*/
    $scope.applyFilter = function (SeasonID,LeagueGUID)
    {
        $scope.SeasonID = SeasonID;
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList(LeagueGUID);
    }


    /*list append*/
    $scope.getList = function (LeagueGUID)
    {
        if ($scope.SeasonID == "" || $scope.SeasonID == null || $scope.SeasonID == "undefined") return;
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&SeasonID='+$scope.SeasonID+'&LeagueGUID='+LeagueGUID+'&Params=TeamID,Status,LeagueName,TeamName,TeamNameShort,TeamFlag,TeamColor&PageNo='+$scope.data.pageNo+'&PageSize='+$scope.data.pageSize+'&'+$('#filterForm').serialize();
        $http.post(API_URL_FOOTBALL+'getTeams', data, contentType).then(function(response) {
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

    $scope.getManualLeagueList = function(CompetitionGUID) {
        var data = 'SessionKey='+SessionKey+'&CompetitionGUID='+CompetitionGUID+"&LeagueSource=Manual&SeasonID="+$scope.SeasonID;
        $http.post(API_URL_FOOTBALL+'getLeagues', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                setTimeout(function(){
                    $scope.$apply(function(){
                        $scope.ManualLeagueData = response.Data.Records;
                    });
                    $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
                }, 00);
            } else {
                setTimeout(function(){
                    $scope.$apply(function(){
                        $scope.ManualLeagueData = [];
                    });
                    $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
                }, 00);
            }
        });
    }


        /**
     * load add form
     */
    $scope.loadFormAdd = function(){
        $scope.formData = {};
        $scope.templateURLadd = PATH_TEMPLATE + module + '/add_form.htm?' + Math.random();
        $('#add_model').modal({ show: true });
        $timeout(function(){
            $("select.chosen-select").chosen({ width: '100%',"disable_search_threshold": 8}).trigger("chosen:updated");
            $scope.getFilterData();
        }, 200); 
    }
     /**
     * add 
     */
    $scope.addData = function(){
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $("form[name='add_form']").serialize();
        $http.post(API_URL + 'admin/Football/addTeam', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.applyFilter($scope.SeasonID, "");
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*load edit form*/
    $scope.loadFormEdit = function (Position, TeamGUID)
    {
        $scope.formData={};
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE+module+'/edit_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        var data = 'SessionKey='+SessionKey+'&TeamGUID='+TeamGUID+'&Params=TeamName,Name,TeamNameShort,TeamFlag,TeamColor';
        $http.post(API_URL_FOOTBALL+'getTeams', data, contentType).then(function(response) {
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

     /*edit data*/
     $scope.editData = function ()
     {
 
         $scope.editDataLoading = true;
         var data = 'SessionKey='+SessionKey+'&'+$("form[name='edit_form']").serialize();
         console.log(data);
         $http.post(API_URL_FOOTBALL+'updateTeam', data, contentType).then(function(response) {
             var response = response.data;
             if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                var leagueName = $scope.data.dataList[$scope.data.Position]['LeagueName'];
                $scope.data.dataList[$scope.data.Position] = response.Data;
                $scope.data.dataList[$scope.data.Position]['LeagueName']= leagueName;
                $('.modal-header .close').click();
             }else{
                alertify.error(response.Message);
             }
             $scope.editDataLoading = false;          
         });
     }

    /*load team standing edit form*/
    $scope.loadFormUpdateTeam = function (Position, TeamGUID)
    {
        $scope.formData={};
        $scope.data.Position = Position;
        $scope.templateURLEditTS = PATH_TEMPLATE+module+'/updateTS_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        var data = 'SessionKey='+SessionKey+'&TeamGUID='+TeamGUID+
        '&Params=TeamStandings,OverallGamePlayed,OverallWon,OverallDraw,OverallLost,OverallGoalFor,OverallGoalAgainst,HomeGamePlayed,HomeWon,HomeDraw,HomeLost,AwayGamePlayed,AwayWon,AwayDraw,AwayLost,Points,GoalDifference,TeamName';
        $http.post(API_URL_FOOTBALL+'getTeams', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data;
                // $scope.formData.Position = $scope.data.Position;
                $('#editTS_model').modal({show:true});
                $timeout(function(){            
                   $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
               }, 200);
            }
        });
    }

     /*edit team standing*/
     $scope.editTeamStanding = function ()
     {
 
         $scope.editDataLoading = true;
         var data = 'SessionKey='+SessionKey+'&'+$("form[name='editTS_form']").serialize();
         console.log(data);
         $http.post(API_URL_FOOTBALL+'updateTeamStandings', data, contentType).then(function(response) {
             var response = response.data;
             if(response.ResponseCode==200){ /* success case */               
                 alertify.success(response.Message);
                //  $scope.data.dataList[$scope.data.Position] = response.Data;
                 $('.modal-header .close').click();
             }else{
                 alertify.error(response.Message);
             }
             $scope.editDataLoading = false;          
         });
    }

    
    $scope.deleteTeam = function (TeamGUID) {
        var status = confirm('Are you sure, do you want to delete?');
        if (status) {
            var data = 'SessionKey=' + SessionKey + '&TeamGUID='+TeamGUID;
            $http.post(API_URL + 'admin/Football/deleteTeam', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) {
                    $scope.applyFilter($scope.SeasonID, "");
                }
            });
        }
    }

}); 
