'use strict';
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
        $scope.buttonText = 'Search';
        $scope.isInlineForm = true;
        $scope.booking = API.getBookingData();
        $scope.today = new Date();
        $scope.search = function(isValid) {
            if (isValid) {
                API.setBookingData({ checkIn: $scope.booking.checkIn, checkOut: $scope.booking.checkOut, adult: $scope.booking.adult, child: $scope.booking.child });
                $state.go('roomsAvailable');
            }
        }
    }
])

.controller('roomAvailableCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh', 'Lightbox',
    function($scope, $rootScope, $state, $stateParams, API, sh, Lightbox) {
        var sc = $scope;
        sc.minPrice = 0;
        sc.maxPrice = 0;
        sc.result = 0;

        sc.roomLists = [];
        sc.loaded = false;
        var filterDefault = {
            building: '',
            search: '',
            pricing: {
                value: sc.maxPrice,
                options: {
                    floor: sc.minPrice,
                    ceil: sc.maxPrice,
                    translate: function(value, sliderId, label) {
                        switch (label) {
                            case 'model':
                                return '‎₱' + Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                            case 'high':
                                return '‎₱' + Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                            default:
                                return '‎₱' + Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                        }
                    }
                }
            }

        };

        sc.filter = filterDefault;

        sc.getMinAndMaxPrice = function(data) {

            for (var i = 0; i < data.length; i++) {
                if (Number(data[i].minimumRate) > sc.maxPrice) {
                    sc.maxPrice = data[i].minimumRate;
                }
            }



            sc.minPrice = sc.maxPrice;
            for (var i = 0; i < data.length; i++) {
                if (Number(data[i].minimumRate) < sc.minPrice) {
                    sc.minPrice = data[i].minimumRate;
                }
            }
            sc.filter.pricing.options.floor = sc.minPrice;
            sc.filter.pricing.value = sc.filter.pricing.options.ceil = sc.maxPrice;

        }
        sc.togglePictures = function(index) {
            sc.roomLists[index].showPictures = sc.roomLists[index].showPictures == 1 ? 0 : 1;
        }
        sc.resetFilter = function() {
            sc.filter = {
                building: '',
                search: '',
                pricing: {
                    value: sc.maxPrice,
                    options: {
                        floor: sc.minPrice,
                        ceil: sc.maxPrice,
                        translate: function(value, sliderId, label) {
                            switch (label) {
                                case 'model':
                                    return '‎₱' + Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                                case 'high':
                                    return '‎₱' + Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                                default:
                                    return '‎₱' + Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                            }
                        }
                    }
                }

            };
        }
        sc.lessThanEqualTo = function(prop, val) {

            return function(item) {
                return item[prop] <= val;
            }
        }

        sc.openLightboxModal = function(photos, index, caption) {
            var images = [];
            for (var i = 0; i < photos.length; i++) {
                images.push({
                    url: photos[i].file.orig,
                    caption: caption
                });
            }
            Lightbox.openModal(images, index);
        }

        API.getAvailableRooms().then(function(response) {
            sc.roomLists = response.data;
            sc.getMinAndMaxPrice(sc.roomLists);
            sc.loaded = true;
        }, function(error) {
            showPopup('Error', error.data, sh);
            sc.loaded = true;
        });
    }
])

.controller('roomDetailsCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh',
    function($scope, $rootScope, $state, $stateParams, API, sh) {
        var sc = $scope;
        sc.today = new Date();
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


        sc.calendarView = 'month';
        sc.hasSelectedDate = false;
        sc.viewDate = moment().startOf('month').toDate();
        var currentDate = moment(sc.viewDate).format('MMMM DD, YYYY');
        sc.events = [];

        var popupModal = null;

        sc.showLoader = function() {
            popupModal = sh.openModal('globalPopup.html', 'Loading...', '<div class="progress"><div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">60% Complete</span></div></div>');
        }


        var fetchCalendar = function() {
            API.fetchUnavailableCalendarByRoomId(sc.room.id).then(function(response) {

                angular.forEach(response.data, function(data) {
                    var event = {
                        title: data.calendarTitle,
                        startsAt: new Date(data.startsAt * 1000),
                        info: data,
                        allDay: true
                    };
                    sc.events.push(event);
                });

                popupModal.dismiss('cancel');
            }, function(error) {
                showPopup('Error', error.data, sh);
            });
        }


        API.getRoomBySlug($stateParams.slug).then(function(response) {
            if (!response.data) {
                $state.go('home');
            }
            sc.room = response.data;
            fetchCalendar();
        }, function(error) {
            showPopup('Error', error.data, sh);
            $state.go('home');
        });




        sc.book = function(isValid) {
            if (isValid) {
                API.checkRoomAvailability({
                    roomID: sc.room.id,
                    checkIn: sc.booking.checkIn,
                    checkOut: sc.booking.checkOut,
                    noOfRooms: sc.booking.noRooms,
                }).then(function(response) {

                    API.bookingStep1({
                        roomId: sc.room.id,
                        rateId: sc.booking.rateSelected.id,
                        checkIn: sc.booking.checkIn,
                        checkOut: sc.booking.checkOut,
                        noOfAdults: sc.booking.adult,
                        noOfChild: sc.booking.child,
                        noOfRooms: sc.booking.noRooms,
                    }).then(function(response) {
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
                    }, function(error) {
                        showPopup('Error', error.data, sh);
                    });

                }, function(error) {
                    showPopup('Oops!', error.data, sh);
                });


            }
        }
        sc.showLoader();

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