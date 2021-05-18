'use strict';

app.controller('contactController', ['$scope', '$rootScope', '$location', 'environment', '$localStorage', '$sessionStorage', 'appDB', '$timeout', 'toastr', function ($scope, $rootScope, $location, environment, $localStorage, $sessionStorage, appDB, $timeout, toastr) {
    $scope.env = environment;
    $scope.contactForm = {};
    $scope.submitted = false;
    $scope.isLoggedIn = ($localStorage.isLoggedIn) ? true : false;
    $scope.type = getQueryStringValue('type');
    $scope.contactUS = function (form) {
        var $data = {};
        $scope.submitted = true;
        if (!form.$valid) {
            return false;
        }
        $data = $scope.contactForm;
        appDB
            .callPostForm('utilities/contact', $data)
            .then(
                function successCallback(data) {
                    if ($scope.checkResponseCode(data)) {
                        $scope.successMessageShow(data.Message);
                    }
                },
                function errorCallback(data) {
                    $scope.checkResponseCode(data)
                }
            );
    }
    $scope.downloadFormSubmitted = false;
    $scope.info = {};
    $scope.info.PhoneNumber = '';
    $scope.SendLink = function (form) {
        var $data = {};
        $scope.downloadFormSubmitted = true;
        if (!form.$valid) {
            return false;
        }
        $data.PhoneNumber = $scope.info.PhoneNumber;
        appDB
            .callPostForm('utilities/sendAppLink', $data)
            .then(
                function successCallback(data) {
                    if ($scope.checkResponseCode(data)) {
                        $scope.successMessageShow(data.Message);
                        $scope.info.PhoneNumber = '';
                        $scope.downloadFormSubmitted = false;
                    }
                },
                function errorCallback(data) {
                    $scope.checkResponseCode(data)
                }
            );
    }

}]);