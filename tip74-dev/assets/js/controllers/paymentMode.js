'use strict';

app.controller('paymentModeController',['$scope','$rootScope','$location','environment','$localStorage','$sessionStorage','appDB','toastr',function($scope,$rootScope,$location,environment,$localStorage,$sessionStorage,appDB,toastr){
  $scope.env = environment;
  
  	$scope.closePopup('add_money');
  	
  	$rootScope.amount = $scope.amount;
  	$scope.submitPayTmData=function(payTmData){
      angular.element('#submitPayTm').trigger('submit')
    }
}]);
