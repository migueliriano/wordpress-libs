/*! For license information please see meta-boxes.js.LICENSE.txt */
(()=>{var e={524:(e,t,n)=>{"use strict";n.d(t,{Cz:()=>c,hf:()=>l}),wp.apiFetch,n(470),wp.url,wp.blocks;const r=wp.plugins,o=wp.data;wp.richText;var s=n(594);function i(e,t,n){if(!e.s){if(n instanceof a){if(!n.s)return void(n.o=i.bind(null,e,t));1&t&&(t=n.s),n=n.v}if(n&&n.then)return void n.then(i.bind(null,e,t),i.bind(null,e,2));e.s=t,e.v=n;const r=e.o;r&&r(e)}}var a=function(){function e(){}return e.prototype.then=function(t,n){var r=new e,o=this.s;if(o){var s=1&o?t:n;if(s){try{i(r,1,s(this.v))}catch(e){i(r,2,e)}return r}return this}return this.o=function(e){try{var o=e.v;1&e.s?i(r,1,t?t(o):o):n?i(r,1,n(o)):i(r,2,o)}catch(e){i(r,2,e)}},r},e}(),c=function(e,t){u({afterReload:function(){},beforeReload:function(){},getContext:e,pluginModule:t,register:r.registerPlugin,unregister:r.unregisterPlugin,type:"plugin"})},u=function(e){var t=e.afterReload,n=e.beforeReload,r=e.getContext,o=e.pluginModule,s=e.register,i=e.unregister,a=e.type,c={},u=function(){n();var e=r();if(e){var o=[];return e.keys().forEach((function(t){var n=e(t);n.exclude||n!==c[t]&&(c[n.name+"-"+a]&&i(n.name),s(n.name,n.settings),o.push(n.name),c[n.name+"-"+a]=n)})),t(o),e}},l=u();o.hot&&null!=l&&l.id&&o.hot.accept(l.id.toString(),u)};function l(e){var t=(0,o.useDispatch)("core/editor").editPost,n=(0,o.useSelect)((function(t){var n=t("core").getTaxonomy(e);return n?{taxonomy:n,current:t("core/editor").getEditedPostAttribute(n.rest_base),previous:t("core/editor").getCurrentPostAttribute(n.rest_base)}:{current:[],previous:[]}})),r=(0,s.useCallback)((function(e){try{var r;return Promise.resolve(n.taxonomy?t(((r={})[n.taxonomy.rest_base]=e,r)):void 0)}catch(e){return Promise.reject(e)}}),[n,t]);return[n.current,r,n.previous]}},994:(e,t,n)=>{"use strict";n.r(t),n.d(t,{name:()=>j,settings:()=>T});const r={taxonomyMetaBoxes:window.LIPE_LIBS_META_BOXES??[]},o=wp.editPost,s=wp.coreData;var i=n(524);const a=wp.components;var c=n(470),u=n(534),l=n.n(u),d=n(666),p=n.n(d),f=n(845),m=n.n(f),v=n(246),h=n.n(v),y=n(650),g=n.n(y),b=n(956),x={};x.setAttributes=h(),x.insert=m().bind(null,"head"),x.domAPI=p(),x.insertStyleElement=g(),l()(b.A,x);const _=b.A&&b.A.locals?b.A.locals:void 0;var w=n(298);const A=({taxonomy:e,checkedOnTop:t})=>{const{record:n}=(0,s.useEntityRecord)("root","taxonomy",e),{records:r}=(0,s.useEntityRecords)("taxonomy",e,{}),[u,l]=(0,i.hf)(e);return t&&r?.sort((e=>u.includes(e.id)?-1:1)),(0,w.jsx)(o.PluginDocumentSettingPanel,{name:"lipe/libs/meta-boxes/radio-terms",title:n?.name??(0,c.__)("Loading...","lipe"),icon:void 0===n?.name?"download":null,children:(0,w.jsx)(a.PanelRow,{children:(0,w.jsx)(a.RadioControl,{className:_.control,selected:u[0]??"",options:r?.map((e=>({label:e.name,value:e.id})))??[],onChange:e=>{l(null===e?[]:[parseInt(e)])}})})})};var E=n(372),O={};O.setAttributes=h(),O.insert=m().bind(null,"head"),O.domAPI=p(),O.insertStyleElement=g(),l()(E.A,O);const S=E.A&&E.A.locals?E.A.locals:void 0,C=({})=>(0,w.jsx)("div",{className:S.wrap,children:"#### START HERE ###### Populate the Dropdown and Simple Terms components."}),P=({})=>(0,w.jsx)(w.Fragment,{children:r.taxonomyMetaBoxes?.map(((e,t)=>{switch(e.type){case"radio":return(0,w.jsx)(A,{taxonomy:e.taxonomy,checkedOnTop:e.checkedOnTop},t);case"dropdown":return(0,w.jsx)(C,{taxonomy:e.taxonomy,checkedOnTop:e.checkedOnTop},t)}return null}))}),j="lipe-libs-meta-boxes",T={render:()=>(0,w.jsx)(P,{})}},237:(e,t,n)=>{"use strict";var r=n(524);e=n.hmd(e),void 0!==window.wp?.editPost&&(0,r.Cz)((()=>n(136)),e)},372:(e,t,n)=>{"use strict";n.d(t,{A:()=>a});var r=n(386),o=n.n(r),s=n(498),i=n.n(s)()(o());i.push([e.id,".wp-libs_dropdown-terms_wrap_X4OBL{background:red}","",{version:3,sources:["webpack://./js/src/gutenberg/meta-boxes/Taxonomy/%3Cinput%20css%20tKfvCT%3E"],names:[],mappings:"AAAA,mCAAM,cAAc",sourcesContent:[".wrap{background:red}"],sourceRoot:""}]),i.locals={wrap:"wp-libs_dropdown-terms_wrap_X4OBL"};const a=i},956:(e,t,n)=>{"use strict";n.d(t,{A:()=>a});var r=n(386),o=n.n(r),s=n(498),i=n.n(s)()(o());i.push([e.id,".wp-libs_radio_control_hsGlA .components-radio-control__option{margin-bottom:.5em}.wp-libs_radio_control_hsGlA .components-radio-control__option input{margin-right:12px}","",{version:3,sources:["webpack://./js/src/gutenberg/meta-boxes/Taxonomy/%3Cinput%20css%20gW2An1%3E"],names:[],mappings:"AAAA,+DAAoD,kBAAkB,CAAC,qEAA0D,iBAAiB",sourcesContent:[".control :global(.components-radio-control__option){margin-bottom:.5em}.control :global(.components-radio-control__option) input{margin-right:12px}"],sourceRoot:""}]),i.locals={control:"wp-libs_radio_control_hsGlA"};const a=i},498:e=>{"use strict";e.exports=function(e){var t=[];return t.toString=function(){return this.map((function(t){var n="",r=void 0!==t[5];return t[4]&&(n+="@supports (".concat(t[4],") {")),t[2]&&(n+="@media ".concat(t[2]," {")),r&&(n+="@layer".concat(t[5].length>0?" ".concat(t[5]):""," {")),n+=e(t),r&&(n+="}"),t[2]&&(n+="}"),t[4]&&(n+="}"),n})).join("")},t.i=function(e,n,r,o,s){"string"==typeof e&&(e=[[null,e,void 0]]);var i={};if(r)for(var a=0;a<this.length;a++){var c=this[a][0];null!=c&&(i[c]=!0)}for(var u=0;u<e.length;u++){var l=[].concat(e[u]);r&&i[l[0]]||(void 0!==s&&(void 0===l[5]||(l[1]="@layer".concat(l[5].length>0?" ".concat(l[5]):""," {").concat(l[1],"}")),l[5]=s),n&&(l[2]?(l[1]="@media ".concat(l[2]," {").concat(l[1],"}"),l[2]=n):l[2]=n),o&&(l[4]?(l[1]="@supports (".concat(l[4],") {").concat(l[1],"}"),l[4]=o):l[4]="".concat(o)),t.push(l))}},t}},386:e=>{"use strict";e.exports=function(e){var t=e[1],n=e[3];if(!n)return t;if("function"==typeof btoa){var r=btoa(unescape(encodeURIComponent(JSON.stringify(n)))),o="sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(r),s="/*# ".concat(o," */"),i=n.sources.map((function(e){return"/*# sourceURL=".concat(n.sourceRoot||"").concat(e," */")}));return[t].concat(i).concat([s]).join("\n")}return[t].join("\n")}},534:e=>{"use strict";var t=[];function n(e){for(var n=-1,r=0;r<t.length;r++)if(t[r].identifier===e){n=r;break}return n}function r(e,r){for(var s={},i=[],a=0;a<e.length;a++){var c=e[a],u=r.base?c[0]+r.base:c[0],l=s[u]||0,d="".concat(u," ").concat(l);s[u]=l+1;var p=n(d),f={css:c[1],media:c[2],sourceMap:c[3],supports:c[4],layer:c[5]};if(-1!==p)t[p].references++,t[p].updater(f);else{var m=o(f,r);r.byIndex=a,t.splice(a,0,{identifier:d,updater:m,references:1})}i.push(d)}return i}function o(e,t){var n=t.domAPI(t);return n.update(e),function(t){if(t){if(t.css===e.css&&t.media===e.media&&t.sourceMap===e.sourceMap&&t.supports===e.supports&&t.layer===e.layer)return;n.update(e=t)}else n.remove()}}e.exports=function(e,o){var s=r(e=e||[],o=o||{});return function(e){e=e||[];for(var i=0;i<s.length;i++){var a=n(s[i]);t[a].references--}for(var c=r(e,o),u=0;u<s.length;u++){var l=n(s[u]);0===t[l].references&&(t[l].updater(),t.splice(l,1))}s=c}}},845:e=>{"use strict";var t={};e.exports=function(e,n){var r=function(e){if(void 0===t[e]){var n=document.querySelector(e);if(window.HTMLIFrameElement&&n instanceof window.HTMLIFrameElement)try{n=n.contentDocument.head}catch(e){n=null}t[e]=n}return t[e]}(e);if(!r)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");r.appendChild(n)}},650:e=>{"use strict";e.exports=function(e){var t=document.createElement("style");return e.setAttributes(t,e.attributes),e.insert(t,e.options),t}},246:(e,t,n)=>{"use strict";e.exports=function(e){var t=n.nc;t&&e.setAttribute("nonce",t)}},666:e=>{"use strict";var t,n=(t=[],function(e,n){return t[e]=n,t.filter(Boolean).join("\n")});function r(e,t,r,o){var s;if(r)s="";else{s="",o.supports&&(s+="@supports (".concat(o.supports,") {")),o.media&&(s+="@media ".concat(o.media," {"));var i=void 0!==o.layer;i&&(s+="@layer".concat(o.layer.length>0?" ".concat(o.layer):""," {")),s+=o.css,i&&(s+="}"),o.media&&(s+="}"),o.supports&&(s+="}")}if(e.styleSheet)e.styleSheet.cssText=n(t,s);else{var a=document.createTextNode(s),c=e.childNodes;c[t]&&e.removeChild(c[t]),c.length?e.insertBefore(a,c[t]):e.appendChild(a)}}var o={singleton:null,singletonCounter:0};e.exports=function(e){if("undefined"==typeof document)return{update:function(){},remove:function(){}};var t=o.singletonCounter++,n=o.singleton||(o.singleton=e.insertStyleElement(e));return{update:function(e){r(n,t,!1,e)},remove:function(e){r(n,t,!0,e)}}}},394:(e,t,n)=>{"use strict";var r=n(594),o=Symbol.for("react.element"),s=Symbol.for("react.fragment"),i=Object.prototype.hasOwnProperty,a=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,c={key:!0,ref:!0,__self:!0,__source:!0};t.Fragment=s,t.jsx=function(e,t,n){var r,s={},u=null,l=null;for(r in void 0!==n&&(u=""+n),void 0!==t.key&&(u=""+t.key),void 0!==t.ref&&(l=t.ref),t)i.call(t,r)&&!c.hasOwnProperty(r)&&(s[r]=t[r]);if(e&&e.defaultProps)for(r in t=e.defaultProps)void 0===s[r]&&(s[r]=t[r]);return{$$typeof:o,type:e,key:u,ref:l,props:s,_owner:a.current}}},298:(e,t,n)=>{"use strict";e.exports=n(394)},136:(e,t,n)=>{var r={"./index.tsx":994,"gutenberg/meta-boxes/index.tsx":994};function o(e){var t=s(e);return n(t)}function s(e){if(!n.o(r,e)){var t=new Error("Cannot find module '"+e+"'");throw t.code="MODULE_NOT_FOUND",t}return r[e]}o.keys=function(){return Object.keys(r)},o.resolve=s,e.exports=o,o.id=136},594:e=>{"use strict";e.exports=React},470:e=>{"use strict";e.exports=wp.i18n}},t={};function n(r){var o=t[r];if(void 0!==o)return o.exports;var s=t[r]={id:r,loaded:!1,exports:{}};return e[r](s,s.exports,n),s.loaded=!0,s.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.hmd=e=>((e=Object.create(e)).children||(e.children=[]),Object.defineProperty(e,"exports",{enumerable:!0,set:()=>{throw new Error("ES Modules may not assign module.exports or exports.*, Use ESM export syntax, instead: "+e.id)}}),e),n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.nc=void 0,n(237)})();
//# sourceMappingURL=meta-boxes.js.map