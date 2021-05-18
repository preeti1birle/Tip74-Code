<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>

<div class="panel-body" ng-controller="PageController"><!-- Body -->

    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
<!--        <div class="float-right">
            <form id="filterForm" role="form" autocomplete="off" ng-submit="applyFilter()" class="ng-pristine ng-valid">
                <input type="text" class="form-control ml-1" name="Keyword" placeholder="Search">
            </form>
        </div>-->
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model"><img src="asset/img/filter.svg"></button>&nbsp;
        </div>
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
        </div>
<!--        <span class="float-left records hidden-sm-down">
            <span class="h5">Total records: {{data.totalRecords}}</span>
        </span>-->
        <div class="row w-100 pt-4 m-0">
            <div class="col-lg-12">
               <div class="panel_grp block">
                <div class="panel_title"> Total Amount Collection </div>
                <div class="d-flex justify-content-between py-3 px-4">
                    
                    <ul class="list_style">
                        <li>Total Collection</li>
                        <li>Total Wallet Collection</li>
                        <li>Total Bonus Collection</li>

                    </ul>

                    <ul class="list_style">
                        <li>Rs.{{MatchWiseData.TotalJoinContestCollection}}</li>
                        <li>Rs.{{MatchWiseData.TotalDepositCollection}}</li>
                        <li>Rs.{{MatchWiseData.TotalCashBonusCollection}}</li>

                    </ul>
                </div>
                
              </div>
            </div>
            <div class="col-lg-6">
             <div class="panel_grp block mb-4">
                <div class="panel_title"> Total Winning Distribution Real Users </div>
                
                 <div class="d-flex justify-content-between py-3 px-4">
                    
                    <ul class="list_style">
                        <li>Total Winning Distribution</li>

                    </ul>

                    <ul class="list_style">
                        <li>Rs.{{MatchWiseData.TotalRealUserWinningCollection}}</li>

                    </ul>
                </div>
                
              </div>
            </div>
            <div class="col-lg-6">
             <div class="panel_grp block">
                <div class="panel_title"> Total Winning Distribution Virtual Users </div>
                
                 <div class="d-flex justify-content-between py-3 px-4">
                    
                    <ul class="list_style">
                        <li>Total Winning Distribution</li>

                    </ul>

                    <ul class="list_style">
                        <li>Rs.{{MatchWiseData.TotalVirtualUserWinningCollection}}</li>

                    </ul>
                </div>
                
              </div>
            </div>
        </div>
    </div>
    <!-- Top container/ -->

    <!-- Data table -->
    <div class="table-responsive block_pad_md" infinite-scroll-disabled='data.listLoading' infinite-scroll-distance="0"> 
        <!-- loading -->
        <p ng-if="data.listLoading" class="text-center data-loader"><img src="asset/img/loader.svg"></p>

        <!-- data table -->
    </div>
    <!-- Data table/ -->

    <!-- Filter Modal -->
    <div class="modal fade" id="filter_model"  ng-init="getFilterData()">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5">Get Reports</h3>     	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <!-- Filter form -->
                <form id="filterForm1" role="form" autocomplete="off" class="ng-pristine ng-valid">
                    <div class="modal-body">
                        <div class="form-area">

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="filter-col" for="CategoryTypeName">Series</label>
                                        <select id="SeriesGUID" name="SeriesGUID" ng-model="SeriesGUID" ng-change="getMatches(SeriesGUID, '')" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option ng-repeat="row in filterData.SeiresData" value="{{row.SeriesGUID}}">{{row.SeriesName}}</option>
                                        </select>   
                                    </div>
                                </div>
                            </div>

                            <div class="row">							
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="filter-col" for="ParentCategory">Match</label>
                                        <select id="MatchGUID" name="MatchGUID" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option ng-repeat="match in MatchData" value="{{match.MatchGUID}}">{{match.TeamNameLocal}} Vs {{match.TeamNameVisitor}} ON {{match.MatchStartDateTime}}</option>
                                        </select>
                                        <small>Select this option to select match according to selected series.</small>
                                    </div>
                                </div>
                            </div>
                            <!--
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label class="filter-col">From Date</label>
                                                                    <input type="date" name="FromDate" class="form-control"> 
                                                                    <label class="filter-col">To Date</label>
                                                                    <input type="date" name="ToDate" class="form-control"> 
                                                                </div>
                                                            </div>
                                                        </div>-->

                            <!-- <div class="row">
                                    <div class="col-md-8">
                                            <div class="form-group">
                                                    <label class="filter-col" for="Status">Game type</label>
                                                    <select id="GameType" name="GameType" class="form-control chosen-select">
                                                            <option value="">Please Select</option>
                                                            <option value="Advance">Advance</option>
                                                            <option value="Safe">Safe</option>
                                                    </select>   
                                            </div>
                                    </div>
                            </div>
                            -->
                            <!--                            <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label class="filter-col" for="Status">Entry type</label>
                                                                    <select id="EntryType" name="EntryType" class="form-control chosen-select">
                                                                        <option value="">Please Select</option>
                                                                        <option value="Multiple">Multiple</option>
                                                                        <option value="Single">Single</option>
                                                                    </select>   
                                                                </div>
                                                            </div>
                                                        </div>-->

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