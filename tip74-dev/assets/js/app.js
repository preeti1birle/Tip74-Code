
var app = angular.module('TIP74', ['ngStorage', 'ngAnimate', 'toastr', 'socialLogin', 'ui.bootstrap.datetimepicker', 'ngSanitize']);
var contentType = {
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    }
};
/*main controller*/
app.controller('MainController', ["$scope", "$http", "$timeout", "$localStorage", "$sessionStorage", "appDB", "toastr", "$rootScope", "environment", function ($scope, $http, $timeout, $localStorage, $sessionStorage, appDB, toastr, $rootScope, environment) {
    $scope.data = { dataList: [], totalRecords: '0', pageNo: 1, pageSize: 25, noRecords: false, UserGUID: UserGUID, notificationCount: 0 };
    $scope.orig = angular.copy($scope.data);
    $scope.UserTypeID = UserTypeID;
    $scope.base_url = base_url;
    $scope.env = environment;
    $scope.isLoggedIn = $localStorage.isLoggedIn;
    $scope.amount = 100;
    $scope.Currency = '$';
    $rootScope.profileDetails = {};
    $scope.Colors = ['won', 'denied', 'lost']
    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.getWalletDetails = function () {
            var $data = {};
            $data.UserGUID = $localStorage.user_details.UserGUID;
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.Params = 'WithdrawText,Address,Address1,IBAN,RoutingCode,SwiftCode,UserEntriesBalance,PhoneStatus,UserName,FirstName,Email,ProfilePic,WalletAmount,WinningAmount,CashBonus,TotalCash,WeekData,WeekID';
            $data.WithdrawText = 'Yes';
            $scope.WeekGUID = getQueryStringValue("WeekGUID");
            appDB
                .callPostForm('users/getProfile', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $rootScope.profileDetails = data.Data;
                            $scope.WinningAmount = $rootScope.profileDetails.WinningAmount;
                            $scope.getSetting();
                            $scope.getWeekList();
                            if ($scope.getPageName() == 'prediction') {
                                $scope.getEntriesUpdate($scope.WeekGUID);
                            }
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    });
        }
        $scope.getWalletDetails();
        /**
         * get entries update
         */     
        $rootScope.WalletInfo = {};
        $scope.getEntriesUpdate = function (WeekGUID) {
            var $data = {};
            $data.WeekGUID = WeekGUID;
            $data.SessionKey = $scope.user_details.SessionKey;
            appDB
                .callPostForm($rootScope.apiPrefix + "entries/getUserBalance", $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $rootScope.WalletInfo = data.Data;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    });
        }
        
        $scope.getTimeZone = function() {
        var offset = new Date().getTimezoneOffset(),
            o = Math.abs(offset);
        return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
        }

        /**
         * get user balance
         */
        $scope.getUserBalance = function (SelectedWeekGUID, EntryNo) {
            var $data = {};
            $scope.WeekGUID = SelectedWeekGUID;
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.WeekGUID = SelectedWeekGUID;
            if(EntryNo) {
              $data.EntryNo = EntryNo;
            }
            appDB.callPostForm($rootScope.apiPrefix + "entries/getUserBalance", $data, contentType).then(function successCallback(data) {
              if ($scope.checkResponseCode(data)) {
                  setTimeout(function(){
                    //   $scope.$apply(function(){
                        $scope.UserEntriesBalance = data.Data;
                        $scope.$apply();
                    //   });
                  }, 00);
              }
            }, function errorCallback(data) {
              $scope.checkResponseCode(data);
            });
        }

        /**
         * get user entries balance
         */
        // $scope.getUserEntriesBalance = function (SelectedWeekGUID) {
        //     console.log('SelectedWeekGUID',SelectedWeekGUID)
        //     var $data = {};
        //     $data.SessionKey = $scope.user_details.SessionKey;
        //     appDB.callPostForm($rootScope.apiPrefix + "entries/getUserEntriesBalance", $data, contentType).then(function successCallback(data) {
        //       if ($scope.checkResponseCode(data)) {
        //         $scope.UnAssignedEntries  = Number(data.Data.UnAssignedEntries);
        //       }
        //     }, function errorCallback(data) {
        //       $scope.checkResponseCode(data);
        //     });
        // }
        $scope.getUserEntriesBalance = function (SelectedWeekGUID) {
            $scope.WeekGUID = SelectedWeekGUID;
            $scope.assigningEntries = "1";
            console.log('SelectedWeekGUID',SelectedWeekGUID)
            var $data = {};
            $data.Filter = 'UnAssigned';
            $scope.UnAssignedEntries = "";
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.Params = "WeekGUID,WeekCount,EntryNo,AllowedPredictions,ConsumedPredictions,AllowedPurchaseDoubleUps,TotalPurchasedDoubleUps,ConsumeDoubleUps";
            appDB.callPostForm($rootScope.apiPrefix + "entries/list", $data, contentType).then(function successCallback(data) {
              if ($scope.checkResponseCode(data)) {
                setTimeout(function(){
                    // $scope.$apply(function(){
                        $scope.UnAssignedEntries  = data.Data; 
                        $scope.$apply();
                }, 00);
                
              }
            }, function errorCallback(data) {
              $scope.checkResponseCode(data);
            });
        }

        /**
         * Get site setting
         */
        $rootScope.BonusExpireDays = 0;
        $rootScope.MinimumWithdarwalAmount = 0;
        $rootScope.MinimumDepositAmount = 0;
        $rootScope.MinimumWithdrawalLimitPaytm = 0;
        $rootScope.MaximumWithdrawalLimitPaytmPerDay = 0;
        $scope.getSetting = function () {
            $http.get($scope.env.api_url + 'Utilities/setting', contentType).then(function (response) {
                var response = response.data;
                if ($scope.checkResponseCode(response)) {
                    var data = response.Data.Records;
                    for (var i in data) {
                        if (data[i].ConfigTypeGUID == 'CashBonusExpireTimeInDays') {
                            $rootScope.BonusExpireDays = data[i].ConfigTypeValue;
                        } else if (data[i].ConfigTypeGUID == 'MinimumWithdrawalLimitBank') {
                            $rootScope.MinimumWithdarwalAmount = data[i].ConfigTypeValue;
                        } else if (data[i].ConfigTypeGUID == 'MinimumDepositLimit') {
                            $rootScope.MinimumDepositAmount = data[i].ConfigTypeValue;
                        } else if (data[i].ConfigTypeGUID == 'MinimumWithdrawalLimitPaytm') {
                            $rootScope.MinimumWithdrawalLimitPaytm = data[i].ConfigTypeValue;
                        } else if (data[i].ConfigTypeGUID == 'MaximumWithdrawalLimitPaytmPerDay') {
                            $rootScope.MaximumWithdrawalLimitPaytmPerDay = data[i].ConfigTypeValue;
                        }
                    }
                }
            });
        }
        /**
         * change password
        */
        $scope.isFormSubmitted = false;
        $scope.updatePassword = [];
        $scope.changePassword = function (form) {
            $scope.helpers = Mobiweb.helpers;
            $scope.isFormSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            var data = {};
            data.SessionKey = $localStorage.user_details.SessionKey;
            data.CurrentPassword = $scope.CurrentPassword;
            data.Password = $scope.Password;
            appDB
                .callPostForm('users/changePassword', data)
                .then(
                    function success(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.successMessageShow(data.Message);
                            $scope.isFormSubmitted = false;
                            $scope.Password = '';
                            $scope.CurrentPassword = '';
                            $scope.closePopup('changePassword')
                        }
                    },
                    function error(data) {
                        $scope.checkResponseCode(data)
                    }
                );
        }
        /**
         * update bank info
         */
        $scope.isBankFormSubmitted = false;
        $scope.updateBankDetails = function (form) {
            $scope.helpers = Mobiweb.helpers;
            $scope.isBankFormSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            var data = {};
            data.SessionKey = $localStorage.user_details.SessionKey;
            data.Address = $rootScope.profileDetails.Address;
            data.Address1 = $rootScope.profileDetails.Address1;
            data.IBAN = $rootScope.profileDetails.IBAN;
            data.RoutingCode = $rootScope.profileDetails.RoutingCode;
            data.SwiftCode = $rootScope.profileDetails.SwiftCode;
            appDB
                .callPostForm('users/updateBankAccountDetails', data)
                .then(
                    function success(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.successMessageShow(data.Message);
                            $scope.closePopup('bankDetails')
                        }
                    },
                    function error(data) {
                        $scope.checkResponseCode(data)
                    }
                );
        }

       /**
         * get weeks list
         */
        $scope.getWeekList = function () {
            var $data = {};
            $data.Params = 'LeagueFlag,WeekStartDate,WeekEndDate,Status,WeekCount';
            $data.UpcomingWeekStatus = 'Pending';
            $data.SessionKey = $scope.user_details.SessionKey;
            appDB
                .callPostForm($rootScope.apiPrefix + 'football/getWeeks', $data, contentType)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data) && data.Data.hasOwnProperty('Records')) {
                            $scope.UpcomingWeekList = data.Data.Records;
                            if(getQueryStringValue("WeekGUID")) {
                                $scope.SelectedWeekGUID = $scope.WeekGUID ? $scope.WeekGUID : getQueryStringValue("WeekGUID");
                            } else {
                                $scope.SelectedWeekGUID = $scope.WeekGUID ? $scope.WeekGUID : $scope.UpcomingWeekList[0].WeekGUID;
                            }
                            $scope.getUserBalance($scope.SelectedWeekGUID)
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        /**
         * get purchase entry data
         */
        $scope.EntryInfo = {};
        $scope.Info = {};
        $scope.Info.DoubleUps = false;
        $scope.Info.PerDoubleUpPrice = 0;
        $scope.Info.NoOfDoubles = 1;
        $scope.getEntryList = function (WeekGUID, LeagueGUID, MatchGUID) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.Params = 'NoOfEntries,NoOfPrediction,EntriesAmount,NoOfDoubleUps,PerDoubleUpPrice';
            appDB
                .callPostForm('entries/packages', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.Info.PerDoubleUpPrice = data.Data.Data.PerDoubleUpPrice;
                            $scope.EntryList = data.Data.Data.Records;
                            $scope.EntriesID = $scope.EntryList[0].EntriesID;
                            $scope.EntryInfo = $scope.EntryList[0];
                            $scope.calcuateAmount();

                            if(WeekGUID) {
                                $scope.purchaseEntryFromHeader(WeekGUID, LeagueGUID, MatchGUID);
                            }
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    });
        }
        /**
         * show entry info
         */
        $scope.showEntryInfo = function (EntriesID) {
            var index = $scope.EntryList.map(e => {
                return e.EntriesID;
            }).indexOf(EntriesID);
            $scope.EntryInfo = $scope.EntryList[index];
            $scope.calcuateAmount();
        }
        /**
         * purchase entry
         */
        // $scope.purchaseEntry = function (WeekGUID) {
        //     var $data = {};
        //     $data.SessionKey = $localStorage.user_details.SessionKey;
        //     $data.EntriesID = $scope.EntryInfo.EntriesID;
        //     $data.IsDoubleUps = ($scope.Info.DoubleUps) ? 'Yes' : 'No';
        //     $data.WeekGUID = WeekGUID;
        //     $data.WeekGUID = $rootScope.profileDetails.WeekData.WeekGUID;
        //     appDB
        //         .callPostForm('entries/purchasePackage', $data)
        //         .then(
        //             function successCallback(data) {
        //                 if ($scope.checkResponseCode(data)) {
        //                     $scope.successMessageShow(data.Message);
        //                     $scope.closePopup('entryPopup')
        //                     $scope.Info.DoubleUps = true;
        //                     if($scope.getPageName() == 'myAccount'){
        //                         $timeout(function () {
        //                             window.location.reload();
        //                         }, 100);
        //                     }
        //                 }
        //             },
        //             function errorCallback(data) {
        //                 $scope.checkResponseCode(data)
        //             });
        // }
        $scope.purchaseEntryFromHeader = function (WeekGUID, LeagueGUID, MatchGUID) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.EntriesID = $scope.EntryInfo.EntriesID;
            $data['PurchaseType'] = 'Direct';
            // $data.IsDoubleUps = ($scope.Info.DoubleUps) ? 'Yes' : 'No';
            $data.WeekGUID = WeekGUID;
            // $data.WeekGUID = $rootScope.profileDetails.WeekData.WeekGUID;
            appDB
            .callPostForm('entries/purchase', $data)
            .then(
            function successCallback(data) {
                if ($scope.checkResponseCode(data)) {
                    $scope.successMessageShow(data.Message);
                    
                    $scope.Info.DoubleUps = true;
                    $scope.getUserEntriesBalance(WeekGUID);
                    $scope.getUserBalance(WeekGUID);
                    $scope.getEntryList();

                    $scope.changeWeek(WeekGUID, LeagueGUID, MatchGUID)
                    
                    // $scope.closePopup('assignPopup')
                    // // if($scope.getPageName() == 'myEntries'){
                    //     $timeout(function () {
                    //         window.location.reload();
                    //     }, 100);
                    // // }
                }
            },
            function errorCallback(data) {
                // $scope.checkResponseCode(data)
                $rootScope.purchaseMessage = data.Message;
                $scope.openPopup("PurchaseEntryAlertPopup");
            });
        }

        /**
             * change week
             */
        $scope.changeWeek  = function (WeekGUID, LeagueGUID, MatchGUID) {
            $scope.WeekGUID = WeekGUID;
            let index = $scope.UpcomingWeekList.map(e => {
            return e.WeekGUID;
            }).indexOf(WeekGUID);
            
            setTimeout(function(){
                // $scope.$apply(function(){
                    $scope.WeekInfo = $scope.UpcomingWeekList[index];
                    $scope.$apply(); 
                // });
            }, 00);
            $scope.getUserBalance($scope.WeekGUID);
            if ($scope.getPageName() == 'prediction') {
                $scope.getEntriesUpdate($scope.WeekGUID);
                var queryParams = new URLSearchParams(window.location.search);
                // Set new or modify existing parameter value. 
                queryParams.set("LeagueGUID", LeagueGUID);
                queryParams.set("MatchGUID", MatchGUID);
                console.log(MatchGUID)
                history.replaceState(null, null, "?"+queryParams.toString());
                window.location.reload();
            }

        };
        $scope.purchaseEntry = function (WeekGUID) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.EntriesID = $scope.EntryInfo.EntriesID;
            // $data.IsDoubleUps = ($scope.Info.DoubleUps) ? 'Yes' : 'No';
            // $data.WeekGUID = WeekGUID;
            // $data.WeekGUID = $rootScope.profileDetails.WeekData.WeekGUID;
            appDB
                .callPostForm('entries/purchase', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.successMessageShow(data.Message);
                            $scope.Info.DoubleUps = true;
                            $scope.closePopup('entryPopup')
                            if($scope.getPageName() == 'myEntries'){
                                $timeout(function () {
                                    window.location.reload();
                                }, 100);
                            }
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    });
        }
        /**
         * purchase double ups
         */
        $scope.purchaseDouble = function (WeekGUID) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.NoOfDoubleUps = $scope.Info.NoOfDoubles;
            $data.WeekGUID = WeekGUID;
            // $data.WeekGUID = $rootScope.profileDetails.WeekData.WeekGUID;
            appDB
                .callPostForm('entries/purchaseDoubleUps', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.successMessageShow(data.Message);
                            $scope.Info.DoubleUps = true;
                            $scope.closePopup('doubleupPopup')
                            if($scope.getPageName() == 'myEntries'){
                                $timeout(function () {
                                    window.location.reload();
                                }, 100);
                            }else{
                                $rootScope.UserEntriesBalance = data.Data;
                            }
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    });
        }

        
        $scope.assigningEntries = "1";
        $scope.getAssignEnt = function(ent) {
            $scope.assigningEntries = ent;
        }
        /**
         * assign entry
         */
        $scope.assignEntry = function (WeekGUID, GameEntryID) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.GameEntryID = GameEntryID;
            $data.WeekGUID = WeekGUID;
            appDB.callPostForm('entries/assign', $data)
            .then(function successCallback(data) {
                if ($scope.checkResponseCode(data)) {
                    $scope.successMessageShow(data.Message);
                    // if($scope.getPageName() == 'myEntries'){
                        $timeout(function () {
                            window.location.reload();
                        }, 100);
                    // }
                }
            },
            function errorCallback(data) {
                $scope.checkResponseCode(data)
            });
            $scope.closePopup('assignPopup');
            $scope.getAssignEnt('1');
            $scope.getUserBalance(WeekGUID);
            // window.location.reload();
        }
        
        /**
         * calcuclate amount
         */
        $scope.calcuateAmount = function () {
            var amount = Number($scope.EntryInfo.EntriesAmount);
            if ($scope.Info.DoubleUps) {
                amount += ($scope.EntryInfo.NoOfDoubleUps * $scope.Info.PerDoubleUpPrice);
            }
            return amount;
        }
        $scope.$watch('Info.NoOfDoubles', function (newValue, oldValue) {
            if (newValue != oldValue) {
                if (typeof newValue == 'undefined') {
                    $scope.Info.NoOfDoubles = 1;
                    return false;
                }
            }
        });

    } else {
        $scope.getCountryList = function () {
            var $data = {};
            appDB.callPostForm('utilities/getCountries', $data)
            .then(function successCallback(data) {
                if ($scope.checkResponseCode(data)) {
                    $scope.countryList = data.Data.Records;
                    $scope.formData.PhoneCode = $scope.countryList[0].phonecode;
                }
            },
            function errorCallback(data) {
                $scope.checkResponseCode(data)
            });
        }
        /**
         * OPen signup modal 
         */
        $scope.openSignupModal = function () {
            $scope.formData.FirstName = '';
            $scope.formData.Email = '';
            $scope.formData.Username = '';
            $scope.formData.Password = '';
            $scope.formData.confrim_password = '';
            $scope.formData.BirthDate = '';
            $scope.openPopup('SignUpModal')
        }
        /**
         * Datepiker
         */
        $scope.startDateBeforeRender = startDateBeforeRender;
        $scope.startDateOnSetTime = startDateOnSetTime;

        function startDateOnSetTime() {
            $scope.$broadcast('start-date-changed');
        }
        $scope.Date = new Date();
        var pastYear = $scope.Date.getFullYear() - 18;
        $scope.Date.setFullYear(pastYear);
        function startDateBeforeRender($dates) {
            if ($scope.Date) {
                var activeDate = moment($scope.Date);

                $dates.filter(function (date) {
                    return date.localDateValue() >= activeDate.valueOf()
                }).forEach(function (date) {
                    date.selectable = false;
                })
            }
        }

        $scope.loginData = {};
        /*Login*/
        $scope.LoginSubmitted = false;
        $scope.signIn = function (form) {
            var $data = {};
            $scope.helpers = Mobiweb.helpers;
            $scope.LoginSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            if ($scope.loginType == 'phone') {
                $scope.loginDataPhone.DeviceType = 'Native';
                $scope.loginDataPhone.Source = 'Otp';
                var $data = $scope.loginDataPhone;
            } else {
                $scope.loginData.DeviceType = 'Native';
                $scope.loginData.Source = 'Direct';
                var $data = $scope.loginData;
            }
            appDB
                .callPostForm('signin', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data) && data.Data != '') {
                            $localStorage.user_details = data.Data;
                            $localStorage.isLoggedIn = true;
                            $sessionStorage.walletBalance = data.Data.WalletAmount;
                            $scope.successMessageShow(data.Message);
                            window.location.href = base_url;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    });


        }
        $scope.formData = {};

        if (getQueryStringValue('referral')) {
            $scope.formData.ReferralCode = getQueryStringValue('referral');
            $rootScope.activeTabLogin = 'signup';
        }

        /*signUp*/
        $scope.signpOTP = false;
        $scope.signupSubmitted = false;
        $scope.signUp = function (form) {
            $scope.helpers = Mobiweb.helpers;
            $scope.signupSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            if ($scope.formData.BirthDate == '' || $scope.formData.BirthDate == undefined) {
                $scope.errorMessageShow('DOB is required');
                return false;
            }
            $scope.formData.UserTypeID = 2;
            $scope.formData.Source = 'Direct';
            $scope.formData.LoginType = 'Web';
            $scope.formData.DeviceType = 'Native';
            $scope.formData.BirthDate = ($scope.formData.BirthDate != '') ? $scope.todayDateFunction($scope.formData.BirthDate) : '';
            var data = $scope.formData;
            appDB
                .callPostForm('signup', data)
                .then(
                    function success(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.formData = {};
                            $scope.signupSubmitted = false;
                            $scope.successMessageShow(data.Message);
                            $scope.closePopup('SignUpModal');
                            $scope.openPopup('LoginModal')
                        }
                    },
                    function error(data) {
                        $scope.checkResponseCode(data)
                    });
        }
        /* send forgot password email */
        $scope.forgotPasswordData = {};
        $scope.forgotEmailSubmitted = false;
        $scope.sendEmailForgotPassword = function (form) {
            $scope.forgotEmailSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            $scope.data.listLoading = true;
            var data = {};
            data.type = ($scope.CheckEmail($scope.forgotPasswordData.Keyword)) ? 'Email' : 'Phone';
            data.Keyword = $scope.forgotPasswordData.Keyword;
            appDB
                .callPostForm('recovery', data)
                .then(
                    function success(data) {
                        $scope.data.listLoading = false;
                        if ($scope.checkResponseCode(data)) {
                            $scope.closePopup('forgotPasswordModal');
                            $scope.openPopup('verifyForgotPassword');
                            $scope.successMessageShow(data.Message);
                            $scope.forgotPasswordData = {};
                            $scope.forgotPassword = {};
                        }
                    },
                    function error(data) {
                        $scope.data.listLoading = false;
                        $scope.checkResponseCode(data)
                    });

        }

        /* verify forgot password & create new password */
        $scope.forgotPassword = {};
        $scope.forgotPasswordSubmitted = false;
        $scope.verifyForgotPassword = function (form) {
            $scope.forgotPasswordSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            $scope.data.listLoading = true;
            var data = $scope.forgotPassword;
            appDB
                .callPostForm('recovery/setPassword', data)
                .then(
                    function success(data) {
                        $scope.data.listLoading = false;
                        if ($scope.checkResponseCode(data)) {
                            $scope.closePopup('verifyForgotPassword');
                            $scope.successMessageShow(data.Message);
                            $scope.forgotPassword = {};
                        }
                    },
                    function error(data) {
                        $scope.data.listLoading = false;
                        $scope.checkResponseCode(data);
                    });
        }

        /*Social Login*/
        $scope.SocialLogin = function (Source) {
            $rootScope.$on('event:social-sign-in-success', function (event, userDetails) {
                var $data = {};
                $scope.formData = {};

                $scope.formData.UserTypeID = 2;
                $scope.formData.Source = Source;
                $scope.formData.Password = userDetails.uid;
                $scope.formData.DeviceType = 'Native';
                var $data = $scope.formData;
                appDB
                    .callPostForm('signin', $data)
                    .then(
                        function successCallback(data) {

                            if (data.ResponseCode == 200) {
                                $localStorage.user_details = data.Data;
                                $localStorage.isLoggedIn = true;
                                $localStorage.SocialLogin = true;
                                $sessionStorage.walletBalance = data.Data.WalletAmount;
                                $scope.loginData = {};
                                window.location.href = base_url + 'dashboard';
                            }
                            if (data.ResponseCode == 500) {
                                var $data = {};
                                delete $scope.formData;
                                $scope.formData = {};

                                $scope.formData.UserTypeID = 2;
                                $scope.formData.Source = Source;
                                $scope.formData.SourceGUID = userDetails.uid;
                                $scope.formData.FirstName = userDetails.name;
                                $scope.formData.DeviceType = 'Native';
                                $scope.formData.Email = userDetails.email;
                                $scope.formData.LoginType = 'Web';
                                var $data = $scope.formData;
                                appDB
                                    .callPostForm('signup', $data)
                                    .then(
                                        function success(data) {
                                            if (data.ResponseCode == 200) {
                                                $localStorage.SocialLogin = true;
                                                $localStorage.user_details = data.Data;
                                                $localStorage.isLoggedIn = true;
                                                $sessionStorage.walletBalance = data.Data.WalletAmount;

                                                window.location.href = base_url + 'dashboard';
                                            }

                                            if (data.ResponseCode == 500) {
                                                var toast = toastr.warning(data.Message);
                                                toastr.refreshTimer(toast, 5000);
                                            }

                                            if (data.ResponseCode == 501) {
                                                var toast = toastr.error(data.Message);
                                                toastr.refreshTimer(toast, 5000);
                                            }

                                        },
                                        function error(data) {
                                            if (typeof data == 'object') {

                                                var toast = toastr.error(data.Message, {
                                                    closeButton: true
                                                });
                                                toastr.refreshTimer(toast, 5000);

                                            }
                                        });
                            }
                        },
                        function errorCallback(data) {
                            delete $scope.formData;
                            var $data = {};
                            $scope.formData = {};
                            $scope.formData.UserTypeID = 2;
                            $scope.formData.Source = Source;
                            $scope.formData.SourceGUID = userDetails.uid;
                            $scope.formData.FirstName = userDetails.name;
                            $scope.formData.DeviceType = 'Native';
                            $scope.formData.Email = userDetails.email;
                            $scope.formData.LoginType = 'Web';
                            var $data = $scope.formData;

                            appDB
                                .callPostForm('signup', $data)
                                .then(
                                    function success(data) {
                                        if (data.ResponseCode == 200) {
                                            $localStorage.user_details = data.Data;
                                            $localStorage.isLoggedIn = true;
                                            $sessionStorage.walletBalance = data.Data.WalletAmount;
                                            window.location.href = base_url + 'dashboard';
                                        }

                                        if (data.ResponseCode == 500) {
                                            var toast = toastr.warning(data.Message);
                                            toastr.refreshTimer(toast, 5000);
                                        }

                                        if (data.ResponseCode == 501) {
                                            var toast = toastr.error(data.Message);
                                            toastr.refreshTimer(toast, 5000);
                                        }

                                    },
                                    function error(data) {
                                        if (typeof data == 'object') {

                                            var toast = toastr.error(data.Message, {
                                                closeButton: true
                                            });
                                            toastr.refreshTimer(toast, 5000);

                                        }
                                    });
                        });

            });
        }

        // Check email
        $scope.CheckEmail = function (mail) {
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
                return true;
            } else {
                return false;
            }
        }
    }
    /**
     * get page content
     */
    $scope.ContentData = [];
    $scope.getStaticPageContent = function (page) {
        var $data = {};
        $data.Status = 'Active';
        $data.PageGUID = page;
        appDB
            .callPostForm('utilities/getPage', $data)
            .then(
                function successCallback(data) {
                    if ($scope.checkResponseCode(data)) {
                        $scope.ContentData = data.Data.Data.Records;
                        let info = window.location.pathname.split('/');
                        $scope.PageName = info[info.length - 1];
                        $scope.ContentData.forEach(e => {
                            if ($scope.PageName == 'AboutUs' && e.Title == 'About Us') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'ContactUs' && e.Title == 'Contact Us') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'PrivacyPolicy' && e.Title == 'Privacy Policy') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'TermAndCondition' && e.Title == 'Terms & Conditions') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'Disclaimer' && e.Title == 'Disclaimer') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'HowItWork' && e.Title == 'How It Works') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'SoccerRules' && e.Title == 'Soccer Rules') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'SoccerPoints' && e.Title == 'Soccer Points') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'HorseRacingRules' && e.Title == 'Horse Racing Rules') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            if ($scope.PageName == 'HorseRacingPoints' && e.Title == 'Horse Racing Points') {
                                $scope.ContestInfo = e.Content;
                                $scope.Title = e.Title;
                            }
                            
                        })

                    }
                },
                function errorCallback(data) {
                    $scope.checkResponseCode(data)
                });
    }
    $scope.checkResponseCode = function (data) {
        if (data.ResponseCode == 200) {
            return true;
        } else if (data.ResponseCode == 500) {
            var toast = toastr.error(data.Message, {
                closeButton: true
            });
            toastr.refreshTimer(toast, 5000);
            return false;
        } else if (data.ResponseCode == 501) {
            var toast = toastr.error(data.Message, {
                closeButton: true
            });
            toastr.refreshTimer(toast, 5000);
            return false;
        } else if (data.ResponseCode == 502) {
            var toast = toastr.error(data.Message, {
                closeButton: true
            });
            toastr.refreshTimer(toast, 5000);
            setTimeout(function () {
                localStorage.clear();
                window.location.reload();
            }, 1000);
            return false;
        } else {
            var toast = toastr.error(data.Message, {
                closeButton: true
            });
            toastr.refreshTimer(toast, 5000);
            return false;
        }
    }

    $scope.errorMessageShow = function (Message) {
        var toast = toastr.error(Message, {
            closeButton: true
        });
        toastr.refreshTimer(toast, 5000);
    }
    $scope.successMessageShow = function (Message) {
        var toast = toastr.success(Message, {
            closeButton: true
        });
        toastr.refreshTimer(toast, 5000);
    }
    $scope.warningMessageShow = function (Message) {
        var toast = toastr.warning(Message, {
            closeButton: true
        });
        toastr.refreshTimer(toast, 5000);
    }

    $scope.getPlayerShortName = function (PlayerName) {
        var FirstLetter = PlayerName.substr(0, 1);
        var SecondLetter = PlayerName.substr(PlayerName.indexOf(' ') + 1);
        return FirstLetter + ' ' + SecondLetter;
    }

    $scope.getUserNameFirstLetter = function (PlayerName, type) {
        if (PlayerName) {
            var FirstLetter = PlayerName.substr(0, 1);
            var SecondLetter = PlayerName.substr(0, PlayerName.indexOf(' ') + 1);
            if (type == 'First') {
                return FirstLetter;
            }
            if (type == 'Short') {
                return SecondLetter;
            }
        }
    }

    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.isLoggedIn = $localStorage.isLoggedIn;
        $scope.user_details = $localStorage.user_details;
    }

    $scope.moneyFormat = function (money) {
        money = Number(money);
        var a = money.toLocaleString('en-US', {
            maximumFractionDigits: 2,
            style: 'currency',
            currency: 'USD'
        });
        return a;
    }

    $scope.numberFormat = function (money) {
        money = Number(money);
        var a = money.toLocaleString('en-US');
        return a;
    }
    $scope.todayDateFunction = function (date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }
    /**
     * get page name
     */
    $scope.getPageName = function () {
        let info = window.location.pathname;
        info = info.split('/');
        return info[info.length - 1];
    }
    /**
     * get Banner list
     */
    $scope.BannerList = [];
    $scope.AboutUsBanner = [];
    $scope.ContactUsBanner = [];
    $scope.getBannerList = function () {
        if (!$scope.getPageName()) {
            return false;
        }
        var $data = {};
        appDB
            .callPostForm('utilities/bannerList', $data)
            .then(
                function success(data) {
                    if ($scope.checkResponseCode(data)) {
                        if(data.Data.Records) {
                            $scope.BannerList = data.Data.Records;
                            $scope.BannerList.forEach(e => {
                                if ($scope.getPageName() == 'AboutUs' && e.BannerPage == 'AboutUs') {
                                    $scope.AboutUsBanner.push({"MediaURL":e.MediaThumbURL});
                                }
                                if ($scope.getPageName() == 'ContactUs' && e.BannerPage == 'ContactUs') {
                                    $scope.ContactUsBanner.push({"MediaURL":e.MediaThumbURL});
                                }
                                    
                            })
                        }
                    }
                },
                function error(data) {
                    $scope.checkResponseCode(data)
                });
    }
}]);

