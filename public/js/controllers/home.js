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

.controller('bookingFormCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh',
    function($scope, $rootScope, $state, API, sh) {
        var sc = $scope;
        sc.buttonText = 'Search';
        sc.isInlineForm = true;
        $rootScope.booking = API.getBookingData() || {
            adult: 1,
            child: 0,
            noRooms: 1
        };
        sc.today = new Date();
        sc.occMinus = function(type) {
            switch (type) {
                case 'rooms':
                    $rootScope.booking.noRooms = $rootScope.booking.noRooms > 0 ? $rootScope.booking.noRooms - 1 : 0;
                    break;
                case 'adults':
                    $rootScope.booking.adult = $rootScope.booking.adult > 0 ? $rootScope.booking.adult - 1 : 0;
                    break;
                case 'children':
                    $rootScope.booking.child = $rootScope.booking.child > 0 ? $rootScope.booking.child - 1 : 0;
                    break;
            }

        }
        sc.occPlus = function(type) {
            switch (type) {
                case 'rooms':
                    if ($rootScope.booking.noRooms < 10)
                        $rootScope.booking.noRooms += 1;
                    break;
                case 'adults':
                    if ($rootScope.booking.adult < 36)
                        $rootScope.booking.adult += 1;
                    break;
                case 'children':
                    if ($rootScope.booking.child < 36)
                        $rootScope.booking.child += 1;
                    break;
            }

        }

        sc.search = function(isValid) {
            var checkIn = moment($rootScope.booking.checkIn).format('YYYY-MM-DD');
            var checkOut = moment($rootScope.booking.checkOut).format('YYYY-MM-DD');
            if (window.CopenhagenAppConfig.disabledDates.indexOf(checkIn) > -1) {
                isValid = false;
                sh.openModal('globalPopup.html', 'Oops: ' + checkIn, 'Selected check in was disabled by admin');
            }
            if (window.CopenhagenAppConfig.disabledDates.indexOf(checkOut) > -1) {
                isValid = false;
                sh.openModal('globalPopup.html', 'Oops: ' + checkOut, 'Selected check out was disabled by admin');
            }

            if (isValid) {






                API.setBookingData({
                    checkIn: $rootScope.booking.checkIn,
                    checkOut: $rootScope.booking.checkOut,
                    noRooms: $rootScope.booking.noRooms,
                    adult: $rootScope.booking.adult,
                    child: $rootScope.booking.child
                });
                $state.go('roomsAvailable');
            }
        }
        sc.dateDisable = function(date, type) {
            return type != 'day' || window.CopenhagenAppConfig.disabledDates.indexOf(date.format('YYYY-MM-DD')) == -1;
        };

    }
])

