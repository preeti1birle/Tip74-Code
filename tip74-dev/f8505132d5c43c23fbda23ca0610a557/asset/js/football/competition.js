app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 15;
    $scope.data.pageNo = 1;
    /*list*/
    $scope.applyFilter = function ()
    {
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList();
    }

    /*list append*/
    // $scope.Status = "Active";
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&Params=CompetitionName,CompetitionFlag,&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize+'&'+$('#filterForm').serialize();
        $http.post(API_URL_FOOTBALL+'getCompetitions', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                // for (var i in response.Data.Records) {
                //  $scope.data.dataList.push(response.Data.Records[i]);
                // }
                $scope.data.dataList=response.Data.Records;
                $scope.data.pageNo++;               
            }else{
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
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
        $http.post(API_URL + 'admin/Football/addCompetition', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.applyFilter();
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*load edit form*/
    $scope.loadFormEdit = function (Position, CompetitionGUID)
    {
        $scope.formData={};
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE+module+'/edit_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL_FOOTBALL+'getCompetitions','CompetitionGUID='+CompetitionGUID+'&Params=CompetitionName,CompetitionFlag,Status&SessionKey='+SessionKey, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data;
                $('#edit_model').modal({show:true});
            }
        });
    }

    // **
    //  * delete venue 
    //  */
    $scope.deleteCompetition = function (CompetitionGUID) {
        var status = confirm('Are you sure, do you want to delete?');
        if (status) {
            var data = 'SessionKey=' + SessionKey + '&CompetitionGUID='+CompetitionGUID;
            $http.post(API_URL + 'admin/Football/deleteCompetition', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) {
                    $scope.applyFilter();
                }
            });
        }
    }
     /*edit data*/
    $scope.editData = function() {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $('#edit_form').serialize();
        $http.post(API_URL_FOOTBALL + 'updateCompetition', data, contentType).then(function(response) {
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


