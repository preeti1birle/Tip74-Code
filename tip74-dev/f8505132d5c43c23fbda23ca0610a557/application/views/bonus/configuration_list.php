<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>
<div class="panel-body" ng-controller="PageController" ng-init="getList()"><!-- Body -->

	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		
	</div>
	<!-- Top container/ -->


	<!-- Data table -->
	<div class="table-responsive block_pad_md" > 
		<!-- loading -->
		<p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>
		<form method="post" id="generalPoint_form" name="generalPoint_form"  autocomplete='off'>
		<!-- data table for General Points -->
		
		<table class="table table-striped table-condensed table-hover table-sortable all-table-scroll config_bonus_table" ng-show="data.dataList.length > 0">
			<!-- table heading -->
			<thead>
				<tr>
					<th >Config Type</th>
					<th style="width: 150px;" >Config Type Value</th>
					<th style="width: 150px;" class="text-center">Status</th>
					<th style="width: 100px;" class="text-center">Action</th>
				</tr>
			</thead>
			<!-- table body -->
			<tbody id="tabledivbody">
				
					<tr scope="row" ng-repeat="(key, row) in data.dataList" ng-if="row.ConfigTypeGUID != 'AndroidAppFeatures'">	
						<td>
							<strong>{{row.ConfigTypeDescprition}}</strong>
						</td>
						<td>
							<div class="form-group mb-0">
								<input type="text" class="form-control numeric " ng-model="row.ConfigTypeValue" >
							</div>
						</td>
						<td>
							<div class="form-group mb-0">
								<select name="Status" id="Status" class="form-control chosen-select" ng-model="row.Status">
									<option value="Active" ng-selected="row.Status=='Active'" >Active</option>
									<option value="Inactive" ng-selected="row.Status=='Inactive'" >Inactive</option>
								</select>
							</div>
						</td>
						<td>
							<button class="btn btn-success btn-sm" ng-click="updateConfigData(row.ConfigTypeGUID,row.ConfigTypeValue,row.Status)">Update</button>
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

	<div class="modal fade" id="appAPKUpdateMsg_model">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Add New Android Features</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<!-- form -->
				<form id="appAPKUpdateMsg_form" name="appAPKUpdateMsg_form" ng-submit="saveAPKUpdates(appAPKUpdateMsg_form)" autocomplete="off">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<strong class="control-label text-center mb-3 d-block h4">Message</strong>
									<textarea name="Message" id="editor" class="form-control" ng-model="editor" rows="15"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-success btn-sm" >Save</button>
					</div>
				</form>
				<!-- /form -->
			</div>
		</div>
	</div>
	<!-- Data table/ -->


</div><!-- Body/ -->