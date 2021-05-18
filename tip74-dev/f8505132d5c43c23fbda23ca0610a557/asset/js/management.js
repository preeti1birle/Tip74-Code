app.controller('PageController', function ($scope, $http,$timeout){
    $scope.pageSize = 15;
    $scope.pageNo = 1;
     /*list*/
    $scope.applyFilter = function (type) {
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.pageNo = 1;
        if(type === 'Horse') {
            $scope.getHorseList();
        } else if(type === 'Jockey') {
            $scope.getJockeyList();
        } else if(type === 'Trainer') {
            $scope.getTrainerList();
        }
    }

    $scope.getHorseList = function() {
        if ($scope.data.listLoading || $scope.data.noRecords) {
            return;
        }
        $scope.data.listLoading = true;
        var data =  'SessionKey='+SessionKey+'&Params=Description,HorseName,Age&PageNo=' + $scope.pageNo + '&PageSize=' + $scope.pageSize;
        $http.post(API_URL+'admin/management/getHorseList', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                 $scope.data.dataList.push(response.Data.Records[i]);
                }
                $scope.pageNo++;               
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }

    /*load edit form*/
    $scope.loadFormAdd = function (Position, StoreGUID) {
        $scope.templateURLAdd = PATH_TEMPLATE+module+'/add_form.htm?'+Math.random();
        $('#add_model').modal({show:true});
    //     $timeout(function(){        
    //        $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
    //        $('input[name=CouponValidTillDate]').datetimepicker({format: "yyyy-mm-dd",minView: 2, startDate: new Date()});
    //    }, 500);
    }

    /*load edit form*/
    $scope.loadFormEdit = function (Position, HorseGUID, type){
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE+module+'/edit_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        if(type === 'Horse') {
           var url = 'admin/management/getHorseList';
           var val = 'HorseGUID='+HorseGUID;
           var params = 'Description,HorseName,Age';
        } else if(type === 'Jockey') {
            var url = 'admin/management/getJockeyList';
            var val = 'JockeyGUID='+HorseGUID;
            var params = 'JockeyName';
        } else if(type === 'Trainer') {
            var url = 'admin/management/getTrainerList';
            var val = 'TrainerGUID='+HorseGUID;
            var params = 'TrainerName';
        }
        $http.post(API_URL+url,val+'&Params='+ params + '&SessionKey='+SessionKey, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data;
                $('#edit_model').modal({show:true});
            }
        });
    }

    /*load delete form*/
    $scope.loadFormDelete = function (Position, HorseGUID, type) {
        $scope.data.Position = Position;
        $scope.templateURLDelete = PATH_TEMPLATE + module + '/delete_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        if(type === 'Horse') {
            var url = 'admin/management/getHorseList';
            var val = 'HorseGUID='+HorseGUID;
            var params = 'Description,HorseName,Age';
         } else if(type === 'Jockey') {
             var url = 'admin/management/getJockeyList';
             var val = 'JockeyGUID='+HorseGUID;
             var params = 'JockeyName';
         } else if(type === 'Trainer') {
             var url = 'admin/management/getTrainerList';
             var val = 'TrainerGUID='+HorseGUID;
             var params = 'TrainerName';
         }
        $http.post(API_URL+url,val+'&Params='+ params + '&SessionKey='+SessionKey, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#delete_model').modal({
                    show: true
                });
                $timeout(function () {
                    $(".chosen-select").chosen({
                        width: '100%',
                        "disable_search_threshold": 8,
                        "placeholder_text_multiple": "Please Select",
                    }).trigger("chosen:updated");
                }, 200);
            }
        });

    }

    /*delete selected */
    $scope.deleteSelectedRecords = function () {
        alertify.confirm('Are you sure you want to delete?', function () {
            var data = 'SessionKey=' + SessionKey + '&' + $('#records_form').serialize();
            $http.post(API_URL + 'admin/entity/deleteSelected', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) { /* success case */
                    alertify.success(response.Message);
                    $scope.applyFilter();
                    window.location.reload();
                } else {
                    alertify.error(response.Message);
                }
                if ($scope.data.totalRecords == 0) {
                    $scope.data.noRecords = true;
                }
            });
        }).set('labels', {
            ok: 'Yes',
            cancel: 'No'
        });
    }

    /*add data*/
    $scope.addData = function (type){
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+$("form[name='add_form']").serialize();
        var url = '';
        if(type === 'Horse') {
            url = 'admin/management/addHorse';
        } else if(type === 'Jockey') {
            url = 'admin/management/addJockey';
        } else if(type === 'Trainer') {
            url = 'admin/management/addTrainer';
        }
        $http.post(API_URL+url, data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                $scope.applyFilter(type);
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;          
        });
    }

     /*edit data*/
    $scope.editData = function(type) {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $('#edit_form').serialize();
        if(type === 'Horse') {
            var url = 'admin/management/editHorse';
        } else if(type === 'Jockey') {
            var url = 'admin/management/editJockey';
        }else if(type === 'Trainer') {
            var url = 'admin/management/editTrainer';
        }
        $http.post(API_URL + url, data, contentType).then(function(response) {
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

    // getJockeyList
    /* ---------------------For Jockey Management----------------------------------------------*/
    $scope.getJockeyList = function() {
        if ($scope.data.listLoading || $scope.data.noRecords) {
            return;
        }
        $scope.data.listLoading = true;
        var data =  'SessionKey='+SessionKey+'&Params=JockeyName&PageNo=' + $scope.pageNo + '&PageSize=' + $scope.pageSize;
        $http.post(API_URL+'admin/management/getJockeyList', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                 $scope.data.dataList.push(response.Data.Records[i]);
                }
             $scope.pageNo++;               
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }

     // getJockeyList
    /* ---------------------For Trainer Management----------------------------------------------*/
    $scope.getTrainerList = function() {
        if ($scope.data.listLoading || $scope.data.noRecords) {
            return;
        }
        $scope.data.listLoading = true;
        var data =  'SessionKey='+SessionKey+'&Params=TrainerName&PageNo=' + $scope.pageNo + '&PageSize=' + $scope.pageSize;
        $http.post(API_URL+'admin/management/getTrainerList', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                 $scope.data.dataList.push(response.Data.Records[i]);
                }
             $scope.pageNo++;               
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }
});
