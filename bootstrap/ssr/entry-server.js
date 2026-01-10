import { ref, computed, onMounted, onUnmounted, watch, resolveComponent, mergeProps, unref, withCtx, openBlock, createBlock, toDisplayString, createTextVNode, useSSRContext, createVNode, createCommentVNode, createSSRApp } from "vue";
import { createHead, renderHeadToString } from "@vueuse/head";
import { defineStore, createPinia } from "pinia";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrRenderSlot, ssrRenderStyle, ssrRenderClass } from "vue/server-renderer";
import { useRoute, useRouter, createRouter as createRouter$1, createWebHistory, createMemoryHistory } from "vue-router";
import gsap, { gsap as gsap$1 } from "gsap";
import renderer from "@vue/server-renderer";
const useDarkModeStore = defineStore("darkMode", () => {
  const isDark = ref(false);
  function toggleDarkMode() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle("dark");
    localStorage.setItem("darkMode", isDark.value ? "dark" : "light");
  }
  function initDarkMode() {
    const savedMode = localStorage.getItem("darkMode");
    if (savedMode === "dark") {
      isDark.value = true;
      document.documentElement.classList.add("dark");
    }
  }
  return {
    isDark,
    toggleDarkMode,
    initDarkMode
  };
});
function useLanguage() {
  const route = useRoute();
  const router = useRouter();
  const currentLanguage = computed(() => {
    var _a;
    return ((_a = route.meta) == null ? void 0 : _a.language) || "sv";
  });
  const isSwedish = computed(() => currentLanguage.value === "sv");
  const isEnglish = computed(() => currentLanguage.value === "en");
  const getLocalizedPath = (path, targetLanguage = null) => {
    const lang = targetLanguage || currentLanguage.value;
    if (lang === "sv") {
      return path;
    } else if (lang === "en") {
      return `/en${path}`;
    }
    return path;
  };
  const switchLanguage = (newLang) => {
    if (newLang === currentLanguage.value) return;
    const currentPath = route.path;
    let newPath;
    if (newLang === "sv") {
      newPath = currentPath.replace(/^\/en/, "") || "/";
    } else if (newLang === "en") {
      if (currentPath.startsWith("/en")) {
        newPath = currentPath;
      } else {
        newPath = `/en${currentPath}`;
      }
    }
    if (newLang === "en") {
      newPath = newPath.replace("/blogg", "/blog");
    } else if (newLang === "sv") {
      newPath = newPath.replace("/blog", "/blogg");
    }
    router.push(newPath);
  };
  const getLanguageLabel = (lang) => {
    return {
      "sv": "Svenska",
      "en": "English"
    }[lang] || lang;
  };
  return {
    currentLanguage,
    isSwedish,
    isEnglish,
    getLocalizedPath,
    switchLanguage,
    getLanguageLabel
  };
}
const scrollThreshold = 5;
const _sfc_main$8 = {
  __name: "NormalNav",
  __ssrInlineRender: true,
  props: {
    navigation: {
      type: Array,
      required: true
    },
    settings: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    useDarkModeStore();
    const { currentLanguage } = useLanguage();
    const isOpen = ref(false);
    const menuContainer = ref(null);
    const isSticky = ref(false);
    const hasScrolled = ref(false);
    const lastScrollTop = ref(0);
    const getRouteForSlug = (slug) => {
      const blogSlug = currentLanguage.value === "en" ? "blog" : "blogg";
      if (slug === "blogg" || slug === "blog") {
        return { name: currentLanguage.value === "en" ? "page-en" : "page", params: { slug: blogSlug } };
      }
      return { name: currentLanguage.value === "en" ? "page-en" : "page", params: { slug } };
    };
    const handleScroll = () => {
      const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
      hasScrolled.value = currentScrollTop > 0;
      if (Math.abs(currentScrollTop - lastScrollTop.value) <= scrollThreshold) {
        return;
      }
      if (currentScrollTop < lastScrollTop.value && currentScrollTop > 0) {
        isSticky.value = true;
      } else {
        isSticky.value = false;
      }
      lastScrollTop.value = currentScrollTop;
    };
    onMounted(() => {
      window.addEventListener("scroll", handleScroll, { passive: true });
    });
    onUnmounted(() => {
      window.removeEventListener("scroll", handleScroll);
    });
    function toggleMenu(open) {
      if (open) {
        gsap.fromTo(
          menuContainer.value,
          {
            scaleY: 0,
            transformOrigin: "top"
          },
          {
            scaleY: 1,
            duration: 0.5,
            ease: "power2.out"
          }
        );
      } else {
        gsap.to(menuContainer.value, {
          scaleY: 0,
          duration: 0.5,
          ease: "power2.in",
          transformOrigin: "top"
        });
      }
    }
    watch(isOpen, (newValue) => {
      toggleMenu(newValue);
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_router_link = resolveComponent("router-link");
      _push(`<nav${ssrRenderAttrs(mergeProps({
        class: ["px-8 py-4 transition-all duration-300 z-50", {
          "fixed w-full top-0": isSticky.value && hasScrolled.value,
          "relative": !isSticky.value || !hasScrolled.value
        }],
        style: {
          backgroundColor: "var(--background)",
          boxShadow: isSticky.value ? "0 2px 4px rgba(0,0,0,0.1)" : "none",
          transform: isSticky.value && hasScrolled.value ? "translateY(0)" : !isSticky.value && hasScrolled.value ? "translateY(-100%)" : "none"
        }
      }, _attrs))}><div class="items-center justify-between hidden md:flex">`);
      _push(ssrRenderComponent(_component_router_link, {
        to: unref(currentLanguage) === "en" ? "/en/" : "/",
        class: "flex items-center"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d;
          if (_push2) {
            if ((_a = __props.settings) == null ? void 0 : _a.logo) {
              _push2(`<img${ssrRenderAttr("src", __props.settings.logo)} alt="Logo" class="h-8"${_scopeId}>`);
            } else {
              _push2(`<span class="text-xl font-bold"${_scopeId}>${ssrInterpolate((_b = __props.settings) == null ? void 0 : _b.site_name)}</span>`);
            }
          } else {
            return [
              ((_c = __props.settings) == null ? void 0 : _c.logo) ? (openBlock(), createBlock("img", {
                key: 0,
                src: __props.settings.logo,
                alt: "Logo",
                class: "h-8"
              }, null, 8, ["src"])) : (openBlock(), createBlock("span", {
                key: 1,
                class: "text-xl font-bold"
              }, toDisplayString((_d = __props.settings) == null ? void 0 : _d.site_name), 1))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="flex space-x-8 items-center"><!--[-->`);
      ssrRenderList(__props.navigation, (item) => {
        _push(`<div class="relative group">`);
        if (!item.children) {
          _push(ssrRenderComponent(_component_router_link, {
            to: getRouteForSlug(item.slug),
            class: "text-[var(--text-color)] hover:opacity-70 transition-opacity"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(item.title)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(item.title), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(`<div class="relative inline-block group"><button class="flex items-center text-[var(--text-color)] hover:opacity-70 transition-opacity space-x-1"><span>${ssrInterpolate(item.title)}</span><svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button><div class="absolute -bottom-2 left-0 right-0 h-2 bg-transparent"></div><div class="absolute left-0 z-10 invisible w-48 py-2 mt-2 bg-[var(--background)] rounded-md shadow-lg group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200"><!--[-->`);
          ssrRenderList(item.children, (child) => {
            _push(ssrRenderComponent(_component_router_link, {
              key: child.id,
              to: getRouteForSlug(child.slug),
              class: "block px-4 py-2 text-[var(--text-color)] hover:opacity-70 transition-opacity"
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(child.title)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(child.title), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          });
          _push(`<!--]--></div></div>`);
        }
        _push(`</div>`);
      });
      _push(`<!--]--></div></div><div class="md:hidden"><div class="flex items-center justify-between">`);
      _push(ssrRenderComponent(_component_router_link, {
        to: unref(currentLanguage) === "en" ? "/en/" : "/",
        class: "flex items-center"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d;
          if (_push2) {
            if ((_a = __props.settings) == null ? void 0 : _a.logo) {
              _push2(`<img${ssrRenderAttr("src", __props.settings.logo)} alt="Logo" class="h-8"${_scopeId}>`);
            } else {
              _push2(`<span class="text-xl font-bold"${_scopeId}>${ssrInterpolate((_b = __props.settings) == null ? void 0 : _b.site_name)}</span>`);
            }
          } else {
            return [
              ((_c = __props.settings) == null ? void 0 : _c.logo) ? (openBlock(), createBlock("img", {
                key: 0,
                src: __props.settings.logo,
                alt: "Logo",
                class: "h-8"
              }, null, 8, ["src"])) : (openBlock(), createBlock("span", {
                key: 1,
                class: "text-xl font-bold"
              }, toDisplayString((_d = __props.settings) == null ? void 0 : _d.site_name), 1))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<button class="text-[var(--text-color)]"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">`);
      if (!isOpen.value) {
        _push(`<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>`);
      } else {
        _push(`<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>`);
      }
      _push(`</svg></button></div>`);
      if (isOpen.value) {
        _push(`<div class="fixed inset-x-0 top-[64px] bottom-0 bg-[var(--background)] z-50 overflow-y-auto border-t border-gray-200"><div class="flex flex-col items-center py-8 space-y-6"><!--[-->`);
        ssrRenderList(__props.navigation, (item) => {
          _push(`<!--[-->`);
          if (!item.children) {
            _push(ssrRenderComponent(_component_router_link, {
              to: getRouteForSlug(item.slug),
              class: "text-[var(--text-color)] text-xl hover:opacity-70 transition-opacity",
              onClick: ($event) => isOpen.value = false
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(item.title)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(item.title), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
          } else {
            _push(`<div class="flex flex-col items-center space-y-4"><span class="text-[var(--text-color)] text-lg">${ssrInterpolate(item.title)}</span><div class="flex flex-col items-center space-y-4"><!--[-->`);
            ssrRenderList(item.children, (child) => {
              _push(ssrRenderComponent(_component_router_link, {
                key: child.id,
                to: getRouteForSlug(child.slug),
                class: "text-[var(--text-color)] text-base hover:opacity-70 transition-opacity",
                onClick: ($event) => isOpen.value = false
              }, {
                default: withCtx((_, _push2, _parent2, _scopeId) => {
                  if (_push2) {
                    _push2(`${ssrInterpolate(child.title)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(child.title), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent));
            });
            _push(`<!--]--></div></div>`);
          }
          _push(`<!--]-->`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></nav>`);
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/layout/navigation/NormalNav.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$7 = {
  __name: "MagneticElement",
  __ssrInlineRender: true,
  props: {
    // Magnetic strength (how much the element moves towards the cursor)
    strength: {
      type: Number,
      default: 0.3
    },
    // Animation duration for the magnetic effect
    duration: {
      type: Number,
      default: 0.4
    },
    // Animation duration for snapping back to original position
    snapDuration: {
      type: Number,
      default: 0.6
    },
    // Easing function for magnetic movement
    ease: {
      type: String,
      default: "power2.out"
    },
    // Easing function for snapping back
    snapEase: {
      type: String,
      default: "elastic.out(1, 0.3)"
    },
    // Whether to scale the element on hover
    scale: {
      type: Boolean,
      default: false
    },
    // Scale amount when hovering
    scaleAmount: {
      type: Number,
      default: 1.05
    },
    // Whether to add a subtle rotation effect
    rotate: {
      type: Boolean,
      default: false
    },
    // Maximum rotation in degrees
    maxRotation: {
      type: Number,
      default: 5
    },
    // Disable on mobile devices
    disableOnMobile: {
      type: Boolean,
      default: true
    }
  },
  setup(__props) {
    const magneticElement = ref(null);
    onMounted(() => {
      gsap$1.set(magneticElement.value, {
        x: 0,
        y: 0,
        rotation: 0,
        scale: 1
      });
    });
    onUnmounted(() => {
      gsap$1.killTweensOf(magneticElement.value);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        ref_key: "magneticElement",
        ref: magneticElement,
        class: "magnetic-element"
      }, _attrs))} data-v-15834192>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</div>`);
    };
  }
};
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/common/MagneticElement.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const MagneticElement = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["__scopeId", "data-v-15834192"]]);
const _sfc_main$6 = {
  __name: "HamburgerNav",
  __ssrInlineRender: true,
  props: {
    navigation: {
      type: Array,
      required: true
    },
    settings: {
      type: Object,
      required: true
    },
    showContact: Boolean,
    showSocial: Boolean,
    contactInfo: String,
    facebook: String,
    instagram: String,
    linkedin: String
  },
  setup(__props) {
    const { currentLanguage } = useLanguage();
    const isOpen = ref(false);
    const menuContainer = ref(null);
    const getRouteForSlug = (slug) => {
      const blogSlug = currentLanguage.value === "en" ? "blog" : "blogg";
      if (slug === "blogg" || slug === "blog") {
        return { name: currentLanguage.value === "en" ? "page-en" : "page", params: { slug: blogSlug } };
      }
      return { name: currentLanguage.value === "en" ? "page-en" : "page", params: { slug } };
    };
    function toggleMenu(open) {
      if (open) {
        gsap.fromTo(
          menuContainer.value,
          {
            scaleY: 0,
            transformOrigin: "top"
          },
          {
            scaleY: 1,
            duration: 0.5,
            ease: "power2.out"
          }
        );
      } else {
        gsap.to(menuContainer.value, {
          scaleY: 0,
          duration: 0.5,
          ease: "power2.in",
          transformOrigin: "top"
        });
      }
    }
    watch(isOpen, (newValue) => {
      toggleMenu(newValue);
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_router_link = resolveComponent("router-link");
      _push(`<nav${ssrRenderAttrs(mergeProps({ class: "relative px-8 py-6" }, _attrs))}><div class="flex items-center justify-between">`);
      _push(ssrRenderComponent(_component_router_link, {
        to: unref(currentLanguage) === "en" ? "/en/" : "/",
        class: "flex items-center"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d;
          if (_push2) {
            if ((_a = __props.settings) == null ? void 0 : _a.logo) {
              _push2(`<img${ssrRenderAttr("src", __props.settings.logo)} alt="Logo" class="h-8"${_scopeId}>`);
            } else {
              _push2(`<span class="text-xl font-bold"${_scopeId}>${ssrInterpolate((_b = __props.settings) == null ? void 0 : _b.site_name)}</span>`);
            }
          } else {
            return [
              ((_c = __props.settings) == null ? void 0 : _c.logo) ? (openBlock(), createBlock("img", {
                key: 0,
                src: __props.settings.logo,
                alt: "Logo",
                class: "h-8"
              }, null, 8, ["src"])) : (openBlock(), createBlock("span", {
                key: 1,
                class: "text-xl font-bold"
              }, toDisplayString((_d = __props.settings) == null ? void 0 : _d.site_name), 1))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(MagneticElement, {
        strength: 0.8,
        scale: true,
        "scale-amount": 1.3,
        rotate: true,
        "max-rotation": 8
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<button class="bg-white p-3 rounded-full"${_scopeId}>`);
            if (!isOpen.value) {
              _push2(`<svg class="w-6 h-6 text-background-color" fill="none" viewBox="0 0 24 24" stroke="currentColor"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"${_scopeId}></path></svg>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</button>`);
          } else {
            return [
              createVNode("button", {
                onClick: ($event) => isOpen.value = !isOpen.value,
                class: "bg-white p-3 rounded-full"
              }, [
                !isOpen.value ? (openBlock(), createBlock("svg", {
                  key: 0,
                  class: "w-6 h-6 text-background-color",
                  fill: "none",
                  viewBox: "0 0 24 24",
                  stroke: "currentColor"
                }, [
                  createVNode("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M4 6h16M4 12h16M4 18h16"
                  })
                ])) : createCommentVNode("", true)
              ], 8, ["onClick"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div style="${ssrRenderStyle([
        isOpen.value ? null : { display: "none" },
        { "height": "100svh" }
      ])}" class="fixed inset-0 z-50"><div class="bg-white"><div class="flex items-center justify-between px-4 py-4">`);
      _push(ssrRenderComponent(_component_router_link, {
        to: unref(currentLanguage) === "en" ? "/en/" : "/",
        class: "flex items-center"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d;
          if (_push2) {
            if ((_a = __props.settings) == null ? void 0 : _a.logo) {
              _push2(`<img${ssrRenderAttr("src", __props.settings.logo)} alt="Logo" class="h-8"${_scopeId}>`);
            } else {
              _push2(`<span class="text-xl font-bold"${_scopeId}>${ssrInterpolate((_b = __props.settings) == null ? void 0 : _b.site_name)}</span>`);
            }
          } else {
            return [
              ((_c = __props.settings) == null ? void 0 : _c.logo) ? (openBlock(), createBlock("img", {
                key: 0,
                src: __props.settings.logo,
                alt: "Logo",
                class: "h-8"
              }, null, 8, ["src"])) : (openBlock(), createBlock("span", {
                key: 1,
                class: "text-xl font-bold"
              }, toDisplayString((_d = __props.settings) == null ? void 0 : _d.site_name), 1))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<button class="text-gray-900"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div><div class="origin-top bg-white" style="${ssrRenderStyle({ "height": "calc(100svh - 64px)" })}"><div class="container px-4 py-16 mx-auto"><div class="mb-12 text-center"><!--[-->`);
      ssrRenderList(__props.navigation, (item) => {
        _push(ssrRenderComponent(_component_router_link, {
          key: item.id,
          to: getRouteForSlug(item.slug),
          onClick: ($event) => isOpen.value = false,
          class: "block py-4 text-2xl text-gray-900 hover:text-gray-600"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(item.title)}`);
            } else {
              return [
                createTextVNode(toDisplayString(item.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
      });
      _push(`<!--]--></div>`);
      if (__props.showContact && __props.contactInfo) {
        _push(`<div class="mb-8 text-center">${__props.contactInfo ?? ""}</div>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.showSocial) {
        _push(`<div class="flex justify-center space-x-6">`);
        if (__props.facebook) {
          _push(`<a${ssrRenderAttr("href", __props.facebook)} target="_blank" class="text-gray-900 hover:text-gray-600"><span class="sr-only">Facebook</span><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg></a>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.instagram) {
          _push(`<a${ssrRenderAttr("href", __props.instagram)} target="_blank" class="text-gray-900 hover:text-gray-600"><span class="sr-only">Instagram</span><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"></path></svg></a>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.linkedin) {
          _push(`<a${ssrRenderAttr("href", __props.linkedin)} target="_blank" class="text-gray-900 hover:text-gray-600"><span class="sr-only">LinkedIn</span><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path></svg></a>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></nav>`);
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/layout/navigation/HamburgerNav.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
  __name: "Navigation",
  __ssrInlineRender: true,
  props: {
    type: {
      type: String,
      default: "normal"
    },
    settings: {
      type: Object,
      required: true
    },
    showContact: {
      type: Boolean,
      default: false
    },
    showSocial: {
      type: Boolean,
      default: false
    },
    contactInfo: {
      type: String,
      default: ""
    },
    facebook: {
      type: String,
      default: ""
    },
    instagram: {
      type: String,
      default: ""
    },
    linkedin: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    const menuItems = ref([]);
    onMounted(async () => {
      try {
        const response = await fetch("/api/navigation");
        const pages = await response.json();
        const topLevelPages = pages.filter((page) => !page.parent_id);
        menuItems.value = topLevelPages.map((page) => {
          const children = pages.filter((child) => child.parent_id === page.id);
          return {
            ...page,
            isOpen: false,
            children: children.length > 0 ? children : void 0
          };
        });
      } catch (error) {
        console.error("Failed to fetch navigation:", error);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      if (__props.type === "normal") {
        _push(ssrRenderComponent(_sfc_main$8, {
          navigation: menuItems.value,
          settings: __props.settings
        }, null, _parent));
      } else {
        _push(ssrRenderComponent(_sfc_main$6, {
          navigation: menuItems.value,
          settings: __props.settings,
          "show-contact": __props.showContact,
          "show-social": __props.showSocial,
          "contact-info": __props.contactInfo,
          facebook: __props.facebook,
          instagram: __props.instagram,
          linkedin: __props.linkedin
        }, null, _parent));
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/layout/Navigation.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "Footer",
  __ssrInlineRender: true,
  setup(__props) {
    const { currentLanguage } = useLanguage();
    const settings = ref(null);
    const menuItems = ref([]);
    const getRouteForSlug = (slug) => {
      if (slug === "home") {
        return currentLanguage.value === "en" ? "/en/" : "/";
      }
      const blogSlug = currentLanguage.value === "en" ? "blog" : "blogg";
      if (slug === "blogg" || slug === "blog") {
        return { name: currentLanguage.value === "en" ? "page-en" : "page", params: { slug: blogSlug } };
      }
      return { name: currentLanguage.value === "en" ? "page-en" : "page", params: { slug } };
    };
    onMounted(async () => {
      try {
        const [navResponse, settingsResponse] = await Promise.all([
          fetch("/api/navigation/footer"),
          fetch("/api/settings")
        ]);
        const [pages, siteSettings] = await Promise.all([
          navResponse.json(),
          settingsResponse.json()
        ]);
        menuItems.value = pages;
        settings.value = siteSettings;
      } catch (error) {
        console.error("Failed to fetch footer data:", error);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c;
      const _component_router_link = resolveComponent("router-link");
      _push(`<footer${ssrRenderAttrs(mergeProps({ class: "footer-bg py-12 text-white" }, _attrs))} data-v-55a62e24><div class="container px-4 mx-auto" data-v-55a62e24><div class="grid grid-cols-1 lg:grid-cols-4 gap-8 text-center lg:text-start" data-v-55a62e24><div class="footer-column" data-v-55a62e24><h3 class="mb-4 text-xl font-bold min-h-[28px]" data-v-55a62e24>Kalibr.</h3><p class="text-gray-400 min-h-[96px]" data-v-55a62e24>Vi är en utvecklings- ai- och designbyrå i Malmö, men har hela världen som vårt spelfält. Vi brinner för att skapa lösningar som gör skillnad.</p></div><div class="footer-column" data-v-55a62e24><h3 class="mb-4 text-xl font-bold min-h-[28px]" data-v-55a62e24>Snabblänkar</h3><ul class="space-y-2 min-h-[96px]" data-v-55a62e24><!--[-->`);
      ssrRenderList(menuItems.value, (item) => {
        _push(`<li data-v-55a62e24>`);
        _push(ssrRenderComponent(_component_router_link, {
          to: getRouteForSlug(item.slug),
          class: "text-gray-400 hover:text-white"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(item.title)}`);
            } else {
              return [
                createTextVNode(toDisplayString(item.title), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</li>`);
      });
      _push(`<!--]--></ul></div><div class="footer-column" data-v-55a62e24><h3 class="mb-4 text-xl font-bold min-h-[28px]" data-v-55a62e24>Kontakt</h3><ul class="space-y-2 text-gray-400 min-h-[96px]" data-v-55a62e24><li data-v-55a62e24>E-post: info@kalibr.se</li><li data-v-55a62e24>Telefon: +46 (0736) 15 12 20</li><li data-v-55a62e24>Adress: Simrishamnsgatan 20a<br data-v-55a62e24>SE-214 23 Malmö<br data-v-55a62e24>Sweden</li></ul></div><div class="footer-column" data-v-55a62e24><h3 class="mb-4 text-xl font-bold min-h-[28px]" data-v-55a62e24>Follow Us</h3><div class="flex space-x-4 justify-center lg:justify-start min-h-[96px]" data-v-55a62e24>`);
      if ((_a = settings.value) == null ? void 0 : _a.facebook_url) {
        _push(`<a${ssrRenderAttr("href", settings.value.facebook_url)} target="_blank" rel="noopener" class="text-gray-400 hover:text-white" data-v-55a62e24><svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-v-55a62e24><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" data-v-55a62e24></path></svg></a>`);
      } else {
        _push(`<!---->`);
      }
      if ((_b = settings.value) == null ? void 0 : _b.instagram_url) {
        _push(`<a${ssrRenderAttr("href", settings.value.instagram_url)} target="_blank" rel="noopener" class="text-gray-400 hover:text-white" data-v-55a62e24><svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-v-55a62e24><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" data-v-55a62e24></path></svg></a>`);
      } else {
        _push(`<!---->`);
      }
      if ((_c = settings.value) == null ? void 0 : _c.linkedin_url) {
        _push(`<a${ssrRenderAttr("href", settings.value.linkedin_url)} target="_blank" rel="noopener" class="text-gray-400 hover:text-white" data-v-55a62e24><svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-v-55a62e24><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" data-v-55a62e24></path></svg></a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div><div class="pt-8 mt-8 text-center text-gray-400 border-t border-gray-800" data-v-55a62e24><p class="min-h-[24px]" data-v-55a62e24>${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())} Kalibr. Alla rättigheter reserverade.</p></div></div></footer>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/layout/Footer.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const Footer = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["__scopeId", "data-v-55a62e24"]]);
const _sfc_main$3 = {
  __name: "LanguageSwitcher",
  __ssrInlineRender: true,
  setup(__props) {
    const { currentLanguage, switchLanguage } = useLanguage();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "language-switcher" }, _attrs))} data-v-7c9fc0e2><button class="${ssrRenderClass([{
        "active": unref(currentLanguage) === "sv",
        "inactive": unref(currentLanguage) !== "sv"
      }, "language-btn"])}" data-v-7c9fc0e2> Svenska </button><span class="separator" data-v-7c9fc0e2>|</span><button class="${ssrRenderClass([{
        "active": unref(currentLanguage) === "en",
        "inactive": unref(currentLanguage) !== "en"
      }, "language-btn"])}" data-v-7c9fc0e2> English </button></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/LanguageSwitcher.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const LanguageSwitcher = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-7c9fc0e2"]]);
const _sfc_main$2 = {
  __name: "MainLayout",
  __ssrInlineRender: true,
  setup(__props) {
    const route = useRoute();
    const settings = ref({
      navigation_type: "normal",
      show_contact_in_menu: false,
      show_social_in_menu: false,
      menu_contact_info: "",
      facebook_url: "",
      instagram_url: "",
      linkedin_url: "",
      logo: "",
      site_name: "Site Name",
      head_code_snippets: "",
      footer_code_snippets: ""
    });
    const pageId = computed(() => `page-${route.params.slug || "home"}`);
    const pageClass = computed(() => `page-${route.params.slug || "home"}`);
    onMounted(async () => {
      try {
        const response = await fetch("/api/settings");
        settings.value = await response.json();
      } catch (error) {
        console.error("Error fetching settings:", error);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        id: pageId.value,
        class: pageClass.value
      }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$5, {
        type: settings.value.navigation_type,
        settings: settings.value,
        "show-contact": settings.value.show_contact_in_menu,
        "show-social": settings.value.show_social_in_menu,
        "contact-info": settings.value.menu_contact_info,
        facebook: settings.value.facebook_url,
        instagram: settings.value.instagram_url,
        linkedin: settings.value.linkedin_url
      }, null, _parent));
      _push(`<main>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</main>`);
      _push(ssrRenderComponent(Footer, null, null, _parent));
      _push(ssrRenderComponent(LanguageSwitcher, null, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/layout/MainLayout.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const COOKIE_CONSENT_KEY = "cookie_consent";
const _sfc_main$1 = {
  __name: "CookieConsent",
  __ssrInlineRender: true,
  setup(__props) {
    const hasConsent = ref(false);
    const checkConsent = () => {
      const consent = localStorage.getItem(COOKIE_CONSENT_KEY);
      hasConsent.value = consent !== null;
    };
    onMounted(() => {
      checkConsent();
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<template>`);
      if (!hasConsent.value) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed left-4 bottom-4 right-4 sm:left-auto sm:right-4 sm:bottom-4 sm:w-[400px] bg-[#111820] shadow-lg rounded-lg p-4 z-50" }, _attrs))} data-v-1b3e14cd><div class="flex flex-col gap-4" data-v-1b3e14cd><div class="text-white" data-v-1b3e14cd><h4 class="text-lg font-medium mb-2" data-v-1b3e14cd>Vi värdesätter din integritet</h4><p class="mb-0 text-sm text-white/90" data-v-1b3e14cd> Vi använder cookies för att förbättra din surfupplevelse, visa personligt anpassade annonser eller innehåll och analysera vår trafik. Genom att klicka på &quot;Godkänn&quot; samtycker du till vår användning av cookies. </p></div><div class="flex gap-2" data-v-1b3e14cd><button class="flex-1 px-4 py-2 bg-white text-[#111820] rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium" data-v-1b3e14cd> Godkänn </button><button class="flex-1 px-4 py-2 border border-white/30 text-white rounded-lg hover:bg-white/10 transition-colors text-sm font-medium" data-v-1b3e14cd> Avböj </button></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</template>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/common/CookieConsent.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const CookieConsent = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-1b3e14cd"]]);
const _sfc_main = {
  __name: "App",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_router_view = resolveComponent("router-view");
      _push(`<!--[-->`);
      _push(ssrRenderComponent(_sfc_main$2, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_router_view, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_router_view)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(CookieConsent, null, null, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/App.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
function createRouter(type = "client") {
  return createRouter$1({
    history: type === "client" ? createWebHistory() : createMemoryHistory(),
    routes: [
      // Swedish routes (no prefix)
      {
        path: "/",
        name: "home",
        component: () => import("./assets/Home-GQ37GdKZ.js"),
        meta: { language: "sv" }
      },
      {
        path: "/blogg",
        name: "blog",
        component: () => import("./assets/Blog-DtNMOMY3.js"),
        meta: { language: "sv" }
      },
      {
        path: "/blogg/:slug",
        name: "post",
        component: () => import("./assets/Post-Bzv69R6z.js"),
        meta: { language: "sv" }
      },
      {
        path: "/shop",
        name: "shop",
        component: () => import("./assets/Shop-DpsvUdDM.js"),
        meta: { language: "sv" }
      },
      {
        path: "/shop/products/:slug",
        name: "product",
        component: () => import("./assets/Product-CBdFNIVQ.js"),
        meta: { language: "sv" }
      },
      {
        path: "/shop/checkout",
        name: "checkout",
        component: () => import("./assets/Checkout-DW5-EtWq.js"),
        meta: { language: "sv" }
      },
      {
        path: "/shop/downloads",
        name: "downloads",
        component: () => import("./assets/Downloads-B3QxNZub.js"),
        meta: { language: "sv" }
      },
      // English routes (with /en prefix)
      {
        path: "/en",
        name: "home-en",
        component: () => import("./assets/Home-GQ37GdKZ.js"),
        meta: { language: "en" }
      },
      {
        path: "/en/blog",
        name: "blog-en",
        component: () => import("./assets/Blog-DtNMOMY3.js"),
        meta: { language: "en" }
      },
      {
        path: "/en/blog/:slug",
        name: "post-en",
        component: () => import("./assets/Post-Bzv69R6z.js"),
        meta: { language: "en" }
      },
      {
        path: "/en/shop",
        name: "shop-en",
        component: () => import("./assets/Shop-DpsvUdDM.js"),
        meta: { language: "en" }
      },
      {
        path: "/en/shop/products/:slug",
        name: "product-en",
        component: () => import("./assets/Product-CBdFNIVQ.js"),
        meta: { language: "en" }
      },
      {
        path: "/en/shop/checkout",
        name: "checkout-en",
        component: () => import("./assets/Checkout-DW5-EtWq.js"),
        meta: { language: "en" }
      },
      {
        path: "/en/shop/downloads",
        name: "downloads-en",
        component: () => import("./assets/Downloads-B3QxNZub.js"),
        meta: { language: "en" }
      },
      {
        path: "/en/:slug",
        name: "page-en",
        component: () => import("./assets/Page-DDrXKOHe.js"),
        meta: { language: "en" }
      },
      // Swedish page routes (must come after specific routes)
      {
        path: "/:slug",
        name: "page",
        component: () => import("./assets/Page-DDrXKOHe.js"),
        meta: { language: "sv" }
      },
      {
        path: "/:pathMatch(.*)*",
        name: "not-found",
        component: () => import("./assets/NotFound-BMJPkwxu.js")
      }
    ]
  });
}
async function render(url) {
  const app = createSSRApp(_sfc_main);
  const head = createHead();
  const pinia = createPinia();
  const router = createRouter("server");
  app.use(router);
  app.use(head);
  app.use(pinia);
  await router.push(url);
  await router.isReady();
  const ctx = {};
  const html = await renderer.renderToString(app, ctx);
  const { headTags } = await renderHeadToString(head);
  return {
    html,
    head: headTags,
    state: null
    // You can pass initial state here if needed
  };
}
export {
  MagneticElement as M,
  _export_sfc as _,
  render,
  useLanguage as u
};
