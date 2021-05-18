app.controller('PageController', function ($scope, $http, $timeout , $rootScope) {
    $scope.data.pageSize = 15;
    /*----------------*/
    /*list*/
    $scope.applyFilter = function (Status) {
        $rootScope.Status = Status;
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList(true);
    }
    $rootScope.Status = 'Completed';
    $scope.TransactionMode = 'All';
    /*list append*/
    $scope.getList = function (status,Status)
    {
        if(status){
            $scope.data.dataList=[];
            $scope.data.pageNo = 1;
        }
        
        if (getQueryStringValue('Type')) {
            var ListType = getQueryStringValue('Type');
        } else {
            var ListType = '';
        }
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey + '&ListType=' + ListType + '&Params=ProfilePic,Amount,Email,PhoneNumber,PaytmPhoneNumber,PaymentGateway,Status,EntryDate,FirstName,MediaBANK&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize + '&OrderBy=EntryDate&Sequence=DESC&' + $('#filterForm1').serialize() + '&' + $('#filterForm').serialize()+"&Status="+$rootScope.Status;
        $http.post(API_URL + 'admin/users/getWithdrawals', data, contentType).then(function (response) {
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
        });
    }

    /*load edit form*/
    $scope.loadFormEdit = function (Position, WithdrawalID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/edit_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'admin/users/getWithdrawal', 'SessionKey=' + SessionKey + '&WithdrawalID=' + WithdrawalID + '&Params=Params=Amount,PaymentGateway,Status,EntryDate,FirstName,Email,PhoneNumber,ProfilePic,MediaBANK,UserID', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data.Records[0];

                $('#edit_model').modal({
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

    /*edit data*/
    $scope.editData = function (Status, WithdrawalID) {
        $scope.editDataLoading = true;
        var person = '';
        if (Status == 'Rejected') {
            var person = prompt("Reason for Rejection-", '');
            if (person == null || person == '') {
                alertify.error("Reject Reason is Required");
                $scope.editDataLoading = false;
                return false;
            }
        } else if (Status == 'Verified') {
            var r = confirm("Are you sure? You want to Verify!");
            if (r != true) {
                return false;
            }
        }
        var data = 'SessionKey=' + SessionKey + '&Status=' + Status + '&WithdrawalID=' + WithdrawalID + '&Comments=' + person + '&Params=WithdrawalID,Amount,PaymentGateway,Status,EntryDate,FirstName,Email,PhoneNumber&' + $('#edit_form').serialize();
        $http.post(API_URL + 'admin/users/changeWithdrawalStatus', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.getList(true);
//                $timeout(function () {
//                    location.reload();
//                }, 200);
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*Export List*/
    $scope.ExportExcel = function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
                base64 = function (s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                },
                format = function (s, c) {
                    return s.replace(/{(\w+)}/g, function (m, p) {
                        return c[p];
                    })
                }
        /*$('#WithdrawalList').find("td").last().remove();*/
        var toExcel = document.getElementById("WithdrawalList").innerHTML;
        var ctx = {
            worksheet: name || '',
            table: toExcel
        };
        var link = document.createElement("a");
        link.download = "Withdrawal-List.xlsx";
        link.href = uri + base64(format(template, ctx))
        link.click();
    }

    $scope.ExportList = function (extention) {
        var data = 'SessionKey=' + SessionKey + '&Params=Amount,Email,PhoneNumber,PaymentGateway,Status,EntryDate,FirstName,MediaBANK&' + $('#filterForm1').serialize();
        $http.post(API_URL + 'admin/users/export_Withdrawal_list_csv', data, contentType).then(function (response) {
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
    }
});