.controller('roomAvailableCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh', 'Lightbox',
    function($scope, $rootScope, $state, $stateParams, API, sh, Lightbox) {

        var sc = $scope;
        sc.minPrice = 0;
        sc.maxPrice = 0;
        sc.result = 0;
        sc.roomTypeLists = CopenhagenAppConfig.bedding;
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
                var rates = data[i].rates;
                for (var z = 0; z < rates.length; z++) {
                    if (rates[z].pivot.isActive) {
                        if (Number(rates[z].pivot.price) > sc.maxPrice) {
                            sc.maxPrice = rates[z].pivot.price;
                        }
                    }
                }
            }



            sc.minPrice = sc.maxPrice;
            for (var i = 0; i < data.length; i++) {
                var rates = data[i].rates;
                for (var z = 0; z < rates.length; z++) {
                    if (rates[z].pivot.isActive) {
                        if (Number(rates[z].pivot.price) < sc.minPrice) {
                            sc.minPrice = rates[z].pivot.price;
                        }
                    }
                }
            }

            sc.filter.pricing.options.floor = sc.minPrice;
            sc.filter.pricing.value = sc.filter.pricing.options.ceil = sc.maxPrice;

        }
        sc.togglePictures = function(item) {
            item.showPictures = !item.showPictures;
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
        sc.lessThanEqualToRates = function(val) {

            return function(item) {
                for (var i = 0; i < item['rates'].length; i++) {
                    if (item['rates'][i].pivot.isActive) {
                        return item['rates'][i].pivot.price <= val;
                    }
                }
            }
        }

        sc.lessThanEqualTo = function(key, val) {

            return function(item) {
                return item[key] >= val;
            }
        }

        sc.hasThisBedTypes = function(types) {
            return function(item) {
                if (types == undefined) return true;

                for (var i = 0; i < item.beds.length; i++) {
                    return types[item.beds[i].type] == true;
                }
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

.controller('roomDetailsCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh', 'Lightbox', '$location',

    function($scope, $rootScope, $state, $stateParams, API, sh, Lightbox, $location) {
        var sc = $scope;
        sc.today = new Date();
        //Hide Top Booking Form
        //sc.bookingFormTopHide = true;
        //Current Step
        sc.buttonText = 'Book Now';
        sc.step = 1;
        sc.room = [];
        $rootScope.booking != null ? $rootScope.booking : {};


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
            sc.events = [];
            API.fetchUnavailableCalendarByRoomId(
                sc.room.id,
                moment(sc.viewDate).startOf('month').format('YYYY-MM-DD'),
                moment(sc.viewDate).endOf('month').format('YYYY-MM-DD')
            ).then(function(response) {

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

            $rootScope.title = sc.room.seo.metaTitle;
            $rootScope.description = sc.room.seo.metaDescription;
            $rootScope.keywords = sc.room.seo.metaKeywords;
            $rootScope.canonical = sc.room.seo.canonicalLinks || $location.absUrl();


            fetchCalendar();
        }, function(error) {
            showPopup('Error', error.data, sh);
            $state.go('home');
        });

        sc.changeCalendarViewMonth = function(viewDate) {
            sc.showLoader();
            fetchCalendar();

        };




        sc.book = function(isValid) {
            sc.buttonText = 'Processing...';
            if (isValid) {
                API.checkRoomAvailability({
                    roomID: sc.room.id,
                    checkIn: sc.booking.checkIn,
                    checkOut: sc.booking.checkOut,
                    noOfRooms: sc.booking.noRooms,
                    noOfAdults: sc.booking.adult,
                }).then(function(response) {
                    API.bookingStep({
                        roomId: sc.room.id,
                        rateId: sc.booking.rateSelected.id,
                        checkIn: sc.booking.checkIn,
                        checkOut: sc.booking.checkOut,
                        noOfAdults: sc.booking.adult,
                        noOfChild: sc.booking.child,
                        noOfRooms: sc.booking.noRooms,
                    }, 1).then(function(response) {

                        API.setBookingData({
                            checkIn: sc.booking.checkIn,
                            checkOut: sc.booking.checkOut,
                            adult: sc.booking.adult,
                            child: sc.booking.child,
                            noRooms: sc.booking.noRooms,
                            roomRate: response.data.roomRate,
                            totalAmount: response.data.totalAmount,
                            noOfNights: response.data.noOfNights,
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
                    }).finally(function() {
                        sc.buttonText = 'Book Now';
                    });

                }, function(error) {
                    showPopup('Oops!', error.data, sh);
                });


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
        sc.showLoader();

    }
])

.controller('customerDetailCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh',
    function($scope, $rootScope, $state, $stateParams, API, sh) {
        var data = $rootScope.booking;
        if (data == undefined || data.room == undefined) {
            $state.go('roomsAvailable');
        }
        //Hide Top Booking Form
        $scope.bookingFormTopHide = true;
        //Current Step
        $scope.step = 2;
        $scope.submitCustomerDetail = function(isValid) {
            if (isValid) {
                var data = $rootScope.booking;
                API.bookingStep({
                    salutation: data.salutation,
                    firstname: data.firstname,
                    lastname: data.lastname,
                    middleName: data.middleName,
                    email: data.email,
                    contact: data.contact,
                    specialInstructions: data.specialInstructions,
                    address1: data.address1,
                    address2: data.address2,
                    state: data.state,
                    city: data.city,
                    zipcode: data.zipcode,
                    country: data.country,
                    billingInstructions: data.billingInstructions
                }, 2).then(function(response) {
                    API.setBookingData(data);
                    $state.go('paymentDetail');
                }, function(error) {
                    showPopup('Error', error.data, sh);
                });
            }
        }
    }
])

.controller('paymentDetailCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', 'sh',
        function($scope, $rootScope, $state, $stateParams, API, sh) {
            //Hide Top Booking Form
            $scope.bookingFormTopHide = true;
            $scope.paymentMethod = CopenhagenAppConfig.paymentMethod;
            //Current Step
            $scope.step = 3;
            $scope.pay = function(isValid) {
                if (isValid) {
                    var data = $rootScope.booking;
                    API.bookingStep({
                        agree: data.accept,
                        paymentMethod: data.paymentType
                    }, 3).then(function(response) {
                        if (response.data == 'success' && $scope.paymentMethod.pesopay == true) {
                            $state.go('paymentPesopay');
                        } else {
                            $state.go('bookingComplete');
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
            $scope.step = 4;
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

.controller('pageCtrl', ['$scope', '$rootScope', '$state', '$stateParams', 'API', '$timeout', 'sh',
        function($scope, $rootScope, $state, $stateParams, API, $timeout, sh) {
            var sc = $scope;
            sc.contactButtonText = 'SEND';

            sc.myInterval = 5000;
            sc.noWrapSlides = false;
            sc.active = 0;
            var slides = sc.slides = [{
                id: 0,
                image: '/images/meetings/1.jpg'
            }, {
                id: 1,
                image: '/images/meetings/2.jpg'
            }, {
                id: 2,
                image: '/images/meetings/3.jpg'
            }, {
                id: 3,
                image: '/images/meetings/4.jpg'
            }, {
                id: 4,
                image: '/images/meetings/5.jpg'
            }];
            var currIndex = 0;


            sc.send = function(isValid) {
                if (isValid) {
                    sc.contactButtonText = 'SENDING...';
                    API.sendContact({
                        firstname: sc.contact.firstname,
                        lastname: sc.contact.lastname,
                        email: sc.contact.email,
                        phone: sc.contact.phone,
                        message: sc.contact.message
                    }).then(function(response) {
                        sc.contact = null;
                        showPopup('Sent', response.data, sh);
                    }, function(error) {
                        showPopup('Error', error.data, sh);

                    }).finally(function() {
                        sc.contactButtonText = 'SEND';
                    });
                }
            }
        }
    ])
    .controller('galleryCtrl', ['$scope', 'Lightbox',
        function($scope, Lightbox) {
            var sc = $scope;
            sc.gallery = [{
                caption: 'Gallery',
                url: '/images/gallery/1.jpg',
                buiding: 'main',
            }, {
                caption: 'Gallery',
                url: '/images/gallery/2.jpg',
                buiding: 'main',
            }, {
                caption: 'Gallery',
                url: '/images/gallery/3.jpg',
                buiding: 'main',
            }, {
                caption: 'Gallery',
                url: '/images/gallery/4.jpg',
                buiding: 'east',
            }, {
                caption: 'Gallery',
                url: '/images/gallery/5.jpg',
                buiding: 'east',
            }, {
                caption: 'Gallery',
                url: '/images/gallery/6.jpg',
                buiding: 'east',
            }, {
                caption: 'Gallery',
                url: '/images/gallery/7.jpg',
                buiding: 'east',
            }, {
                caption: 'Gallery',
                url: '/images/gallery/8.jpg',
                buiding: 'east',
            }];


            sc.openLightboxModal = function(index) {
                Lightbox.openModal(sc.gallery, index);
            }

        }
    ])
    .controller('headerCtrl', ['$scope', '$location',
        function($scope, $location) {
            $scope.isActive = function(viewLocation) {
                return viewLocation === $location.path();
            };
        }
    ])
    .controller('bookingCompleteCtrl', ['$scope', '$location', function($scope, $location) {
        var sc = $scope;
        sc.step = 4;
    }]);