app.controller('PageController', function ($scope, $http,$timeout){
    $scope.data.pageSize = 50;
    $scope.data.ParentCategoryGUID = ParentCategoryGUID;
    /*----------------*/
    $scope.getFilterData = function ()
    {
        var data = 'SessionKey='+SessionKey+'&ParentCategoryGUID='+ParentCategoryGUID+'&'+$('#filterPanel form').serialize();
        $http.post(API_URL+'admin/category/getFilterData', data, contentType).then(function(response) {
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
        $scope.getList();
    }


    /*list append*/
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&PageNo='+$scope.data.pageNo+'&PageSize='+$scope.data.pageSize+'&'
        +'&PostType=Testimonial'+$('#filterForm').serialize();
        $http.post(API_URL+'utilities/getPosts', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                 $scope.data.dataList.push(response.Data.Records[i]);
             }
             $scope.data.pageNo++;               
         }else{
            $scope.data.noRecords = true;
        }
        $scope.data.listLoading = false;
        setTimeout(function(){ tblsort(); }, 1000);
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


    /*load delete form*/
    $scope.loadFormDelete = function(Position, PostGUID) {
        $scope.data.Position = Position;
        $scope.templateURLDelete = PATH_TEMPLATE + module + '/delete_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'post/getPost', 'SessionKey='+SessionKey+'&PostGUID='+PostGUID, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#delete_model').modal({
                    show: true
                });
                $timeout(function() {
                    $(".chosen-select").chosen({
                        width: '100%',
                        "disable_search_threshold": 8,
                        "placeholder_text_multiple": "Please Select",
                    }).trigger("chosen:updated");
                }, 200);
            }
        });
    }

    /*load edit form*/
    $scope.loadFormEdit = function(Position, PostGUID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/edit_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'post/getPost', 'SessionKey=' + SessionKey + '&PostGUID=' + PostGUID + '&Params=Status,ProfilePic,MediaPAN,MediaBANK', contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data
                $('#edit_model').modal({
                    show: true
                });
                $timeout(function() {
                    $(".chosen-select").chosen({
                        width: '100%',
                        "disable_search_threshold": 8,
                        "placeholder_text_multiple": "Please Select",
                    }).trigger("chosen:updated");
                }, 200);
            }
        });

    }
    /*add data*/
    $scope.addData = function ()
    {
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+$("form[name='add_form']").serialize();
        $http.post(API_URL+'post/add', data, contentType).then(function(response) {
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
    $scope.editData = function() {
        console.log($('#edit_form').serialize()); 
        // $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $('#edit_form').serialize();
        $http.post(API_URL + 'post/edit', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.data.dataList[$scope.data.Position].Status = response.Data.Status;
                $('.modal-header .close').click();
                window.location.reload();
            } else {
                alertify.error(response.Message);
            }
            // $scope.editDataLoading = false;
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