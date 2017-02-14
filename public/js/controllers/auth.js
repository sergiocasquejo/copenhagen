copenhagenApp.controller('loginCtrl', ['$scope', '$rootScope', '$state', 'API', function($scope, $rootScope, $state, API) {
    $scope.auth = {};
    $scope.login = function(isValid) {
        if (isValid) {
            API.login($scope.auth).then(function(response) {
                if (response.data === 'success') {
                    $state.go('home');
                }
            }, function(error) {
                $rootScope.message = error.data;
            });
        }

    }
}]);