(function () {
    'use strict';
    var myApp = angular.module('Body');

    /************************************************* Misc Service ************************************************************/
    myApp.service('GlobeVars', function(){
        return{
            uploadFileName: '',
            activeShipmentSelect: '',
            blankRedirect: '',
            isMobile: ''
        };
    });

    /************************************************* Image Upload Service ************************************************************/
    myApp.service('imageUploadService', function ($http, $q) {

        this.uploadImageToUrl = function (file, uploadUrl, prof) {
            var fileFormData = new FormData();
            fileFormData.append('file', file);
            fileFormData.append('prof', prof);


            var deffered = $q.defer();
            $http.post(uploadUrl, fileFormData,{
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}//, 'enctype': 'multipart/form-data'}
            })
                .then(function (response) {
                    deffered.resolve(response);
                }, function (response) {
                    deffered.reject(response);
                });

            return deffered.promise;
        }
    });

    /************************************************* Page Scroll Service ************************************************************/
    myApp.service('scrollPageService', function($rootScope){
        //... Scroll Through Pages on mouseWheel ...
        this.pageOnScroll = function(meth, varToGet){
            var direction = '';
            meth === 'mouse' ? direction = getMouseScrollDirection(varToGet) : meth === 'touch'? direction = getTouchScrollDirection(varToGet) : direction = getKeyScrollDirection(varToGet);

            setScrollAnim(direction);
            var dir = $rootScope.curPage;
            if(direction === 'down'){
                if($rootScope.curPage < 6){
                    dir++;
                }
            }
            if(direction === 'up'){
                if($rootScope.curPage > 0){
                    dir--;
                }
            }
            if($rootScope.indexReady){
                $rootScope.shiftPafe(dir);
            }
        };
        //... get direction from mouse event ...
        function getMouseScrollDirection(e){
            var delta = null,
                direction = false;

            if(!e){ //If event not provided, get it from window object.
                e = window.event;
            }
            if(e.wheelDelta){ //...Most Browsers ...
                delta = e.wheelDelta / 60;
            }else if(e.detail){ //...fallback for Firefox
                delta = -e.detail / 2;
            }
            if(delta !== null){
                direction = delta > 0 ? 'up' : 'down';
            }
            return direction;
        }
        //... get direction from Touch event ...
        function getTouchScrollDirection(e){
            return (e === 'pandown') ? 'up' : (e === 'panup') ? 'down' : '';
        }
        //... get direction from keyboard event ...
        function getKeyScrollDirection(e){
            return (e === 'ArrowUp') ? 'up' : (e === 'ArrowDown') ? 'down' : '';
        }
        //... Detect current and Set new page Scroll Animation Direction ...
        function setScrollAnim(direction){
            var remClass = '',
                addClass = '',
                item;
            if($rootScope.pageAnimDir !== direction){
                if(direction === 'up'){
                    item = document.getElementById("mainPage");
                    remClass = 'changeUP';
                    addClass = 'changeDOWN';
                }else{
                    item = document.getElementById("mainPage");
                    remClass = 'changeDOWN';
                    addClass = 'changeUP';
                }

                if($rootScope.indexReady){
                    item.classList.add(addClass);
                    item.classList.remove(remClass);
                    $rootScope.pageAnimDir = direction;
                }

            }
        }


    }); /*** END 'scrollPageService' ***/
})();