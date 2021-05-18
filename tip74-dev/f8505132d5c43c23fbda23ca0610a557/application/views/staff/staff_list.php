<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>
<div class="panel-body" ng-controller="PageController" ><!-- Body -->

	<!-- Top container -->
	<div class="clearfix mt-2 mb-2 d_flex">
		<span class="records hidden-sm-down" style="flex: 1;">
			<span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
		</span>

		<div>
			<div class="float-right">
				<form class="form-inline" id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter()">
					<input type="text" class="form-control" name="Keyword" placeholder="Search">
				</form>
			</div>

			<div class="float-right mr-2">		
				<button class="btn btn-success btn-sm ml-1 float-right" ng-click="loadFormAdd();">Add Staff</button>
			</div>
		</div>	
	</div>
	<!-- Top container/ -->



	<!-- Data table -->
	<div class="table-responsive block_pad_md" infinite-scroll="getList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 

		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>


		<form name="records_form" id="records_form" class="pb-0">
			<!-- data table -->
			
				<table class="table table-striped table-hover all-table-scroll  admin_staff_table" ng-if="data.dataList.length">
					<!-- table heading -->
					<thead>
						<tr>
							<th>User</th>
							<th>Contact No.</th>
							<th>Role</th>
							<th>Registered On</th>
							<th>Last Login</th>
							<th class="text-center">Status</th>
							<th class="text-center">Action</th>

						</tr>
					</thead>
					<!-- table body -->
					<tbody>
						<tr scope="row" ng-repeat="(key, row) in data.dataList">
							
							<td class="listed sm clearfix table_list">
								<img class="rounded-circle float-left" ng-src="{{row.ProfilePic}}">
								<div class="content float-left"><strong>{{row.FullName}}</strong>
								<div class="user_table"><a href="mailto:{{row.Email}}" target="_top">{{(row.Email ? row.Email : row.EmailForChange)}}</a></div><!-- <div ng-if="!row.Email">-</div> -->
								</div>

							</td> 
							<td><span ng-if="row.PhoneNumber">{{row.PhoneNumber}}</span><span ng-if="!row.PhoneNumber">-</span></td> 
							<td ng-bind="row.UserTypeName"></td> 
							<td ng-bind="row.RegisteredOn"></td>  
							<td><span ng-if="row.LastLoginDate">{{row.LastLoginDate}}</span><span ng-if="!row.LastLoginDate">-</span></td> 
							<td class="text-center"><span ng-class="{Pending:'text-danger', Verified:'text-success',Deleted:'text-danger',Blocked:'text-danger'}[row.Status]">{{row.Status}}</span></td> 
							<td class="text-center">
								<div class="dropdown action_toggle">
									<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-if="data.UserGUID!=row.UserGUID"><i class="fa fa-ellipsis-h"></i></button>
									<div class="dropdown-menu dropdown-menu-left">
										<a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.UserGUID)">Edit</a>
										<a class="dropdown-item" href="javascript:void(0)" ng-click="loadFormChangePassword(key, row.UserGUID)">Change Password</a>
										<a class="dropdown-item" href="" ng-click="loadFormDelete(key, row.UserGUID)">Delete</a>
									</div>
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

	<div class="modal fade" id="changeUserPassword_form" >
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Change Password</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>

				<!-- Filter form -->
				<form id="changePassword_form" role="form" name="changePassword_form" autocomplete="off" class="ng-pristine ng-valid">
					<div class="modal-body">
						<div class="form-area">
							<div class="row">
								<div class="col-md-8 offset-md-2">
									<div class="form-group">
										<input type="password" name="Password" class="form-control" placeholder="New Password">
										<input type="hidden" name="UserGUID" class="form-control" value="{{ChangePasswordformData.UserGUID}}">
									</div>
								</div>
							</div>
						</div> <!-- form-area /-->
					</div> <!-- modal-body /-->

					<div class="modal-footer">
						<button type="submit" class="btn btn-success btn-sm m-auto"  ng-disabled="changeCP" ng-click="changeUserPassword()">Submit</button>
					</div>

				</form>
				<!-- Filter form/ -->
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



