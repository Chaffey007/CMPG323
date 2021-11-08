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
    /************************* Directive to export data to Excel **********************************/
    myApp.directive('excelExport', function(){
                return{
                    restrict: 'A',
                    scope: {
                        fileName: "@",
                        data: "&exportData"
                    },
                    replace: true,
                    //template: '<button class="btn btn-primary btn-ef btn-ef-3 btn-ef-3c mb-10" ng-click="download()">Export to Excel <i class="fa fa-download"></i></button>',
                    template: '<div class="opt" ng-click="download()"><font class="txt">Excel</font></div>',
                    link: function (scope, element) {

                        scope.download = function() {

                            function datenum(v, date1904) {
                                if(date1904) v+=1462;
                                var epoch = Date.parse(v);
                                return (epoch - new Date(Date.UTC(1899, 11, 30))) / (24 * 60 * 60 * 1000);
                            };

                            function getSheet(data, opts) {
                                var ws = {};
                                var range = {s: {c:10000000, r:10000000}, e: {c:0, r:0 }};
                                for(var R = 0; R != data.length; ++R) {
                                    for(var C = 0; C != data[R].length; ++C) {
                                        if(range.s.r > R) range.s.r = R;
                                        if(range.s.c > C) range.s.c = C;
                                        if(range.e.r < R) range.e.r = R;
                                        if(range.e.c < C) range.e.c = C;
                                        var cell = {v: data[R][C] };
                                        if(cell.v == null) continue;
                                        var cell_ref = XLSX.utils.encode_cell({c:C,r:R});

                                        if(typeof cell.v === 'number') cell.t = 'n';
                                        else if(typeof cell.v === 'boolean') cell.t = 'b';
                                        else if(cell.v instanceof Date) {
                                            cell.t = 'n'; cell.z = XLSX.SSF._table[14];
                                            cell.v = datenum(cell.v);
                                        }
                                        else cell.t = 's';

                                        ws[cell_ref] = cell;
                                    }
                                }
                                if(range.s.c < 10000000) ws['!ref'] = XLSX.utils.encode_range(range);
                                //... Column Widths ...
                                let wscols = [
                                    {wch:20},
                                    {wch:40},
                                    {wch:15}
                                ];

                                ws['!cols'] = wscols;

                                //... Cell merge ...
                                let mergeOne = [{s:{r:0,c:0},e:{r:0,c:3}}];
                                ws['!merges'] = mergeOne;
                                return ws;
                            };

                            function Workbook() {
                                if(!(this instanceof Workbook)) return new Workbook();
                                this.SheetNames = [];
                                this.Sheets = {};
                            }

                            var wb = new Workbook(), ws = getSheet(scope.data());
                            /* add worksheet to workbook */
                            wb.SheetNames.push(scope.fileName);
                            wb.Sheets[scope.fileName] = ws;
                            var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});

                            function s2ab(s) {
                                var buf = new ArrayBuffer(s.length);
                                var view = new Uint8Array(buf);
                                for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                                return buf;
                            }

                            saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), scope.fileName+'.xlsx');

                        };

                    }
                };
            }
        );

})();