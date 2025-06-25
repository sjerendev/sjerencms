function useStructuredData(type, data) {
  if (typeof window === "undefined") return;
  const origin = window.location.origin;
  const currentUrl = window.location.href;
  const addJsonLd = (jsonLd) => {
    const script = document.createElement("script");
    script.type = "application/ld+json";
    script.text = JSON.stringify(jsonLd);
    document.head.appendChild(script);
  };
  switch (type) {
    case "BlogPosting":
      addJsonLd({
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        headline: data.title,
        description: data.meta_description,
        image: data.hero_image ? `${origin}/storage/${data.hero_image}` : null,
        author: {
          "@type": "Organization",
          name: "Kalibr",
          url: "https://kalibr.se"
        },
        publisher: {
          "@type": "Organization",
          name: "Kalibr",
          logo: {
            "@type": "ImageObject",
            url: `${origin}/images/logo.png`
          }
        },
        datePublished: data.published_at,
        dateModified: data.updated_at || data.published_at,
        mainEntityOfPage: {
          "@type": "WebPage",
          "@id": currentUrl
        }
      });
      break;
    case "Blog":
      addJsonLd({
        "@context": "https://schema.org",
        "@type": "Blog",
        name: "Kalibr Blog",
        description: "Läs de senaste inläggen från Kalibr om webb, design och AI.",
        publisher: {
          "@type": "Organization",
          name: "Kalibr",
          logo: {
            "@type": "ImageObject",
            url: `${origin}/images/logo.png`
          }
        },
        url: currentUrl
      });
      break;
    case "Organization":
      addJsonLd({
        "@context": "https://schema.org",
        "@type": "Organization",
        name: "Kalibr",
        url: "https://kalibr.se",
        logo: `${origin}/images/logo.png`,
        description: "Kalibr är en Webb, Design & AI-byrå med bas i Malmö men har kunder i hela Sverige.",
        address: {
          "@type": "PostalAddress",
          streetAddress: "Simrishamnsgatan 20a",
          addressLocality: "Malmö",
          postalCode: "214 23",
          addressCountry: "SE"
        },
        contactPoint: {
          "@type": "ContactPoint",
          telephone: "+46-736-151220",
          email: "info@kalibr.se",
          contactType: "customer service"
        }
      });
      break;
  }
}
export {
  useStructuredData as u
};
