'use strict';
copenhagenApp.controller('calendarCtrl', ['$scope', '$compile', '$timeout', 'API', 'sh', 'calendarConfig',
    function($scope, $compile, $timeout, API, sh, calendarConfig) {
        var sc = $scope;
        sc.loaded = false;
        sc.calendarView = 'month';
        sc.hasSelectedDate = false;
        sc.viewDate = moment().startOf('month').toDate();
        var currentDate = moment(sc.viewDate).format('MMMM DD, YYYY');

        sc.events = [];
        sc.roomTypes = [];
        sc.calendar = {
            roomType: null,
            from: sc.currentDate,
            to: sc.currentDate
        };

        API.getRoomTypes().then(function(response) {
            sc.roomTypes = response.data;
            if (response.data[0]) {
                sc.calendar.roomType = response.data[0];
                fetchCalendarByRoomID(sc.calendar.roomType.id)
            }
            sc.loaded = true;
        }, function(error) {
            showPopup('Error', error.data, sh);
            sc.loaded = true;
        });

        API.getRates().then(function(response) {
            sc.rateLists = response.data;
        }, function(error) {
            showPopup('Error', error.data, sh);
        });

        function fetchCalendarByRoomID(roomID) {
            API.getRoomCalendar(roomID).then(function(response) {
                // angular.forEach(response.data, function(data) {
                //     var event = {
                //         title: data.title,
                //         startsAt: new Date(data.startsAt * 1000),
                //         endsAt: new Date(data.startsAt * 1000),
                //         cssClass: 'a-css-class-name',
                //         allDay: true
                //     };
                //     sc.events.push(event);
                // });
            }, function(error) {
                showPopup('Error', error.data, sh);
            });
        }



        sc.rangeSelected = function(startDate, endDate) {
            sc.calendar.from = startDate;
            sc.calendar.to = endDate;
            sc.hasSelectedDate = true;
            console.log(sc.calendar.from, sc.calendar.to);
        };

        sc.dayClicked = function(day) {
            console.log('test');
            sc.hasSelectedDate = true;
            sc.calendar.to = sc.calendar.from = startDate;
            // sc.calendar.to = sc.calendar.from = date;
        };



        // Save Calendar
        sc.saveCalendar = function(isValid) {
            if (isValid) {
                API.saveCalendar(sc.calendar).then(function(response) {
                    console.log(response.data);
                }, function(error) {
                    showPopup('Error', error.data, sh);
                });
            }
        }
    }
]);