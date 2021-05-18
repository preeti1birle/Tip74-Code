<header class="panel-heading">
  <h1 class="h4"> Weeks List</h1>
</header>

<div class="panel-body" ng-controller="PageController" ng-init="getWeeksList();"><!-- Body -->
	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		<span class="float-left records d-none d-sm-block">
			<span ng-if="WeekList.Records.length" class="h5">Total records: {{WeekList.TotalRecords}}</span>
		</span>
        <!-- <div class="float-right">
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Entries Package</button>
		</div> -->
	</div>
	<!-- Top container/ -->

	<!-- Data table -->
	<div class="table-responsive block_pad_md"> 
		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

		<!-- data table -->
		<table class="table table-striped table-condensed table-hover table-sortable" ng-if="WeekList.Records.length">
			<!-- table heading -->
			<thead>
				<tr>
					<th>Week Start Date</th>
					<th>Week End Date</th>
                    <th>Status</th>
					<!-- <th class="text-center">Action</th> -->
				</tr>
			</thead>
			<!-- table body -->
			<tbody id="tabledivbody">
				<tr scope="row" ng-repeat="(key, row) in WeekList.Records" id="sectionsid_{{row.WeekGUID}}">
				    <td >
                        <strong>{{row.WeekStartDate}}</strong>
					</td>
					<td>
						<strong>{{row.WeekEndDate}}</strong>
					</td>
					<td>
						<strong>{{row.Status}}</strong>
					</td>
					<!-- <td class="text-center">
						<div class="dropdown">
							<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#8230;</button>
							<div class="dropdown-menu dropdown-menu-left">
								<a class="dropdown-item" href="javascript:void(0)" ng-click="loadFormEdit(row)">Edit</a>
                                <a class="dropdown-item" href="javascript:void(0)" ng-click="deleteEntry(row.EntriesID)">Delete</a>
							</div>
						</div>
					</td> -->
				</tr>
			</tbody>
		</table>

		<!-- no record -->
		<p class="no-records text-center" ng-if="WeekList.Records.length == 0">
			<span>No records found.</span>
		</p>
	</div>
	<!-- Data table/ -->

	<!-- edit Modal -->
	<div class="modal fade" id="edit_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Edit Entries Package</h3>     	
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
					<h3 class="modal-title h5">Add Entries Package</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLadd"></div>
			</div>
		</div>
	</div>
</div><!-- Body/ -->