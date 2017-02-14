copenhagenApp.factory('API', ['$http', '$rootScope', '$state', function($http, $rootScope, $state) {
    var urlBase = '/api/v1';
    var api = {};

    api.login = function(auth) {
        return $http.post(urlBase + '/login', auth);
    };

    api.saveRoom = function(room) {
        if (room.id) {
            return $http.put(urlBase + '/room/' + room.id, room);
        }
        return $http.post(urlBase + '/room', room);
    }
    api.getRooms = function() {
        return $http.get(urlBase + '/rooms');
    }

    api.deletePhoto = function(roomID, photoID) {
        return $http.delete(urlBase + '/rooms/' + roomID + '/photo/' + photoID);
    }




    return api;
}]);