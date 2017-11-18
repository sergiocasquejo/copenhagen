'use strict';

function showPopup(title, message, sh, showButton) {
    if (message.status == 401) return;
    var popupModal = sh.openModal('globalPopup.html', title, message, showButton);
    return popupModal.result.then(function(result) {
        return result;
    });
}


var copenhagenApp = angular.module('copenhagenApp', ['ngAnimate', 'ui.router', 'ui.bootstrap', 'mwl.calendar', 'angularFileUpload', 'ui.toggle', 'moment-picker', 'countrySelect', 'rzModule', 'bootstrapLightbox', 'ngCookies', 'textAngular', 'ngSanitize'])
    .config(['$httpProvider', '$qProvider',

        function($httpProvider, $qProvider) {
            $httpProvider.defaults.stripTrailingSlashes = false;

            /**
             * adds CSRF token to header
             */
            $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = CopenhagenAppConfig.csrfToken;
            $qProvider.errorOnUnhandledRejections(false);
            $httpProvider.interceptors.push('authHttpResponseInterceptor');
        }
    ])
    .run(['$rootScope', '$window', 'API', '$location', function($rootScope, $window, API, $location) {
        $rootScope.currentUser = API.getCurrentUser();
        $rootScope.booking = API.getBookingData();

        $rootScope.$on('$stateChangeStart',
            function(event, toState, toParams, fromState, fromParams) {
                if (toState.external) {
                    event.preventDefault();
                    $window.open(toState.url, '_self');
                }
            });

        $rootScope.$on('$stateChangeSuccess',
            function(event, toState, toParams, fromState, fromParams) {
                $rootScope.title = toState.title || 'Long Term Stay Apartments | Cebu Serviced Apartments';
                $rootScope.description = toState.description || 'Copenhagen, a long term stay apartments with hotel accommodation. It&#039;s Cebu serviced apartments &amp; corporate housing for long term stay serviced apartments.';
                $rootScope.keywords = toState.keywords || 'copenhagen,bookings,rooms';
                $rootScope.canonical = $location.absUrl();
            });
    }])
    .filter('range', function() {
        return function(input, min, max) {
            min = parseInt(min); //Make string input int
            max = parseInt(max);
            for (var i = min; i < max; i++)
                input.push(i);
            return input;
        };
    })

.factory('sh', function($uibModal) {
        var modalInstance = null;

        function openModal(_templateUrl, _title, _message, _showButton, _resolve) {
            if (_resolve == undefined) {
                var _resolve = {
                    modalTitle: function() {
                        return _title;
                    },
                    bodyMessage: function() {
                        return _message;
                    }
                };
            }
            return $uibModal.open({
                animation: true,
                templateUrl: _templateUrl,
                backdrop: 'static',
                keyboard: false,
                controller: function($scope, $uibModalInstance, modalTitle, bodyMessage) {
                    $scope.modalTitle = modalTitle;
                    $scope.modalMessage = bodyMessage;
                    $scope.showButton = _showButton;
                    $scope.ok = function() {
                        $uibModalInstance.close('ok');

                    };

                    $scope.cancel = function() {
                        $uibModalInstance.dismiss('cancel');
                    };
                },
                resolve: _resolve
            })
        }

        return {
            openModal: openModal
        }
    })
    .filter('unsafe', ['$sce', function($sce) {
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }])
    .filter('trim', function() {
        return function(value) {
            if (!angular.isString(value)) {
                return value;
            }
            return value.replace(/^\s+|\s+$/g, ''); // you could use .trim, but it's not going to work in IE<9
        };
    }).directive('pagenotfound', function() {
        //define the directive object
        var directive = {};

        //restrict = E, signifies that directive is Element directive
        directive.restrict = 'E';

        //template replaces the complete element with its text. 
        directive.template = '<div class="text-center box-404"><h1>404</h1><p>Sorry the page you are looking for is not found.</p><a ui-sref="home">Home Page</a></div>';

        //scope is used to distinguish each student element based on criteria.
        directive.scope = {}

        //compile is called during application initialization. AngularJS calls it once when html page is loaded.

        directive.compile = function(element, attributes) {

            //linkFunction is linked with each element with scope to get the element specific data.
            var linkFunction = function($scope, element, attributes) {}
            return linkFunction;
        }
        return directive;
    });