app.controller('PageController', function ($scope, $http, $timeout) {
    var module = 'signin';
    var module2 = 'signin/otp';
    $scope.btn = false;
    /*add data*/
    $scope.signIn = function ()
    {
        $scope.processing = true;
        var data = $('#login_form').serialize();
        $http.post(module, data, contentType).then(function (Response) {
            //console.log(Response);
            var response = Response.data;

            if (response.ResponseCode == 200) { /* success case */
                // $('#login_form')[0].reset();
                if (response.Data.UserTypeID == 1) {
                    $('#myModal').modal('show');
                } else {
                    $('#login_form')[0].reset();
                    window.location.href = 'dashboard';
                }

                alertify.success(response.Message);
            } else {
                alertify.error(response.Message);
            }
            $scope.processing = false;
        });

    }

    $scope.signinwithotp = function (otp)
    {
        $scope.btn = true;
        if (!otp.$valid)
            return false;
        $scope.processing = true;
        var data = $('#verifyOTP').serialize() + "&" + $('#login_form').serialize();
        $http.post(module2, data, contentType).then(function (Response) {
            //console.log(Response);
            var response = Response.data;
            if (response.ResponseCode == 200) { /* success case */
                $('#login_form')[0].reset();
                window.location.href = 'dashboard';
            } else {
                alertify.error(response.Message);
            }
            $scope.processing = false;
        });
    }
});



