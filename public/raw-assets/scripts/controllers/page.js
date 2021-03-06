copenhagenApp
    .controller('pagesIndexCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh',
        function($scope, $rootScope, $state, API, sh) {
            $scope.pages = null;

            API.getPages().then(function(response) {
                $scope.pages = response.data;
            }, function(err) {
                if (error.status != 400) { showPopup('Error', error.data, sh); }
            });

            $scope.delete = function(id) {
                popupModal = sh.openModal('globalPopup.html', 'Confirm', 'Are you sure?', 'ModalInstanceCtrl');
                popupModal.result.then(function(result) {
                    if (result == 'ok') {
                        API.deletePage(id).then(function(response) {
                            $scope.pages = response.data;
                            $scope.page = null;
                        }, function(err) {
                            if (error.status == 400) { showPopup('Error', error.data, sh); }
                        });
                    }
                });
            }


        }
    ])
    .controller('pagesCreateCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh', '$stateParams',
        function($scope, $rootScope, $state, API, sh, $stateParams) {
            $scope.page = null;

            var id = $stateParams.id;
            if (id != 0) {
                API.getPage(id).then(function(response) {
                    $scope.page = response.data;


                }, function(err) {
                    if (error.status == 400) { showPopup('Error', error.data, sh); }
                });
            }

            $scope.save = function(isValid) {
                if (isValid) {
                    API.savePage($scope.page, $scope.page.id).then(function(response) {
                        if (id == 0) {
                            $state.go('pagesCreate', { id: response.data.id });
                        }
                        showPopup('Success', 'Successfully saved.', sh);
                    }, function(error) {
                        if (error.status == 400) { showPopup('Error', error.data, sh); }
                    });
                }
            }



        }
    ])
    .controller('pageDetailsCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh', '$stateParams',
        function($scope, $rootScope, $state, API, sh, $stateParams) {
            $scope.page = null;

            var slug = $stateParams.slug;
            if (slug) {

                API.getPageBySlug(slug).then(function(response) {
                    $scope.page = response.data;
                    $rootScope.title = $scope.page.seo.metaTitle;
                    $rootScope.description = $scope.page.seo.metaDescription;
                    $rootScope.keywords = $scope.page.seo.metaKeywords;
                    $rootScope.canonical = $scope.page.seo.canonicalLinks;
                }, function(err) {
                    if (err.status == 404) $scope.page = 404;
                    if (err.status == 400) { showPopup('Error', err.data, sh); }
                });
            }



        }
    ]);