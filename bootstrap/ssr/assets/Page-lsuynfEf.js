import { ref, onMounted, resolveComponent, withCtx, openBlock, createBlock, createCommentVNode, createVNode, toDisplayString, createTextVNode, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrRenderComponent } from "vue/server-renderer";
import { useRoute } from "vue-router";
import { _ as _sfc_main$1 } from "./BlockRenderer-ffzv9Wx2.js";
import { u as useSeo } from "./useSeo-3U0Uvxby.js";
import "marked";
import "dompurify";
import "../entry-server.js";
import "@vueuse/head";
import "pinia";
import "gsap";
import "@vue/server-renderer";
import "vue3-carousel";
import "@iconify/vue";
const _sfc_main = {
  __name: "Page",
  __ssrInlineRender: true,
  setup(__props) {
    const route = useRoute();
    const page = ref(null);
    const posts = ref([]);
    const formatDate = (date) => {
      if (!date) return "";
      return new Date(date).toLocaleDateString("sv-SE", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    useSeo(page, {
      title: "Kalibr",
      description: "Kalibr. är en Webb, Design & AI-byrå med bas i Malmö men har kunder i hela Sverige.",
      type: "article"
    });
    onMounted(async () => {
      const response = await fetch(`/api/pages/${route.params.slug}`);
      page.value = await response.json();
      if (route.params.slug === "blogg") {
        const params = new URLSearchParams();
        if (route.query.category) {
          params.append("categories", route.query.category);
        }
        const response2 = await fetch("/api/posts?" + params.toString());
        const data = await response2.json();
        posts.value = data.data;
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d;
      const _component_router_link = resolveComponent("router-link");
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      if ((_a = page.value) == null ? void 0 : _a.hero_background_type) {
        _push(`<div class="relative hero-section" style="${ssrRenderStyle({
          height: page.value.hero_height || "50svh",
          backgroundColor: page.value.hero_background_color,
          color: page.value.hero_text_color
        })}">`);
        if ((_b = page.value) == null ? void 0 : _b.hero_image) {
          _push(`<img${ssrRenderAttr("src", `/storage/${page.value.hero_image}`)} class="absolute inset-0 object-cover w-full h-full" style="${ssrRenderStyle({ opacity: page.value.hero_background_type === "both" ? 0.5 : 1 })}">`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="container relative z-10 px-4 py-12 mx-auto"><h1 class="text-4xl font-bold">${ssrInterpolate(page.value.hero_headline || page.value.title)}</h1>`);
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
      if (((_d = page.value) == null ? void 0 : _d.slug) === "blogg" && posts.value.length > 0) {
        _push(`<div class="container px-4 py-12 mx-auto"><div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"><!--[-->`);
        ssrRenderList(posts.value, (post) => {
          _push(ssrRenderComponent(_component_router_link, {
            key: post.id,
            to: { name: "post", params: { slug: post.slug } },
            class: "overflow-hidden bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              var _a2, _b2;
              if (_push2) {
                if (post.list_image || post.featured_image) {
                  _push2(`<img${ssrRenderAttr("src", `/storage/${post.list_image || post.featured_image}`)}${ssrRenderAttr("alt", post.title)} class="object-cover w-full h-48"${_scopeId}>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<div class="p-6"${_scopeId}><h2 class="mb-2 text-2xl font-bold"${_scopeId}>${ssrInterpolate(post.title)}</h2><div class="flex items-center justify-between mb-4 text-sm"${_scopeId}><div class="flex items-center text-gray-500"${_scopeId}><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"${_scopeId}></path></svg> ${ssrInterpolate(formatDate(post.publish_date || post.created_at))}</div>`);
                if ((_a2 = post.categories) == null ? void 0 : _a2.length) {
                  _push2(`<div class="flex gap-2"${_scopeId}><!--[-->`);
                  ssrRenderList(post.categories, (category) => {
                    _push2(`<span class="category-tag text-sm font-medium"${_scopeId}>${ssrInterpolate(category.name)}</span>`);
                  });
                  _push2(`<!--]--></div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
                if (post.hero_paragraph) {
                  _push2(`<p class="mb-4 text-gray-600"${_scopeId}>${ssrInterpolate(post.hero_paragraph)}</p>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                return [
                  post.list_image || post.featured_image ? (openBlock(), createBlock("img", {
                    key: 0,
                    src: `/storage/${post.list_image || post.featured_image}`,
                    alt: post.title,
                    class: "object-cover w-full h-48"
                  }, null, 8, ["src", "alt"])) : createCommentVNode("", true),
                  createVNode("div", { class: "p-6" }, [
                    createVNode("h2", { class: "mb-2 text-2xl font-bold" }, toDisplayString(post.title), 1),
                    createVNode("div", { class: "flex items-center justify-between mb-4 text-sm" }, [
                      createVNode("div", { class: "flex items-center text-gray-500" }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 mr-1",
                          fill: "none",
                          stroke: "currentColor",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            "stroke-width": "2",
                            d: "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                          })
                        ])),
                        createTextVNode(" " + toDisplayString(formatDate(post.publish_date || post.created_at)), 1)
                      ]),
                      ((_b2 = post.categories) == null ? void 0 : _b2.length) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "flex gap-2"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(post.categories, (category) => {
                          return openBlock(), createBlock("span", {
                            key: category.id,
                            class: "category-tag text-sm font-medium"
                          }, toDisplayString(category.name), 1);
                        }), 128))
                      ])) : createCommentVNode("", true)
                    ]),
                    post.hero_paragraph ? (openBlock(), createBlock("p", {
                      key: 0,
                      class: "mb-4 text-gray-600"
                    }, toDisplayString(post.hero_paragraph), 1)) : createCommentVNode("", true)
                  ])
                ];
              }
            }),
            _: 2
          }, _parent));
        });
        _push(`<!--]--></div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/Page.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
