<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>

<div class="panel-body" ng-controller="PageController"><!-- Body -->

<!-- Filter form -->
    <div class="clearfix mt-2 mb-2">
        <div class="form-area">
            <h3 class="modal-title h5">Filters</h3>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter(SeasonID)" class="ng-pristine ng-valid">
                            <input type="text" class="form-control ml-1" ng-model="Keyword" name="Keyword" placeholder="Search">
                        </form>
                    </div>							
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <!-- <button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button> -->
                        <button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>
                    </div>							
                </div>
            </div>		
        </div>
	</div>
	<!-- Filter form/ -->
	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		<span class="float-left records d-none d-sm-block">
			<span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
		</span>
		<div class="float-right">
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Player</button>
		</div>
	</div>
	<!-- Top container/ -->

    <div class="row" >
        <div class="col-md-12 pl-2 pr-2">
            <div class="verified_tabs">
                <!-- <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-home" aria-selected="true" ng-click="applyFilter('Midfielder');">Midfielder</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-profile" aria-selected="false" ng-click="applyFilter('Goalkeeper');">Goalkeeper</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-contact" aria-selected="false" ng-click="applyFilter('Striker');">Striker</a>
                        <a class="nav-item nav-link" id="nav-withdraw-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-withdraw" aria-selected="false" ng-click="applyFilter('Defender')">Defender</a>
                    </div>
                </nav> -->
                <!-- <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="table-responsive block_pad_md" >  -->

                            <!-- Data table -->
						    <div class="table-responsive block_pad_md" infinite-scroll="getList(Status)" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
						        <!-- loading -->
						        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

						        <!-- data table -->
						        <table class="footbal_player_manage_table table table-striped table-condensed table-hover table-sortable" ng-if="data.dataList && data.dataList.length">
						            <!-- table heading -->
						            <thead>
						                <tr>
						                    <th style="width: 16%;">Player's Name</th>
						                    <!-- <th>Team Name</th>
						                    <th>Dublicate</th>
                                            <th>Playing11</th>
                                            <th>IsActive</th>                    
						                    <th style="width: 100px;" ng-if="!AllMatches" >Player's Role</th>
						                    <th style="width: 100px;" ng-if="!SGUID && RoundIDAva">Salary Credit</th>
						                    <th style="width: 100px;" class="text-center"></th> -->
						                    <th style="width: 100px;" class="text-center">Action</th>
						                </tr>
						            </thead>
						            <!-- table body -->
						            <tbody id="tabledivbody">
						                <tr scope="row" ng-repeat="(key, row) in data.dataList" id="sectionsid_{{row.MenuOrder}}.{{row.CategoryID}}">
						                    <td class="listed sm clearfix">
						                        <img class="rounded-circle float-left" ng-src="{{row.PlayerPic}}">
						                        <div class="content float-left">
						                            <strong>{{row.PlayerName}}</strong>
						                        </div>
						                    </td>
						                   
						                    <td class="text-center">
						                        <div class="dropdown action_toggle">
						                            <button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
						                            <div class="dropdown-menu dropdown-menu-left">
						                                <a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.PlayerGUID)">Edit</a>
                                                        <a class="dropdown-item" href="" ng-click="deletePlayer(row.PlayerGUID)">Delete</a>
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
                        <!-- </div>
                    </div>
                </div> -->
            </div>

        </div>
    </div>

    <!-- add Modal -->
    <div class="modal fade" id="add_model">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title h5">Add <?php echo $this->ModuleData['ModuleName']; ?></h3>     	
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div ng-include="templateURLAdd"></div>
                </div>
            </div>
    </div><!-- Body/ -->

    <!-- edit Modal -->
	<div class="modal fade" id="edit_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Edit Football/Player</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLEdit"></div>
			</div>
		</div>
	</div>


    
</div><!-- Body/ -->