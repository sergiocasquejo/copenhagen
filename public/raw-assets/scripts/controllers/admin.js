copenhagenApp.controller('profileCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh',
        function($scope, $rootScope, $state, API, sh) {
            var sc = $scope;
            sc.user = API.getCurrentUser();
            sc.isReadOnly = true;
            sc.update = function(isValid) {
                if (isValid) {
                    API.updateProfile(sc.user.id, {
                        username: sc.user.username,
                        email: sc.user.email,
                        password: sc.user.password,
                        password2: sc.user.password2
                    }).then(function() {
                        showPopup('Success', 'Account successfully updated. system will be logout.', sh);
                        $state.go('logout');
                    }, function(error) {
                        if (error.status == 400) { showPopup('Error', error.data, sh); }
                    });
                }
            }

            sc.status = {
                isopen: false
            };

            sc.toggled = function(open) {
                $log.log('Dropdown is now: ', open);
            };

            sc.toggleDropdown = function($event) {
                $event.preventDefault();
                $event.stopPropagation();
                sc.status.isopen = !sc.status.isopen;
            };

        }
    ])
    .controller('disableDateCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh',
        function($scope, $rootScope, $state, API, sh) {
            var sc = $scope;
            sc.data = {
                "selectedDate": "",
                "room": "",
            };

            sc.dates = [];
            sc.rooms = [];

            API.getDisabledDates().then(function(response) {
                sc.dates = response.data;
            }, function(err) {
                if (error.status == 400) { showPopup('Error', error.data, sh); }
            });

            API.getRoomsLists().then(function(response) {
                sc.rooms = response.data;
            }, function(err) {
                if (error.status == 400) { showPopup('Error', error.data, sh); }
            });

            sc.save = function(isValid) {
                if (isValid) {
                    API.saveDisableDate({
                        room: sc.data.room,
                        selected_date: moment(sc.data.selectedDate).format('YYYY-MM-DD')
                    }).then(function(response) {
                        sc.dates = response.data;
                        sc.data = null;
                        showPopup('Success', 'Successfully saved.', sh);
                    }, function(error) {
                        if (error.status == 400) { showPopup('Error', error.data, sh); }
                    });
                }
            }

            sc.delete = function(id) {
                popupModal = sh.openModal('globalPopup.html', 'Confirm', 'Are you sure?', 'ModalInstanceCtrl');
                popupModal.result.then(function(result) {
                    if (result == 'ok') {
                        API.deleteDisabledDate(id).then(function(response) {
                            sc.dates = response.data;
                        }, function(err) {
                            if (error.status == 400) { showPopup('Error', error.data, sh); }
                        });
                    }
                });
            }
        }
    ])
    .controller('metaContentCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh', '$stateParams',
        function($scope, $rootScope, $state, API, sh, $stateParams) {
            var sc = $scope;
            sc.seo = {};

            API.seoMeta($stateParams.type, $stateParams.id).then(function(response) {
                sc.seo = response.data;
            }, function(error) {
                if (error.status == 400) { showPopup('Error', error.data, sh); }
            });
            sc.save = function(isValid) {
                if (isValid) {
                    API.saveSeoMeta(sc.seo).then(function(response) {
                        showPopup('Success', response.data, sh);
                    }, function(error) {
                        if (error.status == 400) { showPopup('Error', error.data, sh); }
                    });
                }
            }
            sc.slugit = function() {
                sc.seo.slug = sc.seo.slug.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            }
        }
    ]);