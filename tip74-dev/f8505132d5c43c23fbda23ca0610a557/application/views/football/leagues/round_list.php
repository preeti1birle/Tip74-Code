<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>

<div class="panel-body" ng-controller="PageController" ng-init="loadRoundList()"><!-- Body -->
    <div class="clearfix mt-2 mb-2"> 
        <div class="form-group">
            <label class="filter-col" for="ParentCategory"><b>League Name:</b> {{LeagueData.LeagueName}}</label>
        </div>
        <span class="float-left records d-none d-sm-block">
            <span ng-if="RoundformData.TotalRecords" class="h5">Total Records: {{RoundformData.TotalRecords}}</span>
        </span>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Round Name</th>
                        <th class="text-center">Start Date</th>
                        <th class="text-center">End Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Total Matches</th>
                        <!-- <th class="text-center">Action</th> -->
                    </tr>
                </thead>
                <tbody>

                    <tr ng-repeat="list in RoundformData.Records" >
                        <td>{{$index + 1}}</td>
                        <td>{{LeagueData.LeagueName +" "+list.RoundName}}</td>
                        <td class="text-center">{{list.RoundStartDate}}</td>
                        <td class="text-center">{{list.RoundEndDate}}</td>
                        <td class="text-center"><span ng-class="{Active:'text-primary', Completed:'text-success',Inactive:'text-danger'}[list.Status]">{{list.Status}}</span></td>

                        <td class="text-center">{{list.TotalMatches}}</td>
                        <!-- <td class="text-center">
                        	<div class="dropdown action_toggle">
								<button class="btn btn-secondary  btn-sm action" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></button>
								<div class="dropdown-menu dropdown-menu-left">
									<a class="dropdown-item" target="_blank" href="football/players?RoundID={{list.RoundID}}">Players
									</a>
								</div>
							</div>
                        </td> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- hidden parameters -->
    <input type="hidden" name="ContestGUID" value="{{formData.ContestGUID}}">
    <!-- hidden parameters /-->



	<!-- Filter Modal -->
	<div class="modal fade" id="filter_model" >
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title h5">Filters</h3>     	
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>

				<!-- Filter form -->
				<form id="filterForm1" role="form" name="form" autocomplete="off" class="ng-pristine ng-valid">
					<div class="modal-body">
						<div class="form-area">

							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label class="filter-col" for="Status">Status</label>
										<select id="StatusID" name="StatusID" class="form-control chosen-select">
											<option value="">Please Select</option>
											<option value="2">Active</option>
											<option value="6">Inactive</option>
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