<!DOCTYPE html>
<html ng-app="Body">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="image/png" href="Images/tabIcon.png">
    <title>Management</title>
    <script src="Bower/angular/angular.min.js"></script>
</head>
<!-- ================================= Body ================================= -->
<body>
<div id="bodyContent" class="fade-in bodyContent">
    <div class="bodyOverlay"></div>
    <!-- ---------------------------------------------- Loading Controller ------------------------------------------------ -->
    <div ng-controller="loadingController">
        <div class="loadingOverlay" ng-if="$root.loading"></div>
        <div layout="row" layout-sm="column" layout-align="space-around" class="progressCircle" ng-class="{progressCircleMobile: $root.isMobile}" ng-show="$root.loading">
            <md-progress-circular ng-if="!$root.isMobile" md-mode="indeterminate" md-diameter="40"></md-progress-circular>
            <md-progress-circular ng-if="$root.isMobile" md-mode="indeterminate" md-diameter="80"></md-progress-circular>
        </div>
        <div class="mainLoading"></div>
    </div>
    <!----------------------------------------------------- Content ----------------------------------------------------------------->
    <div class="pageContainer fade-in" ng-class="{pageContainerMobile: $root.isMobile}" role="main">
        <div class="cont fade-in">
            <div ng-view></div>
        </div>
    </div>








</div> <!-- End bodyContent -->

    <link rel="stylesheet" href="CSS/misc.css" />
    <link rel="stylesheet" href="CSS/index.css" />
    <link rel="stylesheet" href="CSS/indexMain.css" />
    <link rel="stylesheet" href="CSS/indexBank.css" />
    <link rel="stylesheet" href="CSS/indexShop.css" />
    <link rel="stylesheet" href="CSS/indexEmployee.css" />


    <link rel="stylesheet" href="Bower/angular-material/angular-material.min.css" />


    <script type="text/javascript" src="Components/indexModule.js"></script>
    <script type="text/javascript" src="Components/indexDirectives.js"></script>
    <script type="text/javascript" src="Components/indexServices.js"></script>
    <script type="text/javascript" src="Components/indexControllers.js"></script>
    <script type="text/javascript" src="Components/indexMobileControllers.js"></script>

    <script src="Bower/angular-route/angular-route.min.js"></script>
    <script src="Bower/angular-material/angular-material.min.js"></script>
    <script src="Bower/angular-animate/angular-animate.min.js"></script>
    <script src="Bower/angular-material-icons/angular-material-icons.min.js"></script>
    <script src="Bower/angular-aria/angular-aria.min.js"></script>
    <script src="Bower/angular-messages/angular-messages.min.js"></script>
    <script src="Bower/angular-cookies/angular-cookies.min.js"></script>
    <script src="Bower/jquery/dist/jquery.min.js"></script>
    <script src="Bower/svg-morpheus/compile/minified/svg-morpheus.js"></script>

    <!--<script async src="Bower/jquery/dist/jquery.min.js"></script>-->
    <!--<script async src="Bower/svg-morpheus/compile/minified/svg-morpheus.js"></script>-->
    <!--<script async src="JS/angular.ng-modules.js"></script>-->

    <script async src="JS/angular.ng-modules.js"></script>
    <script src="JS/hammer.min.js"></script>
    <script src="JS/jszip.js"></script>
    <script src="JS/xlsx.js"></script>
    <script src="JS/FileSaver.js"></script>
    <script src="JS/ng-img-crop.js"></script>
</body>
</html>