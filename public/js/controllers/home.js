copenhagenApp.controller('homeCtrl', ['$scope', '$rootScope', '$state', 'API', function($scope, $rootScope, $state, API) {

    $scope.myInterval = 5000;
    $scope.noWrapSlides = false;
    $scope.active = 0;
    var slides = $scope.slides = [{
        id: 0,
        image: '/images/slider/1.jpg'
    }, {
        id: 1,
        image: '/images/slider/2.jpg'
    }, {
        id: 2,
        image: '/images/slider/3.jpg'
    }, {
        id: 3,
        image: '/images/slider/4.jpg'
    }, {
        id: 4,
        image: '/images/slider/5.jpg'
    }];
    var currIndex = 0;

}])

.controller('bookingFormCtrl', ['$scope', '$rootScope', '$state', 'API',
    function($scope, $rootScope, $state, API) {
        $scope.buttonText = 'Check Availability';
        $scope.isInlineForm = true;
        $scope.booking = API.getBookingData();
        $scope.search = function(isValid) {
            if (isValid) {
                API.setBookingData({ checkIn: $scope.booking.checkIn, checkOut: $scope.booking.checkOut, adult: $scope.booking.adult, child: $scope.booking.child });
                $state.go('roomsAvailable');
            }
        }
    }
])

.controller('roomAvailableCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API',
    function($scope, $rootScope, $state, $stateParams, API) {
        $scope.roomLists = [];
        API.getRooms().then(function(response) {
            $scope.roomLists = response.data;
        }, function(error) {
            console.log(error);
        });
    }
])

.controller('roomDetailsCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API',
    function($scope, $rootScope, $state, $stateParams, API) {
        //Hide Top Booking Form
        $scope.bookingFormTopHide = true;
        //Current Step
        $scope.step = 1;
        $scope.room = [];

        $scope.myInterval = 5000;
        $scope.noWrapSlides = false;
        $scope.active = 0;
        var currIndex = 0;


        API.getRoomBySlug($stateParams.slug).then(function(response) {
            if (!response.data) {
                $state.go('home');
            }
            $scope.room = response.data;
            fetchCalendarByRoomID($scope.room.id);
        }, function(error) {
            $state.go('home');
        });

        $scope.events = [];
        $scope.calendarView = 'month';
        $scope.viewDate = moment().startOf('month').toDate();
        var currentDate = moment($scope.viewDate).format('MMMM DD, YYYY');

        function fetchCalendarByRoomID(roomID) {
            API.getRoomCalendar(roomID).then(function(response) {
                angular.forEach(response.data, function(data) {
                    var event = {
                        title: data.title, // The title of the event
                        isActive: data.isActive,
                        data: data,
                        startsAt: new Date(data.startsAt * 1000), // A javascript date object for when the event starts
                        //endsAt: new Date(data.startsAt * 1000), // Optional - a javascript date object for when the event ends
                        // color: { // can also be calendarConfig.colorTypes.warning for shortcuts to the deprecated event types
                        //     primary: '#e3bc08', // the primary event color (should be darker than secondary)
                        //     secondary: '#fdf1ba' // the secondary event color (should be lighter than primary)
                        // },
                        // actions: [{ // an array of actions that will be displayed next to the event title
                        //     label: '<i class=\'glyphicon glyphicon-pencil\'></i>', // the label of the action
                        //     cssClass: 'edit-action', // a CSS class that will be added to the action element so you can implement custom styling
                        //     onClick: function(args) { // the action that occurs when it is clicked. The first argument will be an object containing the parent event
                        //         console.log('Edit event', args.calendarEvent);
                        //     }
                        // }],
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

        $scope.cellModifier = function(cell) {
            if (cell.events[0] !== undefined) {
                if (cell.events[0].isActive == 0) {
                    cell.cssClass = 'bg-danger';
                }
            }
        };

        $scope.book = function(isValid) {
            if (isValid) {

                var date1 = new Date($rootScope.booking.checkIn);
                var date2 = new Date($rootScope.booking.checkOut);
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                var nights = Math.ceil(timeDiff / (1000 * 3600 * 24));

                API.setBookingData({
                    checkIn: $rootScope.booking.checkIn,
                    checkOut: $rootScope.booking.checkOut,
                    adult: $rootScope.booking.adult,
                    child: $rootScope.booking.child,
                    noRooms: $rootScope.booking.noRooms,
                    nights: nights,
                    room: {
                        id: $scope.room.id,
                        name: $scope.room.name,
                        building: $scope.room.building,
                        price: $scope.room.minimumRate
                    }
                });

                console.log(nights, $scope.room);
                $state.go('customerDetail');
            }
        }


    }
])

.controller('customerDetail', ['$scope', '$rootScope', '$state', '$stateParams', 'API',
    function($scope, $rootScope, $state, $stateParams, API) {
        //Hide Top Booking Form
        $scope.bookingFormTopHide = true;
        //Current Step
        $scope.step = 2;
        $scope.submitCustomerDetail = function(isValid) {
            if (isValid) {
                API.setBookingData($rootScope.booking);
                $state.go('paymentDetail');
            }
        }
    }
])

.controller('paymentDetailCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API',
        function($scope, $rootScope, $state, $stateParams, API) {
            //Hide Top Booking Form
            $scope.bookingFormTopHide = true;
            //Current Step
            $scope.step = 3;
            $scope.pay = function(isValid) {
                if (isValid) {
                    API.setBookingData($rootScope.booking);
                    $state.go('bookingComplete');
                }
            }
        }
    ])
    .controller('bookingCompleteCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API',
        function($scope, $rootScope, $state, $stateParams, API) {
            //Hide Top Booking Form
            $scope.bookingFormTopHide = true;
            //Current Step
            $scope.step = 4;
        }
    ]);