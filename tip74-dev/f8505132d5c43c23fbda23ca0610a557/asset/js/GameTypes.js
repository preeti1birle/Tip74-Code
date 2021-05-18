app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 100;
    $scope.data.ParentCategoryGUID = ParentCategoryGUID;
    /*----------------*/

    /*list append*/
    $scope.AndroidFetures = '';
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey;
        $http.post(API_URL+'admin/config/getGameTypes', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                    if(response.Data.Records[i].ConfigTypeGUID == 'AndroidAppFeatures'){
                        $scope.AndroidFetures = response.Data.Records[i].ConfigTypeValue;
                    }
                 $scope.data.dataList.push(response.Data.Records[i]);
             }
             $scope.data.pageNo++;               
         }else{
            $scope.data.noRecords = true;
        }
        $scope.data.listLoading = false;

    });
    }


    /*add data*/
    $scope.updateConfigData = function (GameTypeID,Name,Status)
    {
        // console.log(ConfigTypeGUID+' '+ConfigValue+' '+ConfigStatus); return false;
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+'GameTypeID='+GameTypeID+'&Name='+Name+'&Status='+Status;
        $http.post(API_URL+'admin/config/updateGameType', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                if (ConfigTypeGUID == 'AndroidAppVersion') {
                    $scope.openAddFeatureModel();
                } else {
                    $scope.applyFilter();
                    $('.modal-header .close').click();
                }
            } else {
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;          
        });
    }

    /*load add form*/
    $scope.loadFormAdd = function (Position, CategoryGUID)
    {
        $scope.SeriesGUID = '';
        $scope.MatchData = {};
        $scope.templateURLAdd = PATH_TEMPLATE + module + '/add_form.htm?' + Math.random();
        $('#add_model').modal({show: true});
    }

    $scope.addData = function ()
    {
        $scope.addDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&'+ $("form[name='add_form']").serialize();
        $http.post(API_URL + 'admin/config/addGameType', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.addDataLoading = false;
                $('.modal-header .close').click();
                window.location.reload();
            } else {
                $scope.addDataLoading = false;
                alertify.error(response.Message);
            }
        });
        $scope.addDataLoading = false;
    }
}); 

