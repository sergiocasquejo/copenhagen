copenhagenApp.controller('loginCtrl', ['$scope', '$rootScope', '$state', 'API',
        function($scope, $rootScope, $state, API) {
            if (API.isAuthenticated()) {
                $state.go($state.go('adminRoomSetup'));
            }
            $scope.auth = {};
            $scope.login = function(isValid) {
                if (isValid) {
                    API.login($scope.auth).then(function(response) {
                        if (response.data) {
                            API.setCurrentUser(response.data);
                            $state.go('adminRoomSetup');
                        }
                    }, function(error) {
                        $rootScope.message = error.data;
                    });
                }

            }
        }
    ])
    .controller('logoutCtrl', ['$scope', '$rootScope', '$state', 'API',
        function($scope, $rootScope, $state, API) {
            API.logout().then(function() {
                $state.go('login');
            }, function(error) {
                console.log(error);
            });
        }
    ]);