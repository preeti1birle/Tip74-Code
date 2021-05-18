'use strict';
app.directive('addCash', ['$localStorage', '$sessionStorage', 'appDB', '$location', 'toastr', function ($localStorage, $sessionStorage, appDB, $location, toastr) {
    return {
        restrict: 'E',
        controller: 'headerController',
        templateUrl: 'addCashPopup.php',
        link: function (scope, element, attributes) {
        }
    };
}]);
app.controller('headerController', ['$scope', '$rootScope', '$location', 'environment', '$localStorage', '$sessionStorage', 'appDB', '$filter', 'toastr', 'socialLoginService', '$http', '$timeout', function ($scope, $rootScope, $location, environment, $localStorage, $sessionStorage, appDB, $filter, toastr, socialLoginService, $http, $timeout) {
    $rootScope.apiPrefix = '';
    $rootScope.GamesType = $localStorage.GamesType ? $localStorage.GamesType : 'SPORTS';
    $scope.gameTypeSelection = function (GamesType) {
        if(GamesType == 'SPORTS') {
            $localStorage.GamesType = 'SPORTS';
        }
        $rootScope.GamesType = GamesType;
        $localStorage.GamesType = GamesType;
        if ($localStorage.GamesType == 'Football') {
            $rootScope.apiPrefix = 'football/';
        } else {
            $rootScope.apiPrefix = '';
        }
        // window.location.href = base_url + 'lobby';
        // $timeout(function () {
        //     location.reload();
        // }, 500);
    }

    $scope.env = environment;
    $scope.paymentMode = 'payu';
    $scope.headerActiveMenu = 'lobby';
    var pathArray = window.location.pathname.split('/');
    var secondLevelLocation = pathArray[2];
    if (window.location.host == '') {
        secondLevelLocation = pathArray[1];
    }
    $scope.type = getQueryStringValue('type');
    $scope.secondLevelLocation = secondLevelLocation;
    $scope.base_url = base_url;
    $scope.isLoggedIn = ($localStorage.isLoggedIn) ? $localStorage.isLoggedIn : false;
    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.user_details = $localStorage.user_details;
        $scope.referral_url = base_url + $localStorage.user_details.ReferralCode;
        $rootScope.resetPromo = function (isPromoCode) {
            if (!isPromoCode) {
                $scope.PromoCodeFlag = false;
                $scope.PromoCode = '';
                $scope.GotCashBonus = 0;
                $scope.CouponData = {};
            }
        }

        /* 
         Description : To apply coupon code 
         */
        $scope.PromoCodeFlag = false;
        $scope.PromoCode = '';
        $scope.GotCashBonus = 0;
        $scope.CouponData = {};

        /*Add and validate coupon code*/
        $scope.applyPromoCode = function (PromoCode, Amount) {

            $scope.PromoCode = PromoCode;

            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.CouponCode = $scope.PromoCode;
            $data.Amount = Amount;
            appDB
                .callPostForm('store/validateCoupon', $data)
                .then(
                    function successCallback(data) {
                        if (data.ResponseCode == 200) {
                            $scope.PromoCodeFlag = true;
                            $scope.CouponData = data.Data;

                            if ($scope.CouponData.CouponType == 'Percentage') {
                                $scope.GotCashBonus = ($scope.CouponData.CouponValue / 100) * $scope.amount;
                            } else {
                                $scope.GotCashBonus = $scope.CouponData.CouponValue;
                            }
                            $sessionStorage.CouponGUID = $scope.CouponData.CouponGUID;
                        }
                        if (data.ResponseCode == 500) {
                            var toast = toastr.warning(data.Message, {
                                closeButton: true
                            });
                            toastr.refreshTimer(toast, 5000);
                        }
                        if (data.ResponseCode == 502) {
                            var toast = toastr.warning(data.Message, {
                                closeButton: true
                            });
                            toastr.refreshTimer(toast, 5000);
                            setTimeout(function () {
                                localStorage.clear();
                                window.location.reload();
                            }, 1000);
                        }
                    },
                    function errorCallback(data) {
                        var toast = toastr.warning(data.Message, {
                            closeButton: true
                        });
                        toastr.refreshTimer(toast, 5000);
                    }
                );
        }

        /*Remove applied coupon*/
        $scope.removeCoupon = function () {
            $scope.PromoCodeFlag = false;
            $scope.PromoCode = '';
            $scope.GotCashBonus = 0;
            $scope.CouponData = {};
            delete $sessionStorage.CouponGUID;
        }

        /*add cash popup*/
        $scope.addMoreCash = function (amnt) {
            $scope.removeCoupon();
            $scope.amount = (!$scope.amount) ? 0 : $scope.amount;
            $scope.amount = Number($scope.amount) + amnt;
        }
        $scope.cashSubmitted = false;
        $scope.selectPaymentMode = function (amount, form) {
            $scope.cashSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            if (parseFloat(amount) < parseInt($rootScope.MinimumDepositAmount)) {
                $scope.errorAmount = true;
                $scope.errorAmountMsg = 'Min limit of deposit amount is ' + $rootScope.MinimumDepositAmount;
                return false;
            }

            $scope.isWalletSubmitted = false;
            if (!form.$valid) {
                $scope.isWalletSubmitted = true;
                return false;
            }
            $rootScope.addBalance = {
                'amount': amount
            };
            $scope.closePopup('add_money');
            window.location.href = 'paymentMethod?amount=' + amount;
        }
        /*validate amount*/
        $scope.validateAmount = function () {
            $scope.isWalletSubmitted = false;
            $scope.errorAmount = false;
            $scope.errorAmount = '';
            if ($scope.amount.match(/^0[0-9].*$/)) {
                $scope.amount = $scope.amount.replace(/^0+/, '');
            }
            if (parseFloat(amount) < parseInt($rootScope.MinimumDepositAmount)) {
                $scope.errorAmount = true;
                $scope.errorAmountMsg = 'Min limit of deposit amount is ' + $rootScope.MinimumDepositAmount;
                return false;
            }
            if ($scope.amount > 10000) {
                $scope.amount = 10000;
                $scope.errorAmount = true;
                $scope.errorAmountMsg = 'Daily add cash limit is Rs 10000, Pls do varification of your KYC to increase limit.';
                return false;
            }
        }
        /*PayU Money Request*/
        if (getQueryStringValue('amount')) {
            $scope.amount = getQueryStringValue('amount');
        }
        $scope.addExtraCash = function (amount) {
            $scope.amount = parseFloat($scope.amount) + parseFloat(amount);
        }

        /*get notifications*/
        $scope.notificationList = [];
        $scope.getNotifications = function () {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.PageNo = 1;
            $data.PageSize = 10;
            $data.Status = 1;
            appDB
                .callPostForm('notifications', $data)
                .then(
                    function successCallback(data) {
                        $scope.getNotificationCount();
                        $scope.notificationList = [];
                        if ($scope.checkResponseCode(data) && data.Data.Records) {
                            data.Data.Records.forEach(element => {
                                element.isChecked = false;
                                element.EntryDate = new Date($filter('convertIntoUserTimeZone')(element.EntryDate));
                                $scope.notificationList.push(element);
                            });
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    }
                );
        }


        /*get notification count*/
        $scope.notificationCount = 0;
        $scope.getNotificationCount = function () {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            appDB
                .callPostForm('notifications/getNotificationCount', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.notificationCount = Number(data.Data.TotalUnread);
                        }

                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    }
                );
        }
        $rootScope.loader = {};
        $scope.readNotification = function (notification_id, MatchGUID) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.NotificationID = notification_id;
            $rootScope.loader.isLoading = false;
            appDB
                .callPostForm('notifications/markRead', $data)
                .then(
                    function successCallback(data) {
                        $rootScope.loader.isLoading = true;
                        if ($scope.checkResponseCode(data)) {
                            $scope.getNotifications();
                        }
                    },
                    function errorCallback(data) {
                        $rootScope.loader.isLoading = true;
                        $scope.checkResponseCode(data)
                    }
                );
        }

        /*Logout*/
        $scope.logout = function () {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            appDB
                .callPostForm('signin/signout', $data)
                .then(
                    function successCallback(data) {
                        if (data.ResponseCode == 200) {
                            if ($localStorage.hasOwnProperty('SocialLogin') && $localStorage.SocialLogin === true) {
                                socialLoginService.logout();
                                $http.jsonp('https://accounts.google.com/logout');
                            }
                            localStorage.clear();
                            $rootScope.GamesType = 'SPORTS';
                            $localStorage.GamesType = 'SPORTS';
                            window.location.href = base_url;
                        }
                    },
                    function errorCallback(data) {
                        localStorage.clear();
                    }
                );
        }
        /**
         * Checked/un-checked all notification
         */
        $scope.selectAllNotification = function (status) {
            $scope.notificationList.forEach(element => {
                element.isChecked = status;
            });
        }
        /**
         * Check notification deletion count
         */
        $scope.checkNotificationDeletionCount = function () {
            $scope.DeleteList = [];
            $scope.notificationList.forEach(element => {
                $scope.DeleteList.push(element.NotificationID);
            });
            if ($scope.DeleteList.length == 0) {
                return false;
            } else {
                var status = confirm("Are you sure, you want to delete notification?");
                if (status) {
                    $scope.readAllNotification($scope.DeleteList);
                }
            }
        }
        /**
         * Delete multiples notification
         */
        $scope.notificationSelect = {};
        $scope.notificationSelect.selectAll = false;
        $scope.readAllNotification = function (notification_ids) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.NotificationIDs = notification_ids;
            $rootScope.loader.isLoading = false;
            $http.post($scope.env.api_url + 'notifications/deleteAll', $.param($data), contentType).then(function (response) {
                var response = response.data;
                $rootScope.loader.isLoading = true;
                if ($scope.checkResponseCode(response)) {
                    // $scope.notificationSelect.selectAll = false;
                    $scope.getNotifications();
                }
            });
        }
        /**
         * stripe payment
         */
        $scope.StripeReq = function (amount) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.PaymentGateway = 'Stripe';
            $data.RequestSource = 'Web';
            $data.Amount = Number($scope.amount);
            $data.FirstName = $localStorage.user_details.FirstName;
            $data.Email = $localStorage.user_details.Email;
            $data.PhoneNumber = $localStorage.user_details.PhoneNumber;
            if ($sessionStorage.hasOwnProperty('CouponGUID')) {
                $data.CouponGUID = $sessionStorage.CouponGUID;
            }
            $scope.isWalletSubmitted = true;
            $rootScope.StripePayData = {};
            appDB
                .callPostForm('wallet/add', $data)
                .then(
                    function success(data) {
                        if ($scope.checkResponseCode(data)) {
                            var StripePayData = data.Data;
                            var handler = StripeCheckout.configure({
                                key: StripePayData.PublishableKey,
                                image: StripePayData.LogoImage,
                                locale: 'auto',
                                token: function (token) {
                                    $('#payProcess').val(1);
                                    var StripeData = {
                                        'stripeToken': token.id,
                                        'stripeEmail': token.email,
                                        'Amount': StripePayData.CentAmount,
                                        'OrderID': StripePayData.OrderID,
                                        'description': 'Add funds',
                                        'currency': StripePayData.Currency
                                    };
                                    $scope.StripeResponce(StripeData, StripePayData.OrderID);
                                }
                            });
                            $timeout(function () {
                                handler.open({
                                    name: StripePayData.MerchantName,
                                    description: 'Add funds',
                                    amount: Number(StripePayData.CentAmount),
                                    currency: StripePayData.Currency,
                                    email: $localStorage.user_details.Email
                                });
                            }, 500);
                        }

                    },
                    function error(data) {
                    }
                );
        }

        $scope.StripeResponce = function (StripeResponceData, WalletID) {
            var $data = {};
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.PaymentGateway = 'Stripe';
            $data.PaymentGatewayResponse = JSON.stringify(StripeResponceData);
            $data.WalletID = WalletID;
            $data.StripeToken = StripeResponceData.stripeToken;
            $data.Amount = StripeResponceData.Amount;
            appDB
                .callPostForm('wallet/confirm', $data)
                .then(
                    function success(data) {
                        if ($scope.checkResponseCode(data)) {
                            $localStorage.user_details.WalletAmount = parseFloat(data.Data.WalletAmount).toFixed(2);
                            delete $sessionStorage.CouponGUID;
                            if (data.Data.StripeAPIStatus == 'Cancelled') {
                                window.location.href = base_url + 'myAccount?status=Cancelled';
                            }
                            if (data.Data.StripeAPIStatus == 'Success') {
                                window.location.href = base_url + 'myAccount?status=Success';
                            }
                            $scope.successMessageShow(data.Message);
                        } else {
                            window.location.href = base_url + 'myAccount?status=Failed';
                        }
                    },
                    function error(data) {
                        $scope.errorMessageShow(data.Message);
                        window.location.href = base_url + 'myAccount?status=Failed';
                    }
                );

        }
    }
}]);
app.directive('addWithdrawalRequest', ['$localStorage', '$sessionStorage', 'appDB', '$timeout', '$rootScope', '$location', function ($localStorage, $sessionStorage, appDB, $timeout, rootScope, $location) {
    return {
        restrict: 'E',
        controller: 'headerController',
        templateUrl: 'WithdrawalRequest.php',
        link: function (scope, element, attributes) {
            scope.test = {};
            $timeout(function () {
                scope.test.amount = rootScope.profileDetails.WinningAmount;
            }, 1000);
            scope.withdrawSubmitted = false;
            scope.test.PaytmPhoneNumber = ($localStorage.hasOwnProperty('user_details')) ? $localStorage.user_details.PhoneNumber : '';
            scope.showOtp = false;
            scope.test.WithdrwalOTP = '';
            scope.withdrawRequest = function (form, amount, PaymentGateway) {
                scope.helpers = Mobiweb.helpers;
                scope.withdrawSubmitted = true;
                if (!form.$valid) {
                    return false;
                }
                var $data = {};
                $data.PaymentGateway = PaymentGateway;
                if (PaymentGateway == 'Paytm') {
                    $data.PaytmPhoneNumber = scope.test.PaytmPhoneNumber;
                }
                $data.Amount = amount;
                $data.SessionKey = $localStorage.user_details.SessionKey;
                $data.UserGUID = $localStorage.user_details.UserGUID;
                appDB
                    .callPostForm('wallet/withdrawal', $data)
                    .then(
                        function successCallback(data) {
                            if (scope.checkResponseCode) {
                                scope.withdrawSubmitted = false;
                                scope.getWalletDetails();
                                scope.closePopup('withdrawPopup');
                                scope.successMessageShow(data.Message);
                            }
                        },
                        function errorCallback(data) {
                            scope.checkResponseCode(data);
                        });

            }

            scope.withdrawlConfirm = function (form, OTP, Mode) {
                scope.helpers = Mobiweb.helpers;
                scope.withdrawSubmitted = true;
                if (!form.$valid) {
                    return false;
                }
                var $walletConfirm = {};
                $walletConfirm.WithdrawalID = scope.WithdrawalID;
                $walletConfirm.OTP = OTP;
                $walletConfirm.PaymentGateway = Mode;
                $walletConfirm.SessionKey = $localStorage.user_details.SessionKey;
                $walletConfirm.PaytmPhoneNumber = scope.PaytmPhoneNumber;
                $walletConfirm.Amount = scope.WithdrawalAmount;
                appDB
                    .callPostForm('wallet/withdrawal_confirm', $walletConfirm)
                    .then(
                        function successCallback(data) {
                            if (scope.checkResponseCode(data)) {
                                if (data.Data.paytmResponse.status == 'SUCCESS') {
                                    scope.successMessageShow('Withdrawal request sent successfully.')
                                    scope.getWalletDetails();
                                } else {
                                    scope.errorMessageShow('Please try again, later');
                                }
                                scope.closePopup('withdrawPopup');
                                scope.showOtp = false;
                                delete scope.WithdrawalID;
                            }
                        },
                        function errorCallback(data) {
                            scope.checkResponseCode(data);
                        }
                    );
            }
        }
    };
}]);