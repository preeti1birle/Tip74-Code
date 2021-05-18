<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>
<style>
   .daterangepicker {
        z-index: 9999;
    }
</style>
<div class="panel-body" ng-controller="PageController" ><!-- Body -->

	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		<span class="float-left records hidden-sm-down d_flex">
			<span ng-if="data.dataList.length" class="h5">Total Records: {{data.totalRecords}}</span>
		</span>

        <div>
			<div class="float-right">
				<form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter()" class="ng-pristine ng-valid">
					<input type="text" class="form-control" name="Keyword" placeholder="Search">
				</form>
			</div>
			<div class="float-right">
				<button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>&nbsp;
			</div>
			<div class="float-right">
				<button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
			</div>
		</div>
			
	</div>
	<!-- Top container/ -->



	<!-- Data table -->
	<div class="table-responsive block_pad_md" infinite-scroll="getList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 

		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>
		<form name="records_form" id="records_form">
			<!-- data table -->
				<table class="table table-striped table-hover all-table-scroll user_verifications_table" ng-if="data.dataList.length">
					<!-- table heading -->
					<thead>
						<tr>
							<!-- <th style="width: 50px;" class="text-center" ng-if="data.dataList.length>1"><input type="checkbox" name="select-all" id="select-all" class="mt-1" ></th> -->	
							<th class="text-center">User</th>
							<th class="text-center">Aadhar Card</th>
							<th>Aadhar Details</th>
							<th class="text-center">Aadhar Status</th>
							<th class="text-center">Pan Card</th>
							<th>PAN Details</th>
							<th class="text-center">PAN Status</th>
							<th class="text-center">Bank Account</th>
							<th class="text-center">A/C Details</th>
							<th class="text-center">A/C Status</th>
							<th class="text-center">Action</th>
						</tr>
					</thead>
					<!-- table body -->
					<tbody>
						<tr scope="row" ng-repeat="(key, row) in data.dataList"  >

							<td class="listed sm clearfix table_list userDetails">
								<img class="rounded-circle " ng-src="{{row.ProfilePic}}">
								<div class=" "><strong>{{row.FullName}}</strong>
								<div ng-if="row.Email" class=""><a class="text-dark" href="javascript:void(0)" target="_top">{{row.Email}}</a></div><div ng-if="!row.Email">-</div>
								</div>
							</td>

							<td class="text-center">
								<p ng-if="!row.MediaAadhar.MediaURL">-</p>
								<a ng-if="row.MediaAadhar.MediaURL"  ng-click="loadFormVerification(key,row.UserGUID,'Aadhar')"><span class="btn theme_btn  btn-secondary btn-sm ng-scope mb-2" ng-if="row.MediaAadhar.MediaURL"> View Front </span></a>
								<a ng-if="row.MediaAadharBack.MediaURL"  ng-click="loadFormVerification(key,row.UserGUID,'AadharBack')"><span class="btn theme_btn  btn-secondary btn-sm ng-scope mb-2" ng-if="row.MediaAadharBack.MediaURL"> View Back </span></a>
								<p ng-if="row.AadharStatus == 'Pending'">(<span am-time-ago="row.MediaAadhar.EntryDate" ></span>)</p>
							</td>

							<td>
								<!-- <div class="form-group mb-0" ng-if="row.MediaAadhar.MediaCaption.FullName">
									<label class="control-label">Name</label>
									<p class="text-muted">{{row.MediaAadhar.MediaCaption.FullName}}</p>
								</div> -->
								<div class="form-group mb-0" ng-if="row.MediaAadhar.MediaCaption.AadharCardNumber">
									<label class="control-label mb-0"><strong>Aadhar Card Number</strong></label>
									<p class="text-muted">{{row.MediaAadhar.MediaCaption.AadharCardNumber}}</p>
								</div>
								<!-- <div class="form-group mb-0" ng-if="row.MediaAadhar.MediaCaption.CountryCode">
									<label class="control-label">Country Code</label>
									<p class="text-muted">{{row.MediaAadhar.MediaCaption.CountryCode}}</p>
								</div> -->
								<div class="form-group">
									<span ng-if="!row.MediaAadhar.MediaCaption">-</span>
								</div>
							</td> 
								
							<td class="text-center"><span ng-if="row.AadharStatus" ng-class="{Pending:'text-danger', Verified:'text-success',Deleted:'text-danger',Blocked:'text-danger'}[row.AadharStatus]" >{{row.AadharStatus}}</span><span ng-if="!row.AadharStatus">-</span></td> 


							<td class="text-center">
								<p ng-if="!row.MediaPAN.MediaURL">-</p>
								<a ng-if="row.MediaPAN.MediaURL"  ng-click="loadFormVerification(key,row.UserGUID,'PAN')"><span class="btn theme_btn  btn-secondary btn-sm ng-scope mb-2" ng-if="row.MediaPAN.MediaURL"> View Pan </span></a>
								<p ng-if="row.PanStatus == 'Pending'">(<span am-time-ago="row.MediaPAN.EntryDate" ></span>)</p>
							</td>

							<td>
								<div class="form-group mb-0" ng-if="row.MediaPAN.MediaCaption.FullName">
									<label class="control-label mb-0"><strong>Name</strong></label>
									<p class="text-muted">{{row.MediaPAN.MediaCaption.FullName}}</p>
								</div>
								<div class="form-group mb-0" ng-if="row.MediaPAN.MediaCaption.PanCardNumber">
									<label class="control-label mb-0"><strong>PAN Number</strong></label>
									<p class="text-muted">{{row.MediaPAN.MediaCaption.PanCardNumber}}</p>
								</div>
								<div class="form-group mb-0" ng-if="row.MediaPAN.MediaCaption.CountryCode">
									<label class="control-label mb-0"><strong>Country Code</strong></label>
									<p class="text-muted mb-0">{{row.MediaPAN.MediaCaption.CountryCode}}</p>
								</div>
								<div class="form-group mb-0">
									<span ng-if="!row.MediaPAN.MediaCaption">-</span>
								</div>
							</td> 
								
							<td class="text-center"><span ng-if="row.PanStatus" ng-class="{Pending:'text-danger', Verified:'text-success',Deleted:'text-danger',Blocked:'text-danger'}[row.PanStatus]" >{{row.PanStatus}}</span><span ng-if="!row.PanStatus">-</span></td> 

							<td class="text-center">
								<p ng-if="!row.MediaBANK.MediaURL">-</p>
								<a ng-if="row.MediaBANK.MediaURL" ng-click="loadFormVerification(key,row.UserGUID,'BANK')"><span ng-if="row.MediaBANK.MediaURL" class="btn theme_btn  btn-secondary btn-sm ng-scope mb-2">View Bank</span></a>
								<p ng-if="row.BankStatus == 'Pending'">(<span am-time-ago="row.MediaBANK.EntryDate" ></span>)</p>
							</td>
							
							<td>
								<div class="form-group mb-0">
									<label class="control-label mb-0"><strong>Name</strong></label>
									<p class="text-muted">{{row.MediaBANK.MediaCaption.FullName}}</p>
								</div>
								<div class="form-group mb-0">
									<label class="control-label mb-0"><strong>Account Number</strong></label>
									<p class="text-muted">{{row.MediaBANK.MediaCaption.AccountNumber}}</p>
								</div>
								<div class="form-group mb-0">
									<label class="control-label mb-0"><strong>IFSC Code</strong></label>
									<p class="text-muted mb-0">{{row.MediaBANK.MediaCaption.IFSCCode}}</p>
								</div>
							</td> 

							<td class="text-center"><span ng-if="row.BankStatus" ng-class="{Pending:'text-danger', Verified:'text-success',Deleted:'text-danger',Blocked:'text-danger'}[row.BankStatus]" >{{row.BankStatus}}</span><span ng-if="!row.BankStatus">-</span></td> 

							<td class="text-center">
								<div class="form-group mb-1" >
									<select name="PanStatus" id="PanStatus" ng-model="PanStatus" class="form-control chosen-select select_wd mb-1" ng-change="verifyDetails(row.UserGUID,'PAN',PanStatus,row.MediaPAN.MediaGUID)">
										<option value="">PAN</option>
										<option value="Pending" ng-selected="row.PanStatus == 'Pending'">Pending</option>
										<option value="Verified" ng-selected="row.PanStatus == 'Verified'">Verified</option>
										<option value="Rejected" ng-selected="row.PanStatus == 'Rejected'">Rejected</option>
									</select>
									<!-- <span ng-if="row.PanStatus == 'Verified'" ng-class="{Pending:'text-danger', Verified:'text-success',Deleted:'text-danger',Blocked:'text-danger'}[row.PanStatus]" >PAN CARD {{row.PanStatus}}</span> -->
								</div>
									
								<div class="form-group mb-1" >
									<select name="BankStatus" id="BankStatus" ng-model="BankStatus" class="form-control chosen-select select_wd mb-1" ng-change="verifyDetails(row.UserGUID,'BANK',BankStatus,row.MediaBANK.MediaGUID)">
										<option value="">A/C</option>
										<option value="Pending" ng-selected="row.BankStatus == 'Pending'">Pending</option>
										<option value="Verified" ng-selected="row.BankStatus == 'Verified'">Verified</option>
										<option value="Rejected" ng-selected="row.BankStatus == 'Rejected'">Rejected</option>
									</select>
									<!-- <span ng-if="row.BankStatus == 'Verified'" ng-class="{Pending:'text-danger', Verified:'text-success',Deleted:'text-danger',Blocked:'text-danger'}[row.BankStatus]" >Bank Details {{row.BankStatus}}</span> -->
								</div>

								<div class="form-group mb-1" >
									<select name="AadharStatus" id="AadharStatus" ng-model="AadharStatus" class="form-control chosen-select select_wd mb-1" ng-change="verifyDetails(row.UserGUID,'Aadhar',AadharStatus,row.MediaAadhar.MediaGUID)">
										<option value="">Aadhar</option>
										<option value="Pending" ng-selected="row.AadharStatus == 'Pending'">Pending</option>
										<option value="Verified" ng-selected="row.AadharStatus == 'Verified'">Verified</option>
										<option value="Rejected" ng-selected="row.AadharStatus == 'Rejected'">Rejected</option>
									</select>
									<!-- <span ng-if="row.BankStatus == 'Verified'" ng-class="{Pending:'text-danger', Verified:'text-success',Deleted:'text-danger',Blocked:'text-danger'}[row.BankStatus]" >Bank Details {{row.BankStatus}}</span> -->
								</div>	
							</td>
						</tr>
					</tbody>
				</table>
		</form>
		<!-- no record -->
		<p class="no-records text-center" ng-if="data.noRecords">
			<span ng-if="data.dataList.length">No more records found.</span>
			<span ng-if="!data.dataList.length">No records found.</span>
		</p>
	</div>
	<!-- Data table/ -->
	
	<div class="modal fade" id="filter_model" ng-init="initDateRangePicker()">
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
								<div class="col-md-6">
									<div class="form-group">
										<label class="filter-col" for="PanStatus">Pan Status</label>
										<select id="PanStatus" name="PanStatus" class="form-control chosen-select select_wd">
											<option value="">Please Select</option>
											<option value="1">Pending</option>
											<option value="2">Verified</option>
											<option value="3">Rejected</option>
											<option value="9">Not Submitted</option>
										</select>   
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="filter-col" for="BankStatus">Bank Status</label>
										<select id="BankStatus" name="BankStatus" class="select_wd form-control chosen-select ">
											<option value="">Please Select</option>
											<option value="1">Pending</option>
											<option value="2">Verified</option>
											<option value="3">Rejected</option>
											<option value="9">Not Submitted</option>
										</select>   
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="filter-col" for="AadharStatus">Aadhar Status</label>
										<select id="AadharStatus" name="AadharStatus" class="select_wd form-control chosen-select ">
											<option value="">Please Select</option>
											<option value="1">Pending</option>
											<option value="2">Verified</option>
											<option value="3">Rejected</option>
											<option value="9">Not Submitted</option>
										</select>   
									</div>
								</div>
								<div class="col-md-6 registered_list">
									<div class="form-group">
										<label class="filter-col" for="ParentCategory">Registered Between</label>
										<div id="dateRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
											<i class="fa fa-calendar"></i>&nbsp;
											<span>Select Date Range</span> <i class="fa fa-caret-down"></i> 
										</div>
									</div>
								</div>
							</div>
						</div> <!-- form-area /-->
					</div> <!-- modal-body /-->

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" ng-click="resetUserForm()">Reset</button>
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
				<!-- form -->
				<form id="edit_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
				</form>
				<!-- /form -->
			</div>
		</div>
	</div>
	<!-- Verification Modal -->
	<div class="modal fade" id="Verification_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Verification</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<!-- form -->
				<form id="Verification_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
				</form>
				<!-- /form -->
			</div>
		</div>
	</div>
	<!-- Add cash bonus Modal -->
	<div class="modal fade" id="AddCashBonus_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Add Cash Bonus</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<!-- form -->
				<form id="addCash_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
				</form>
				<!-- /form -->
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



