'use strict';
copenhagenApp.controller('bookingsCtrl', ['$scope', '$compile', '$timeout', 'API', 'sh', 'calendarConfig',
    function($scope, $compile, $timeout, API, sh, calendarConfig) {
        var sc = $scope;
        sc.bookings = [];
        sc.calendarView = 'month';
        sc.viewDate = new Date();
        sc.cellIsOpen = true;

        var actions = [{
            label: '<i class=\'glyphicon glyphicon glyphicon-eye-open\'></i>',
            onClick: function(args) {
                var data = args.calendarEvent;
                showPopup('#' + data.info.id,
                    '<div class="booking-info-main">' +
                    '<h4>Booking Information</h4>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Check In:</strong> ' + data.info.checkIn + '</div>' +
                    '<div class="col-md-6"><strong>Check Out:</strong> ' + data.info.checkOut + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Adults:</strong> ' + data.info.noOfAdults + '</div>' +
                    '<div class="col-md-6"><strong>Child: </strong>' + data.info.noOfChild + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Special Instruction:</strong> <i>' + data.info.specialInstructions + '</i></div>' +
                    '<div class="col-md-6"><strong>Billing Instructions:</strong> <i>' + data.info.billingInstructions + '</i></div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Room Price:</strong> ' + data.info.roomRateFormatted + '</div>' +
                    '<div class="col-md-6"><strong>Rate Code:</strong> ' + data.info.rateCode + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Rooms:</strong> x ' + data.info.noOfRooms + '</div>' +
                    '<div class="col-md-6"><strong>Meal Type:</strong> ' + data.info.mealType + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Nights:</strong> x ' + data.info.noOfNights + '</div>' +
                    '<div class="col-md-6"><strong>Room Type Code:</strong> ' + data.info.roomTypeCode + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Total Amount:</strong> ' + data.info.totalAmountFormatted + '</div>' +
                    '<div class="col-md-6"><strong>Company Code:</strong> ' + data.info.companyCode + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6"><strong>Payment Status:</strong><span class="badge">' + data.info.lastPayment.status + '</span></div>' +
                    '<div class="col-md-6"><strong>Booking Status:</strong><span class="badge">' + data.info.status + '</span></div>' +
                    '</div>' +
                    '<h4>Customer Information</h4>' +
                    '<div class="row">' +
                    '<div class="col-md-12"><strong>Name:</strong> ' + data.info.customer.salutation + '. ' + data.info.customer.firstName + ' ' + data.info.customer.middleName + ' ' + data.info.customer.lastName + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-12"><strong>Address:</strong> ' + data.info.customer.address1 + ', ' + data.info.customer.city + ', ' + data.info.customer.zipcode + ', ' + data.info.customer.state + ', ' + data.info.customer.countryCode + '</div>' +
                    '</div>' +

                    '</div>', sh);

            }
        }];

        API.getBookings().then(function(response) {
            // sc.bookings = ;
            angular.forEach(response.data, function(item) {
                var event = {
                    title: item.title + ' - Status: ' + item.status,
                    info: item,
                    startsAt: new Date(item.arrival * 1000),
                    endsAt: new Date(item.departure * 1000),
                    actions: actions
                };

                sc.bookings.push(event);
            });
        }, function(error) {
            showPopup('Error', error.data, sh);
        });




        sc.timespanClicked = function(date, cell) {

            if (sc.calendarView === 'month') {
                if ((sc.cellIsOpen && moment(date).startOf('day').isSame(moment(sc.viewDate).startOf('day'))) || cell.events.length === 0 || !cell.inMonth) {
                    sc.cellIsOpen = false;
                } else {
                    sc.cellIsOpen = true;
                    sc.viewDate = date;
                }
            } else if (sc.calendarView === 'year') {
                if ((sc.cellIsOpen && moment(date).startOf('month').isSame(moment(sc.viewDate).startOf('month'))) || cell.events.length === 0) {
                    sc.cellIsOpen = false;
                } else {
                    sc.cellIsOpen = true;
                    sc.viewDate = date;
                }
            }

        }



    }
]);