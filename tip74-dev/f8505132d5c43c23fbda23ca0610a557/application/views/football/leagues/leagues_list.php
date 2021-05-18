<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="PageController" ng-init="getSeasonList();getCompetitionList()"><!-- Body -->
	<!-- Filter form -->
	<div class="clearfix mt-2 mb-2">
		<div class="form-area">
			<h3 class="modal-title h5">Filters</h3>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter(SeasonID,CompetitionGUID)" class="ng-pristine ng-valid">
							<input type="text" class="form-control ml-1" ng-model="Keyword" name="Keyword" placeholder="Search">
						</form>
					</div>							
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select name="SeasonID" class="form-control chosen-select" ng-model="SeasonID" ng-change="applyFilter(SeasonID,'season')">
							<option value="">Select Season</option>
							<option ng-repeat="season in SeasonList" value="{{season.SeasonID}}">Season {{season.SeasonName}}</option>
						</select>   
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select name="CompetitionGUID" class="form-control chosen-select" ng-model="CompetitionGUID" ng-change="applyFilter(CompetitionGUID, 'comp')">
							<option value="">Select Competition</option>
							<option ng-repeat="competition in CompetitionList" value="{{competition.CompetitionGUID}}">{{competition.CompetitionName}}</option>
						</select>   
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<!-- <button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button> -->
						<button type="button" class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>
					</div>							
				</div>
			</div>		
		</div>
	</div>
	<!-- Filter form/ -->
	<!-- Top container -->
	<div class="clearfix mt-2 mb-2" ng-if="data.dataList.length"> 
		<span class="float-left records d-none d-sm-block">
			<span ng-if="data.dataList.length" class="h5">Total Records: {{data.totalRecords}}</span>
		</span>
		<div class="float-right">
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add League</button>
		</div>
	</div>
	<!-- Top container/ -->


	<!-- <nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-home" aria-selected="true" ng-click="applyFilter('Active');">Active</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-profile" aria-selected="false" ng-click="applyFilter('Inactive');">Completed</a>
        </div>
    </nav> -->
    <!-- Data table -->
    <div class="table-responsive block_pad_md" infinite-scroll="getList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
        <!-- loading -->
        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

        <!-- data table -->
        <table class="table table-striped table-condensed table-hover table-sortable all-table-scroll football_series_table cricket_series_table" ng-if="data.dataList.length">
            <!-- table heading -->
            <thead>
                <tr>
                    <th style="max-width: 50px;"></th>
                    <th style="min-width: 200px;" class="text-left">League Name</th>
                    <!-- <th>Rounds</th> -->
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <!-- table body -->
            <tbody id="tabledivbody">

                <tr scope="row" ng-repeat="(key, row) in data.dataList" id="sectionsid_{{row.MenuOrder}}.{{row.CategoryID}}">
                	<td class="user_table"  style="max-width: 50px;">
                        <img ng-src="{{row.LeagueFlag}}" width="70px" height="45px;">
                    </td>
                    <td style="min-width: 200px;" class="text-left">
						<a><strong>{{row.LeagueName}}</strong></a>
						<!-- href="football/roundList?LeagueGUID={{row.LeagueGUID}}" target="_blank" -->
                    </td>
                    <!-- <td>
                        {{row.TotalRounds}}
                    </td> -->
					<td class="text-center">
						<span ng-class="{Active:'text-success',Inactive:'text-danger'}[row.Status]">{{row.Status}}</span>
					</td>

                    <td class="text-center">
                        <div class="dropdown action_toggle">
                            <button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu dropdown-menu-left">
                                <a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.LeagueGUID)">Edit</a>
								<a class="dropdown-item" href="" ng-click="deleteLeague(row.LeagueGUID)">Delete</a>
								<!-- <a class="dropdown-item" target="_blank" href="football/roundList?LeagueGUID={{row.LeagueGUID}}">View Rounds</a> -->
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


	<!-- Add Modal -->
	<div class="modal fade" id="add_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Add Football/League</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLadd"></div>
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