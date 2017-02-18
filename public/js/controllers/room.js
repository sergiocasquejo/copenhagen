'use strict';
copenhagenApp
    .controller('roomCtrl', ['$scope', '$rootScope', '$state', 'API', 'FileUploader', 'CSRF_TOKEN', '$uibModal', '$log', 'sh',
        function($scope, $rootScope, $state, API, FileUploader, CSRF_TOKEN, $uibModal, $log, sh) {
            $scope.roomLists = {};
            $scope.rateLists = null;
            $scope.selectedRoom = null;
            var roomListIndex = null;
            API.getRooms().then(function(response) {
                $scope.roomLists = response.data;
            }, function(error) {
                console.log(error);
            });

            API.getRates().then(function(response) {
                $scope.rateLists = response.data;
                console.log($scope.rateLists);
            }, function(error) {
                console.log(error);
            });
            /*
            $scope.deleteRoom = function(room) {
                if (room) {
                    API.deleteRoom(room.id, room).then(function(response) {
                        $scope.roomLists = response.data;
                    }, function(error) {
                        console.log(error);
                    });
                }
            }*/

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
                uploader.url = '/api/v1/rooms/' + roomID + '/photos';
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
                var popupModal = sh.openModal('adminGlobalPopup.html', 'Confirm', 'Are you sure?', 'ModalInstanceCtrl');
                popupModal.result.then(function(result) {
                    if (result == 'ok') {
                        API.deleteRoom(room.id).then(function(response) {
                            $scope.roomLists = response.data;
                        }, function(error) {
                            sh.openModal('adminGlobalPopup.html', 'Error', error.data, 'ModalInstanceCtrl');
                        });
                    }
                });

                popupModal.result.then(function(roomLists) {
                    $scope.roomLists = roomLists;
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            };
            var aminitiesModalInstance

            $scope.openAminities = function(index, room) {
                roomListIndex = index;
                aminitiesModalInstance = $uibModal.open({
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
                aminitiesModalInstance.result.then(function(data) {
                    $scope.roomLists[data.index].facilities = data.data;
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            };
            $scope.deleteAminities = function(id) {
                var popupModal = sh.openModal('adminGlobalPopup.html', 'Confirm', 'Are you sure?', 'ModalInstanceCtrl');
                popupModal.result.then(function(result) {
                    if (result == 'ok') {
                        API.deleteAminities(id).then(function(response) {
                            aminitiesModalInstance.close();
                            $scope.openAminities(roomListIndex, $scope.roomLists[roomListIndex]);
                        }, function(error) {
                            sh.openModal('adminGlobalPopup.html', 'Error', error.data, 'ModalInstanceCtrl');
                        });
                    }
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
            return room.facilities.indexOf(id) !== -1 ? 1 : 0;
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
            $uibModalInstance.close('ok');
        };

        $scope.cancel = function() {
            $uibModalInstance.dismiss('cancel');
        };
    });