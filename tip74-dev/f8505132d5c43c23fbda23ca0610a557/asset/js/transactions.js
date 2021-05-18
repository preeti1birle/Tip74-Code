app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 15;
    /*----------------*/
     /*list*/
    $scope.applyFilter = function() {
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList();
    }


    $scope.getUserInfo = function(){
        $scope.userData = {};
        var UserGUID = getQueryStringValue('UserGUID');
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=Status,ProfilePic,MediaPAN,MediaBANK', contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.userData = response.Data;
            }
        });
    }

    $scope.TransactionMode = 'All';
    /*list append*/
    $scope.getList = function ()
    {
        $scope.getUserInfo();
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&UserGUID='+getQueryStringValue('UserGUID')+'&TransactionMode='+$scope.TransactionMode+'&Params=EntityID,Amount,CurrencyPaymentGateway,TransactionType,TransactionID,Status,Narration,OpeningBalance,ClosingBalance,EntryDate,WalletAmount,WinningAmount,CashBonus&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize + '&OrderBy=' + $scope.data.OrderBy + '&Sequence=' + $scope.data.Sequence + '&' + $('#filterForm').serialize();
        $http.post(API_URL+'admin/users/getWallet', data, contentType).then(function(response) {
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

    /*Export List*/
    $scope.ExportList = function() {
        var data = 'SessionKey='+SessionKey+'&UserGUID='+getQueryStringValue('UserGUID')+'&TransactionMode='+$scope.TransactionMode+'&Params=Amount,CurrencyPaymentGateway,TransactionType,TransactionID,Status,Narration,OpeningBalance,ClosingBalance,EntryDate,WalletAmount,WinningAmount,CashBonus&' + $('#filterForm').serialize();
        $http.post(API_URL + 'admin/users/export_Transactions_list', data, contentType).then(function(response) {
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
