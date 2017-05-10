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
                        showPopup('Error', error.data, sh);
                    });
                }
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
                showPopup('Error', error.data, sh);
            });
            sc.save = function(isValid) {
                if (isValid) {
                    API.saveSeoMeta(sc.seo).then(function(response) {
                        showPopup('Success', response.data, sh);
                    }, function(error) {
                        showPopup('Error', error.data, sh);
                    });
                }
            }
            sc.slugit = function() {
                sc.seo.slug = sc.seo.slug.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            }
        }
    ]);