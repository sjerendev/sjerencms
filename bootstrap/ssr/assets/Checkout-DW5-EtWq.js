import { resolveComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
const _sfc_main = {
  __name: "Checkout",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_checkout_form = resolveComponent("checkout-form");
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="container px-4 py-12 mx-auto"><h1 class="mb-8 text-4xl font-bold">Checkout</h1>`);
      _push(ssrRenderComponent(_component_checkout_form, null, null, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/Checkout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
