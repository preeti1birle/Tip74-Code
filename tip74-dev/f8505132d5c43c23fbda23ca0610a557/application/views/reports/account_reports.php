<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="PageController"><!-- Body -->
    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" data-toggle="modal" data-target="#filter_model">Filter <img src="asset/img/filter.svg"></button>&nbsp;
        </div>
        <div class="float-right">
            <button class="btn btn-default btn-secondary btn-sm ng-scope" ng-click="reloadPage()"><img src="asset/img/reset.svg"></button>&nbsp;
        </div>
        <div class="float-right">
            <button class="btn theme_btn btn-secondary btn-sm ng-scope" ng-click="getAccountReportExport()"><i class="fa fa-file-excel-o"></i>CSV</button>
        </div>

        <div class="row w-100 pt-4 m-0">
            <div class="col-lg-12  mb-4">
                
                <b> Filters </b><br>
                <span> Series : <strong><span class="text-danger" id="Series_GUID"> </span></strong> </span><br>
                <span ng-hide="SeriesShow"> Start Date : <strong><span class="text-danger"  id="Start_Date"> {{MatchWiseDataReports.Matches[0].MatchDetails.SeriesStartDate}} </span> </strong></span><br>
                <span ng-hide="SeriesShow"> End Date : <strong><span class="text-danger"  id="End_Date">{{MatchWiseDataReports.Matches[0].MatchDetails.SeriesEndDate}} </span> </strong></span>

                <span ng-show="SeriesShow"> Start Date : <strong><span class="text-danger"> {{MatchWiseDataReports.Matches[0].MatchDetails.SeriesStartDate}} </span> </strong></span><br>
                <span ng-show="SeriesShow"> End Date : <strong><span class="text-danger">{{MatchWiseDataReports.Matches[0].MatchDetails.SeriesEndDate}} </span> </strong></span>
                
                <div class="panel_grp block" style="background-color: #fcb9a9;">
                   <div class="panel_title" style="background-color: #fcb9a9; font-weight: 900" ng-show="SeriesShow">Series:{{MatchWiseDataReports.Matches[0].MatchDetails.SeriesName}}  ({{MatchWiseDataReports.Matches[0].MatchDetails.SeriesStartDate}} - {{MatchWiseDataReports.Matches[0].MatchDetails.SeriesEndDate}} )</div>
                    <div class="d-flex justify-content-between py-3 px-4">
                        <ul class="list_style">
                            <li>Total Collection</li>
                            <li>Total Wallet Collection</li>
                            <li>Total Bonus Collection</li>
                            <li>Total Matches</li>
                        </ul>
                        <ul class="list_style">
                            <li><strong>Rs.{{MatchWiseDataReports.TotalSeriesCollection.TotalJoinContestCollection}}</strong></li>
                            <li><strong>Rs.{{MatchWiseDataReports.TotalSeriesCollection.TotalDepositCollection}}</strong></li>
                            <li><strong>Rs.{{MatchWiseDataReports.TotalSeriesCollection.TotalCashBonusCollection}}</strong></li>
                            <li><strong>{{MatchWiseDataReports.Matches.length}}</strong></li>                        
                        </ul>
                        <ul class="list_style">
                            <li>Total Winning Distribution  (Category 1)</li>
                            <li>Series Net Profit</li>
                            <li>Series Profit</li>
                            <li>Series Loss</li>
                            <!-- <li>Total Teams</li> -->
                        </ul>
                        <ul class="list_style">
                            <li><strong>Rs.{{MatchWiseDataReports.TotalSeriesCollection.TotalRealUserWinningCollection}}</strong></li>
                             <li><strong style="color:green">Rs.{{MatchWiseDataReports.TotalSeriesCollection.TotalNetProfit}}</strong></li>
                            <li><strong style="color:green">Rs.{{MatchWiseDataReports.TotalSeriesCollection.TotalProfit}}</strong></li>
                            <li><strong style="color:red">Rs.{{MatchWiseDataReports.TotalSeriesCollection.Totalloss}}</strong></li>
                          <!--   <li><strong>{{MatchWiseDataReports.TotalSeriesCollection.TotalTeams}}</strong></li> -->
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        
        
        <div class="row w-100 pt-4 m-0">
            <div class="col-lg-12  mb-4" ng-repeat="MatchList in MatchWiseDataReports.Matches">
                <div class="panel_grp block">
                    <div class="panel_title"> Match: {{MatchList.MatchDetails.SeriesName}} <strong>({{MatchList.MatchDetails.MatchName}} - {{MatchList.MatchDetails.MatchNo}} - {{MatchList.MatchDetails.MatchStartDateTime}})</strong> 
