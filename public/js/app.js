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
var copenhagenApp = angular.module('copenhagenApp', ['ui.router', 'ui.bootstrap', 'mwl.calendar', 'angularFileUpload', 'ui.toggle', 'moment-picker', 'countrySelect'])
    .constant('CSRF_TOKEN', csrftoken)
    .config(['$httpProvider', '$qProvider', 'CSRF_TOKEN',

        function($httpProvider, $qProvider, CSRF_TOKEN) {
            $httpProvider.defaults.stripTrailingSlashes = false;

            /**
             * adds CSRF token to header
             */
            $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = CSRF_TOKEN;
            $qProvider.errorOnUnhandledRejections(false);
        }
    ])
    .run(['$rootScope', 'API', function($rootScope, API) {
        $rootScope.currentUser = API.getCurrentUser();
        $rootScope.booking = API.getBookingData();
    }])
    .filter('range', function() {
        return function(input, min, max) {
            min = parseInt(min); //Make string input int
            max = parseInt(max);
            for (var i = min; i < max; i++)
                input.push(i);
            return input;
        };
    });