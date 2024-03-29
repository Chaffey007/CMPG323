/*
manageBanking
*/
(function () {
    'use strict';
    var myApp = angular.module('Body');

    /** ***********************************************************************************************************/
    /************************************************* Direct To Page ************************************************************/
    /** ***********************************************************************************************************/
    myApp.config(['$routeProvider', '$locationProvider', function($routeProvider, $rootScope){
        //... Check if Mobile ...
        var _tst = window.mobileAndTabletcheck = function() {
            var check = false;
            (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
            return check;
        };

        _tst() ? $rootScope.isMobile = true : $rootScope.isMobile = false;

        $routeProvider
            .when('/login',{
                templateUrl: (_tst())?'Templates/indexLoginMobile.php':'Templates/indexLogin.php',
                controller: (_tst())?'loginControllerMobile':'loginController'
            })
            .when('/main',{
                resolve: {
                    check: function($location, $http, $rootScope) {
                        $http({
                            url: 'Services/credentialsGet.php',
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function(getResponse){
                            $rootScope.curUserPriv = $rootScope.fullName = $rootScope.fName = $rootScope.lName = $rootScope.uName = $rootScope.email = $rootScope.number = $rootScope.code = $rootScope.profpic = '';
                            if(getResponse.data.status === 'loggedin'){
                                console.warn(getResponse.data.status);
                                $rootScope.curUserPriv = getResponse.data.userPriv;
                                $rootScope.fName = getResponse.data.firstname;
                                $rootScope.lName = getResponse.data.lastname;
                                $rootScope.uName = getResponse.data.user;
                                $rootScope.email = getResponse.data.email;
                                $rootScope.number = getResponse.data.contactnum;
                                $rootScope.profreg = getResponse.data.userReg;
                                $rootScope.logStat = true;
                                $location.path('/main');
                            }
                            else if (getResponse.data.status !== 'loggedin'){
                                console.warn(getResponse.data.status);
                                $rootScope.logStat = false;
                                $location.path('/login');
                            }
                        });
                    }
                },
                templateUrl: (_tst())?'Templates/indexMainMobile.php':'Templates/indexMain.php',
                controller: (_tst())?'mainControllerMobile':'mainController'
            })
            .otherwise({
                redirectTo: '/login'
            });

    }]);
    /** ***********************************************************************************************************/
    /************************************************* Initialise Run ************************************************************/
    /** ***********************************************************************************************************/
    myApp.run(function($rootScope, $timeout, $document, scrollPageService){
        console.log('Going to: Melkbos Management Login...');

        //... Check slow network ...
        var slowLoad = window.setTimeout(function(){
            console.log('Slow Network');
        }, 70);
        window.addEventListener('load', function(){
            window.clearTimeout(slowLoad);
        }, false);

        //... Check scroll events for keyboard ...
        /*var bodyElement = angular.element($document);
        angular.forEach(['keydown'], function(EventName){
            bodyElement.bind(EventName, function(e){
                if((e.key === 'ArrowUp') || (e.key === 'ArrowDown')){
                    scrollPageService.pageOnScroll('keyboard', e.key)
                }
            });
        });*/
    });

    /** ***********************************************************************************************************/
    /************************************************* Loading Controller ************************************************************/
    /** ***********************************************************************************************************/
    myApp.controller('loadingController', function($scope, $rootScope, $timeout, $log, $http){
        $rootScope.documentReady = false;
        $rootScope.loading = true;

        angular.element(document).ready(function(){
            $rootScope.documentReady = true;
            $timeout(function() {
                $rootScope.loading = false;
            }, 2000);
        });
    });


    /** ***********************************************************************************************************/
    /************************************************* Login ************************************************************/
    /** ***********************************************************************************************************/

    /******************************** Login / Register *************************************/
    myApp.controller('loginController', function ($scope, $rootScope, $timeout, $mdSidenav, $log, $http, $location) {

        /*** Main ***/
        $scope.loginInputOne = "Username";
        $scope.logReg = 'Login';
        $scope.logRegTog = "Register";

        //... Change mail ...
        var email = '',
            maile = false;
        $scope.validateReset = function(data){
            $scope.logErrorMsg = "";
            var mail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            email = data;
            ((data.match(mail)) && (data !== undefined) && (data !== '')) ? maile = true : maile = false ;
        };

        /***************************** Login ************************************/
        $scope.login = function() {
            $rootScope.loading = true;

            //... Fullscreen ...
            var docElm = document.documentElement;
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            } else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            } else if (docElm.webkitRequestFullScreen) {
                docElm.webkitRequestFullScreen();
            } else if (docElm.msRequestFullscreen) {
                docElm.msRequestFullscreen();
            }


            var username = $rootScope.loginUsername;
            var password = $rootScope.loginPassword;
            $http({
                url: 'Services/serverLogin.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'username='+username+'&password='+password
            }).then(function(response) {
                    if(response.data.status === 'loggedin') {
                        $http({
                            url: 'Services/credentialsSet.php',
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            data: response.data
                        }).then(function(responseOne){
                                if(responseOne.data.status === 'loggedin'){
                                    $location.path('/main');
                                }
                            },
                            function(e){
                                $scope.logErrorMsg = "Failed to login !!! Please try again later.";
                                $rootScope.loading = false;
                            });
                    }
                    else if(response.data.status === 'notFound') {
                        $rootScope.loading = false;
                        $scope.logErrorMsg = "This Username does not exist.";
                    }
                    else if(response.data.status === 'DB Problem'){
                        $scope.logErrorMsg = "DB Error";
                    }
                    else {
                        $rootScope.loading = false;
                        $scope.logErrorMsg = "Incorrect Password";
                    }
                },
                function(){
                    $scope.logErrorMsg = "Connection Error";
                    $rootScope.loading = false;
                }
            );
        };

        /***************************** Register ************************************/
        $scope.register = function() {
            $rootScope.loading = true;

            var firstname = $rootScope.regFname;
            var lastname = $rootScope.regLname;
            var email = $rootScope.regMail;
            var username = $rootScope.regUsername;
            var password = $rootScope.regPassword;
            $http({
                url: 'Services/serverReg.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'firstname='+firstname+'&lastname='+lastname+'&email='+email+'&username='+username+'&password='+password
            }).then(function(response) {
                    if(response.data.status === 'Success') {
                        $http({
                            url: 'Services/serverLogin.php',
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            data: 'username='+username+'&password='+password
                        }).then(function(responseOne){
                                if(responseOne.data.status === 'loggedin'){
                                    $http({
                                        url: 'Services/credentialsSet.php',
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        data: responseOne.data
                                    }).then(function(responseTwo){
                                            if(responseTwo.data.status === 'loggedin'){
                                                $location.path('/main');
                                            }
                                        },
                                        function(e){
                                            $scope.logErrorMsg = "Failed to login after registration !!! Please try again later.";
                                            $rootScope.loading = false;
                                        });
                                }else{
                                    $scope.logErrorMsg = "Failed to Login after registration !!! Please try again later.";
                                    $rootScope.loading = false;
                                }
                            },
                            function(e){
                                $scope.logErrorMsg = "Failed to Login after registration !!! Please try again later.";
                                $rootScope.loading = false;
                            });
                    }
                    else if(response.data.status === 'Found'){
                        $rootScope.loading = false;
                        $scope.logErrorMsg = "This Username already exists. Please Try again!";
                    }
                    else {
                        $rootScope.loading = false;
                        $scope.logErrorMsg = "Failed to register !!! Please try again later.";
                    }
                },
                function(){
                    $scope.logErrorMsg = "Connection Error";
                    $rootScope.loading = false;
                }
            );
        };

        //... Clear Error Messge ...
        $scope.clearError = function(){
            $scope.logErrorMsg = "";
        };

        //...
        $scope.logRegSwitch = function(){
            if($scope.logReg == 'Login'){
                $scope.logReg = 'Register';
                $scope.logRegTog = 'Login';
            }else{
                $scope.logReg = 'Login';
                $scope.logRegTog = 'Register';
            }
        };


    });

    /** ***********************************************************************************************************/
    /************************************************* Main Controller ************************************************************/
    /** ***********************************************************************************************************/
    myApp.controller('mainController', ['$scope', '$rootScope', '$timeout', '$log', '$http', 'imageUploadService', '$location', 'GlobeVars', 'scrollPageService', function($scope, $rootScope, $timeout, $log, $http, imageUploadService, $location, GlobeVars, scrollPageService){

        $scope.showActivePage = 'Home';
        $rootScope.pageTitle = 'Image Management';
        $rootScope.isMobile = false;
        /******************************** Load Doc *************************************/
        $rootScope.indexReady = false;
        angular.element(document).ready(function(){
            $timeout(function() {
                $rootScope.indexReady = true;
                $rootScope.loading = false;

                $rootScope.fullName = $rootScope.fName + " " + $rootScope.lName;
            }, 500);
        });

        /******************************** Date *************************************/
        //... DatePicker ...
        let monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        $scope.shipshowDays = true;
        let dte = new Date();// get the current date
        let selectedDate = new Date();
        $scope.shipMonthShow = selectedDate;
        let month = selectedDate.getMonth();
        let year = selectedDate.getFullYear();
        let day = selectedDate.getDate();
        $scope.dispCurDate = day + " " + monthNames[month] + " " + year;
        $scope.nowDate = $scope.dispCurDate;
        //console.log(day);

        /************************************************* Auto Logout on Idle ************************************************************/
        $scope.logoutlog = function(){
            console.log('Logout');
        };

        $scope.onInactive = function(millisecond, callback){
            let wait = setTimeout(callback, millisecond);
            document.onmousemove = document.mousedown = document.mouseup = document.onkeydown = document.onkeyup = document.focus = document.onmousewheel = function() {
                clearTimeout(wait);
                wait = setTimeout(callback, millisecond);
            };
        };
        //....................... Log Out .......................
        /** Log Out **/
        $rootScope.logout = function(){
            $rootScope.loading = true;
            $http({
                url: 'Services/credentialsClear.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).then(function(responseOne){
                if(responseOne.data.status === ''){
                    $timeout(function() {
                        $rootScope.logStat = false;
                        window.location.href = 'index.php';
                    }, 400);
                }
            });
        };
        //..............................................

        /******************************** Profile Menu *************************************/
        $scope.togProfMenuDrop = TogProfMenuDrop;
        function TogProfMenuDrop(){
            $scope.showProfMenuDrop = !$scope.showProfMenuDrop;
        }

        /******************************** Left Menu *************************************/
        $rootScope.headMenu = 'menu';

        //... Toggle Left Menu ...
        $rootScope.togLeftMenu = TogLeftMenu;
        function TogLeftMenu(){
            $rootScope.showFullLeftMenu = !$rootScope.showFullLeftMenu;
            if(!$rootScope.showFullLeftMenu){
                $rootScope.headMenu = 'menu';
            }
            if($rootScope.showFullLeftMenu){
                $rootScope.headMenu = 'close';
            }
        }

        /******************************** Menu Selection *************************************/
        $scope.menuOptionList = [];
        $scope.menuOptions = ['Home', 'My Uploads', 'Shared With Me'];
        $scope.menuOptionsIcons = ['home', 'schedule','group'];
        for(let a = 0; a < $scope.menuOptions.length; a++){
            $scope.menuOptionList.push({
                id: a,
                title: $scope.menuOptions[a],
                icon: $scope.menuOptionsIcons[a]
            });
        }

        //... Select Page To Display ...
        $rootScope.pageAnimDir = 'up';
        $rootScope.curPage = 0;
        $rootScope.selectedPage = 0;
        $rootScope.dispCurPageTitle = true;
        $scope.getShared = '0';
        //... Set Page Transition Variables ...
        $scope.selectDispPage = function(selID){
            $rootScope.loading = true;
            if(selID.includes('Shared')){
                $scope.getShared = '1';
            }else{
                $scope.getShared = '0';
            }
            getShopList();
            $timeout(function(){
                $timeout(function(){
                    $scope.showActivePage = selID;
                    $rootScope.loading = false;
                },200);
            },100);
        };


        /************************************************ Uploads *****************************************************/
        let shopAct = "get";
        var uplAct = 'add';
        $scope.uplAct = 'add';
        $scope.fullUplList = [];
        getShopList();
        //... Get Upload List from DB ...
        function getShopList(){
            $scope.dispUplList = [];
            $rootScope.loading = true;
            $timeout(function(){
                $http({
                    url: 'Services/manageUploads.php',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: 'act='+shopAct+'&share='+$scope.getShared
                }).then(function(response) {
                        $scope.fullUplList = [];
                        if(response.data[0].status.includes('Yes')){
                            for(let a = 0; a < response.data.length; a++){
                                let tmpImg = null;
                                if(response.data[a].dbId !== null){
                                    tmpImg = "Uploads/"+response.data[a].fileName;
                                }
                                $scope.fullUplList.push({
                                    listID: a,
                                    dbId: response.data[a].dbId,
                                    user: response.data[a].user,
                                    uplDate: response.data[a].uplDate,
                                    fileType: response.data[a].fileType,
                                    logo: tmpImg,
                                    location: response.data[a].location,
                                    tags: response.data[a].tags,
                                    captDate: response.data[a].captDate,
                                    captBy: response.data[a].captBy,
                                    ttl: response.data[a].ttl,
                                    descr: response.data[a].descr,
                                    shr: response.data[a].shr,
                                    shrWith: response.data[a].shrWith,
                                    fileName: response.data[a].fileName
                                });
                            }
                            $scope.dispUplList = $scope.fullUplList;
                            $rootScope.loading = false;
                        }
                        else {
                            $rootScope.loading = false;
                        }
                    },
                    function(){
                        console.warn("Connection Error - Get Upload List");
                        $rootScope.loading = false;
                    }
                );
            },500);
        }


        //... Populate shop list ...
        function shopListToDisp(filter){

        }

        //... Toggle Add Shop Popup ...
        $scope.listSelectedItem = '';
        $scope.showShopPop = false;
        $scope.togShopPopup = function(popupFunc, index){
            uplAct = popupFunc;
            $scope.uplAct = popupFunc;
            $scope.showShopPop = !$scope.showShopPop;
            if(popupFunc === "add"){
                $scope.showImgCrop = false;
                $scope.shopDetsChanged = false;
                $scope.uploadDetsTtl = "New Upload";
                setSelectedShopItem(index);
                $scope.showShopEdit = !$scope.showShopEdit;
                if($scope.showShopEdit){
                    $timeout(function(){
                        shopEditArray[0] = null;
                        shopEditArray[1] = $scope.editClass = 0;
                        shopEditArray[2] = document.getElementById('uplTtl').value = "";
                        shopEditArray[3] = document.getElementById('uplDesc').value = "";
                        shopEditArray[4] = document.getElementById('uplTags').value = "";
                        shopEditArray[5] = document.getElementById('uplGeo').value = "";
                        shopEditArray[6] = document.getElementById('uplCapDate').value = "";
                        shopEditArray[7] = document.getElementById('uplCapBy').value = "";
                        shopEditArray[8] = document.getElementById('uplShareWith').value = "";
                    },200);
                }
            } else if(popupFunc === "edit"){
                $scope.showImgCrop = false;
                $scope.shopDetsChanged = false;
                $scope.uploadDetsTtl = "Edit Details";
                setSelectedShopItem(index);
                $scope.showShopEdit = !$scope.showShopEdit;
                if($scope.showShopEdit){
                    $timeout(function(){
                        shopEditArray[0] = $scope.activShopListItem.dbId;
                        shopEditArray[1] = $scope.editClass = $scope.activShopListItem.shr;
                        shopEditArray[2] = document.getElementById('uplTtl').value = $scope.activShopListItem.ttl;
                        shopEditArray[3] = document.getElementById('uplDesc').value = $scope.activShopListItem.descr;
                        shopEditArray[4] = document.getElementById('uplTags').value = $scope.activShopListItem.tags;
                        shopEditArray[5] = document.getElementById('uplGeo').value = $scope.activShopListItem.location;
                        shopEditArray[6] = document.getElementById('uplCapDate').value = $scope.activShopListItem.captDate;
                        shopEditArray[7] = document.getElementById('uplCapBy').value = $scope.activShopListItem.captBy;
                        shopEditArray[8] = document.getElementById('uplShareWith').value = $scope.activShopListItem.shrWith;
                    },200);
                }
            }else if(popupFunc === "del"){
                setSelectedShopItem(index);
                $scope.showShopDel = !$scope.showShopDel;
            }
        };

        //... Toggle Shop class (Prim / Sec) ...
        $scope.selClass = 'prim';
        $scope.togShopClass = function(){
            if($scope.selClass === 'prim'){
                $scope.selClass = 'sec';
            }else{
                $scope.selClass = 'prim';
            }
        };
        //... Toggle Upload Edit class (Prim / Sec) ...
        $scope.editClass = 0;
        $scope.togShopEditClass = function(){
            if($scope.editClass === 0){
                $scope.editClass = 1;
            }else{
                $scope.editClass = 0;
            }
            shopEditArray[1] = $scope.editClass;
            onShopDetsChange();
        };

        //... New Shop Name ...
        let newShopName = null;
        $scope.changeNewShopName = function(txt){
            newShopName = txt;
        };
        //... New Shop Branch ...
        let newShopBranch = null;
        $scope.changeNewShopBranch = function(txt){
            newShopBranch = txt;
        };

        //... Add New Shop to DB ...
        $scope.addNewShop = function(){
            if(newShopName.length > 0 && newShopBranch.length > 0){
                let action = 'add';
                $http({
                    url: 'Services/manageUploads.php',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: 'act='+action+'&name='+newShopName+'&class='+$scope.selClass+'&branch='+newShopBranch
                }).then(function(responseOne){
                    console.log(responseOne.data[0].status);
                    if(responseOne.data[0].status.includes('Yes')){
                        getShopList();
                        $scope.togShopPopup('add');
                        $rootScope.loading = false;
                    }
                },
                    function(){
                        console.error("Connection Error - Add New Shop");
                        $rootScope.loading = false;
                    }
                );
            }
        };

        //... Set index of selected list item to Edit / Remove ...
        $scope.activShopListItem = [];
        let actShopSelIndex = null;
        function setSelectedShopItem(index){
            if(index !== ''){
                actShopSelIndex = index;
                $scope.activShopListItem = $scope.dispUplList[index];
                $scope.listSelectedItem = 'Upl-' + $scope.activShopListItem.dbId;
                $scope.dispCurImg = "Uploads/"+$scope.activShopListItem.fileName;
            }else{
                $scope.listSelectedItem = 'Upl-';
            }
        }

        //... Delete Shop Item Confirmation click ...
        $scope.acceptShopDelete = function(){
            $rootScope.loading = true;
            let action = 'delete';
            $http({
                url: 'Services/manageUploads.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'act='+action+'&id='+$scope.activShopListItem.dbId+'&file='+$scope.activShopListItem.fileName
            }).then(function(responseOne){
                    console.log(responseOne.data[0].status);
                    if(responseOne.data[0].status.includes('Yes')){
                        //$scope.dispUplList.splice(actShopSelIndex, 1);
                        getShopList();
                        actShopSelIndex = null;
                        $scope.togShopPopup('del', '');
                        $rootScope.loading = false;
                    }
                },
                function(){
                    console.error("Connection Error - Delete Shop");
                    $rootScope.loading = false;
                }
            );
        };

        //....................................... Upload Image .......................................
        $scope.myImage = '';
        $scope.myCroppedImage = '';
        $scope.dispCurImg = '';
        $scope.dispNewImg = '';
        $scope.userImage = $scope.compImg = '';
        $rootScope.shopImgChanged = function(evt){
            let file = evt.files[0];
            let reader = new FileReader();
            reader.onload = function (evt) {
                $scope.$apply(function($scope){
                    $scope.myImage = evt.target.result;
                });
            };
            reader.readAsDataURL(file);
            $scope.showImgCrop = true;

        };
        //....................................... Close Img Crop .......................................
        $scope.closeImgCrop = CloseImgCrop;
        function CloseImgCrop(){
            $scope.showImgCrop = false;
        }
        //....................................... Set Crop Change .......................................
        $scope.setCroppedResult = function(img){
            $scope.dispNewImg = img;
        };
        //....................................... Save Profile Image .......................................
        $scope.saveImgCrop = SaveImgCrop;
        function SaveImgCrop(){
            $rootScope.loading = true;
            let file = $scope.dispNewImg;
            file = dataURItoBlob(file, 'image/jpeg');
            let uploadUrl = "./Services/uploadImage.php";
            let promise = imageUploadService.uploadImageToUrl(file, uploadUrl, $scope.listSelectedItem);

            promise.then(function (response) {
                if((response.data === 'Image Upload Successful!')){
                    console.warn(response.data);
                    if($scope.listSelectedItem === 'User Profile'){
                        $scope.dispCurImg = $scope.userImage = '';
                        $scope.dispCurImg = $scope.userImage = $scope.dispNewImg;
                    }else{
                        $scope.dispCurImg = $scope.compImg = '';
                        $scope.dispCurImg = $scope.compImg = $scope.dispNewImg;
                    }
                    CloseImgCrop();
                    $scope.togShopPopup('add','');
                    getShopList();
                }else{
                    alert('Image Uplaod Error.');
                    console.error(response.data);
                }
                $rootScope.loading = false;
            }, function () {
                $rootScope.loading = false;
            });
        }
        //....................................... Convert ImageURI to Blob .......................................
        function dataURItoBlob(dataURI, type) {
            // convert base64 to raw binary data held in a string
            let byteString = atob(dataURI.split(',')[1]);

            // separate out the mime component
            let mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

            // write the bytes of the string to an ArrayBuffer
            let ab = new ArrayBuffer(byteString.length);
            let ia = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }

            // write the ArrayBuffer to a blob, and you're done
            let bb = new Blob([ab], { type: type });
            return bb;
        }

        //....................................... Edit Upload details .......................................
        $scope.shopDetsChanged = false;
        let uplTtl = null,
            uplDesc = null,
            uplTags = null,
            uplGeo = null,
            uplCapDate = null,
            uplCapBy = null,
            uplShareWith = null;
        let shopEditArray = [null,null,1,null,null,null,null,null,null,null];

        //... Trigger upload details changed ...
        function onShopDetsChange(){
            $scope.shopDetsChanged = true;
        }

        //... Change Edit upload Details ...
        $scope.changeEditShopDets = function(id, data){
            switch(id){
                case 'ttl':
                    uplTtl = data;
                    shopEditArray[2] = data;
                    break;
                case 'descr':
                    uplDesc = data;
                    shopEditArray[3] = data;
                    break;
                case 'tags':
                    uplTags = data;
                    shopEditArray[4] = data;
                    break;
                case 'geoloc':
                    uplGeo = data;
                    shopEditArray[5] = data;
                    break;
                case 'capDate':
                    uplCapDate = data;
                    shopEditArray[6] = data;
                    break;
                case 'capBy':
                    uplCapBy = data;
                    shopEditArray[7] = data;
                    break;
                case 'shareWith':
                    uplShareWith = data;
                    shopEditArray[8] = data;
                    break;
            }
            onShopDetsChange();
        };

        //... Save Shop Edit ...
        $scope.saveEditShop = function(){
            if($scope.shopDetsChanged){
                $rootScope.loading = true;
                $http({
                    url: 'Services/manageUploads.php',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: 'act='+uplAct+'&data='+shopEditArray
                }).then(function(responseOne){
                        console.log(responseOne.data[0].status);
                        if(responseOne.data[0].status.includes('Yes')){
                            getShopList();
                            actShopSelIndex = null;
                            $scope.togShopPopup('edit', '');
                            $rootScope.loading = false;
                        }
                    },
                    function(){
                        console.error("Connection Error - Edit Shop");
                        $rootScope.loading = false;
                    }
                );
            }
        };


    }]); //End Controller ...
})();