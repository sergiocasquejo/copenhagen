copenhagenApp.controller('loginCtrl', ['$scope', '$rootScope', '$state', 'API',
        function($scope, $rootScope, $state, API) {

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