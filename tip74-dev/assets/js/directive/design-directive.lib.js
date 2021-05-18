
app.directive('popupHandler', function () {
    return {
        restrict: 'A', link: function ($scope, element) {
            $scope.closePopup = function (id) {
                if ($('#' + id).find('.close').length > 0) {
                    $('#' + id).trigger('click');
                } else {
                    $('#' + id).remove();
                }
                if ($('.modal-backdrop').length > 0) {
                    $('.modal-backdrop').remove();
                }
                $('body').removeClass('modal-open').css('padding-right', "");
            };
            $scope.openPopup = function (id) {
                var popup = $('#' + id);
                $('#' + id).modal('show');
            };
        },
    }
});
app.directive('loading', ['$http', function ($http) {
    return {
        restrict: 'A',
        template: '',
        link: function (scope, elm, attrs) {
            scope.isLoading = function () {
                return $http.pendingRequests.length > 0;
            };
            scope.showLoader = function () {
                elm.show();
            }
            scope.hideLoader = function () {
                elm.hide();
            }
            scope.$watch(scope.isLoading, function (v) {
                if (scope.loader) {
                    if (scope.loader.isLoading == false) {
                        elm.hide();
                    } else {
                        if (v) {
                            elm.show();
                        } else {
                            elm.hide();
                        }
                    }

                } else {
                    if (v) {
                        elm.show();
                    } else {
                        elm.hide();
                    }
                }

            });
        }
    };
}])
app.directive('numbersOnly', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                if (text) {
                    var transformedInput = text.replace(/[^0-9]/g, '');

                    if (transformedInput !== text) {
                        ngModelCtrl.$setViewValue(transformedInput);
                        ngModelCtrl.$render();
                    }
                    return transformedInput;
                }
                return undefined;
            }
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});
app.directive('timerText', ["$interval", "$filter", function ($interval, $filter) {
    return {
        restrict: 'A',
        link: function (scope, el, attrs) {


            var tick = function () {
                // date = scope.matches.MatchStartDateTime;

                date = attrs.timerData;
                status = attrs.matchStatus;
                type = attrs.matchType;

                var date1 = date;
                date1 = $filter('convertIntoUserTimeZone')(date1);
                var date3 = new Date(date1);
                var date2 = new Date();
                var date4 = $filter('date')(date1, "medium");
                if (date3 < date2 && status == 'Completed') {
                    scope.clock = '<span class="text-success">Completed</span>';
                    return false;
                } else if (date3 < date2 && status == 'Running') {
                    scope.clock = '<span class="text-success">Running</span>';
                    return false;
                } else if (date3 < date2 && status == 'Pending') {
                    scope.clock = '<span class="text-success">' + date4 + '</span>';
                    return false;
                } else {
                    // var diffInSeconds = Math.abs(date1 - date2) / 1000;
                    var diffInSeconds = Math.abs(moment().diff(date1) / 1000);
                    var days = Math.floor(diffInSeconds / 60 / 60 / 24);
                    var hours = Math.floor(diffInSeconds / 60 / 60 % 24);
                    var minutes = Math.floor(diffInSeconds / 60 % 60);
                    var seconds = Math.floor(diffInSeconds % 60);
                    days = days.toString();

                    minutes = minutes.toString();
                    hours = hours.toString();
                    seconds = seconds.toString();

                    if (type == 'createTeam' && type != undefined) {
                        var showTime = '';
                        if (days >= 2) {
                            showTime = days + 'days left';
                        } else if (days <= 2 && days != 0) {
                            showTime = days + ' days left';
                            hours = Number(days * 24) + Number(hours);
                            showTime = hours + 'h left';
                        } else if (hours <= 24 && hours > 0) {
                            if (minutes.length == 1) {
                                minutes = '0' + minutes;
                            }
                            showTime = hours + 'h ' + minutes + 'm left';
                        } else if (minutes > 0 && hours == 0) {
                            if (minutes.length == 1) {
                                minutes = '0' + minutes;
                            }
                            if (seconds.length == 1) {
                                seconds = '0' + seconds;
                            }
                            showTime = minutes + 'm ' + seconds + 's left';
                        } else if (minutes == 0) {
                            if (seconds.length == 1) {
                                seconds = '0' + seconds;
                            }
                            showTime = seconds + 's left';
                        }
                        scope.clock = showTime;
                    } else {
                        if (days.length == 1) {
                            days = '0' + days;
                        }
                        days = '<span>' + days + ' <strong>DAY</strong></span>';
                        if (minutes.length == 1) {
                            minutes = '0' + minutes;
                        }
                        minutes = '<span>' + minutes + ' <strong>MIN</strong></span>';
                        if (seconds.length == 1) {
                            seconds = '0' + seconds;
                        }
                        seconds = '<span>' + seconds + ' <strong>SEC</strong></span>';
                        if (hours.length == 1) {
                            hours = '0' + hours;
                        }
                        hours = '<span>' + hours + ' <strong>HRS</strong></span>';
                        var milliseconds = Math.round((diffInSeconds - Math.floor(diffInSeconds)) * 1000);
                        scope.clock = days + hours + minutes + seconds;
                    }


                }

            }
            tick();
            $interval(tick, 1000);
        }
    }
}]);
app.filter('trustAsHtml', ['$sce', function ($sce) {
    return function (input) {
        return $sce.trustAsHtml(input);
    };
}]);
app.filter('secondsToDateTime', [function () {
    return function (seconds) {
        return new Date(1970, 0, 1).setSeconds(seconds);
    };
}])
app.directive('scrolly', function ($location) {
    return {
        restrict: 'A',
        scope: true,
        link: function (scope, element, attrs) {
            var raw = element[0];
            var location = $location.absUrl();
            var location_array = location.split('/');
            var page_name = location_array[location_array.length - 1];
            element.bind('scroll', function () {
                if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {
                    if (page_name == 'leaderboard') {
                        scope.getLeaderBoardLeagueSeasonWise(false);
                        scope.getLeaderBoardMatchWise(false);
                        scope.getLeaderBoardWeekWise(false);
                    } else if(page_name == 'live-result-inner'){
                        scope.getLeaderBoardMatchWise(false);
                    } else if(page_name == 'myPrediction'){
                        scope.getPredictedMatches(false);
                    }else if(page_name == 'myEntries'){
                        scope.getAssignedEntries(false);
                    }
                    scope.$apply(attrs.scrolly);
                }
            });
        }
    };
});
app.filter('myDateFormat', function myDateFormat($filter) {
    return function (text) {
        var tempdate = new Date(text);
        return $filter('date')(tempdate, "medium");
    }
});
app.filter('convertIntoUserTimeZone', function () {
    return function (input) {
        var offset = new Date().getTimezoneOffset();
        offset = offset.toString();
        var plusSign = offset.indexOf("+");
        var minusSign = offset.indexOf("-");
        var timeZoneObj = {};
        timeZoneObj.offset = offset;
        if (plusSign > -1) {
            timeZoneObj.identifire = "-";
            timeZoneObj.totalMinutes = parseInt(offset.replace("+", ""));
        } else if (minusSign > -1) {
            timeZoneObj.identifire = "+";
            timeZoneObj.totalMinutes = parseInt(offset.replace("-", ""));
        } else {
            timeZoneObj.identifire = "-";
            timeZoneObj.totalMinutes = parseInt(offset);
        }
        let totalMinutes = timeZoneObj.totalMinutes;
        let totalHours = parseInt(totalMinutes / 60);
        let hourMinutes = 60 * totalHours;
        let reaminingMinutes = totalMinutes - hourMinutes;
        timeZoneObj.totalHours = totalHours;
        timeZoneObj.hourMinutes = hourMinutes;
        timeZoneObj.reaminingMinutes = reaminingMinutes;
        timeZoneObj.finalTimeZoneFormatted = ((totalHours > 10) ? totalHours : "0" + totalHours)
        let identifire = timeZoneObj.identifire;
        totalMinutes = timeZoneObj.totalMinutes;
        var utcTime = '';
        if (identifire === '+') {
            utcTime = moment(input).add(totalMinutes, 'minutes');
        } else {
            utcTime = moment(input).subtract(totalMinutes, 'minutes');
        }
        utcTime = moment(utcTime).format("LLL"); // March 19, 2018 4:04 PM
        return utcTime;
    }
});
app.directive('onErrorSrc', function () {
    return {
        link: function (scope, element, attrs) {
            element.bind('error', function () {
                if (attrs.src != attrs.onErrorSrc) {
                    attrs.$set('src', attrs.onErrorSrc);
                }
            });
        }
    }
});