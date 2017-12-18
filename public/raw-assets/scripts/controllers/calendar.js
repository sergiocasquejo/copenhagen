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
            from: currentDate,
            to: currentDate,
            rates: []
        };

        var popupModal = null;


        API.getRoomTypes().then(function(response) {
            sc.roomTypes = response.data;
            if (response.data[0]) {
                sc.calendar.roomType = response.data[0];
                var params = sc.getCalStartAndEndDate(sc.viewDate);
                fetchCalendarByRoomID(sc.calendar.roomType.id, params);
            }
            sc.loaded = true;
        }, function(error) {
            if (error.status == 400) { showPopup('Error', error.data, sh); }
            sc.loaded = true;
        });

        API.getRates().then(function(response) {
            sc.rateLists = response.data;
        }, function(error) {
            if (error.status == 400) { showPopup('Error', error.data, sh); }
        });

        sc.showLoader = function() {
            popupModal = sh.openModal('globalPopup.html', 'Loading...', '<div class="progress"><div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">60% Complete</span></div></div>');
        }

        sc.updateCalendar = function() {
            sc.showLoader();
            fetchCalendarByRoomID(sc.calendar.roomType.id, sc.getCalStartAndEndDate(sc.viewDate));
        }


        function fetchCalendarByRoomID(roomID, params) {
            sc.events = [];
            sc.calendar.calendarRates = null;
            sc.availability = null;
            sc.isActive = 0;

            API.fetchCalendarByRoomIdAndDate(roomID, params.start, params.end).then(function(response) {

                angular.forEach(response.data, function(data) {
                    var event = {
                        title: data.calendarTitle,
                        startsAt: new Date(data.startsAt * 1000),
                        //endsAt: new Date(data.startsAt * 1000),
                        cssClass: 'a-css-class-name',
                        info: data,
                        allDay: true
                    };
                    sc.events.push(event);
                });

                popupModal.dismiss('cancel');
            }, function(error) {
                if (error.status == 400) { showPopup('Error', error.data, sh); }
            });



            API.fetchUnavailableCalendarByRoomId(
                roomID,
                params.start,
                params.end
            ).then(function(response) {
                angular.forEach(response.data, function(data) {
                    var event = {
                        title: 'NOT AVAILABLE', //data.calendarTitle,
                        startsAt: new Date(data.startsAt * 1000),
                        cssClass: 'cal-day-notavailable',
                        info: data,
                        allDay: true
                    };
                    sc.events.push(event);

                });

                popupModal.dismiss('cancel');
            }, function(error) {
                if (error.status == 400) { showPopup('Error', error.data, sh); }
            });


        }



        sc.rangeSelected = function(startDate, endDate) {
            sc.calendar.from = startDate;
            sc.calendar.to = endDate;
            sc.hasSelectedDate = true;
        };

        sc.resetSelected = function() {
            sc.calendar.from = null;
            sc.calendar.to = null;
            sc.hasSelectedDate = false;
        }

        sc.getCalStartAndEndDate = function(date) {
            var startDate = moment(date).subtract(moment(date).day() - 0, "days").format('YYYY-MM-DD');
            var endDate = moment(date).endOf('month').add(6 - moment(date).endOf('month').day(), "days").format('YYYY-MM-DD');

            return { 'start': startDate, 'end': endDate };
        }
        sc.timespanClicked = function(date, cell) {
            sc.hasSelectedDate = true;
            sc.calendar.to = sc.calendar.from = date;
            if (cell.events.length != 0) {
                sc.calendar.calendarRates = cell.events[0].info.calendarRates;
                sc.calendar.availability = cell.events[0].info.availability;
                sc.calendar.isActive = cell.events[0].info.isActive;
            } else {
                sc.calendar.calendarRates = sc.calendar.roomType.roomRates;
                sc.calendar.availability = sc.calendar.roomType.totalRooms;
                sc.calendar.isActive = sc.calendar.roomType.isActive;
            }



        }

        sc.changeCalendarViewMonth = function(viewDate) {
            sc.showLoader();
            var params = sc.getCalStartAndEndDate(viewDate);
            fetchCalendarByRoomID(sc.calendar.roomType.id, params);

        };
        // Save Calendar
        sc.saveCalendar = function(isValid) {
            if (isValid) {
                API.saveCalendar({
                    roomID: sc.calendar.roomType.id,
                    from: moment(sc.calendar.from).format('YYYY-MM-DD'),
                    to: moment(sc.calendar.to).format('YYYY-MM-DD'),
                    availability: sc.calendar.availability,
                    isActive: sc.calendar.isActive,
                    rates: sc.calendar.calendarRates
                }).then(function(response) {
                    var params = sc.getCalStartAndEndDate(sc.viewDate);
                    fetchCalendarByRoomID(sc.calendar.roomType.id, params);
                }, function(error) {
                    if (error.status == 400) { showPopup('Error', error.data, sh); }
                });
            }
        }


        sc.showLoader();
    }
]);