copenhagenApp
    .controller('roomCtrl', ['$scope', '$rootScope', '$state', 'API', 'FileUploader', 'CSRF_TOKEN', '$uibModal', '$log',
        function($scope, $rootScope, $state, API, FileUploader, CSRF_TOKEN, $uibModal, $log) {
            $scope.roomLists = {};
            $scope.selectedRoom = null;
            roomListIndex = null;

            API.getRooms().then(function(response) {
                $scope.roomLists = response.data;
            }, function(error) {
                console.log(error);
            });

            $scope.deleteRoom = function(room) {
                if (room) {
                    API.deleteRoom(room.id, room).then(function(response) {
                        $scope.roomLists = response.data;
                    }, function(error) {
                        console.log(error);
                    });
                }
            }

            $scope.openCorfirmPopup = function(index) {
                roomListIndex = index;
                $scope.open();
            }

            $scope.deletePhoto = function(index, roomID, photoID) {
                API.deletePhoto(roomID, photoID).then(function(response) {
                    if ($scope.roomLists[index]) {
                        $scope.roomLists[index].photos = response.data;
                    }
                }, function(error) {
                    console.log(error);
                });
            }
            $scope.addNewRoom = function() {
                var room = {};
                API.saveRoom(room).then(function(response) {
                    $scope.roomLists = response.data;
                }, function(error) {
                    console.log(error);
                });
            }

            $scope.save = function(isValid, room) {
                if (isValid) {
                    API.saveRoom(room).then(function(response) {
                        $scope.roomLists = response.data;
                    }, function(error) {
                        console.log(error);
                    })
                }
            }

            // Photos Upoader
            var uploader = $scope.uploader = new FileUploader({
                formData: [{ '_token': CSRF_TOKEN }],
                alias: 'photo',
                autoUpload: true
            });

            $scope.setPhotoRoomID = function(roomID, index) {
                roomListIndex = index;
                uploader.url = '/api/v1/room/' + roomID + '/photo';
                uploader.formData.push({ 'roomID': roomID });
            }
            $scope.clearUploadQueue = function() {
                uploader.queue = [];
            }
            uploader.onAfterAddingFile = function(fileItem) {
                fileItem.upload();
            };
            uploader.onSuccessItem = function(fileItem, response, status, headers) {
                if ($scope.roomLists[roomListIndex]) {
                    $scope.roomLists[roomListIndex].photos = response;
                }
                //roomListIndex = null;
            };


            var controller = $scope.controller = {
                isImage: function(item) {
                    var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                    return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
                }
            };

            $scope.open = function(room) {
                var modalInstance = $uibModal.open({
                    templateUrl: "confirmPopup.html",
                    controller: 'ModalInstanceCtrl',
                    scope: $scope,
                    resolve: {
                        room: function() {
                            return room;
                        },
                        API: function() {
                            return API;
                        }
                    }
                });
                modalInstance.result.then(function(roomLists) {
                    $scope.roomLists = roomLists;
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            };


            $scope.openAminities = function(index, room) {
                var modalInstance = $uibModal.open({
                    templateUrl: "aminitiesPopup.html",
                    controller: 'ModalAminitiesInstanceCtrl',
                    scope: $scope,
                    size: 'lg',
                    resolve: {
                        index: function() {
                            return index;
                        },
                        room: function() {
                            return room;
                        },
                        API: function() {
                            return API;
                        }
                    }
                });
                modalInstance.result.then(function(data) {

                    $scope.roomLists[data.index].aminities = data.data;
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            }



        }
    ])


.controller('ModalAminitiesInstanceCtrl', function($scope, $uibModalInstance, index, room, API) {
        $scope.aminities = [];
        $scope.facility = null;
        $scope.selected = [];

        API.getAminities().then(function(response) {
            $scope.aminities = response.data;
        }, function(error) {
            console.log(error);
        });

        $scope.saveFacility = function(isValid) {
            if (isValid) {
                API.saveFacility($scope.facility).then(function(response) {
                    $scope.aminities = response.data;
                }, function(error) {

                });
            }
        }

        $scope.aminitiesExists = function(id) {

            return room.aminities.indexOf(id) !== -1 ? 1 : 0;
        };

        $scope.ok = function() {

            for (var i in $scope.aminities) {
                var facility = $scope.aminities[i];
                if (facility.active) {
                    $scope.selected.push(facility.id);
                }
            }

            API.saveRoomAminities(room.id, { roomID: room.id, aminities: JSON.stringify($scope.selected) })
                .then(function(response) {
                    var data = {
                        index: index,
                        data: response.data
                    };

                    $uibModalInstance.close(data);
                }, function(error) {
                    // console.log(error);
                });

        };

        $scope.cancel = function() {
            $uibModalInstance.dismiss('cancel');
        };
    })
    .controller('ModalInstanceCtrl', function($scope, $uibModalInstance, room, API) {
        $scope.ok = function() {
            API.deleteRoom(room.id, { params: { roomID: room.id } }).then(function(response) {
                $uibModalInstance.close(response.data);
            }, function(error) {
                console.log(error);
            });

        };

        $scope.cancel = function() {
            $uibModalInstance.dismiss('cancel');
        };
    });