<div class="panel-body" ng-controller="PageController" ng-init="BroadcastList()" ><!-- Body -->

    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
        <span class="float-left records hidden-sm-down">
            <span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
        </span>
        <div class="float-right">
            <button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Post</button>
        </div>
    </div>
    <!-- Top container/ -->


    <!-- Data table -->
    <div class="table-responsive block_pad_md" infinite-scroll="" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 

        <!-- loading -->
        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

        <!-- data table -->
            <table class="table table-striped table-hover all-table-scroll admin_broadcast_table" ng-if="data.dataList.length">
                <!-- table heading -->
                <thead>
                    <tr>
                        <th style="width: 100px;">Title</th>
                        <th style="width: 300px;">Text</th>
                        <th>Date</th>
                        <th style="width: 160px;" class="text-center">IsSent</th>
                        <!-- <th style="width: 160px;" class="text-center">Status</th> -->
                        <th style="width: 100px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <!-- table body -->
                <tbody>
                    <tr scope="row" ng-repeat="(key, row) in data.dataList">

                        <td>{{row.Title}}</td>
                        <td class="listed sm clearfix">
                            <strong>{{row.Text}}</strong>
                        </td>

                        <td >{{row.Date}}</td> 
                        <td style="text-align: center;">{{row.IsSend}}</td> 
                        <!-- <td style="text-align: center;" >{{row.Status}}</td> -->
                        <td class="text-center">
                            <div class="dropdown action_toggle">
                                <button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
                                <div class="dropdown-menu dropdown-menu-left">
                                    <a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.ID)">Edit</a>
                                    <!-- <a class="dropdown-item" href="" ng-click="deleteData(row.PostGUID)">Delete</a> -->
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        <!-- no record -->
        <p class="no-records text-center" ng-if="data.noRecords">
            <span ng-if="data.dataList.length">No more records found.</span>
            <span ng-if="!data.dataList.length">No records found.</span>
        </p>
    </div>
    <!-- Data table/ -->

    <!-- delete Modal -->
    <div class="modal fade" id="view_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5"><?php echo $this->ModuleData['ModuleName'];?> Content</h3>       
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="edit_form" name="edit_form" autocomplete="off" ng-include="templateURLView">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>

    <!-- edit Modal -->
    <div class="modal fade" id="edit_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Edit <?php echo $this->ModuleData['ModuleName'];?></h3>      
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div ng-include="templateURLEdit"></div>
            </div>
        </div>
    </div>

    <!-- add Modal -->
    <div class="modal fade" id="add_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Add <?php echo $this->ModuleData['ModuleName'];?></h3>       
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                    <div ng-include="templateURLAdd"></div>
            </div>
        </div>
    </div>
    <!-- delete Modal -->
    <div class="modal fade" id="delete_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Delete <?php echo $this->ModuleData['ModuleName'];?></h3>        
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="edit_form" name="edit_form" autocomplete="off" ng-include="templateURLDelete">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>
</div><!-- Body/ -->



