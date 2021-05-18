<?php include('header.php') ?>
<section class="common_bg" ng-controller="predictionController" ng-cloak ng-init="getLeague();getFullStanding()">
	<div class="container burger">
		<div class="row">
			<div class="col-6"><h2 class="mb-5 mt-5 text-white text-left">{{League.LeagueName}} Standings</h2></div>
			<div class="col-6"><button class="btn bg-gradient back-btn-position2" ng-click="goBack()">Go Back</button></div>
		</div>
		<div class="standings">
			<table class=" standing_table table_scroll common_table">
			<thead>
				<tr>
					<th>Rank</th>
					<th>Team</th>
					<th data-toggle="tooltip" data-placement="top" title="Game Played">P</th>
					<th data-toggle="tooltip" data-placement="top" title="Won">W</th>
					<th data-toggle="tooltip" data-placement="top" title="Lost">L</th>
					<th data-toggle="tooltip" data-placement="top" title="Drawn">D</th>
					<th data-toggle="tooltip" data-placement="top" title="Goal For">GF</th>
					<th data-toggle="tooltip" data-placement="top" title="Goal Against">GA</th>
					<th data-toggle="tooltip" data-placement="top" title="Goal Difference">GD</th>
					<th>Pts</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="state in MatchFullStanding" style="cursor:pointer" ng-click="teamModal(state,state.TeamGUID,'')">
					<td>{{state.TeamStandings.Position}}</td>
					<td><img ng-src="{{state.TeamFlag}}" alt="{{state.TeamNameShort}}" class="img-fluid player_profile"> {{state.TeamName}}</td>
					<td>{{state.TeamStandings.Overall.GamePlayed}}</td>
					<td>{{state.TeamStandings.Overall.Won}}</td>
					<td>{{state.TeamStandings.Overall.Lost}}</td>
					<td>{{state.TeamStandings.Overall.Draw}}</td>
					<td>{{state.TeamStandings.Overall.GoalFor}}</td>
					<td>{{state.TeamStandings.Overall.GoalAgainst}}</td>
					<td class="{{state.TeamStandings.GoalDifference > 0?'text-success':'text-danger'}}">{{state.TeamStandings.GoalDifference}}</td>
					<td >{{state.TeamStandings.Points}}</td>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
</section>
<?php include('footerHome.php') ?>
