<header class="panel-heading">
  	<h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="PageController"><!-- Body -->
	<!-- Top container -->
	<div class="clearfix mt-2 mb-2">
		<!-- <span class="float-left records hidden-sm-down">
			<span ng-if="data.dataList.length" class="h5">Total records: {{data.totalRecords}}</span>
		</span> -->
		<!-- <div class="float-right">
			<button class="btn btn-success btn-sm" ng-click="loadFormAdd();">Add Page</button>
		</div> -->
	</div>
	<!-- Top container/ -->

	<!-- Nav tabs -->
	<!-- <ul class="nav nav-tabs" style="max-width: 300px; margin: auto;">
		<li class="nav-item" ng-repeat="(key, row) in data.dataList">
			<a class="nav-link" ng-class="(PageGUID==row.PageGUID ? 'active' : '')" href="page?PageGUID={{row.PageGUID}}">{{row.Title}}</a>
		</li>
	</ul> -->



	<div class="form-area" style="max-width:900px; margin: auto; border:1px solid #f7f7f7; padding:10px;">
		<form id="add_form" name="add_form" autocomplete="off" >
		<div class="row">
		  <div class="col-md-6">
				<div class="form-group">
					<label class="control-label">Pages</label>
					<select name="PageGUID" id="PageGUID" ng-model="PageGUID" class="form-control" ng-change="getData(PageGUID)">
						<option value="">Select Page</option>
						<option value="aboutus">About Us</option>
						<option value="contactus">Contact Us</option>
						<option value="privacypolicy">Privacy Policy</option>
						<option value="termsconditions">Terms & Conditions</option>
						<option value="disclaimer">Disclaimer</option>
						<option value="howitworks">How It Works</option>
						<option value="soccerrules">Soccer Rules</option>
						<option value="soccerpoints">Soccer Points</option>
						<option value="racingrules">Horse Racing Rules</option>
						<option value="racingpoints">Horse Racing Points</option>
					</select>
				</div>
			</div>
		  </div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Title</label>
						<input type="text" name="Title" id="Title" class="form-control" ng-model="formData.Title">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label">Content</label>
						<textarea id="editor" name="Content" class="form-control" style="min-height:300px"></textarea>
					</div>
				</div>
			</div>
		</form>
		<button type="submit" class="btn btn-success btn-sm"  ng-click="addData()">Save</button>

	</div>

<!-- add Modal -->
    <!-- <div class="modal fade" id="add_model">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Add Page</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
				    <div class="form-area">
				        <form id="add_form" name="add_form" autocomplete="off" >
				            <div class="row">
				            	<div class="col-md-12">
				                    <div class="form-group">
				                        <label class="filter-col">Page Title</label>
				                        <input type="text" class="form-control" name="Title">
				                    </div>
				                </div>
				            </div>
				        </form>

				    </div>
				</div>

				<div class="modal-footer">
				    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
				    <button type="submit" class="btn btn-success btn-sm" ng-disabled="addDataLoading" ng-click="addData()">Save</button>
				</div>
            </div>
        </div>
    </div> -->
</div><!-- Body/ -->



