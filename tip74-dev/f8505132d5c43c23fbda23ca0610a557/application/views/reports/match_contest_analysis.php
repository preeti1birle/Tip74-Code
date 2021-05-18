<header class="panel-heading">
    <h1 class="h4"><?php echo $this->ModuleData['ModuleTitle']; ?></h1>
</header>
<div class="panel-body" ng-controller="PageController"><!-- Body -->
    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
            <b> Profit Loss Filters </b><br>
            <div>
                <div class="checkbox" ng-init="VisibleContest='All'">
                   <label><input type="radio" ng-model="VisibleContest" value="Profit">Profit</label>
                   <label><input type="radio" ng-model="VisibleContest" value="Loss">Loss</label>
                   <label><input type="radio" ng-model="VisibleContest" value="All" ng-checked = "true">All</label>                               
            </div>
        </div>
        <input type='hidden' id="FilterType" value="{{VisibleContest}}">
        <div class="float-right">
            <button class="btn theme_btn btn-secondary btn-sm ng-scope" ng-click="getMatchAllContestReportExports()"><i class="fa fa-file-excel-o"></i> Exports</button>
        </div>
        <div class="row w-100 pt-4 m-0">
                        <div class="col-lg-12  mb-4">
                            <div class="panel_grp block">
                                <div class="panel_title">Series: <strong>{{MatchDetails.SeriesName}} </strong></div>
                                <div class="panel_title">Match: <strong>{{MatchDetails.MatchName}}  ({{MatchDetails.MatchStartDateTime}})</strong></div>
                            </div>
                        </div>
                <div class="col-lg-12  mb-4"  ng-repeat="Contests in ContestAnalysisAll" ng-show="(VisibleContest != 'All') ? (VisibleContest == Contests.visible) ? 1 : 0 : 1">
                    <div class="panel_grp block">
                        <div class="panel_title" style="background-color: #fcb9a9;">Contest Details: {{Contests.ContestDetails.ContestName}} - {{Contests.ContestDetails.ContestType}} <span ng-show="Contests.Profit >0" class="text-success"> ( <i class=" fa fa-check"></i> Profit)</span>
                            <span ng-show="Contests.loss >0" class="text-danger"> (<i class=" fa fa-times"></i> loss) </span></div>
                        <div class="d-flex justify-content-between py-3 px-4">
                            <ul class="list_style col-lg-5">
                                <li>Category 1 Joined Users:</li>
                                <li>Category 2 Joined Users:</li>
                                <li>Total Deposit (Category 1):</li>
                                <li>Total Bonus (Category 1):</li>
                                <li>Total Winning (Category 1):</li>
                                <li>Net Profit:</li>
                                <li>Profit:</li>
                                <li>Loss:</li>
                            </ul>
                            <ul class="list_style col-lg-7">
                                <li><strong>{{(Contests.TotalRealUserJoined) ? Contests.TotalRealUserJoined : 0}}</strong></li>
                                <li><strong>{{(Contests.TotalVirtualUserJoined) ? Contests.TotalVirtualUserJoined : 0}}</strong></li>
                                <li><strong>Rs.{{Contests.TotalDepositCollection}}</strong></li>
                                <li><strong>Rs.{{Contests.TotalCashBonusCollection}}</strong></li>
                                <li><strong>Rs.{{Contests.TotalRealUserWinningCollection}}</strong></li>
                                <li style="color:green"><strong>Rs.{{Contests.NetProfit}}</strong></li>
                                <li style="color:green"><strong>Rs.{{Contests.Profit}}</strong>(Net Profit - 2.5% - bonus)</li>
                                <li style="color:red"><strong>Rs.{{Contests.loss}}</strong></li>
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


</div><!-- Body/ -->
<script>
function check(){
    $('#Contest_Type').text($('#ContestType').val());
    $('#Contest_Name').text($('#ContestName').val());
    var Seriesvalue = $("#SeriesGUID option:selected");
    $('#Series_GUID').text((Seriesvalue.text() == "Please Select") ? '' : Seriesvalue.text());
    var MatchGUIDvalue = $("#MatchGUID option:selected");
    $('#Match_GUID').text((MatchGUIDvalue.text() == "Please Select") ? '' : MatchGUIDvalue.text());
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