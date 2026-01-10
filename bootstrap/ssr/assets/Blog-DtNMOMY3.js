import { ref, onMounted, watch, resolveComponent, mergeProps, withCtx, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderAttr, ssrInterpolate, ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
import { useRoute } from "vue-router";
import { u as useSeo } from "./useSeo-3U0Uvxby.js";
import { u as useStructuredData } from "./useStructuredData-DPVNhKsh.js";
import "@vueuse/head";
const _sfc_main = {
  __name: "Blog",
  __ssrInlineRender: true,
  setup(__props) {
    const route = useRoute();
    const posts = ref([]);
    const page = ref({
      title: "Kalibr Blog",
      meta_title: "Kalibr Blog - Webb, Design & AI Artiklar",
      meta_description: "Läs de senaste inläggen från Kalibr om webb, design och AI. Vi delar med oss av vår kunskap och erfarenhet inom webbutveckling, design och artificiell intelligens."
    });
    useSeo(page, {
      title: "Kalibr Blog",
      description: "Läs de senaste inläggen från Kalibr om webb, design och AI.",
      type: "blog",
      image: "/images/blog-default.jpg"
    });
    onMounted(() => {
      useStructuredData("Blog", page.value);
      useStructuredData("Organization", {});
    });
    const fetchPosts = async () => {
      const params = new URLSearchParams();
      if (route.query.category) {
        params.append("category", route.query.category);
      }
      const response = await fetch("/api/posts?" + params.toString());
      posts.value = (await response.json()).data;
    };
    watch(() => route.query, fetchPosts, { immediate: true });
    onMounted(fetchPosts);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_router_link = resolveComponent("router-link");
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "container px-4 py-12 mx-auto" }, _attrs))}><h1 class="mb-8 text-4xl font-bold">Blog</h1><div class="grid grid-cols-3 gap-8"><!--[-->`);
      ssrRenderList(posts.value, (post) => {
        _push(`<article class="post-card">`);
        if (post.list_image || post.featured_image) {
          _push(`<img${ssrRenderAttr("src", `/storage/${post.list_image || post.featured_image}`)}${ssrRenderAttr("alt", post.title)} class="object-cover w-full h-48 mb-4">`);
        } else {
          _push(`<!---->`);
        }
        _push(`<h2 class="mb-2 text-2xl font-bold">${ssrInterpolate(post.title)}</h2><p class="mb-4">${ssrInterpolate(post.hero_paragraph)}</p>`);
        _push(ssrRenderComponent(_component_router_link, {
          to: { name: "post", params: { slug: post.slug } },
          class: "font-semibold text-primary-500"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` Read More `);
            } else {
              return [
                createTextVNode(" Read More ")
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</article>`);
      });
      _push(`<!--]--></div><div class="flex justify-center gap-2 mt-8"><!--[-->`);
      ssrRenderList(_ctx.totalPages, (page2) => {
        _push(`<button class="${ssrRenderClass([
          "px-4 py-2 rounded",
          _ctx.currentPage === page2 ? "bg-primary-500 text-white" : "bg-gray-200"
        ])}">${ssrInterpolate(page2)}</button>`);
      });
      _push(`<!--]--></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/Blog.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
