<!-- loadBankingData -->

<!------------------------------------------------ Main Page -------------------------------------------------->
<div ng-controller="mainController" ng-cloak>
    <div class="fade-in" ng-show="$root.indexReady" ng-init="onInactive(600000, $root.logout);">
        <div class="mainPage">
            <!------------------------------------------------ Header -------------------------------------------------->
            <div class="header">
                <div class="leftMenuIcon" ng-click="togLeftMenu()">
                    <ng-md-icon class="menuIcon" icon="{{$root.headMenu}}" size="40"></ng-md-icon>
                </div>
                <div class="title">
                    <font class="txt">{{$root.pageTitle}}</font>
                    <font class="txtOne">- {{showActivePage}}</font>
                </div>
                <div class="acountDiv" layout="row" ng-click="togProfMenuDrop()">
                    <font class="txt">{{$root.fullName}}</font>
                    <div class="img">
                        <ng-md-icon class="menuIcon" icon="account_circle" size="40"></ng-md-icon>
                    </div>
                    <div class="profMenu" layout="column" ng-if="showProfMenuDrop">
                        <!--<div class="selection" layout="row">
                            <ng-md-icon class="icon" icon="mode_edit" size="30"></ng-md-icon>
                            <font class="txt">Edit Profile</font>
                        </div>-->
                        <div class="selection" layout="row" ng-click="$root.logout()">
                            <ng-md-icon class="icon" icon="logout" size="30"></ng-md-icon>
                            <font class="txt">Sign Out</font>
                        </div>
                    </div>
                </div>
            </div>
            <!------------------------------------------------ Menu Left -------------------------------------------------->
            <div class="menuLeft" ng-if="$root.showFullLeftMenu" layout="column">
                <div class="leftMenuList" ng-repeat="item in menuOptionList">
                    <div class="menuItem" ng-class="{menuItemAct: showActivePage === item.title}" layout="row" ng-click="$root.togLeftMenu(); selectDispPage(item.title)">
                        <ng-md-icon class="icon" icon="{{item.icon}}" size="30"></ng-md-icon>
                        <div class="font">
                            <font class="txt">{{item.title}}</font>
                        </div>
                    </div>
                </div>
            </div>
            <!------------------------------------------------ Home -------------------------------------------------->
            <div class="mainSubPage" ng-if="showActivePage === 'Home'">
                <div class="welcome">
                    <font class="txt">Welcome</font><br><br>
                    <font class="txt">Manage your uploads and view what has been shared with you.</font>
                </div>
            </div>

            <!------------------------------------------------ Uploads -------------------------------------------------->
            <div class="mainSubPage" ng-if="showActivePage === 'My Uploads'">

                <div class="shopList" layout="column">
                    <div class="shopListItem" ng-if="fullUplList.length > 0" ng-repeat="shop in dispUplList">
                        <div class="cont" layout="row">
                            <div class="img">
                                <ng-md-icon class="icon" icon="image" size="40" ng-show="shop.logo === null"></ng-md-icon>
                                <div class="pic" ng-class="{picNone: shop.logo === null}">
                                    <img class="crop" ng-src="{{shop.logo}}" />
                                </div>

                            </div>
                            <div class="name">
                                <font class="nme">{{shop.name}}</font>
                            </div>
                            <div class="controls" layout="row">
                                <ng-md-icon class="icon edit" icon="edit" size="20" ng-show="item.logo !== 'yes'" ng-click="togShopPopup('edit', $index)"></ng-md-icon>
                                <div class="shopEditInfoPop"><font class="txt">Edit Details</font></div>
                                <ng-md-icon class="icon remove" icon="delete" size="20" ng-show="item.logo !== 'yes'" ng-click="togShopPopup('del', $index)"></ng-md-icon>
                                <div class="shopRemoveInfoPop"><font class="txt">Delete From List</font></div>
                            </div>
                        </div>
                    </div>

                    <div class="emptyShopList" ng-if="fullUplList.length === 0"><font class="txt">No images uploaded</font></div>
                </div>

                <div class="addShopBtn" ng-click="togShopPopup('add', '')">
                    <ng-md-icon class="icon" icon="add" size="35" ng-click=""></ng-md-icon>
                    <div class="addShopPop"><font class="txt">Add New Shop</font></div>
                </div>

                <div class="shopPop" ng-show="showShopPop">
                    <div class="fullCont">
                        <div class="confirmShopDelete" ng-if="showShopDel" layout="column">
                            <div class="head" layout="row">
                                <ng-md-icon class="icon" icon="warning" size="30"></ng-md-icon>
                                <div class="text">
                                    <font class="txt">Are you sure you want to delete </font><br>
                                    <font class="txtAlt"> {{activShopListItem.name}}</font>
                                    <font class="txt">?</font>
                                </div>
                            </div>
                            <div class="actions" layout="row">
                                <button class="btn" ng-click="togShopPopup('del', '')">Cancel</button>
                                <button class="btn" ng-click="acceptShopDelete()">Delete</button>
                            </div>
                        </div>
                        <div class="shopEdit" ng-if="showShopEdit" layout="column">
                            <div class="head" layout="row">
                                <font class="ttl">{{uploadDetsTtl}}</font>
                                <font class="close" ng-click="togShopPopup('edit', '')">Close</font>
                            </div>
                            <div class="body" layout="column">
                                <div class="scrl" layout="column">
                                    <div class="imga">
                                        <ng-md-icon icon="image" class="icon" size="100"></ng-md-icon>
                                        <img class="crp" ng-class="{crpNone: dispCurImg === '' || dispCurImg === null}" ng-src="{{dispCurImg}}"/>
                                    </div>
                                    <div class="edit">
                                        <input type="file" class="selImgBtn" demo-img-select="newImg"  />
                                        <ng-md-icon icon="edit" class="editImgIco" size="17"></ng-md-icon>
                                    </div>
                                    <div class="class" layout="row" ng-click="togShopEditClass()">
                                        <font class="one" ng-class="{two: editClass === 0}">Not Shared</font>
                                        <div class="switch">
                                            <div class="bar" ng-class="{barTwo: editClass === 1}"></div>
                                            <div class="dot" ng-class="{dotTwo: editClass === 1}"></div>
                                        </div>
                                        <font class="one" ng-class="{two: editClass === 1}">Shared</font>
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('ttl',uplTtl)" ng-model="uplTtl" id="uplTtl" placeholder="Title" type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('descr',uplDesc)" ng-model="uplDesc" id="uplDesc" placeholder="Description" type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('tags',uplTags)" ng-model="uplTags" id="uplTags" placeholder="Tags" type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('geoloc',uplGeo)" ng-model="uplGeo" id="uplGeo" placeholder="Geolocation" type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('capDate',uplCapDate)" ng-model="uplCapDate" id="uplCapDate" placeholder="Capture Date" type="email" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('capBy',uplCapBy)" ng-model="uplCapBy" id="uplCapBy" placeholder="Captured By" type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('shareWith',uplShareWith)" ng-model="uplShareWith" id="uplShareWith" placeholder="Shared With" type="text" />
                                    </div>

                                </div>
                                <div class="actions" layout="row">
                                    <button class="add" ng-click="togShopPopup('edit', '')">Cancel</button>
                                    <button class="addDis" ng-class="{add: shopDetsChanged}" ng-click="saveEditShop()">Save</button>
                                </div>
                                <!---------------------------------------------------- Image Crop -------------------------------------------------------------->
                                <div class="cropDisp" ng-if="showImgCrop">
                                    <div class="cropArea" layout="row">
                                        <div class="left" layout="column">
                                            <font class="txt">Crop Image:</font>
                                            <img-crop image="myImage" result-image="myCroppedImage" result-image-format="image/jpeg" on-change="setCroppedResult($dataURI)" ></img-crop>
                                        </div>
                                        <div class="right" layout="column">
                                            <font  class="txt">Preview Selection</font>
                                            <img ng-src="{{myCroppedImage}}" class="crp" />
                                            <div class="actions" layout="row">
                                                <button class="btn" ng-click="closeImgCrop()">Cancel</button>
                                                <button class="btn" ng-click="saveImgCrop()">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!------------------------------------------------ Employees -------------------------------------------------->
            <div class="mainSubPage" ng-if="showActivePage === 'Employees'">
                <div class="userArea" layout="column">
                    <!------ Head Bar ------->
                    <div class="head" layout="row">
                        <div class="cont edge" ng-click="togEmpActInact()">
                            <div class="block">
                                <div class="dispSw" layout="row">
                                    <font class="txtDis" ng-class="{txt: empDispSw === 'Active'}">Active</font>
                                    <div class="switch">
                                        <div class="bar"></div>
                                        <div class="dot" ng-class="{dotTwo: empDispSw === 'Inactive'}"></div>
                                    </div>
                                    <font class="txtDis" ng-class="{txt: empDispSw === 'Inactive'}">Inactive</font>
                                </div>
                                <div class="empHeadTipPop"><font class="txt">Active- / Inactive Employees</font></div>
                            </div>
                        </div>
                        <div class="cont mid" ng-click="togEmpShopList()">
                            <div class="block" ng-mouseleave="closeEmpShopList()">
                                <font class="dateTxt">{{empActShop}}</font>
                                <div class="headDrop" ng-show="showEmpShopList" >
                                    <div class="list" layout="column">
                                        <div class="item">
                                            <div class="cont" ng-click="setDispEmpShop('All')">
                                                <font class="txt">All</font>
                                            </div>
                                        </div>
                                        <div class="item" ng-repeat="shop in fullUplList">
                                            <div class="cont" ng-click="setDispEmpShop(shop.name)">
                                                <font class="txt">{{shop.name}}</font>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="empHeadTipPop"><font class="txt">Filter By Shop</font></div>
                            </div>
                        </div>
                        <div class="cont edge">
                            <div class="block" ng-click="togEmpMonthPicker('List')">
                                <font class="dateTxt">{{dispEmpListDate}}</font>
                                <div class="empHeadTipPop"><font class="txt">Select Moth to Display</font></div>
                            </div>
                        </div>
                    </div>
                    <!------ List Block ------->
                    <div class="listBlock">
                        <div class="list" ng-repeat="user in dispEmpUserList">

                        </div>
                    </div>
                </div>
                <!------ Add New Button ------->
                <div class="addShopBtn" ng-click="">
                    <ng-md-icon class="icon" icon="add" size="35" ng-click=""></ng-md-icon>
                    <div class="addShopPop"><font class="txt">Add New Employee</font></div>
                </div>
                <!------------- MonthPicker Popup -------------->
                <div class="empMonthOver" ng-show="showEmpMonthPop">
                    <div class="contain" ng-if="showEmpMonthPop" layout="column">
                        <div class="disp" layout="row">
                            <div class="back" ng-click="togEmpMonthDrop()" ng-mouseleave="closeEmpMonthDrop()">
                                <font class="txt">{{dispSelEmpMonth}}</font>
                                <div class="drop" ng-show="showEmpMonthDrop">
                                    <div class="cont">
                                        <div class="list" ng-repeat="month in months">
                                            <div class="item" ng-click="setNewEmpMonth(month)">
                                                <font class="txt">{{month}}</font>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="back" ng-click="togEmpYearDrop()" ng-mouseleave="closeEmpYearDrop()">
                                <font class="txt">{{dispSelEmpYear}}</font>
                                <div class="drop" ng-show="showEmpYearDrop">
                                    <div class="cont">
                                        <div class="list" ng-repeat="year in years">
                                            <div class="item" ng-click="setNewEmpYear(year)">
                                                <font class="txt">{{year}}</font>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action" layout="row">
                            <button class="act" ng-click="togEmpMonthPicker('')">Cancel</button>
                            <button class="act" ng-click="acceptNewEmpMonth()">Accept</button>
                        </div>
                    </div>
                </div>

            </div> <!-- End EMPLOYEES -->


        </div>
    </div>
</div>