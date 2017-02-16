copenhagenApp.factory('Booking', ['$http', '$rootScope', '$state', '$window',
    function($http, $rootScope, $state, $window) {
        var urlBase = '/api/v1';

        return {
            setData: function(data) {
                $window.sessionStorage.setItem('Booking', JSON.stringify(data));
            },
            getData: function() {
                var _b = JSON.parse($window.sessionStorage.getItem("Booking"));
                return _b != null && _b.length > 0 ? _b[0] : _b;
            }
        };
    }
]);