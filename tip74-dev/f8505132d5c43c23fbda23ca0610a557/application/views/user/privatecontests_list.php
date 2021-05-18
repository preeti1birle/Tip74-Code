<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="PageController"><!-- Body -->

	<!-- Top container -->
	<div class="clearfix mt-2 mb-2" >
		
		</div>
	<div class="clearfix mt-2 mb-2" >
		<span class="float-left records d-none d-sm-block">
			<span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
		</span>
		<div class="float-right">
			<form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter(Status)" class="ng-pristine ng-valid">
				<input type="text" class="form-control ml-1" name="Keyword" placeholder="Search">
			</form>
		</div>
		<div class="float-right">
			<button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>&nbsp;
		</div>
		<div class="float-right">
			<button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
		</div>
	</div>
	<!-- Top container/ -->

	<div class="row" >
        <div class="col-md-12 pl-2 pr-2">
            <div class="verified_tabs">
                <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-home" aria-selected="true" ng-click="applyFilter('Running');">Running</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-profile" aria-selected="false" ng-click="applyFilter('Pending');">Pending</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-contact" aria-selected="false" ng-click="applyFilter('Completed');">Completed</a>
                        <a class="nav-item nav-link" id="nav-withdraw-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-withdraw" aria-selected="false" ng-click="applyFilter('Cancelled')">Cancelled</a>
                    </div>
                </nav>
                <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="table-responsive block_pad_md" > 
							<!-- Data table -->
							<div class="table-responsive block_pad_md" infinite-scroll="getList(Status)" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
								<!-- loading -->
								<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

								<!-- data table -->
								<table class="table table-striped all-table-scroll user_private_contest_table table-condensed table-hover table-sortable" ng-if="data.dataList.length">
									<!-- table heading -->
									<thead>
										<tr>
											<th style="width: 100px;">Game Type</th>
											<th style="width: 200px;">Contest Name</th>
											<th>Paid Type</th>
											<th>Contest Size</th>
											<th>Privacy</th>
											<th>Is Confirm</th>
											<th class="text-center">Admin Fee</th>
											<th class="text-center">Entry Fee</th>
											<th style="width: 100px;" class="text-center">Entry Type</th>
											<th class="text-center">No. of Winners</th>
											<th class="text-center">Winning Amount</th>
											<th style="width: 150px;" class="text-center">Match Date</th>
											<th style="width: 100px;" class="text-center">Total Joined</th>
											<th style="width: 100px;" class="text-center">Amount Received</th>
											<th style="width: 100px;" class="text-center">Winning Distributed</th>
											<th style="width: 100px;" class="text-center">Status</th>
											<th style="width: 100px;" class="text-center">Action</th>
										</tr>
									</thead>
									<!-- table body -->
									<tbody id="tabledivbody">

										<tr scope="row" ng-repeat="(key, row) in data.dataList" id="sectionsid_{{row.MenuOrder}}.{{row.CategoryID}}">

											<td>
												<p>{{row.GameType}}</p>
											</td>
											<td>
												<div class="content float-left"><strong><a href="javascript:void(0)" ng-click="loadContestJoinedUser(key,row.ContestGUID)">{{row.ContestName}}</a></strong>
													<div ng-if="row.TeamNameLocal">({{row.TeamNameLocal}} v/s {{row.TeamNameVisitor}})</div><div ng-if="!row.TeamNameLocal">-</div>
												</div>
											</td>
											<td>
												<p>{{row.IsPaid}}</p>
											</td>
											<td>
												<p>{{row.ContestSize}}</p>
											</td>
											<td>
												<p>{{row.Privacy}}</p>
											</td>
											<td>
													<p>{{row.IsConfirm}}</p>
											</td>
											<td>
												<p>{{row.AdminPercent}}</p>
											</td>
											<td>
												<p>{{row.EntryFee}}</p>
											</td>
											<td>
												<p>{{row.EntryType}}</p>
											</td>
											<td>
												<p>{{row.NoOfWinners}}</p>
											</td>
											<td>
												<p>{{row.WinningAmount}}</p>
											</td>
											<td>
												<p>{{row.MatchStartDateTime}}</p>
											</td>
											<td class="text-center">
												<p>{{row.TotalJoined}}</p>
											</td>
											<td class="text-center">
												<p>{{row.TotalAmountReceived}}</p>
											</td>
											<td class="text-center">
												<p>{{row.TotalWinningAmount}}</p>
											</td>
											<td class="text-center"><span ng-class="{Inactive:'text-danger', Active:'text-success'}[row.StatusID]">{{row.Status}}</span></td>

											<td class="text-center">
												<div class="dropdown" ng-if="row.Status=='Pending'">
													<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#8230;</button>
													<div class="dropdown-menu dropdown-menu-left">
														<a class="dropdown-item" href="" ng-click="loadFormStatus(key, row.ContestGUID)">Status</a>
													</div>
												</div>
												<div class="dropdown" ng-if="row.Status!='Pending'">
													<span>-</span>
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
                        </div>
                    </div>
                </div>
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
										<select id="SeriesGUID" name="SeriesGUID" ng-model="SeriesGUID" ng-change="getMatches(SeriesGUID,'')" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option ng-repeat="row in filterData.SeiresData" value="{{row.SeriesGUID}}">{{row.SeriesName}}</option>
										</select>   
									</div>
								</div>
							</div>
              
							<div class="row">							
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="ParentCategory">Match</label>
										<select id="MatchGUID" name="MatchGUID" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option ng-repeat="match in MatchData" value="{{match.MatchGUID}}">{{match.TeamNameLocal}} Vs {{match.TeamNameVisitor}} ON {{match.MatchStartDateTime}}</option>
										</select>
										<small>Select this option to select match according to selected series.</small>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col">From Date</label>
										<input type="date" name="FromDate" class="form-control"> 
										<label class="filter-col">To Date</label>
										<input type="date" name="ToDate" class="form-control"> 
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="Status">Entry type</label>
										<select id="EntryType" name="EntryType" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option value="Multiple">Multiple</option>
											<option value="Single">Single</option>
										</select>   
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
										<div class="form-group">
												<label class="filter-col" for="IsConfirm">Confirm</label>
												<select id="IsConfirm" name="IsConfirm" class="form-control chosen-select">
														<option value="">Please Select</option>
														<option value="Yes">Yes</option>
														<option value="No">No</option>
												</select>   
										</div>
								</div>
								<!-- <div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="Status">Status</label>
										<select id="Status" name="Status" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option value="Pending">Pending</option>
											<option value="Running">Running</option>
											<option value="Completed">Completed</option>
											<option value="Cancelled">Cancelled</option>
										</select>   
									</div>
								</div> -->
								<input type="hidden" name="Status" ng-model="Status">
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="Status">Contest Type</label>
										<select id="ContestType" name="ContestType" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option value="Normal">Normal</option>
											<option value="Hot">Hot</option>
											<option value="Champion">Champion</option>
											<option value="Practice">Practice</option>
											<option value="Winner">Winner</option>
											<option value="Takes All">Takes All</option>
											<option value="Head to Head">Head to Head</option>
											<option value="Only For Beginners">Only For Beginners</option>
										</select>   
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="Status">Entry Range</label>
										<select id="EntryFee" name="" class="form-control chosen-select">
											<option value="">Please Select </option>
											<option value="0-50">0 - 50</option>
											<option value="50-200">50 - 200</option>
											<option value="200-500">200 - 500</option>
											<option value="500"> Greater than 500</option>
										</select>   
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="Status">Contest Size</label>
										<select id="ContestSize" name="" class="form-control chosen-select">
											<option value="">Please Select </option>
											<option value="0-50">0 - 50</option>
											<option value="50-200">50 - 200</option>
											<option value="200-500">200 - 500</option>
											<option value="500"> Greater than 500</option>
										</select>   
									</div>
								</div>
							</div>

						</div> <!-- form-area /-->
					</div> <!-- modal-body /-->

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm1').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button>
						<button type="submit" class="btn btn-success btn-sm" data-dismiss="modal" ng-disabled="editDataLoading" ng-click="applyFilter(Status)">Apply</button>
					</div>

				</form>
				<!-- Filter form/ -->
			</div>
		</div>
	</div>


	<!-- contest joined user Modal -->
	<div class="modal fade" id="contestJoinedUsers_model">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5"><?php echo $this->ModuleData['ModuleName'];?></h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLEdit"></div>
			</div>
		</div>
	</div>

	<!-- status Modal -->
	<div class="modal fade" id="status_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5"><?php echo $this->ModuleData['ModuleName'];?></h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div ng-include="templateURLEdit"></div>
			</div>
		</div>
	</div>

</div><!-- Body/ -->