app.directive('addPurchaseRequest', ['$localStorage', '$sessionStorage', 'appDB', '$timeout', '$rootScope', '$location', function ($localStorage, $sessionStorage, appDB, $timeout, rootScope, $location) {
    return {
        restrict: 'E',
        controller: 'MainController',
        templateUrl: 'purchasePopup.php',
        link: function (scope, element, attributes) {

        }
    };
}]);
app.directive('redirectPurchaseRequest', ['$localStorage', '$sessionStorage', 'appDB', '$timeout', '$rootScope', '$location', function ($localStorage, $sessionStorage, appDB, $timeout, rootScope, $location) {
    return {
        restrict: 'E',
        controller: 'MainController',
        templateUrl: 'purchaseRedirectPopUp.php',
        link: function (scope, element, attributes) {

        }
    };
}]);
app.directive('addDoubleupRequest', ['$localStorage', '$sessionStorage', 'appDB', '$timeout', '$rootScope', '$location', function ($localStorage, $sessionStorage, appDB, $timeout, rootScope, $location) {
    return {
        restrict: 'E',
        controller: 'MainController',
        templateUrl: 'doubleupPopup.php',
        link: function (scope, element, attributes) {

        }
    };
}]);
app.directive('addAssignEntry', ['$localStorage', '$sessionStorage', 'appDB', '$timeout', '$rootScope', '$location', function ($localStorage, $sessionStorage, appDB, $timeout, rootScope, $location) {
    return {
        restrict: 'E',
        controller: 'MainController',
        templateUrl: 'AssignEntryPopup.php',
        link: function (scope, element, attributes) {

        }
    };
}]);

$(document).ready(function () {
    $(".form-control").keypress(function (e) {
        if (e.which == 13) {
            $(this.form).find(':submit').focus().click();
        }
    });
    $('[data-toggle="tooltip"]').tooltip();

});
function getQueryStringValue(key) {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return (!vars[key]) ? '' : vars[key];
}


