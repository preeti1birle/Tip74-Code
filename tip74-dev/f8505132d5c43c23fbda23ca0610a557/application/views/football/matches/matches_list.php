<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="PageController" ng-init="getSeasonList();"><!-- Body -->

	<!-- Filter form -->
	<div class="clearfix mt-2 mb-2">
		<div class="form-area">
			<h3 class="modal-title h5">Filters</h3>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter(Status,SeasonID,LeagueGUID)" class="ng-pristine ng-valid">
							<input type="text" class="form-control ml-1" ng-model="Keyword" name="Keyword" placeholder="Search">
						</form>
					</div>							
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select name="SeasonID" class="form-control chosen-select" ng-model="SeasonID" ng-change="getFilterData()">
							<option value="">Select Season</option>
							<option ng-repeat="season in SeasonList" value="{{season.SeasonID}}">Season {{season.SeasonName}}</option>
						</select>   
					</div>
				</div> 
				<div class="col-md-3">
					<div class="form-group">
						<select name="LeagueGUID" class="form-control chosen-select" ng-model="LeagueGUID" ng-change="applyFilter(Status,SeasonID,LeagueGUID)">
							<option value="">Select League</option>
							<option ng-repeat="league in LeagueData" value="{{league.LeagueGUID}}">{{league.LeagueName}}</option>
						</select>   
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
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Match</button>
		</div>
	</div>
	<!-- Top container/ -->


	<!-- Data table -->
	<nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-home" aria-selected="true" ng-click="applyFilter('Pending',SeasonID,LeagueGUID);">Pending</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-profile" aria-selected="false" ng-click="applyFilter('Running',SeasonID,LeagueGUID);">Running</a>
            <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-contact" aria-selected="false" ng-click="applyFilter('Completed',SeasonID,LeagueGUID);">Completed</a>
            <a class="nav-item nav-link" id="nav-withdraw-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-withdraw" aria-selected="false" ng-click="applyFilter('Cancelled',SeasonID,LeagueGUID)">Cancelled</a>
        </div>
    </nav>
	<div class="table-responsive block_pad_md" infinite-scroll="getList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

		<!-- data table -->
			<table class="table table-striped table-condensed table-hover table-sortable all-table-scroll football_matches_table" ng-if="data.dataList.length">
				<!-- table heading -->
				<thead>
					<tr>
						<th>League Name</th>
						<th style="width: 200px;">Team Local</th>
						<th style="width: 200px;">Team Visitor</th>
						<th style="width: 100px;">Match Location</th>
						<th style="width: 160px;" class="text-center sort" ng-click="applyOrderedList('M.MatchStartDateTime', 'ASC')">Match Start On<span class="sort_deactive">&nbsp;</span></th>
						<th style="width: 75px;" class="text-center">Status</th>
						<th style="width: 50px;" class="text-center">Action</th>
					</tr>
				</thead>
				<!-- table body -->
				<tbody id="tabledivbody">
					<tr scope="row" ng-repeat="(key, row) in data.dataList" id="sectionsid_{{row.MenuOrder}}.{{row.CategoryID}}">
					
						<td>
							<strong>{{row.LeagueName}}</strong>
						</td>
						<td class="text-right">
							<img class="float-left" ng-src="{{row.TeamFlagLocal}}" width="70px" height="45px;">
							<p>{{row.TeamNameLocal}} <br><small>( {{row.TeamNameShortLocal}} )</small></p>
						</td>
						<td class="text-right">
							<img class="float-left" ng-src="{{row.TeamFlagVisitor}}" width="70px" height="45px;">
							<p>{{row.TeamNameVisitor}} <br><small>( {{row.TeamNameShortVisitor}} )</small></p>
						</td>
						<td>
							<p>{{row.MatchType}} at {{row.VenueName}} </p>
						</td>
						
						<td align="center">
							<p>{{row.MatchStartDateTime}} <br> (<span am-time-ago="row.MatchStartDateTime" ></span>)</p>
						</td>
						
						<td class="text-center"><span ng-class="{Pending:'text-secondary', Completed:'text-success',Cancelled:'text-danger',Running:'text-primary'}[row.Status]">{{row.Status}}</span></td>

						<td class="text-center">
							<div class="dropdown action_toggle">
								<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
								<div class="dropdown-menu dropdown-menu-left">
									<!-- <a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.MatchGUID)">Edit</a> -->
									<a class="dropdown-item" href="" ng-click="loadFormUpdateScore(key, row.MatchGUID)">Update Live Score</a>
									<a class="dropdown-item" target="_blank" href="football/assignplayers?MatchGUID={{row.MatchGUID}}" >Assign Players</a>
									<!-- <a class="dropdown-item" target="_blank" href="football/players?MatchGUID={{row.MatchGUID}}" >Player Management</a> -->
									<a class="dropdown-item" href="" ng-click="deleteMatch(row.MatchGUID)">Delete</a>
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
	<!-- edit Modal -->
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

	<!-- edit Modal -->
	<div class="modal fade" id="update_score_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Update Live Score <?php echo $this->ModuleData['ModuleName'];?></h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLUpdate"></div>
			</div>
		</div>
	</div>
</div><!-- Body/ -->