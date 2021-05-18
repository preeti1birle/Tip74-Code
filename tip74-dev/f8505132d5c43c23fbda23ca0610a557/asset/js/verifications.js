app.controller('PageController', function ($scope, $http,$timeout){
    var FromDate = ToDate = '';
    $scope.data.pageSize = 15;
    /*----------------*/
    $scope.getFilterData = function ()
    {
        var data = 'SessionKey='+SessionKey+'&Params=SeriesName,SeriesGUID&'+$('#filterPanel form').serialize();
        $http.post(API_URL+'admin/matches/getFilterData', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data){ 
                /* success case */
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

    /* Add Date Range Picker */
    $scope.initDateRangePicker = function (){
        $('#dateRange').daterangepicker({
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            locale: {
                cancelLabel: 'Clear'
            },
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });
        $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
            FromDate = picker.startDate.format('YYYY-MM-DD');
            ToDate   = picker.endDate.format('YYYY-MM-DD');
            $('#dateRange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
        });
        $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
            $('#dateRange span').html('Select Date Range');
            FromDate = ToDate = '';
        });
    }
    /*list append*/
    $scope.getList = function ()
    {
        if ($scope.data.listLoading || $scope.data.noRecords) return;
        $scope.data.listLoading = true;
        if(getQueryStringValue('UserGUID')){
            var UserGUID = getQueryStringValue('UserGUID');
           
        }else{
            var UserGUID = '';
        }
        var data = 'SessionKey='+SessionKey+'&ForVerify=Yes&UserGUID='+UserGUID+'&EntryFrom=' + FromDate + '&EntryTo=' + ToDate +'&Params=FullName, Email, Username, ProfilePic, PhoneNumber,MediaAadhar,AadharStatus,MediaAadharBack,MediaPAN,MediaBANK,PanStatus,ModifiedDate,BankStatus&OrderBy=ModifiedDate&Sequence=DESC&PageNo='+$scope.data.pageNo+'&PageSize='+$scope.data.pageSize +'&'+ $('#filterForm1').serialize()+'&'+$('#filterForm').serialize();
        $http.post(API_URL+'admin/users', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200 && response.Data.Records){ /* success case */
                $scope.data.totalRecords = response.Data.TotalRecords;
                for (var i in response.Data.Records) {
                    if(response.Data.Records[i].MediaPAN.hasOwnProperty('MediaCaption')){

                       response.Data.Records[i].MediaPAN.MediaCaption = response.Data.Records[i].MediaPAN.MediaCaption != '' ? JSON.parse(response.Data.Records[i].MediaPAN.MediaCaption) : ''; 
                    }
                    if(response.Data.Records[i].MediaAadhar.hasOwnProperty('MediaCaption')){

                        response.Data.Records[i].MediaAadhar.MediaCaption = response.Data.Records[i].MediaAadhar.MediaCaption != '' ? JSON.parse(response.Data.Records[i].MediaAadhar.MediaCaption) : ''; 
                     }
                    if(response.Data.Records[i].MediaBANK.hasOwnProperty('MediaCaption')){
                       response.Data.Records[i].MediaBANK.MediaCaption = response.Data.Records[i].MediaBANK.MediaCaption != '' ? JSON.parse(response.Data.Records[i].MediaBANK.MediaCaption) : ''; 
                    }
                    $scope.data.dataList.push(response.Data.Records[i]);
                }
             $scope.data.pageNo++;               
         }else{
            $scope.data.noRecords = true;
        }
        $scope.data.listLoading = false;
        // setTimeout(function(){ tblsort(); }, 1000);
    });
    }



    /*edit data*/
    $scope.verifyDetails = function(UserGUID,VetificationType,Status,MediaGUID) {
        if (Status == 'Verified') {
            var r = confirm("Are you sure? You want to Verify "+ VetificationType + " Details!");
            if (r != true) {
                return false;
            }
        }
        $scope.editDataLoading = true;
        if(VetificationType=='PAN'){
            var Params = '&PanStatus='+Status;
        }else if(VetificationType=='Aadhar'){
            var Params = '&AadharStatus='+Status;
        }else{
            var Params = '&BankStatus='+Status;
        }
        var person ='';
        if (Status == 'Rejected') {
            var person = prompt("Reason for Rejection-", 'Image Not Readable');
            if (person == null) {
                alertify.error("Reject Reason is Required");
                $scope.editDataLoading = false;
                return false;   
            }
        }

        var data = 'SessionKey=' + SessionKey + '&MediaGUID='+MediaGUID+'&Comments='+person+'&UserGUID=' +UserGUID+'&VetificationType='+VetificationType+Params ;
        $http.post(API_URL + 'admin/users/changeVerificationStatus', data, contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
                
            } else {
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;
        });
    }

    $scope.loadFormVerification = function(Position, UserGUID, Mode) {
        $scope.data.Position = Position;
        if(Mode=='PAN'){
            var Mode = 'MediaPAN';
        }
        else if(Mode=='Aadhar'){
            var Mode = 'MediaAadhar';
        }
        else if(Mode=='AadharBack'){
            var Mode = 'MediaAadharBack';
        }
        else{
            var Mode = 'MediaBANK';
        }
        $scope.templateURLEdit = PATH_TEMPLATE + 'user/verification_form.htm?' + Math.random();
        $scope.data.pageLoading = true;
        $http.post(API_URL + 'users/getProfile', 'SessionKey=' + SessionKey + '&UserGUID=' + UserGUID + '&Params='+Mode+',UserTypeName,ProfilePic,FullName', contentType).then(function(response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.data.pageLoading = false;
                $scope.formData = response.Data;

                if(Mode=='MediaPAN'){
                    $scope.MediaURL =  $scope.formData.MediaPAN.MediaURL; 
                    $scope.MediaCaption = ''; 
                }
                else if(Mode == 'MediaAadhar'){
                    $scope.MediaURL =  $scope.formData.MediaAadhar.MediaURL; 
                    $scope.MediaCaption = ''; 
                }
                else if(Mode == 'MediaAadharBack'){
                    $scope.MediaURL =  $scope.formData.MediaAadharBack.MediaURL; 
                    $scope.MediaCaption = ''; 
                }
                else{
                    $scope.MediaURL =  $scope.formData.MediaBANK.MediaURL;
                    $scope.MediaCaption = JSON.parse($scope.formData.MediaBANK.MediaCaption);
                }
                console.log($scope.MediaURL);
                $('#Verification_model').modal({
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

    /*edit data*/
    $scope.editData = function ()
    {
        $scope.editDataLoading = true;
        var data = 'SessionKey='+SessionKey+'&'+'UpdateBankInfo=Yes&'+$("#Verification_form").serialize();
        console.log(data);
        $http.post(API_URL+'admin/users/updateUserInfo', data, contentType).then(function(response) {
            var response = response.data;
            if(response.ResponseCode==200){ /* success case */               
                alertify.success(response.Message);
                $('.modal-header .close').click();
            }else{
                alertify.error(response.Message);
            }
            $scope.editDataLoading = false;          
        });
    }

}); 
