app.controller('PageController', function ($scope, $http, $timeout) {

    $scope.playersList = [];
    $scope.totalAddedPlayerlist = [];
    $scope.getPlayersList = function () {
        if ($scope.data.listLoading || $scope.data.noRecords)
            return;
        $scope.data.listLoading = true;
        var data = 'SessionKey=' + SessionKey + '&PlayerSource=Manual&Params=PlayerName,PlayerGUID';
        $http.post(API_URL_FOOTBALL + 'getPlayers', data, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) {
                $scope.playersList = response.Data.Records;
                $scope.playersList.forEach(e => {
                    e.isSelected = false;
                });
            } else {
                $scope.data.noRecords = true;
            }
            $scope.data.listLoading = false;
        });
    }

    $scope.getMatchDetail = function () {
        $scope.matchDetail = {};
        if (getQueryStringValue('MatchGUID')) {
            var MatchGUID = getQueryStringValue('MatchGUID');
            $scope.AllMatches = false;
        } else {
            var MatchGUID = '';
            $scope.AllMatches = true;
        }
        $http.post(API_URL_FOOTBALL + 'getMatches', 'MatchGUID=' + MatchGUID + '&Params=TeamGUIDLocal,LeagueGUID,TeamGUIDVisitor,Status,MatchClosedInMinutes,LeagueName,MatchNo,MatchStartDateTime,TeamNameLocal,TeamNameVisitor,TeamNameShortLocal,TeamNameShortVisitor,TeamFlagLocal,TeamFlagVisitor,VenueName&SessionKey=' + SessionKey, contentType).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                $scope.matchDetail = response.Data;
                var data = 'SessionKey=' + SessionKey + '&MatchGUID=' + getQueryStringValue('MatchGUID') + '&PlayerSource=Manual&Params=PlayerName,PlayerGUID,TeamGUID,PlayerRole';
                $http.post(API_URL_FOOTBALL + 'getPlayers', data, contentType).then(function (response) {
                    var response = response.data;
                    if (response.ResponseCode == 200) {
                        $scope.selectedPlayers = response.Data.Records;
                        $scope.localTeam = [];
                        $scope.visitorTeam = []
                        if ($scope.selectedPlayers) {
                            $scope.selectedPlayers.forEach(player => {
                                if (player.TeamGUID === $scope.matchDetail.TeamGUIDLocal) {
                                    $scope.localTeam.push(
                                        {
                                            'LocalPlayerGUID': player.PlayerGUID,
                                            'TeamGUIDLocal': player.TeamGUID,
                                            'LocalPlayerPosition': player.PlayerRole,

                                        })
                                } else {
                                    $scope.visitorTeam.push(
                                        {
                                            'VisitorPlayerGUID': player.PlayerGUID,
                                            'TeamGUIDVisitor': player.TeamGUID,
                                            'VisitorPlayerPosition': player.PlayerRole
                                        })
                                }
                            });
                        }
                        $scope.totalAddedPlayerlist = $scope.localTeam.map((item, i) => Object.assign({}, item, $scope.visitorTeam[i]));

                    } else {
                        $scope.data.noRecords = true;
                    }
                    if ($scope.totalAddedPlayerlist.length < 1) {
                        $scope.totalAddedPlayerlist.push({ 'LocalPlayerGUID': '', 'TeamGUIDLocal': $scope.matchDetail.TeamGUIDLocal, 'LocalPlayerPosition': '', 'VisitorPlayerGUID': '', 'TeamGUIDVisitor': $scope.matchDetail.TeamGUIDVisitor, 'VisitorPlayerPosition': '' })
                    }
                    $scope.data.listLoading = false;
                });

            }
        });

    }

    $scope.addRow = function () {
        $scope.totalAddedPlayerlist.push({ 'LocalPlayerGUID': '', 'TeamGUIDLocal': $scope.matchDetail.TeamGUIDLocal, 'LocalPlayerPosition': '', 'VisitorPlayerGUID': '', 'TeamGUIDVisitor': $scope.matchDetail.TeamGUIDVisitor, 'VisitorPlayerPosition': '' })
    }
    $scope.removeRow = function (index) {
        let data = $scope.totalAddedPlayerlist[index];
        if (data.LocalPlayerGUID) {
            let index = $scope.playersList.map(e => { return e.PlayerGUID }).indexOf(data.LocalPlayerGUID);
            if (index != -1) {
                $scope.playersList[index].isSelected = false;
            }
        }
        if (data.VisitorPlayerGUID) {
            let index = $scope.playersList.map(e => { return e.PlayerGUID }).indexOf(data.VisitorPlayerGUID);
            if (index != -1) {
                $scope.playersList[index].isSelected = false;
            }
        }
        $scope.totalAddedPlayerlist.splice(index, 1)
    }

    $scope.checkPlayer = function (type, PlayerGUID) {
        if (type != '') {
            let index = $scope.playersList.map(e => { return e.PlayerGUID }).indexOf(type);
            if (index != -1) {
                $scope.playersList[index].isSelected = false;
            }
        }

        let index = $scope.playersList.map(e => { return e.PlayerGUID }).indexOf(PlayerGUID);
        if (index != -1) {
            $scope.playersList[index].isSelected = true;
        }
    }

    $scope.addData = function (LeagueGUID) {
        if (getQueryStringValue('MatchGUID')) {
            var MatchGUID = getQueryStringValue('MatchGUID');
        } else {
            var MatchGUID = '';
        }
        var Data = {
            SessionKey: SessionKey,
            MatchGUID: MatchGUID,
            LeagueGUID: LeagueGUID,
            MatchPlayers: $scope.totalAddedPlayerlist

        }
        $http.post(API_URL_FOOTBALL + 'assignPlayers', Data, { "Content-Type": "application/json" }).then(function (response) {
            var response = response.data;
            if (response.ResponseCode == 200) { /* success case */
                alertify.success(response.Message);
            } else {
                alertify.error(response.Message);
            }
        });
    }
});