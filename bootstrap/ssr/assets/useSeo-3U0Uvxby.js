import { useHead } from "@vueuse/head";
import "vue";
function useSeo(pageData, defaults = {}) {
  const isClient = typeof window !== "undefined";
  const origin = isClient ? window.location.origin : "";
  const currentUrl = isClient ? window.location.href : "";
  useHead(() => {
    var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k;
    return {
      title: ((_a = pageData.value) == null ? void 0 : _a.meta_title) || ((_b = pageData.value) == null ? void 0 : _b.title) || defaults.title,
      meta: [
        {
          name: "description",
          content: ((_c = pageData.value) == null ? void 0 : _c.meta_description) || defaults.description
        },
        // OpenGraph
        {
          property: "og:title",
          content: ((_d = pageData.value) == null ? void 0 : _d.meta_title) || ((_e = pageData.value) == null ? void 0 : _e.title) || defaults.title
        },
        {
          property: "og:description",
          content: ((_f = pageData.value) == null ? void 0 : _f.meta_description) || defaults.description
        },
        {
          property: "og:image",
          content: ((_g = pageData.value) == null ? void 0 : _g.hero_image) ? `${origin}/storage/${pageData.value.hero_image}` : defaults.image ? `${origin}${defaults.image}` : null
        },
        {
          property: "og:type",
          content: defaults.type || "website"
        },
        // Twitter
        {
          name: "twitter:card",
          content: "summary_large_image"
        },
        {
          name: "twitter:title",
          content: ((_h = pageData.value) == null ? void 0 : _h.meta_title) || ((_i = pageData.value) == null ? void 0 : _i.title) || defaults.title
        },
        {
          name: "twitter:description",
          content: ((_j = pageData.value) == null ? void 0 : _j.meta_description) || defaults.description
        },
        {
          name: "twitter:image",
          content: ((_k = pageData.value) == null ? void 0 : _k.hero_image) ? `${origin}/storage/${pageData.value.hero_image}` : defaults.image ? `${origin}${defaults.image}` : null
        }
      ].filter((meta) => meta.content),
      // Remove meta tags with null content
      link: [
        {
          rel: "canonical",
          href: defaults.canonical || currentUrl || null
        }
      ].filter((link) => link.href)
    };
  });
}
export {
  useSeo as u
};
