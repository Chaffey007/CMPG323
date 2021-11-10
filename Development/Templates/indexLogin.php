<!------------------------------------------------ Login Popup -------------------------------------------------->
<div ng-controller="loginController" ng-cloak>
    <div class="fade-in" ng-show="$root.documentReady">
        <div class="logAndReg fade-in" ng-class="{logAndRegMobile: $root.isMobile}">
            <font class="title">{{logReg}}</font>
            <form id="loginForm" name="form" ng-submit="login()" role="form" class="logForm" ng-show="logReg == 'Login'">
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
                <button class="logBut" ng-click="" ng-disabled="form.$invalid || $root.loading">Login</button>
            </form>
            <form id="registerForm" name="regform" ng-submit="register()" role="form" class="logForm" ng-show="logReg == 'Register'">
                <div class="loginInputBar">
                    <md-input-container md-no-float class="md-block logInput" flex-gt-sm>
                        <input type="text" name="regFname" id="regFname" ng-model="$root.regFname" placeholder="First Name" ng-change="clearError()" required/>
                    </md-input-container>
                </div>
                <div class="loginInputBar">
                    <md-input-container md-no-float class="md-block logInput" flex-gt-sm>
                        <input type="text" name="regLname" id="regLname" ng-model="$root.regLname" placeholder="Last Name" ng-change="clearError()" required/>
                    </md-input-container>
                </div>
                <div class="loginInputBar">
                    <md-input-container md-no-float class="md-block logInput" flex-gt-sm>
                        <input type="text" name="regMail" id="regMail" ng-model="$root.regMail" placeholder="Email" ng-change="clearError()" required/>
                    </md-input-container>
                </div>

                <div class="loginInputBar">
                    <md-input-container md-no-float class="md-block logInput" flex-gt-sm>
                        <input type="text" name="regUsername" id="regUsername" ng-model="$root.regUsername" placeholder="Username" ng-change="clearError()" required/>
                    </md-input-container>
                </div>
                <div class="loginInputBar">
                    <md-input-container md-no-float class="md-block logInput" flex-gt-sm>
                        <input type="password" id="regPassword" ng-model="$root.regPassword" placeholder="Password" ng-change="clearError()" required />
                    </md-input-container>
                </div>
                <div class="logErrorMsg" ng-show="logErrorMsg !== ''">{{logErrorMsg}}</div>
                <button class="logBut" ng-click="" ng-disabled="regform.$invalid || $root.loading">Register</button>
            </form>
            <br>
            <a style="color: #fff" ng-click="logRegSwitch()">{{logRegTog}}</a>
        </div>
    </div>
</div>