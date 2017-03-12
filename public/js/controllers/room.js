'use strict';
copenhagenApp
    .controller('roomCtrl', ['$scope', '$rootScope', '$state', 'API', 'FileUploader', '$uibModal', '$log', 'sh',
        function($scope, $rootScope, $state, API, FileUploader, $uibModal, $log, sh) {
            var sc = $scope;
            sc.loaded = false;
            sc.roomLists = {};
            sc.rateLists = null;
            sc.selectedRoom = null;
            sc.beddingLists = CopenhagenAppConfig.bedding;
            var roomListIndex = null;
            var popupModal = null;
            API.getRooms().then(function(response) {
                sc.roomLists = response.data;
                sc.loaded = true;
            }, function(error) {
                showPopup('Error', error.data, sh);
                sc.loaded = true;
            });

            API.getRates().then(function(response) {
                sc.rateLists = response.data;
            }, function(error) {
                showPopup('Error', error.data, sh);
            });

            sc.openCorfirmPopup = function(index) {
                roomListIndex = index;
                sc.open();
            }

            sc.deletePhoto = function(index, roomID, photoID) {
                API.deletePhoto(roomID, photoID).then(function(response) {
                    if (sc.roomLists[index]) {
                        sc.roomLists[index].photos = response.data;
                    }
                }, function(error) {
                    showPopup('Error', error.data, sh);
                });
            }
            sc.addNewRoom = function() {
                var room = {};
                API.saveRoom(room).then(function(response) {
                    sc.roomLists = response.data;
                }, function(error) {
                    showPopup('Error', error.data, sh);
                });
            }

            sc.save = function(isValid, room) {
                if (isValid) {
                    API.saveRoom(room).then(function(response) {
                        sc.roomLists = response.data;
                    }, function(error) {
                        showPopup('Error', error.data, sh);
                    })
                }
            }



            sc.showLoader = function() {
                popupModal = sh.openModal('globalPopup.html', 'Loading...', '<div class="progress"><div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">60% Complete</span></div></div>');
            }



            // Photos Upoader
            var uploader = sc.uploader = new FileUploader({
                formData: [{ '_token': CopenhagenAppConfig.csrfToken }],
                alias: 'photo',
                autoUpload: true
            });

            sc.setPhotoRoomID = function(roomID, index) {
                roomListIndex = index;
                uploader.url = '/rooms/' + roomID + '/photos';
                uploader.formData.push({ 'roomID': roomID });
            }
            sc.clearUploadQueue = function() {
                uploader.queue = [];
            }

            uploader.onErrorItem = function(item, response, status, headers) {
                popupModal.dismiss('cancel');
                // console.log(item, response, status, headers);
                showPopup('Error', response, sh);
            }

            uploader.onProgressAll = function(progress) {
                sc.showLoader();
            };


            uploader.onAfterAddingFile = function(fileItem) {
                fileItem.upload();
            };
            uploader.onSuccessItem = function(fileItem, response, status, headers) {
                if (sc.roomLists[roomListIndex]) {
                    sc.roomLists[roomListIndex].photos = response;
                }

                popupModal.dismiss('cancel');

            };






            var controller = sc.controller = {
                isImage: function(item) {
                    var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                    return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
                }
            };

            sc.open = function(room) {
                var popupModal = sh.openModal('globalPopup.html', 'Confirm', 'Are you sure?', 'ModalInstanceCtrl');
                popupModal.result.then(function(result) {
                    if (result == 'ok') {
                        API.deleteRoom(room.id).then(function(response) {
                            sc.roomLists = response.data;
                        }, function(error) {
                            showPopup('Error', error.data, sh);
                        });
                    }
                });

                popupModal.result.then(function(roomLists) {
                    sc.roomLists = roomLists;
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            };
            var aminitiesModalInstance

            sc.openAminities = function(index, room) {
                roomListIndex = index;
                aminitiesModalInstance = $uibModal.open({
                    templateUrl: "aminitiesPopup.html",
                    controller: 'ModalAminitiesInstanceCtrl',
                    scope: sc,
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
                    sc.roomLists[data.index].facilities = data.data;
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            };
            sc.deleteAminities = function(id) {
                var popupModal = sh.openModal('globalPopup.html', 'Confirm', 'Are you sure?', 'ModalInstanceCtrl');
                popupModal.result.then(function(result) {
                    if (result == 'ok') {
                        API.deleteAminities(id).then(function(response) {
                            aminitiesModalInstance.close();
                            sc.openAminities(roomListIndex, sc.roomLists[roomListIndex]);
                        }, function(error) {
                            showPopup('Error', error.data, sh);
                        });
                    }
                });


            }

            var beddingModalInstance;
            sc.openBedding = function(index, room) {
                roomListIndex = index;
                beddingModalInstance = $uibModal.open({
                    templateUrl: "beddingPopup.html",
                    controller: 'ModalBeddingInstanceCtrl',
                    scope: sc,
                    size: 'md',
                    resolve: {
                        index: function() {
                            return index;
                        },
                        room: function() {
                            return room;
                        },
                        API: function() {
                            return API;
                        },
                        sh: function() {
                            return sh;
                        }
                    }
                });
                beddingModalInstance.result.then(function(data) {
                    console.log(data);
                    sc.roomLists[data.index].beds = data.data;
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
                    showPopup('Error', error.data, sh);
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
    })
    .controller('ModalBeddingInstanceCtrl', function($scope, $uibModalInstance, index, room, API, sh) {
        var sc = $scope;
        var defaultBed = {
            'id': '_static_' + new Date().getTime(),
            'qty': 1,
            'roomID': room.id,
            'type': 'queen size'
        };
        sc.beds = room.beds;



        if (room.beds == undefined || !room.beds.length) {
            sc.beds = [defaultBed];
        }



        sc.bedTypeLists = CopenhagenAppConfig.bedding;

        sc.addBed = function() {
            sc.beds.push({
                'id': '_static_' + new Date().getTime(),
                'qty': 1,
                'roomID': room.id,
                'type': 'queen size'
            });

        }

        sc.deleteBed = function(index) {
            var b = sc.beds[index];
            if (b.id != undefined) {
                API.deleteRoomBed(room.id, b.id).then(function(response) {}, function(error) {
                    showPopup('Error', error.data, sh);
                });
            }
            sc.beds.splice(index, 1);
        }

        sc.ok = function() {
            API.saveRoomBed(room.id, { beds: sc.beds }).then(function(response) {
                $uibModalInstance.close({ index: index, data: response.data });
            }, function(error) {
                showPopup('Error', error.data, sh);
            });

        };
        sc.cancel = function() {
            $uibModalInstance.dismiss('cancel');
        };
    });