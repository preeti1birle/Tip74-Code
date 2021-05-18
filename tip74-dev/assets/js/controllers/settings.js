'use strict';
app.controller('settingsController', ['$scope', '$rootScope', '$location', 'environment', '$localStorage', '$sessionStorage', 'appDB', 'toastr', 'Upload', '$timeout', function ($scope, $rootScope, $location, environment, $localStorage, $sessionStorage, appDB, toastr, Upload, $timeout) {
    $scope.env = environment;
    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.user_details = $localStorage.user_details;
        $scope.isLoggedIn = $localStorage.isLoggedIn;

        $scope.profileDetails = {};
        $scope.panDetails = {};
        $scope.bankDetails = {};

        $scope.getProfileInfo = function () {
            var $data = {};
            $data.UserGUID = $localStorage.user_details.UserGUID;
            $data.SessionKey = $localStorage.user_details.SessionKey;
            $data.Params = 'MediaAadharBack,AadharStatus,MediaAadhar,EmailForChange,EmailStatus,UserTypeName,FirstName, MiddleName, LastName, Email, Username, Gender, BirthDate, CountryCode, CountryName, CityName, StateName, PhoneNumber,Address,MediaPAN,MediaBANK,PanStatus,BankStatus';
            appDB
                .callPostForm('users/getProfile', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.profileDetails = data.Data;
                            $scope.PhoneNumber = ($scope.profileDetails.PhoneNumber) ? $scope.profileDetails.PhoneNumber : '';
                            if ($scope.profileDetails.hasOwnProperty('PhoneNumber')) {
                                $localStorage.user_details.PhoneNumber = $scope.profileDetails.PhoneNumber;
                            } else {
                                $localStorage.user_details.PhoneNumber = '';
                            }
                            if ($scope.profileDetails.EmailStatus === 'Verified') {
                                $scope.Email = $scope.profileDetails.Email;
                            } else {
                                $scope.Email = $scope.profileDetails.EmailForChange;
                            }
                            if ($scope.profileDetails.AadharStatus != 'Rejected' && $scope.profileDetails.AadharStatus != 'Not Submitted') {
                                $scope.PreviewImageAadhaarFront = $scope.profileDetails.MediaAadhar.MediaURL;
                                $scope.PreviewImageAadhaarBack = $scope.profileDetails.MediaAadharBack.MediaURL;
                            } else {
                                $scope.PreviewImageAadhaarFront = '';
                                $scope.PreviewImageAadhaarBack = '';
                            }
                            $scope.panDetails = ($scope.profileDetails.MediaPAN.MediaCaption !== '') ? JSON.parse($scope.profileDetails.MediaPAN.MediaCaption) : {};
                            $scope.bankDetails = ($scope.profileDetails.MediaBANK.MediaCaption !== '') ? JSON.parse($scope.profileDetails.MediaBANK.MediaCaption) : {};
                            $scope.aadhaarDetails = ($scope.profileDetails.MediaAadhar.MediaCaption !== '') ? JSON.parse($scope.profileDetails.MediaAadhar.MediaCaption) : {};
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data)
                    });

        }
        $scope.getProfileInfo();
        $scope.isOtpSend = false; //to manage otp screen

        $scope.activeMenu = 'verification';
        $scope.activateTab = function (tab) {
            $scope.activeMenu = tab;
        }

        $scope.submitted = false;

        /*function to update phone number and send OTP for mobile verification*/

        $scope.updateMobileNumber = function (form) {
            var $data = {};
            $scope.helpers = Mobiweb.helpers;
            $scope.submitted = true;
            if (!form.$valid) {
                return false;
            }
            $data.PhoneNumber = $scope.PhoneNumber;
            $data.SessionKey = $localStorage.user_details.SessionKey;

            appDB
                .callPostForm('users/updateUserInfo', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.profileDetails = data.Data;
                            $scope.profileDetails.PhoneNumber = '';

                            $scope.isOtpSend = true;
                            $scope.submitted = false;
                            $scope.successMessageShow('OTP sent to your mobile number');
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        /*function to verify mobile number*/
        $scope.otpSubmitted = false;
        $scope.verifyMobileNumber = function (form) {

            var $data = {};
            $scope.helpers = Mobiweb.helpers;
            $scope.otpSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            $data.DeviceType = 'Native';
            $data.Source = 'Direct';
            $data.OTP = $scope.OTP;
            $data.SessionKey = $localStorage.user_details.SessionKey;
            appDB
                .callPostForm('signup/verifyPhoneNumber', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            delete $scope.OTP;
                            //$scope.profileDetails = data.Data;
                            $scope.isOtpSend = false;
                            $scope.otpSubmitted = false;
                            $scope.checkResponseCode(data.Message);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        $scope.emailSubmitted = false;
        $scope.isEmailSend = false;
        $scope.WidgetIdEmailVerify = '';
        $scope.CaptchaEnabledEmailVerify = 'No';
        $scope.EmailVerifyCaptchaResponse = '';
        /**
         * Update email address for Verification
         */
        $scope.updateEmail = function (form) {
            var $data = {};
            $scope.helpers = Mobiweb.helpers;
            $scope.emailSubmitted = true;
            if (!form.$valid) {
                return false;
            }
            if ($scope.CaptchaEnabledEmailVerify == 'Yes' && $scope.EmailVerifyCaptchaResponse == '') { //if string is empty
                $scope.errorMessageShow('Please resolve the captcha and submit!');
            } else {
                if ($scope.CaptchaEnabledEmailVerify == 'Yes' && $scope.EmailVerifyCaptchaResponse != '') {
                    $data["g-recaptcha-response"] = $scope.EmailVerifyCaptchaResponse;
                }
                $data.Email = $scope.Email;
                $data.SessionKey = $localStorage.user_details.SessionKey;
                $data.Type = 'Email';
                appDB
                    .callPostForm('signup/resendVerification', $data)
                    .then(
                        function successCallback(data) {
                            if ($scope.checkResponseCode(data)) {
                                $scope.emailSubmitted = false;
                                $scope.successMessageShow('Email has been sent.');
                            }
                        },
                        function errorCallback(data) {
                            if ($scope.CaptchaEnabledEmailVerify == 'No' && data.CaptchaEnable == 'Yes') {
                                $scope.CaptchaEnabledEmailVerify = data.CaptchaEnable;
                                $timeout(function () {
                                    $scope.WidgetIdEmailVerify = grecaptcha.render(document.getElementById('EmailVerifyCaptchaEnabled'), {
                                        'sitekey': $scope.CaptchaSITEKey,
                                        'callback': EmailVerifyCaptchaCallback,
                                    });
                                }, 500)
                            } else if (data.CaptchaEnable == 'Yes') {
                                $scope.EmailVerifyCaptchaResponse = '';
                                grecaptcha.reset($scope.WidgetIdEmailVerify)
                            }
                            $scope.checkResponseCode(data);
                        });
            }
        }

        var EmailVerifyCaptchaCallback = function (response) {
            $scope.EmailVerifyCaptchaResponse = response;
        }
        /*PAN upload*/
        $scope.panSubmitted = false;
        $scope.uploadPanCardDetails = function (form, files) {
            $scope.panSubmitted = true;
            $scope.helpers = Mobiweb.helpers;
            if (!form.$valid) {
                return false;
            }
            if (files != null) {
                var fd = new FormData();
                fd.append('SessionKey', $localStorage.user_details.SessionKey);
                fd.append('File', files);
                fd.append('Section', 'PAN');
                fd.append('MediaCaption', JSON.stringify($scope.panDetails));
                appDB
                    .callPostImage('upload/image', fd)
                    .then(
                        function success(data) {
                            if ($scope.checkResponseCode(data)) {
                                $scope.successMessageShow(data.Message);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            }
                        },
                        function error(data) {
                            $scope.checkResponseCode(data);
                        }
                    );

            }
        }

        /*Bank upload*/
        $scope.bankSubmitted = false;
        $scope.uploadBankDetail = function (form, files) {
            $scope.bankSubmitted = true;
            $scope.helpers = Mobiweb.helpers;
            if (!form.$valid) {
                return false;
            }
            if (files != null) {
                var fd = new FormData();
                fd.append('SessionKey', $localStorage.user_details.SessionKey);
                fd.append('File', files);
                fd.append('Section', 'BankDetail');
                fd.append('MediaCaption', JSON.stringify($scope.bankDetails));
                appDB
                    .callPostImage('upload/image', fd)
                    .then(
                        function success(data) {

                            if ($scope.checkResponseCode(data)) {
                                $scope.successMessageShow(data.Message);
                                $scope.bankDetailsForm = false;
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            }
                        },
                        function error(data) {
                            $scope.checkResponseCode(data);
                        }
                    );

            }
        }

        $scope.countryList = [];
        $scope.getCountryList = function () {

            var $data = {};
            appDB
                .callPostForm('utilities/getCountries', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.countryList = data.Data;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        $scope.StateList = [];
        $scope.getStatesList = function () {
            var $data = {};
            $data.CountryCode = 'IN';
            appDB
                .callPostForm('utilities/getStates', $data)
                .then(
                    function successCallback(data) {

                        if ($scope.checkResponseCode(data)) {
                            $scope.StateList = data.Data;
                            $timeout(function () {
                                $('.selectpicker').selectpicker('render');
                            }, 500);
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }
        /**
         * Preview Aadhaar card image
         */
        $scope.PreviewImageAadhaarFront = '';
        $scope.PreviewImageAadhaarBack = ''
        $scope.SelectAadhaarFile = function (e, type) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if (type == 'front') {
                    $scope.PreviewImageAadhaarFront = e.target.result;
                } else {
                    $scope.PreviewImageAadhaarBack = e.target.result;
                }
                $scope.$apply();
            };
            reader.readAsDataURL(e.target.files[0]);
        }
        /**
         * Upload Aadhar card info
         */
        $scope.aadhaarSubmitted = false;
        $scope.uploadAadharCardDetails = function (form, filesFront, filesBack) {
            $scope.aadhaarSubmitted = true;
            $scope.helpers = Mobiweb.helpers;
            if (!form.$valid) {
                return false;
            }
            if (filesFront != null && filesBack != null) {
                let fd = new FormData();
                fd.append('SessionKey', $localStorage.user_details.SessionKey);
                fd.append('File', filesFront);
                fd.append('Section', 'Aadhar');
                fd.append('MediaCaption', JSON.stringify($scope.aadhaarDetails));
                appDB
                    .callPostImage('upload/image', fd)
                    .then(
                        function success(data) {
                            if ($scope.checkResponseCode(data)) {
                                let fd = new FormData();
                                fd.append('SessionKey', $localStorage.user_details.SessionKey);
                                fd.append('File', filesBack);
                                fd.append('Section', 'AadharBack');
                                fd.append('MediaCaption', JSON.stringify($scope.aadhaarDetails));
                                appDB
                                    .callPostImage('upload/image', fd)
                                    .then(
                                        function success(data) {
                                            if ($scope.checkResponseCode(data)) {
                                                $scope.successMessageShow(data.Message);
                                                setTimeout(function () {
                                                    window.location.reload();
                                                }, 1000);
                                            }
                                        },
                                        function error(data) {
                                            $scope.checkResponseCode(data);
                                        }
                                    );
                            }
                        },
                        function error(data) {
                            $scope.checkResponseCode(data);
                        }
                    );
            }
        }
    } else {
        window.location.href = base_url;
    }
}]);