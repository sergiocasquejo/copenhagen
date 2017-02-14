copenhagenApp.controller('homeCtrl', ['$scope', '$rootScope', '$state', 'API', function($scope, $rootScope, $state, API) {

    $scope.myInterval = 5000;
    $scope.noWrapSlides = false;
    $scope.active = 0;
    var slides = $scope.slides = [{
        id: 0,
        image: '/images/slider/1.jpg'
    }, {
        id: 1,
        image: '/images/slider/2.jpg'
    }, {
        id: 2,
        image: '/images/slider/3.jpg'
    }, {
        id: 3,
        image: '/images/slider/4.jpg'
    }, {
        id: 4,
        image: '/images/slider/5.jpg'
    }];
    var currIndex = 0;

}])

.controller('bookingFormCtrl', ['$scope', '$rootScope', '$state', 'API', function($scope, $rootScope, $state, API) {

}]);