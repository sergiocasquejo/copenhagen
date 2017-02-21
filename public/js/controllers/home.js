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

.controller('roomAvailableCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh',
    function($scope, $rootScope, $state, $stateParams, API, sh) {
        var sc = $scope;
        sc.roomLists = [];
        sc.loaded = false;
        sc.buildingSelected = 'all';
        API.getAvailableRooms().then(function(response) {
            sc.roomLists = response.data;
            sc.loaded = true;
        }, function(error) {
            showPopup('Error', error.data, sh);
            sc.loaded = true;
        });

        sc.selectedBuilding = function(selected) {
            sc.buildingSelected = selected;
        }
    }
])

.controller('roomDetailsCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh',
    function($scope, $rootScope, $state, $stateParams, API, sh) {
        var sc = $scope;
        //Hide Top Booking Form
        sc.bookingFormTopHide = true;
        //Current Step
        sc.step = 1;
        sc.room = [];
        sc.booking = $rootScope.booking != null ? $rootScope.booking : {};


        sc.myInterval = 5000;
        sc.noWrapSlides = false;
        sc.active = 0;
        var currIndex = 0;


        API.getRoomBySlug($stateParams.slug).then(function(response) {
            if (!response.data) {
                $state.go('home');
            }
            sc.room = response.data;
            fetchCalendarByRoomID(sc.room.id);
        }, function(error) {
            showPopup('Error', error.data, sh);
            $state.go('home');
        });

        sc.events = [];
        sc.calendarView = 'month';
        sc.viewDate = moment().startOf('month').toDate();
        var currentDate = moment(sc.viewDate).format('MMMM DD, YYYY');

        function fetchCalendarByRoomID(roomID) {
            API.getRoomCalendar(roomID).then(function(response) {
                angular.forEach(response.data, function(data) {
                    var event = {
                        title: data.title,
                        isActive: data.isActive,
                        data: data,
                        startsAt: new Date(data.startsAt * 1000),
                        allDay: true
                    };
                    sc.events.push(event);
                });
            }, function(error) {
                showPopup('Error', error.data, sh);
            });
        }

        sc.cellModifier = function(cell) {
            if (cell.events[0] !== undefined) {
                if (cell.events[0].isActive == 0) {
                    cell.cssClass = 'bg-danger';
                }
            }
        };

        sc.book = function(isValid) {
            if (isValid) {
                console.log(sc.booking);

                var date1 = new Date(sc.booking.checkIn);
                var date2 = new Date(sc.booking.checkOut);
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                var nights = Math.ceil(timeDiff / (1000 * 3600 * 24));


                API.setBookingData({
                    checkIn: sc.booking.checkIn,
                    checkOut: sc.booking.checkOut,
                    noOfAdults: sc.booking.adult,
                    noOfChild: sc.booking.child,
                    noOfRooms: sc.booking.noRooms,
                    roomRate: sc.room.minimumRate,
                    noOfNights: nights,
                    room: {
                        id: sc.room.id,
                        name: sc.room.name,
                        building: sc.room.building,
                        price: sc.room.minimumRate
                    }
                });

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

.controller('paymentDetailCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh',
        function($scope, $rootScope, $state, $stateParams, API, sh) {
            //Hide Top Booking Form
            $scope.bookingFormTopHide = true;
            //Current Step
            $scope.step = 3;
            $scope.pay = function(isValid) {
                if (isValid) {
                    API.setBookingData($rootScope.booking);
                    var data = $rootScope.booking;
                    data.roomID = data.room.id;
                    API.book(data).then(function(response) {
                        if (response.data == 'success') {
                            $state.go('paymentPesopay');
                        }
                    }, function(error) {
                        showPopup('Error', error.data, sh);
                    });

                }
            }
        }
    ])
    .controller('paymentStatusCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', '$timeout',
        function($scope, $rootScope, $state, $stateParams, API, $timeout) {
            $scope.paymentStatus = $stateParams.status;
            $scope.load = function() {
                document.getElementById("payForm").submit();
            };

            $scope.payment = {
                merchantId: '',
                amount: '',
                orderRef: '',
                secureHash: '',
            };
            // $timeout(function() { $scope.load(); }, 1000, true);
        }
    ])

.controller('pageCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', '$timeout',
    function($scope, $rootScope, $state, $stateParams, API, $timeout) {
        var sc = $scope;

        sc.send = function(isValid) {
            if (isValid) {
                //
            }
        }
    }
]);