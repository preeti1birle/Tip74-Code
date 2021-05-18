<header class="panel-heading">
  <h1 class="h4"> Football Entries Packages</h1>
</header>

<div class="panel-body" ng-controller="PageController" ng-init="getEntriesList();"><!-- Body -->
	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		<span class="float-left records d-none d-sm-block">
			<span ng-if="EntriesList.Records.length" class="h5">Total records: {{EntriesList.TotalRecords}}</span>
		</span>
        <div class="float-right">
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Entries Package</button>
		</div>
	</div>
	<!-- Top container/ -->

	<!-- Data table -->
	<div class="table-responsive block_pad_md"> 
		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

		<!-- data table -->
		<table class="table table-striped table-condensed table-hover table-sortable" ng-if="EntriesList.Records.length">
			<!-- table heading -->
			<thead>
				<tr>
					<th>No. Of Entries</th>
					<th>No.Of Prediction</th>
                    <th>No. Of Double Ups</th>
					<th>Entries Amount</th>
					<th>Created Date</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<!-- table body -->
			<tbody id="tabledivbody">
				<tr scope="row" ng-repeat="(key, row) in EntriesList.Records" id="sectionsid_{{row.EntriesID}}">
				    <td >
                        <strong>{{row.NoOfEntries}}</strong>
					</td>
					<td>
						<strong>{{row.NoOfPrediction}}</strong>
					</td>
					<td>
						<strong>{{row.NoOfDoubleUps}}</strong>
					</td>
					<td>
						<strong>{{Currency}} {{row.EntriesAmount}}</strong>
					</td>
					<td>
						<strong>{{row.CreatedDate}}</strong>
					</td>
					<td class="text-center">
						<div class="dropdown">
							<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#8230;</button>
							<div class="dropdown-menu dropdown-menu-left">
								<a class="dropdown-item" href="javascript:void(0)" ng-click="loadFormEdit(row)">Edit</a>
                                <a class="dropdown-item" href="javascript:void(0)" ng-click="deleteEntry(row.EntriesID)">Delete</a>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- no record -->
		<p class="no-records text-center" ng-if="EntriesList.Records.length == 0">
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