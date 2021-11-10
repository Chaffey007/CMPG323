(function() {
    'use strict';
    var myApp = angular.module('Body');

    /****************************************************** Image Upload (Profile) ****************************************************************************/
    myApp.directive('demoImgSelect', function ($parse, $rootScope) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                var model = $parse(attrs.demoImgSelect),
                    modelSetter = model.assign;
                element.bind('change', function () {
                    scope.$apply(function () {
                        modelSetter(scope, element[0].files[0]);
                        $rootScope.shopImgChanged(element[0]);
                    });
                });
            }
        };
    });

})();