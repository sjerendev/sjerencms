import { resolveComponent, mergeProps, withCtx, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "../entry-server.js";
import "@vueuse/head";
import "pinia";
import "vue-router";
import "gsap";
import "@vue/server-renderer";
const _sfc_main = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_router_link = resolveComponent("router-link");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex items-center justify-center min-h-screen bg-gray-100" }, _attrs))}><div class="text-center"><h1 class="text-6xl font-bold text-gray-800">404</h1><p class="mt-4 text-xl text-gray-600">Page not found</p>`);
  _push(ssrRenderComponent(_component_router_link, {
    to: "/",
    class: "inline-block px-6 py-3 mt-8 text-white rounded-lg bg-primary-500"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Return Home `);
      } else {
        return [
          createTextVNode(" Return Home ")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/NotFound.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const NotFound = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  NotFound as default
};
