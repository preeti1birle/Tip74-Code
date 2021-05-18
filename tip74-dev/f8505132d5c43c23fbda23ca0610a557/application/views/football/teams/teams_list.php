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
						<form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter(SeasonID,LeagueGUID)" class="ng-pristine ng-valid">
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
						<select name="LeagueGUID" class="form-control chosen-select" ng-model="LeagueGUID" ng-change="applyFilter(SeasonID,LeagueGUID)">
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
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Team</button>
		</div>
	</div>
	<!-- Top container/ -->

	<!-- Data table -->
	<div class="table-responsive block_pad_md" infinite-scroll="getList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

		<!-- data table -->
		<table class="table table-striped table-condensed table-hover table-sortable" ng-if="data.dataList.length">
			<!-- table heading -->
			<thead>
				<tr>
					<th style="width:100px;"></th>
					<th style="width:300px;">Team Name</th>
					<th style="width:200px;">League Name</th>
					<th style="width:100px;" class="text-center">Action</th>
				</tr>
			</thead>
			<!-- table body -->
			<tbody id="tabledivbody">
				<tr scope="row" ng-repeat="(key, row) in data.dataList" id="sectionsid_{{row.MenuOrder}}.{{row.CategoryID}}">
				
					<td class="text-center">
						<img ng-src="{{row.TeamFlag}}" width="80px" height="80px;">
					</td>
					
					<td>
						<strong >{{row.TeamName}}</strong>
						<br><small>( {{row.TeamNameShort}} )</small>
					</td>
					<td>
						<strong>{{row.LeagueName}}</strong>
					</td>
					<td class="text-center">
						<div class="dropdown">
							<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#8230;</button>
							<div class="dropdown-menu dropdown-menu-left">
								<a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.TeamGUID)">Edit</a>
								<a class="dropdown-item" href="" ng-click="loadFormUpdateTeam(key, row.TeamGUID)">Update Team Standings</a>
								<a class="dropdown-item" href="" ng-click="deleteTeam(row.TeamGUID)">Delete</a>
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
					<h3 class="modal-title h5">Add Team</h3>     	
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
					<h3 class="modal-title h5">Edit Team</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLEdit"></div>
			</div>
		</div>
	</div>

	<!-- edit team standing Modal -->
	<div class="modal fade" id="editTS_model">
		<div class="modal-dialog modal-md" style="max-width:745px" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Update Team Stadnings</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLEditTS"></div>
			</div>
		</div>
	</div>
</div><!-- Body/ -->