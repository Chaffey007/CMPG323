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
                                $rootScope.code = getResponse.data.userCode;
                                $rootScope.profpic = getResponse.data.profPic;
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

        //... Clear Error Messge ...
        $scope.clearError = function(){
            $scope.logErrorMsg = "";
        };


    });

    /** ***********************************************************************************************************/
    /************************************************* Main Controller ************************************************************/
    /** ***********************************************************************************************************/
    myApp.controller('mainController', ['$scope', '$rootScope', '$timeout', '$log', '$http', 'imageUploadService', '$location', 'GlobeVars', 'scrollPageService', function($scope, $rootScope, $timeout, $log, $http, imageUploadService, $location, GlobeVars, scrollPageService){

        $scope.showActivePage = 'Home';
        $rootScope.pageTitle = 'System Management';
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
        $scope.menuOptions = ['Home', 'Planning', 'Stock','Banking', 'Accounts', 'Employees', 'Shops'];
        $scope.menuOptionsIcons = ['home', 'schedule','equalizer','attach_money', 'account_balance', 'group', 'store'];
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
        //... Set Page Transition Variables ...
        $scope.selectDispPage = function(selID){
            $rootScope.loading = true;

            $timeout(function(){
                if($scope.showActivePage === 'Banking'){
                    $scope.bankBookDisp = [];
                }
                if(selID === 'Banking'){
                    ResetTimeFrame();
                    $scope.showTFrame = false;
                }
                $timeout(function(){
                    $scope.showActivePage = selID;
                    $rootScope.loading = false;
                },200);
            },100);
        };


        /************************************************ User Data *****************************************************/
        getUserData();
        $scope.fullUserList = [];
        function getUserData(){
            let action = 'get';
            $http({
                url: 'Services/manageUsers.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'act='+action+'&date='+$scope.dispCurDate
            }).then(function(responseOne){
                //... User List ...
                if(responseOne.data[0][0].status.includes('Yes')){
                    for(let a = 0; a < responseOne.data[0].length; a++){
                        $scope.fullUserList.push({
                            listID: a,
                            dbID: responseOne.data[0][a].dbID,
                            fName: responseOne.data[0][a].fName,
                            lName: responseOne.data[0][a].lName,
                            uName: responseOne.data[0][a].uName,
                            alias: responseOne.data[0][a].alias,
                            pass: responseOne.data[0][a].pass,
                            priv: responseOne.data[0][a].priv,
                            appDate: responseOne.data[0][a].appDate,
                            mail: responseOne.data[0][a].mail,
                            num: responseOne.data[0][a].num,
                            code: responseOne.data[0][a].code,
                            idNum: responseOne.data[0][a].idNum,
                            bankAcc: responseOne.data[0][a].bankAcc,
                            bankBran: responseOne.data[0][a].bankBran,
                            bankNme: responseOne.data[0][a].bankNme,
                            profPic: responseOne.data[0][a].profPic,
                            created: responseOne.data[0][a].created,
                            edited: responseOne.data[0][a].edited,
                            editBy: responseOne.data[0][a].editBy
                        });
                    }
                }else{
                    $scope.fullUserList = [];
                }

                $rootScope.loading = false;
            });
        }

        /************************************************ Banking *****************************************************/
        $scope.shipDateSel = 'start';
        $scope.showEntryDetsPop = false;
        $scope.showBankFilterPop = false;
        $scope.bankAutosugList = [];
        let tstBankAutosugList = [];
        $scope.bankBookFull = [];
        //... Get data for banking ...
        $scope.loadBankingData = getBankingData;
        //getBankingData();
        function getBankingData(){
            $scope.bankBookFull = [];
            $scope.fullUserList = [];
            $rootScope.loading = true;
            let action = 'get';
            $http({
                url: 'Services/manageBanking.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'act='+action+'&date='+$scope.dispCurDate
            }).then(function(responseOne){
                //... Opening Balance ...
                if(responseOne.data[2][0].status.includes('Yes')){
                    console.log(responseOne.data[2][0].status);
                    $scope.openBal = responseOne.data[2][0].val;
                }else{
                    console.log(responseOne.data[2][0].status);
                    $scope.openBal = '0.00';
                }

                //... User List ...
                if(responseOne.data[3][0].status.includes('Yes')){
                    for(let a = 0; a < responseOne.data[3].length; a++){
                        $scope.fullUserList.push({
                            listID: a,
                            dbID: responseOne.data[3][a].dbID,
                            fName: responseOne.data[3][a].fName,
                            lName: responseOne.data[3][a].lName,
                            uName: responseOne.data[3][a].uName,
                            pass: responseOne.data[3][a].pass,
                            priv: responseOne.data[3][a].priv,
                            appDate: responseOne.data[3][a].appDate,
                            mail: responseOne.data[3][a].mail,
                            num: responseOne.data[3][a].num,
                            code: responseOne.data[3][a].code,
                            idNum: responseOne.data[3][a].idNum,
                            bankAcc: responseOne.data[3][a].bankAcc,
                            bankBran: responseOne.data[3][a].bankBran,
                            bankNme: responseOne.data[3][a].bankNme,
                            profPic: responseOne.data[3][a].profPic
                        });
                    }
                }else{
                    $scope.fullUserList = [];
                }

                //... Full Banking List ...
                if(responseOne.data[0][0].status.includes('Yes')){
                    for(let a = 0; a < responseOne.data[0].length; a++){
                        let tmpUser = '';
                        for(let b = 0; b < $scope.fullUserList.length; b++){
                            if(responseOne.data[0][a].user === $scope.fullUserList[b].code){
                                tmpUser = $scope.fullUserList[b].fName + " " + $scope.fullUserList[b].lName;
                            }
                        }
                        if(responseOne.data[0][a].type !== 'Balance'){
                            $scope.bankBookFull.push({
                                listID: a,
                                dbId: responseOne.data[0][a].dbId,
                                entryDate: responseOne.data[0][a].entryDate,
                                type: responseOne.data[0][a].type,
                                stat: responseOne.data[0][a].stat,
                                descript: responseOne.data[0][a].descript,
                                val: responseOne.data[0][a].val,
                                user: tmpUser, //responseOne.data[0][a].user,
                                note: responseOne.data[0][a].note,
                                editDate: responseOne.data[0][a].editDate
                            });
                        }
                    }
                }
                bankBookPopulate($scope.dispCurDate);
                //... Description options ...
                if(responseOne.data[1][0].status.includes('Yes')){
                    for(let a = 0; a < responseOne.data[1].length; a++){
                        let test = 0;
                        //... Remove double entries ...
                        for(let b = 0; b < tstBankAutosugList.length; b++){
                            if(tstBankAutosugList[b].title === responseOne.data[1][a].descript){
                                test++;
                            }
                        }
                        if(test === 0){
                            tstBankAutosugList.push({
                                listID: a,
                                title: responseOne.data[1][a].descript
                            });
                        }
                    }
                    //... Sort ...
                    tstBankAutosugList.sort(SortByTitle);

                    //... Insert Entry ...
                    bankNewDescriptAutoPopulate('all');
                }
                $rootScope.loading = false;
            });
        }

        //... Sort alphabetically ...
        function SortByTitle(x,y) {
            return ((x.title === y.title) ? 0 : ((x.title > y.title) ? 1 : -1 ));
        }
        function SortByDescription(x,y) {
            return ((x.descript === y.descript) ? 0 : ((x.descript > y.descript) ? 1 : -1 ));
        }
        function SortByDate(x,y) {
            return ((x.entryDate === y.entryDate) ? 0 : ((x.entryDate > y.entryDate) ? 1 : -1 ));
        }
        function SortByEntry(x,y) {
            return ((x.editDate === y.editDate) ? 0 : ((x.editDate > y.editDate) ? 1 : -1 ));
        }
        function SortByInEx(){
            let tmpFullDisp = [];
            tmpFullDisp = $scope.bankBookDisp;
            let tmpInDisp = [];
            let tmpExDisp = [];
            let tmpInTot = 0.00;
            let tmpExTot = 0.00;
            for(let a = 0; a < tmpFullDisp.length; a++){
                if(tmpFullDisp[a].type === 'income'){
                    tmpInDisp.push(tmpFullDisp[a]);
                }else if(tmpFullDisp[a].type === 'expenses'){
                    tmpExDisp.push(tmpFullDisp[a]);
                }
            }
            for(let a = 0; a < tmpInDisp.length; a++){
                tmpInTot += parseFloat(tmpInDisp[a].val);
            }
            for(let a = 0; a < tmpExDisp.length; a++){
                tmpExTot += parseFloat(tmpExDisp[a].val);
            }
            tmpInTot = tmpInTot.toFixed(2);
            tmpExTot = tmpExTot.toFixed(2);

            $scope.bankBookDisp = [];
            let tmpcnt = 0;
            for(let a = 0; a < tmpInDisp.length; a++){
                $scope.bankBookDisp.push(tmpInDisp[a]);
                tmpcnt++;
            }
            $scope.bankBookDisp.push({
                listID: 10000000000,
                dbId: 'none',
                entryDate: '',
                type: 'income',
                stat: 'Valid',
                descript: 'Sub Total',
                val: tmpInTot,
                user: 'System',
                note: 'Temporary display for all income transactions of this date.',
                editDate: ''
            });
            for(let a = 0; a < tmpExDisp.length; a++){
                $scope.bankBookDisp.push(tmpExDisp[a]);
                tmpcnt++;
            }
            $scope.bankBookDisp.push({
                listID: 10000000001,
                dbId: 'none',
                entryDate: '',
                type: 'expenses',
                stat: 'Valid',
                descript: 'Sub Total',
                val: tmpExTot,
                user: 'System',
                note: 'Temporary display for all expense transactions of this date.',
                editDate: ''
            });
        }

        //... Populate Banking Description Autosuggest ...
        function bankNewDescriptAutoPopulate(txt){
            $scope.bankAutosugList = [];
            if(txt === 'all'){
                for(let a = 0; a < tstBankAutosugList.length; a++){
                    $scope.bankAutosugList.push(tstBankAutosugList[a]);
                }
            }else{
                //... Compare to entered text (all lowercase) ...
                for(let a = 0; a < tstBankAutosugList.length; a++){
                    if(tstBankAutosugList[a].title.toLowerCase().includes(txt.toLowerCase())){
                        $scope.bankAutosugList.push(tstBankAutosugList[a]);
                    }
                }
            }
        }



        //... Show or Hide new entry ...
        $scope.checkUserPriv = function(currentPriv, reqPriv){
            return (currentPriv >= reqPriv);
        };

        $scope.newEntryType = 'income';
        //... Toggle banking Debit / Credit entry ...
        $scope.togBankNewEntryType = TogBankNewEntryType;
        function TogBankNewEntryType(){
            if($scope.newEntryType === 'income'){
                $scope.newEntryType = 'expenses'
            }else{
                $scope.newEntryType = 'income'
            }
        }

        //... Change Description for new entry ...
        let newBankDescript = '';
        $scope.changeNewBankDescript = function(txt){
            newBankDescript = txt;
            $timeout(function(){
                bankNewDescriptAutoPopulate(txt);
            },200);
        };
        //... Select Description from list ...
        $scope.selectNewDescr = function(sel){
            document.getElementById('bankNewDescript').value = sel;
            newBankDescript = sel;
        };
        //... Change Value for new entry ...
        let newBankVal = '';
        $scope.changeNewBankVal = function(txt){
            newBankVal = txt;
        };

        //... Add new bank entry ...
        $scope.addNewBankEntry = AddNewBankEntry;
        function AddNewBankEntry(){
            if((newBankDescript !== '') && (newBankVal !== '')){
                $rootScope.loading = true;
                let action = 'add';

                //... Calc new Balance ...
                let tmpCurBalance = 0.00;
                for(let a = 0; a < $scope.bankBookDisp.length; a++){
                    if($scope.bankBookDisp[a].type === "income"){
                        tmpCurBalance += parseFloat($scope.bankBookDisp[a].val);
                    }else{
                        tmpCurBalance -= parseFloat($scope.bankBookDisp[a].val);
                    }
                }
                if($scope.newEntryType === 'income'){
                    tmpCurBalance += parseFloat(newBankVal);
                }else{
                    tmpCurBalance -= parseFloat(newBankVal);
                }
                tmpCurBalance = parseFloat(tmpCurBalance).toFixed(2);

                //... Add to DB ...
                newBankVal = parseFloat(newBankVal).toFixed(2);
                $http({
                    url: 'Services/manageBanking.php',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: 'act='+action+'&descript='+newBankDescript+'&type='+$scope.newEntryType+'&date='+$scope.dispCurDate+'&val='+newBankVal+'&bal='+tmpCurBalance
                }).then(function(responseOne){
                    console.log(responseOne.data[0].status);
                    if(responseOne.data[0].status.includes('Yes')){
                        newBankDescript = newBankVal = '';
                        document.getElementById('bankNewDescript').value = "";
                        document.getElementById('bankNewVal').value = "";
                        $rootScope.loading = false;
                        getBankingData();
                    }
                });
            }
        }


        //............. BOOK .............
        //... Header ...
        $scope.bankHeaders = [];
        let bankHeadTitles = ["i","Date","Description","Income","Expenses"];
        let bankColSize = ["one","two","three","two","two"];
        for(let a = 0; a < bankHeadTitles.length; a++){
            $scope.bankHeaders.push({
                title: bankHeadTitles[a],
                class: bankColSize[a]
            });
        }

        //... Populate Banking Book ...
        $scope.bankBookDisp = [];
        $scope.curBankBalance = 0.00;
        function bankBookPopulate(date){
            $scope.curBankBalance = 0.00;
            $scope.bankBookDisp = [];
            let tmp = 0;
            for(let a = 0; a < $scope.bankBookFull.length; a++){
                if(($scope.bankBookFull[a].entryDate.includes(date)) && ($scope.bankBookFull[a].stat === 'Valid') && ($scope.bankBookFull[a].type !== 'Balance')){
                    $scope.bankBookDisp.push({
                        listID: tmp,
                        dbId: $scope.bankBookFull[a].dbId,
                        entryDate: $scope.bankBookFull[a].entryDate,
                        type: $scope.bankBookFull[a].type,
                        stat: $scope.bankBookFull[a].stat,
                        descript: $scope.bankBookFull[a].descript,
                        val: $scope.bankBookFull[a].val,
                        user: $scope.bankBookFull[a].user,
                        note: $scope.bankBookFull[a].note,
                        editDate: $scope.bankBookFull[a].editDate
                    });
                    tmp++;
                    if($scope.bankBookFull[a].type === "income"){
                        $scope.curBankBalance += parseFloat($scope.bankBookFull[a].val);
                    }else{
                        $scope.curBankBalance -= parseFloat($scope.bankBookFull[a].val);
                    }
                }
            }
            $scope.curBankBalance += parseFloat($scope.openBal);
            $scope.curBankBalance = $scope.curBankBalance.toFixed(2);
        }

        //... Toggle Banking Sort ...
        $scope.sortBankingAlpha = sortBankBook;
        function sortBankBook(){
            $scope.showBankFilterPop = !$scope.showBankFilterPop;

        }
        //... Sort by selection ...
        $scope.bankSortBy = function(sel){
            $rootScope.loading = true;
            //... Remove shipment from the list ...
            for(let r = 0; r < $scope.bankBookDisp.length; r++){
                if($scope.bankBookDisp[r].descript === 'Sub Total'){

                }
            }
            if(sel === 'Date'){
                $scope.bankBookDisp.sort(SortByDate);
            }else if(sel === 'Descript'){
                $scope.bankBookDisp.sort(SortByDescription);
            }else if(sel === 'Entry'){
                $scope.bankBookDisp.sort(SortByEntry);
            }else if(sel === 'InEx'){
                SortByInEx();
            }
            $rootScope.loading = false;
            $scope.showBankFilterPop = false;
        };

        //... Toggle Entry Details Popup ...
        $scope.detsDescr = $scope.detsDate = $scope.detsEdit = $scope.detsType = $scope.detsStat = '';
        $scope.detsVal = $scope.detsUser = $scope.detsNote = $scope.detsDbId = '';
        $scope.togEntryDetsPop = function(dbID){
            $scope.detsDescr = $scope.detsDate = $scope.detsEdit = $scope.detsType = $scope.detsStat = '';
            $scope.detsVal = $scope.detsUser = $scope.detsNote = '';

            $scope.showEntryDetsPop = !$scope.showEntryDetsPop;

            $scope.detsDbId = $scope.bankBookDisp[dbID].dbId;
            $scope.detsDescr = $scope.bankBookDisp[dbID].descript;
            $scope.detsDate = $scope.bankBookDisp[dbID].entryDate;
            $scope.detsEdit = $scope.bankBookDisp[dbID].editDate;
            $scope.detsType = $scope.bankBookDisp[dbID].type;
            $scope.detsStat = $scope.bankBookDisp[dbID].stat;
            $scope.detsVal = $scope.bankBookDisp[dbID].val;
            $scope.detsUser = $scope.bankBookDisp[dbID].user;
            $scope.detsNote = $scope.bankBookDisp[dbID].note;
        };
        //... Close Entry Details Popup ...
        $scope.closeEntryDetsPop = CloseEntryDetsPop;
        function CloseEntryDetsPop(){
            $scope.showEntryDetsPop = false;
        }

        //... Recalculate Current Book and Update balance ...
        $scope.recalcCurrent = RecalcCurrent;
        function RecalcCurrent(){
            $scope.curBankBalance = 0.00;
            for(let a = 0; a < $scope.bankBookDisp.length; a++){
                if($scope.bankBookDisp[a].type === "income"){
                    $scope.curBankBalance += parseFloat($scope.bankBookDisp[a].val);
                }else{
                    $scope.curBankBalance -= parseFloat($scope.bankBookDisp[a].val);
                }
            }
            $scope.curBankBalance += parseFloat($scope.openBal);
            $scope.curBankBalance = $scope.curBankBalance.toFixed(2);

            /************************ **************************/
            let action = 'balUpdate';
            $http({
                url: 'Services/manageBanking.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'act='+action+'&date='+$scope.dispCurDate+'&val='+$scope.curBankBalance
            }).then(function(responseOne){
                console.log(responseOne.data[0].status);
                if(responseOne.data[0].status.includes('Yes')){
                    newBankDescript = newBankVal = '';
                    $rootScope.loading = false;
                    getBankingData();
                }
            });
            /************************ **************************/
        }

        let preSearchedBook = [];
        //... Toggle book search bar ...
        $scope.togBookSearchPop = TogBookSearchPop;
        function TogBookSearchPop(){
            $rootScope.loading = true;

            if(!$scope.showBankSearchPop){
                document.getElementById('bankNewSearch').value = '';
                $scope.bankNewSearch = '';
                preSearchedBook = $scope.bankBookDisp;
                $scope.bankBookDisp = [];
            } else if($scope.showBankSearchPop){
                $scope.bankBookDisp = [];
                $scope.bankBookDisp = preSearchedBook;
            }

            $scope.showBankSearchPop = !$scope.showBankSearchPop;
            $rootScope.loading = false;
        }

        //... Search in current book display ...
        let counter = 0;
        $scope.changeNewBankSearch = function(txt){
            if(counter === 0){
                $scope.bankBookDisp = [];
                //... Compare to entered text (all lowercase) ...
                for(let a = 0; a < preSearchedBook.length; a++){
                    if(preSearchedBook[a].descript.toLowerCase().includes(txt.toLowerCase())){
                        $scope.bankBookDisp.push(preSearchedBook[a]);
                    }
                    counter++;
                }
                counter = 0;
            }
        };

        //... Toggle book Export Popup ...
        $scope.togBankExportPop = TogBankExportPop;
        function TogBankExportPop(){
            // Prepare Excel data:
            $scope.fileName = "Banking_" + $scope.dispCurDate;
            $scope.exportData = [];
            // Headers:
            $scope.exportData.push(["Banking"]);
            $scope.exportData.push(["Date", "Description", "Income", "Expenses"]);
            $scope.exportData.push([""]);
            $scope.exportData.push(["", "Opening Balance", "R " + $scope.openBal, ""]);
            $scope.exportData.push([""]);
            // Data:
            angular.forEach($scope.bankBookDisp, function(value, key){
                if(value.type === 'income'){
                    $scope.exportData.push([value.entryDate, value.descript, value.val, ""]);
                }else if(value.type === 'expenses'){
                    //let tmpVal = "-" + value.val;
                    $scope.exportData.push([value.entryDate, value.descript, "", value.val]);
                }
            });
            $scope.exportData.push([""]);
            $scope.exportData.push(["", "Closing Balance", "R " + $scope.curBankBalance, ""]);
            $scope.showExportSelection = !$scope.showExportSelection;
        }

        let focusCnt = 0;
        //... Auto close bank export popup ...
        $scope.onFocusBankExportPop = function(sel){
            if(sel === 'enter'){
                focusCnt = 0;
                $scope.showExportSelection = true;
            }else{
                focusCnt++;
                $timeout(function(){
                    if(focusCnt !== 0){
                        $scope.showExportSelection = false;
                    }
                },500);
            }
        };

        let focusCntOne = 0;
        //... Auto close bank Sort By popup ...
        $scope.onFocusSortPop = function(sel){
            if(sel === 'enter'){
                focusCntOne = 0;
                $scope.showBankFilterPop = true;
            }else{
                focusCntOne++;
                $timeout(function(){
                    if(focusCntOne !== 0){
                        $scope.showBankFilterPop = false;
                    }
                },500);
            }
        };

        //... Invalidate current bank Entry ...
        $scope.deleteBankEntry = function(selID){
            let action = 'invalidate';
            $http({
                url: 'Services/manageBanking.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'act='+action+'&id='+selID
            }).then(function(responseOne){
                console.log(responseOne.data[0].status);
                if(responseOne.data[0].status.includes('Yes')){
                    newBankDescript = newBankVal = '';
                    $rootScope.loading = false;
                    $scope.showEntryDetsPop = false;
                    getBankingData();
                    $timeout(function(){
                        RecalcCurrent();
                    },500);
                }
            });
        };


        /************************************************ Datepicker *****************************************************/
        $scope.togDatePicker = TogDatePicker;
        function TogDatePicker(){
            $scope.showTFrame = !$scope.showTFrame;
            if($scope.showTFrame){
                $scope.shipDateSel = 'start';
            }
        }
        $scope.closeDatePicker = CloseDatePicker;
        function CloseDatePicker(){
            $scope.showTFrame = false;
        }
        /** Time Frame **/
        $scope.shipStartDay = $scope.shipStartDate = $scope.shipEndDay = $scope.shipEndDate = null;
        //... Initiate dates if saved ...

        //... Select Start Date / End Date ...
        $scope.shipDateSelChange = function(selID){
            $scope.shipDateSel = selID;
        };

        //... Save selected Dates ...
        $scope.saveShipDates = SaveShipDates;
        function SaveShipDates(){
            if($scope.shipStartDay !== null && $scope.shipStartDay !== undefined){
                if($scope.showBankSearchPop){
                    TogBookSearchPop();
                }
                let tmpDay = $scope.shipStartDay + " " + monthNames[$scope.shipStartDate.getMonth()] + " " + $scope.shipStartDate.getFullYear();
                let tmpMonth = monthNames[$scope.shipStartDate.getMonth()] + " " + $scope.shipStartDate.getFullYear();
                if($scope.shipDateSel === 'start'){
                    $scope.dispCurDate = tmpDay;
                }else{
                    $scope.dispCurDate = tmpMonth;
                }
                $scope.shipStartDay = $scope.shipStartDate = $scope.shipEndDay = $scope.shipEndDate = null;
                getBankingData();
                TogDatePicker();
            }
        }


        //... Calender ...
        $scope.shipTimeWeek = [
            {
                letter: 'Su'
            },
            {
                letter: 'Mo'
            },
            {
                letter: 'Tu'
            },
            {
                letter: 'We'
            },
            {
                letter: 'Th'
            },
            {
                letter: 'Fr'
            },
            {
                letter: 'Sa'
            }
        ];

        LoadNewMonth(selectedDate);

        function LoadNewMonth(dispMonth){

            $scope.weekOne = [];
            $scope.weekTwo = [];
            $scope.weekThree = [];
            $scope.weekFour = [];
            $scope.weekFive = [];
            $scope.weekSix = [];

            $timeout(function(){
                $scope.shipshowDays = true;
                selectedDate = new Date(dispMonth);
                month = selectedDate.getMonth();
                year = selectedDate.getFullYear();
                let day = selectedDate.getDate();

                let firstDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);    //... First day of display month ...
                let lastDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1, 1 - 1); //... Last day of disp month ...
                let testDay = firstDay;                                                                 //... Tester to insert incrementally into array ...
                let testDayW = (testDay.getDay());                                                  //... Get first day of week ...
                let weekDayTest = 0;                                                                    //... Start of week (Monday) ...
                let insDay = 0;

                //alert(firstDay + "\r\n" + (firstDay.getDate()) + "\r\n" + lastDay + "\r\n" + (lastDay.getDate()) + "\r\n" + testDay + "\r\n" + testDayW + "\r\n");
                for(let i = (firstDay.getDate() - 1); insDay < (lastDay.getDate()); i++){
                    if(testDayW === weekDayTest){
                        if(i < 7){
                            $scope.weekOne.push({name: (insDay + 1)});
                            insDay++;
                            weekDayTest++;
                            testDayW++;
                        }
                        if((i >= 7) && (i < 14)){
                            $scope.weekTwo.push({name: (insDay + 1)});
                            insDay++;
                            weekDayTest++;
                            testDayW++;
                        }
                        if((i >= 14) && (i < 21)){
                            $scope.weekThree.push({name: (insDay + 1)});
                            insDay++;
                            weekDayTest++;
                            testDayW++;
                        }
                        if((i >= 21) && (i < 28)){
                            $scope.weekFour.push({name: (insDay + 1)});
                            insDay++;
                            weekDayTest++;
                            testDayW++;
                        }
                        if((i >= 28) && (i < 35)){
                            $scope.weekFive.push({name: (insDay + 1)});
                            insDay++;
                            weekDayTest++;
                            testDayW++;
                        }
                        if((i >= 35) && (i < 42)){
                            $scope.weekSix.push({name: (insDay + 1)});
                            insDay++;
                            weekDayTest++;
                            testDayW++;
                        }
                    }
                    else{
                        if(i <= 7){
                            $scope.weekOne.push({name: null});
                            weekDayTest++;
                        }
                    }
                }
            }, 350);
        }

        //... Previous Month ...
        $scope.prevMonth = PrevMonth;
        function PrevMonth(){
            $scope.shipshowDays = false;
            if(month === 0){
                month = 11;
                year --;
            }
            else{
                month --;
            }
            let newMonth = new Date(year, month, 1);
            LoadNewMonth(newMonth);
            $timeout(function(){
                $scope.shipMonthShow = newMonth;
            }, 350)
        }
        //... Next Month ...
        $scope.nextMonth = NextMonth;
        function NextMonth(){
            $scope.shipshowDays = false;
            if(month === 11){
                month = 0;
                year ++;
            }
            else{
                month ++;
            }
            let newMonth = new Date(year, month, 1);
            LoadNewMonth(newMonth);
            $timeout(function(){
                $scope.shipMonthShow = newMonth;
            }, 350)
        }
        //... Select Date ...
        let startDayToCompare;
        let startDateToCompare;
        let endDayToCompare;
        let endDateToCompare;
        $scope.shipSelectDate = function(selID){
            $scope.dateError = "";
            if(selID != null){
                $scope.shipStartDay = startDayToCompare = selID;
                $scope.shipStartDate = $scope.shipMonthShow;
            }
        };
        //... Reset Times ...
        $scope.resetTimeFrame = ResetTimeFrame;
        function ResetTimeFrame(){
            if($scope.showBankSearchPop){
                TogBookSearchPop();
            }
            $scope.shipshowDays = false;
            $scope.shipDateSel = 'start';
            $scope.dateError = "";
            $scope.shipStartDay = $scope.shipStartDate = $scope.shipEndDay = $scope.shipEndDate = null;
            LoadNewMonth(dte);
            $scope.dispCurDate = $scope.nowDate;
            $timeout(function(){
                $scope.shipMonthShow = dte;
            }, 350);
            getBankingData();
            TogDatePicker();
        }

        /************************************************ Shops *****************************************************/
        let shopAct = "get";
        $scope.fullShopList = [];
        getShopList();
        //... Get Shop List from DB ...
        function getShopList(){
            $scope.dispShopList = [];
            $rootScope.loading = true;
            $timeout(function(){
                $http({
                    url: 'Services/manageShops.php',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: 'act='+shopAct
                }).then(function(response) {
                        console.log("Get Shop List - " + response.data[0].status);
                        if(response.data[0].status.includes('Yes')){
                            $scope.fullShopList = [];
                            for(let a = 0; a < response.data.length; a++){
                                let tmpImg = null;
                                if(response.data[a].logo !== null){
                                    tmpImg = "Shop_Pics/"+response.data[a].dbId+".jpg";
                                }
                                $scope.fullShopList.push({
                                    listID: a,
                                    dbId: response.data[a].dbId,
                                    name: response.data[a].name,
                                    class: response.data[a].class,
                                    colorPrim: response.data[a].colorPrim,
                                    logo: tmpImg,
                                    branch: response.data[a].branch,
                                    tel: response.data[a].tel,
                                    reg: response.data[a].reg,
                                    comp: response.data[a].comp,
                                    addr: response.data[a].addr,
                                    mail: response.data[a].mail,
                                    web: response.data[a].web
                                });
                            }
                            $scope.dispShopList = $scope.fullShopList;
                            $rootScope.loading = false;
                        }
                        else {
                            $rootScope.loading = false;
                        }
                    },
                    function(){
                        console.warn("Connection Error - Get Shop List");
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
            $scope.showShopPop = !$scope.showShopPop;
            if(popupFunc === "add"){
                $scope.showShopAdd = !$scope.showShopAdd;
            }else if(popupFunc === "edit"){
                $scope.showImgCrop = false;
                $scope.shopDetsChanged = false;
                setSelectedShopItem(index);
                $scope.showShopEdit = !$scope.showShopEdit;
                if($scope.showShopEdit){
                    $timeout(function(){
                        shopEditArray[0] = $scope.activShopListItem.dbId;
                        shopEditArray[1] = $scope.editClass = $scope.activShopListItem.class;
                        shopEditArray[2] = document.getElementById('shopEditName').value = $scope.activShopListItem.name;
                        shopEditArray[3] = document.getElementById('shopEditBranch').value = $scope.activShopListItem.branch;
                        shopEditArray[4] = document.getElementById('shopEditTel').value = $scope.activShopListItem.tel;
                        shopEditArray[5] = document.getElementById('shopEditAddr').value = $scope.activShopListItem.addr;
                        shopEditArray[6] = document.getElementById('shopEditMail').value = $scope.activShopListItem.mail;
                        shopEditArray[7] = document.getElementById('shopEditWeb').value = $scope.activShopListItem.web;
                        shopEditArray[8] = document.getElementById('shopEditComp').value = $scope.activShopListItem.comp;
                        shopEditArray[9] = document.getElementById('shopEditReg').value = $scope.activShopListItem.reg;
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
        //... Toggle Shop Edit class (Prim / Sec) ...
        $scope.editClass = 'prim';
        $scope.togShopEditClass = function(){
            if($scope.editClass === 'prim'){
                $scope.editClass = 'sec';
            }else{
                $scope.editClass = 'prim';
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
                    url: 'Services/manageShops.php',
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
                $scope.activShopListItem = $scope.dispShopList[index];
                $scope.listSelectedItem = 'Shop-' + $scope.activShopListItem.dbId;
                $scope.dispCurImg = $scope.activShopListItem.logo;
            }
        }

        //... Delete Shop Item Confirmation click ...
        $scope.acceptShopDelete = function(){
            $rootScope.loading = true;
            let action = 'delete';
            $http({
                url: 'Services/manageShops.php',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: 'act='+action+'&id='+$scope.activShopListItem.dbId
            }).then(function(responseOne){
                    console.log(responseOne.data[0].status);
                    if(responseOne.data[0].status.includes('Yes')){
                        //$scope.dispShopList.splice(actShopSelIndex, 1);
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

        //....................................... Upload shop Image .......................................
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

        //....................................... Edit shop details .......................................
        $scope.shopDetsChanged = false;
        let shopEditnme = null,
            shopEditbr = null,
            shopEdittel = null,
            shopEditaddr = null,
            shopEditmail = null,
            shopEditweb = null,
            shopEditcomp = null,
            shopEditreg = null;
        let shopEditArray = [null,null,1,null,null,null,null,null,null,null];

        //... Trigger shop details changed ...
        function onShopDetsChange(){
            $scope.shopDetsChanged = true;
        }

        //... Change Edit shop Details ...
        $scope.changeEditShopDets = function(id, data){
            switch(id){
                case 'name':
                    shopEditnme = data;
                    shopEditArray[2] = data;
                    break;
                case 'branch':
                    shopEditbr = data;
                    shopEditArray[3] = data;
                    break;
                case 'tel':
                    shopEdittel = data;
                    shopEditArray[4] = data;
                    break;
                case 'addr':
                    shopEditaddr = data;
                    shopEditArray[5] = data;
                    break;
                case 'mail':
                    shopEditmail = data;
                    shopEditArray[6] = data;
                    break;
                case 'web':
                    shopEditweb = data;
                    shopEditArray[7] = data;
                    break;
                case 'comp':
                    shopEditcomp = data;
                    shopEditArray[8] = data;
                    break;
                case 'reg':
                    shopEditreg = data;
                    shopEditArray[9] = data;
                    break;
            }
            onShopDetsChange();
        };

        //... Save Shop Edit ...
        $scope.saveEditShop = function(){
            if($scope.shopDetsChanged){
                $rootScope.loading = true;
                let action = 'edit';
                $http({
                    url: 'Services/manageShops.php',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: 'act='+action+'&data='+shopEditArray
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

        /************************************************ Employees *****************************************************/
        //... Select Active/Inactive employees ...
        $scope.empDispSw = 'Active';
        $scope.togEmpActInact = function(){
            if($scope.empDispSw === 'Active'){
                $scope.empDispSw = 'Inactive';
            }else{
                $scope.empDispSw = 'Active';
            }
        };

        //... Selected shop ...
        $scope.empActShop = 'All';

        //... Toggle Shop List ...
        $scope.showEmpShopList = false;
        $scope.togEmpShopList = function(){
            $scope.showEmpShopList = !$scope.showEmpShopList;
        };
        //... Close Shop List ...
        $scope.closeEmpShopList = function(){
            $scope.showEmpShopList = false;
        };

        //... Set new Shop to display ...
        $scope.setDispEmpShop = function(sel){
            $scope.empActShop = sel;

            //... Call method to load new employee list ...

        };

        //... Set month List ...
        $scope.months = monthNames;
        //... Set Year List ...
        $scope.years = [];
        for(let a = 2000; a < (year + 10); a++){
            $scope.years.push(a);
        }

        //... Display Current Month ...
        let empCurDate = monthNames[month] + " " + year;
        $scope.empListMonth = monthNames[month];
        $scope.empListYear = year;
        $scope.dispEmpListDate = $scope.empListMonth + " " + $scope.empListYear;
        empMonthNow();
        function empMonthNow(){
            $scope.dispEmpListDate = empCurDate;
            $scope.dispSelEmpMonth = monthNames[month];
            $scope.dispSelEmpYear = year;


            //... Call method to load initial employee list ...

        }


        //... Toggle monthpicker ...
        let monthPickerFunction = 'List';
        $scope.showEmpMonthPop = false;
        $scope.togEmpMonthPicker = function(fn){
            if($scope.showEmpMonthPop){
                $scope.showEmpMonthPop = false;
            }else{
                monthPickerFunction = fn;
                $scope.showEmpMonthPop = true;
            }
        };

        //... Toggle empMonth drop ...
        $scope.togEmpMonthDrop = function(){
            $scope.showEmpMonthDrop = !$scope.showEmpMonthDrop;
        };
        //... Close empMonth drop ...
        $scope.closeEmpMonthDrop = function(){
            $scope.showEmpMonthDrop = false;
        };
        //... Toggle empYear drop ...
        $scope.togEmpYearDrop = function(){
            $scope.showEmpYearDrop = !$scope.showEmpYearDrop;
        };
        //... Close empYear drop ...
        $scope.closeEmpYearDrop = function(){
            $scope.showEmpYearDrop = false;
        };

        //... Select new month from list ...
        $scope.setNewEmpMonth = function(sel){
            if(monthPickerFunction = 'List'){
                $scope.empListMonth = sel;
            }
            $scope.dispSelEmpMonth = sel;
        };
        //... Select new year from list ...
        $scope.setNewEmpYear = function(sel){
            if(monthPickerFunction = 'List'){
                $scope.empListYear = sel;
            }
            $scope.dispSelEmpYear = sel;
        };

        //... Set new Emp Month ...
        $scope.acceptNewEmpMonth = function(){
            if(monthPickerFunction = 'List'){
                $scope.dispEmpListDate = $scope.dispSelEmpMonth + " " + $scope.dispSelEmpYear;

                $scope.togEmpMonthPicker('');
            }
        };










    }]); //End Controller ...
})();