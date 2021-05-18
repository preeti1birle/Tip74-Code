<div class="panel-body" ng-controller="PageController" ><!-- Body -->
	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		<span class="float-left records hidden-sm-down">
			<span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
		</span>
		<div class="float-right">
			<button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>&nbsp;
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Coupon</button>
		</div>
	</div>
	<!-- Top container/ -->


	<!-- Data table -->
	<div class="table-responsive block_pad_md" infinite-scroll="getList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 

		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

		<!-- data table -->
		
			<table class="table table-striped table-hover all-table-scroll store_coupon_table 	 coupon_pad" ng-if="data.dataList.length">
				<!-- table heading -->
				<thead>
					<tr>
						<th class="text-center">Banner</th>
						<th> Coupon Code</th>
						<th> Coupon Type</th>
						<th> Title</th>
						<th>Description</th>
						<th  class="text-center">Value</th>
						<th  class="text-center">Created on</th>
						<th  class="text-center">Valid Till</th>
						<th  class="text-center">Minium Amount</th>
						<th  class="text-center">Maximum Amount</th>
						<th  class="text-center">No. Of Uses</th>
						<th  class="text-center">Status</th>
						<th  class="text-center">Action</th>
					</tr>
				</thead>
				<!-- table body -->
				<tbody>
					<tr scope="row" ng-repeat="(key, row) in data.dataList">

						<td class="listed sm text-center">
							<img ng-if="!row.MediaURL" ng-src="./asset/img/default-coupon.png">
							<img ng-if="row.MediaURL" ng-src="{{row.MediaURL}}">
						</td>


						<td class="text-center"><strong>{{row.CouponCode}}</strong></td>
						<td class="text-center"><strong>{{row.OfferType}}</strong></td>
						<td>{{row.CouponTitle}}</td>
						<td>{{row.CouponDescription}}</td>
						<td class="text-center">{{row.CouponValue}}<span ng-if="row.CouponType=='Percentage'">%</span></td>
						<td>{{row.EntryDate}}</td>
						<td>{{row.CouponValidTillDate}}</td>
						<td>{{row.MiniumAmount}}</td>
						<td>{{row.MaximumAmount}}</td>
						<td>{{row.NumberOfUses}}</td>
						<td class="text-center"><span ng-class="{Inactive:'text-danger', Active:'text-success'}[row.Status]">{{row.Status}}</span></td> 
						<td class="text-center">
							<div class="dropdown action_toggle">
								<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-ellipsis-h"></i></button>
								<div class="dropdown-menu dropdown-menu-left">
									<a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.CouponGUID)">Edit</a>
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


	<!-- add Modal -->
	<div class="modal fade" id="add_model">
		<div class="modal-dialog modal-lg" role="document">
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
	<div class="modal fade" id="Edit_model">
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
										<label class="filter-col" for="Status">Status</label>
										<select id="Status" name="Status" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
										</select>   
									</div>
								</div>
							</div>
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

</div><!-- Body/ -->



