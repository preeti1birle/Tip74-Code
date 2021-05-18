<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="PageController"><!-- Body -->
	
	<!-- Top container -->
	<div class="clearfix mt-2 mb-2" ng-if="data.dataList.length"> 
		<span class="float-left records d-none d-sm-block">
			<span ng-if="data.dataList.length" class="h5">Total Records: {{data.totalRecords}}</span>
		</span>
		<div class="float-right">
			<!-- <button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>&nbsp; -->
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Horse</button>
		</div>
	</div>
	<!-- Top container/ -->
    <!-- Data table -->
    <div class="table-responsive block_pad_md" infinite-scroll="getHorseList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
        <!-- loading -->
        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

        <!-- data table -->
        <table class="table table-striped table-condensed table-hover table-sortable all-table-scroll football_series_table cricket_series_table" ng-if="data.dataList.length">
            <!-- table heading -->
            <thead>
                <tr>
					<th style="max-width: 50px;">#</th>
					<th class="text-center">Image</th>
                    <th style="min-width: 200px;" class="text-left">Horse Name</th>
                    <th class="text-center">Age</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <!-- table body -->
            <tbody id="tabledivbody">

				<tr scope="row" ng-repeat="(key, value) in data.dataList" id="sectionsid">
					<td class="user_table"  style="max-width: 50px;">
						{{key +1}}
					</td>
					<td class="listed sm text-center">
						<img class="rounded-circle float-left" ng-src="{{value.MediaURL}}">
						<!-- <img ng-if="!value.MediaURL" ng-src="./asset/img/default-coupon.png">
						<img ng-if="value.MediaURL" ng-src="{{value.MediaURL}}"> -->
					</td>
                    <td style="min-width: 200px;" class="text-left">
							<strong>{{value.HorseName}}</strong>
					</td>
					<td class="text-center">
                        {{value.Age}}
                    </td>
                    <td class="text-center">

                        {{value.Description}}
                    </td>
                    <td class="text-center">
                        <div class="dropdown action_toggle">
                            <button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu dropdown-menu-left">
                                <a class="dropdown-item" href="" ng-click="loadFormEdit(key, value.HorseGUID, 'Horse')">Edit</a>
                                <a class="dropdown-item" href="javascript:void(0)" ng-click="loadFormDelete(key, value.HorseGUID, 'Horse')">Delete</a>
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
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Add Horse</h3>     	
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
					<h3 class="modal-title h5">Edit Horse</h3>     	
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
					<h3 class="modal-title h5">Delete Horse</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<!-- form -->
				<form  ng-include="templateURLDelete">
				</form>
				<!-- /form -->
			</div>
		</div>
	</div>
</div><!-- Body/ -->