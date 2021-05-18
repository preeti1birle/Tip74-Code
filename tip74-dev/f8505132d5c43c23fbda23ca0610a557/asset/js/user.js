app.controller('PageController', function ($scope, $http, $timeout) {

    /*list*/
    $scope.applyFilter = function () {
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList();
    }

    /*list append*/
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

    $scope.st = "verified";
    $scope.groupFilter = "All";
    if (getQueryStringValue('pending')) {
        $scope.st = "pending";
        $scope.groupFilter = "Group";
    }

    /*list append*/
    $scope.getList = function () {

        if (getQueryStringValue('Type')) {
            var ListType = getQueryStringValue('Type');
        } else {
            var ListType = '';
        }
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey + '&ListType=' + ListType + '&groupFilter=' + $scope.groupFilter + '&UserTypeID=2&IsAdmin=No&UserTypeID=2&PageNo=' + $scope.data.pageNo + '&PageSize=' + $scope.data.pageSize + '&OrderBy=' + $scope.data.OrderBy + '&Sequence=' + $scope.data.Sequence + '&' + 'Params=RegisteredOn,EmailForChange,EmailStatus,PhoneCode ,PhoneNumberForChange,PhoneStatus,LastLoginDate,UserTypeName, FullName, Email, Username, ProfilePic, Gender, BirthDate, PhoneNumber, Status, ReferredCount,StatusID&' + $('#filterForm1').serialize() + '&' + $('#filterForm').serialize();

        $http.post(API_URL + 'admin/users', data, contentType).then(function (response) {
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

    /*Resend Mail*/
    $scope.ResendVerificationMail = function (UserGUID) {
        var data = 'SessionKey=' + SessionKey + '&Type=Email&UserGUID=' + UserGUID;
        $http.post(API_URL + 'signup/resendVerification', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
            } else {
                alertify.error(response.Message);
            }
        });
    }

    /*Resend Mail*/
    $scope.sendUserMsgMobileVerification = function (MobileNumber) {
        var data = 'SessionKey=' + SessionKey + '&MobileNumber=' + MobileNumber;
        $http.post(API_URL + 'signup/sendUserMsgMobileVerification', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
            } else {
                alertify.error(response.Message);
            }
        });
    }

    /*Export List*/
    $scope.ExportList = function () {
        if (getQueryStringValue('pending')) {
            var UserType = getQueryStringValue('pending');
        } else {
            var UserType = '';
        }
        var data = 'SessionKey=' + SessionKey + '&UserType=' + UserType + '&IsAdmin=No&Params=Email,EmailForChange,FirstName,LastName,Address,Address1,CityName,StateName,CountryName,Postal,PhoneNumber,RegisteredOn,PhoneNumberForChange&' + $('#filterForm1').serialize() + '&' + $('#filterForm').serialize();
        $http.post(API_URL + 'admin/users/export_Users_list_csv', data, contentType).then(function (response) {
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

    /*load edit form*/
    $scope.loadFormEdit = function (Position, UserGUID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/edit_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=isWithdrawal,ReferralCode,IsPrivacyNameDisplay,Status,ProfilePic,MediaPAN,MediaBANK', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
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

    /*load edit form*/
    $scope.loadFormChangePassword = function (Position, UserGUID) {
        $scope.data.Position = Position;
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=Status,ProfilePic,MediaPAN,MediaBANK', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.ChangePasswordformData = response.Data
                $('#changeUserPassword_form').modal({
                    show: true
                });
            }
        });

    }

    /*load edit form*/
    $scope.loadFormAddCash = function (Position, UserGUID) {

        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/addCashBonus_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=FirstName,ProfilePic,Status', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#AddCashBonus_model').modal({
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
    /*loadFormAddCashDeposit edit form*/
    $scope.loadFormAddCashDeposit = function (Position, UserGUID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/verifyOtp.htm?' + Math.random();
        $http.post(API_URL + 'admin/signin/SentOtp', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=FirstName,ProfilePic,Status', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $scope.UserGUIDS = response.UserGUID;
                $('#verifyOtp_model').modal({
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


    $scope.addCashDepositMoney = function (Position, UserGUID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/addCashBonusDeposit_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=FirstName,ProfilePic,Status', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#AddCashBonusDeposit_model').modal({
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

    $scope.verifyOTP = function (UserGUID) {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&Status=Completed&Narration=Admin Deposit Money&' + $('#verify_form').serialize();
        $http.post(API_URL + 'admin/signin/VerifyOtp', data, contentType).then(function (response) {
            var response = response.data;
            console.log(response.ResponseCode);
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.addCashDepositMoney('0', UserGUID);
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*load edit form*/
    $scope.loadFormReferredUsersList = function (Position, UserGUID) {

        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/referredUserlist_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'admin/users/getReferredUsers', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=FirstName,ProfilePic,Email,Status, Gender, BirthDate, PhoneNumber', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#referralUserList_model').modal({
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

    /*load verification form*/
    $scope.loadFormVerification = function (Position, UserGUID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/verification_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=Status,ProfilePic,MediaPAN,MediaBANK,PanStatus,BankStatus', contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data;


                console.log($scope.formData);
                $('#Verification_model').modal({
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


    /*edit data*/
    $scope.editData = function () {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $('#edit_form').serialize();
        $http.post(API_URL + 'admin/users/changeStatus', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.data.dataList[$scope.data.Position].Status = response.Data.Status;
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    /*change password*/
    $scope.changeUserPassword = function () {
        $scope.changeCP = true;
        var data = 'SessionKey=' + SessionKey + '&' + $('#changePassword_form').serialize();
        $http.post(API_URL + 'users/changePasswordAdmin', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $('.modal-header .close').click();
                alertify.success(response.Message);
            } else {
                alertify.error(response.Message);
            }
            $scope.changeCP = false;
        });
    }

    /*add cash bonus data*/
    $scope.addCashBonus = function () {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&Status=Completed&Narration=Admin Cash Bonus&' + $('#addCash_form').serialize();
        $http.post(API_URL + 'admin/users/addCashBonus', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.data.dataList[$scope.data.Position].Status = response.Data.Status;
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }
    /*add cash deposit data*/
    $scope.addCashDeposit = function () {
        $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&Status=Completed&Narration=Admin Deposit Money&' + $('#addCashDeposit_form').serialize();
        $http.post(API_URL + 'admin/users/addCashDeposit', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.data.dataList[$scope.data.Position].Status = response.Data.Status;
                $('.modal-header .close').click();
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }


    /*edit data*/
    $scope.verifyDetails = function (UserGUID, VetificationType, Status) {
        $scope.editDataLoading = true;
        if (VetificationType == 'PAN') {
            var Params = '&PanStatus=' + Status;
        } else {
            var Params = '&BankStatus=' + Status;
        }
        var data = 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&VetificationType=' + VetificationType + Params;
        $http.post(API_URL + 'admin/users/changeVerificationStatus', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);

            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }


    /*load delete form*/
    $scope.loadFormDelete = function (Position, UserGUID) {
        $scope.data.Position = Position;
        $scope.templateURLDelete = PATH_TEMPLATE + module + '/delete_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params=Status,ProfilePic', contentType).then(function (response) {
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



});