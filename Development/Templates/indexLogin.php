<!------------------------------------------------ Login Popup -------------------------------------------------->
<div ng-controller="loginController" ng-cloak>
    <div class="fade-in" ng-show="$root.documentReady">
        <div class="logAndReg fade-in" ng-class="{logAndRegMobile: $root.isMobile}">
            <font class="title">Login</font>
            <form id="loginForm" name="form" ng-submit="login()" role="form" class="logForm">
                <div class="loginInputBar">
                    <md-input-container md-no-float class="md-block logInput" flex-gt-sm>
                        <input type="text" name="loginUsername" id="loginUsername" ng-model="$root.loginUsername" placeholder="{{loginInputOne}}" ng-change="clearError()" required/>
                    </md-input-container>
                </div>
                <div class="loginInputBar">
                    <md-input-container md-no-float class="md-block logInput" flex-gt-sm>
                        <input type="password" id="loginPassword" ng-model="$root.loginPassword" placeholder="Password" ng-change="clearError()" required />
                    </md-input-container>
                </div>
                <div class="logErrorMsg" ng-show="logErrorMsg !== ''">{{logErrorMsg}}</div>
                <button class="logBut" ng-click="login()" ng-disabled="form.$invalid || $root.loading">Submit</button>
            </form>
        </div>
    </div>
</div>