<header class="panel-heading">
  <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle'];?></h1>
</header>
<div class="panel-body" ng-controller="PageController" ><!-- Body -->

    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
        <span class="float-left records hidden-sm-down">
            <span ng-if="totalRecords" class="h5">Total records: {{totalRecords}}</span>
        </span>

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
    <!-- Top container/ -->

    <!-- Data table -->
    <div class="table-responsive block_pad_md" infinite-scroll="TotalDepositsList()" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 

        <!-- loading -->
        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>
        <form name="records_form" id="records_form">
            <!-- data table -->
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Amount</th>
                        <th>Payment Gateway</th>
                        <th>Transaction ID</th>
                        <th>Deposited Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="(key, row) in data.dataList" >
                        <td>{{row.FirstName}}</td>
                        <td>{{row.Email}}</td>
                        <td>{{row.PhoneNumber}}</td>
                        <td>{{row.Amount}}</td>
                        <td>{{row.PaymentGateway}}</td>
                        <td>{{row.TransactionID}}</td>
                        <td>{{row.EntryDate}}</td>
                    </tr>
                </tbody>
            </table>
        </form>
        <!-- no record -->
        <p class="no-records text-center">
            <span ng-if="totalRecords">No more records found.</span>
            <span ng-if="!totalRecords">No records found.</span>
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
                <form id="filterForm1" role="form" autocomplete="off" class="ng-pristine ng-valid">
                    <div class="modal-body">
                        <div class="form-area">

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



