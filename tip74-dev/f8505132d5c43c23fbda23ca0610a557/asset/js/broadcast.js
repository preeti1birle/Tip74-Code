app.controller('PageController', function ($scope, $http, $timeout) {

    $scope.applyFilter = function (Keyword,State) {
        $scope.data = angular.copy($scope.orig); /*copy and reset from original scope*/
        $scope.getList(Keyword,State);
    }

    $(".chosen-select").chosen({
        width: '100%',
        "disable_search_threshold": 8,
        "placeholder_text_multiple": "Select User",
    }).trigger("chosen:updated");
    /*list append*/
    $scope.getList = function (Keyword,State) {

        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        if (Keyword) {
            var Keyword = Keyword;
        } else {
            var Keyword = '';
        }
        var data = 'SessionKey=' + SessionKey + '&Keyword=' + Keyword +'&UserTypeID=2' + '&Status=Verified&IsAdmin=No&Params=UserTypeID,EmailForChange,FullName,Status,Email, PhoneNumber&' + $('#filterForm').serialize();
        if(State != '' && State != undefined){
            data += '&StateName=' + State;
        }

        $http.post(API_URL + 'admin/users', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200 && response.Data.Records) { /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                    $scope.data.dataList.push(response.Data.Records[i]);
                }

                $scope.data.pageNo++;
                $timeout(function () {
                    $(".chosen-select").chosen({
                        width: '100%',
                        "disable_search_threshold": 8,
                        "placeholder_text_multiple": "Select User",
                    }).trigger("chosen:updated");
                }, 100);
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }
    $scope.Switch = 'All';
    $scope.SwitchCheck = function (Type) {
        if (Type == "Selected") {
            $timeout(function () {
                $(".chosen-select").chosen({
                    width: '100%',
                    "disable_search_threshold": 8,
                    "placeholder_text_multiple": "Select User",
                }).trigger("chosen:updated");
            }, 50);
        }
    }

    /*add data*/
    $scope.addData = function ()
    {
        if (confirm("Are you sure want to message broadcast?")) {
            $scope.addDataLoading = true;
            var EmailMessage = '';
            if ($scope.broadcast == 1) {
                EmailMessage = tinyMCE.get('editor').getContent();
            }
            var data = 'SessionKey=' + SessionKey + '&' + $("form[name='add_form']").serialize() + "&EmailMessage=" + EmailMessage;
            $http.post(API_URL + 'admin/users/broadcast', data, contentType).then(function (response) {
                var response = response.data;
                if (response.ResponseCode == 200) { /* success case */
                    alertify.success(response.Message);
                    $timeout(function () {
                        window.location.reload();
                    }, 200);
                } else {
                    alertify.error(response.Message);
                }
                $scope.addDataLoading = false;
            });
        }

    }

    $scope.getStates = function ()
    {

        var data = 'SessionKey=' + SessionKey + '&CountryCode=IN';
        $http.post(API_URL + 'utilities/getStates', data, contentType).then(function (response) {
            $scope.StatesfilterData = response.data.Data.Records;
             console.log(StatesfilterData);
        });

    }

    $scope.$watch('broadcast', function (newValue, oldValue) {
        if (newValue == 1) {
            tinymce.init({
                selector: '#editor',
                font_size_classes: "fontSize1, fontSize2, fontSize3, fontSize4, fontSize5, fontSize6",
                plugins: [
                    "advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                    "save table contextmenu directionality template paste textcolor code"
                ],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons | sizeselect | fontselect | fontsize | fontsizeselect",
                style_formats: [{
                        title: 'Bold text',
                        inline: 'b'
                    }, {
                        title: 'Red text',
                        inline: 'span',
                        styles: {
                            color: '#ff0000'
                        }
                    }, {
                        title: 'Red header',
                        block: 'h1',
                        styles: {
                            color: '#ff0000'
                        }
                    }, {
                        title: 'Example 1',
                        inline: 'span',
                        classes: 'example1'
                    }, {
                        title: 'Example 2',
                        inline: 'span',
                        classes: 'example2'
                    }, {
                        title: 'Table styles'
                    }, {
                        title: 'Table row 1',
                        selector: 'tr',
                        classes: 'tablerow1'
                    }],
                image_title: true,
                automatic_uploads: true
            });
        } else {
            tinymce.get('editor').remove();
        }
    });

    /*load add form*/
    $scope.loadFormAdd = function (Position, CategoryGUID)
    {
        $scope.templateURLAdd = PATH_TEMPLATE+module+'/add_form.htm?'+Math.random();
        $('#add_model').modal({show:true});
        $timeout(function(){            
           $(".chosen-select").chosen({ width: '100%',"disable_search_threshold": 8 ,"placeholder_text_multiple": "Please Select",}).trigger("chosen:updated");
           $('input[name=Date]').datetimepicker({startDate: new Date()});
       }, 500);
    }

    /*add data*/
    $scope.addDataBroadcastScheduling = function ()
    {
        $scope.addDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+$("form[name='add_form']").serialize();
        console.log(data);
        $http.post(API_URL+'post/addBroadcastScheduling', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                // $scope.applyFilter();
                $('.modal-header .close').click();
                location.reload();
            }else{
                alertify.error(response.Message);
            }
            $scope.addDataLoading = false;          
        });
    }


    /*list append*/
    $scope.BroadcastList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        var data = 'SessionKey='+SessionKey+'&PageNo='+$scope.data.pageNo+'&PageSize='+$scope.data.pageSize+'&'
        +'&PostType=Testimonial'+$('#filterForm').serialize();
        $http.post(API_URL+'post/BroadcastSchedulingList', data, contentType).then(function(response) {
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

    /*load edit form*/
    $scope.loadFormEdit = function(Position, ID) {
        $scope.data.Position = Position;
        $scope.templateURLEdit = PATH_TEMPLATE + module + '/edit_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'post/BroadcastSchedulingList', 'SessionKey=' + SessionKey + '&ID=' + ID, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data.Records;
                $('#edit_model').modal({
                    show: true
                });
                $timeout(function() {
                    // $(".chosen-select").chosen({
                    //     width: '100%',
                    //     "disable_search_threshold": 8,
                    //     "placeholder_text_multiple": "Please Select",
                    // }).trigger("chosen:updated");
                    // $('input[name=Date]').datetimepicker({startDate: new Date()});
                }, 500);
            }
        });

    }


    /*edit data*/
    $scope.editData = function() {
        console.log($('#edit_form').serialize()); 
        // $scope.editDataLoading = true;
        var data = 'SessionKey=' + SessionKey + '&' + $('#edit_form').serialize();
        $http.post(API_URL + 'post/editBroadcastScheduling', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                // $scope.data.dataList[$scope.data.Position].Status = response.Data.Status;
                $('.modal-header .close').click();
                window.location.reload();
            } else {
                alertify.error(response.Message);
            }
            // $scope.editDataLoading = false;
        });
    }

    $scope.RunCrons = function (Url,id)
    {
        var data = 'SessionKey=' + SessionKey;
        $http.get(API_URL + Url, data, contentType).then(function (response) {
            $("#"+id).html("Running...");
            console.log(response.data);
            if(response.data == "Success"){
             $("#"+id).html("Click");   
            }
        });

    }

}); 