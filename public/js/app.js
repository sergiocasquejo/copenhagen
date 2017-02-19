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

function errorHandler(error, sh) {
    console.log('err');
    var popupModal = sh.openModal('globalPopup.html', 'Error', error.data);
    popupModal.result.then(function(result) {
        console.log(result);
    });
}
var copenhagenApp = angular.module('copenhagenApp', ['ngAnimate', 'ui.router', 'ui.bootstrap', 'mwl.calendar', 'angularFileUpload', 'ui.toggle', 'moment-picker', 'countrySelect'])
    .constant('CSRF_TOKEN', csrftoken)
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

        function openModal(page, title, message) {

            return $uibModal.open({
                animation: true,
                templateUrl: page,
                backdrop: 'static',
                keyboard: false,
                controller: function($scope, $uibModalInstance, modalTitle, bodyMessage) {
                    $scope.modalTitle = modalTitle;
                    $scope.modalMessage = bodyMessage;
                    $scope.ok = function() {
                        $uibModalInstance.close('ok');

                    };

                    $scope.cancel = function() {
                        $uibModalInstance.dismiss('cancel');
                    };
                },
                resolve: {
                    modalTitle: function() {
                        return title;
                    },
                    bodyMessage: function() {
                        return message;
                    }
                }
            })
        }

        return {
            openModal: openModal
        }
    })
    .filter('unsafe', function($sce) { return $sce.trustAsHtml; });