copenhagenApp.config(function($stateProvider, $urlRouterProvider, $locationProvider) {
    $stateProvider
        .state('home', {
            url: '/',
            views: {
                '': {
                    controller: 'homeCtrl',
                    templateUrl: '/views/_partials/home.html'
                },
                'header@home': {
                    templateUrl: '/views/_partials/header.html'
                },
                'bookingForm@home': {
                    templateUrl: '/views/_partials/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@home': {
                    templateUrl: '/views/_partials/footer.html'
                }
            }

        })
        .state('roomsAvailable', {
            url: '/available-rooms-cebu-hotels',
            views: {
                '': {
                    templateUrl: '/views/_partials/rooms/index.html'
                },
                'header@roomsAvailable': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'roomAvailableCtrl'
                },
                'bookingForm@roomsAvailable': {
                    templateUrl: '/views/_partials/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@roomsAvailable': {
                    templateUrl: '/views/_partials/footer.html'
                }

            }
        })
        .state('roomDetail', {
            url: '/rooms-suites/:slug',
            views: {
                '': {
                    templateUrl: '/views/_partials/rooms/details.html',
                    controller: 'roomDetailsCtrl'
                },
                'header@roomDetail': {
                    templateUrl: '/views/_partials/header.html'
                },
                'steps@roomDetail': {
                    templateUrl: '/views/_partials/rooms/steps.html'
                },
                'bookingForm@roomDetail': {
                    templateUrl: '/views/_partials/booking-form.html',
                },
                'footer@roomDetail': {
                    templateUrl: '/views/_partials/footer.html'
                }

            }
        })
        .state('customerDetail', {
            url: '/booking/submit-your-details',
            views: {
                '': {
                    templateUrl: '/views/_partials/booking/customer-details.html'
                },
                'header@customerDetail': {
                    templateUrl: '/views/_partials/header.html'
                },
                'steps@customerDetail': {
                    templateUrl: '/views/_partials/rooms/steps.html'
                },
                'bookingForm@customerDetail': {
                    templateUrl: '/views/_partials/booking-form.html'
                },
                'bookingDetails@customerDetail': {
                    templateUrl: '/views/_partials/booking/booking-details.html'
                },
                'footer@customerDetail': {
                    templateUrl: '/views/_partials/footer.html'
                }

            }
        })
        .state('paymentDetail', {
            url: '/booking/payment',
            views: {
                '': {
                    templateUrl: '/views/_partials/booking/payment.html'
                },
                'header@paymentDetail': {
                    templateUrl: '/views/_partials/header.html'
                },
                'steps@paymentDetail': {
                    templateUrl: '/views/_partials/rooms/steps.html'
                },
                'bookingForm@paymentDetail': {
                    templateUrl: '/views/_partials/booking-form.html'
                },
                'bookingDetails@paymentDetail': {
                    templateUrl: '/views/_partials/booking/booking-details.html'
                },
                'footer@paymentDetail': {
                    templateUrl: '/views/_partials/footer.html'
                }

            }
        })




    /*================================================================================================
     * ADMIN Routes
     *================================================================================================*/
    // User Authentication Route
    .state('login', {
        url: '/login',
        views: {
            '': {
                controller: 'loginCtrl',
                templateUrl: '/views/_partials/login.html'
            }
            // ,
            // 'header@home': {
            //     templateUrl: '/views/_partials/header.html'
            // }
        }

    })

    // Admin Routes
    .state('adminCalendar', {
            url: '/admin/calendar',
            views: {
                '': {
                    controller: 'calendarCtrl',
                    templateUrl: '/views/_partials/admin/calendar.html'
                },
                'header@adminCalendar': {
                    templateUrl: '/views/_partials/admin/header.html'
                },
                'calendarControls@adminCalendar': {
                    templateUrl: '/views/_partials/admin/partials/calendarControls.html'
                }
            }

        })
        .state('adminRoomSetup', {
            url: '/admin/rooms',
            views: {
                '': {
                    controller: 'roomCtrl',
                    templateUrl: '/views/_partials/admin/room/index.html'
                },
                'header@adminRoomSetup': {
                    templateUrl: '/views/_partials/admin/header.html'
                }
            }

        });

    $urlRouterProvider.otherwise('/');
    $locationProvider.html5Mode(true);


});