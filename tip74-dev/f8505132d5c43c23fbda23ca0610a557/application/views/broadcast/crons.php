<div class="panel-body" ng-controller="PageController" ><!-- Body -->

    <!-- Top container -->
    <div class="clearfix mt-2 mb-2">
    </div>
    <!-- Top container/ -->
    <!-- Data table -->
    <div class="table-responsive block_pad_md"> 

        <!-- loading -->

        <!-- data table -->
            <table class="table">
                <!-- table heading -->
                <thead>
                    <tr>
                        <th style="width: 500px;">Cron Name</th>
                        <th style="width: 200px;">Action</th>
                    </tr>

                <!-- table body -->
                <tbody>
                    <tr scope="row">
                        <td class="listed sm clearfix">Cricket: Get & Updates All Upcoming Matches Metric 100</td>
                        <td>
                             <button id="a"  class="btn btn-success" ng-click="RunCrons('utilities/getMatchesLive','a')">Click</button>
                        </td>
                    </tr>
                     <tr scope="row">

                        <td class="listed sm clearfix">Cricket: Get & Updates All Upcoming Players Metric 100</td>
                        <td>
                              <button id="b" class="btn btn-success" ng-click="RunCrons('utilities/getPlayersLive','b')">Click</button>
                        </td>
                     </tr>
                    <tr scope="row">
                        <td class="listed sm clearfix">Cricket: Get & Updates All Upcoming Series Metric 100</td>
                        <td>
                               <button id="c" class="btn btn-success" ng-click="RunCrons('utilities/getSeriesLive','c')">Click</button>
                        </td>
                    </tr>

                    <tr scope="row">
                        <td class="listed sm clearfix">Cricket: Get & Updates All Upcoming Matches Metric 101</td>
                        <td>
                            <button id="d" class="btn btn-success" ng-click="RunCrons('utilities/getMatchesLiveMatric101','d')">Click</button>
                        </td>
                    </tr>
                     <tr scope="row">

                        <td class="listed sm clearfix">Cricket: Get & Updates All Upcoming Players Metric 101</td>
                        <td>
                            <button id="e" class="btn btn-success" ng-click="RunCrons('utilities/getPlayersLiveMatric101','e')">Click</button>
                        </td>
                     </tr>

                    <tr scope="row">
                        <td class="listed sm clearfix">Football: Get & Updates All Upcoming Series</td>
                        <td>
                            <button id="f" class="btn btn-success" ng-click="RunCrons('football/utilities/getSeriesLive','f')">Click</button>
                        </td>
                     </tr>

                     <tr scope="row">
                        <td class="listed sm clearfix">Football: Get & Updates All Upcoming Matches</td>
                        <td>
                            <button id="g" class="btn btn-success" ng-click="RunCrons('football/utilities/getMatchesLive','g')">Click</button>
                        </td>
                     </tr>

                     <tr scope="row">
                        <td class="listed sm clearfix">Football: Get & Updates All Upcoming Players</td>
                        <td>
                            <button id="h" class="btn btn-success" ng-click="RunCrons('football/utilities/getPlayersLive','h')">Click</button>
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



