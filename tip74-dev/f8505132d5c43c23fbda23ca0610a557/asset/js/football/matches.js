app.controller('PageController', function ($scope, $http, $timeout, $filter){
    $scope.data.pageSize = 15;
    $scope.LeagueData = [];
    $scope.WeekListData = [];
    $scope.LeagueListData = [];
    $scope.VenueListData = [];
    $scope.TeamListData = [];
    $scope.SeasonList = [];
    $scope.CompetitionList=[];
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


    $scope.getFilterData = function ()
    {
        var data = 'SessionKey='+SessionKey+"&SeasonID="+$scope.SeasonID;
        $http.post(API_URL_FOOTBALL+'getLeagues', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                // for (var i in response.Data.Records) {
                //     $scope.LeagueData.push(response.Data.Records[i]);
                // }
                $scope.LeagueData = response.Data.Records;
            }
            $timeout(function(){
                $(".chosen-select").chosen({ search_contains: true, width: '100%',"disable_search_threshold": 8}).trigger("chosen:updated");
                $scope.applyFilter($scope.Status,$scope.SeasonID,"");
            }, 200);   
        });
    }

    $scope.getWeeks=function() {
        var data = 'SessionKey='+SessionKey +  '&UpcomingWeekStatus=Pending&Params=WeekCount,Status';
        // var data = 'SessionKey='+SessionKey +  '&Params=Status,WeekCount';

        $http.post(API_URL_FOOTBALL+'getWeeks', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                // for (var i in response.Data.Records) {
                    $scope.WeekListData = response.Data.Records;
                // }
            }   
        });
    }

    $scope.getCompetitionList = function() {
        var data = 'SessionKey='+SessionKey+'&Params=CompetitionGUID,CompetitionName';
        $http.post(API_URL_FOOTBALL+'getCompetitions', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                for (var i in response.Data.Records) {
                    $scope.CompetitionList.push(response.Data.Records[i]);
                }
                $timeout(function(){
                    $("select.chosen-select").chosen({ width: '100%',"disable_search_threshold": 8}).trigger("chosen:updated");
                    $scope.applyFilter($scope.SeasonID, $scope.CompetitionGUID);
                }, 200);       
            }
        });
    }

    $scope.getLeagues=function(CompetitionGUID) {
        var data = 'SessionKey='+SessionKey + '&CompetitionGUID='+CompetitionGUID+'&LeagueSource=Manual';
        $http.post(API_URL_FOOTBALL+'getLeagues', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                setTimeout(function(){
                    $scope.$apply(function(){
                        $scope.LeagueListData = response.Data.Records;
                    });
                    $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
                }, 00);
            } else {
                setTimeout(function(){
                    $scope.$apply(function(){
                        $scope.LeagueListData = [];
                    });
                    $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
                }, 00);
            }
        });
    }

    $scope.getVenues=function() {
        var data = 'SessionKey='+SessionKey + '&VenueSource=Manual&Params=VenueID,VenueCity,VenueName';
        $http.post(API_URL_FOOTBALL+'getVenues', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                // for (var i in response.Data.Records) {
                    $scope.VenueListData = response.Data.Records;
                // }
            }   
        });
    }

    $scope.getTeams=function(LeagueGUID) {
        if(LeagueGUID == "") {
            $scope.TeamListData = [];
        } else {
            var data = 'SessionKey='+SessionKey + '&LeagueGUID='+ LeagueGUID + '&TeamSource=Manual';
            $http.post(API_URL_FOOTBALL+'getTeams', data, contentType).then(function(response) {
                var response = response.data;
                if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                    // for (var i in response.Data.Records) {
                    $scope.TeamListData = response.Data.Records;
                    $timeout(function(){ 
                        $scope.$apply(function () {
                            $scope.TeamListData = $scope.TeamListData;
                        });           
                        $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
                    }, 300);
                        
                }   
            });
        }
    }

    /*list*/
    $scope.applyFilter = function (Status,SeasonID,LeagueGUID)
    {
        $scope.Status = Status;
        $scope.SeasonID = SeasonID;
        $scope.LeagueGUID = LeagueGUID;
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList();
    }

    /*list append*/
    $scope.Status = "Pending";
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        if ($scope.SeasonID == "" || $scope.SeasonID == null || $scope.SeasonID == "undefined") return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey + '&SeasonID=' + $scope.SeasonID +'&LeagueGUID=' + $scope.LeagueGUID +'&OrderBy=' + $scope.data.OrderBy + '&Sequence=' + $scope.data.Sequence +
                    '&OrderByToday=Yes&Params=' +
                     'LeagueName,LeagueFlag,VenueName,VenueAddress,VenueCity,VenueCapicity,VenueImage,TeamGUIDLocal,TeamGUIDVisitor,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchStartDateTime,CurrentDateTime,MatchDate,MatchTime,MatchStartDateTimeUTC,Status&PageNo=' 
                    + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize + '&' + $('#filterForm').serialize()
                    + '&Status=' + $scope.Status;
        $http.post(API_URL_FOOTBALL+'getMatches', data, contentType).then(function(response) {
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
        // setTimeout(function(){ tblsort(); }, 1000);
    });
    }

    $scope.applyOrderedList = function(OrderBy, Sequence) {
        PSequence = $scope.data.Sequence;
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/

        $scope.data.OrderBy = OrderBy;
        if (PSequence == '' || PSequence == 'ASC' || typeof PSequence == 'undefined') {
            $scope.data.Sequence = 'DESC';
        } else {
            $scope.data.Sequence = 'ASC';
        }

        $scope.getList();
    }

    $scope.getTeamData = function (SeriesGUID,LocalTeamGUID='')
    {
        var data = 'SessionKey=' + SessionKey + '&LocalTeamGUID='+LocalTeamGUID+'&SeriesGUID='+ SeriesGUID+'&Params=TeamNameLocal,TeamNameVisitor,TeamGUID&' + $('#filterPanel form').serialize();
        $http.post(API_URL_FOOTBALL + 'getTeams', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data) {
                /* success case */
                if (LocalTeamGUID != '') {
                    $scope.VisitorTeamData = response.Data;
                }else{
                    $scope.LocalTeamData = response.Data;
                }
                $timeout(function () {
                    $("select.chosen-select").chosen({width: '100%', "disable_search_threshold": 8}).trigger("chosen:updated");
                }, 300);
            }
        });
    }

    $scope.today = new Date();
    
    
    /*load add form*/
    $scope.loadFormAdd = function (Position, CategoryGUID)
    {
        $scope.formData={};
        var date = new Date();
        // $scope.formData['TimeZoneIdentifire'] = ($filter("convertIntoUserTimeZone")(date));
        $scope.formData['TimeZoneIdentifire'] = ($filter('date')(date, 'Z'));
        var a = ($scope.formData['TimeZoneIdentifire'].split(""));
        var b = a.splice(-2, 0, ":");
        $scope.formData['TimeZoneIdentifire'] = a.join("");

        $scope.templateURLAdd = PATH_TEMPLATE+module+'/add_form.htm?'+Math.random();
        $('#add_model').modal({show:true});

       $timeout(function(){            
        $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
        document.getElementById("MatchStartDateTime").min = new Date();
        document.getElementById("MatchStartDateTime").max = new Date();
        // $('#MatchStartDateTime').datetimepicker({format: "DD/MM/YYYY hh:mm", minView: 2, startDate: new Date()});
        // $('#datetimepicker1').datetimepicker({
        //     language: 'pt-BR'
        //   });
    }, 300);
    }

     /**
     * add 
     */
    $scope.addData = function(){
       if($scope.formData.TeamGUIDVisitor && $scope.formData.TeamGUIDVisitor !== "" && $scope.formData.TeamGUIDLocal && $scope.formData.TeamGUIDLocal != ""){
        if($scope.formData.TeamGUIDVisitor == $scope.formData.TeamGUIDLocal) {
            $scope.formData.TeamGUIDVisitor = "";
            $scope.formData.TeamGUIDLocal = "";
            $timeout(function(){            
                $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
            }, 300);
            alertify.error("Team Visitor and Team Local can't be same");
            return;
        }
       }
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $("form[name='add_form']").serialize();
        $http.post(API_URL + 'admin/Football/addMatch', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.applyFilter($scope.Status, $scope.SeasonID, $scope.LeagueGUID);
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*load edit form*/
    $scope.loadFormEdit = function (Position, MatchGUID)
    {
        $scope.formData= {};
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE+module+'/edit_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL_FOOTBALL+'getMatches', 'MatchGUID='+MatchGUID+'&Params=Status,MatchClosedInMinutes,LeagueName,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,VenueName&SessionKey='+SessionKey, contentType).then(function(response) {
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
        $http.post(API_URL_FOOTBALL+'updateMatch', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                $scope.data.dataList[$scope.data.Position] = response.Data;
                $('.modal-header .close').click();
            }else{
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;          
        });
    }

    /*load delete form*/
    // $scope.loadFormDelete = function (Position, CategoryGUID)
    // {
    //     $scope.data.Position = Position;
    //     $scope.templateURLDelete = PATH_TEMPLATE+module+'/delete_form.htm?'+Math.random();
    //     $scope.data.pageLoading = true;
    //     $http.post(API_URL_FOOTBALL+'category/getCategory', 'SessionKey='+SessionKey+'&CategoryGUID='+CategoryGUID, contentType).then(function(response) {
    //         var response = response.data;
    //         if(response.ResponseCode==200){ /* success case */
    //             $scope.data.pageLoading = false;
    //             $scope.formData = response.Data
    //             $('#delete_model').modal({show:true});
    //             $timeout(function(){            
    //                $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
    //            }, 200);
    //         }
    //     });
    // }

    $scope.deleteMatch = function (MatchGUID) {
        var status = confirm('Are you sure, do you want to delete?');
        if (status) {
            var data = 'SessionKey=' + SessionKey + '&MatchGUID='+MatchGUID;
            $http.post(API_URL + 'admin/Football/deleteMatch', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) {
                    $scope.applyFilter($scope.Status, $scope.SeasonID, $scope.LeagueGUID);
                }
            });
        }
    }

    /*load update score form*/
    $scope.loadFormUpdateScore = function (Position, MatchGUID)
    {
        $scope.formData= {};
        $scope.data.Position = Position;
        $scope.templateURLUpdate = PATH_TEMPLATE+module+'/update_score_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL_FOOTBALL+'getMatches', 'MatchGUID='+MatchGUID+
        '&Params=LongestOddsLabel,MatchScoreDetails,LeagueName,LeagueFlag,VenueName,VenueAddress,VenueCity,VenueCapicity,VenueImage,TeamGUIDLocal,TeamGUIDVisitor,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchStartDateTime,CurrentDateTime,MatchDate,MatchTime,MatchStartDateTimeUTC,Status&SessionKey='
        +SessionKey, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data;
                $scope.formData['MatchStatus']=$scope.formData.Status;
                if($scope.formData['MatchScoreDetails']['HalfTimeScore']){
                    $scope.formData['HalfTimeLocalTeamScore']=Number($scope.formData['MatchScoreDetails']['HalfTimeScore'][0]);
                    $scope.formData['HalfTimeVisitorTeamScore']=Number($scope.formData['MatchScoreDetails']['HalfTimeScore'][2]);
                }
                if($scope.formData['MatchScoreDetails']['LocalTeamScore']){
                    $scope.formData['FullTimeLocalTeamScore']=Number($scope.formData['MatchScoreDetails']['LocalTeamScore']);
                    $scope.formData['FullTimeVisitorTeamScore']=Number($scope.formData['MatchScoreDetails']['VisitorTeamScore']);    
                }
                $scope.formData['WinnerTeam']=$scope.formData['MatchScoreDetails']['WinnerTeam'];
                $scope.formData['LongestOdds']=$scope.formData['LongestOddsLabel'];
                if($scope.formData['MatchScoreDetails']['WinnerTeam'] == "Local"){
                    $scope.formData['WinnerTeamGUID']= $scope.formData['TeamGUIDLocal'];
                } else if($scope.formData['MatchScoreDetails']['WinnerTeam'] == "Visitor"){
                    $scope.formData['WinnerTeamGUID']= $scope.formData['TeamGUIDVisitor'];
                }


                $('#update_score_model').modal({show:true});
                $timeout(function(){            
                   $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
               }, 200);
            }
        });
    }

    $scope.getTeamGuID=function(data) {
        if(data.WinnerTeam == "Local"){
            $scope.formData['WinnerTeamGUID']= data.TeamGUIDLocal;
        }
        if(data.WinnerTeam == "Visitor"){
            $scope.formData['WinnerTeamGUID']= data.TeamGUIDVisitor;
        }
    }

    /*update Score data*/
    $scope.updateScore = function ()
    {
        $scope.editDataLoading = true;
        $scope.formData;
        var data = 'SessionKey='+SessionKey+'&'+$("form[name='update_score_form']").serialize();
        $http.post(API_URL_FOOTBALL+'manageLiveScoring', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                // $scope.data.dataList[$scope.data.Position] = response.Data;
                $scope.applyFilter($scope.Status, $scope.SeasonID, $scope.LeagueGUID);
                $('.modal-header .close').click();
            }else{
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;          
        });
    }
}); 
