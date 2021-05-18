app.controller('PageController', function ($scope, $http,$timeout){

    $scope.recovery = function ()
    {
        $scope.processing = true;
        var data = $('#recovery_form').serialize();
        $http.post(API_URL+'recovery', data, contentType).then(function(Response) {
            var response = Response.data;
            if(response.ResponseCode==200){ /* success case */
                alertify.success(response.Message);  
                $timeout(function(){
                    window.location.href = 'recovery/reset';   
               }, 2000);                  
            }else{
                alertify.error(response.Message);
            }
            $('#recovery_form')[0].reset();
            $scope.processing = false;           
        });
    }

    $scope.reset = function ()
    {
        $scope.processing = true;
        var data = $('#recovery_reset_form').serialize();
        $http.post(API_URL+'recovery/setPassword', data, contentType).then(function(Response) {
            var response = Response.data;
            if(response.ResponseCode==200){ /* success case */
                $('#recovery_reset_form')[0].reset();
                alertify.success(response.Message);  
                $timeout(function(){
                    window.location.href = 'signin';   
               }, 3000);                  
            }else{
                alertify.error(response.Message);
            }
            $scope.processing = false;           
        });
    }
    
}); 





