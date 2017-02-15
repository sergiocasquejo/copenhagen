copenhagenApp.factory('Booking', ['$http', '$rootScope', '$state', function($http, $rootScope, $state) {
    var urlBase = '/api/v1';
    var bookingObj = {};

    return {
        setData: function(data) {
            bookingObj = data;
        },
        getData: function() {
            return bookingObj;
        }
    };
}]);