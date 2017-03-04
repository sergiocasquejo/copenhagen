copenhagenApp.factory('API', ['$http', '$rootScope', '$state', '$window', function($http, $rootScope, $state, $window) {
        var urlBase = '';
        var api = {};
        api.login = function(auth) {
            return $http.post(urlBase + '/login', auth);
        };

        api.logout = function() {
            $window.sessionStorage.removeItem('currentUser');
            return $http.get(urlBase + '/logout');
        }

        api.isAuthenticated = function() {
            return JSON.parse($window.sessionStorage.getItem('currentUser')) !== null;
        };

        api.setCurrentUser = function(user) {
            $window.sessionStorage.setItem('currentUser', JSON.stringify(user));
        }

        api.getCurrentUser = function() {
            var _user = JSON.parse($window.sessionStorage.getItem("currentUser"));
            return _user != null && _user.length > 0 ? _user[0] : _user;
        }

        /*============================================================================================
         * Rate Factory
         *============================================================================================*/

        api.getRates = function() {
            return $http.get(urlBase + '/rates');
        }
        api.saveRate = function(rate, id) {
            if (id) {
                return $http.put(urlBase + '/rates/' + id, rate);
            }
            return $http.post(urlBase + '/rates', rate);
        }
        api.deleteRate = function(id) {
            return $http.delete(urlBase + '/rates/' + id);
        }

        /*============================================================================================
         * Room Factory
         *============================================================================================*/

        api.saveRoom = function(room) {
            if (room.id) {
                return $http.put(urlBase + '/rooms/' + room.id, room);
            }
            return $http.post(urlBase + '/rooms', room);
        }
        api.getRooms = function() {
            return $http.get(urlBase + '/rooms');
        }

        api.getAvailableRooms = function() {
            return $http.get(urlBase + '/rooms/available');
        }

        api.getRoomBySlug = function(slug) {
            return $http.get(urlBase + '/rooms/' + slug);
        }
        api.getRoomById = function(id) {
            return $http.get(urlBase + '/room/' + id);
        }

        api.deleteRoom = function(roomID) {
            return $http.delete(urlBase + '/rooms/' + roomID);
        }

        api.deletePhoto = function(roomID, photoID) {
            return $http.delete(urlBase + '/rooms/' + roomID + '/photos/' + photoID);
        }

        api.getAminities = function() {
            return $http.get(urlBase + '/rooms/aminities');
        }

        api.saveFacility = function(facility) {
            return $http.post(urlBase + '/rooms/aminities', facility);
        }

        api.deleteAminities = function(id) {
            return $http.delete(urlBase + '/rooms/aminities/' + id);
        }

        api.saveRoomAminities = function(roomID, data) {
            return $http.post(urlBase + '/rooms/' + roomID + '/aminities', data);
        }

        api.saveRoomBed = function(roomID, data) {
            return $http.post(urlBase + '/rooms/' + roomID + '/beds', data);
        }
        api.deleteRoomBed = function(roomID, bedID) {
            return $http.delete(urlBase + '/rooms/' + roomID + '/beds/' + bedID);
        }

        api.getRoomTypes = function() {
            return $http.get(urlBase + '/rooms/types');
        }
        api.saveCalendar = function(calendar) {
            return $http.post(urlBase + '/calendar', calendar);
        }

        api.fetchCalendarByRoomIdAndDate = function(roomID, start, end) {
            return $http.get(urlBase + '/rooms/' + roomID + '/calendar/' + start + '/' + end);
        }
        api.setBookingData = function(data) {
            $rootScope.booking = data;
            $window.sessionStorage.setItem('Booking', JSON.stringify(data));
        }
        api.getBookingData = function() {
            var _b = JSON.parse($window.sessionStorage.getItem("Booking"));

            return _b != null && _b.length > 0 ? _b[0] : _b;
        }

        api.book = function(data) {
            return $http.post(urlBase + '/book', data);
        }


        api.getBookings = function() {
            return $http.get(urlBase + '/bookings');
        }

        api.checkRoomAvailability = function(params) {
            return $http.post(urlBase + '/rooms/availability', params);
        }

        api.fetchUnavailableCalendarByRoomId = function(roomID) {
            return $http.get(urlBase + '/rooms/' + roomID + '/calendar/unavailable');
        }
        api.bookingStep = function(params, step) {
            return $http.post(urlBase + '/booking/step/' + step, params);
        }

        api.sendContact = function(params) {
            return $http.post(urlBase + '/contact', params);
        }

        return api;
    }])
    .factory('authHttpResponseInterceptor', ['$q', '$location', function($q, $location) {
        return {
            response: function(response) {
                if (response.status === 401) {
                    console.log("Response 401");
                }
                return response || $q.when(response);
            },
            responseError: function(rejection) {
                if (rejection.status === 401) {

                    console.log("Response Error 401", rejection);

                    $location.path('/login').search('returnTo', $location.path());
                }
                return $q.reject(rejection);
            }
        }
    }])