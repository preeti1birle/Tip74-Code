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
        $http.post(API_URL+'admin/config/getConfigs', data, contentType).then(function(response) {
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
    $scope.updateConfigData = function (ConfigTypeGUID,ConfigValue,ConfigStatus)
    {
        // console.log(ConfigTypeGUID+' '+ConfigValue+' '+ConfigStatus); return false;
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+'ConfigTypeGUID='+ConfigTypeGUID+'&ConfigTypeValue='+ConfigValue+'&Status='+ConfigStatus;
        $http.post(API_URL+'admin/config/update', data, contentType).then(function(response) {
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

    /**
     * initialize tinymac on click
     */
    $scope.editor = '';
    $scope.openAddFeatureModel = function () {
        tinymce.init({
            selector: '#editor',
            font_size_classes: "fontSize1, fontSize2, fontSize3, fontSize4, fontSize5, fontSize6",
            plugins: [
                "advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                // "save directionality template paste textcolor code"
            ],
            menubar: false,
            toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist  | sizeselect | fontsize | fontsizeselect",
            image_title: true,
        });
        tinymce.get("editor").setContent($scope.AndroidFetures);
        $('#appAPKUpdateMsg_model').modal({
            show: true
        });
    }
    /**
     * Save apk update info
     */
    $scope.saveAPKUpdates = function (form) {
        // console.log(form.serialize());
        var ConfigTypeGUID = 'AndroidAppFeatures';
        var ConfigValue = (tinyMCE.get('editor').getContent());
        var ConfigStatus = 'Active';
        // console.log(tinyMCE.get('editor').getContent());
        $scope.addDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + 'ConfigTypeGUID=' + ConfigTypeGUID + '&ConfigTypeValue=' + ConfigValue.trim() + '&Status=' + ConfigStatus;
        $http.post(API_URL + 'admin/config/update', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.AndroidFetures = ConfigValue;
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;
        });
    }


}); 