<!--                         <a href="reports?SeriesGUID={{SeriesGUID}}&MatchGUID={{MatchList.MatchDetails.MatchGUID}}" target="_blank"><button class="btn btn-default btn-secondary btn-sm">Match Analysis</button></a> -->
                    </div>
                    <div class="d-flex justify-content-between py-3 px-4">
                        <ul class="list_style">
                            <li>Total Collection</li>
                            <li>Total Wallet Collection</li>
                            <li>Total Bonus Collection</li>
                            <li>Total Contest</li>
                        </ul>
                        <ul class="list_style">
                            <li><strong>Rs.{{MatchList.TotalJoinContestCollection}}</strong></li>
                            <li><strong>Rs.{{MatchList.TotalDepositCollection}}</strong></li>
                            <li><strong>Rs.{{MatchList.TotalCashBonusCollection}}</strong></li>
                            <li><strong>{{MatchList.ContestDetails.TotalContest}}</strong> </li>
                        </ul>
                        <ul class="list_style">
                            <li>Total Winning Distribution  (Category 1)</li>
                            <li>Match Net Profit</li>
                            <li>Match Profit</li>
                            <li>Match Loss</li>
                        </ul>
                        <ul class="list_style">
                            <li><strong>Rs.{{MatchList.TotalRealUserWinningCollection}}</strong></li>
                             <li><strong style="color:green">Rs.{{MatchList.NetProfit}}</strong></li>
                            <li><strong style="color:green">Rs.{{MatchList.Profit}}</strong><div ng-show="MatchList.Profit>0 ? 1 : 0" class="text-danger">(Net Profit - 2.5% - bonus)</div></li>
                            <li><strong style="color:red">Rs.{{MatchList.loss}}</strong><br>
                             <a href="reports?SeriesGUID={{MatchList.MatchDetails.SeriesGUID}}&MatchGUID={{MatchList.MatchDetails.MatchGUID}}" target="_blank"><button class="btn btn-success btn-sm">Match Analysis</button></a>
                            <a href="reports/matchContestAnalysis?SeriesGUID={{MatchList.MatchDetails.SeriesGUID}}&MatchGUID={{MatchList.MatchDetails.MatchGUID}}&Contest=All" target="_blank"><button class="btn btn-success btn-sm">Contests Analysis</button></a>
                         </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                        <select id="SeriesGUID" name="SeriesGUID" ng-model="SeriesGUID" class="form-control chosen-select">
                                            <option value="">Please Select</option>
                                            <option ng-repeat="row in filterData.SeiresData" value="{{row.SeriesGUID}}">{{row.SeriesName}} ( End On {{row.SeriesEndDate}} )</option>
                                        </select>   
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="filter-col">Start Date</label>
                                        <input type="date" id="StartDate" name="FromDate" class="form-control txtDate"> 
                                    </div>
                                </div> </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group"> 
                                        <label class="filter-col">End Date</label>
                                        <input type="date" id="EndDate" name="ToDate" class="form-control txtDate"> 
                                    </div>
                                </div> </div>

                        </div> <!-- form-area /-->
                    </div> <!-- modal-body /-->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="$('#filterForm1').trigger('reset'); $('.chosen-select').trigger('chosen:updated');">Reset</button>
                        <button type="submit" class="btn btn-success btn-sm" data-dismiss="modal" onclick="check()" ng-disabled="editDataLoading" ng-click="getAccountReport()">Apply</button>
                    </div>

                </form>
                <!-- Filter form/ -->
            </div>
        </div>
    </div>

</div><!-- Body/ -->

<script>
function check(){
    var Seriesvalue = $("#SeriesGUID option:selected");
    $('#Series_GUID').text((Seriesvalue.text() == "Please Select") ? '' : Seriesvalue.text());
    $('#Start_Date').text($('#StartDate').val());
    $('#End_Date').text($('#EndDate').val());
}

$(document).ready(function(){
    var dtToday = new Date();

    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();

    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();

    var maxDate = year + '-' + month + '-' + day;    
    $('.txtDate').attr('max', maxDate);
});
</script>