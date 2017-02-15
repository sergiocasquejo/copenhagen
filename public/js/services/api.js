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

    api.getRoomBySlug = function(slug) {
        return $http.get(urlBase + '/room/' + slug);
    }
    api.getRoomById = function(id) {
        return $http.get(urlBase + '/room/' + id);
    }

    api.deleteRoom = function(roomID, data) {
        return $http.delete(urlBase + '/room/' + roomID, data);
    }

    api.deletePhoto = function(roomID, photoID) {
        return $http.delete(urlBase + '/rooms/' + roomID + '/photo/' + photoID);
    }

    api.getAminities = function() {
        return $http.get(urlBase + '/rooms/aminities');
    }

    api.saveFacility = function(facility) {
        return $http.post(urlBase + '/rooms/facility', facility);
    }

    api.saveRoomAminities = function(roomID, data) {
        return $http.post(urlBase + '/room/' + roomID + '/aminities', data);
    }

    api.getRoomTypes = function() {
        return $http.get(urlBase + '/rooms/types');
    }
    api.saveCalendar = function(calendar) {
        return $http.post(urlBase + '/calendar', calendar);
    }

    api.getRoomCalendar = function(roomID) {
        return $http.get(urlBase + '/room/' + roomID + '/calendar');
    }



    return api;
}]);