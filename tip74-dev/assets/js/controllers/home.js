"use strict";
app.controller('HomeController', ['$scope', '$localStorage', 'appDB', function ($scope, $localStorage, appDB) {
    $scope.isLoggedIn = $localStorage.isLoggedIn;
    if (!$localStorage.hasOwnProperty('user_details')) {
        /**
         * Get Testimonials lists
         */
        $scope.Testimonials = [];
        $scope.getTestimonials = function () {
            var $data = {};
            $data.PostType = 'Testimonial';
            appDB
                .callPostForm('utilities/getPosts', $data)
                .then(
                    function success(data) {
                        if ($scope.checkResponseCode(data)) {
                            $scope.Testimonials = data.Data.Records;
                        }
                    },
                    function error(data) {
                        $scope.checkResponseCode(data)
                    });
        }
    } else {
        // window.location.href = base_url + '';
    }
    /**
     * get Banner list
     */
    $scope.BannerList = [];
    $scope.MediaThumbURL = [];
    $scope.getBannerList = function () {
        var $data = {};
        appDB
            .callPostForm('utilities/bannerList', $data)
            .then(
                function success(data) {
                    if ($scope.checkResponseCode(data)) {
                        if(data.Data.Records) {
                            $scope.BannerList = data.Data.Records;
                            $scope.BannerList.forEach(e => {
                                if (e.BannerPage == 'Home') {
                                    $scope.MediaThumbURL.push({"MediaURL":e.MediaThumbURL});
                                }         
                            })
                        }
                        
                    }
                },
                function error(data) {
                    $scope.checkResponseCode(data)
                });
    }
    $scope.getBannerList();
}]);