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
            to: sc.currentDate,
            rates: []
        };

        API.getRoomTypes().then(function(response) {
            sc.roomTypes = response.data;
            if (response.data[0]) {
                sc.calendar.roomType = response.data[0];
                var params = sc.getCalStartAndEndDate(sc.viewDate);
                fetchCalendarByRoomID(sc.calendar.roomType.id, params);
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

        function fetchCalendarByRoomID(roomID, params) {
            API.fetchCalendarByRoomIdAndDate(roomID, params.start, params.end).then(function(response) {

                angular.forEach(response.data, function(data) {
                    var event = {
                        title: data.calendarTitle,
                        startsAt: new Date(data.startsAt * 1000),
                        endsAt: new Date(data.startsAt * 1000),
                        cssClass: 'a-css-class-name',
                        info: data,
                        allDay: true
                    };
                    sc.events.push(event);
                });
            }, function(error) {
                showPopup('Error', error.data, sh);
            });
        }



        sc.rangeSelected = function(startDate, endDate) {
            sc.calendar.from = startDate;
            sc.calendar.to = endDate;
            sc.hasSelectedDate = true;
        };

        // sc.dayClicked = function(day) {
        //     sc.hasSelectedDate = true;
        //     sc.calendar.to = sc.calendar.from = day;
        //     // sc.calendar.to = sc.calendar.from = date;
        //     console.log(day);
        // };

        sc.getCalStartAndEndDate = function(date) {
            var startDate = moment(date).subtract(moment(date).day() - 0, "days").format('YYYY-MM-DD');
            var endDate = moment(date).endOf('month').add(6 - moment(date).endOf('month').day(), "days").format('YYYY-MM-DD');

            return { 'start': startDate, 'end': endDate };
        }
        sc.timespanClicked = function(date, cell) {

            sc.hasSelectedDate = true;
            sc.calendar.to = sc.calendar.from = date;
            if (cell.events[0].info != undefined) {
                sc.calendar.rates = cell.events[0].info.calendarRates;
                sc.calendar.availability = cell.events[0].info.availability;
                sc.calendar.isActive = cell.events[0].info.isActive;
            }


            console.log(sc.calendar);

        }


        // Save Calendar
        sc.saveCalendar = function(isValid) {
            if (isValid) {
                API.saveCalendar(sc.calendar).then(function(response) {
                    var params = sc.getCalStartAndEndDate(sc.viewDate);
                    fetchCalendarByRoomID(sc.calendar.roomType.id, params);
                }, function(error) {
                    showPopup('Error', error.data, sh);
                });
            }
        }
    }
]);