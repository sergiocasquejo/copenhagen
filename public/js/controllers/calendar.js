copenhagenApp.controller('calendarCtrl', ['$scope', '$compile', '$timeout', 'API',
    function($scope, $compile, $timeout, API) {
        $scope.calendarView = 'month';
        $scope.viewDate = moment().startOf('month').toDate();
        var currentDate = moment($scope.viewDate).format('MMMM DD, YYYY');

        $scope.events = [];
        $scope.roomTypes = [];
        $scope.calendar = {
            roomType: null,
            from: $scope.currentDate,
            to: $scope.currentDate
        };

        API.getRoomTypes().then(function(response) {
            $scope.roomTypes = response.data;
            if (response.data[0]) {
                $scope.calendar.roomType = response.data[0];
                fetchCalendarByRoomID($scope.calendar.roomType.id)
            }
        }, function(error) {
            console.log(error);
        });

        function fetchCalendarByRoomID(roomID) {
            API.getRoomCalendar(roomID).then(function(response) {
                angular.forEach(response.data, function(data) {
                    var event = {
                        title: data.title, // The title of the event
                        startsAt: new Date(data.startsAt * 1000), // A javascript date object for when the event starts
                        endsAt: new Date(data.startsAt * 1000), // Optional - a javascript date object for when the event ends
                        color: { // can also be calendarConfig.colorTypes.warning for shortcuts to the deprecated event types
                            primary: '#e3bc08', // the primary event color (should be darker than secondary)
                            secondary: '#fdf1ba' // the secondary event color (should be lighter than primary)
                        },
                        actions: [{ // an array of actions that will be displayed next to the event title
                            label: '<i class=\'glyphicon glyphicon-pencil\'></i>', // the label of the action
                            cssClass: 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
                            onClick: function(args) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
                                console.log('Edit event', args.calendarEvent);
                            }
                        }],
                        draggable: true, //Allow an event to be dragged and dropped
                        resizable: true, //Allow an event to be resizable
                        incrementsBadgeTotal: true, //If set to false then will not count towards the badge total amount on the month and year view
                        recursOn: 'year', // If set the event will recur on the given period. Valid values are year or month
                        cssClass: 'a-css-class-name', //A CSS class (or more, just separate with spaces) that will be added to the event when it is displayed on each view. Useful for marking an event as selected / active etc
                        allDay: true // set to true to display the event as an all day event on the day view
                    };
                    $scope.events.push(event);
                });
            }, function(error) {
                console.log(error);
            });
        }



        $scope.rangeSelected = function(startDate, endDate) {
            $scope.calendar.from = startDate;
            $scope.calendar.to = endDate;
        };

        $scope.timespanClicked = function(event) {
            console.log(event);
        };

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