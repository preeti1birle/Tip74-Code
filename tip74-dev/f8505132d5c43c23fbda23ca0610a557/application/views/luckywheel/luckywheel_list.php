<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="luckyWheelController"><!-- Body -->

	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		<span class="float-left records d-none d-sm-block">
			<span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
		</span>
		<div class="float-right"><!-- ng-if="filterData.CategoryTypes.length>1" -->
			<!-- <button  class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>
			<button class="btn btn-success btn-sm ml-1" ng-click="loadFormAdd();">Add <?php // if(!empty($_GET['ParentCategoryGUID'])){echo "Subcategory";}else{echo "Gift";}?></button> -->
		</div>

		<!-- <div class="float-right">
            <a href="LuckyWheel/transtion_list"><button class="btn btn-success btn-sm ml-1"> Transtion List </button></a>
        </div> -->
	</div>
	<!-- Top container/ -->


	<!-- Data table -->
	<div class="table-responsive block_pad_md" ng-init="getList()"> 
		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

		<div class="all-table-scroll">
			<!-- data table -->
			<table class="table table-striped table-condensed table-hover table-sortable all-table-scroll-item coupon_pad" ng-if="data.dataList.length">
				<!-- table heading -->
				<thead>
					<tr>
						<th style="width:80px;" class="text-center">Picture</th>
						<th style="width: 200px;">Points</th>
						<th style="width: 200px;">Pick</th>
						<th style="width: 200px;">Colour Picker</th>
						<th style="width: 100px;" class="text-center">Show</th>
						<th style="width: 100px;" class="text-center">Action</th>
					</tr>
				</thead>
				<!-- table body -->
				<tbody id="tabledivbody">
					<tr scope="row" ng-repeat="(key, row) in data.dataList" id="sectionsid_{{row.MenuOrder}}.{{row.CategoryID}}">
						<td class="listed sm text-center">
							<img ng-src="{{row.Image}}">
							<!-- <img ng-if="row.Image.Records[0].MediaThumbURL" ng-src="{{row.Image}}"> -->
						</td>

						<td>
							<strong>{{row.Points}}</strong>
						</td>

						<td>{{row.Pick}}</td>
						<td><span class="color_code" style="background-color: {{row.ColourCode}};"></span></td>


						<td class="text-center"><span ng-class="{Inactive:'text-danger', Active:'text-success'}[row.Status]">{{row.Status}}</span></td> 

						<td class="text-center">
							<div class="dropdown">
								<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#8230;</button>
								<div class="dropdown-menu dropdown-menu-left">
									<a class="dropdown-item" href="" ng-click="loadFormEdit(key, row.PointsID)">Edit</a>
									<!-- <a class="dropdown-item" href="" ng-click="loadFormDelete(key, row.CategoryGUID)">Delete</a> -->
								</div>
							</div>
						</td>
					</tr>










				</tbody>
			</table>
        </div>
		<!-- no record -->
		<p class="no-records text-center" ng-if="data.noRecords">
			<span ng-if="data.dataList.length">No more records found.</span>
			<span ng-if="!data.dataList.length">No records found.</span>
		</p>
	</div>
	<!-- Data table/ -->




	<!-- Filter Modal -->
	<div class="modal fade" id="filter_model"  ng-init="getFilterData()">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Filters</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>

				<!-- Filter form -->
				<form id="filterForm" role="form" autocomplete="off" class="ng-pristine ng-valid">
					<div class="modal-body">
						<div class="form-area">

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="CategoryTypeName">Category Type</label>
										<select id="CategoryTypeName" name="CategoryTypeName" class="form-control chosen-select">
											<option value="">All Categories</option>
											<option ng-repeat="row in filterData.CategoryTypes" value="{{row.CategoryTypeName}}">{{row.CategoryTypeName}}</option>
										</select>   
									</div>
								</div>
							</div>

						</div> <!-- form-area /-->
					</div> <!-- modal-body /-->

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button>
						<button type="submit" class="btn btn-success btn-sm" data-dismiss="modal" ng-disabled="editDataLoading" ng-click="applyFilter()">Apply</button>
					</div>

				</form>
				<!-- Filter form/ -->
			</div>
		</div>
	</div>



	<!-- add Modal -->
	<div class="modal fade" id="add_model">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Add <?php if(!empty($_GET['ParentCategoryGUID'])){echo "Subcategory";}else{echo "Gift";}?></h3>     	
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
					<h3 class="modal-title h5">Lucky Wheel</h3>     	
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



