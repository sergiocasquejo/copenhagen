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
var copenhagenApp = angular.module('copenhagenApp', ['ui.router', 'ui.bootstrap', 'ui.calendar', 'angularFileUpload', 'ui.toggle'])
    .constant('CSRF_TOKEN', csrftoken)
    .config(['$httpProvider', '$qProvider', 'CSRF_TOKEN',

        function($httpProvider, $qProvider, CSRF_TOKEN) {


            /**
             * adds CSRF token to header
             */
            $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = CSRF_TOKEN;
            $qProvider.errorOnUnhandledRejections(false);
        }
    ]);;