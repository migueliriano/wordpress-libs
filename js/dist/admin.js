(()=>{var t={936:(t,e,r)=>{"use strict";r.r(e);var n=r(1669),o=r(1669);const i=window.LIPE_LIBS_CMB2_TERM_SELECT2;if(!i)throw new Error("LIPE_LIBS_CMB2_TERM_SELECT2 is not defined");const s={url:i.ajaxUrl,dataType:"json",cache:!0,type:"POST",delay:350,processResults:t=>t.success?{results:t.data}:(console.error(t.data),{results:[]})};function u(t,e){let r=n(`div:not(.empty-row) > div > [data-js="${t.id}"]`);0!==r.length&&(e&&(r=r.last()),r.each(((e,r)=>{const o=n(r);s.data=e=>({term:e.term,id:t.id,selected:o.val(),_wpnonce:o.parent().find(`input[name="${t.id}_nonce"]`).val()});const i={ajax:s,minimumInputLength:3,language:{noResults:()=>t.noResultsText}};o.select2(i)})))}o((function(t){i.fields.forEach((function(e){u(e,!1),t(`[data-selector="${e.id}_repeat"]`).on("click",(function(){setTimeout((()=>u(e,!0)),20)}))}))}))},1852:(t,e,r)=>{r(1669)((function(t){const e=t('[data-js="lipe/lib/taxonomy/terms-checklist"]');e.on("click",'input[value="0"]',(function(){const e=t(this);!0===e.prop("checked")&&e.closest("ul").find('input[type="checkbox"]').not(e).prop("checked",!1)})),e.on("click","input",(function(){const e=t(this);parseInt(e.val())>0&&e.closest("ul").find('input[value="0"]').prop("checked",!1)}))}))},1669:t=>{"use strict";t.exports=jQuery},1468:(t,e,r)=>{"use strict";var n=r(1207),o=r(8381),i=TypeError;t.exports=function(t){if(n(t))return t;throw new i(o(t)+" is not a function")}},6577:(t,e,r)=>{"use strict";var n=r(9340),o=String,i=TypeError;t.exports=function(t){if(n(t))return t;throw new i(o(t)+" is not an object")}},3114:(t,e,r)=>{"use strict";var n=r(1182),o=n({}.toString),i=n("".slice);t.exports=function(t){return i(o(t),8,-1)}},6413:(t,e,r)=>{"use strict";var n=r(4558),o=r(1207),i=r(3114),s=r(9101)("toStringTag"),u=Object,c="Arguments"===i(function(){return arguments}());t.exports=n?i:function(t){var e,r,n;return void 0===t?"Undefined":null===t?"Null":"string"==typeof(r=function(t,e){try{return t[e]}catch(t){}}(e=u(t),s))?r:c?i(e):"Object"===(n=i(e))&&o(e.callee)?"Arguments":n}},6641:(t,e,r)=>{"use strict";var n=r(1458),o=r(2127),i=r(3158);t.exports=n?function(t,e,r){return o.f(t,e,i(1,r))}:function(t,e,r){return t[e]=r,t}},3158:t=>{"use strict";t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e}}},2372:(t,e,r)=>{"use strict";var n=r(1293),o=r(2127);t.exports=function(t,e,r){return r.get&&n(r.get,e,{getter:!0}),r.set&&n(r.set,e,{setter:!0}),o.f(t,e,r)}},4150:(t,e,r)=>{"use strict";var n=r(1207),o=r(2127),i=r(1293),s=r(6827);t.exports=function(t,e,r,u){u||(u={});var c=u.enumerable,a=void 0!==u.name?u.name:e;if(n(r)&&i(r,a,u),u.global)c?t[e]=r:s(e,r);else{try{u.unsafe?t[e]&&(c=!0):delete t[e]}catch(t){}c?t[e]=r:o.f(t,e,{value:r,enumerable:!1,configurable:!u.nonConfigurable,writable:!u.nonWritable})}return t}},6827:(t,e,r)=>{"use strict";var n=r(4577),o=Object.defineProperty;t.exports=function(t,e){try{o(n,t,{value:e,configurable:!0,writable:!0})}catch(r){n[t]=e}return e}},1458:(t,e,r)=>{"use strict";var n=r(5421);t.exports=!n((function(){return 7!==Object.defineProperty({},1,{get:function(){return 7}})[1]}))},6341:(t,e,r)=>{"use strict";var n=r(4577),o=r(9340),i=n.document,s=o(i)&&o(i.createElement);t.exports=function(t){return s?i.createElement(t):{}}},6506:t=>{"use strict";t.exports="undefined"!=typeof navigator&&String(navigator.userAgent)||""},3694:(t,e,r)=>{"use strict";var n,o,i=r(4577),s=r(6506),u=i.process,c=i.Deno,a=u&&u.versions||c&&c.version,p=a&&a.v8;p&&(o=(n=p.split("."))[0]>0&&n[0]<4?1:+(n[0]+n[1])),!o&&s&&(!(n=s.match(/Edge\/(\d+)/))||n[1]>=74)&&(n=s.match(/Chrome\/(\d+)/))&&(o=+n[1]),t.exports=o},5421:t=>{"use strict";t.exports=function(t){try{return!!t()}catch(t){return!0}}},6318:(t,e,r)=>{"use strict";var n=r(5421);t.exports=!n((function(){var t=function(){}.bind();return"function"!=typeof t||t.hasOwnProperty("prototype")}))},3227:(t,e,r)=>{"use strict";var n=r(6318),o=Function.prototype.call;t.exports=n?o.bind(o):function(){return o.apply(o,arguments)}},9924:(t,e,r)=>{"use strict";var n=r(1458),o=r(3863),i=Function.prototype,s=n&&Object.getOwnPropertyDescriptor,u=o(i,"name"),c=u&&"something"===function(){}.name,a=u&&(!n||n&&s(i,"name").configurable);t.exports={EXISTS:u,PROPER:c,CONFIGURABLE:a}},1182:(t,e,r)=>{"use strict";var n=r(6318),o=Function.prototype,i=o.call,s=n&&o.bind.bind(i,i);t.exports=n?s:function(t){return function(){return i.apply(t,arguments)}}},2677:(t,e,r)=>{"use strict";var n=r(4577),o=r(1207);t.exports=function(t,e){return arguments.length<2?(r=n[t],o(r)?r:void 0):n[t]&&n[t][e];var r}},440:(t,e,r)=>{"use strict";var n=r(1468),o=r(1547);t.exports=function(t,e){var r=t[e];return o(r)?void 0:n(r)}},4577:function(t,e,r){"use strict";var n=function(t){return t&&t.Math===Math&&t};t.exports=n("object"==typeof globalThis&&globalThis)||n("object"==typeof window&&window)||n("object"==typeof self&&self)||n("object"==typeof r.g&&r.g)||n("object"==typeof this&&this)||function(){return this}()||Function("return this")()},3863:(t,e,r)=>{"use strict";var n=r(1182),o=r(2847),i=n({}.hasOwnProperty);t.exports=Object.hasOwn||function(t,e){return i(o(t),e)}},775:t=>{"use strict";t.exports={}},7675:(t,e,r)=>{"use strict";var n=r(1458),o=r(5421),i=r(6341);t.exports=!n&&!o((function(){return 7!==Object.defineProperty(i("div"),"a",{get:function(){return 7}}).a}))},696:(t,e,r)=>{"use strict";var n=r(1182),o=r(1207),i=r(1731),s=n(Function.toString);o(i.inspectSource)||(i.inspectSource=function(t){return s(t)}),t.exports=i.inspectSource},9079:(t,e,r)=>{"use strict";var n,o,i,s=r(5208),u=r(4577),c=r(9340),a=r(6641),p=r(3863),f=r(1731),l=r(7581),v=r(775),b="Object already initialized",d=u.TypeError,y=u.WeakMap;if(s||f.state){var h=f.state||(f.state=new y);h.get=h.get,h.has=h.has,h.set=h.set,n=function(t,e){if(h.has(t))throw new d(b);return e.facade=t,h.set(t,e),e},o=function(t){return h.get(t)||{}},i=function(t){return h.has(t)}}else{var g=l("state");v[g]=!0,n=function(t,e){if(p(t,g))throw new d(b);return e.facade=t,a(t,g,e),e},o=function(t){return p(t,g)?t[g]:{}},i=function(t){return p(t,g)}}t.exports={set:n,get:o,has:i,enforce:function(t){return i(t)?o(t):n(t,{})},getterFor:function(t){return function(e){var r;if(!c(e)||(r=o(e)).type!==t)throw new d("Incompatible receiver, "+t+" required");return r}}}},1207:t=>{"use strict";var e="object"==typeof document&&document.all;t.exports=void 0===e&&void 0!==e?function(t){return"function"==typeof t||t===e}:function(t){return"function"==typeof t}},1547:t=>{"use strict";t.exports=function(t){return null==t}},9340:(t,e,r)=>{"use strict";var n=r(1207);t.exports=function(t){return"object"==typeof t?null!==t:n(t)}},5329:t=>{"use strict";t.exports=!1},6563:(t,e,r)=>{"use strict";var n=r(2677),o=r(1207),i=r(7443),s=r(3714),u=Object;t.exports=s?function(t){return"symbol"==typeof t}:function(t){var e=n("Symbol");return o(e)&&i(e.prototype,u(t))}},1293:(t,e,r)=>{"use strict";var n=r(1182),o=r(5421),i=r(1207),s=r(3863),u=r(1458),c=r(9924).CONFIGURABLE,a=r(696),p=r(9079),f=p.enforce,l=p.get,v=String,b=Object.defineProperty,d=n("".slice),y=n("".replace),h=n([].join),g=u&&!o((function(){return 8!==b((function(){}),"length",{value:8}).length})),m=String(String).split("String"),w=t.exports=function(t,e,r){"Symbol("===d(v(e),0,7)&&(e="["+y(v(e),/^Symbol\(([^)]*)\).*$/,"$1")+"]"),r&&r.getter&&(e="get "+e),r&&r.setter&&(e="set "+e),(!s(t,"name")||c&&t.name!==e)&&(u?b(t,"name",{value:e,configurable:!0}):t.name=e),g&&r&&s(r,"arity")&&t.length!==r.arity&&b(t,"length",{value:r.arity});try{r&&s(r,"constructor")&&r.constructor?u&&b(t,"prototype",{writable:!1}):t.prototype&&(t.prototype=void 0)}catch(t){}var n=f(t);return s(n,"source")||(n.source=h(m,"string"==typeof e?e:"")),t};Function.prototype.toString=w((function(){return i(this)&&l(this).source||a(this)}),"toString")},2127:(t,e,r)=>{"use strict";var n=r(1458),o=r(7675),i=r(4956),s=r(6577),u=r(5515),c=TypeError,a=Object.defineProperty,p=Object.getOwnPropertyDescriptor,f="enumerable",l="configurable",v="writable";e.f=n?i?function(t,e,r){if(s(t),e=u(e),s(r),"function"==typeof t&&"prototype"===e&&"value"in r&&v in r&&!r[v]){var n=p(t,e);n&&n[v]&&(t[e]=r.value,r={configurable:l in r?r[l]:n[l],enumerable:f in r?r[f]:n[f],writable:!1})}return a(t,e,r)}:a:function(t,e,r){if(s(t),e=u(e),s(r),o)try{return a(t,e,r)}catch(t){}if("get"in r||"set"in r)throw new c("Accessors not supported");return"value"in r&&(t[e]=r.value),t}},7443:(t,e,r)=>{"use strict";var n=r(1182);t.exports=n({}.isPrototypeOf)},152:(t,e,r)=>{"use strict";var n=r(3227),o=r(1207),i=r(9340),s=TypeError;t.exports=function(t,e){var r,u;if("string"===e&&o(r=t.toString)&&!i(u=n(r,t)))return u;if(o(r=t.valueOf)&&!i(u=n(r,t)))return u;if("string"!==e&&o(r=t.toString)&&!i(u=n(r,t)))return u;throw new s("Can't convert object to primitive value")}},1484:(t,e,r)=>{"use strict";var n=r(1547),o=TypeError;t.exports=function(t){if(n(t))throw new o("Can't call method on "+t);return t}},7581:(t,e,r)=>{"use strict";var n=r(1027),o=r(2686),i=n("keys");t.exports=function(t){return i[t]||(i[t]=o(t))}},1731:(t,e,r)=>{"use strict";var n=r(5329),o=r(4577),i=r(6827),s="__core-js_shared__",u=t.exports=o[s]||i(s,{});(u.versions||(u.versions=[])).push({version:"3.37.1",mode:n?"pure":"global",copyright:"© 2014-2024 Denis Pushkarev (zloirock.ru)",license:"https://github.com/zloirock/core-js/blob/v3.37.1/LICENSE",source:"https://github.com/zloirock/core-js"})},1027:(t,e,r)=>{"use strict";var n=r(1731);t.exports=function(t,e){return n[t]||(n[t]=e||{})}},4185:(t,e,r)=>{"use strict";var n=r(3694),o=r(5421),i=r(4577).String;t.exports=!!Object.getOwnPropertySymbols&&!o((function(){var t=Symbol("symbol detection");return!i(t)||!(Object(t)instanceof Symbol)||!Symbol.sham&&n&&n<41}))},2847:(t,e,r)=>{"use strict";var n=r(1484),o=Object;t.exports=function(t){return o(n(t))}},2007:(t,e,r)=>{"use strict";var n=r(3227),o=r(9340),i=r(6563),s=r(440),u=r(152),c=r(9101),a=TypeError,p=c("toPrimitive");t.exports=function(t,e){if(!o(t)||i(t))return t;var r,c=s(t,p);if(c){if(void 0===e&&(e="default"),r=n(c,t,e),!o(r)||i(r))return r;throw new a("Can't convert object to primitive value")}return void 0===e&&(e="number"),u(t,e)}},5515:(t,e,r)=>{"use strict";var n=r(2007),o=r(6563);t.exports=function(t){var e=n(t,"string");return o(e)?e:e+""}},4558:(t,e,r)=>{"use strict";var n={};n[r(9101)("toStringTag")]="z",t.exports="[object z]"===String(n)},8441:(t,e,r)=>{"use strict";var n=r(6413),o=String;t.exports=function(t){if("Symbol"===n(t))throw new TypeError("Cannot convert a Symbol value to a string");return o(t)}},8381:t=>{"use strict";var e=String;t.exports=function(t){try{return e(t)}catch(t){return"Object"}}},2686:(t,e,r)=>{"use strict";var n=r(1182),o=0,i=Math.random(),s=n(1..toString);t.exports=function(t){return"Symbol("+(void 0===t?"":t)+")_"+s(++o+i,36)}},3714:(t,e,r)=>{"use strict";var n=r(4185);t.exports=n&&!Symbol.sham&&"symbol"==typeof Symbol.iterator},4956:(t,e,r)=>{"use strict";var n=r(1458),o=r(5421);t.exports=n&&o((function(){return 42!==Object.defineProperty((function(){}),"prototype",{value:42,writable:!1}).prototype}))},4822:t=>{"use strict";var e=TypeError;t.exports=function(t,r){if(t<r)throw new e("Not enough arguments");return t}},5208:(t,e,r)=>{"use strict";var n=r(4577),o=r(1207),i=n.WeakMap;t.exports=o(i)&&/native code/.test(String(i))},9101:(t,e,r)=>{"use strict";var n=r(4577),o=r(1027),i=r(3863),s=r(2686),u=r(4185),c=r(3714),a=n.Symbol,p=o("wks"),f=c?a.for||a:a&&a.withoutSetter||s;t.exports=function(t){return i(p,t)||(p[t]=u&&i(a,t)?a[t]:f("Symbol."+t)),p[t]}},5261:(t,e,r)=>{"use strict";var n=r(4150),o=r(1182),i=r(8441),s=r(4822),u=URLSearchParams,c=u.prototype,a=o(c.append),p=o(c.delete),f=o(c.forEach),l=o([].push),v=new u("a=1&a=2&b=3");v.delete("a",1),v.delete("b",void 0),v+""!="a=2"&&n(c,"delete",(function(t){var e=arguments.length,r=e<2?void 0:arguments[1];if(e&&void 0===r)return p(this,t);var n=[];f(this,(function(t,e){l(n,{key:e,value:t})})),s(e,1);for(var o,u=i(t),c=i(r),v=0,b=0,d=!1,y=n.length;v<y;)o=n[v++],d||o.key===u?(d=!0,p(this,o.key)):b++;for(;b<y;)(o=n[b++]).key===u&&o.value===c||a(this,o.key,o.value)}),{enumerable:!0,unsafe:!0})},3184:(t,e,r)=>{"use strict";var n=r(4150),o=r(1182),i=r(8441),s=r(4822),u=URLSearchParams,c=u.prototype,a=o(c.getAll),p=o(c.has),f=new u("a=1");!f.has("a",2)&&f.has("a",void 0)||n(c,"has",(function(t){var e=arguments.length,r=e<2?void 0:arguments[1];if(e&&void 0===r)return p(this,t);var n=a(this,t);s(e,1);for(var o=i(r),u=0;u<n.length;)if(n[u++]===o)return!0;return!1}),{enumerable:!0,unsafe:!0})},7195:(t,e,r)=>{"use strict";var n=r(1458),o=r(1182),i=r(2372),s=URLSearchParams.prototype,u=o(s.forEach);n&&!("size"in s)&&i(s,"size",{get:function(){var t=0;return u(this,(function(){t++})),t},configurable:!0,enumerable:!0})}},e={};function r(n){var o=e[n];if(void 0!==o)return o.exports;var i=e[n]={exports:{}};return t[n].call(i.exports,i,i.exports,r),i.exports}r.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),r.r=t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},(()=>{"use strict";r(5261),r(3184),r(7195),window.LIPE_LIBS_BLOCK_EDITOR_CONFIG;const t=window.LIPE_LIBS_ADMIN_CONFIG;r(1669)((function(e){e('[data-js="lipe/lib/cmb2/box/tabs"]').on("click","a",(function(r){r.preventDefault();const n=e(this).parent(),o=n.data("panel"),i=n.parents(".cmb-tabs").find(".cmb2-wrap-tabs"),s=i.find('[class*="cmb-tab-panel-'+o+'"]');try{const r=e('[name="_wp_http_referer"]'),n=new URL(r.val()?.toString()??"");n.searchParams.set(t.cmb2BoxTabs.field,o),r.val(n.toString())}catch(t){console.error(t)}n.addClass("cmb-tab-active").siblings().removeClass("cmb-tab-active"),i.find(".cmb-tab-panel").removeClass("show"),s.addClass("show")}))})),r(1852),void 0!==window.LIPE_LIBS_CMB2_TERM_SELECT2&&r(936)})()})();
//# sourceMappingURL=admin.js.map