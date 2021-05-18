<header class="panel-heading">
  <h1 class="h4">Assign Match Players</h1>
</header>

<div class="panel-body" ng-controller="PageController" ng-init="getPlayersList();getMatchDetail()"> <!-- Body -->
  <div class="clearfix mt-2 mb-2">
    <div class="form-area">  
      <div class="row" style="text-align: center;">
        <div class="col-md-4">
          <div class="form-group">
            <img ng-src="{{matchDetail.TeamFlagLocal}}" width="100px" height="50px">
          </div>
          <div class="form-group">
            <p><strong>{{matchDetail.TeamNameLocal}}</strong></p>
          </div>
        </div>
        <div class="col-md-4">
            <h6 class="display-4 text-muted">v/s</h6>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <img ng-src="{{matchDetail.TeamFlagVisitor}}" width="100px" height="50px">
          </div>
          <div class="form-group">
            <p><strong>{{matchDetail.TeamNameVisitor}}</strong></p>
          </div>
        </div>
      </div>
      <hr>
      <div><h4>Match Start Datetime: {{matchDetail.MatchStartDateTime}}</h4>
    </div>
    <div>
      <!-- Data table -->
      <div class="table-responsive block_pad_md"> 
        <!-- data table -->
        <table class="footbal_player_manage_table table table-striped table-condensed table-hover table-sortable" >
          <!-- table heading -->
          <thead>
            <tr>
              <th style="width: 22%;">Player Local</th>
              <th style="width: 22%;">Player Position Local</th>
              <th style="width: 22%;">Player Visitor</th>
              <th style="width: 22%;">Player Position Visitor</th>
              <th style="width: 12px;" class="text-center">Action</th>
            </tr>
          </thead>
          <!-- table body -->
          <tbody id="tabledivbody">
            <tr scope="row" ng-repeat="(key, row) in totalAddedPlayerlist">
              <td>
                <div class="">
                <!-- <strong>{{row.PlayerName}}</strong> -->
                  <div class="form-group mb-0">
                    <select name="PlayerLocal" id="PlayerLocal" ng-change="checkPlayer('{{row.LocalPlayerGUID}}',row.LocalPlayerGUID)" placeholder="Player" class="form-control" ng-model="row.LocalPlayerGUID">
                      <option value="">Select Player Local</option>
                      <option value="{{player.PlayerGUID}}" ng-if="!player.isSelected || player.PlayerGUID == row.LocalPlayerGUID" ng-repeat="player in playersList" >{{player.PlayerName}}</option>
                    </select>
                  </div>
                </div>
              </td>  
              <td>
                <div class="">
                  <div class="form-group mb-0">
                    <select name="PlayerLocalPosition" id="PlayerLocalPosition" placeholder="Player" class="form-control" ng-model="row.LocalPlayerPosition">
                      <option value="">Select Player Local Position</option>
                      <option value="Goalkeeper"  >Goalkeeper</option>
                      <option value="Defender"  >Defender</option>
                      <option value="Midfielder"  >Midfielder</option>
                      <option value="Forward" >Forward</option>
                    </select>
                  </div>
                </div>
              </td>
              <td>
                <div class="">
                  <div class="form-group mb-0">
                  <select name="PlayerVisitor" id="PlayerVisitor" ng-change="checkPlayer('{{row.VisitorPlayerGUID}}',row.VisitorPlayerGUID)" placeholder="Player" class="form-control " ng-model="row.VisitorPlayerGUID">
                      <option value="">Select Player Visitor</option>
                      <option value="{{player.PlayerGUID}}" ng-if="!player.isSelected || player.PlayerGUID == row.VisitorPlayerGUID" ng-repeat="player in playersList" >{{player.PlayerName}}</option>
                    </select>
                  </div>
                </div>
              </td>
              <td>
                <div class="">
                  <div class="form-group mb-0">
                    <select name="PlayerVisitorPosition" id="PlayerVisitorPosition" placeholder="Player" class="form-control " ng-model="row.VisitorPlayerPosition">
                      <option value="">Select PlayerVisitor Position</option>
                      <option value="Goalkeeper"  >Goalkeeper</option>
                      <option value="Defender"  >Defender</option>
                      <option value="Midfielder"  >Midfielder</option>
                      <option value="Forward"  >Forward</option>
                    </select>
                  </div>
                </div>
              </td>
              
              <td class="text-center">
                    <button type="button" style="cursor:pointer;" class="btn-sm btn-success" ng-click="addRow()">+</button>
                    <button type="button" style="cursor:pointer;" ng-if="totalAddedPlayerlist.length != 1" class="btn-sm btn-danger" ng-click="removeRow(key)">-</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Reset</button> -->
        <button type="submit" class="btn btn-success btn-sm" ng-click="addData(matchDetail.LeagueGUID)">Submit</button>
      </div>
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