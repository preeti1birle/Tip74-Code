'use strict';
app.controller('profileController', ['$scope', 'environment', '$localStorage', 'appDB', '$timeout', function ($scope, environment, $localStorage, appDB, $timeout) {
    $scope.env = environment;

    if ($localStorage.hasOwnProperty('user_details') && $localStorage.isLoggedIn == true) {
        $scope.user_details = $localStorage.user_details;
        $scope.isLoggedIn = $localStorage.isLoggedIn;

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
        /**
         * function to get profile details
        */
        $scope.profileDetails = {};
        $scope.getProfileInfo = function () {
            var $data = {};
            $data.UserGUID = $scope.user_details.UserGUID;
            $data.SessionKey = $scope.user_details.SessionKey;
            $data.Params = 'UserTypeName,FirstName, MiddleName, LastName, Email, PhoneCode, Username, Gender, BirthDate, CountryCode, CountryName, CityName, StateName, PhoneNumber,Address,ReferralCode,ProfilePic,TotalCash,Source';
            appDB
                .callPostForm('users/getProfile', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.profileDetails = data.Data;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });

        }
        /**
         * function to update profile details
        */

        $scope.submitted = false;
        $scope.updateProfile = function (form) {
            var $data = {};
            $scope.helpers = Mobiweb.helpers;
            $scope.submitted = true;
            if (!form.$valid) {
                return false;
            }
            $data = {
                FirstName: $scope.profileDetails.FirstName,
                BirthDate: $scope.todayDateFunction($scope.profileDetails.BirthDate),
                Gender: $scope.profileDetails.Gender,
                CountryCode: $scope.profileDetails.CountryCode,
                CityName: $scope.profileDetails.CityName,
                Address: $scope.profileDetails.Address,
                SessionKey: $localStorage.user_details.SessionKey
            };
            appDB
                .callPostForm('users/updateUserInfo', $data)
                .then(
                    function successCallback(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.profileDetails = data.Data;
                            $scope.getProfileInfo();
                            $scope.submitted = false;
                            $scope.successMessageShow(data.Message);
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

        /**
         * update profile image
         * */
        $scope.updateProfilePic = function (files) {
            if (files != null) {
                var fd = new FormData();
                fd.append('SessionKey', $scope.user_details.SessionKey);
                fd.append('File', files[0]);
                fd.append('Section', 'ProfilePic');
                fd.append('Caption', 'Profile Pic');
                appDB
                    .callPostImage('upload/image', fd, contentType)
                    .then(
                        function success(data) {
                            if ($scope.checkResponseCode(data)) {
                                $localStorage.user_details.ProfilePic = data.Data.MediaURL;
                                $scope.profileDetails.ProfilePic = data.Data.MediaURL;
                            }
                        },
                        function error(data) {
                            $scope.checkResponseCode(data)
                        }
                    );

            }
        }


        /*function to get country list*/
        $scope.countryList = [];
        $scope.getCountryList = function () {
            var $data = {};
            appDB
                .callPostForm('utilities/getCountries', $data)
                .then(
                    function successCallback(data) {

                        if ($scope.checkResponseCode(data)) {
                            $scope.countryList = data.Data.Records;
                        }
                    },
                    function errorCallback(data) {
                        $scope.checkResponseCode(data);
                    });
        }

    } else {
        window.location.href = base_url;
    }

}]);