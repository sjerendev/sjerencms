import { resolveComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { useRoute } from "vue-router";
const _sfc_main = {
  __name: "Product",
  __ssrInlineRender: true,
  setup(__props) {
    useRoute();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_view = resolveComponent("product-view");
      const _component_shopping_cart = resolveComponent("shopping-cart");
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="container px-4 py-12 mx-auto">`);
      _push(ssrRenderComponent(_component_product_view, {
        slug: _ctx.$route.params.slug
      }, null, _parent));
      _push(`</div>`);
      _push(ssrRenderComponent(_component_shopping_cart, null, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/Product.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
