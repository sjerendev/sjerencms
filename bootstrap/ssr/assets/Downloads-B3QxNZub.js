import { ref, onMounted, resolveComponent, withCtx, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import axios from "axios";
const _sfc_main = {
  __name: "Downloads",
  __ssrInlineRender: true,
  setup(__props) {
    const downloads = ref([]);
    onMounted(async () => {
      try {
        const response = await axios.get("/api/downloads");
        downloads.value = response.data;
      } catch (error) {
        console.error("Error fetching downloads:", error);
      }
    });
    const formatDate = (date) => {
      if (!date) return "Never";
      return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const canDownload = (download) => {
      return download.download_count < 3 && (!download.expires_at || new Date(download.expires_at) > /* @__PURE__ */ new Date());
    };
    const isExpired = (download) => {
      return download.expires_at && new Date(download.expires_at) <= /* @__PURE__ */ new Date();
    };
    const downloadUrl = (token) => {
      return `/shop/download/${token}`;
    };
    return (_ctx, _push, _parent, _attrs) => {
      const _component_router_link = resolveComponent("router-link");
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="container px-4 py-12 mx-auto"><h1 class="mb-8 text-4xl font-bold">My Downloads</h1>`);
      if (downloads.value.length === 0) {
        _push(`<div class="text-center py-12"><h3 class="mt-2 text-sm font-medium text-gray-900">No downloads available</h3><p class="mt-1 text-sm text-gray-500">Your purchased downloads will appear here.</p><div class="mt-6">`);
        _push(ssrRenderComponent(_component_router_link, {
          to: { name: "shop" },
          class: "inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` Browse Products `);
            } else {
              return [
                createTextVNode(" Browse Products ")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></div>`);
      } else {
        _push(`<div class="bg-white shadow overflow-hidden sm:rounded-md"><ul role="list" class="divide-y divide-gray-200"><!--[-->`);
        ssrRenderList(downloads.value, (download) => {
          _push(`<li class="px-4 py-4 sm:px-6"><div class="flex items-center justify-between"><div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">${ssrInterpolate(download.orderItem.product.name)}</p><p class="mt-1 text-sm text-gray-500"> Downloads remaining: ${ssrInterpolate(3 - download.download_count)}</p><p class="mt-1 text-sm text-gray-500"> Expires: ${ssrInterpolate(formatDate(download.expires_at))}</p></div><div class="ml-4 flex-shrink-0">`);
          if (canDownload(download)) {
            _push(`<a${ssrRenderAttr("href", downloadUrl(download.download_token))} class="font-medium text-primary-600 hover:text-primary-500"> Download </a>`);
          } else {
            _push(`<span class="text-gray-400">${ssrInterpolate(isExpired(download) ? "Expired" : "No downloads remaining")}</span>`);
          }
          _push(`</div></div></li>`);
        });
        _push(`<!--]--></ul></div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/Downloads.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
