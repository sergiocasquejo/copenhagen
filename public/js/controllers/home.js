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
            showPopup('Error', error.data, sh);
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
                        title: data.title,
                        isActive: data.isActive,
                        data: data,
                        startsAt: new Date(data.startsAt * 1000),
                        allDay: true
                    };
                    $scope.events.push(event);
                });
            }, function(error) {
                showPopup('Error', error.data, sh);
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
                console.log($rootScope.booking);

                API.setBookingData({
                    checkIn: $rootScope.booking.checkIn,
                    checkOut: $rootScope.booking.checkOut,
                    noOfAdults: $rootScope.booking.adult,
                    noOfChild: $rootScope.booking.child,
                    noOfRooms: $rootScope.booking.noRooms,
                    roomRate: $scope.room.minimumRate,
                    noOfNights: nights,
                    room: {
                        id: $scope.room.id,
                        name: $scope.room.name,
                        building: $scope.room.building,
                        price: $scope.room.minimumRate
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
    ]);