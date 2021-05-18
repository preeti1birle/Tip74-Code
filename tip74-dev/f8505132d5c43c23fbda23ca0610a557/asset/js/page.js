app.controller('PageController', function ($scope, $http,$timeout){
    $scope.saveDataLoading = false;
    /*get pages data*/
    $scope.getData = function (PageGUID)
    {
        $scope.saveDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&PageGUID='+PageGUID;
        $http.post(API_URL+'admin/page/getPage', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Data){ /* success case */
                $scope.data.totalRecords = response.Data.Data.TotalRecords;
                for (var i in response.Data.Data.Records) {
                    $scope.data.dataList.push(response.Data.Data.Records[i]);
                }
                $scope.getContent(PageGUID);
            }else{
                $scope.formData = {};
                $scope.data.dataList = [];
                $scope.getContent(PageGUID);
               // $scope.data.noRecords = true;
                //alertify.error(response.Message);
            }
            $scope.saveDataLoading = false;          
        });
    }

    $scope.formData = {};
    $scope.getContent = function (PageGUID=""){
        $scope.PageGUID = PageGUID;

        if ($scope.PageGUID != "") {
            var PageGUID = $scope.PageGUID;
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
            var newIndex = $scope.data.dataList.map((e)=>{return e.PageGUID; }).indexOf(PageGUID);
            $scope.formData = $scope.data.dataList[newIndex];
           if($scope.formData)
           {
            tinymce.get('editor').setContent($scope.formData.Content);
           }
            // CKEDITOR.editorConfig = function (config) {
            //     config.language = 'es';
            //     config.uiColor = '#F7B42C';
            //     config.height = 500;
            //     config.toolbarCanCollapse = true;
            // };
            // CKEDITOR.replace( 'editor', {
            //     toolbar: [
            //     { name: 'styles', items: [  'Format', 'Font', 'FontSize' ] },
            //     { name: 'colors', items: [ 'TextColor'] },
            //     { name: 'basicstyles', groups: [ 'basicstyles'], items: [ 'Bold', 'Italic', 'Underline'] },
            //     { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            //     /*{ name: 'insert', items: [ 'Image'] },*/
            //     ]
            // });
        }
    }

    /*load add form*/
    $scope.loadFormAdd = function ()
    {
        $('#add_model').modal({show: true});
    }


    $scope.addData = function ()
    {
        $scope.addDataLoading = true;
        var Content = tinyMCE.get('editor').getContent({format : 'html'});
        var data ={};
            data.SessionKey =  SessionKey;
            data.Content =  Content;
            data.PageGUID = $("#PageGUID").val();
            data.Title = $("#Title").val();
       // var data = 'SessionKey=' + SessionKey + '&'+ $("form[name='add_form']").serialize()+'&Content='+ Content;
       $http.post(API_URL + 'admin/page/addPage', data,{'Content-Type': 'application/json'}).then(function (response) {
        //$http.post(API_URL + 'admin/page/addPage', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                $scope.addDataLoading = false;
                $('.modal-header .close').click();
                //window.location.reload();
            } else {
                $scope.addDataLoading = false;
                alertify.error(response.Message);
            }
        });
        $scope.addDataLoading = false;
    }

    /*edit data*/
    $scope.saveData = function ()
    {
        $scope.saveDataLoading = true;
        // CKEDITOR.instances['editor'].updateElement();
        var Content = tinyMCE.get('editor').getContent();
        var data = 'SessionKey='+SessionKey+'&PageGUID='+$scope.PageGUID+'&'+$('#save_form').serialize()+'&Content='+ Content;
        $http.post(API_URL+'admin/page/editPage', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
            }else{
                alertify.error(response.Message);
            }
            $scope.saveDataLoading = false;          
        });
    }
});