copenhagenApp.factory('API', ['$http', '$rootScope', '$state', '$cookies', function($http, $rootScope, $state, $cookies) {
        var urlBase = '';
        var api = {};
        api.login = function(auth) {
            return $http.post(urlBase + '/login', auth);
        };

        api.logout = function() {
            $cookies.remove('currentUser');
            return $http.get(urlBase + '/logout');
        };

        api.isAuthenticated = function() {
            if ($cookies.get('currentUser') == undefined) return false;
            return $cookies.get('currentUser');
        };

        api.setCurrentUser = function(user) {
            $cookies.put('currentUser', JSON.stringify(user[0]));
        };

        api.getCurrentUser = function() {

            var _user = $cookies.get('currentUser');
            if (_user == null) return;
            return JSON.parse(_user);
        };

        api.updateProfile = function(id, user) {
            return $http.put(urlBase + '/profile/' + id, user);
        };
        /*============================================================================================
         * Seo Factory
         *============================================================================================*/
        api.seoMeta = function(type, id) {
            return $http.get(urlBase + '/seo/' + type + '/' + id);
        };
        api.saveSeoMeta = function(seo) {
            return $http.put(urlBase + '/seo/' + seo.id, seo);
        };
        /*============================================================================================
         * Rate Factory
         *============================================================================================*/

        api.getRates = function() {
            return $http.get(urlBase + '/rates');
        };
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

        api.getRoomsLists = function() {
            return $http.get(urlBase + '/rooms/lists');
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
            $cookies.put('Booking', JSON.stringify(data));
        }
        api.getBookingData = function() {
            var _b = $cookies.get("Booking");
            if (_b == null) return;
            return JSON.parse(_b);
        }

        api.book = function(data) {
            return $http.post(urlBase + '/book', data);
        }


        api.getBookings = function() {
            return $http.get(urlBase + '/bookings');
        }

        api.sendEmailNotification = function(ref) {
            return $http.post(urlBase + '/booking/notify', { Ref: ref });
        }

        api.checkRoomAvailability = function(params) {
            return $http.post(urlBase + '/rooms/availability', params);
        }

        api.fetchUnavailableCalendarByRoomId = function(roomID, start, end) {
            return $http.get(urlBase + '/rooms/' + roomID + '/calendar/unavailable/' + start + '/' + end);
        }
        api.bookingStep = function(params, step) {
            return $http.post(urlBase + '/booking/step/' + step, params);
        }

        api.sendContact = function(params) {
            return $http.post(urlBase + '/contact', params);
        }

        api.getDisabledDates = function() {
            return $http.get(urlBase + '/disable-date');
        }
        api.saveDisableDate = function(params) {
            return $http.post(urlBase + '/disable-date', params);
        }

        api.deleteDisabledDate = function(id) {
            return $http.delete(urlBase + '/disable-date/' + id);
        }


        // Pages
        api.getPages = function() {
            return $http.get(urlBase + '/pages');
        }

        api.getPage = function(id) {
            return $http.get(urlBase + '/pages/' + id);
        }

        api.savePage = function(rate, id) {
            if (id) {
                return $http.put(urlBase + '/pages/' + id, rate);
            }
            return $http.post(urlBase + '/pages', rate);
        }
        api.deletePage = function(id) {
            return $http.delete(urlBase + '/pages/' + id);
        }
        api.getPageBySlug = function(slug) {
            return $http.get(urlBase + '/page/' + slug);
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
                    $cookies.remove('currentUser');

                    $location.path('/login').search('returnTo', $location.path());
                }
                return $q.reject(rejection);
            }
        }
    }])