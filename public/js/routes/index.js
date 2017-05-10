copenhagenApp.config(function($stateProvider, $urlRouterProvider, $locationProvider) {
    $stateProvider
        .state('home', {
            url: '/',
            views: {
                '': {
                    controller: 'homeCtrl',
                    templateUrl: '/views/page/home.html'
                },
                'header@home': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl',
                },
                'bookingForm@home': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@home': {
                    templateUrl: '/views/_partials/footer.html',
                    controller: 'homeCtrl',
                }
            }

        })
        .state('meetings', {
            url: '/meetings',
            title: 'Meetings - Copenhagen',
            description: 'The event space on the 7th floor (elevator accessible) features a fantastic view of Cebu and the environs. The facilities can host up to 80 people with a special area for commemorative picture taking against the backdrop of the surrounding mountains. Our kitchen can cater to all your culinary needs. Ideal for weddings, anniversaries and &hellip;',
            views: {
                '': {
                    controller: 'pageCtrl',
                    templateUrl: '/views/page/meetings.html'
                },
                'header@meetings': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl',
                },
                'bookingForm@meetings': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@meetings': {
                    templateUrl: '/views/_partials/footer.html',
                    controller: 'pageCtrl',
                }
            }

        })
        .state('gallery', {
            url: '/gallery',
            views: {
                '': {
                    controller: 'galleryCtrl',
                    templateUrl: '/views/page/gallery.html'
                },
                'header@gallery': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl',
                },
                'bookingForm@gallery': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@gallery': {
                    templateUrl: '/views/_partials/footer.html',
                }
            }

        })
        .state('contact', {
            url: '/contact-location',
            title: 'Contact &amp; Location - Copenhagen',
            description: '',
            views: {
                '': {
                    controller: 'pageCtrl',
                    templateUrl: '/views/page/contact.html'
                },
                'header@contact': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl',
                },
                'bookingForm@contact': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@contact': {
                    templateUrl: '/views/_partials/footer.html',
                    controller: 'pageCtrl',
                }
            }

        })
        .state('accomodation', {
            url: '/cebu-accommodation',
            views: {
                '': {
                    controller: 'pageCtrl',
                    templateUrl: '/views/page/accomodation.html'
                },
                'header@accomodation': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl',
                },
                'bookingForm@accomodation': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@accomodation': {
                    templateUrl: '/views/_partials/footer.html',
                    controller: 'pageCtrl',
                }
            }

        })
        .state('policy', {
            url: '/policy',
            views: {
                '': {
                    controller: 'pageCtrl',
                    templateUrl: '/views/page/policy.html'
                },
                'header@policy': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl',
                },
                'bookingForm@policy': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@policy': {
                    templateUrl: '/views/_partials/footer.html',
                    controller: 'pageCtrl',
                }
            }

        })
        .state('about', {
            url: '/about-ironwood',
            views: {
                '': {
                    controller: 'pageCtrl',
                    templateUrl: '/views/page/about.html'
                },
                'header@about': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl',
                },
                'bookingForm@about': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@about': {
                    templateUrl: '/views/_partials/footer.html',
                    controller: 'pageCtrl',
                }
            }

        })
        .state('roomsAvailable', {
            url: '/available-rooms-cebu-hotels',
            title: 'Cebu Hotels | Cebu Accomodation | Available Rooms',
            description: 'Cebu hotels available rooms for cebu accomodation.',
            controller: 'roomAvailableCtrl',
            views: {
                '': {
                    templateUrl: '/views/rooms/listing.html'
                },
                'header@roomsAvailable': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl'
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
                    controller: 'roomDetailsCtrl',
                },
                'header@roomDetail': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl'
                },
                'steps@roomDetail': {
                    templateUrl: '/views/booking/steps.html'
                },
                'bookingForm@roomDetail': {
                    templateUrl: '/views/booking/booking-form.html',
                    controller: 'bookingFormCtrl'
                },
                'footer@roomDetail': {
                    templateUrl: '/views/_partials/footer.html'
                }

            }
        })
        .state('customerDetail', {
            url: '/booking/submit-your-details',
            controller: 'customerDetailCtrl',
            views: {
                '': {
                    templateUrl: '/views/booking/customer-details.html'
                },
                'header@customerDetail': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl'
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
            controller: 'paymentDetailCtrl',
            views: {
                '': {
                    templateUrl: '/views/booking/payment.html',
                },
                'header@paymentDetail': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl'
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
            controller: 'paymentStatusCtrl',
            views: {
                '': {
                    templateUrl: '/views/booking/process-payment.html',
                },
                'header@paymentStatus': {
                    templateUrl: '/views/_partials/header.html',
                    controller: 'headerCtrl'
                },
                'steps@paymentStatus': {
                    templateUrl: '/views/booking/steps.html'
                },
                'bookingForm@paymentStatus': {
                    templateUrl: '/views/booking/booking-form.html'
                },
                'bookingDetails@paymentStatus': {
                    templateUrl: '/views/booking/booking-details.html'
                },
                'footer@paymentStatus': {
                    templateUrl: '/views/_partials/footer.html'
                }

            }
        })

    .state('paymentPesopay', {
            url: '/payment/pesopay',
            external: true
        })
        .state('bookingComplete', {
            url: '/booking/complete',
            controller: 'bookingCompleteCtrl',
            views: {
                '': {
                    templateUrl: '/views/booking/complete.html',
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
            controller: 'loginCtrl',
            views: {
                '': {
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
    .state('admin', {
            url: '/admin',
            controller: 'calendarCtrl',
            views: {
                '': {
                    templateUrl: '/views/admin/calendar.html',
                    resolve: { authenticate: authenticate }
                },
                'header@admin': {
                    templateUrl: '/views/_partials/admin-header.html'
                },
                'calendarControls@admin': {
                    templateUrl: '/views/admin/partials/calendarControls.html'
                },
                'footer@admin': {
                    templateUrl: '/views/_partials/admin-footer.html',
                }
            }
        })
        .state('adminCalendar', {
            url: '/admin/calendar',
            controller: 'calendarCtrl',
            views: {
                '': {
                    templateUrl: '/views/admin/calendar.html',
                    resolve: { authenticate: authenticate }
                },
                'header@adminCalendar': {
                    templateUrl: '/views/_partials/admin-header.html'
                },
                'calendarControls@adminCalendar': {
                    templateUrl: '/views/admin/partials/calendarControls.html'
                },
                'footer@adminCalendar': {
                    templateUrl: '/views/_partials/admin-footer.html',
                }
            }

        })
        .state('adminRateSetup', {
            url: '/admin/rate-setup',
            controller: 'rateCtrl',
            views: {
                '': {
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
            controller: 'roomCtrl',
            views: {
                '': {
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

        })
        .state('adminRoomSetupMetaContent', {
            url: '/admin/seo/:type/:id',
            controller: 'roomCtrl',
            views: {
                '': {
                    templateUrl: '/views/admin/meta-content.html',
                    resolve: { authenticate: authenticate }
                },
                'header@adminRoomSetupMetaContent': {
                    templateUrl: '/views/_partials/admin-header.html',
                },
                'footer@adminRoomSetupMetaContent': {
                    templateUrl: '/views/_partials/admin-footer.html',
                }
            }

        })
        .state('bookings', {
            url: '/admin/bookings',
            controller: 'bookingsCtrl',
            views: {
                '': {
                    templateUrl: '/views/admin/bookings.html',
                    resolve: { authenticate: authenticate }
                },
                'header@bookings': {
                    templateUrl: '/views/_partials/admin-header.html',
                },
                'footer@bookings': {
                    templateUrl: '/views/_partials/admin-footer.html',
                }
            }

        })
        .state('profile', {
            url: '/admin/profile',
            controller: 'profileCtrl',
            views: {
                '': {
                    templateUrl: '/views/admin/profile.html',
                    resolve: { authenticate: authenticate }
                },
                'header@profile': {
                    templateUrl: '/views/_partials/admin-header.html',
                },
                'footer@profile': {
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