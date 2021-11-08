(function () {
    'use strict';
    var myApp = angular.module('Body');


    /** ***********************************************************************************************************/
    /************************************************* Main Controller Mobile ************************************************************/
    /** ***********************************************************************************************************/
    myApp.controller('mainControllerMobile', function($scope, $location, $rootScope, $timeout, $log, $http){

        $rootScope.isMobile = true;
        $scope.toLogistics = ToLogistics;
        function ToLogistics(){
            $rootScope.loading = true;
            //... Fullscreen ...
            /*var docElm = document.documentElement;
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            } else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            } else if (docElm.webkitRequestFullScreen) {
                docElm.webkitRequestFullScreen();
            } else if (docElm.msRequestFullscreen) {
                docElm.msRequestFullscreen();
            }*/
            $timeout(function(){
                $location.path('/logistics');
            },500);

        }

    });
    /** ***********************************************************************************************************/
    /************************************************* Main Controller ************************************************************/
    /** ***********************************************************************************************************/
    myApp.controller('logisticsControllerMobile', function($scope, $rootScope, $timeout, $log, $http, $location, scrollPageService, GlobeVars){

        $rootScope.isMobile = true;
        var panAct = '',
            panDir = 0;
        /******************************** Load Doc *************************************/
        $rootScope.indexReady = false;
        angular.element(document).ready(function(){
            $timeout(function() {
                $rootScope.indexReady = true;
                $rootScope.loading = false;
                //...

                var mainElement = document.getElementById('mainPage');
                var mc = new Hammer(mainElement);
                mc.get('pan').set({ direction: Hammer.DIRECTION_ALL });
                // listen to events...
                mc.on("panleft panright panup pandown tap press", function(ev) {
                    if((ev.type === 'panup') || (ev.type === 'pandown')){
                        panAct = ev.type;
                    }
                    //... initiate scroll only when animation finished.
                    if((panAct === ev.type) && (panDir === 0)){
                        panDir = ev.direction;
                        scrollPageService.pageOnScroll('touch', panAct);
                    }
                    //... Open/Close Side menu ...
                    if(((ev.type === 'panleft') || (ev.type === 'panright')) && (panDir === 0)){
                        panDir = ev.direction;
                        $rootScope.togLeftMenu(ev.type);
                    }
                });
            }, 1);
        });

    });


})();