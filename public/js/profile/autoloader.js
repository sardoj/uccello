(window.webpackJsonp=window.webpackJsonp||[]).push([[4],{2:function(e,t,i){e.exports=i("U7wA")},U7wA:function(e,t,i){"use strict";function n(e,t){for(var i=0;i<t.length;i++){var n=t[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}i.r(t);var a=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.autoDisplayApiCapabilities()}var t,i,a;return t=e,(i=[{key:"autoDisplayApiCapabilities",value:function(){var e=$("#permissions-table td.for-api").length,t=$("#permissions-table td.for-api[data-checked='true']").length;t>0&&e!==t&&$("#permissions-table .for-api").removeClass("hide")}}])&&n(t.prototype,i),a&&n(t,a),e}();function s(e,t){for(var i=0;i<t.length;i++){var n=t[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var r=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.intiSwitchListener(),this.initCheckboxListener(),this.autoCheck(),this.autoDisplayApiCapabilities(),this.autoDisplaySwitch()}var t,i,n;return t=e,(i=[{key:"intiSwitchListener",value:function(){$("#manage-api-capabilities").on("change",(function(e){$(e.currentTarget).is(":checked")?$("#permissions-table .for-api").removeClass("hide"):$("#permissions-table .for-api").addClass("hide")}))}},{key:"initCheckboxListener",value:function(){var e=this;$("#permissions-table input.select-all").on("change",(function(t){$(t.currentTarget).is(":checked")?$("#permissions-table input[type='checkbox']").prop("checked",!0):$("#permissions-table input[type='checkbox']").prop("checked",!1),e.autoCheck()})),$("#permissions-table input.select-row").on("change",(function(t){var i=$(t.currentTarget),n=$(i).parents("tr:first");i.is(":checked")?$(".select-item",n).prop("checked",!0):$(".select-item",n).prop("checked",!1),e.autoCheck()})),$("#permissions-table input.select-item").on("change",(function(t){$(t.currentTarget),e.autoCheck()}))}},{key:"autoCheck",value:function(){var e=$("#permissions-table .select-item:checked").length,t=$("#permissions-table .select-item").length;$("#permissions-table .select-all").prop("checked",e===t),$("#permissions-table tbody tr").each((function(e,t){var i=$(t),n=$(".select-item:checked",i).length,a=$(".select-item",i).length;$(".select-row",i).prop("checked",n===a)}))}},{key:"autoDisplayApiCapabilities",value:function(){var e=$("#permissions-table td.for-api input.select-item").length,t=$("#permissions-table td.for-api input.select-item:checked").length;t>0&&e!==t&&$("#manage-api-capabilities").prop("checked",!0).change()}},{key:"autoDisplaySwitch",value:function(){$("#permissions-table td.for-api input.select-item").length>0&&$(".api-switch").removeClass("hide")}}])&&s(t.prototype,i),n&&s(t,n),e}();function c(e,t){for(var i=0;i<t.length;i++){var n=t[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}new(function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.lazyLoad()}var t,i,n;return t=e,(i=[{key:"lazyLoad",value:function(){switch($('meta[name="page"]').attr("content")){case"detail":new a;break;case"edit":new r}}}])&&c(t.prototype,i),n&&c(t,n),e}())}},[[2,0]]]);