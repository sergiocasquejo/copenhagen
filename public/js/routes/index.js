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
                    templateUrl: '/views/booking/booking-form.html',
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
                    templateUrl: '/views/rooms/index.html'
                },
                'header@roomsAvailable': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'roomAvailableCtrl'
                },
                'bookingForm@roomsAvailable': {
                    templateUrl: '/views/booking/booking-form.html',
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
                    templateUrl: '/views/rooms/details.html',
                    controller: 'roomDetailsCtrl'
                },
                'header@roomDetail': {
                    templateUrl: '/views/_partials/header.html'
                },
                'steps@roomDetail': {
                    templateUrl: '/views/booking/steps.html'
                },
                'bookingForm@roomDetail': {
                    templateUrl: '/views/booking/booking-form.html',
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
                    templateUrl: '/views/booking/customer-details.html'
                },
                'header@customerDetail': {
                    templateUrl: '/views/_partials/header.html'
                },
                'steps@customerDetail': {
                    templateUrl: '/views/booking/steps.html'
                },
                'bookingForm@customerDetail': {
                    templateUrl: '/views/booking/booking-form.html'
                },
                'bookingDetails@customerDetail': {
                    templateUrl: '/views/booking/booking-details.html'
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
                    templateUrl: '/views/booking/payment.html',
                    controller: 'paymentDetailCtrl'
                },
                'header@paymentDetail': {
                    templateUrl: '/views/_partials/header.html'
                },
                'steps@paymentDetail': {
                    templateUrl: '/views/booking/steps.html'
                },
                'bookingForm@paymentDetail': {
                    templateUrl: '/views/booking/booking-form.html'
                },
                'bookingDetails@paymentDetail': {
                    templateUrl: '/views/booking/booking-details.html'
                },
                'footer@paymentDetail': {
                    templateUrl: '/views/_partials/footer.html'
                }

            }
        })
        .state('paymentStatus', {
            url: '/payment/:status',
            views: {
                '': {
                    templateUrl: '/views/booking/process-payment.html',
                    controller: 'paymentStatusCtrl'
                }

            }
        })

    .state('paymentPesopay', {
            url: '/payment/pesopay',
            external: true
        })
        .state('bookingComplete', {
            url: '/booking/complete',
            views: {
                '': {
                    templateUrl: '/views/booking/complete.html',
                    controller: 'bookingCompleteCtrl'
                },
                'header@bookingComplete': {
                    templateUrl: '/views/_partials/header.html'
                },
                'steps@bookingComplete': {
                    templateUrl: '/views/booking/steps.html'
                },
                'bookingForm@bookingComplete': {
                    templateUrl: '/views/booking/booking-form.html'
                },
                'bookingDetails@bookingComplete': {
                    templateUrl: '/views/booking/booking-details.html'
                },
                'footer@bookingComplete': {
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
                    templateUrl: '/views/auth/login.html'
                }
            },
            // resolve: { unauthenticate: unauthenticate }

        })
        .state('logout', {
            url: '/logout',
            controller: 'logoutCtrl',
        })

    // Admin Routes
    .state('adminCalendar', {
            url: '/admin/calendar',
            views: {
                '': {
                    controller: 'calendarCtrl',
                    templateUrl: '/views/admin/calendar.html',
                    resolve: { authenticate: authenticate }
                },
                'header@adminCalendar': {
                    templateUrl: '/views/_partials/admin-header.html'
                },
                'calendarControls@adminCalendar': {
                    templateUrl: '/views/admin/partials/calendarControls.html'
                }
            }

        })
        .state('adminRateSetup', {
            url: '/admin/rate-setup',
            views: {
                '': {
                    controller: 'rateCtrl',
                    templateUrl: '/views/admin/rate-setup.html',
                    resolve: { authenticate: authenticate }
                },
                'header@adminRateSetup': {
                    templateUrl: '/views/_partials/admin-header.html',
                },
                'footer@adminRateSetup': {
                    templateUrl: '/views/_partials/admin-footer.html',
                }
            }
        })
        .state('adminRoomSetup', {
            url: '/admin/room-setup',
            views: {
                '': {
                    controller: 'roomCtrl',
                    templateUrl: '/views/admin/room-setup.html',
                    resolve: { authenticate: authenticate }
                },
                'header@adminRoomSetup': {
                    templateUrl: '/views/_partials/admin-header.html',
                },
                'footer@adminRoomSetup': {
                    templateUrl: '/views/_partials/admin-footer.html',
                }
            }

        });

    $urlRouterProvider.otherwise('/');
    $locationProvider.html5Mode(true);


    function authenticate($q, API, $state, $timeout) {
        if (API.isAuthenticated()) {
            //Resolve the promise successfully
            return $q.when();
        } else {
            $timeout(function() {
                    // This code runs after the authentication promise has been rejected.
                    // Go to the log-in page
                    $state.go('login');
                })
                // Reject the authentication promise to prevent the state from loading
            return $q.reject();
        }
    }

    function unauthenticate($q, API, $state, $timeout) {
        if (!API.isAuthenticated()) {
            //Resolve the promise successfully
            return $q.when();
        } else {
            $timeout(function() {
                    // This code runs after the authentication promise has been rejected.
                    // Go to the log-in page
                    $state.go('adminRoomSetup');
                })
                // Reject the authentication promise to prevent the state from loading
            return $q.reject();
        }
    }



});