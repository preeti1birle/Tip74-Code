app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 100;
    $scope.data.ParentCategoryGUID = ParentCategoryGUID;
    /*----------------*/

    /*list append*/
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+$('#filterForm').serialize();
        $http.post(API_URL+'admin/config/bannerList', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                 $scope.data.dataList = response.Data.Records;
                /*for (var i in response.Data.Records) {
                    $scope.data.dataList.push(response.Data.Records[i]);
                }
                $scope.data.pageNo++;               */
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


    /*load add form*/
    $scope.loadFormAdd = function (Position, CategoryGUID)
    {
        $scope.templateURLAdd = PATH_TEMPLATE+module+'/add_form.htm?'+Math.random();
        $('#add_model').modal({show:true});
        $timeout(function(){            
           $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
       }, 200);
    }

    /*add data*/
    $scope.addData = function ()
    {
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&Section=Banner&'+$("form[name='add_form']").serialize();
        $http.post(API_URL+'admin/config/addBanner', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                window.location.reload();
                $('.modal-header .close').click();
                $scope.applyFilter();
            }else{
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;          
        });
    }

    /*load delete form*/
    $scope.loadFormDelete = function (Position, MediaGUID)
    {
        $scope.data.Position = Position;
        $scope.templateURLDelete = PATH_TEMPLATE+module+'/delete_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL+'upload/delete', 'SessionKey='+SessionKey+'&MediaGUID='+MediaGUID, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#delete_model').modal({show:true});
                $timeout(function(){            
                   $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
               }, 200);
            }
        });
    }    

    /*edit Banner Sort*/
    $scope.editBannerSort = function (MediaGUID) {
        $("#text_sort_id_" + MediaGUID).hide();
        $("#input_sort_id_" + MediaGUID).show();
        $("#editBtn_" + MediaGUID).hide();
        $("#submitBtn_" + MediaGUID).show();
    }

    /*Update Banner Sort*/
    $scope.submitBannerSort = function (MediaGUID) {
        $scope.addDataLoading = true;
        var inputSort = $("#banner_sort_id_" + MediaGUID).val()
        var data = 'SessionKey=' + SessionKey + '&Section=Banner&MediaGUIDs=' + MediaGUID + '&SortBy=' + inputSort;
        $http.post(API_URL + 'admin/config/updateBannerSort', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                window.location.reload();
            } else {
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;
        });
    }
});