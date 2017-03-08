copenhagenApp.controller('profileCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh',
    function($scope, $rootScope, $state, API, sh) {
        var sc = $scope;
        sc.user = API.getCurrentUser();
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
                    showPopup('Error', error.data, sh);
                });
            }
        }
    }
]);