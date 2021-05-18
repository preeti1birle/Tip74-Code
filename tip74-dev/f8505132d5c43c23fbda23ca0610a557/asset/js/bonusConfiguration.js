app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 100;
    $scope.data.ParentCategoryGUID = ParentCategoryGUID;
    /*----------------*/

    /*list append*/
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey;
        $http.post(API_URL+'admin/config/getConfigs', data, contentType).then(function(response) {
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


    /*add data*/
    $scope.updateConfigData = function (ConfigTypeGUID,ConfigValue,ConfigStatus)
    {
        // console.log(ConfigTypeGUID+' '+ConfigValue+' '+ConfigStatus); return false;
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+'ConfigTypeGUID='+ConfigTypeGUID+'&ConfigTypeValue='+ConfigValue+'&Status='+ConfigStatus;
        $http.post(API_URL+'admin/config/update', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                $scope.applyFilter();
                $('.modal-header .close').click();
            }else{
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;          
        });
    }


}); 

