import { ref, computed, onMounted, resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrRenderComponent } from "vue/server-renderer";
import { useRoute } from "vue-router";
import "./BlockRenderer-ffzv9Wx2.js";
import { u as useSeo } from "./useSeo-3U0Uvxby.js";
import { u as useStructuredData } from "./useStructuredData-DPVNhKsh.js";
import { marked } from "marked";
import "dompurify";
import "../entry-server.js";
import "@vueuse/head";
import "pinia";
import "gsap";
import "@vue/server-renderer";
import "vue3-carousel";
import "@iconify/vue";
const _sfc_main = {
  __name: "Post",
  __ssrInlineRender: true,
  setup(__props) {
    const route = useRoute();
    const post = ref(null);
    const renderedContent = computed(() => {
      var _a;
      if (!((_a = post.value) == null ? void 0 : _a.content)) return "";
      return marked(post.value.content);
    });
    useSeo(post, {
      title: "Kalibr Blog",
      description: "Läs de senaste inläggen från Kalibr om webb, design och AI.",
      type: "article",
      image: "/images/blog-default.jpg"
    });
    const formatDate = (date) => {
      if (!date) return "";
      return new Date(date).toLocaleDateString("sv-SE", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    onMounted(async () => {
      var _a;
      const response = await fetch(`/api/posts/${route.params.slug}`);
      post.value = await response.json();
      useStructuredData("BlogPosting", post.value);
      if ((_a = post.value) == null ? void 0 : _a.published_at) {
        const head = document.head;
        const meta = document.createElement("meta");
        meta.setAttribute("property", "article:published_time");
        meta.setAttribute("content", new Date(post.value.published_at).toISOString());
        head.appendChild(meta);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d, _e, _f, _g;
      const _component_router_link = resolveComponent("router-link");
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "blogg" }, _attrs))}>`);
      if ((_a = post.value) == null ? void 0 : _a.hero_background_type) {
        _push(`<div class="relative flex items-center hero-section" style="${ssrRenderStyle({
          height: post.value.hero_height || "50svh",
          backgroundColor: post.value.hero_background_color,
          color: post.value.hero_text_color
        })}">`);
        if ((_b = post.value) == null ? void 0 : _b.hero_image) {
          _push(`<img${ssrRenderAttr("src", `/storage/${post.value.hero_image}`)} class="absolute inset-0 object-cover w-full h-full" style="${ssrRenderStyle({ opacity: post.value.hero_background_type === "both" ? 0.5 : 1 })}">`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="container relative z-10 px-4 py-12 mx-auto"><h1 class="text-4xl font-bold">${ssrInterpolate(((_c = post.value) == null ? void 0 : _c.hero_headline) || ((_d = post.value) == null ? void 0 : _d.title))}</h1>`);
        if (post.value) {
          _push(`<div class="flex items-center justify-between mt-4 text-sm"><div class="flex items-center text-gray-500"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> ${ssrInterpolate(formatDate(post.value.publish_date || post.value.created_at))}</div>`);
          if ((_e = post.value.categories) == null ? void 0 : _e.length) {
            _push(`<div class="flex gap-2"><!--[-->`);
            ssrRenderList(post.value.categories, (category) => {
              _push(ssrRenderComponent(_component_router_link, {
                key: category.id,
                to: {
                  name: "page",
                  params: { slug: "blogg" },
                  query: { category: category.id }
                },
                class: "text-sm font-medium text-primary-600 hover:text-primary-700"
              }, {
                default: withCtx((_, _push2, _parent2, _scopeId) => {
                  if (_push2) {
                    _push2(`${ssrInterpolate(category.name)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(category.name), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent));
            });
            _push(`<!--]--></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        if (post.value.hero_subheadline) {
          _push(`<p class="mt-4 text-xl">${ssrInterpolate(post.value.hero_subheadline)}</p>`);
        } else {
          _push(`<!---->`);
        }
        if (post.value.hero_paragraph) {
          _push(`<p class="mt-4">${ssrInterpolate(post.value.hero_paragraph)}</p>`);
        } else {
          _push(`<!---->`);
        }
        if (post.value.hero_cta_text && post.value.hero_cta_url) {
          _push(`<a${ssrRenderAttr("href", post.value.hero_cta_url)} class="inline-block px-6 py-3 mt-6 text-white rounded-lg bg-primary-500">${ssrInterpolate(post.value.hero_cta_text)}</a>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
      } else if (post.value) {
        _push(`<div class="blog-intro"><h1 class="text-4xl font-bold">${ssrInterpolate(post.value.title)}</h1><div class="flex items-center justify-between mt-4 text-sm"><div class="flex items-center text-gray-500"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> ${ssrInterpolate(formatDate(post.value.publish_date || post.value.created_at))}</div>`);
        if ((_f = post.value.categories) == null ? void 0 : _f.length) {
          _push(`<div class="flex gap-2"><!--[-->`);
          ssrRenderList(post.value.categories, (category) => {
            _push(ssrRenderComponent(_component_router_link, {
              key: category.id,
              to: {
                name: "page",
                params: { slug: "blogg" },
                query: { category: category.id }
              },
              class: "text-sm font-medium text-primary-600 hover:text-primary-700"
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(category.name)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(category.name), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          });
          _push(`<!--]--></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
      } else {
        _push(`<!---->`);
      }
      if ((_g = post.value) == null ? void 0 : _g.content) {
        _push(`<div class="container mx-auto px-4 py-12"><div class="prose prose-lg max-w-none">${renderedContent.value ?? ""}</div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/Post.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
