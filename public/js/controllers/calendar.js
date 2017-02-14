copenhagenApp.controller('calendarCtrl', ['$scope', '$compile', '$timeout', 'uiCalendarConfig', 'API',
    function($scope, $compile, $timeout, uiCalendarConfig, API) {
        $scope.roomTypes = [];
        $scope.calendar = {};
        $scope.events = [];
        API.getRoomTypes().then(function(response) {
            $scope.roomTypes = response.data;
            $scope.calendar.roomType = response.data[0];
            if (response.data[0]) {
                API.getRoomCalendar(response.data[0].id).then(function(response) {
                    $scope.events = response.data;
                    console.log($scope.events);
                }, function(error) {
                    console.log(error);
                });
            }

        }, function(error) {
            console.log(error);
        });


        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        var firstDay = new Date(y, m - 1, 1);
        var lastDay = new Date(y, m + 12, 0);
        console.log(lastDay);
        for (var d = firstDay; d <= lastDay; d.setDate(d.getDate() + 1)) {
            /* event source that contains custom events on the scope */

            $scope.events.push({ title: '1 Total PHP 9999', start: new Date(d) });
        }

        /* alert on eventClick */
        $scope.alertOnEventClick = function(date, jsEvent, view) {
            $scope.alertMessage = (date.title + ' was clicked ');
        };
        /* alert on Drop */
        $scope.alertOnDrop = function(event, delta, revertFunc, jsEvent, ui, view) {
            $scope.alertMessage = ('Event Dropped to make dayDelta ' + delta);
        };
        /* alert on Resize */
        $scope.alertOnResize = function(event, delta, revertFunc, jsEvent, ui, view) {
            $scope.alertMessage = ('Event Resized to make dayDelta ' + delta);
        };
        /* add and removes an event source of choice */
        $scope.addRemoveEventSource = function(sources, source) {
            var canAdd = 0;
            angular.forEach(sources, function(value, key) {
                if (sources[key] === source) {
                    sources.splice(key, 1);
                    canAdd = 1;
                }
            });
            if (canAdd === 0) {
                sources.push(source);
            }
        };
        /* add custom event*/
        $scope.addEvent = function() {
            $scope.events.push({
                title: 'Open Sesame',
                start: new Date(y, m, 28),
                end: new Date(y, m, 29),
                className: ['openSesame']
            });
        };
        /* remove event */
        $scope.remove = function(index) {
            $scope.events.splice(index, 1);
        };
        /* Change View */
        $scope.changeView = function(view, calendar) {
            uiCalendarConfig.calendars[calendar].fullCalendar('changeView', view);
        };
        /* Change View */
        $scope.renderCalendar = function(calendar) {
            $timeout(function() {
                if (uiCalendarConfig.calendars[calendar]) {
                    uiCalendarConfig.calendars[calendar].fullCalendar('render');
                }
            });
        };
        /* Render Tooltip */
        $scope.eventRender = function(event, element, view) {
            element.attr({
                'tooltip': event.title,
                'tooltip-append-to-body': true
            });
            $compile(element)($scope);
        };
        /* config object */
        $scope.uiConfig = {
            calendar: {
                height: 450,
                editable: true,
                header: {
                    left: 'title',
                    center: '',
                    right: 'today prev,next'
                },
                eventClick: $scope.alertOnEventClick,
                eventDrop: $scope.alertOnDrop,
                eventResize: $scope.alertOnResize,
                eventRender: $scope.eventRender
            }
        };
        $scope.eventSources = [$scope.events];



        // Save Calendar
        $scope.saveCalendar = function(isValid) {
            if (isValid) {
                API.saveCalendar($scope.calendar).then(function(response) {
                    console.log(response.data);
                }, function(error) {
                    console.log(error);
                });
            }
        }
    }
]);