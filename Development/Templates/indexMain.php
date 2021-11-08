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
                    <font class="txt">Melkbosstand</font><br><br>
                    <font class="txt">Steers / Debonairs / Fishaways</font>
                </div>
            </div>
            <!------------------------------------------------ Stock -------------------------------------------------->
            <div class="mainSubPage" ng-if="showActivePage === 'Stock'">
                <!--<div class="title"><font class="txt">Stock</font></div>-->
            </div>
            <!------------------------------------------------ Employees -------------------------------------------------->
            <div class="mainSubPage" ng-if="showActivePage === 'Employees'">
                <!--<div class="title"><font class="txt">Employees</font></div>-->
            </div>
            <!------------------------------------------------ Banking -------------------------------------------------->
            <div class="mainSubPage" ng-if="showActivePage === 'Banking'">
                <!--<div class="title"><font class="txt">Banking</font></div>-->

                <div class="bankArea">
                    <div class="curDate" ng-click="togDatePicker()">
                        <font class="txt">{{dispCurDate}}</font>
                    </div>
                    <div class="new" ng-if="checkUserPriv($root.curUserPriv, 5) && (shipDateSel === 'start')">
                        <div class="descript">
                            <input ng-focus="focused = true" ng-blur="focused = false" class="bankNewInput" ng-change="changeNewBankDescript(bankNewDescript)" ng-model="bankNewDescript" id="bankNewDescript" placeholder="Description..." type="text" />
                        </div>
                        <div class="drop" ng-show="bankNewDescript !== '' && bankNewDescript !== undefined && focused" layout="column">
                            <div class="scrl">
                                <div class="empty" ng-if="bankAutosugList.length < 1"><font class="txt">No previous entries...</font></div>
                                <div class="list" ng-repeat="item in bankAutosugList">
                                    <div class="cont" ng-click="selectNewDescr(item.title)">
                                        <font class="txt">{{item.title}}</font>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="type" layout="row" ng-click="togBankNewEntryType()">
                            <font class="txt" ng-class="{txtSel: newEntryType === 'income'}">Income</font>
                            <div class="switch">
                                <div class="bar"></div>
                                <div class="ballDeb" ng-class="{ballCred: newEntryType === 'expenses'}"></div>
                            </div>
                            <font class="txt" ng-class="{txtSel: newEntryType === 'expenses'}">Expense</font>
                        </div>
                        <div class="val">
                            <font class="cur">R</font>
                            <input class="bankNewVal" ng-change="changeNewBankVal(bankNewVal)" ng-model="bankNewVal" id="bankNewVal" placeholder="00.00" type="text" />
                        </div>
                        <button class="add" ng-click="addNewBankEntry()">Add Entry</button>
                    </div>
                    <div class="book">
                        <div class="header" layout="row">
                            <div class="headerList {{item.class}}" ng-repeat="item in bankHeaders">
                                <div class="cont">
                                    <font class="txt">{{item.title}}</font>
                                </div>
                            </div>
                        </div>
                        <div class="searchPop" ng-show="showBankSearchPop">
                            <div class="close"><font class="txt" ng-click="togBookSearchPop()">X</font></div>
                            <input class="bankSearchyInput" ng-change="changeNewBankSearch(bankNewSearch)" ng-model="bankNewSearch" id="bankNewSearch" placeholder="Search..." type="text" />
                        </div>
                        <div class="entries">
                            <div class="openBal">
                                <div class="descr">
                                    <font class="txt">Opening Balance:</font>
                                </div>
                                <div class="val">
                                    <font class="txt">R {{openBal}}</font>
                                </div>
                            </div>
                            <div class="emptyBook" ng-if="bankBookDisp.length === 0">
                                <font class="txt">No entries found...</font>
                            </div>
                            <div class="list" ng-repeat="item in bankBookDisp">
                                <div class="cont" ng-class="{contAlt: $index % 2 === 1, contSub: item.descript === 'Sub Total'}" layout="row">
                                    <div class="main one">
                                        <ng-md-icon class="icon" icon="info_outline" size="25" ng-click="togEntryDetsPop(item.listID)" ng-if="item.descript !== 'Sub Total'"></ng-md-icon>
                                    </div>
                                    <div class="main two">
                                        <font class="txt">{{item.entryDate}}</font>
                                    </div>
                                    <div class="main three">
                                        <font class="txt">{{item.descript}}</font>
                                    </div>
                                    <div class="main two">
                                        <font class="txt" ng-if="item.type === 'income'">{{item.val}}</font>
                                    </div>
                                    <div class="main two">
                                        <font class="txt" ng-if="item.type === 'expenses'">{{item.val}}</font>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="entryDetsPop" ng-show="showEntryDetsPop">
                            <div class="head" layout="row">
                                <font class="txt">Details:</font>
                                <ng-md-icon class="icon edit" icon="create" size="25" ng-click=""></ng-md-icon>
                                <div class="smlPop editSmlPop"><font class="txt">Edit</font></div>
                                <ng-md-icon class="icon delete" icon="delete_forever" size="25" ng-click="deleteBankEntry(detsDbId)"></ng-md-icon>
                                <div class="smlPop deleteSmlPop"><font class="txt">Delete</font></div>
                            </div>
                            <div class="close" ng-click="closeEntryDetsPop()"><font class="txt">Close</font></div>
                            <div class="body" layout="column">
                                <div class="sec " layout="row"><font class="title">Description:</font><font class="data">{{detsDescr}}</font></div>
                                <div class="sec " layout="row"><font class="title">Date:</font><font class="data">{{detsDate}}</font></div>
                                <div class="sec " layout="row"><font class="title">Edited:</font><font class="data">{{detsEdit}}</font></div>
                                <div class="sec " layout="row"><font class="title">Type:</font><font class="data">{{detsType}}</font></div>
                                <div class="sec " layout="row"><font class="title">Status:</font><font class="data">{{detsStat}}</font></div>
                                <div class="sec " layout="row"><font class="title">Amount:</font><font class="data">R {{detsVal}}</font></div>
                                <div class="sec " layout="row"><font class="title">Edited By:</font><font class="data">{{detsUser}}</font></div>
                                <div class="sec " layout="row"><font class="title">Notes:</font><font class="data">{{detsNote}}</font></div>
                            </div>
                        </div>
                        <div class="foot">
                            <ng-md-icon class="icon sort" icon="sort" size="25" ng-click="sortBankingAlpha()" ng-mouseleave="onFocusSortPop('leave')"></ng-md-icon>
                            <div class="sortPopSml"><font class="txt">Sort By</font></div>
                            <ng-md-icon class="icon recalc" icon="equalizer" size="25" ng-click="recalcCurrent()"></ng-md-icon>
                            <div class="calcPop"><font class="txt">Recalculate</font></div>
                            <ng-md-icon class="icon refresh" icon="loop" size="25" ng-click="loadBankingData()"></ng-md-icon>
                            <div class="refreshPop"><font class="txt">Refresh</font></div>
                            <ng-md-icon class="icon search" icon="search" size="25" ng-click="togBookSearchPop()"></ng-md-icon>
                            <div class="searchPopSml"><font class="txt">Search</font></div>
                            <ng-md-icon class="icon export" icon="file_download" size="25" ng-click="togBankExportPop()" ng-mouseleave="onFocusBankExportPop('leave')"></ng-md-icon>
                            <div class="exportPopSml"><font class="txt">Export</font></div>
                            <div class="sortPop" ng-show="showBankFilterPop" ng-mouseover="onFocusSortPop('enter')" ng-mouseleave="onFocusSortPop('leave')">
                                <div class="head"><font class="txt">Sort By:</font></div>
                                <div class="opt" ng-click="bankSortBy('Date')"><font class="txt">Date</font></div>
                                <div class="opt" ng-click="bankSortBy('Descript')"><font class="txt">Description</font></div>
                                <div class="opt" ng-click="bankSortBy('Entry')"><font class="txt">Entry Order</font></div>
                                <div class="opt" ng-click="bankSortBy('InEx')"><font class="txt">Income / Expenses</font></div>
                            </div>
                            <div class="exportPop" ng-show="showExportSelection" ng-mouseover="onFocusBankExportPop('enter')" ng-mouseleave="onFocusBankExportPop('leave')">
                                <div class="head"><font class="txt">Export To:</font></div>
                                <!--<div class="opt" ng-click=""><font class="txt">Excel</font></div>-->
                                <div excel-export export-data="exportData" file-name="{{fileName}}"></div>
                            </div>
                            <div class="ttl">
                                <font class="txt">Bank Balance:</font>
                            </div>
                            <div class="tot">
                                <font class="txt">R {{curBankBalance}}</font>
                            </div>
                        </div>
                    </div>
                </div>

                <!------------------------------------ TimeFrame Popup ----------------------------------------->
                <div class="tFrameOver" ng-if="showTFrame"></div>
                <div class="tFrame" ng-if="showTFrame">
                    <div class="head">
                        <font class="txt">Time Frame</font>
                        <font class="close" ng-click="resetTimeFrame(); closeDatePicker()">X</font>
                    </div>
                    <div class="content">
                        <div class="startDate" ng-click="shipDateSelChange('start')">
                            <div class="selectInd" ng-class="{selectIndAct: shipDateSel === 'start'}"></div>
                            <font class="dateID">Day: </font>
                            <font class="selectedDate">{{shipStartDay}} {{shipStartDate | date: "MMMM yyyy"}}</font>
                        </div>
                        <div class="startDate" ng-click="shipDateSelChange('end')">
                            <div class="selectInd" ng-class="{selectIndAct: shipDateSel === 'end'}"></div>
                            <font class="dateID">Month: </font>
                            <font class="selectedDate">{{shipEndDay}} {{shipStartDate | date: "MMMM yyyy"}}</font>
                        </div>
                        <font class="dateErrorMsg">{{dateError}}</font>
                        <div class="calender">
                            <div class="calHead">
                                <ng-md-icon class="monthLeft" icon="keyboard_arrow_left" size="25" ng-click="prevMonth()"></ng-md-icon>
                                <font class="fade-in monthShow" ng-if="shipshowDays">{{shipMonthShow | date: "MMMM yyyy"}}</font>
                                <ng-md-icon class="monthRight" icon="keyboard_arrow_right" size="25" ng-click="nextMonth()"></ng-md-icon>
                            </div>
                            <div class="dayLet" layout="row">
                                <div class="dayList" ng-repeat="day in shipTimeWeek">
                                    <font class="listItem">{{day.letter}}</font>
                                </div>
                            </div>
                            <div class="fade-in monthDaysOne" layout="row" ng-if="shipshowDays">
                                <div class="dayList" ng-repeat="item in weekOne | limitTo:7" ng-click="shipSelectDate(item.name)">
                                    <font class="dayNum" >{{item.name}}</font>
                                </div>
                            </div>
                            <div class="fade-in monthDaysTwo" layout="row" ng-if="shipshowDays">
                                <div class="dayList" ng-repeat="item in weekTwo | limitTo:7" ng-click="shipSelectDate(item.name)">
                                    <font class="dayNum">{{item.name}}</font>
                                </div>
                            </div>
                            <div class="fade-in monthDaysThree" layout="row" ng-if="shipshowDays">
                                <div class="dayList" ng-repeat="item in weekThree | limitTo:7" ng-click="shipSelectDate(item.name)">
                                    <font class="dayNum">{{item.name}}</font>
                                </div>
                            </div>
                            <div class="fade-in monthDaysFour" layout="row" ng-if="shipshowDays">
                                <div class="dayList" ng-repeat="item in weekFour | limitTo:7" ng-click="shipSelectDate(item.name)">
                                    <font class="dayNum">{{item.name}}</font>
                                </div>
                            </div>
                            <div class="fade-in monthDaysFive" layout="row" ng-if="shipshowDays">
                                <div class="dayList" ng-repeat="item in weekFive | limitTo:7" ng-click="shipSelectDate(item.name)">
                                    <font class="dayNum">{{item.name}}</font>
                                </div>
                            </div>
                            <div class="fade-in monthDaysSix" layout="row" ng-if="shipshowDays">
                                <div class="dayList" ng-repeat="item in weekSix | limitTo:7" ng-click="shipSelectDate(item.name)">
                                    <font class="dayNum">{{item.name}}</font>
                                </div>
                            </div>
                        </div>
                        <div class="actions" layout="row">
                            <div class="btn" ng-class="{btnDis: shipStartDay === undefined || shipStartDay === null }" ng-click="saveShipDates(); togTFrame()">Apply</div>
                            <div class="btn" ng-click="resetTimeFrame()">Today</div>
                        </div>
                    </div>
                    <div class="foot"></div>
                </div>
            </div>

            <!------------------------------------------------ Shops -------------------------------------------------->
            <div class="mainSubPage" ng-if="showActivePage === 'Shops'">

                <div class="shopList" layout="column">
                    <div class="shopListItem" ng-if="fullShopList.length > 0" ng-repeat="shop in dispShopList">
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

                    <div class="emptyShopList" ng-if="fullShopList.length === 0"><font class="txt">No shops in list</font></div>
                </div>

                <div class="addShopBtn" ng-click="togShopPopup('add', '')">
                    <ng-md-icon class="icon" icon="add" size="35" ng-click=""></ng-md-icon>
                    <div class="addShopPop"><font class="txt">Add New Shop</font></div>
                </div>

                <div class="shopPop" ng-show="showShopPop">
                    <div class="fullCont">
                        <div class="shopAdd" ng-if="showShopAdd" layout="column">
                            <div class="head" layout="row">
                                <font class="ttl">Add Shop</font>
                                <font class="close" ng-click="togShopPopup('add', '')">Close</font>
                            </div>
                            <div class="body" layout="column">
                                <div class="in">
                                    <input class="shopNewIn" ng-change="changeNewShopName(shopNewName)" ng-model="shopNewName" id="shopNewName" placeholder="Name..." type="text" />
                                </div>
                                <div class="in">
                                    <input class="shopNewIn" ng-change="changeNewShopBranch(shopNewBranch)" ng-model="shopNewBranch" id="shopNewBranch" placeholder="Branch..." type="text" />
                                </div>
                                <div class="class" layout="row" ng-click="togShopClass()">
                                    <font class="one" ng-class="{two: selClass === 'prim'}">Primary</font>
                                    <div class="switch">
                                        <div class="bar" ng-class="{barTwo: selClass === 'sec'}"></div>
                                        <div class="dot" ng-class="{dotTwo: selClass === 'sec'}"></div>
                                    </div>
                                    <font class="one" ng-class="{two: selClass === 'sec'}">Secondary</font>
                                </div>
                                <button class="addDis" ng-class="{add: (shopNewName.length > 0) && (shopNewBranch.length > 0)}" ng-click="addNewShop()">Add</button>
                            </div>
                        </div>
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
                                <font class="ttl">Edit Shop Details</font>
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
                                        <font class="one" ng-class="{two: editClass === 'prim'}">Primary</font>
                                        <div class="switch">
                                            <div class="bar" ng-class="{barTwo: editClass === 'sec'}"></div>
                                            <div class="dot" ng-class="{dotTwo: editClass === 'sec'}"></div>
                                        </div>
                                        <font class="one" ng-class="{two: editClass === 'sec'}">Secondary</font>
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('name',shopEditName)" ng-model="shopEditName" id="shopEditName" placeholder="Name (Trading As)..." type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('branch',shopEditBranch)" ng-model="shopEditBranch" id="shopEditBranch" placeholder="Branch..." type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('tel',shopEditTel)" ng-model="shopEditTel" id="shopEditTel" placeholder="Tel..." type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('addr',shopEditAddr)" ng-model="shopEditAddr" id="shopEditAddr" placeholder="Address..." type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('mail',shopEditMail)" ng-model="shopEditMail" id="shopEditMail" placeholder="Email..." type="email" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('web',shopEditWeb)" ng-model="shopEditWeb" id="shopEditWeb" placeholder="Website..." type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('comp',shopEditComp)" ng-model="shopEditComp" id="shopEditComp" placeholder="Registered Company..." type="text" />
                                    </div>
                                    <div class="in">
                                        <input class="shopNewIn" ng-change="changeEditShopDets('reg',shopEditReg)" ng-model="shopEditReg" id="shopEditReg" placeholder="Reg. No..." type="text" />
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
                                        <div class="item" ng-repeat="shop in fullShopList">
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