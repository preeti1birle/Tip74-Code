<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="PageController"><!-- Body -->

	<!-- Top container -->
	<div class="clearfix mt-2 mb-2" ng-if="data.dataList.length">
		<span class="float-left records d-none d-sm-block">
			<span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
		</span>
		<div class="float-right">
			<form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter()" class="ng-pristine ng-valid">
				<input type="text" class="form-control ml-1" name="Keyword" placeholder="Search">
			</form>
		</div>
		<div class="float-right mr-1">
			<button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>
		</div>
		<div class="float-right mr-1">
		<button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="ExportExcelMatches()">Export</button>&nbsp;
			<button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>
		</div>
	</div>
	<!-- Top container/ -->


	<!-- Data table -->
	<nav>
<!--        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-home" aria-selected="true" ng-click="applyFilter('2');">Running</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-profile" aria-selected="false" ng-click="applyFilter('1');">Pending</a>
            <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-contact" aria-selected="false" ng-click="applyFilter('5');">Completed</a>
            <a class="nav-item nav-link" id="nav-withdraw-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-withdraw" aria-selected="false" ng-click="applyFilter('3')">Cancelled</a>
        </div>-->
    </nav>
	<div class="table-responsive block_pad_md" infinite-scroll="getList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

		<!-- data table -->
			<table class="table table-striped table-condensed table-hover table-sortable all-table-scroll football_upcoming_matches" ng-if="data.dataList.length">
				<!-- table heading -->
				<thead>
					<tr>
						<th>Series Name</th>
						<th></th>
						<th>Team Local</th>
						<th></th>
						<th>Team Visitor</th>
						<th>Match Type</th>
						<th class="text-center sort" ng-click="applyOrderedList('M.MatchStartDateTime', 'ASC')">Match Start On<span class="sort_deactive">&nbsp;</span></th>
						<th class="text-center">Is Edited</th>
						<th class="text-center">Public Player Joined</th>
						<th class="text-center">Player Joined Private</th>
						<th class="text-center">Status</th>
						<th class="text-center">Match Display</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<!-- table body -->
				<tbody id="tabledivbody">
					<tr scope="row" ng-repeat="(key, row) in data.dataList" id="sectionsid_{{row.MenuOrder}}.{{row.CategoryID}}">
					
						<td>
							<strong>{{row.SeriesName}}</strong>
							<!-- <button ng-if="row.IsPlayingXINotificationSent == 'No'" ng-click="Playing11Notification(row.MatchGUID)">Playing11</button> -->
						</td>
						<td class="text-center">
							<img class="float-left" ng-src="{{row.TeamFlagLocal}}" width="70px" height="45px;">
						</td>
						<td>
							<p>{{row.TeamNameLocal}} <br><small>( {{row.TeamNameShortLocal}} )</small></p>
						</td>
						<td class="text-center">
							<img class="float-left" ng-src="{{row.TeamFlagVisitor}}" width="70px" height="45px;">
						</td>
						<td>
							<p>{{row.TeamNameVisitor}} <br><small>( {{row.TeamNameShortVisitor}} )</small></p>
						</td>
						<td>
							<p>{{row.MatchType}} at {{row.MatchLocation}} </p>
						</td>
						
						<td align="center">
							<p>{{row.MatchStartDateTime}} <br> (<span am-time-ago="row.MatchStartDateTime" ></span>)</p>
						</td>

						<td align="center">
							<p>{{row.IsEdited}}</p>
						</td>
						
						<td align="center">
							<p>{{row.TotalPlayerCountMatch}}</p>
						</td>

						<td align="center">
							<p>{{row.TotalPlayerCountPrivate}}</p>
						</td>
							
						<td class="text-center"><span ng-class="{Pending:'text-secondary', Completed:'text-success',Cancelled:'text-danger',Running:'text-primary'}[row.Status]">{{row.Status}}</span>
						<!-- <button ng-if="row.Status == 'Pending'" ng-click="makeLive(row.MatchGUID)">Go Live</button> -->
						</td>

						<td class="text-center"><span ng-class="{Enable:'text-success',Disable:'text-danger'}[row.MatchDisplay]">{{row.MatchDisplay}}</span></td>

						<td class="text-center">
							<div class="dropdown action_toggle">
								<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
								<div class="dropdown-menu dropdown-menu-left">
									<!-- <a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.MatchGUID)">Edit</a> -->
									<a class="dropdown-item" target="_blank" href="football/players?MatchGUID={{row.MatchGUID}}" >Player Management</a>
									<a class="dropdown-item" href="" ng-click="loadFormStatus(key, row.MatchGUID)">Status</a>
									<a class="dropdown-item" href="football/contests?MatchGUID={{row.MatchGUID}}" target="_blank">Contests</a>
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

	<!-- status Modal -->
	<div class="modal fade" id="status_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5"><?php echo $this->ModuleData['ModuleName']; ?></h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div ng-include="templateURLEdit"></div>
            </div>
        </div>
    </div>

	<!-- Filter Modal -->
	<div class="modal fade" id="filter_model"  ng-init="getFilterData()">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Filters</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>

				<!-- Filter form -->
				<form id="filterForm1" role="form" autocomplete="off" class="ng-pristine ng-valid">
					<div class="modal-body">
						<div class="form-area">

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="CategoryTypeName">Series</label>
										<select id="SeriesGUID" name="SeriesGUID" class="form-control chosen-select" ng-model="SeriesGUID" ng-change="getTeamData(SeriesGUID)">
											<option value="">Please Select</option>
											<option ng-repeat="row in filterData.SeiresData" value="{{row.SeriesGUID}}">{{row.SeriesName}}</option>
										</select>   
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col">Local Team</label>
										<select name="LocalTeamGUID" class="form-control chosen-select" ng-model="LocalTeamGUID" ng-change="getTeamData(SeriesGUID,LocalTeamGUID)">
											<option value="">Please Select</option>
											<option ng-repeat="row in LocalTeamData.TeamData" value="{{row.TeamGUID}}">{{row.TeamName}}</option>
										</select>   
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col">Visitor Team</label>
										<select name="VisitorTeamGUID" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option ng-repeat="row in VisitorTeamData.TeamData" value="{{row.TeamGUID}}">{{row.TeamName}}</option>
										</select>   
									</div>
								</div>
							</div>

							<!-- <div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="Status">Status</label>
										<select id="StatusID" name="StatusID" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option value="1">Pending</option>
											<option value="2">Running</option>
											<option value="5">Completed</option>
											<option value="3">Cancelled</option>
										</select>   
									</div>
								</div>
							</div> -->

						</div> <!-- form-area /-->
					</div> <!-- modal-body /-->

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm1').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button>
						<button type="submit" class="btn btn-success btn-sm" data-dismiss="modal" ng-disabled="editDataLoading" ng-click="applyFilter()">Apply</button>
					</div>

				</form>
				<!-- Filter form/ -->
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
</div><!-- Body/ -->