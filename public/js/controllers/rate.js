copenhagenApp
    .controller('rateCtrl', ['$scope', '$rootScope', '$state', 'API', 'sh',
        function($scope, $rootScope, $state, API, sh) {
            $scope.rates = null;
            $scope.rate = null;
            API.getRates().then(function(response) {
                $scope.rates = response.data;
            }, function(err) {
                showPopup('Error', error.data, sh);
            });

            $scope.save = function(isValid) {
                if (isValid) {
                    API.saveRate($scope.rate, $scope.rate.id).then(function(response) {
                        $scope.rates = response.data;
                        $scope.rate = null;
                    }, function(err) {
                        showPopup('Error', error.data, sh);
                    });
                }
            }

            $scope.edit = function(rate) {
                $scope.rate = rate;
                $scope.rate.active = rate.active == 1;
            }

            $scope.delete = function(id) {
                popupModal = sh.openModal('globalPopup.html', 'Confirm', 'Are you sure?', 'ModalInstanceCtrl');
                popupModal.result.then(function(result) {
                    if (result == 'ok') {
                        API.deleteRate(id).then(function(response) {
                            $scope.rates = response.data;
                            $scope.rate = null;
                        }, function(err) {
                            showPopup('Error', error.data, sh);
                        });
                    }
                });
            }
        }
    ])

.controller('ModalInstanceCtrl', function($scope, $uibModalInstance, modalTitle, bodyMessage) {
    $scope.modalTitle = modalTitle;
    $scope.modalMessage = bodyMessage;
    $scope.ok = function() {

        $uibModalInstance.close();

    };

    $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
    };
});