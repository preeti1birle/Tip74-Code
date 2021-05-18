app.controller('PageController', function ($scope, $http, $timeout) {
    $scope.data.pageSize = 15;
    /*----------------*/
    $scope.getFilterData = function ()
    {
        var data = 'SessionKey=' + SessionKey + '&StatusID=2&Params=SeriesName,SeriesGUID&' + $('#filterPanel form').serialize();
        $http.post(API_URL_FOOTBALL + 'admin/matches/getFilterData', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data) {
                /* success case */
                $scope.filterData = response.Data;
                $timeout(function () {
                    $("select.chosen-select").chosen({width: '100%', "disable_search_threshold": 8}).trigger("chosen:updated");
                }, 300);
            }
        });
    }

    $scope.applyOrderedList = function (OrderBy, Sequence) {
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

    $scope.Playing11Notification = function (MatchGUID)
    {
        alertify.confirm('Are you sure you want sent notifiaction?', function() {
            var data = 'SessionKey=' + SessionKey + '&MatchGUID=' + MatchGUID;
            $http.post(API_URL_FOOTBALL + 'admin/matches/playing11NotificationAdmin', data, contentType).then(function(response) {
                var response = response.data;
                if (response.ResponseCode == 200) { /* success case */
                    alertify.success(response.Message);
                } else {
                    alertify.error(response.Message);
                }
            });
        })
    }

    $scope.getTeamData = function (SeriesGUID, LocalTeamGUID = '')
    {
        var data = 'SessionKey=' + SessionKey + '&LocalTeamGUID=' + LocalTeamGUID + '&SeriesGUID=' + SeriesGUID + '&Params=TeamNameLocal,TeamNameVisitor,TeamGUID&' + $('#filterPanel form').serialize();
        $http.post(API_URL_FOOTBALL + 'admin/matches/getTeamData', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data) {
                /* success case */
                if (LocalTeamGUID != '') {
                    $scope.VisitorTeamData = response.Data;
                } else {
                    $scope.LocalTeamData = response.Data;
                }
                $timeout(function () {
                    $("select.chosen-select").chosen({width: '100%', "disable_search_threshold": 8}).trigger("chosen:updated");
                }, 300);
            }
        });
    }

    /*list*/
    $scope.applyFilter = function (Status)
    {
        $scope.Status = Status;
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList();
    }

    /*list append*/
    $scope.Status = 1;
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey + '&Filter=AddDays' + '&OrderBy=' + $scope.data.OrderBy + '&Sequence=' + $scope.data.Sequence + '&OrderByToday=Yes&Params=' + 'SeriesName,MatchDisplay,IsPlayingXINotificationSent,IsEdited,TotalPlayerCountMatch,TotalPlayerCountPrivate,MatchType,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation,Status&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize + '&' + $('#filterForm').serialize() + '&' + $('#filterForm1').serialize() + '&StatusID=' + $scope.Status;
        $http.post(API_URL_FOOTBALL + 'getMatches', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records) { /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                    $scope.data.dataList.push(response.Data.Records[i]);
                }
                $scope.data.pageNo++;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
            // setTimeout(function(){ tblsort(); }, 1000);
        });
    }

    /*load add form*/
    $scope.loadFormAdd = function (Position, CategoryGUID)
    {
        $scope.templateURLAdd = PATH_TEMPLATE + module + '/add_form.htm?' + Math.random();
        $('#add_model').modal({show: true});
        $timeout(function () {
            $(".chosen-select").chosen({width: '100%', "disable_search_threshold": 8, "placeholder_text_multiple": "Please Select", }).trigger("chosen:updated");
        }, 200);
    }



    /*load edit form*/
    $scope.loadFormEdit = function (Position, MatchGUID)
    {

        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/edit_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL_FOOTBALL + 'admin/matches/getMatch', 'MatchGUID=' + MatchGUID + '&Params=Status,MatchClosedInMinutes,SeriesName,MatchType,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation&SessionKey=' + SessionKey, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#edit_model').modal({show: true});
                $timeout(function () {
                    $(".chosen-select").chosen({width: '100%', "disable_search_threshold": 8, "placeholder_text_multiple": "Please Select", }).trigger("chosen:updated");
                }, 200);
            }
        });
    }

    /*load delete form*/
    $scope.loadFormDelete = function (Position, CategoryGUID)
    {
        $scope.data.Position = Position;
        $scope.templateURLDelete = PATH_TEMPLATE + module + '/delete_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL_FOOTBALL + 'category/getCategory', 'SessionKey=' + SessionKey + '&CategoryGUID=' + CategoryGUID, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#delete_model').modal({show: true});
                $timeout(function () {
                    $(".chosen-select").chosen({width: '100%', "disable_search_threshold": 8, "placeholder_text_multiple": "Please Select", }).trigger("chosen:updated");
                }, 200);
            }
        });
    }

    /*edit data*/
    $scope.editData = function ()
    {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $("form[name='edit_form']").serialize();
        $http.post(API_URL_FOOTBALL + 'admin/matches/changeStatus', data, contentType).then(function (response) {
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

    $scope.ExportExcelMatches = function () {

        $http.post(API_URL + 'football/admin/matches/getMatchesExport', 'SessionKey=' + SessionKey +'&'+$('#filterForm1').serialize() + '&' + $('#filterForm').serialize()+'&StatusID=1', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                var encodedUri = encodeURI(response.Data);
                var link = document.createElement("a");
                link.href = encodedUri;
                link.style = "visibility:hidden";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                alertify.success(response.Message);
            } else {
                alertify.error(response.Message);
            }
        });
            //     var response = response.data;
        //     if (response.ResponseCode == 200) { /* success case */
        //         $scope.data.pageLoading = false;
        //         $scope.formData = response.Data
        //         $('#delete_model').modal({show: true});
        //         $timeout(function () {
        //             $(".chosen-select").chosen({width: '100%', "disable_search_threshold": 8, "placeholder_text_multiple": "Please Select", }).trigger("chosen:updated");
        //         }, 200);
        //     }
        // });
    }

    $scope.loadFormStatus = function (Position, MatchGUID)
    {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/updateStatus_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'football/admin/matches/getMatch', 'SessionKey=' + SessionKey + '&MatchGUID=' + MatchGUID + '&Params=MatchDisplay,SeriesName,MatchType,MatchNo,MatchStartDateTime,IsPlayingXINotificationSent,Status,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,MatchLocation', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                console.log($scope.formData)

                $('#status_model').modal({show: true});

                $timeout(function () {

                    $(".chosen-select").chosen({width: '100%', "disable_search_threshold": 8, "placeholder_text_multiple": "Please Select", }).trigger("chosen:updated");
                }, 200);
            }
        });
    }

    $scope.editStatusMatchDisplay = function (StatusID, MatchGUID)
    {
        $scope.editDataLoading = true;
            var data = 'SessionKey=' + SessionKey + '&MatchGUID=' + MatchGUID + '&MatchDisplay=' + StatusID;
            $http.post(API_URL + 'football/admin/matches/editStatusMatchDisplay', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) { /* success case */
                    alertify.success(response.Message);
                    // $scope.data.dataList[$scope.data.Position] = response.Data;
                    $('.modal-header .close').click();
                    window.location.reload();
                } else {
                    alertify.error(response.Message);
                }
                $scope.editDataLoading = false;
            });
    }

     $scope.makeLive = function (MatchGUID)
    {
        alertify.confirm('Are you sure you want go live?', function() {
            var data = 'SessionKey=' + SessionKey + '&MatchGUID=' + MatchGUID + '&Status=Running';
                $http.post(API_URL + 'football/admin/matches/goLive', data, contentType).then(function(response) {
                var response = response.data;
                if (response.ResponseCode == 200) { /* success case */
                    alertify.success(response.Message);
                    window.location.reload();
                } else {
                    alertify.error(response.Message);
                }
            });
        })
    }
});
