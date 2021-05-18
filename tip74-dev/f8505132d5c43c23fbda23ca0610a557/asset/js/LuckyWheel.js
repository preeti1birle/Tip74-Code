app.controller('luckyWheelController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 50;
    $scope.data.ParentCategoryGUID = ParentCategoryGUID;
    /*----------------*/
    $scope.getFilterData = function ()
    {
        var data = 'SessionKey='+SessionKey+'&ParentCategoryGUID='+ParentCategoryGUID+'&'+$('#filterPanel form').serialize();
        $http.post(API_URL+'LuckyWheel/getReport', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data){ /* success case */
             $scope.filterData =  response.Data;
             $timeout(function(){
                $("select.chosen-select").chosen({ width: '100%',"disable_search_threshold": 8}).trigger("chosen:updated");
            }, 300);          
         }
     });
    }


    /*list*/
    $scope.applyFilter = function ()
    {
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getListWheel(); 
    }


    /*list append*/
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&ParentCategoryGUID='+ParentCategoryGUID+'&'+$('#filterForm').serialize();
        $http.post(API_URL+'LuckyWheel', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                 $scope.data.dataList.push(response.Data.Records[i]);
             }
             $scope.data.pageNo++;
             $scope.getListWheel();               
         }else{
            $scope.data.noRecords = true;
        }
        
        $scope.data.listLoading = false;
        setTimeout(function(){ tblsort(); }, 1000);
    });
    }

    /*list append*/
    $scope.getListWheel = function () {

        if (getQueryStringValue('Type')) {
            var ListType = getQueryStringValue('Type');
        } else {
            var ListType = '';
        }
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey;

        $http.post(API_URL + 'LuckyWheel/getAll', data, contentType).then(function (response) {
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


    /*load add form*/
    $scope.loadFormAdd = function (Position, CategoryGUID)
    {
        $scope.templateURLAdd = PATH_TEMPLATE+module+'/add_form.htm?'+Math.random();
        $('#add_model').modal({show:true});
        $timeout(function(){            
           $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
       }, 200);
    }



    /*load edit form*/
    $scope.loadFormEdit = function (Position, PointsID)
    {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE+module+'/edit_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL+'LuckyWheel/getPointID', 'SessionKey='+SessionKey+'&PointsID='+PointsID, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data.Records
                
                $timeout(function(){           
                $('#edit_model').modal({show:true}); 
                   $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
               }, 200);
            }
        });
    }

    /*load delete form*/
    $scope.loadFormDelete = function (Position, CategoryGUID)
    {
        $scope.data.Position = Position;
        $scope.templateURLDelete = PATH_TEMPLATE+module+'/delete_form.htm?'+Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL+'category/getCategory', 'SessionKey='+SessionKey+'&PointsID='+CategoryGUID, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#delete_model').modal({show:true});
                $timeout(function(){            
                   $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
               }, 200);
            }
        });
    }

    /*add data*/
    $scope.addData = function ()
    {
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+$("form[name='add_form']").serialize();
        $http.post(API_URL+'admin/category/add', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                $scope.applyFilter();
                $('.modal-header .close').click();
            }else{
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;          
        });
    }


    /*edit data*/
    $scope.editData = function ()
    {
        $scope.editDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+$("form[name='edit_form']").serialize();
        console.log(data);
        $http.post(API_URL+'LuckyWheel/editPoints', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                $scope.data.dataList[$scope.data.Position] = response.Data;
                $('.modal-header .close').click();
                location.reload();
            }else{
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;          
        });
    }



}); 




/* sortable - starts */
function tblsort() {

  var fixHelper = function(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}

$(".table-sortable tbody").sortable({
    placeholder: 'tr_placeholder',
    helper: fixHelper,
    cursor: "move",
    tolerance: 'pointer',
    axis: 'y',
    dropOnEmpty: false,
    update: function (event, ui) {
      sendOrderToServer();
  }      
}).disableSelection();
$(".table-sortable thead").disableSelection();


function sendOrderToServer() {
    var order = 'SessionKey='+SessionKey+'&'+$("#tabledivbody").sortable("serialize");
    $.ajax({
        type: "POST", dataType: "json", url: API_URL+'admin/entity/setOrder',
        data: order,
        stop: function(response) {
            if (response.status == "success") {
                window.location.href = window.location.href;
            } else {
                alert('Some error occurred');
            }
        }
    });
}



}


/* sortable - ends */