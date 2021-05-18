<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>

<div class="panel-body" ng-controller="PageController" ><!-- Body -->
    <div class="form-group">
        <div class="picture-box banner">
            <img src="./asset/img/broadcast.png">
        </div>
        <!-- <div id="picture-box" class="picture-box banner">
                <img id="picture-box-picture" src="./asset/img/broadcast.png">

                <div class="picture-upload">
                        <img src="./asset/img/upload.svg" id="picture-uploadBtn">
                        <form enctype="multipart/form-data" action="../api/upload/image" method="post" name="picture_upload_form" id="picture_upload_form">
                                <input type="hidden" name="Section" value="Broadcast">
                                <input type="file" accept="image/*" name="File" id="fileInput" data-target="#picture-box #picture-box-picture" data-targetinput="#MediaGUIDs">
                        </form>
                </div>

                <div class="progressBar">
                        <div class="bar"></div>
                        <div class="percent">0%</div>
                </div>
        </div> -->
    </div>

    <div class="form-area" style="max-width:70%; margin: auto; border:1px solid #f7f7f7; padding:10px;">
        <!-- <div class="col-md-12">
            <div class="row float-right" ng-if="Switch == 'Selected'">
                <form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter()" class="ng-pristine ng-valid">
                </form>
            </div>
        </div> -->
        <form id="add_form" name="add_form" autocomplete="off" >
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <h4 class="control-label mb-3"> Broadcast Way </h4>
                        <div class="row">
                            <div class="col-md-5 mb-2"> Email</div>
                            <div class="col-md-6 mb-2">
                                <input name="broadcast" type="radio" ng-model="broadcast" class="Type" value="1"> 
                            </div>
                            <div class="col-md-5 mb-2"> SMS </div>
                            <div class="col-md-6 mb-2">
                                <input name="broadcast" ng-model="broadcast" type="radio" class="Type" value="2">
                            </div>
                            <div class="col-md-5 mb-2"> Notification</div>
                            <div class="col-md-1 mb-2"> 
                                <input name="broadcast" ng-model="broadcast" type="radio" class="Type" value="3">
                            </div>
                            <div class="col-md-2" ng-if="broadcast == 3"> 
                                <input name="Push" type="checkbox" value="1"><label class="ml-1"> Push </label>
                            </div>
                            <div class="col-md-2" ng-if="broadcast == 3"> 
                                <input name="Normal" type="checkbox" value="1"><label class="ml-1"> Normal </label>
                            </div>
                            <div class="col-md-2" ng-if="broadcast == 3"> 
                                <input name="both" type="checkbox" value="1"><label class="ml-1"> Both </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Title</label>
                        <select class="form-control chosen-select" name="Redirection">
                            <option value="">Please Select</option>
                            <!-- <option value="Dfs">Cricket Dfs</option>
                            <option value="Gully">Cricket Gully</option>
                            <option value="Auction">Cricket Auction</option> -->
                            <option value="FT-Dfs">Football Dfs</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"> <strong> Send To </strong></label>
                        <div class="col-md-12 d-flex">
                            <div class="col-md-3">
                                <input name="UserType" ng-model="Switch" ng-click="SwitchCheck('All')" type="radio" class="Type" value="All"> All Users
                            </div>
                            <div class="col-md-3">
                                <input name="UserType" ng-model="Switch" ng-click="SwitchCheck('Selected')" type="radio" class="Type" value="Selected"> Selected Users
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" ng-if="Switch == 'Selected'" ng-init="getStates()">
                <div class="col-md-12 text-primary">Filter By</div>
                <hr>
                <div class="col-md-4 pr-0">
                    <div class="form-group">
                        
                        <select id="Series" name="State" ng-model="State" class="form-control chosen-select">
                            <option value="">Select State</option>
                            <option ng-repeat="StateList in StatesfilterData" value="{{StateList.StateName}}">{{StateList.StateName}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 pr-0">
                    <div class="form-group">
                        <input type="text" class="form-control" ng-model="Keyword" name="Keyword" placeholder="Search Name,Email,Phone">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <a href="javascript:void(0)" ng-click="applyFilter(Keyword,State)" class="btn btn-default btn-secondary btn-sm"><img src="asset/img/search.svg"></a>
                    </div>
                </div>
                <div class="col-md-8" ng-if="data.totalRecords > 0">
                    <div class="form-group">
                        <small>Select single or multiple users to continue</small>
                        <small class="float-right">Records Available ({{data.totalRecords}})</small>
                        <select class="form-control chosen-select" name="selectedUser[]" multiple="">
                            <option value="">Select User</option>
                            <option ng-repeat="User in data.dataList" ng-if="User.Email || User.PhoneNumber" value="{{User.UserGUID}}">{{(User.Email) ? User.Email : User.PhoneNumber}} ({{User.FullName}})</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Title</label>
                        <input name="Title" type="text" class="form-control" value="" maxlength="40">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Message</label>
                        <textarea name="Message" id="editor" class="form-control" rows="10"></textarea>
                    </div>
                </div>
            </div>

            <!-- hidden parameters -->
            <input type="hidden" class="MediaGUIDs" id="MediaGUIDs" name="MediaGUIDs" value=""> <!-- for banner -->
            <!-- hidden parameters /-->
        </form>
        <button type="submit" class="btn btn-success btn-sm" ng-disabled="addDataLoading" ng-click="addData()">Send</button>
    </div>


</div>
</div><!-- Body/ -->