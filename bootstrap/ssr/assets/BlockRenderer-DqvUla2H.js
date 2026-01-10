import { useSSRContext, mergeProps, ref, computed, withCtx, openBlock, createBlock, toDisplayString, createCommentVNode, unref, createVNode, Fragment, renderList, resolveDynamicComponent, createTextVNode, reactive, onMounted, resolveComponent, onUnmounted } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderClass, ssrRenderComponent, ssrInterpolate, ssrRenderStyle, ssrRenderList, ssrRenderVNode, ssrIncludeBooleanAttr, ssrRenderSuspense } from "vue/server-renderer";
import { marked } from "marked";
import DOMPurify from "dompurify";
import { M as MagneticElement, _ as _export_sfc } from "../entry-server.js";
import { Carousel, Navigation, Pagination, Slide } from "vue3-carousel";
import { Icon } from "@iconify/vue";
import { useRouter } from "vue-router";
import { gsap } from "gsap";
const _sfc_main$i = {
  __name: "FullImage",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    },
    isFirstBlock: {
      type: Boolean,
      default: false
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-12 mx-auto"><img${ssrRenderAttr("src", `/storage/${__props.block.image}`)}${ssrRenderAttr("fetchpriority", __props.isFirstBlock ? "high" : "auto")}${ssrRenderAttr("loading", __props.isFirstBlock ? "eager" : "lazy")} class="w-full h-auto"${ssrRenderAttr("alt", __props.block.image_alt || "")}></div></section>`);
    };
  }
};
const _sfc_setup$i = _sfc_main$i.setup;
_sfc_main$i.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/FullImage.vue");
  return _sfc_setup$i ? _sfc_setup$i(props, ctx) : void 0;
};
const _sfc_main$h = {
  __name: "ImageText",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    },
    isFirstBlock: {
      type: Boolean,
      default: false
    }
  },
  setup(__props) {
    const props = __props;
    const aspectRatio = ref(56.25);
    const parsedContent = computed(() => {
      if (!props.block.text) return "";
      const htmlContent = marked.parse(props.block.text);
      return DOMPurify.sanitize(htmlContent);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-492774c4><div class="container px-6 py-12 mx-auto md:px-8 lg:px-12" data-v-492774c4><div class="${ssrRenderClass([{ "md:flex-row-reverse": __props.block.reverse_layout }, "flex flex-col items-center md:flex-row"])}" data-v-492774c4><div class="${ssrRenderClass([{
        "lg:pl-6": !__props.block.reverse_layout,
        "lg:pr-6": __props.block.reverse_layout,
        "md:order-1": !__props.block.reverse_layout,
        "md:order-2": __props.block.reverse_layout
      }, "flex order-1 items-center px-2 w-full md:w-1/2"])}" data-v-492774c4><div class="py-4 w-full" data-v-492774c4>`);
      if (__props.isFirstBlock) {
        _push(`<div data-v-492774c4><div class="mb-4" data-v-492774c4>${parsedContent.value ?? ""}</div></div>`);
      } else {
        _push(`<div data-v-492774c4><div class="mb-4 content-visibility-auto" data-v-492774c4>${parsedContent.value ?? ""}</div></div>`);
      }
      if (__props.block.button1_text || __props.block.button2_text) {
        _push(`<div class="block gap-4 mt-10 md:flex" data-v-492774c4>`);
        _push(ssrRenderComponent(MagneticElement, {
          strength: 0.3,
          scale: true,
          rotate: true
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              if (__props.block.button1_text) {
                _push2(`<a${ssrRenderAttr("href", __props.block.button1_url)} class="block items-center px-4 py-2 mb-4 font-semibold text-center text-white rounded-xl transition-colors md:inline-flex button-one md:mb-0" data-v-492774c4${_scopeId}>${ssrInterpolate(__props.block.button1_text)}</a>`);
              } else {
                _push2(`<!---->`);
              }
            } else {
              return [
                __props.block.button1_text ? (openBlock(), createBlock("a", {
                  key: 0,
                  href: __props.block.button1_url,
                  class: "block items-center px-4 py-2 mb-4 font-semibold text-center text-white rounded-xl transition-colors md:inline-flex button-one md:mb-0"
                }, toDisplayString(__props.block.button1_text), 9, ["href"])) : createCommentVNode("", true)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(MagneticElement, {
          strength: 0.3,
          scale: true,
          rotate: true
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              if (__props.block.button2_text) {
                _push2(`<a${ssrRenderAttr("href", __props.block.button2_url)} class="block items-center px-4 py-2 font-semibold text-center text-white rounded-xl transition-colors md:inline-flex button-two" data-v-492774c4${_scopeId}>${ssrInterpolate(__props.block.button2_text)}</a>`);
              } else {
                _push2(`<!---->`);
              }
            } else {
              return [
                __props.block.button2_text ? (openBlock(), createBlock("a", {
                  key: 0,
                  href: __props.block.button2_url,
                  class: "block items-center px-4 py-2 font-semibold text-center text-white rounded-xl transition-colors md:inline-flex button-two"
                }, toDisplayString(__props.block.button2_text), 9, ["href"])) : createCommentVNode("", true)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="${ssrRenderClass([{
        "lg:pr-6": !__props.block.reverse_layout,
        "lg:pl-6": __props.block.reverse_layout,
        "md:order-2": !__props.block.reverse_layout,
        "md:order-1": __props.block.reverse_layout
      }, "order-2 mb-8 w-full md:w-1/2 md:mb-0"])}" data-v-492774c4><div class="relative z-0 aspect-ratio-box" style="${ssrRenderStyle({ paddingBottom: aspectRatio.value + "%" })}" data-v-492774c4><img${ssrRenderAttr("src", `/storage/${__props.block.image}`)}${ssrRenderAttr("fetchpriority", __props.isFirstBlock ? "low" : "auto")}${ssrRenderAttr("loading", __props.isFirstBlock ? "lazy" : "lazy")} class="object-cover absolute inset-0 w-full h-full"${ssrRenderAttr("alt", __props.block.image_alt || "")} data-v-492774c4></div></div></div></div></section>`);
    };
  }
};
const _sfc_setup$h = _sfc_main$h.setup;
_sfc_main$h.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/ImageText.vue");
  return _sfc_setup$h ? _sfc_setup$h(props, ctx) : void 0;
};
const ImageText = /* @__PURE__ */ _export_sfc(_sfc_main$h, [["__scopeId", "data-v-492774c4"]]);
const _sfc_main$g = {
  __name: "FullText",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const props = __props;
    const parsedContent = computed(() => {
      if (!props.block.text) return "";
      const htmlContent = marked.parse(props.block.text);
      return DOMPurify.sanitize(htmlContent);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0"><div>${parsedContent.value ?? ""}</div></div></section>`);
    };
  }
};
const _sfc_setup$g = _sfc_main$g.setup;
_sfc_main$g.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/FullText.vue");
  return _sfc_setup$g ? _sfc_setup$g(props, ctx) : void 0;
};
const _sfc_main$f = {
  __name: "Carousel",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-12 mx-auto">`);
      _push(ssrRenderComponent(unref(Carousel), {
        "items-to-show": 1,
        "wrap-around": true
      }, {
        addons: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(Navigation), null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(Pagination), null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(unref(Navigation)),
              createVNode(unref(Pagination))
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(__props.block.carousel_images, (image, index) => {
              _push2(ssrRenderComponent(unref(Slide), { key: index }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<img${ssrRenderAttr("src", `/storage/${image}`)} class="w-full h-auto" alt=""${_scopeId2}>`);
                  } else {
                    return [
                      createVNode("img", {
                        src: `/storage/${image}`,
                        class: "w-full h-auto",
                        alt: ""
                      }, null, 8, ["src"])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]-->`);
          } else {
            return [
              (openBlock(true), createBlock(Fragment, null, renderList(__props.block.carousel_images, (image, index) => {
                return openBlock(), createBlock(unref(Slide), { key: index }, {
                  default: withCtx(() => [
                    createVNode("img", {
                      src: `/storage/${image}`,
                      class: "w-full h-auto",
                      alt: ""
                    }, null, 8, ["src"])
                  ]),
                  _: 2
                }, 1024);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></section>`);
    };
  }
};
const _sfc_setup$f = _sfc_main$f.setup;
_sfc_main$f.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/Carousel.vue");
  return _sfc_setup$f ? _sfc_setup$f(props, ctx) : void 0;
};
const _sfc_main$e = {
  __name: "ThreeColumnCards",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const props = __props;
    const getColumnClass = computed(() => {
      switch (props.block.columns) {
        case "3":
          return "grid-cols-1 sm:grid-cols-3";
        case "4":
          return "grid-cols-1 sm:grid-cols-4";
        case "5":
          return "grid-cols-1 sm:grid-cols-5";
        default:
          return "grid-cols-1 sm:grid-cols-3";
      }
    });
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-12 mx-auto"><div class="${ssrRenderClass(`grid gap-8 ${getColumnClass.value}`)}"><!--[-->`);
      ssrRenderList(__props.block.cards, (card, index) => {
        _push(`<div class="card">`);
        if (card.image) {
          _push(`<img${ssrRenderAttr("src", `/storage/${card.image}`)}${ssrRenderAttr("alt", card.title)} class="object-cover w-full h-48 mb-4">`);
        } else {
          _push(`<!---->`);
        }
        _push(`<h3 class="mb-2 text-xl font-bold">${ssrInterpolate(card.title)}</h3><div class="mb-4">${getMarkdownContent(card.text) ?? ""}</div><a${ssrRenderAttr("href", card.link_url)} class="inline-block px-4 py-2 text-white rounded bg-primary-500">${ssrInterpolate(card.link_text)}</a></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$e = _sfc_main$e.setup;
_sfc_main$e.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/ThreeColumnCards.vue");
  return _sfc_setup$e ? _sfc_setup$e(props, ctx) : void 0;
};
const _sfc_main$d = {
  __name: "Video",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    function getEmbedUrl(url) {
      if (url.includes("youtu.be")) {
        const id = url.split("/").pop().split("?")[0];
        return `https://www.youtube.com/embed/${id}`;
      }
      return url;
    }
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-12 mx-auto"><div class="video-container">`);
      if (__props.block.type === "embed") {
        _push(`<div class="aspect-w-16 aspect-h-9"><iframe${ssrRenderAttr("src", getEmbedUrl(__props.block.embed_url))} frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe></div>`);
      } else if (__props.block.video_file) {
        _push(`<video${ssrRenderAttr("src", `/storage/${__props.block.video_file}`)} controls class="w-full"></video>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.block.title || __props.block.description) {
        _push(`<div class="mt-4">`);
        if (__props.block.title) {
          _push(`<h3 class="mb-2 text-xl font-bold">${ssrInterpolate(__props.block.title)}</h3>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.block.description) {
          _push(`<div class="text-gray-600">${getMarkdownContent(__props.block.description) ?? ""}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></section>`);
    };
  }
};
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/Video.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const _sfc_main$c = {
  __name: "CallToAction",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const props = __props;
    const backgroundStyle = computed(() => {
      if (props.block.background_type === "image" && props.block.background_image) {
        return {
          backgroundImage: `url(/storage/${props.block.background_image})`,
          backgroundSize: "cover",
          backgroundPosition: "center"
        };
      }
      return {
        backgroundColor: props.block.background_color
      };
    });
    computed(() => ({
      "mx-auto": props.block.text_alignment === "center",
      "ml-auto": props.block.text_alignment === "right",
      "mr-auto": props.block.text_alignment === "left"
    }));
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: [__props.block.section_class, "py-16"],
        style: backgroundStyle.value
      }, _attrs))}><div class="${ssrRenderClass([[
        __props.block.text_alignment === "left" ? "text-left" : __props.block.text_alignment === "right" ? "text-right" : "text-center"
      ], "container px-4 mx-auto"])}" style="${ssrRenderStyle({ color: __props.block.text_color })}"><h2 class="mb-4 text-3xl font-bold">${ssrInterpolate(__props.block.headline)}</h2><div class="mb-6">${getMarkdownContent(__props.block.text) ?? ""}</div><a${ssrRenderAttr("href", __props.block.button_url)} class="inline-block px-8 py-3 font-semibold rounded-lg" style="${ssrRenderStyle({
        backgroundColor: __props.block.text_color,
        color: __props.block.background_type === "color" ? __props.block.background_color : "#ffffff"
      })}">${ssrInterpolate(__props.block.button_text)}</a></div></section>`);
    };
  }
};
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/CallToAction.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const _sfc_main$b = {
  __name: "HeadlineText",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    },
    isFirstBlock: {
      type: Boolean,
      default: false
    }
  },
  setup(__props) {
    const props = __props;
    const headlineType = computed(() => props.block.headline_type || "h2");
    const headline = computed(() => props.block.headline || "");
    const parsedContent = computed(() => {
      if (!props.block.text) return "";
      const htmlContent = marked.parse(props.block.text);
      return DOMPurify.sanitize(htmlContent);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-49980e11><div class="${ssrRenderClass(__props.block.container_type + " mx-auto")}" data-v-49980e11><div class="${ssrRenderClass([
        "flex py-12 items-start content-block headline-text",
        __props.block.container_type === "container-fluid" ? "px-12" : ""
      ])}" data-v-49980e11><div class="w-3/12" data-v-49980e11>`);
      ssrRenderVNode(_push, createVNode(resolveDynamicComponent(headlineType.value), {
        class: ["text-4xl font-bold leading-[1]", { "high-priority": __props.isFirstBlock }]
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(headline.value)}`);
          } else {
            return [
              createTextVNode(toDisplayString(headline.value), 1)
            ];
          }
        }),
        _: 1
      }), _parent);
      _push(`</div><div class="w-9/12 prose max-w-none leading-[1] text-lg content-visibility-auto" data-v-49980e11>${parsedContent.value ?? ""}</div></div></div></section>`);
    };
  }
};
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/HeadlineText.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const HeadlineText = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["__scopeId", "data-v-49980e11"]]);
const _sfc_main$a = {
  __name: "pricing_tables",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-12 mx-auto">`);
      if (__props.block.section_title || __props.block.section_description) {
        _push(`<div class="text-center mb-8">`);
        if (__props.block.section_title) {
          _push(`<h2 class="text-3xl font-bold mb-4">${ssrInterpolate(__props.block.section_title)}</h2>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.block.section_description) {
          _push(`<div class="max-w-3xl mx-auto mb-12 text-center text-gray-500">${getMarkdownContent(__props.block.section_description) ?? ""}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"><!--[-->`);
      ssrRenderList(__props.block.pricing_tables, (plan, index) => {
        _push(`<div class="${ssrRenderClass([
          "bg-white rounded-lg shadow-lg p-6 transition-transform hover:scale-105",
          plan.is_featured ? "ring-2 ring-blue-500" : ""
        ])}"><div class="text-center"><h3 class="text-xl font-bold mb-2">${ssrInterpolate(plan.name)}</h3><div class="text-3xl font-bold mb-4">${ssrInterpolate(plan.price)} <span class="text-sm text-gray-500">${ssrInterpolate(plan.billing_period)}</span></div></div><ul class="space-y-3 mb-6"><!--[-->`);
        ssrRenderList(plan.features, (feature, featureIndex) => {
          _push(`<li class="${ssrRenderClass([{ "text-gray-400": !feature.is_included }, "flex items-center"])}">`);
          if (feature.is_included) {
            _push(`<svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`);
          } else {
            _push(`<svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`);
          }
          _push(` ${ssrInterpolate(feature.feature)}</li>`);
        });
        _push(`<!--]--></ul><div class="text-center"><a${ssrRenderAttr("href", plan.button_url)} style="${ssrRenderStyle(plan.accent_color ? { backgroundColor: plan.accent_color } : {})}" class="inline-block w-full px-6 py-3 text-center text-white bg-blue-600 rounded-lg hover:opacity-90 transition-colors">${ssrInterpolate(plan.button_text)}</a></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/pricing_tables.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const _sfc_main$9 = {
  __name: "FaqAccordion",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const currentlyOpen = ref(null);
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-97ce3b06><div class="container py-12 lg:py-24 mx-auto px-6 2xl:px-0" data-v-97ce3b06>`);
      if (__props.block.heading || __props.block.subheading) {
        _push(`<div class="text-center mb-8" data-v-97ce3b06>`);
        if (__props.block.heading) {
          _push(`<h2 class="text-3xl lg:text-5xl font-bold mb-4" data-v-97ce3b06>${ssrInterpolate(__props.block.heading)}</h2>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.block.subheading) {
          _push(`<p class="text-gray-600" data-v-97ce3b06>${ssrInterpolate(__props.block.subheading)}</p>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="max-w-4xl mx-auto" data-v-97ce3b06><!--[-->`);
      ssrRenderList(__props.block.items, (item, index) => {
        _push(`<div class="mb-4" data-v-97ce3b06><button class="flex justify-between items-center w-full px-4 py-3 text-left rounded-lg focus:outline-none faq-item" data-v-97ce3b06><span class="font-medium text-2xl" data-v-97ce3b06>${ssrInterpolate(item.question)}</span><svg class="${ssrRenderClass([{ "rotate-180": currentlyOpen.value === index }, "w-5 h-5 transition-transform"])}" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-97ce3b06><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" data-v-97ce3b06></path></svg></button><div class="overflow-hidden transition-all duration-300 ease-in-out" style="${ssrRenderStyle({ maxHeight: currentlyOpen.value === index ? "500px" : "0", opacity: currentlyOpen.value === index ? "1" : "0" })}" data-v-97ce3b06><div class="px-4 py-3 mt-2 bg-white rounded-lg" data-v-97ce3b06><div class="text-gray-600" data-v-97ce3b06>${getMarkdownContent(item.answer) ?? ""}</div></div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/FaqAccordion.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const FaqAccordion = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["__scopeId", "data-v-97ce3b06"]]);
const _sfc_main$8 = {
  __name: "TeamMembers",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-12 mx-auto">`);
      if (__props.block.heading || __props.block.subheading) {
        _push(`<div class="text-center mb-8">`);
        if (__props.block.heading) {
          _push(`<h2 class="text-3xl font-bold mb-4">${ssrInterpolate(__props.block.heading)}</h2>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.block.subheading) {
          _push(`<p class="text-gray-600">${ssrInterpolate(__props.block.subheading)}</p>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.block.section_description) {
        _push(`<div class="max-w-3xl mx-auto mb-12 text-center text-gray-500">${getMarkdownContent(__props.block.section_description) ?? ""}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"><!--[-->`);
      ssrRenderList(__props.block.members, (member, index) => {
        _push(`<div class="bg-white rounded-lg shadow-lg overflow-hidden"><div class="aspect-w-1 aspect-h-1"><img${ssrRenderAttr("src", `/storage/${member.image}`)}${ssrRenderAttr("alt", member.name)} class="w-full h-full object-cover"></div><div class="p-6"><h3 class="text-xl font-bold mb-1">${ssrInterpolate(member.name)}</h3><p class="text-gray-600 mb-3">${ssrInterpolate(member.position)}</p><div class="mb-4 text-gray-600">${getMarkdownContent(member.bio) ?? ""}</div><div class="flex space-x-4">`);
        if (member.linkedin) {
          _push(`<a${ssrRenderAttr("href", member.linkedin)} target="_blank" class="text-blue-600 hover:text-blue-800"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg></a>`);
        } else {
          _push(`<!---->`);
        }
        if (member.twitter) {
          _push(`<a${ssrRenderAttr("href", member.twitter)} target="_blank" class="text-blue-400 hover:text-blue-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/TeamMembers.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _sfc_main$7 = {
  __name: "LogoShowcase",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))}><div class="container py-12 mx-auto">`);
      if (__props.block.heading || __props.block.subheading) {
        _push(`<div class="text-center mb-8">`);
        if (__props.block.heading) {
          _push(`<h2 class="text-3xl font-bold mb-4">${ssrInterpolate(__props.block.heading)}</h2>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.block.subheading) {
          _push(`<p class="text-gray-600">${ssrInterpolate(__props.block.subheading)}</p>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.block.section_description) {
        _push(`<div class="max-w-3xl mx-auto mb-12 text-center text-gray-500">${getMarkdownContent(__props.block.section_description) ?? ""}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8 items-center"><!--[-->`);
      ssrRenderList(__props.block.logos, (logo, index) => {
        _push(`<div class="flex items-center justify-center p-4 transition-opacity hover:opacity-75">`);
        if (logo.url) {
          _push(`<a${ssrRenderAttr("href", logo.url)} target="_blank" class="block w-full"><img${ssrRenderAttr("src", `/storage/${logo.image}`)}${ssrRenderAttr("alt", logo.name)} class="max-w-full h-auto mx-auto" style="${ssrRenderStyle({ maxHeight: __props.block.logo_height + "px" })}"></a>`);
        } else {
          _push(`<img${ssrRenderAttr("src", `/storage/${logo.image}`)}${ssrRenderAttr("alt", logo.name)} class="max-w-full h-auto mx-auto" style="${ssrRenderStyle({ maxHeight: __props.block.logo_height + "px" })}">`);
        }
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/LogoShowcase.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = {
  __name: "ContactForm",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true,
      default: () => ({
        notification_email: "info@kalibercreative.se",
        success_message: "Tack för ditt meddelande. Vi kommer att kontakta dig snart!"
      })
    }
  },
  setup(__props) {
    const formData = reactive({
      name: "",
      email: "",
      subject: "",
      message: ""
    });
    const isSubmitting = ref(false);
    const submitStatus = ref(null);
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-546f7b17><div class="container py-12 mx-auto px-6 2xl:px-0" data-v-546f7b17><div class="max-w-3xl mx-auto" data-v-546f7b17>`);
      if (__props.block.heading || __props.block.subheading) {
        _push(`<div class="text-center mb-8" data-v-546f7b17>`);
        if (__props.block.heading) {
          _push(`<h2 class="text-3xl font-bold mb-4" data-v-546f7b17>${ssrInterpolate(__props.block.heading)}</h2>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.block.subheading) {
          _push(`<p class="text-gray-600" data-v-546f7b17>${ssrInterpolate(__props.block.subheading)}</p>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.block.section_description) {
        _push(`<div class="max-w-3xl mx-auto mb-12 text-center text-gray-500" data-v-546f7b17>${getMarkdownContent(__props.block.section_description) ?? ""}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<form class="space-y-6" data-v-546f7b17><div class="grid grid-cols-1 md:grid-cols-2 gap-6" data-v-546f7b17><div data-v-546f7b17><label class="block text-sm font-medium text-gray-700 mb-1" data-v-546f7b17> Namn </label><input type="text"${ssrRenderAttr("value", formData.name)} required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" data-v-546f7b17></div><div data-v-546f7b17><label class="block text-sm font-medium text-gray-700 mb-1" data-v-546f7b17> E-post </label><input type="email"${ssrRenderAttr("value", formData.email)} required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" data-v-546f7b17></div></div><div data-v-546f7b17><label class="block text-sm font-medium text-gray-700 mb-1" data-v-546f7b17> Gällande </label><input type="text"${ssrRenderAttr("value", formData.subject)} required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" data-v-546f7b17></div><div data-v-546f7b17><label class="block text-sm font-medium text-gray-700 mb-1" data-v-546f7b17> Meddelande </label><textarea required rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" data-v-546f7b17>${ssrInterpolate(formData.message)}</textarea></div><div class="flex justify-center" data-v-546f7b17><button type="submit"${ssrIncludeBooleanAttr(isSubmitting.value) ? " disabled" : ""} class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:bg-blue-400" data-v-546f7b17>${ssrInterpolate(isSubmitting.value ? "Skickar..." : __props.block.submit_button_text || "Skicka meddelande")}</button></div>`);
      if (submitStatus.value) {
        _push(`<div class="${ssrRenderClass([
          "text-center p-4 rounded-lg",
          submitStatus.value.type === "success" ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"
        ])}" data-v-546f7b17>${ssrInterpolate(submitStatus.value.message)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</form></div></div></section>`);
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/ContactForm.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const ContactForm = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["__scopeId", "data-v-546f7b17"]]);
const _sfc_main$5 = {
  __name: "IconCards",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const getIconName = (iconName) => {
      if (!iconName) return "";
      if (iconName.startsWith("ph:")) return iconName;
      return `ph:${iconName}`;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-ea1b126d><div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0" data-v-ea1b126d>`);
      if (__props.block.section_title) {
        _push(`<h2 class="mb-16 text-3xl font-bold text-center min-h-[40px]" data-v-ea1b126d>${ssrInterpolate(__props.block.section_title)}</h2>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-v-ea1b126d><!--[-->`);
      ssrRenderList(__props.block.cards, (card, index) => {
        _push(`<div class="iconCard" data-v-ea1b126d><div class="iconCardContent" data-v-ea1b126d><div class="iconBox" data-v-ea1b126d><div class="icon-wrapper" data-v-ea1b126d>`);
        ssrRenderSuspense(_push, {
          default: () => {
            _push(ssrRenderComponent(unref(Icon), {
              icon: getIconName(card.icon),
              class: "text-[#19F2B2] w-6 h-6",
              width: "24",
              height: "24"
            }, null, _parent));
          },
          fallback: () => {
            _push(`<div class="w-10 h-10 animate-pulse bg-gray-200 rounded-full" data-v-ea1b126d></div>`);
          },
          _: 2
        });
        _push(`</div></div><div class="iconCardText" data-v-ea1b126d><h3 class="text-xl font-bold mb-4 min-h-[28px]" data-v-ea1b126d>${ssrInterpolate(card.title)}</h3><p class="text-gray-600 mb-4 min-h-[72px]" data-v-ea1b126d>${ssrInterpolate(card.text)}</p>`);
        if (card.link_url) {
          _push(`<a${ssrRenderAttr("href", card.link_url)} class="iconCardLink" data-v-ea1b126d>Läs mer</a>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/IconCards.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const IconCards = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["__scopeId", "data-v-ea1b126d"]]);
const _sfc_main$4 = {
  __name: "BlogPosts",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    useRouter();
    const props = __props;
    const posts = ref([]);
    const getColumnClass = computed(() => {
      switch (props.block.columns) {
        case "2":
          return "grid-cols-1 lg:grid-cols-2";
        case "3":
          return "grid-cols-1 lg:grid-cols-3";
        case "4":
          return "grid-cols-1 lg:grid-cols-4";
        default:
          return "grid-cols-1 lg:grid-cols-3";
      }
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
      try {
        const params = {
          limit: props.block.posts_count || 3
        };
        if ((_a = props.block.categories) == null ? void 0 : _a.length) {
          params.categories = props.block.categories;
        }
        const response = await fetch("/api/posts?" + new URLSearchParams(params));
        const data = await response.json();
        posts.value = data.data;
      } catch (error) {
        console.error("Error fetching blog posts:", error);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_router_link = resolveComponent("router-link");
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-4580a059><div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0" data-v-4580a059>`);
      if (__props.block.section_title) {
        _push(`<h2 class="mb-8 text-3xl font-bold text-center" data-v-4580a059>${ssrInterpolate(__props.block.section_title)}</h2>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="${ssrRenderClass(`grid gap-8 ${getColumnClass.value}`)}" data-v-4580a059><!--[-->`);
      ssrRenderList(posts.value, (post) => {
        _push(`<article class="blog-card" data-v-4580a059>`);
        _push(ssrRenderComponent(_component_router_link, {
          to: { name: "post", params: { slug: post.slug } },
          class: "block"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            var _a, _b;
            if (_push2) {
              if (__props.block.show_hero_image && post.list_image) {
                _push2(`<img${ssrRenderAttr("src", `/storage/${post.list_image}`)}${ssrRenderAttr("alt", post.title)} class="w-full h-48 object-cover rounded-t-lg" data-v-4580a059${_scopeId}>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="p-6" data-v-4580a059${_scopeId}><h3 class="text-xl font-bold mb-3 hover:text-primary-600 transition" data-v-4580a059${_scopeId}>${ssrInterpolate(post.title)}</h3>`);
              if (post.hero_paragraph) {
                _push2(`<div class="text-gray-600 mb-4" data-v-4580a059${_scopeId}>${ssrInterpolate(post.hero_paragraph)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (__props.block.show_date) {
                _push2(`<div class="block sm:flex items-center justify-between text-sm" data-v-4580a059${_scopeId}><div class="block sm:flex items-center text-gray-500 mb-4 sm:mb-0" data-v-4580a059${_scopeId}><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" data-v-4580a059${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" data-v-4580a059${_scopeId}></path></svg> ${ssrInterpolate(formatDate(post.publish_date || post.created_at))}</div>`);
                if ((_a = post.categories) == null ? void 0 : _a.length) {
                  _push2(`<div class="block sm:flex gap-2" data-v-4580a059${_scopeId}><!--[-->`);
                  ssrRenderList(post.categories, (category) => {
                    _push2(ssrRenderComponent(_component_router_link, {
                      key: category.id,
                      to: {
                        name: "page",
                        params: { slug: "blogg" },
                        query: { category: category.id }
                      },
                      class: "category-tag text-sm font-medium mr-2 sm:mr-0"
                    }, {
                      default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                        if (_push3) {
                          _push3(`${ssrInterpolate(category.name)}`);
                        } else {
                          return [
                            createTextVNode(toDisplayString(category.name), 1)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent2, _scopeId));
                  });
                  _push2(`<!--]--></div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              return [
                __props.block.show_hero_image && post.list_image ? (openBlock(), createBlock("img", {
                  key: 0,
                  src: `/storage/${post.list_image}`,
                  alt: post.title,
                  class: "w-full h-48 object-cover rounded-t-lg"
                }, null, 8, ["src", "alt"])) : createCommentVNode("", true),
                createVNode("div", { class: "p-6" }, [
                  createVNode("h3", { class: "text-xl font-bold mb-3 hover:text-primary-600 transition" }, toDisplayString(post.title), 1),
                  post.hero_paragraph ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "text-gray-600 mb-4"
                  }, toDisplayString(post.hero_paragraph), 1)) : createCommentVNode("", true),
                  __props.block.show_date ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "block sm:flex items-center justify-between text-sm"
                  }, [
                    createVNode("div", { class: "block sm:flex items-center text-gray-500 mb-4 sm:mb-0" }, [
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
                    ((_b = post.categories) == null ? void 0 : _b.length) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "block sm:flex gap-2"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(post.categories, (category) => {
                        return openBlock(), createBlock(_component_router_link, {
                          key: category.id,
                          to: {
                            name: "page",
                            params: { slug: "blogg" },
                            query: { category: category.id }
                          },
                          class: "category-tag text-sm font-medium mr-2 sm:mr-0"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(category.name), 1)
                          ]),
                          _: 2
                        }, 1032, ["to"]);
                      }), 128))
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true)
                ])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</article>`);
      });
      _push(`<!--]--></div><div class="text-center mt-12" data-v-4580a059>`);
      _push(ssrRenderComponent(_component_router_link, {
        to: "/blogg",
        class: "inline-block px-6 py-3 font-semibold text-[#111820] transition-colors rounded-lg bg-[#18F2B2] hover:bg-primary-600"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Se fler Nyheter `);
          } else {
            return [
              createTextVNode(" Se fler Nyheter ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></section>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/BlogPosts.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const BlogPosts = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["__scopeId", "data-v-4580a059"]]);
const _sfc_main$3 = {
  __name: "ServiceCards",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const getIconName = (iconName) => {
      if (!iconName) return "";
      if (iconName.startsWith("ph:")) return iconName;
      return `ph:${iconName}`;
    };
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-e544ace3><div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0" data-v-e544ace3>`);
      if (__props.block.section_title) {
        _push(`<h2 class="mb-16 text-3xl font-bold text-center" data-v-e544ace3>${ssrInterpolate(__props.block.section_title)}</h2>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="space-y-16" data-v-e544ace3><!--[-->`);
      ssrRenderList(__props.block.cards, (card, index) => {
        _push(`<div class="service-card" data-v-e544ace3><div class="card-content" data-v-e544ace3><div class="icon-container flex-shrink-0" data-v-e544ace3>`);
        ssrRenderSuspense(_push, {
          default: () => {
            _push(ssrRenderComponent(unref(Icon), {
              icon: getIconName(card.icon),
              style: { color: card.icon_color || "#F87171" },
              width: "48",
              height: "48"
            }, null, _parent));
          },
          fallback: () => {
            _push(`<div class="w-12 h-12 animate-pulse bg-gray-200 rounded-full" data-v-e544ace3></div>`);
          },
          _: 2
        });
        _push(`</div><div class="content-wrapper" data-v-e544ace3><h3 class="text-2xl font-bold mb-4" data-v-e544ace3>${ssrInterpolate(card.title)}</h3><div class="prose" data-v-e544ace3>${getMarkdownContent(card.description) ?? ""}</div></div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/ServiceCards.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const ServiceCards = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-e544ace3"]]);
const _sfc_main$2 = {
  __name: "PortfolioItems",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    const previewImage = ref(null);
    const hoveredItem = ref(null);
    const getMarkdownContent = (text) => {
      if (!text) return "";
      const htmlContent = marked.parse(text);
      return DOMPurify.sanitize(htmlContent);
    };
    onMounted(() => {
      gsap.set(previewImage.value, {
        scale: 0,
        opacity: 0
      });
    });
    onUnmounted(() => {
      gsap.killTweensOf(previewImage.value);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: __props.block.section_class
      }, _attrs))} data-v-41fac906><div class="container px-6 py-12 mx-auto 2xl:px-0" data-v-41fac906>`);
      if (__props.block.heading || __props.block.subheading) {
        _push(`<div class="mb-8 text-center" data-v-41fac906>`);
        if (__props.block.heading) {
          _push(`<h2 class="mb-4 text-3xl font-bold" data-v-41fac906>${ssrInterpolate(__props.block.heading)}</h2>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.block.subheading) {
          _push(`<p class="text-gray-600" data-v-41fac906>${ssrInterpolate(__props.block.subheading)}</p>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.block.section_description) {
        _push(`<div class="mx-auto mb-12 max-w-3xl text-center text-gray-500" data-v-41fac906>${getMarkdownContent(__props.block.section_description) ?? ""}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="portfolio-grid" data-v-41fac906><!--[-->`);
      ssrRenderList(__props.block.portfolio_items, (item, index) => {
        _push(`<div class="portfolio-item" data-v-41fac906><a${ssrRenderAttr("href", item.website_url)}${ssrRenderAttr("target", item.website_url !== "#" ? "_blank" : "_self")} class="flex flex-row justify-between items-center portfolio-link" data-v-41fac906><h3 class="portfolio-title" data-v-41fac906>${ssrInterpolate(item.name)}</h3><div class="portfolio-description" data-v-41fac906>${getMarkdownContent(item.description) ?? ""}</div></a></div>`);
      });
      _push(`<!--]--></div><div class="portfolio-preview" style="${ssrRenderStyle(hoveredItem.value ? null : { display: "none" })}" data-v-41fac906>`);
      if (hoveredItem.value) {
        _push(`<img${ssrRenderAttr("src", `/storage/${hoveredItem.value.preview_image}`)}${ssrRenderAttr("alt", hoveredItem.value.name)} class="object-cover object-top preview-img" data-v-41fac906>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></section>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/PortfolioItems.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const PortfolioItems = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["__scopeId", "data-v-41fac906"]]);
const _sfc_main$1 = {
  __name: "TickerBlock",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true,
      default: () => ({
        background_color: "#ffffff",
        text_color: "#000000",
        angle: 0,
        direction: "left-to-right",
        words: [],
        section_class: ""
      })
    },
    isFirstBlock: {
      type: Boolean,
      default: false
    }
  },
  setup(__props) {
    const props = __props;
    const track = ref(null);
    let animation;
    onMounted(() => {
      const items = Array.from(track.value.querySelectorAll(".ticker-item"));
      const dir = props.block.direction === "right-to-left" ? -1 : 1;
      items.forEach((item) => {
        track.value.appendChild(item.cloneNode(true));
      });
      const trackWidth = track.value.offsetWidth;
      const halfTrackWidth = trackWidth / 2;
      animation = gsap.to(track.value, {
        x: dir * -halfTrackWidth,
        ease: "none",
        duration: 30,
        repeat: -1
      });
    });
    onUnmounted(() => {
      if (animation) {
        animation.kill();
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: ["ticker-block", __props.block.section_class],
        style: {
          "--background-color": __props.block.background_color,
          "--text-color": __props.block.text_color,
          "--angle": __props.block.angle + "deg"
        }
      }, _attrs))} data-v-e8e7d083><div class="${ssrRenderClass([__props.block.direction, "ticker-wrap"])}" data-v-e8e7d083><div class="ticker-track" data-v-e8e7d083><!--[-->`);
      ssrRenderList(__props.block.words, (word, index) => {
        _push(`<div class="ticker-item" data-v-e8e7d083>${ssrInterpolate(word)}</div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/blocks/TickerBlock.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const TickerBlock = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-e8e7d083"]]);
const _sfc_main = {
  __name: "BlockRenderer",
  __ssrInlineRender: true,
  props: {
    block: {
      type: Object,
      required: true
    },
    isFirstBlock: {
      type: Boolean,
      default: false
    }
  },
  setup(__props) {
    const blockComponents = {
      full_image: _sfc_main$i,
      image_text: ImageText,
      full_text: _sfc_main$g,
      carousel: _sfc_main$f,
      three_column_cards: _sfc_main$e,
      video: _sfc_main$d,
      call_to_action: _sfc_main$c,
      headline_text: HeadlineText,
      pricing_tables: _sfc_main$a,
      faq_accordion: FaqAccordion,
      team_members: _sfc_main$8,
      logo_showcase: _sfc_main$7,
      contact_form: ContactForm,
      icon_cards: IconCards,
      blog_posts: BlogPosts,
      service_cards: ServiceCards,
      portfolio_items: PortfolioItems,
      ticker: TickerBlock
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (blockComponents[__props.block.type]) {
        ssrRenderVNode(_push, createVNode(resolveDynamicComponent(blockComponents[__props.block.type]), mergeProps({
          block: __props.block.data,
          "is-first-block": __props.isFirstBlock
        }, _attrs), null), _parent);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/BlockRenderer.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
