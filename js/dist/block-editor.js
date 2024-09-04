/*! For license information please see block-editor.js.LICENSE.txt */
(()=>{var t={3517:(t,e,r)=>{"use strict";r.d(e,{Cz:()=>a,hf:()=>p}),wp.apiFetch,r(2470),wp.url,wp.blocks;const n=wp.plugins;var o=r(7562);wp.richText;var i=r(1594);function s(t,e,r){if(!t.s){if(r instanceof c){if(!r.s)return void(r.o=s.bind(null,t,e));1&e&&(e=r.s),r=r.v}if(r&&r.then)return void r.then(s.bind(null,t,e),s.bind(null,t,2));t.s=e,t.v=r;const n=t.o;n&&n(t)}}var c=function(){function t(){}return t.prototype.then=function(e,r){var n=new t,o=this.s;if(o){var i=1&o?e:r;if(i){try{s(n,1,i(this.v))}catch(t){s(n,2,t)}return n}return this}return this.o=function(t){try{var o=t.v;1&t.s?s(n,1,e?e(o):o):r?s(n,1,r(o)):s(n,2,o)}catch(t){s(n,2,t)}},n},t}(),a=function(t,e){u({afterReload:function(){},beforeReload:function(){},getContext:t,pluginModule:e,register:n.registerPlugin,unregister:n.unregisterPlugin,type:"plugin"})},u=function(t){var e=t.afterReload,r=t.beforeReload,n=t.getContext,o=t.pluginModule,i=t.register,s=t.unregister,c=t.type,a={},u=function(){r();var t=n();if(t){var o=[];return t.keys().forEach((function(e){var r=t(e);r.exclude||r!==a[e]&&(a[r.name+"-"+c]&&s(r.name),i(r.name,r.settings),o.push(r.name),a[r.name+"-"+c]=r)})),e(o),t}},p=u();o.hot&&null!=p&&p.id&&o.hot.accept(p.id.toString(),u)};function p(t){var e=(0,o.useDispatch)("core/editor").editPost,r=(0,o.useSelect)((function(e){var r=e("core").getTaxonomy(t);return r?{taxonomy:r,current:e("core/editor").getEditedPostAttribute(r.rest_base),previous:e("core/editor").getCurrentPostAttribute(r.rest_base)}:{current:[],previous:[]}})),n=(0,i.useCallback)((function(t){try{var n;return Promise.resolve(r.taxonomy?e(((n={})[r.taxonomy.rest_base]=t,n)):void 0)}catch(t){return Promise.reject(t)}}),[r,e]);return[r.current,n,r.previous]}},9561:(t,e,r)=>{"use strict";var n=r(3517);t=r.hmd(t),void 0!==window.wp?.editPost&&(0,n.Cz)((()=>r(9136)),t)},9089:(t,e,r)=>{"use strict";r.r(e),r.d(e,{name:()=>C,settings:()=>T});const n=window.LIPE_LIBS_BLOCK_EDITOR_CONFIG;window.LIPE_LIBS_ADMIN_CONFIG,r(3768);const o=wp.components;var i=r(2534),s=r.n(i),c=r(6666),a=r.n(c),u=r(3845),p=r.n(u),l=r(6246),f=r.n(l),d=r(5650),v=r.n(d),y=r(1956),m={};m.setAttributes=f(),m.insert=p().bind(null,"head"),m.domAPI=a(),m.insertStyleElement=v(),s()(y.A,m);const h=y.A&&y.A.locals?y.A.locals:void 0;var g=r(8298);const b=({assigned:t,setAssigned:e,terms:r,tax:n})=>{const i=r.map((t=>({label:t.name,value:t.id.toString()})));return i.push({label:n?.labels.no_terms??"None",value:"0"}),(0,g.jsx)(o.RadioControl,{className:h.control,selected:(t[0]??0).toString(),options:i,onChange:t=>{e("0"===t?[]:[parseInt(t)])}})},x=({assigned:t,setAssigned:e,terms:r,tax:n})=>{const i=r.map((t=>({label:t.name,value:t.id.toString()})));return i.unshift({label:`- ${n?.labels.no_terms??"None"} -`,value:"0"}),(0,g.jsx)(o.SelectControl,{value:(t[0]??0).toString(),options:i,onChange:t=>{e("0"===t?[]:[parseInt(t)])}})},w=({assigned:t,setAssigned:e,terms:r,tax:n})=>(0,g.jsxs)(g.Fragment,{children:[r.map((r=>(0,g.jsx)("div",{children:(0,g.jsx)(o.CheckboxControl,{checked:t.includes(r.id),label:r.name,onChange:n=>{e(n?[...t,r.id]:t.filter((t=>t!==r.id)))}})},r.id))),(0,g.jsx)("div",{style:{borderTop:"1px solid #eee",color:"#888",marginTop:"12px",paddingTop:"12px"},children:(0,g.jsx)(o.CheckboxControl,{checked:0===t.length,label:n?.labels.no_terms??"None",onChange:t=>{e(t?[]:r.map((t=>t.id)))}})})]});var O=r(2470);const S=wp.coreData;var j=r(1594),_=r(3517),A=r(7562);const P=window.wp.editor?.PluginDocumentSettingPanel??window.wp.editPost.PluginDocumentSettingPanel;const E=({})=>(0,g.jsx)(g.Fragment,{children:n.taxonomyMetaBoxes?.map((t=>{let e=null;switch(t.type){case"radio":e=b;break;case"dropdown":e=x;break;case"simple":e=w}return function(t,e){const{taxonomy:r,checkedOnTop:n}=e,{record:o}=(0,S.useEntityRecord)("root","taxonomy",r),{records:i}=(0,S.useEntityRecords)("taxonomy",r,{per_page:100}),[s,c]=(0,_.hf)(r);return(0,j.useEffect)((()=>{!function(t){"function"==typeof(0,A.dispatch)("core/editor").removeEditorPanel?(0,A.dispatch)("core/editor").removeEditorPanel((0,O.sprintf)("taxonomy-panel-%1$s",t)):(0,A.dispatch)("core/edit-post").removeEditorPanel((0,O.sprintf)("taxonomy-panel-%1$s",t))}(r)}),[r]),n&&Array.isArray(i)&&i.sort(((t,e)=>{const r=s.includes(t.id);return r!==s.includes(e.id)?r?-1:1:t.name.localeCompare(e.name)})),(0,g.jsx)(P,{name:(0,O.sprintf)("lipe/libs/meta-boxes/taxonomy/%1$s",r),title:o?.name??(0,O.__)("Loading…","lipe"),icon:void 0===o?.name?"download":null,children:(0,g.jsx)(t,{tax:o,terms:i??[],assigned:s,setAssigned:c})},r)}(e,{taxonomy:t.taxonomy,checkedOnTop:t.checkedOnTop})}))}),C="lipe-libs-meta-boxes",T={render:()=>(0,g.jsx)(E,{})}},1956:(t,e,r)=>{"use strict";r.d(e,{A:()=>c});var n=r(6386),o=r.n(n),i=r(6498),s=r.n(i)()(o());s.push([t.id,".wp-libs_radio_control_hsGlA .components-radio-control__option{margin-bottom:.5em}.wp-libs_radio_control_hsGlA .components-radio-control__option input{margin-right:12px}.wp-libs_radio_control_hsGlA .components-radio-control__option:last-child{border-top:1px solid #eee;color:#888;margin-top:5px;padding-top:12px}","",{version:3,sources:["webpack://./js/src/gutenberg/meta-boxes/Taxonomy/%3Cinput%20css%206-BjXq%3E"],names:[],mappings:"AAAA,+DAAoD,kBAAkB,CAAC,qEAA0D,iBAAiB,CAAC,0EAA+D,yBAAyB,CAAC,UAAU,CAAC,cAAc,CAAC,gBAAgB",sourcesContent:[".control :global(.components-radio-control__option){margin-bottom:.5em}.control :global(.components-radio-control__option) input{margin-right:12px}.control :global(.components-radio-control__option):last-child{border-top:1px solid #eee;color:#888;margin-top:5px;padding-top:12px}"],sourceRoot:""}]),s.locals={control:"wp-libs_radio_control_hsGlA"};const c=s},6498:t=>{"use strict";t.exports=function(t){var e=[];return e.toString=function(){return this.map((function(e){var r="",n=void 0!==e[5];return e[4]&&(r+="@supports (".concat(e[4],") {")),e[2]&&(r+="@media ".concat(e[2]," {")),n&&(r+="@layer".concat(e[5].length>0?" ".concat(e[5]):""," {")),r+=t(e),n&&(r+="}"),e[2]&&(r+="}"),e[4]&&(r+="}"),r})).join("")},e.i=function(t,r,n,o,i){"string"==typeof t&&(t=[[null,t,void 0]]);var s={};if(n)for(var c=0;c<this.length;c++){var a=this[c][0];null!=a&&(s[a]=!0)}for(var u=0;u<t.length;u++){var p=[].concat(t[u]);n&&s[p[0]]||(void 0!==i&&(void 0===p[5]||(p[1]="@layer".concat(p[5].length>0?" ".concat(p[5]):""," {").concat(p[1],"}")),p[5]=i),r&&(p[2]?(p[1]="@media ".concat(p[2]," {").concat(p[1],"}"),p[2]=r):p[2]=r),o&&(p[4]?(p[1]="@supports (".concat(p[4],") {").concat(p[1],"}"),p[4]=o):p[4]="".concat(o)),e.push(p))}},e}},6386:t=>{"use strict";t.exports=function(t){var e=t[1],r=t[3];if(!r)return e;if("function"==typeof btoa){var n=btoa(unescape(encodeURIComponent(JSON.stringify(r)))),o="sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(n),i="/*# ".concat(o," */"),s=r.sources.map((function(t){return"/*# sourceURL=".concat(r.sourceRoot||"").concat(t," */")}));return[e].concat(s).concat([i]).join("\n")}return[e].join("\n")}},2534:t=>{"use strict";var e=[];function r(t){for(var r=-1,n=0;n<e.length;n++)if(e[n].identifier===t){r=n;break}return r}function n(t,n){for(var i={},s=[],c=0;c<t.length;c++){var a=t[c],u=n.base?a[0]+n.base:a[0],p=i[u]||0,l="".concat(u," ").concat(p);i[u]=p+1;var f=r(l),d={css:a[1],media:a[2],sourceMap:a[3],supports:a[4],layer:a[5]};if(-1!==f)e[f].references++,e[f].updater(d);else{var v=o(d,n);n.byIndex=c,e.splice(c,0,{identifier:l,updater:v,references:1})}s.push(l)}return s}function o(t,e){var r=e.domAPI(e);return r.update(t),function(e){if(e){if(e.css===t.css&&e.media===t.media&&e.sourceMap===t.sourceMap&&e.supports===t.supports&&e.layer===t.layer)return;r.update(t=e)}else r.remove()}}t.exports=function(t,o){var i=n(t=t||[],o=o||{});return function(t){t=t||[];for(var s=0;s<i.length;s++){var c=r(i[s]);e[c].references--}for(var a=n(t,o),u=0;u<i.length;u++){var p=r(i[u]);0===e[p].references&&(e[p].updater(),e.splice(p,1))}i=a}}},3845:t=>{"use strict";var e={};t.exports=function(t,r){var n=function(t){if(void 0===e[t]){var r=document.querySelector(t);if(window.HTMLIFrameElement&&r instanceof window.HTMLIFrameElement)try{r=r.contentDocument.head}catch(t){r=null}e[t]=r}return e[t]}(t);if(!n)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");n.appendChild(r)}},5650:t=>{"use strict";t.exports=function(t){var e=document.createElement("style");return t.setAttributes(e,t.attributes),t.insert(e,t.options),e}},6246:(t,e,r)=>{"use strict";t.exports=function(t){var e=r.nc;e&&t.setAttribute("nonce",e)}},6666:t=>{"use strict";var e,r=(e=[],function(t,r){return e[t]=r,e.filter(Boolean).join("\n")});function n(t,e,n,o){var i;if(n)i="";else{i="",o.supports&&(i+="@supports (".concat(o.supports,") {")),o.media&&(i+="@media ".concat(o.media," {"));var s=void 0!==o.layer;s&&(i+="@layer".concat(o.layer.length>0?" ".concat(o.layer):""," {")),i+=o.css,s&&(i+="}"),o.media&&(i+="}"),o.supports&&(i+="}")}if(t.styleSheet)t.styleSheet.cssText=r(e,i);else{var c=document.createTextNode(i),a=t.childNodes;a[e]&&t.removeChild(a[e]),a.length?t.insertBefore(c,a[e]):t.appendChild(c)}}var o={singleton:null,singletonCounter:0};t.exports=function(t){if("undefined"==typeof document)return{update:function(){},remove:function(){}};var e=o.singletonCounter++,r=o.singleton||(o.singleton=t.insertStyleElement(t));return{update:function(t){n(r,e,!1,t)},remove:function(t){n(r,e,!0,t)}}}},7394:(t,e,r)=>{"use strict";var n=r(1594),o=Symbol.for("react.element"),i=Symbol.for("react.fragment"),s=Object.prototype.hasOwnProperty,c=n.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,a={key:!0,ref:!0,__self:!0,__source:!0};function u(t,e,r){var n,i={},u=null,p=null;for(n in void 0!==r&&(u=""+r),void 0!==e.key&&(u=""+e.key),void 0!==e.ref&&(p=e.ref),e)s.call(e,n)&&!a.hasOwnProperty(n)&&(i[n]=e[n]);if(t&&t.defaultProps)for(n in e=t.defaultProps)void 0===i[n]&&(i[n]=e[n]);return{$$typeof:o,type:t,key:u,ref:p,props:i,_owner:c.current}}e.Fragment=i,e.jsx=u,e.jsxs=u},8298:(t,e,r)=>{"use strict";t.exports=r(7394)},9136:(t,e,r)=>{var n={"./index.tsx":9089,"gutenberg/meta-boxes/index.tsx":9089};function o(t){var e=i(t);return r(e)}function i(t){if(!r.o(n,t)){var e=new Error("Cannot find module '"+t+"'");throw e.code="MODULE_NOT_FOUND",e}return n[t]}o.keys=function(){return Object.keys(n)},o.resolve=i,t.exports=o,o.id=9136},1594:t=>{"use strict";t.exports=React},7562:t=>{"use strict";t.exports=wp.data},2470:t=>{"use strict";t.exports=wp.i18n},1468:(t,e,r)=>{"use strict";var n=r(1207),o=r(8381),i=TypeError;t.exports=function(t){if(n(t))return t;throw new i(o(t)+" is not a function")}},6577:(t,e,r)=>{"use strict";var n=r(9340),o=String,i=TypeError;t.exports=function(t){if(n(t))return t;throw new i(o(t)+" is not an object")}},9159:(t,e,r)=>{"use strict";var n=r(8803),o=r(5380),i=r(8172),s=function(t){return function(e,r,s){var c=n(e),a=i(c);if(0===a)return!t&&-1;var u,p=o(s,a);if(t&&r!=r){for(;a>p;)if((u=c[p++])!=u)return!0}else for(;a>p;p++)if((t||p in c)&&c[p]===r)return t||p||0;return!t&&-1}};t.exports={includes:s(!0),indexOf:s(!1)}},4541:(t,e,r)=>{"use strict";var n=r(1458),o=r(6598),i=TypeError,s=Object.getOwnPropertyDescriptor,c=n&&!function(){if(void 0!==this)return!0;try{Object.defineProperty([],"length",{writable:!1}).length=1}catch(t){return t instanceof TypeError}}();t.exports=c?function(t,e){if(o(t)&&!s(t,"length").writable)throw new i("Cannot set read only .length");return t.length=e}:function(t,e){return t.length=e}},3114:(t,e,r)=>{"use strict";var n=r(1182),o=n({}.toString),i=n("".slice);t.exports=function(t){return i(o(t),8,-1)}},2226:(t,e,r)=>{"use strict";var n=r(3863),o=r(6653),i=r(2253),s=r(2127);t.exports=function(t,e,r){for(var c=o(e),a=s.f,u=i.f,p=0;p<c.length;p++){var l=c[p];n(t,l)||r&&n(r,l)||a(t,l,u(e,l))}}},6641:(t,e,r)=>{"use strict";var n=r(1458),o=r(2127),i=r(3158);t.exports=n?function(t,e,r){return o.f(t,e,i(1,r))}:function(t,e,r){return t[e]=r,t}},3158:t=>{"use strict";t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e}}},4150:(t,e,r)=>{"use strict";var n=r(1207),o=r(2127),i=r(1293),s=r(6827);t.exports=function(t,e,r,c){c||(c={});var a=c.enumerable,u=void 0!==c.name?c.name:e;if(n(r)&&i(r,u,c),c.global)a?t[e]=r:s(e,r);else{try{c.unsafe?t[e]&&(a=!0):delete t[e]}catch(t){}a?t[e]=r:o.f(t,e,{value:r,enumerable:!1,configurable:!c.nonConfigurable,writable:!c.nonWritable})}return t}},6827:(t,e,r)=>{"use strict";var n=r(4577),o=Object.defineProperty;t.exports=function(t,e){try{o(n,t,{value:e,configurable:!0,writable:!0})}catch(r){n[t]=e}return e}},1458:(t,e,r)=>{"use strict";var n=r(5421);t.exports=!n((function(){return 7!==Object.defineProperty({},1,{get:function(){return 7}})[1]}))},6341:(t,e,r)=>{"use strict";var n=r(4577),o=r(9340),i=n.document,s=o(i)&&o(i.createElement);t.exports=function(t){return s?i.createElement(t):{}}},7847:t=>{"use strict";var e=TypeError;t.exports=function(t){if(t>9007199254740991)throw e("Maximum allowed index exceeded");return t}},6506:t=>{"use strict";t.exports="undefined"!=typeof navigator&&String(navigator.userAgent)||""},3694:(t,e,r)=>{"use strict";var n,o,i=r(4577),s=r(6506),c=i.process,a=i.Deno,u=c&&c.versions||a&&a.version,p=u&&u.v8;p&&(o=(n=p.split("."))[0]>0&&n[0]<4?1:+(n[0]+n[1])),!o&&s&&(!(n=s.match(/Edge\/(\d+)/))||n[1]>=74)&&(n=s.match(/Chrome\/(\d+)/))&&(o=+n[1]),t.exports=o},4529:t=>{"use strict";t.exports=["constructor","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","toLocaleString","toString","valueOf"]},296:(t,e,r)=>{"use strict";var n=r(4577),o=r(2253).f,i=r(6641),s=r(4150),c=r(6827),a=r(2226),u=r(438);t.exports=function(t,e){var r,p,l,f,d,v=t.target,y=t.global,m=t.stat;if(r=y?n:m?n[v]||c(v,{}):n[v]&&n[v].prototype)for(p in e){if(f=e[p],l=t.dontCallGetSet?(d=o(r,p))&&d.value:r[p],!u(y?p:v+(m?".":"#")+p,t.forced)&&void 0!==l){if(typeof f==typeof l)continue;a(f,l)}(t.sham||l&&l.sham)&&i(f,"sham",!0),s(r,p,f,t)}}},5421:t=>{"use strict";t.exports=function(t){try{return!!t()}catch(t){return!0}}},6318:(t,e,r)=>{"use strict";var n=r(5421);t.exports=!n((function(){var t=function(){}.bind();return"function"!=typeof t||t.hasOwnProperty("prototype")}))},3227:(t,e,r)=>{"use strict";var n=r(6318),o=Function.prototype.call;t.exports=n?o.bind(o):function(){return o.apply(o,arguments)}},9924:(t,e,r)=>{"use strict";var n=r(1458),o=r(3863),i=Function.prototype,s=n&&Object.getOwnPropertyDescriptor,c=o(i,"name"),a=c&&"something"===function(){}.name,u=c&&(!n||n&&s(i,"name").configurable);t.exports={EXISTS:c,PROPER:a,CONFIGURABLE:u}},1182:(t,e,r)=>{"use strict";var n=r(6318),o=Function.prototype,i=o.call,s=n&&o.bind.bind(i,i);t.exports=n?s:function(t){return function(){return i.apply(t,arguments)}}},2677:(t,e,r)=>{"use strict";var n=r(4577),o=r(1207);t.exports=function(t,e){return arguments.length<2?(r=n[t],o(r)?r:void 0):n[t]&&n[t][e];var r}},440:(t,e,r)=>{"use strict";var n=r(1468),o=r(1547);t.exports=function(t,e){var r=t[e];return o(r)?void 0:n(r)}},4577:function(t,e,r){"use strict";var n=function(t){return t&&t.Math===Math&&t};t.exports=n("object"==typeof globalThis&&globalThis)||n("object"==typeof window&&window)||n("object"==typeof self&&self)||n("object"==typeof r.g&&r.g)||n("object"==typeof this&&this)||function(){return this}()||Function("return this")()},3863:(t,e,r)=>{"use strict";var n=r(1182),o=r(2847),i=n({}.hasOwnProperty);t.exports=Object.hasOwn||function(t,e){return i(o(t),e)}},775:t=>{"use strict";t.exports={}},7675:(t,e,r)=>{"use strict";var n=r(1458),o=r(5421),i=r(6341);t.exports=!n&&!o((function(){return 7!==Object.defineProperty(i("div"),"a",{get:function(){return 7}}).a}))},6781:(t,e,r)=>{"use strict";var n=r(1182),o=r(5421),i=r(3114),s=Object,c=n("".split);t.exports=o((function(){return!s("z").propertyIsEnumerable(0)}))?function(t){return"String"===i(t)?c(t,""):s(t)}:s},696:(t,e,r)=>{"use strict";var n=r(1182),o=r(1207),i=r(1731),s=n(Function.toString);o(i.inspectSource)||(i.inspectSource=function(t){return s(t)}),t.exports=i.inspectSource},9079:(t,e,r)=>{"use strict";var n,o,i,s=r(5208),c=r(4577),a=r(9340),u=r(6641),p=r(3863),l=r(1731),f=r(7581),d=r(775),v="Object already initialized",y=c.TypeError,m=c.WeakMap;if(s||l.state){var h=l.state||(l.state=new m);h.get=h.get,h.has=h.has,h.set=h.set,n=function(t,e){if(h.has(t))throw new y(v);return e.facade=t,h.set(t,e),e},o=function(t){return h.get(t)||{}},i=function(t){return h.has(t)}}else{var g=f("state");d[g]=!0,n=function(t,e){if(p(t,g))throw new y(v);return e.facade=t,u(t,g,e),e},o=function(t){return p(t,g)?t[g]:{}},i=function(t){return p(t,g)}}t.exports={set:n,get:o,has:i,enforce:function(t){return i(t)?o(t):n(t,{})},getterFor:function(t){return function(e){var r;if(!a(e)||(r=o(e)).type!==t)throw new y("Incompatible receiver, "+t+" required");return r}}}},6598:(t,e,r)=>{"use strict";var n=r(3114);t.exports=Array.isArray||function(t){return"Array"===n(t)}},1207:t=>{"use strict";var e="object"==typeof document&&document.all;t.exports=void 0===e&&void 0!==e?function(t){return"function"==typeof t||t===e}:function(t){return"function"==typeof t}},438:(t,e,r)=>{"use strict";var n=r(5421),o=r(1207),i=/#|\.prototype\./,s=function(t,e){var r=a[c(t)];return r===p||r!==u&&(o(e)?n(e):!!e)},c=s.normalize=function(t){return String(t).replace(i,".").toLowerCase()},a=s.data={},u=s.NATIVE="N",p=s.POLYFILL="P";t.exports=s},1547:t=>{"use strict";t.exports=function(t){return null==t}},9340:(t,e,r)=>{"use strict";var n=r(1207);t.exports=function(t){return"object"==typeof t?null!==t:n(t)}},5329:t=>{"use strict";t.exports=!1},6563:(t,e,r)=>{"use strict";var n=r(2677),o=r(1207),i=r(7443),s=r(3714),c=Object;t.exports=s?function(t){return"symbol"==typeof t}:function(t){var e=n("Symbol");return o(e)&&i(e.prototype,c(t))}},8172:(t,e,r)=>{"use strict";var n=r(2920);t.exports=function(t){return n(t.length)}},1293:(t,e,r)=>{"use strict";var n=r(1182),o=r(5421),i=r(1207),s=r(3863),c=r(1458),a=r(9924).CONFIGURABLE,u=r(696),p=r(9079),l=p.enforce,f=p.get,d=String,v=Object.defineProperty,y=n("".slice),m=n("".replace),h=n([].join),g=c&&!o((function(){return 8!==v((function(){}),"length",{value:8}).length})),b=String(String).split("String"),x=t.exports=function(t,e,r){"Symbol("===y(d(e),0,7)&&(e="["+m(d(e),/^Symbol\(([^)]*)\).*$/,"$1")+"]"),r&&r.getter&&(e="get "+e),r&&r.setter&&(e="set "+e),(!s(t,"name")||a&&t.name!==e)&&(c?v(t,"name",{value:e,configurable:!0}):t.name=e),g&&r&&s(r,"arity")&&t.length!==r.arity&&v(t,"length",{value:r.arity});try{r&&s(r,"constructor")&&r.constructor?c&&v(t,"prototype",{writable:!1}):t.prototype&&(t.prototype=void 0)}catch(t){}var n=l(t);return s(n,"source")||(n.source=h(b,"string"==typeof e?e:"")),t};Function.prototype.toString=x((function(){return i(this)&&f(this).source||u(this)}),"toString")},1267:t=>{"use strict";var e=Math.ceil,r=Math.floor;t.exports=Math.trunc||function(t){var n=+t;return(n>0?r:e)(n)}},2127:(t,e,r)=>{"use strict";var n=r(1458),o=r(7675),i=r(4956),s=r(6577),c=r(5515),a=TypeError,u=Object.defineProperty,p=Object.getOwnPropertyDescriptor,l="enumerable",f="configurable",d="writable";e.f=n?i?function(t,e,r){if(s(t),e=c(e),s(r),"function"==typeof t&&"prototype"===e&&"value"in r&&d in r&&!r[d]){var n=p(t,e);n&&n[d]&&(t[e]=r.value,r={configurable:f in r?r[f]:n[f],enumerable:l in r?r[l]:n[l],writable:!1})}return u(t,e,r)}:u:function(t,e,r){if(s(t),e=c(e),s(r),o)try{return u(t,e,r)}catch(t){}if("get"in r||"set"in r)throw new a("Accessors not supported");return"value"in r&&(t[e]=r.value),t}},2253:(t,e,r)=>{"use strict";var n=r(1458),o=r(3227),i=r(5487),s=r(3158),c=r(8803),a=r(5515),u=r(3863),p=r(7675),l=Object.getOwnPropertyDescriptor;e.f=n?l:function(t,e){if(t=c(t),e=a(e),p)try{return l(t,e)}catch(t){}if(u(t,e))return s(!o(i.f,t,e),t[e])}},9074:(t,e,r)=>{"use strict";var n=r(5130),o=r(4529).concat("length","prototype");e.f=Object.getOwnPropertyNames||function(t){return n(t,o)}},8023:(t,e)=>{"use strict";e.f=Object.getOwnPropertySymbols},7443:(t,e,r)=>{"use strict";var n=r(1182);t.exports=n({}.isPrototypeOf)},5130:(t,e,r)=>{"use strict";var n=r(1182),o=r(3863),i=r(8803),s=r(9159).indexOf,c=r(775),a=n([].push);t.exports=function(t,e){var r,n=i(t),u=0,p=[];for(r in n)!o(c,r)&&o(n,r)&&a(p,r);for(;e.length>u;)o(n,r=e[u++])&&(~s(p,r)||a(p,r));return p}},5487:(t,e)=>{"use strict";var r={}.propertyIsEnumerable,n=Object.getOwnPropertyDescriptor,o=n&&!r.call({1:2},1);e.f=o?function(t){var e=n(this,t);return!!e&&e.enumerable}:r},152:(t,e,r)=>{"use strict";var n=r(3227),o=r(1207),i=r(9340),s=TypeError;t.exports=function(t,e){var r,c;if("string"===e&&o(r=t.toString)&&!i(c=n(r,t)))return c;if(o(r=t.valueOf)&&!i(c=n(r,t)))return c;if("string"!==e&&o(r=t.toString)&&!i(c=n(r,t)))return c;throw new s("Can't convert object to primitive value")}},6653:(t,e,r)=>{"use strict";var n=r(2677),o=r(1182),i=r(9074),s=r(8023),c=r(6577),a=o([].concat);t.exports=n("Reflect","ownKeys")||function(t){var e=i.f(c(t)),r=s.f;return r?a(e,r(t)):e}},1484:(t,e,r)=>{"use strict";var n=r(1547),o=TypeError;t.exports=function(t){if(n(t))throw new o("Can't call method on "+t);return t}},7581:(t,e,r)=>{"use strict";var n=r(1027),o=r(2686),i=n("keys");t.exports=function(t){return i[t]||(i[t]=o(t))}},1731:(t,e,r)=>{"use strict";var n=r(5329),o=r(4577),i=r(6827),s="__core-js_shared__",c=t.exports=o[s]||i(s,{});(c.versions||(c.versions=[])).push({version:"3.37.1",mode:n?"pure":"global",copyright:"© 2014-2024 Denis Pushkarev (zloirock.ru)",license:"https://github.com/zloirock/core-js/blob/v3.37.1/LICENSE",source:"https://github.com/zloirock/core-js"})},1027:(t,e,r)=>{"use strict";var n=r(1731);t.exports=function(t,e){return n[t]||(n[t]=e||{})}},4185:(t,e,r)=>{"use strict";var n=r(3694),o=r(5421),i=r(4577).String;t.exports=!!Object.getOwnPropertySymbols&&!o((function(){var t=Symbol("symbol detection");return!i(t)||!(Object(t)instanceof Symbol)||!Symbol.sham&&n&&n<41}))},5380:(t,e,r)=>{"use strict";var n=r(7425),o=Math.max,i=Math.min;t.exports=function(t,e){var r=n(t);return r<0?o(r+e,0):i(r,e)}},8803:(t,e,r)=>{"use strict";var n=r(6781),o=r(1484);t.exports=function(t){return n(o(t))}},7425:(t,e,r)=>{"use strict";var n=r(1267);t.exports=function(t){var e=+t;return e!=e||0===e?0:n(e)}},2920:(t,e,r)=>{"use strict";var n=r(7425),o=Math.min;t.exports=function(t){var e=n(t);return e>0?o(e,9007199254740991):0}},2847:(t,e,r)=>{"use strict";var n=r(1484),o=Object;t.exports=function(t){return o(n(t))}},2007:(t,e,r)=>{"use strict";var n=r(3227),o=r(9340),i=r(6563),s=r(440),c=r(152),a=r(9101),u=TypeError,p=a("toPrimitive");t.exports=function(t,e){if(!o(t)||i(t))return t;var r,a=s(t,p);if(a){if(void 0===e&&(e="default"),r=n(a,t,e),!o(r)||i(r))return r;throw new u("Can't convert object to primitive value")}return void 0===e&&(e="number"),c(t,e)}},5515:(t,e,r)=>{"use strict";var n=r(2007),o=r(6563);t.exports=function(t){var e=n(t,"string");return o(e)?e:e+""}},8381:t=>{"use strict";var e=String;t.exports=function(t){try{return e(t)}catch(t){return"Object"}}},2686:(t,e,r)=>{"use strict";var n=r(1182),o=0,i=Math.random(),s=n(1..toString);t.exports=function(t){return"Symbol("+(void 0===t?"":t)+")_"+s(++o+i,36)}},3714:(t,e,r)=>{"use strict";var n=r(4185);t.exports=n&&!Symbol.sham&&"symbol"==typeof Symbol.iterator},4956:(t,e,r)=>{"use strict";var n=r(1458),o=r(5421);t.exports=n&&o((function(){return 42!==Object.defineProperty((function(){}),"prototype",{value:42,writable:!1}).prototype}))},5208:(t,e,r)=>{"use strict";var n=r(4577),o=r(1207),i=n.WeakMap;t.exports=o(i)&&/native code/.test(String(i))},9101:(t,e,r)=>{"use strict";var n=r(4577),o=r(1027),i=r(3863),s=r(2686),c=r(4185),a=r(3714),u=n.Symbol,p=o("wks"),l=a?u.for||u:u&&u.withoutSetter||s;t.exports=function(t){return i(p,t)||(p[t]=c&&i(u,t)?u[t]:l("Symbol."+t)),p[t]}},3768:(t,e,r)=>{"use strict";var n=r(296),o=r(2847),i=r(8172),s=r(4541),c=r(7847);n({target:"Array",proto:!0,arity:1,forced:r(5421)((function(){return 4294967297!==[].push.call({length:4294967296},1)}))||!function(){try{Object.defineProperty([],"length",{writable:!1}).push()}catch(t){return t instanceof TypeError}}()},{push:function(t){var e=o(this),r=i(e),n=arguments.length;c(r+n);for(var a=0;a<n;a++)e[r]=arguments[a],r++;return s(e,r),r}})}},e={};function r(n){var o=e[n];if(void 0!==o)return o.exports;var i=e[n]={id:n,loaded:!1,exports:{}};return t[n].call(i.exports,i,i.exports,r),i.loaded=!0,i.exports}r.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return r.d(e,{a:e}),e},r.d=(t,e)=>{for(var n in e)r.o(e,n)&&!r.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},r.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),r.hmd=t=>((t=Object.create(t)).children||(t.children=[]),Object.defineProperty(t,"exports",{enumerable:!0,set:()=>{throw new Error("ES Modules may not assign module.exports or exports.*, Use ESM export syntax, instead: "+t.id)}}),t),r.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),r.r=t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.nc=void 0,r(9561)})();
//# sourceMappingURL=block-editor.js.map