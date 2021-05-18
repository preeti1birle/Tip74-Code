app.controller('PageController', function ($scope, $http, $timeout) {
    $scope.data.pageSize = 15;
    $scope.EntriesList = [];
    $scope.getEntriesList = function () {
        var data = 'SessionKey=' + SessionKey + '&Params=NoOfEntries,NoOfPrediction,EntriesAmount,NoOfDoubleUps,CreatedDate';
        $http.post(API_URL + 'admin/entries/packages', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records) {
                $scope.EntriesList = response.Data;
            }
        });
    }
    /**
     * delete entry 
     */
    $scope.deleteEntry = function (EntriesID) {
        var status = confirm('Are you sure, do you want to delete?');
        if (status) {
            var data = 'SessionKey=' + SessionKey + '&EntriesID='+EntriesID;
            $http.post(API_URL + 'admin/entries/deletePackage', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) {
                    $scope.getEntriesList();
                }
            });
        }
    }
    /**
     * load add form
     */
    $scope.loadFormAdd = function(){
        $scope.EditInfo = {};
        $scope.templateURLadd = PATH_TEMPLATE + module + '/add_form.htm?' + Math.random();
        $('#add_model').modal({ show: true });
    }
    /**
     * add edit
     */
    $scope.addData = function(){
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $("form[name='add_form']").serialize();
        $http.post(API_URL + 'admin/entries/addPackage', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.getEntriesList();
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }
    /*load edit form*/
    $scope.loadFormEdit = function (data) {
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/edit_form.htm?' + Math.random();
        var data = 'EntriesID='+data.EntriesID+'&SessionKey=' + SessionKey + '&Params=NoOfEntries,NoOfPrediction,EntriesAmount,NoOfDoubleUps,CreatedDate';
        $http.post(API_URL + 'admin/entries/packages', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) {
                $scope.EditInfo = response.Data;
                $scope.EditInfo.NoOfEntries = Number($scope.EditInfo.NoOfEntries);
                $scope.EditInfo.NoOfPrediction = Number($scope.EditInfo.NoOfPrediction);
                $scope.EditInfo.NoOfDoubleUps = Number($scope.EditInfo.NoOfDoubleUps);
                $scope.EditInfo.EntriesAmount = Number($scope.EditInfo.EntriesAmount);
                $('#edit_model').modal({ show: true });
            }
        });
    }

    /*edit data*/
    $scope.editData = function () {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $("form[name='edit_form']").serialize();
        $http.post(API_URL + 'admin/entries/editPackage', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.getEntriesList();
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

}); 
