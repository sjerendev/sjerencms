import { ssrRenderAttrs, ssrRenderStyle, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrRenderComponent } from "vue/server-renderer";
import { ref, onMounted, useSSRContext } from "vue";
import { _ as _sfc_main$1 } from "./BlockRenderer-ffzv9Wx2.js";
import { u as useSeo } from "./useSeo-3U0Uvxby.js";
import "marked";
import "dompurify";
import "../entry-server.js";
import "@vueuse/head";
import "pinia";
import "vue-router";
import "gsap";
import "@vue/server-renderer";
import "vue3-carousel";
import "@iconify/vue";
const _sfc_main = {
  __name: "Home",
  __ssrInlineRender: true,
  setup(__props) {
    const page = ref(null);
    useSeo(page, {
      title: "Kalibr Kreativ Studio",
      description: "Kalibr. är en Webb, Design & AI-byrå med bas i Malmö men har kunder i hela Sverige.",
      type: "website"
    });
    onMounted(async () => {
      const response = await fetch("/api/pages/home");
      page.value = await response.json();
      console.log("Page data:", page.value);
    });
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      if ((_a = page.value) == null ? void 0 : _a.hero_background_type) {
        _push(`<div class="relative flex items-center justify-center w-full hero-section" style="${ssrRenderStyle({
          height: page.value.hero_height || "50svh",
          backgroundColor: page.value.hero_background_color,
          color: page.value.hero_text_color
        })}">`);
        if ((_b = page.value) == null ? void 0 : _b.hero_image) {
          _push(`<img${ssrRenderAttr("src", `/storage/${page.value.hero_image}`)} class="absolute inset-0 object-cover w-full h-full" style="${ssrRenderStyle({ opacity: page.value.hero_background_type === "both" ? 0.5 : 1 })}">`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="relative z-10 px-12 py-12 mx-auto container-fluid"><h1 class="text-4xl font-bold">${ssrInterpolate(page.value.hero_headline || page.value.title)}</h1>`);
        if (page.value.hero_subheadline) {
          _push(`<p class="mt-4 text-xl">${ssrInterpolate(page.value.hero_subheadline)}</p>`);
        } else {
          _push(`<!---->`);
        }
        if (page.value.hero_paragraph) {
          _push(`<p class="mt-4">${ssrInterpolate(page.value.hero_paragraph)}</p>`);
        } else {
          _push(`<!---->`);
        }
        if (page.value.hero_cta_text && page.value.hero_cta_url) {
          _push(`<a${ssrRenderAttr("href", page.value.hero_cta_url)} class="inline-block px-6 py-3 mt-6 text-white rounded-lg bg-primary-500">${ssrInterpolate(page.value.hero_cta_text)}</a>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
      } else {
        _push(`<!---->`);
      }
      if ((_c = page.value) == null ? void 0 : _c.content) {
        _push(`<div><!--[-->`);
        ssrRenderList(page.value.content, (block, index) => {
          _push(ssrRenderComponent(_sfc_main$1, {
            key: index,
            block
          }, null, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/Home.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
