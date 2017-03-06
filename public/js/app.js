'use strict';
var csrftoken = (function() {
    // not need Jquery for doing that
    var metas = window.document.getElementsByTagName('meta');

    // finding one has csrf token 
    for (var i = 0; i < metas.length; i++) {

        if (metas[i].name === "csrf-token") {

            return metas[i].content;
        }
    }

})();

function showPopup(title, message, sh, showButton) {
    var popupModal = sh.openModal('globalPopup.html', title, message, showButton);
    return popupModal.result.then(function(result) {
        return result;
    });
}


var copenhagenApp = angular.module('copenhagenApp', ['ngAnimate', 'ui.router', 'ui.bootstrap', 'mwl.calendar', 'angularFileUpload', 'ui.toggle', 'moment-picker', 'countrySelect', 'rzModule', 'bootstrapLightbox'])
    .constant('CSRF_TOKEN', csrftoken)
    .constant('BEDDING', ['single bed', 'single bed with pull-out', 'twin bed', 'double deck/bunk bed', 'queen bed', 'king bed'])
    .config(['$httpProvider', '$qProvider', 'CSRF_TOKEN',

        function($httpProvider, $qProvider, CSRF_TOKEN) {
            $httpProvider.defaults.stripTrailingSlashes = false;

            /**
             * adds CSRF token to header
             */
            $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = CSRF_TOKEN;
            $qProvider.errorOnUnhandledRejections(false);
            $httpProvider.interceptors.push('authHttpResponseInterceptor');
        }
    ])
    .run(['$rootScope', '$window', 'API', function($rootScope, $window, API) {
        $rootScope.currentUser = API.getCurrentUser();
        $rootScope.booking = API.getBookingData();

        $rootScope.$on('$stateChangeStart',
            function(event, toState, toParams, fromState, fromParams) {
                if (toState.external) {
                    event.preventDefault();
                    $window.open(toState.url, '_self');
                }
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
    });;