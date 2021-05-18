<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="PageController" ><!-- Body -->

    <!-- Top container -->
    <div class="clearfix mt-2 mb-2 d_flex">
        <span class="float-left records hidden-sm-down flex_1">
            <span ng-if="data.dataList.length" class="h5">Total Records: {{data.totalRecords}}</span>
        </span>

        <div>
            <div class="float-right">
                <form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter(Status)" class="ng-pristine ng-valid">
                    <input type="text" class="form-control" name="Keyword" placeholder="Search">
                </form>
            </div>
            <div class="float-right">
                <button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>&nbsp;
            </div>
            <div class="float-right">
                <button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
            </div>
            <div class="float-right">
                <button class="btn theme_btn btn-secondary btn-sm ng-scope" ng-click="ExportList('csv')">CSV</button>&nbsp;
                <button class="btn theme_btn btn-secondary btn-sm ng-scope" ng-click="ExportExcel()">Excel</button>&nbsp;
            </div>
        </div>	
    </div>
    <!-- Top container/ -->

    <div class="row" >
        <div class="col-md-12 pl-2 pr-2">
            <div class="verified_tabs">
                <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-home" aria-selected="true" ng-click="applyFilter('Completed');">Completed</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-profile" aria-selected="false" ng-click="applyFilter('Pending');">Pending</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-contact" aria-selected="false" ng-click="applyFilter('Verified');">Processing</a>
                        <a class="nav-item nav-link" id="nav-withdraw-tab" data-toggle="tab" href="#" role="tab" aria-controls="nav-withdraw" aria-selected="false" ng-click="applyFilter('Rejected')">Rejected</a>
                    </div>
                </nav>
                <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="table-responsive block_pad_md" > 
                            <!-- Data table -->
                                <div class="table-responsive block_pad_md" infinite-scroll="getList('',Status)" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 

                            <!-- loading -->
                            <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>
                            <form name="records_form" id="records_form">
                                <!-- data table -->
                                <div class="">
                                    <table id="WithdrawalList" class="table table-striped table-hover all-table-scroll user_withdraw_table" ng-if="data.dataList.length">
                                        <!-- table heading -->
                                        <thead>
                                            <tr>
                                                    <!-- <th style="width: 50px;" class="text-center" ng-if="data.dataList.length>1"><input type="checkbox" name="select-all" id="select-all" class="mt-1" ></th> -->	
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Amount</th>
                                                <th>Payment Gateway</th>
                                                <th>Bank Details</th>
                                                <th>Status</th>
                                                <th>Entry Date</th>
                                                <th class="text-center">Action</th>

                                            </tr>
                                        </thead>
                                        <!-- table body -->
                                        <tbody>
                                            <tr scope="row" ng-repeat="(key, row) in data.dataList">
                                                <td class="listed sm clearfix table_list">
                                                    <img ng-src="{{row.ProfilePic}}" alt="User Image" class="thumbnail rounded-circle mr-2">
                                                    <span class="float-left">{{row.FirstName}}</span>
                                                </td>
                                                <td class="listed sm clearfix">
                                                    <div ng-if="row.Email" class=""><a class="text-dark" href="mailto:{{row.Email}}" target="_top">{{row.Email}}</a></div><div ng-if="!row.Email">-</div>	
                                                </td>
                                                <td>
                                                    <span ng-if="row.Amount">{{row.Amount}}</span><span ng-if="!row.Amount">-</span>
                                                </td>
                                                <td align="text-center">
                                                    <span ng-if="row.PaymentGateway">{{row.PaymentGateway}}</span><span ng-if="!row.Amount">-</span>
                                                </td> 
                                                <td ng-if="row.PaymentGateway == 'Bank'">
                                                    <span ng-if="!row.MediaBANK.MediaCaption.FullName">-</span><br>
                                                    <span ng-if="row.MediaBANK.MediaCaption.FullName">Name : {{row.MediaBANK.MediaCaption.FullName}}</span><br>
                                                    <span ng-if="row.MediaBANK.MediaCaption.Bank"> Bank : {{row.MediaBANK.MediaCaption.Bank}}</span>
                                                    <span ng-if="!row.MediaBANK.MediaCaption.FullName">-</span><br>
                                                    <span ng-if="row.MediaBANK.MediaCaption.AccountNumber"> A/C : {{row.MediaBANK.MediaCaption.AccountNumber}}</span>
                                                    <span ng-if="!row.MediaBANK.MediaCaption.AccountNumber">-</span><br>
                                                    <span ng-if="row.MediaBANK.MediaCaption.IFSCCode"> IFSC : {{row.MediaBANK.MediaCaption.IFSCCode}}</span>
                                                    <span ng-if="!row.MediaBANK.MediaCaption.IFSCCode">-</span>
                                                </td>
                                                <td ng-if="row.PaymentGateway == 'Paytm'">
                                                    {{row.PaytmPhoneNumber}}
                                                </td>
                                                <td class="text-center">
                                                    <span ng-if="row.Status" ng-class="{Pending:'text-danger', Completed:'text-success',Processing:'text-warning',Rejected:'text-danger'}[row.Status]">
                                                        {{row.Status}}</span><span ng-if="!row.Status">-</span><p ng-if="row.Status == 'Pending'">(<span am-time-ago="row.EntryDate" ></span>)</p>
                                                </td>
                                                <td>
                                                    <span ng-if="row.EntryDate">{{row.EntryDate}}</span><span ng-if="!row.EntryDate">-</span>
                                                </td> 
                                                <td>
                                                    <div class="form-group" ng-if="(row.Status == 'Processing' || row.Status == 'Pending')">
                                                        <select name="Status" ng-model="Status" id="Status" ng-init="Status = row.Status" class="form-control chosen-select" ng-change="editData(Status, row.WithdrawalID)">
                                                            <option value="">Please Select</option>
                                                            <option value="Pending" ng-if="row.Status != 'Processing'">Pending</option>
                                                            <option value="Verified" ng-if="row.Status != 'Processing'">Verify</option>
                                                            <option value="Processing" ng-if="row.Status == 'Verified' || row.Status == 'Processing'">Processing</option>
                                                            <option value="Rejected">Reject</option>
                                                            <option value="Completed" ng-if="row.Status == 'Verified' || row.Status == 'Processing'">Paid</option>
                                                        </select>
                                                    </div>
                                                    <div class="text-center" ng-if="row.Status == 'Rejected' || row.Status == 'Completed' || row.PaymentGateway == 'Paytm'">
                                                        <span>-</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>    
                            </form>
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
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="filter-col" for="Status">Status</label>
                                        <select id="Status" name="Status" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option value="Verified">Processing</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>   
                                    </div>
                                </div> -->
                                <input type="hidden" name="Status" ng-model="Status">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="filter-col">Payment Gateway</label>
                                        <select id="PaymentGateway" name="PaymentGateway" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Paytm">Paytm</option>
                                        </select>   
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="filter-col">From</label>
                                        <input type="date" name="FromDate" ng-model="FromDate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="filter-col">To</label>
                                        <input type="date" name="ToDate" ng-model="ToDate" class="form-control">
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

    <!-- edit Modal -->
    <div class="modal fade" id="edit_model">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Withdrawal Request</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <!-- form -->
                <form id="edit_form" name="edit_form" autocomplete="off" ng-include="templateURLEdit">
                </form>
                <!-- /form -->
            </div>
        </div>
    </div>

</div><!-- Body/ -->