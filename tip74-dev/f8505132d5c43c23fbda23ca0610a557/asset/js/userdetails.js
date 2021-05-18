app.controller('PageController', function($scope, $http, $timeout) {

    /*list append*/
    $scope.getUserDetails = function() {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey + '&IsAdmin=No&UserGUID=' + getQueryStringValue('UserGUID') +'&' +'Params=RegisteredOn,LastLoginDate,UserTypeName, FullName, Email, Username, ProfilePic, Gender, BirthDate, PhoneNumber, PhoneCode, Status, ReferredCount,StatusID,PanStatus,BankStatus,WalletAmount,WinningAmount,CashBonus,TotalCash,Address,Address1,IBAN,RoutingCode,SwiftCode&'+$('#filterForm').serialize();

        $http.post(API_URL + 'users/getProfile', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data) { /* success case */
                $scope.userData = response.Data;
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }
    $scope.UserGUID = getQueryStringValue('UserGUID');
    /*load edit form*/
    $scope.loadFormEdit = function(Position, UserGUID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/edit_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=Status,ProfilePic,MediaPAN,MediaBANK', contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#edit_model').modal({
                    show: true
                });
                $timeout(function() {
                    $(".chosen-select").chosen({
                        width: '100%',
                        "disable_search_threshold": 8,
                        "placeholder_text_multiple": "Please Select",
                    }).trigger("chosen:updated");
                }, 200);
            }
        });

    }

    /*list append*/
    $scope.transactions = [];
    $scope.getList = function(TransactionMode,NarrationMultiple = '') {
        
        var data = 'SessionKey=' + SessionKey + '&UserGUID='+getQueryStringValue('UserGUID')+'&IsAdmin=No&OrderBy=' + $scope.data.OrderBy + '&Sequence=' + $scope.data.Sequence + '&' +'Params=Amount,CurrencyPaymentGateway,TransactionType,TransactionID,Status,Narration,EntryDate,OpeningWalletAmount,WalletAmount,ClosingWalletAmount,TotalCash&Filter=FailedCompleted&Narration='+TransactionMode+'&NarrationMultiple='+NarrationMultiple+'&'+$('#filterForm').serialize();

        $http.post(API_URL + 'admin/wallet/getWallet', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records) { /* success case */
                $scope.transactions = response.Data.Records;
            }else if(response.Data.TotalRecords == 0){
                $scope.transactions = [];
            } else {
                $scope.data.noRecords = true;
            }
        });
    }

    /*Withdrawal list append*/
    $scope.WithdrawalsTransactions = [];
    $scope.getWithdrawals = function() {
        
        var data = 'SessionKey=' + SessionKey + '&UserGUID='+getQueryStringValue('UserGUID')+'&Params=Amount,Comments,PaymentGateway,EntryDate,Status&OrderBy=EntryDate&Sequence=DESC&'+$('#filterForm').serialize();

        $http.post(API_URL + 'admin/wallet/getWithdrawals', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records) { /* success case */
                $scope.WithdrawalsTransactions = response.Data.Records;
            } else {
                $scope.data.noRecords = true;
            }
        });
    }
    /**
     * show bank info
     */
    $scope.openBankInfo = function(){
        $('#bankInfo_model').modal({
            show: true
        });
    }
});