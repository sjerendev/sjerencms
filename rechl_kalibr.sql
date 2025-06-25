-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: localhost:3306
-- Tid vid skapande: 09 feb 2025 kl 16:08
-- Serverversion: 8.0.40-cll-lve
-- PHP-version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `rechl_kalibr`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'AI', 'ai', '2024-10-31 21:42:40', '2024-10-31 21:42:40'),
(2, 'Programmering', 'programmering', '2024-11-01 21:55:39', '2024-11-01 21:55:39'),
(3, 'Tjänster', 'tjanster', '2025-02-05 15:47:02', '2025-02-05 15:47:02'),
(4, 'Wordpress', 'wordpress', '2025-02-05 18:47:18', '2025-02-05 18:47:18');

-- --------------------------------------------------------

--
-- Tabellstruktur `category_post`
--

CREATE TABLE `category_post` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `post_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `category_post`
--

INSERT INTO `category_post` (`id`, `category_id`, `post_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(4, 1, 3, NULL, NULL),
(5, 2, 4, NULL, NULL),
(6, 1, 4, NULL, NULL),
(7, 1, 5, NULL, NULL),
(8, 3, 6, NULL, NULL),
(9, 4, 7, NULL, NULL),
(10, 1, 7, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `media`
--

CREATE TABLE `media` (
  `id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_pages_table', 2),
(5, '2024_01_01_000002_create_posts_table', 2),
(6, '2024_01_01_000003_create_categories_table', 2),
(7, '2022_12_14_083707_create_settings_table', 3),
(8, '2024_01_01_000000_create_general_settings', 3),
(9, '2024_10_31_211007_create_general_settings', 3),
(10, '2024_10_31_221315_add_hero_image_to_pages_table', 4),
(11, '2024_11_01_150838_add_hero_image_to_posts_table', 5),
(12, '2024_11_01_152110_add_hero_fields_to_posts_table', 6),
(13, '2024_11_01_152520_add_is_homepage_to_pages_table', 7),
(14, '2024_11_01_201608_add_meta_fields_to_pages_and_posts_tables', 7),
(15, '2024_11_01_203515_add_hero_fields_to_pages_table', 8),
(16, '2024_11_01_215833_create_category_post_table', 9),
(17, '2024_11_03_183138_add_sort_order_to_pages_table', 10),
(18, '2024_11_03_200237_add_hero_height_to_pages_table', 11),
(19, '2024_11_05_191915_create_media_table', 12),
(20, '2024_11_05_192343_add_hero_background_fields_to_pages_table', 13),
(21, '2024_11_05_192545_add_hero_background_fields_to_posts_table', 14),
(22, '2024_11_07_215921_add_navigation_settings_to_settings_table', 15),
(23, '2024_11_07_221415_add_navigation_settings', 16),
(25, '2025_01_07_195740_add_parent_id_to_pages_table', 17),
(26, '2025_01_13_113420_add_blog_fields_to_posts_table', 18),
(27, '2025_01_13_153650_add_list_image_to_posts_table', 18),
(28, '2025_01_14_193933_add_publish_date_to_posts_table', 19),
(29, '2025_01_21_101756_add_navigation_options_to_pages', 20),
(30, '2025_01_26_213055_change_content_column_in_posts_table', 21),
(31, '2025_02_05_214513_add_code_snippets_to_general_settings', 22),
(32, '2025_02_05_214756_remove_favicon_from_general_settings', 22);

-- --------------------------------------------------------

--
-- Tabellstruktur `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` json DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hero_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hero_headline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_subheadline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_paragraph` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hero_cta_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `hero_height` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_background_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_background_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_text_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `show_in_main_nav` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_footer_nav` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `is_published`, `created_at`, `updated_at`, `hero_image`, `is_homepage`, `meta_title`, `meta_description`, `hero_headline`, `hero_subheadline`, `hero_paragraph`, `hero_cta_text`, `hero_cta_url`, `sort_order`, `hero_height`, `hero_background_type`, `hero_background_color`, `hero_text_color`, `parent_id`, `show_in_main_nav`, `show_in_footer_nav`) VALUES
(1, 'Hem', 'hem', '[{\"data\": {\"text\": \"# Vi bygger framtiden.\\n## Pixel för pixel. Algoritm för algoritm.\\n\\n<p>Kalibr. är en Webb & AI-byrå med bas i Malmö, men vår ambition sträcker sig långt bortom stadens gränser – vi tar på oss uppdrag över hela Sverige och världen. Vi är specialister inom webbutveckling, marknadsföring och allt som rör generativ AI.</p>\\n\\n<p>För oss är varje pixel och varje algoritm delar i en större helhet. Vi kombinerar kreativitet & erfarenhet med artificiell intelligens för att effektivisera design, utveckling, test och tillväxt.</p>\\n\", \"image\": \"content-images/01JH6K24PGTAR529XA3JA2EGCQ.svg\", \"content\": null, \"heading\": null, \"html_text\": null, \"button1_url\": \"/om-kalibr\", \"button2_url\": \"/kontakt\", \"button1_text\": \"Om Kalibr.\", \"button2_text\": \"Kontakta oss\", \"section_class\": \"home-hero\", \"image_position\": \"right\", \"reverse_layout\": true, \"background_color\": null}, \"type\": \"image_text\"}, {\"data\": {\"content\": null, \"heading\": null}, \"type\": \"text\"}, {\"data\": {\"cards\": [{\"icon\": \"brackets-curly\", \"text\": \"Vi erbjuder skräddarsydda utvecklingstjänster inom Front- & Backend-utveckling, E-handel, SaaS och Appar. Vårt team av erfarna utvecklare skapar innovativa och användarvänliga lösningar med hjälp av AI som driver din digitala framgång.\", \"title\": \"Utveckling\", \"link_url\": \"/utveckling\", \"icon_color\": \"#18F2B2\", \"icon_style\": \"regular\"}, {\"icon\": \"robot\", \"text\": \"Vår expertis inom Generativ AI ger dig kraftfulla verktyg för framtiden. Vi erbjuder skräddarsydda tjänster inom AI Automation, Chatbots, Voice Agents och AI Agenter som effektiviserar ditt företag och förbättrar kundupplevelsen. \", \"title\": \"Generativ AI\", \"link_url\": \"/generativ-ai\", \"icon_color\": \"#18F2B2\", \"icon_style\": \"regular\"}, {\"icon\": \"list-magnifying-glass\", \"text\": \"Våra avancerade SEO- och Sociala Medier-tjänster kombinerar expertkunskap med AI-teknologi för att leverera snabbare och mer effektiva resultat. Genom att använda AI för analys, optimering, strategi och sociala medier säkerställer vi bästa resultat.\", \"title\": \"SEO & Sociala Medier\", \"link_url\": \"/generativ-ai\", \"icon_color\": \"#18F2B2\", \"icon_style\": \"regular\"}], \"columns\": \"3\", \"section_class\": \"services\", \"section_title\": \"Vad vi erbjuder!\"}, \"type\": \"icon_cards\"}, {\"data\": {\"columns\": 3, \"show_date\": true, \"categories\": [], \"posts_count\": 3, \"section_class\": \"blog-block\", \"section_title\": \"Nyheter & Inspiration\", \"show_categories\": true, \"show_hero_image\": true}, \"type\": \"blog_posts\"}, {\"data\": {\"heading\": \"Har vi väckt ditt intresse?\", \"subheading\": \"Kontakta oss för att få reda på hur vi kan hjälpa din idé att bli verklighet\", \"error_message\": \"Sorry, there was an error sending your message. Please try again later.\", \"section_class\": \"home-contact\", \"success_message\": \"Tack för ditt meddelande. Vi återkommer så snart vi kan!\", \"notification_email\": \"info@kalibercreative.se\", \"submit_button_text\": \"Skicka meddelande\", \"section_description\": null}, \"type\": \"contact_form\"}]', 1, '2024-10-31 21:53:09', '2025-02-04 23:23:20', NULL, 1, 'Kalibr Kreativ Studio', 'Kalibr. är en Webb & AI-byrå med bas i Malmö, men vi jobbar över hela Sverige. Vi kombinerar kreativitet & erfarenhet med AI för att effektivisera design & kod.', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '#1F202D', NULL, NULL, 1, 0),
(3, 'Generativ AI', 'generativ-ai', '[{\"data\": {\"text\": \"# Generativ AI – Innovation utan gränser!\\n\\nPå denna sida hittar du våra tjänster inom Generativ AI, där vi kombinerar senaste tekniken med kreativitet för att skapa intelligenta lösningar som öppnar dörrar till nya möjligheter. Oavsett om du vill generera text, bilder, automatisera processer eller utvinna unika insikter från data – har vi verktygen och kunskapen för att göra det möjligt.\", \"section_class\": \"service-hero\"}, \"type\": \"full_text\"}, {\"data\": {\"cards\": [{\"icon\": \"robot\", \"title\": \"Chatbots – Smarta konversationer som förbättrar engagemang och effektivitet\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår chatbotutveckling fokuserar på att skapa intelligenta och anpassningsbara lösningar som förenklar kommunikationen mellan dig och dina användare. Genom att kombinera avancerad teknik med en människolik interaktion utformar vi chatbotar som inte bara svarar på frågor – de lär sig, växer och anpassar sig till dina behov över tid.\\n\\nOavsett om du vill automatisera kundsupport, generera leads, eller effektivisera interna arbetsflöden – bygger vi chatbotar som är skräddarsydda för din verksamhet.\"}, {\"icon\": \"circuitry\", \"title\": \"AI Automation – Effektivisering som växer med dina behov\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom AI Automation fokuserar på att automatisera arbetsflöden och processer med intelligens som lär sig, anpassar sig och optimerar över tid. Genom att integrera AI i dina befintliga system eller bygga helt nya lösningar från grunden skapar vi smarta automatiseringar som tar hand om repetitiva uppgifter, analyserar data i realtid och fattar proaktiva beslut – så att du kan fokusera på det som verkligen spelar roll.\\n\\nOavsett om du vill effektivisera kundservice, hantera stora datamängder, förbättra rapportering eller automatisera komplexa affärsprocesser – utvecklar vi lösningar som är skräddarsydda för din verksamhets unika utmaningar.\"}, {\"icon\": \"brain\", \"title\": \"AI Agenter – Autonoma lösningar som tänker och agerar för din verksamhet\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom AI Agenter fokuserar på att utveckla intelligenta, självlärande system som tar initiativ, fattar beslut och utför uppgifter i realtid – helt autonomt. Genom att kombinera avancerad AI med dynamiska algoritmer skapar vi agenter som inte bara reagerar på förändringar, utan också förutser och påverkar utfall till din fördel.\\n\\nOavsett om du behöver en virtuell assistent som hanterar kunddialoger, en analytisk agent som identifierar trender i dataflöden, eller en processoptimering som styr logistik och resurser – utformar vi AI Agenter som anpassar sig till din verksamhets komplexitet.\"}, {\"icon\": \"chart-scatter\", \"title\": \"Marknadsföring med AI – Smarter, snabbare och mer personlig\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom AI-driven marknadsföring fokuserar på att omvandla data till intelligenta strategier som träffar rätt målgrupp, vid rätt tid, med rätt budskap. Genom att kombinera avancerad analys, maskininlärning och generativ teknik skapar vi kampanjer som inte bara engagerar – de lär sig, optimeras i realtid och växer med dina kunders behov.\\n\\nOavsett om det handlar om målgruppssegmentering, dynamiskt anpassat innehåll, automatiserade annonser eller personliga kundresor – utnyttjar vi AI för att maximera räckvidd, konvertering och lojalitet. Våra lösningar identifierar trender, förutser beteenden och skapar automatiskt text, bilder eller rekommendationer som resonerar med din publik. Resultatet? Högre ROI & minskad manuell arbetsbörda.\"}, {\"icon\": \"hash\", \"title\": \"SEO & Sociala Medier med AI – Sökmotoroptimering och engagemang på autopilot\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom AI-driven SEO och sociala medier fokuserar på att omvandla algoritmer, data och kreativitet till synlighet och interaktion som växer organisk. Genom att kombinera generativ AI med avancerad analys skapar vi innehåll, optimerar nyckelord och engagerar målgrupper i realtid – så att din närvaro stärks både i sökmotorer och på sociala plattformar.\\n\\nOavsett om det handlar om automatiserad innehållsgenerering, smart hashtag-optimering, SEO-anpassade texter eller datadrivna sociala strategier – utnyttjar vi AI för att analysera trender, skapa personliga budskap och justera taktiken kontinuerligt\"}], \"section_class\": \"service-section\", \"section_title\": \"Vårt utbud av tjänster inom generativ AI\"}, \"type\": \"service_cards\"}, {\"data\": {\"items\": [{\"answer\": \"Generativ AI är teknik som använder maskininlärning för att skapa text, bilder, automatisera processer och extrahera insikter från data. Det kan hjälpa ditt företag genom att effektivisera arbetsflöden, förbättra kundengagemang och skapa innovativa lösningar.\", \"question\": \"Vad är Generativ AI och hur kan det hjälpa mitt företag?\"}, {\"answer\": \"Vi kan lösa en mängd projekt, inklusive chatbots, processautomatisering, marknadsföring, innehållsgenerering, datadriven analys och mer.\", \"question\": \"Vilka typer av projekt kan ni lösa med Generativ AI?\"}, {\"answer\": \"Ja, vi följer strikta säkerhetsprotokoll och dataskyddsregler för att säkerställa att alla lösningar är säkra och efterlever GDPR och andra relevanta lagar.\", \"question\": \"Är Generativ AI säkert att använda i min verksamhet?\"}, {\"answer\": \"Kostnaden varierar beroende på projektets omfattning och komplexitet. Vi erbjuder skräddarsydda offerter efter en första konsultation.\", \"question\": \"Hur mycket kostar det att implementera Generativ AI?\"}, {\"answer\": \"Våra chatbots kan automatisera kundsupport, generera leads, svara på vanliga frågor och effektivisera interna processer m.m.\", \"question\": \"Vad kan en AI-driven chatbot göra för min verksamhet?\"}, {\"answer\": \"Tiden varierar beroende på komplexitet, men vi strävar efter att leverera en första version inom några veckor.\", \"question\": \"Hur snabbt kan ni utveckla en chatbot för mitt företag?\"}, {\"answer\": \"Ja, våra chatbots använder maskininlärning för att lära sig av interaktioner och bli smartare över tid.\", \"question\": \"Kan chatbots lära sig och förbättras över tid?\"}, {\"answer\": \"Vi kan automatisera repetitiva uppgifter som datainmatning, kundsupport, rapportering, logistik med mera.\", \"question\": \"Vilka processer kan automatiseras med AI?\"}, {\"answer\": \"Vi använder avancerade algoritmer och testar noggrant för att säkerställa att automatiseringen fungerar smidigt och ger korrekta resultat.\", \"question\": \"Hur säkerställer ni att automatiseringen är effektiv?\"}, {\"answer\": \"En AI-agent är ett självlärande system som kan fatta beslut, utföra uppgifter och optimera processer autonomt. Den kan användas för kunddialoger, dataanalys, logistik och mer.\", \"question\": \"Vad är en AI-agent och hur kan den hjälpa oss?\"}, {\"answer\": \"Vi implementerar strikta säkerhetsåtgärder och regelbundna granskningar för att säkerställa att AI-agenter fungerar säkert och korrekt.\", \"question\": \"Hur hanterar ni säkerheten för AI-agenter?\"}, {\"answer\": \"AI kan analysera data, skapa personliga kampanjer, optimera annonsering i realtid och generera innehåll som engagerar er målgrupp.\", \"question\": \"Hur kan AI förbättra min marknadsföring?\"}, {\"answer\": \"Ja, vi utvecklar kampanjer som använder AI för målgruppssegmentering, dynamiskt innehåll och automatiserad rapportering.\", \"question\": \"Kan ni skapa AI-drivna marknadsföringskampanjer?\"}, {\"answer\": \"AI kan analysera söktrender, optimera nyckelord, generera SEO-anpassat innehåll och förbättra webbplatsens ranking.\", \"question\": \"Hur kan AI förbättra min SEO-strategi?\"}, {\"answer\": \"Ja, vi använder Generativ AI för att skapa text, bilder och hashtags som engagerar er målgrupp på sociala plattformar.\", \"question\": \"Kan ni automatisera innehållsgenerering för sociala medier?\"}, {\"answer\": \"Ja, vi erbjuder kontinuerlig support, uppdateringar och optimeringar för att säkerställa att din AI-lösning förblir effektiv och relevant.\", \"question\": \"Erbjuder ni support efter att AI-lösningen är implementerad?\"}], \"heading\": \"Vanliga frågor & svar\", \"subheading\": null, \"section_class\": \"faq\"}, \"type\": \"faq_accordion\"}, {\"data\": {\"heading\": \"Börja din resa mot framtiden idag\", \"subheading\": null, \"error_message\": \"Tyvärr uppstod ett fel när meddelandet skulle skickas. Vänligen försök igen senare.\", \"section_class\": \"home-contact\", \"success_message\": \"Tack för ditt meddelande. Vi kommer att kontakta dig snart!\", \"notification_email\": \"info@kalibercreative.se\", \"submit_button_text\": \"Skicka meddelande\", \"section_description\": \"Är du redo att utforska hur Generativ AI kan transformera ditt företag? Kontakta oss redan idag för en kostnadsfri konsultation. Tillsammans skapar vi en lösning som inte bara möter dina behov, utan också sätter dig i framkant i din bransch.\\n\\nFramtiden är här – låt oss hjälpa dig att forma den.\"}, \"type\": \"contact_form\"}]', 1, '2025-01-07 15:39:36', '2025-02-04 23:19:08', NULL, 0, 'Generativ AI Tjänster | Innovation & Tillväxt utan Gränser', 'Utnyttja Generativ AI för smarta lösningar: chatbots, automation, marknadsföring & SEO. Effektivisera processer & öka ROI', NULL, NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, 1, 0),
(10, 'Om Kalibr.', 'om-kalibr', '[{\"data\": {\"text\": \"# Om oss – Innovation möter expertis\\n\\nVi är en digital byrå i Malmö, men med hela världen som vårt spelfält, som brinner för att skapa lösningar som gör skillnad. Med en kombination av teknisk expertis, kreativt tänkande och en passion för innovation hjälper vi företag att växa, anpassa sig och lyckas i en ständigt föränderlig digital värld. Oavsett om det handlar om att bygga en skräddarsydd webbplats, implementera AI-drivna lösningar eller skapa en design som engagerar och konverterar, är vi här för att göra dina idéer till verklighet.\", \"section_class\": \"service-hero\"}, \"type\": \"full_text\"}, {\"data\": {\"text\": \"## Vår vision\\nVi tror på kraften i teknik och design för att skapa upplevelser som inte bara fungerar – utan inspirerar. Genom att sätta användaren i centrum och kombinera strategi med kreativitet, strävar vi efter att leverera resultat som överträffar förväntningarna.\\n\\n### Vårt arbetssätt\\nVi ser varje projekt som en möjlighet att skapa något unikt. Genom att arbeta nära våra kunder och förstå deras behov, mål och utmaningar, utvecklar vi lösningar som inte bara möter dagens krav – utan också förbereder dem för framtiden. Vårt team består av experter inom utveckling, AI och design som tillsammans skapar en helhetslösning för din digitala framgång.\\n\\n### Varför välja oss?\\n\\n- **Användarcentrerat fokus** – Vi sätter alltid användaren i centrum för allt vi gör.\\n- **Teknisk expertis** – Med erfarenhet av både frontend och backend levererar vi lösningar som fungerar.\\n- **Innovation** – Vi ligger i framkant när det gäller AI och digital utveckling.\\n- **Samarbete** – Vi ser oss som en förlängning av ditt team och arbetar nära dig för att nå dina mål.\\n- **Resultatdrivet** – Vi mäter vår framgång i din framgång.\", \"section_class\": null}, \"type\": \"full_text\"}, {\"data\": {\"heading\": \"Låt oss skapa något fantastiskt tillsammans\", \"subheading\": null, \"error_message\": \"Tyvärr uppstod ett fel när meddelandet skulle skickas. Vänligen försök igen senare.\", \"section_class\": \"home-contact\", \"success_message\": \"Tack för ditt meddelande. Vi kommer att kontakta dig snart!\", \"notification_email\": \"info@kalibercreative.se\", \"submit_button_text\": \"Skicka meddelande\", \"section_description\": \"Oavsett om du är ett nystartat företag som vill ta dina första steg online eller en etablerad aktör som vill ta nästa stora kliv, är vi här för att hjälpa dig. Tillsammans kan vi skapa lösningar som inte bara möter dagens behov – utan också morgondagens möjligheter.\\n\\nKontakta oss idag och låt oss börja resan mot din digitala framgång.\"}, \"type\": \"contact_form\"}]', 1, '2025-01-07 17:02:57', '2025-02-07 13:59:59', NULL, 0, 'Digital Byrå Malmö – Expertis & Innovation för Din Framgång', 'Vi är en digital byrå i Malmö som kombinerar teknisk expertis, AI & kreativ design för skräddarsydda lösningar. Låt oss skapa din framgång tillsammans.', NULL, NULL, NULL, NULL, NULL, 20, NULL, NULL, NULL, NULL, NULL, 1, 0),
(11, 'Blogg', 'blogg', '[]', 1, '2025-01-07 17:03:15', '2025-02-07 14:37:59', NULL, 0, 'Blogg – Senaste trenderna inom Webbutveckling, AI & Design', 'Utforska vår blogg för insikter om webbutveckling, AI-lösningar, design och digital innovation. Läs tips, guider och senaste trenderna.', NULL, NULL, NULL, NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, 1, 0),
(12, 'Kontakt', 'kontakt', '[{\"data\": {\"text\": \"Vi finns mitt på Möllan i Malmö.\\n\\nAnvänd formuläret nedan så återkommer vi så snart vi kan.\", \"image\": \"content-images/01JJN3TY90CV9JQVT1MKBSBM1Z.webp\", \"button1_url\": null, \"button2_url\": null, \"button1_text\": null, \"button2_text\": null, \"section_class\": null, \"reverse_layout\": false}, \"type\": \"image_text\"}, {\"data\": {\"heading\": \"Kontakta oss för en kostnadsfri konsultation.\", \"subheading\": null, \"error_message\": \"Tyvärr uppstod ett fel när meddelandet skulle skickas. Vänligen försök igen senare.\", \"section_class\": \"home-contact\", \"success_message\": \"Tack för ditt meddelande. Vi kommer att kontakta dig snart!\", \"notification_email\": \"info@kalibercreative.se\", \"submit_button_text\": \"Skicka meddelande\", \"section_description\": null}, \"type\": \"contact_form\"}]', 1, '2025-01-07 17:03:30', '2025-02-07 14:39:04', NULL, 0, 'Kontakta Oss – Digital Byrå i Malmö för Webb & AI-lösningar', 'Kontakta oss för skräddarsydda webb- och AI-lösningar. Vi finns i Malmö och hjälper dig med design, utveckling och innovation. Hör av dig idag!', NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL, NULL, NULL, 1, 0),
(13, 'Utveckling', 'utveckling', '[{\"data\": {\"text\": \"# Utveckling - innovativt & framtidssäkrat!\\n\\nPå denna sida hittar du vårt breda utbud av tjänster inom webb- och systemutveckling, där vi kombinerar teknik, kreativitet och senaste teknologitrender för att skapa skräddarsydda lösningar som passar just dina behov. Oavsett om du behöver en ny webbplattform, en effektiv applikation eller en sömlös integration med befintliga system – har vi expertisen och passionen för att göra det möjligt.\", \"section_class\": \"service-hero\"}, \"type\": \"full_text\"}, {\"data\": {\"cards\": [{\"icon\": \"code\", \"title\": \"Wordpress Utveckling\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Skräddarsydda teman och tillägg som gör din webbplats unik, seo optimerad och användarvänlig. Vi bygger alla våra teman från grunden så att ni får en hemsida som är specialbyggd för just er.\"}, {\"icon\": \"shopping-cart\", \"title\": \"E-handelslösningar – Driv försäljning med smarta och användarvänliga plattformar\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom e-handel fokuserar på att bygga digitala butiker som inte bara lockar besökare, utan omvandlar dem till lojala kunder. Genom att kombinera intuitiv design, säkra betallösningar och skräddarsydda funktioner skapar vi plattformar som ökar konvertering, effektiviserar lagerhantering och ger en sömlös shoppingupplevelse på alla enheter.\\n\\nOavsett om du vill lansera en ny e-butik, skala upp en befintlig eller integrera avancerade verktyg som personaliserade rekommendationer och AI-driven kundanalys – utvecklar vi lösningar som växer med din verksamhet. \"}, {\"icon\": \"palette\", \"title\": \"Frontend Utveckling – engagerande och användarvänliga upplevelser\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår frontendutveckling fokuserar på att bygga moderna, snabba och responsiva gränssnitt som fångar användarnas uppmärksamhet och ger en sömlös upplevelse. Vi specialiserar oss på React och Next.js, två av de mest kraftfulla ramverken för att skapa dynamiska och högpresterande webbapplikationer.\\n\\nOavsett om du behöver en ny webbplats, en interaktiv app eller en skräddarsydd användarupplevelse – tar vi fram lösningar som inte bara ser bra ut, utan även fungerar effektivt på alla enheter och plattformar. Genom att kombinera vår expertis med AI skapar vi smarta och anpassningsbara gränssnitt som ger dina användare en personlig och engagerande upplevelse.\"}, {\"icon\": \"code-block\", \"title\": \"Backend Utveckling – stabila och skalbara system\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår backendutveckling fokuserar på att skapa robusta, säkra och effektiva lösningar som fungerar som motorerna bakom dina digitala plattformar. Vi specialiserar oss på Laravel samt Node & Express för att utveckla kraftfulla och underhållsbara applikationer, samt API-utveckling för sömlös integration mellan olika system och tjänster.\\n\\nOavsett om du behöver en komplex databas, en säker betallösning eller en skräddarsydd applikation – bygger vi backend-system som är optimerade för prestanda och tillväxt. Genom att kombinera vår expertis med AI skapar vi intelligenta och automatiserade lösningar som effektiviserar dina processer och ger dig ett konkurrensövertag. Låt oss vara din partner för att utveckla backend-system som håller igång din digitala framgång!\"}, {\"icon\": \"head-circuit\", \"title\": \"Kod med AI – Smartare och effektivare lösningar\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Skapa kod med hjälp av AI går snabbt framåt och vi fokuserar på att utnyttja artificiell intelligens för att skapa smartare, snabbare och mer effektiva lösningar. Genom att integrera AI i vår utvecklingsprocess kan vi automatisera uppgifter, optimera kod och skapa intelligenta system som lär sig och anpassar sig över tid. \\n\\nOavsett om det handlar om att förbättra befintlig kod, utveckla AI-drivna funktioner eller bygga helt nya applikationer – använder vi AI för att maximera prestanda och innovation. \"}, {\"icon\": \"app-window\", \"title\": \"Web Apps / SaaS – Skräddarsydda plattformar som växer med ditt företag\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår utveckling av Web Apps och SaaS-lösningar fokuserar på att bygga moderna, skalbara och användarvänliga plattformar som löser komplexa utmaningar och driver digital tillväxt. Genom att kombinera robust backend med intuitiv frontend skapar vi applikationer som inte bara möter dagens behov, utan även anpassar sig till framtida krav – oavsett bransch eller verksamhetsstorlek.\\n\\nOavsett om du är ett startup som behöver en SaaS-plattform från grunden, eller ett etablerat företag som vill digitalisera och effektivisera befintliga processer – utvecklar vi lösningar som är optimerade för prestanda, säkerhet och sömlös integration med dina system. Genom att införliva AI i plattformarna skapar vi smarta funktioner som automatiserar uppgifter, personaliserar användarupplevelser och genererar datadrivna insikter i realtid.\"}, {\"icon\": \"network\", \"title\": \"API-utveckling & Integration – Sömlös kommunikation mellan system\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom API-utveckling och integration fokuserar på att koppla samman dina applikationer, tjänster och dataflöden till en effektiv helhet. Genom att utveckla skräddarsydda API:er och integrationslösningar säkerställer vi att dina system \\\"pratar\\\" med varandra – oavsett plattform eller språk – för att automatisera processer och frigöra tid för innovation.\\n\\nOavsett om du behöver koppla ihop en intern mjukvara med molntjänster, skapa ett öppet API för partners eller bygga en robust infrastruktur för realtidsdatautbyte – säkerställer vi att integrationen är säker, stabil och framtidssäker.\"}, {\"icon\": \"lifebuoy\", \"title\": \"Underhåll & Support – Din trygghet efter lansering\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom underhåll och support fokuserar på att din digitala lösning alltid fungerar på topp – även efter att den gått live. Genom proaktiv övervakning, regelbundna uppdateringar och snabba buggfixar säkerställer vi att dina system förblir säkra, snabba och anpassade till nya behov eller teknologiska framsteg.\"}], \"section_class\": \"service-section\", \"section_title\": \"Vårt utbud av tjänster inom utveckling\"}, \"type\": \"service_cards\"}, {\"data\": {\"items\": [{\"answer\": \"Vi tar emot alla typer av digitala utvecklingsprojekt, inklusive webbplatser, e-handelslösningar, mobilappar, SaaS-plattformar, API-integrationer och mer. Oavsett storlek eller komplexitet har vi expertisen att hjälpa dig.\", \"question\": \"Vilka typer av projekt tar ni emot?\"}, {\"answer\": \"Tiden varierar beroende på projektets omfattning och komplexitet. Efter en första konsultation ger vi en tidsuppskattning och en tydlig projektplan.\", \"question\": \"Hur lång tid tar det att utveckla en webbplats/app?\"}, {\"answer\": \"Kostnaden beror på projektets omfattning, funktionalitet och designkrav. Vi erbjuder skräddarsydda offerter efter en första diskussion om dina behov.\", \"question\": \"Vad kostar det att utveckla en webbplats/app?\"}, {\"answer\": \"Ja, vi skapar skräddarsydda WordPress-teman och tillägg från grunden för att säkerställa en unik och optimerad webbplats. Om det finns färdiga tillägg att använda från 3:e part där det skulle bli allt för dyrt att skapa nya tillägg så lämnar vi förslag på dessa för att göra det så kostnadseffektivt som möjligt för er.\", \"question\": \"Bygger ni WordPress-webbplatser från grunden?\"}, {\"answer\": \"Absolut, vi kan analysera och optimera din webbplats för bättre sökmotorrankning och användarvänlighet.\", \"question\": \"Kan ni optimera min befintliga WordPress-webbplats för SEO?\"}, {\"answer\": \"Ja, vi ger utbildning och vid behov dokumentation så att du enkelt kan hantera din webbplats efter lansering.\", \"question\": \"Erbjuder ni utbildning för att hantera WordPress efter leverans?\"}, {\"answer\": \"Vi jobbar primärt med WooCommerce samt skräddarsydda lösningar men har inga problem med att implementera andra lösningar vid behov.\", \"question\": \"Vilka e-handelsplattformar jobbar ni med?\"}, {\"answer\": \"Vi optimerar design, användarupplevelse och funktionalitet för att locka fler kunder och öka försäljningen.\", \"question\": \"Hur kan ni hjälpa mig att öka konverteringen i min e-butik?\"}, {\"answer\": \"Vi använder moderna ramverk som Vue 3, React och Next.js för att skapa snabba och responsiva gränssnitt. Vilket vi väljer för just ert projekt diskuterar vi med er på förhand och ger vad vi tror är den optimala lösningen för era behov. För Wordpress arbetar vi främst med vanlig html, php och css.\", \"question\": \"Vilka tekniker använder ni för frontend-utveckling?\"}, {\"answer\": \"Ja, alla våra webbplatser är responsiva och fungerar sömlöst på alla enheter.\", \"question\": \"Kan ni skapa en mobilvänlig webbplats?\"}, {\"answer\": \"Vi använder främst Laravel med Filament PHP men kan även lösa backend med Node.js och Express för att bygga robusta och skalbara backend-system. Vad vi väljer beror lite på projektspecifikationen.\", \"question\": \"Vilka ramverk använder ni för backend-utveckling?\"}, {\"answer\": \"Ja, vi specialiserar oss på att skapa skräddarsydda API:er för sömlös integration mellan system.\", \"question\": \"Kan ni utveckla API:er för min applikation?\"}, {\"answer\": \"Vi behov använder vi AI för att automatisera uppgifter, effektivisera utvecklingen, optimera kod och skapa intelligenta system som lär sig över tid.\", \"question\": \"Hur använder ni AI i utvecklingsprocessen?\"}, {\"answer\": \"Ja, vi kan utveckla AI-drivna funktioner som personaliserade rekommendationer eller kundanalys m.m.\", \"question\": \"Kan ni bygga AI-drivna funktioner i min applikation?\"}, {\"answer\": \"Ja, vi skapar skräddarsydda API:er och integrationslösningar för att koppla samman olika system.\", \"question\": \"Kan ni integrera min applikation med tredjepartstjänster?\"}, {\"answer\": \"Vi använder moderna standarder och dokumenterar våra lösningar noggrant för att säkerställa framtida anpassningar.\", \"question\": \"Hur säkerställer ni att integrationen är framtidssäker?\"}, {\"answer\": \"Ja, vi erbjuder kontinuerlig support för buggfixar och proaktiv övervakning. För uppdateringar erbjuder vi underhållspaket för att alltid hålla lösningarna up to date med aktuella säkerhetsfixar och optimeringar.\", \"question\": \"Erbjuder ni support efter lansering?\"}, {\"answer\": \"Absolut, vi erbjuder underhållspaket för att säkerställa att din webbplats alltid är uppdaterad och säker.\", \"question\": \"Kan ni hjälpa mig med regelbundna uppdateringar av min webbplats?\"}], \"heading\": \"Vanliga frågor & Svar\", \"subheading\": null, \"section_class\": \"faq\"}, \"type\": \"faq_accordion\"}, {\"data\": {\"heading\": \"Boka en konsultation idag\", \"subheading\": null, \"error_message\": \"Tyvärr uppstod ett fel när meddelandet skulle skickas. Vänligen försök igen senare.\", \"section_class\": \"home-contact\", \"success_message\": \"Tack för ditt meddelande. Vi kommer att kontakta dig snart!\", \"notification_email\": \"info@kalibercreative.se\", \"submit_button_text\": \"Skicka meddelande\", \"section_description\": \"Är du redo att ta din webbplats till nästa nivå med AI och LLM:er? Kontakta oss redan idag för en kostnadsfri konsultation. Tillsammans utforskar vi dina utvecklingsbehov, identifierar möjligheter och skapar en skräddarsydd lösning som passar just dig.\\n\\nFramtiden för webbutveckling är här – låt oss hjälpa dig att forma den.\"}, \"type\": \"contact_form\"}]', 1, '2025-01-09 10:48:50', '2025-02-04 23:15:35', NULL, 0, 'Utveckling med hjälp av erfarenhet & AI', 'Innovativ webb- & systemutveckling med skräddarsydda lösningar. WordPress, e-handel, AI-kodning, SaaS & API-integration. Framtidssäkra ditt företag med vår expe', NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, 1, 0),
(14, 'UI/UX & Design', 'uiux-design', '[{\"data\": {\"text\": \"# UI/UX & Design – Varje klick har en mening, varje pixel ett syfte\\n\\nPå denna sida hittar du våra heltäckande tjänster inom UI/UX och design, där vi kombinerar kreativ innovation med användarcentrerad strategi för att skapa gränssnitt som inte bara ser fantastiska ut – de fungerar briljant. Oavsett om du vill förbättra konverteringar, stärka varumärkesidentiteten eller göra din digitala produkt tillgänglig för alla, har vi verktygen, kunskapen och passionen för att göra det möjligt.\", \"section_class\": \"service-hero\"}, \"type\": \"full_text\"}, {\"data\": {\"cards\": [{\"icon\": \"tree-structure\", \"title\": \"Användarupplevelse (UX) Design – Förståelsen bakom varje interaktion\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår UX-design fokuserar på att kartlägga och forma upplevelser som gör dina användare lojala och engagerade. Genom att utföra detaljerade användarundersökningar, skapa personas som speglar dina målgruppers behov och designa intuitiva användarresor säkerställer vi att varje klick leder till mervärde. Vi bygger wireframes och prototyper som testas i tidiga skeden – inte för att gissa, utan för att veta vad som fungerar.\\n\\nOavsett om du behöver en ny plattform eller vill förbättra en befintlig produkt – skapar vi lösningar där användarens röst alltid är i centrum. Resultatet? En produkt som inte bara möter förväntningar, utan överträffar dem.\"}, {\"icon\": \"paint-brush-broad\", \"title\": \"Användargränssnitt (UI) Design – Estetik som fungerar\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår UI-design fokuserar på att förvandla komplexa funktioner till enkla, visuellt slagkraftiga gränssnitt. Vi kombinerar visuell design med tekniskt genomtänkta designsystem som säkerställer konsekvens – från färgpaletter och typografi till animationer som gör interaktionen smidig och engagerande. Varje element är anpassat för responsivitet, oavsett om användaren är på mobil, surfplatta eller desktop.\\n\\nOavsett om du vill förnya ett befintligt gränssnitt eller bygga något helt nytt – skapar vi gränssnitt som inte bara imponerar vid första ögonkastet, utan även bibehåller sin funktionalitet över tid.\"}, {\"icon\": \"highlighter-circle\", \"title\": \"Branding & Grafisk Design – Varumärket som berättar en historia\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom branding och grafisk design fokuserar på att göra ditt varumärke oglömligt. Från logotypdesign som fångar essensen av din verksamhet till heltäckande varumärkesidentiteter med färger, typsnitt och grafiska element – skapar vi en visuell identitet som genomsyrar allt från webbplatser till marknadsföringsmaterial.\\n\\nOavsett om du är redo för en rebranding eller vill utveckla material som sticker ut i en överfull digital värld – säkerställer vi att ditt budskap når fram med klarhet och emotionell närvaro.\"}, {\"icon\": \"devices\", \"title\": \"Design för Digitala Produkter – Från idé till konvertering\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår design för digitala produkter fokuserar på att kombinera innovation med resultat. Vi bygger webbplatser som väcker nyfikenhet, appar som gör vardagen enklare och e-handelsplattformar som ökar försäljning genom smart konverteringsoptimering. Varje layout, knapp och bildplacering är genomtänkt för att balansera estetik och funktion.\\n\\nOavsett om du riktar in dig på B2B, B2C eller en nischad målgrupp – skapar vi produkter som inte bara ser bra ut, utan också levererar mätbara affärsresultat.\"}, {\"icon\": \"strategy\", \"title\": \" Strategi & Konsultation – Design som driver framgång\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår strategiska konsultation fokuserar på att göra design till en del av din affärsstrategi. Genom workshops och utbildningar hjälper vi dig att definiera en designvision som stödjer dina långsiktiga mål – inte bara som ett verktyg, utan som en konkurrensfördel. Vi bryter ner komplexa idéer till tydliga steg och lär upp ditt team i användarcentrerat tänk.\\n\\nOavsett om du behöver en ny riktning eller vill skala upp din befintliga designkapacitet – ger vi dig ramverket för att leda, inte bara följa.\"}, {\"icon\": \"hand-heart\", \"title\": \"Tillgänglighet (Accessibility) – Design för alla\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår tjänst inom tillgänglighet fokuserar på att göra digitala upplevelser inkluderande och rättvisa. Genom att följa WCAG-riktlinjer och testa med användare som har funktionsnedsättningar säkerställer vi att dina plattformar inte exkluderar – de välkomnar.\\n\\nOavsett om du behöver en grundläggande anpassning eller en heltäckande tillgänglighetsgranskning – gör vi din design inte bara laglig, utan mänsklig.\"}, {\"icon\": \"speedometer\", \"title\": \"Designoptimering & Uppföljning – Kontinuerlig framgång\", \"icon_color\": \"#111820\", \"icon_style\": \"regular\", \"description\": \"Vår designoptimering fokuserar på att göra det bra till det bästa. Genom A/B-testning, analys av användarbeteende och regelbundna uppdateringar säkerställer vi att din design alltid utvecklas i takt med dina användares behov.\\n\\nOavsett om du vill öka konverteringar, minska laddningstider eller bara hålla din plattform fräsch – tar vi data och gör den till din mest värdefulla designrådgivare.\"}], \"section_class\": \"service-section\", \"section_title\": \"Vårt utbud av tjänster inom UI/UX & Design\"}, \"type\": \"service_cards\"}, {\"data\": {\"items\": [{\"answer\": \"UI (User Interface) handlar om det visuella och interaktiva designen av ett gränssnitt, medan UX (User Experience) fokuserar på användarens övergripande upplevelse och hur smidigt och meningsfullt gränssnittet är att använda.\", \"question\": \"Vad är skillnaden mellan UI och UX?\"}, {\"answer\": \"En bra UI/UX-design ökar användarnöjdheten, förbättrar konverteringsgraden och stärker ditt varumärke genom att erbjuda en smidig och engagerande användarupplevelse.\", \"question\": \"Varför är UI/UX-design viktigt för min verksamhet?\"}, {\"answer\": \"Tiden varierar beroende på projektets omfattning och komplexitet. Efter en första konsultation ger vi en tidsuppskattning och en tydlig projektplan.\", \"question\": \"Hur lång tid tar det att designa en webbplats eller app?\"}, {\"answer\": \"Ja, vi kan anpassa och förbättra befintliga designsystem eller skapa helt nya lösningar som integreras sömlöst med dina nuvarande plattformar.\", \"question\": \"Kan ni arbeta med befintliga designsystem eller plattformar?\"}, {\"answer\": \"Kostnaden beror på projektets omfattning och komplexitet. Vi erbjuder skräddarsydda offerter efter en första diskussion om dina behov.\", \"question\": \"Vad kostar det att anlita er för UI/UX-design?\"}, {\"answer\": \"Vi börjar med användarundersökningar, skapar personas och kartlägger användarresor för att förstå dina användares behov och mål.\", \"question\": \"Hur börjar ni processen med UX-design?\"}, {\"answer\": \"Ja, vi skapar wireframes och prototyper som testas med användare i en staging miljö för att säkerställa att designen fungerar som den ska innan det driftsätts skarpt.\", \"question\": \"Kan ni testa designen innan den implementeras?\"}, {\"answer\": \"Vi följer användarcentrerade designprinciper och testar våra lösningar med riktiga användare för att säkerställa att de är intuitiva och effektiva.\", \"question\": \"Hur säkerställer ni att designen är användarvänlig?\"}, {\"answer\": \"UI-design inkluderar allt från färgval, typografi och ikoner till layout, animationer och interaktiva element.\", \"question\": \"Vad ingår i en UI-design?\"}, {\"answer\": \"Ja, vi designar alltid gränssnitt som är optimerade för att fungera sömlöst på mobil, surfplatta och desktop.\", \"question\": \"Kan ni skapa responsiv design som fungerar på alla enheter?\"}, {\"answer\": \"Ja, vi skapar heltäckande varumärkesidentiteter inklusive logotyper, färgpaletter, typsnitt och grafiska element.\", \"question\": \"Kan ni hjälpa oss att skapa en ny varumärkesidentitet?\"}, {\"answer\": \"Ja, vi skapar allt från visitkort och broschyrer till digitala annonser och sociala medieinlägg som är konsekventa med ditt varumärke.\", \"question\": \"Kan ni designa marknadsföringsmaterial som matchar vår varumärkesidentitet?\"}, {\"answer\": \"Vi optimerar designen för att skapa en smidig användarresa, från första intryck till konvertering, genom att använda bästa praxis för konverteringsoptimering.\", \"question\": \"Hur kan ni hjälpa oss att öka konverteringen på vår webbplats?\"}, {\"answer\": \"Vi designar e-handelsplattformar som är optimerade för att öka försäljning genom smart produktvisning, enkel kassahantering och engagerande användarupplevelser.\", \"question\": \"Hur hanterar ni design för e-handelsplattformar?\"}, {\"answer\": \"Tillgänglighetsdesign handlar om att skapa digitala upplevelser som är inkluderande och användbara för alla, inklusive personer med funktionsnedsättningar.\", \"question\": \"Vad innebär tillgänglighetsdesign?\"}, {\"answer\": \"Ja, vi följer WCAG-riktlinjerna (Web Content Accessibility Guidelines) för att säkerställa att dina plattformar är tillgängliga och lagliga.\", \"question\": \"Följer ni WCAG-riktlinjerna?\"}, {\"answer\": \"Ja, vi erbjuder tillgänglighetsgranskningar och kan hjälpa dig att implementera nödvändiga förbättringar.\", \"question\": \"Kan ni testa vår webbplats för tillgänglighet?\"}, {\"answer\": \"Vi använder A/B-testning, analys av användarbeteende och regelbundna uppdateringar för att kontinuerligt förbättra din design.\", \"question\": \"Hur kan ni hjälpa oss att optimera vår befintliga design?\"}, {\"answer\": \"Ja, vi optimerar design, bilder och kod för att säkerställa snabba laddningstider utan att kompromissa med kvaliteten.\", \"question\": \"Kan ni hjälpa oss att minska laddningstider på vår webbplats?\"}, {\"answer\": \"Ja, vi erbjuder kontinuerlig support och uppföljning för att säkerställa att din design förblir effektiv och relevant över tid.\", \"question\": \"Erbjuder ni kontinuerlig support efter designlansering?\"}], \"heading\": \"Vanliga frågor & svar\", \"subheading\": null, \"section_class\": \"faq\"}, \"type\": \"faq_accordion\"}, {\"data\": {\"heading\": \"Boka en kostnadsfri designkonsultation\", \"subheading\": null, \"error_message\": \"Tyvärr uppstod ett fel när meddelandet skulle skickas. Vänligen försök igen senare.\", \"section_class\": \"home-contact\", \"success_message\": \"Tack för ditt meddelande. Vi kommer att kontakta dig snart!\", \"notification_email\": \"info@kalibercreative.se\", \"submit_button_text\": \"Skicka meddelande\", \"section_description\": \"Är du redo att förvandla din digitala närvaro till en engagerande och användarcentrerad upplevelse? Kontakta oss redan idag för att diskutera dina utmaningar och mål. Tillsammans utforskar vi dina behov – från UX-research och visuell identitet till tillgänglighet och konverteringsoptimering – och skapar en skräddarsydd designstrategi som förhöjer både användarnöjdhet och affärsresultat.\\n\\nFramtiden byggs genom meningsfulla interaktioner. Låt oss forma den tillsammans, en pixel i taget.\"}, \"type\": \"contact_form\"}]', 1, '2025-01-21 10:07:49', '2025-02-04 23:21:58', NULL, 0, 'UI/UX & Design | Skräddarsydda Lösningar & Konvertering', 'Professionell UI/UX-design & grafiska lösningar för ökad konvertering och engagemang. Inkluderar varumärkesdesign, tillgänglighet och datadriven optimering.', NULL, NULL, NULL, NULL, NULL, 15, NULL, NULL, NULL, NULL, NULL, 1, 0),
(15, 'Integritetspolicy', 'integritetspolicy', '[{\"data\": {\"text\": \"# Integritetspolicy  \\n**Senast uppdaterad:** 25-02-02  \\n\\nDenna integritetspolicy beskriver hur **Kalibr.** (\\\"vi\\\", \\\"oss\\\", \\\"vår\\\") samlar in, använder och skyddar dina personuppgifter i enlighet med GDPR (General Data Protection Regulation) och tillämplig svensk lagstiftning.  \\n\\n## 1. Datansvarig  \\n\\n**Kalibr.**<br>\\nSimrishamnsgatan 20a<br>\\nSE-214 23 Malmö<br>\\nSweden<br>\\ninfo@kalibr.se<br>\\n0736 15 12 20\\n\\n## 2. Vilka personuppgifter samlar vi in?  \\nVi kan samla in följande uppgifter:  \\n- **Identifikationsdata:** Namn, e-postadress, telefonnummer.  \\n- **Teknisk data:** IP-adress, enhetsinformation, webbläsartyp.  \\n- **Kommunikationsdata:** Meddelanden via kontaktformulär, e-post eller chatt.  \\n- **Projektspecifik data:** Information relaterad till webbutvecklings- eller AI-projekt (t.ex. kravspecifikationer eller testdata).  \\n\\n\\n## 3. Syfte och rättslig grund för behandling  \\n- **Utföra avtal:** Leverera tjänster enligt överenskommelse (rättslig grund: avtal).  \\n- **Kommunikation:** Svara på förfrågningar (rättslig grund: berättigt intresse).  \\n- **Förbättra tjänster:** Analysera användning för optimering (rättslig grund: samtycke eller berättigt intresse).  \\n- **Lagliga skyldigheter:** Uppfylla juridiska krav (t.ex. bokföring enligt svensk lag).  \\n\\n## 4. Hur länge sparar vi dina uppgifter?  \\nPersonuppgifter sparas endast så länge som nödvändigt för syftet eller enligt lagkrav. Exempel:  \\n- Kunddata: 3 år efter avslutat projekt.  \\n- Bokföring: 7 år (enligt svensk lag).  \\n\\n## 5. Delning av personuppgifter  \\nVi delar inte din information med tredje part utan ditt uttryckliga samtycke, förutom i följande fall:  \\n- **Underleverantörer:** T.ex. molnleverantörer (AWS, Google Cloud) eller betalningshanterare (Stripe, PayPal) som är nödvändiga för tjänsteutförande.  \\n- **Lagliga krav:** Vid begäran från myndighet eller domstol.  \\n\\n## 6. Dina rättigheter enligt GDPR  \\nDu har rätt att:  \\n- Begära **åtkomst** till dina uppgifter.  \\n- Kräva **rättelse** av felaktiga data.  \\n- Begära **radering** (om inte lag kräver sparande).  \\n- Begränsa behandling under tvist.  \\n- Få dina data i **strukturerat format** (dataportabilitet).  \\n- **Invända** mot behandling baserad på berättigt intresse.  \\n\\nSkicka förfrågningar till info@kalibr.se. Du kan även klaga till **Integritetsskyddsmyndigheten (IMY)**.  \\n\\n## 7. Säkerhet  \\nVi använder:  \\n- **Tekniska åtgärder:** Kryptering (SSL/TLS), brandväggar, regelbunden säkerhetsuppdatering.  \\n- **Organisatoriska åtgärder:** Åtkomstbegränsning och datapolicy för anställda.  \\n\\n## 8. Cookies och spårning  \\nVi använder:  \\n- **Nödvändiga cookies:** För webbplatsens funktionalitet (t.ex. inloggning).  \\n- **Analysverktyg:** Google Analytics (sparar IP-adress anonymiserat).    \\n\\n## 9. Ändringar av policyn  \\nAktuell version finns alltid på denna sida. Vid större ändringar meddelar vi dig via e-post eller via en banner på webbplatsen.  \\n\\n## 10. Kontakt  \\nFör frågor om integritet eller dataskydd:<br> \\nKalibr.<br>\\ninfo@kalibr.se<br>\", \"section_class\": null}, \"type\": \"full_text\"}]', 1, '2025-02-02 20:22:07', '2025-02-07 13:51:21', NULL, 0, 'Kalibr - Integritetspolicy', 'Vår integritetspolicy', NULL, NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, 0, 1),
(16, 'Användarvillkor', 'anvandarvillkor', '[{\"data\": {\"text\": \"# Användarvillkor  \\n**Senast uppdaterad:** 25-02-02\\n\\nGenom att använda tjänsterna från **Kalibr.** (\\\"vi\\\", \\\"oss\\\") godkänner du dessa villkor.  \\n\\n## 1. Tjänstbeskrivning  \\nVi erbjuder:  \\n- Webbutveckling (t.ex. CMS, e-handel).  \\n- AI-lösningar (maskininlärning, NLP, chatbots).\\n- UI/UX & Design \\n- Relaterad konsultation och support.  \\n\\nDetaljerad scope fastställs i separat avtal.  \\n\\n## 2. Användarkrav  \\n- **Ålder:** Minst 16 år, eller med vårdnadshavares samtycke.  \\n- **Information:** Du måste ange korrekt och fullständig data (t.ex. faktureringsuppgifter).  \\n- **Användning:** Följ alla tillämpliga lagar (t.ex. upphovsrätt och integritet).  \\n\\n## 3. Immateriella rättigheter  \\n- **Vårt material:** All kod, design och dokumentation skapad av oss tillhör oss eller licensgivare. Du får en icke-exklusiv licens för slutprodukten.  \\n- **Kundmaterial:** Data du tillhandahåller (t.ex. bilder eller text) måste vara ditt eget eller ha korrekt licens.  \\n\\n## 4. Betalning och fakturering  \\n- Priser anges i separat offert.  \\n- Försenad betalning kan leda till ränta eller avbrott i tjänsten.  \\n\\n## 5. Ansvar  \\n- Vi ansvarar inte för:  \\n  - Indirekta skador (förlorad vinst eller data).  \\n  - Problem orsakade av tredjepartstjänster (t.ex. externa API:er).  \\n- Tjänster levereras \\\"som de är\\\" utan garanti för felfrihet.  \\n\\n## 6. Uppsägning  \\n- **Av dig:** Avsluta konto via info@kalibr.se.  \\n- **Av oss:** Vid brott mot villkor, obetalda fakturor eller misstänkt bedrägeri.  \\n\\n## 7. Ändringar av villkoren  \\nUppdaterade villkor publiceras här. Fortsatt användning efter publicering innebär godkännande.  \\n\\n## 8. Tillämplig lag och tvistlösning  \\n- **Lag:** Svensk lag tillämpas.  \\n- **Tvist:** Först förhandling, sedan svensk domstol (för konsumenter: [Allmänna reklamationsnämnden](https://www.arn.se/)).  \\n\\n## 9. Kontakt  \\nFör frågor om villkoren:  \\nKalibr.<br>\\ninfo@kalibr.se\", \"section_class\": null}, \"type\": \"full_text\"}]', 1, '2025-02-02 20:29:10', '2025-02-07 14:21:33', NULL, 0, 'Användarvillkor – Kalibr | Webbutveckling & AI-lösningar', 'Läs våra användarvillkor för tjänster som webbutveckling, AI-lösningar & design. Information om betalning, ansvar och immateriella rättigheter.', NULL, NULL, NULL, NULL, NULL, 20, NULL, NULL, NULL, NULL, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Tabellstruktur `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `publish_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hero_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_headline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_subheadline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_paragraph` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hero_cta_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hero_background_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_background_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_text_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_height` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excerpt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `is_published`, `publish_date`, `created_at`, `updated_at`, `hero_image`, `list_image`, `hero_headline`, `hero_subheadline`, `hero_paragraph`, `hero_cta_text`, `hero_cta_url`, `meta_title`, `meta_description`, `hero_background_type`, `hero_background_color`, `hero_text_color`, `hero_height`, `excerpt`, `featured_image`, `author_id`, `category_id`) VALUES
(1, 'Varför Företag Bör Satsa på Chatbots – Och Hur Det Kan Förbättra Din Verksamhet', 'varfor-foretag-bor-satsa-pa-chatbots-och-hur-det-kan-forbattra-din-verksamhet', '\"I en v\\u00e4rld d\\u00e4r kundernas f\\u00f6rv\\u00e4ntningar p\\u00e5 snabb och effektiv service st\\u00e4ndigt \\u00f6kar, har chatbots blivit en av de mest kraftfulla verktygen f\\u00f6r f\\u00f6retag att m\\u00f6ta dessa krav. Oavsett om du driver en e-handelsbutik, en bank eller en tj\\u00e4nstebaserad verksamhet, kan en v\\u00e4lutvecklad chatbot revolutionera hur du interagerar med dina kunder och f\\u00f6rb\\u00e4ttra din verksamhet p\\u00e5 flera niv\\u00e5er. Men varf\\u00f6r ska f\\u00f6retag egentligen satsa p\\u00e5 en chatbot? Och hur kan det gynna dem? L\\u00e5t oss dyka in i \\u00e4mnet.\\n\\n![](http:\\/\\/localhost:8000\\/storage\\/blog-images\\/PsScriYWbe61wAHYY1RJUIlkgbOu1vx5dkW83p75.jpg)\\n\\n## Vad \\u00e4r en chatbot och varf\\u00f6r \\u00e4r den viktig?\\n\\nEn chatbot \\u00e4r en AI-driven programvara som kan simulera m\\u00e4nskliga samtal via text eller r\\u00f6st. Den kan anv\\u00e4ndas p\\u00e5 webbplatser, i appar, p\\u00e5 sociala medier eller till och med i interna system. Chatbots \\u00e4r designade f\\u00f6r att svara p\\u00e5 fr\\u00e5gor, l\\u00f6sa problem och guida anv\\u00e4ndare \\u2013 allt i realtid.\\n\\nDet som g\\u00f6r chatbots s\\u00e5 viktiga \\u00e4r deras f\\u00f6rm\\u00e5ga att automatisera och effektivisera kundinteraktioner. I en tid d\\u00e4r kunder f\\u00f6rv\\u00e4ntar sig omedelbara svar och personlig service, kan en chatbot vara skillnaden mellan en n\\u00f6jd kund och en f\\u00f6rlorad aff\\u00e4r.\\n\\n### 5 Anledningar till varf\\u00f6r f\\u00f6retag b\\u00f6r satsa p\\u00e5 en chatbot\\n\\n1. **24\\/7 Tillg\\u00e4nglighet**\\nKunder f\\u00f6rv\\u00e4ntar sig att kunna f\\u00e5 hj\\u00e4lp n\\u00e4r som helst, oavsett tid p\\u00e5 dygnet. En chatbot kan vara tillg\\u00e4nglig dygnet runt och svara p\\u00e5 fr\\u00e5gor, l\\u00f6sa problem eller guida kunder genom k\\u00f6pprocessen. Detta inneb\\u00e4r att du aldrig missar en potentiell kund, \\u00e4ven n\\u00e4r ditt team sover.\\n\\nExempel: En e-handelsbutik kan anv\\u00e4nda en chatbot f\\u00f6r att svara p\\u00e5 fr\\u00e5gor om lagerstatus, leveranstider eller returer \\u2013 \\u00e4ven mitt i natten.\\n\\n2. **Kostnadseffektivitet**\\nAtt anst\\u00e4lla och utbilda kundtj\\u00e4nstpersonal kan vara dyrt, s\\u00e4rskilt om du beh\\u00f6ver t\\u00e4cka flera tidszoner. En chatbot kan hantera hundratals, om inte tusentals, konversationer samtidigt utan att det kostar mer \\u00e4n den initiala utvecklingskostnaden. Detta g\\u00f6r det till en kostnadseffektiv l\\u00f6sning f\\u00f6r f\\u00f6retag i alla storlekar.\\n\\nExempel: Ett mindre f\\u00f6retag kan anv\\u00e4nda en chatbot f\\u00f6r att hantera vanliga fr\\u00e5gor, vilket frig\\u00f6r tid f\\u00f6r personalen att fokusera p\\u00e5 mer komplexa uppgifter.\\n\\n3. **F\\u00f6rb\\u00e4ttrad kundupplevelse**\\nEn chatbot kan ge snabba och korrekta svar, vilket f\\u00f6rb\\u00e4ttrar kundupplevelsen. Dessutom kan den personalisera interaktioner baserat p\\u00e5 kundens tidigare beteende eller preferenser. Detta skapar en k\\u00e4nsla av att varje kund \\u00e4r unik och v\\u00e4rdefull.\\n\\nExempel: En resebyr\\u00e5s chatbot kan f\\u00f6resl\\u00e5 resm\\u00e5l baserat p\\u00e5 kundens tidigare bokningar eller intressen.\\n\\n4. **\\u00d6kad f\\u00f6rs\\u00e4ljning och konvertering**\\nChatbots kan guida kunder genom k\\u00f6pprocessen, ge produktrekommendationer och svara p\\u00e5 fr\\u00e5gor som annars skulle kunna hindra ett k\\u00f6p. De kan ocks\\u00e5 anv\\u00e4ndas f\\u00f6r att skicka p\\u00e5minnelser om \\u00f6vergivna varukorgar eller erbjuda specialerbjudanden.\\n\\nExempel: En chatbot p\\u00e5 en e-handelswebbplats kan f\\u00f6resl\\u00e5 kompletterande produkter n\\u00e4r en kund l\\u00e4gger n\\u00e5got i sin varukorg, vilket \\u00f6kar genomsnittsv\\u00e4rdet per k\\u00f6p.\\n\\n5. **Insikter och dataanalys**\\nChatbots samlar in v\\u00e4rdefull data om kundernas beteende, vanliga fr\\u00e5gor och problemomr\\u00e5den. Denna information kan anv\\u00e4ndas f\\u00f6r att f\\u00f6rb\\u00e4ttra produkter, tj\\u00e4nster och kundupplevelser.\\n\\nExempel: Om en chatbot m\\u00e4rker att m\\u00e5nga kunder st\\u00e4ller samma fr\\u00e5ga om en viss produkt, kan f\\u00f6retaget anv\\u00e4nda denna insikt f\\u00f6r att f\\u00f6rb\\u00e4ttra produktbeskrivningen eller utbilda personalen.\\n\\n### Hur kan en chatbot f\\u00f6rb\\u00e4ttra din verksamhet?\\n\\nAtt implementera en chatbot \\u00e4r inte bara en teknisk uppgradering \\u2013 det \\u00e4r en strategisk investering. H\\u00e4r \\u00e4r n\\u00e5gra konkreta s\\u00e4tt p\\u00e5 hur en chatbot kan f\\u00f6rb\\u00e4ttra din verksamhet:\\n\\n- Effektivare kundtj\\u00e4nst: Genom att automatisera vanliga fr\\u00e5gor kan din kundtj\\u00e4nstpersonal fokusera p\\u00e5 mer komplexa \\u00e4renden.\\n- Snabbare svarstider: Kunder slipper v\\u00e4nta i telefonk\\u00f6er eller p\\u00e5 e-postsvar.\\n- \\u00d6kad kundlojalitet: En snabb och smidig upplevelse g\\u00f6r att kunderna \\u00e5terv\\u00e4nder.\\n- B\\u00e4ttre skalbarhet: En chatbot kan hantera en v\\u00e4xande kundbas utan att du beh\\u00f6ver anst\\u00e4lla fler medarbetare.\\n- Minskad m\\u00e4nsklig felmarginal: Chatbots ger konsekventa och korrekta svar, vilket minskar risken f\\u00f6r missf\\u00f6rst\\u00e5nd.\\n\\n### Vanliga anv\\u00e4ndningsomr\\u00e5den f\\u00f6r chatbots\\n\\n- Kundtj\\u00e4nst: Svara p\\u00e5 vanliga fr\\u00e5gor, hantera klagom\\u00e5l och guida kunder.\\n- E-handel: Hj\\u00e4lpa kunder att hitta produkter, ge rekommendationer och slutf\\u00f6ra k\\u00f6p.\\n- Marknadsf\\u00f6ring: Skicka personliga erbjudanden och kampanjer.\\n- Intern kommunikation: Hj\\u00e4lpa anst\\u00e4llda med HR-fr\\u00e5gor, schemal\\u00e4ggning eller IT-support.\\n- Bokningar: Automatisera bokningar f\\u00f6r restauranger, hotell eller tj\\u00e4nster.\\n\\n### Hur kommer man ig\\u00e5ng med en chatbot?\\n\\nAtt implementera en chatbot beh\\u00f6ver inte vara komplicerat. H\\u00e4r \\u00e4r n\\u00e5gra steg f\\u00f6r att komma ig\\u00e5ng:\\n\\n- **Definiera era m\\u00e5l:** Vad vill du att chatboten ska g\\u00f6ra? \\u00c4r det att f\\u00f6rb\\u00e4ttra kundtj\\u00e4nsten, \\u00f6ka f\\u00f6rs\\u00e4ljningen eller n\\u00e5got annat?\\n- **V\\u00e4lj r\\u00e4tt plattform:** Det finns m\\u00e5nga verktyg och plattformar f\\u00f6r att bygga chatbots, fr\\u00e5n enkla l\\u00f6sningar till avancerade AI-drivna system.\\n- **Tr\\u00e4na chatboten:** Se till att den har r\\u00e4tt information och kan hantera de vanligaste fr\\u00e5gorna och scenarierna.\\n- **Testa och optimera:** Testa chatboten med riktiga anv\\u00e4ndare och justera den baserat p\\u00e5 feedback.\\n- **Integrera med dina system:** Se till att chatboten fungerar s\\u00f6ml\\u00f6st med din webbplats, app eller andra system.\\n\\nEn chatbot \\u00e4r inte bara en trend \\u2013 det \\u00e4r en n\\u00f6dv\\u00e4ndighet f\\u00f6r f\\u00f6retag som vill ligga i framkant och m\\u00f6ta kundernas f\\u00f6rv\\u00e4ntningar. Genom att satsa p\\u00e5 en chatbot kan du f\\u00f6rb\\u00e4ttra kundupplevelsen, \\u00f6ka effektiviteten och f\\u00e5 v\\u00e4rdefulla insikter om dina kunder. Oavsett om du \\u00e4r ett litet f\\u00f6retag eller en stor organisation, \\u00e4r det dags att omfamna framtiden och l\\u00e5ta en chatbot bli en del av din strategi.\\n\\nS\\u00e5, vad v\\u00e4ntar du p\\u00e5? B\\u00f6rja din chatbot-resa idag och se hur den kan ta din verksamhet till n\\u00e4sta niv\\u00e5!\"', 1, '2025-01-01 00:00:00', '2024-11-01 15:06:09', '2025-02-07 14:41:11', 'hero-images/01JBM4JRFGKNZ7KXX4555ST58Q.webp', 'posts/01JHNYB0Z5ED4M7WGDVFPGGB11.jpg', NULL, NULL, NULL, NULL, NULL, 'Varför Satsa på Chatbots? Förbättra Din Verksamhet Med AI', 'Upptäck varför chatbots är en nödvändighet för företag. Lär dig hur de förbättrar kundupplevelsen, ökar försäljningen och effektiviserar kundtjänsten.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Hur AI-automatisering kan transformera ditt företag – och varför du borde prata med oss idag', 'hur-ai-automatisering-kan-transformera-ditt-foretag-och-varfor-du-borde-prata-med-oss-idag', '\"AI och automatisering \\u00e4r inte l\\u00e4ngre framtidsdr\\u00f6mmar \\u2013 de \\u00e4r verktyg som redan nu revolutionerar hur f\\u00f6retag arbetar, v\\u00e4xer och konkurrerar. Oavsett om du \\u00e4r en liten startup eller ett stort etablerat f\\u00f6retag finns det otaliga s\\u00e4tt att anv\\u00e4nda AI f\\u00f6r att effektivisera processer, \\u00f6ka l\\u00f6nsamheten och skapa b\\u00e4ttre kundupplevelser. Men var b\\u00f6rjar man? Och hur kan du vara s\\u00e4ker p\\u00e5 att du tar de r\\u00e4tta stegen?\\n\\nSom en ledande webbutvecklings- och AI-byr\\u00e5 \\u00e4r vi h\\u00e4r f\\u00f6r att visa dig hur AI-automatisering kan gynna just ditt f\\u00f6retag \\u2013 och varf\\u00f6r du borde boka en konsultation med oss redan idag.\\n\\n1. **Effektivisera kundsupport med AI-drivna chatbotar**\\nT\\u00e4nk dig en v\\u00e4rld d\\u00e4r dina kunder f\\u00e5r snabba, korrekta svar p\\u00e5 sina fr\\u00e5gor \\u2013 dygnet runt, utan att ditt team beh\\u00f6ver lyfta ett finger. Genom att implementera AI-drivna chatbotar kan du automatisera stora delar av din kundsupport. Chatbotar kan hantera vanliga fr\\u00e5gor, l\\u00f6sa enkla problem och till och med guida kunder genom k\\u00f6pprocessen. Resultatet? N\\u00f6jda kunder och ett avlastat supportteam som kan fokusera p\\u00e5 mer komplexa uppgifter.\\n\\n2. **Personalisera kundupplevelser med AI**\\nAI g\\u00f6r det m\\u00f6jligt att analysera stora m\\u00e4ngder data i realtid, vilket inneb\\u00e4r att du kan skr\\u00e4ddarsy kundupplevelser p\\u00e5 ett helt nytt s\\u00e4tt. Genom att anv\\u00e4nda maskininl\\u00e4rning kan vi skapa system som f\\u00f6rst\\u00e5r dina kunders beteenden, preferenser och k\\u00f6pm\\u00f6nster. Detta l\\u00e5ter dig erbjuda personliga rekommendationer, skr\\u00e4ddarsydda erbjudanden och en upplevelse som f\\u00e5r kunderna att k\\u00e4nna sig sedda och v\\u00e4rderade.\\n\\n3. **Automatisera administrativa uppgifter**\\nHur mycket tid och energi l\\u00e4gger ditt team p\\u00e5 repetitiva, administrativa uppgifter? Med AI-automatisering kan du frig\\u00f6ra v\\u00e4rdefull tid genom att automatisera processer som fakturering, rapportering och evenemangsplanering. Detta inneb\\u00e4r inte bara f\\u00e4rre misstag och snabbare leveranser \\u2013 det ger ocks\\u00e5 ditt team m\\u00f6jlighet att fokusera p\\u00e5 mer strategiska och kreativa uppgifter.\\n\\n4. **F\\u00f6rb\\u00e4ttra beslutsfattandet med datadriven insikt**\\nAI kan analysera komplexa datam\\u00e4ngder och ge dig insikter som skulle vara om\\u00f6jliga att uppt\\u00e4cka manuellt. Oavsett om det handlar om marknadstrender, kundbeteenden eller interna processer kan AI hj\\u00e4lpa dig att fatta b\\u00e4ttre, mer informerade beslut. Detta \\u00e4r s\\u00e4rskilt v\\u00e4rdefullt f\\u00f6r f\\u00f6retag som vill h\\u00e5lla sig f\\u00f6re i konkurrensen och identifiera nya m\\u00f6jligheter.\\n\\n5. **Skapa smartare och mer effektiva webbplatser**\\nSom en webbutvecklingsbyr\\u00e5 med expertis inom AI kan vi hj\\u00e4lpa dig att bygga webbplatser som inte bara ser bra ut, utan som ocks\\u00e5 fungerar smartare. T\\u00e4nk AI-drivna s\\u00f6kfunktioner som f\\u00f6rst\\u00e5r vad anv\\u00e4ndaren letar efter, dynamiskt inneh\\u00e5ll som anpassar sig efter bes\\u00f6karen och automatiserade A\\/B-tester som optimerar din webbplats i realtid.\\n\\n### Varf\\u00f6r v\\u00e4lja oss?\\n\\nVi \\u00e4r inte bara en webbutvecklingsbyr\\u00e5 \\u2013 vi \\u00e4r dina partners i digital transformation. Med v\\u00e5r kombination av tekniskt kunnande och kreativ probleml\\u00f6sning hj\\u00e4lper vi dig att identifiera de b\\u00e4sta s\\u00e4tten att anv\\u00e4nda AI och automatisering f\\u00f6r att n\\u00e5 dina m\\u00e5l. Oavsett om du beh\\u00f6ver en skr\\u00e4ddarsydd AI-l\\u00f6sning eller en helt ny webbplats \\u00e4r vi h\\u00e4r f\\u00f6r att g\\u00f6ra det m\\u00f6jligt.\\n\\n### Boka en konsultation idag\\n\\n\\u00c4r du redo att ta ditt f\\u00f6retag till n\\u00e4sta niv\\u00e5 med AI-automatisering? Kontakta oss redan idag f\\u00f6r en kostnadsfri konsultation. Tillsammans utforskar vi dina behov, identifierar m\\u00f6jligheter och skapar en skr\\u00e4ddarsydd l\\u00f6sning som passar just dig.\"', 1, '2024-11-05 00:00:00', '2025-01-16 16:27:02', '2025-02-07 14:55:59', NULL, 'posts/01JHQZ127HKK2Y7YRF5A6WQCN0.jpg', NULL, NULL, NULL, NULL, NULL, 'AI-automatisering för Företag – Effektivisera & Väx med AI', 'Upptäck hur AI-automatisering kan effektivisera ditt företag, förbättra kundupplevelser och öka lönsamheten. Boka en kostnadsfri konsultation idag!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Framtidens webbutveckling 2025: Hur AI och LLM:er revolutionerar branschen', 'framtidens-webbutveckling-2025-hur-ai-och-llmer-revolutionerar-branschen', '\"Webbutveckling har alltid varit en dynamisk och snabbr\\u00f6rlig bransch, men med framstegen inom artificiell intelligens (AI) och stora spr\\u00e5kmodeller (LLM:er ) st\\u00e5r vi inf\\u00f6r en av de mest sp\\u00e4nnande transformationerna hittills. \\u00c5r 2025 \\u00e4r inte l\\u00e4ngre en avl\\u00e4gsen framtid \\u2013 det \\u00e4r precis runt h\\u00f6rnet. Och f\\u00f6r f\\u00f6retag som vill h\\u00e5lla sig framme \\u00e4r det avg\\u00f6rande att f\\u00f6rst\\u00e5 hur dessa teknologier kan anv\\u00e4ndas f\\u00f6r att skapa smartare, snabbare och mer effektiva webbplatser.\\n\\nSom en ledande webbutvecklings- och AI-byr\\u00e5 \\u00e4r vi i framkant n\\u00e4r det kommer till att integrera dessa verktyg i v\\u00e5ra projekt. L\\u00e5t oss ta en titt p\\u00e5 hur AI och LLM:er kommer att forma webbutveckling 2025 \\u2013 och varf\\u00f6r du borde boka en konsultation med oss redan idag f\\u00f6r att s\\u00e4kerst\\u00e4lla att ditt f\\u00f6retag \\u00e4r redo f\\u00f6r framtiden.\\n\\n![](http:\\/\\/localhost:8000\\/storage\\/blog-images\\/tQEpfM2PhsDeBPMUhOwyrmcoEMAavPiekkbYjJOk.jpg)\\n\\n1. **AI-drivet kodning: Snabbare och mer effektiv utveckling**<br>\\nEn av de mest sp\\u00e4nnande framstegen inom webbutveckling \\u00e4r anv\\u00e4ndningen av stora spr\\u00e5kmodeller (LLM:er ) f\\u00f6r att generera och optimera kod. Modeller som OpenAI:s GPT-4, GitHub Copilot (drivet av OpenAI:s Codex) och Google DeepMind:s AlphaCode har redan visat att de kan skriva fungerande kod, fels\\u00f6ka och till och med f\\u00f6resl\\u00e5 optimeringar.<br><br> \\u00c5r 2025 kommer dessa verktyg att vara \\u00e4nnu mer avancerade, vilket g\\u00f6r det m\\u00f6jligt f\\u00f6r utvecklare att automatisera stora delar av sitt arbete. T\\u00e4nk dig att du kan generera hela kodblock med bara en kort beskrivning, eller att du f\\u00e5r f\\u00f6rslag p\\u00e5 hur du kan f\\u00f6rb\\u00e4ttra prestandan p\\u00e5 din webbplats i realtid. Detta inneb\\u00e4r inte bara snabbare utvecklingstider \\u2013 det \\u00f6ppnar ocks\\u00e5 m\\u00f6jligheter f\\u00f6r mer kreativa och innovativa l\\u00f6sningar.\\n\\n2. **Personalisering p\\u00e5 en helt ny niv\\u00e5**\\nAI g\\u00f6r det m\\u00f6jligt att skapa webbplatser som anpassar sig dynamiskt till varje bes\\u00f6kare. Genom att anv\\u00e4nda LLM:er som ChatGPT och Anthropic:s Claude kan du analysera anv\\u00e4ndarbeteenden i realtid och skr\\u00e4ddarsy inneh\\u00e5ll, erbjudanden och navigation baserat p\\u00e5 varje individs preferenser. <br><br>I \\u00e5r kommer denna personalisering att bli \\u00e4nnu mer sofistikerad, med AI som inte bara f\\u00f6rst\\u00e5r vad anv\\u00e4ndaren vill ha, utan ocks\\u00e5 f\\u00f6rutser deras behov innan de ens sj\\u00e4lva vet om det. Detta inneb\\u00e4r h\\u00f6gre konverteringsgrad, \\u00f6kad kundn\\u00f6jdhet och en konkurrensf\\u00f6rdel som \\u00e4r sv\\u00e5r att matcha.\\n\\n3. **Automatiserad fels\\u00f6kning och optimering**<br>\\nEn av de st\\u00f6rsta utmaningarna inom webbutveckling \\u00e4r att s\\u00e4kerst\\u00e4lla att webbplatser fungerar smidigt p\\u00e5 alla enheter och webbl\\u00e4sare. Med AI-drivna verktyg kan du automatisera processen f\\u00f6r fels\\u00f6kning och optimering. LLM:er som OpenAI:s Codex och Tabnine kan identifiera buggar, f\\u00f6resl\\u00e5 korrigeringar och till och med optimera kod f\\u00f6r b\\u00e4ttre prestanda. <br><br>2025 kommer dessa verktyg att bli mer och mer intelligenta, med m\\u00f6jlighet att f\\u00f6rutse potentiella problem innan de uppst\\u00e5r och automatiskt implementera l\\u00f6sningar. Detta inneb\\u00e4r mindre nedtid, f\\u00e4rre buggar och en s\\u00f6ml\\u00f6s anv\\u00e4ndarupplevelse.\\n\\n4. **AI-drivna designverktyg**<br>\\nWebbutveckling handlar inte bara om kod \\u2013 det handlar ocks\\u00e5 om design. AI-drivna designverktyg som Figma:s AI-funktioner och Adobe Firefly g\\u00f6r det m\\u00f6jligt att skapa visuellt tilltalande och funktionella webbplatser p\\u00e5 rekordtid. P\\u00e5 bara ett halv\\u00e5r har dessa verktyg g\\u00e5tt snabbt fram\\u00e5t, de t\\u00e4vlar mot varandra att bli b\\u00e4st, och redan idag \\u00e4r det m\\u00f6jligt att generera hela designkoncept baserat p\\u00e5 en kort beskrivning.<br><br>T\\u00e4nk dig att du kan s\\u00e4ga \\\"jag vill ha en modern, minimalistisk webbplats med fokus p\\u00e5 anv\\u00e4ndarv\\u00e4nlighet\\\" och f\\u00e5 en fullst\\u00e4ndig design p\\u00e5 n\\u00e5gra minuter. Detta inneb\\u00e4r snabbare projekt och mer tid att fokusera p\\u00e5 det som verkligen betyder n\\u00e5got \\u2013 din aff\\u00e4rsstrategi.\\n\\n5. **F\\u00f6rb\\u00e4ttrad s\\u00e4kerhet med AI**<br>\\nS\\u00e4kerhet \\u00e4r en av de st\\u00f6rsta utmaningarna inom webbutveckling, men AI kan hj\\u00e4lpa till att skydda dina webbplatser p\\u00e5 ett helt nytt s\\u00e4tt. LLM:er som Google Bard och OpenAI:s GPT-4 kan analysera kod f\\u00f6r att identifiera s\\u00e4kerhetsbrister och f\\u00f6resl\\u00e5 f\\u00f6rb\\u00e4ttringar. \\u00c5r 2025 kommer dessa verktyg att bli \\u00e4nnu mer proaktiva, med m\\u00f6jlighet att uppt\\u00e4cka och neutralisera hot i realtid. Detta inneb\\u00e4r mindre risk f\\u00f6r datal\\u00e4ckor, hackning och andra s\\u00e4kerhetshot.\\n\\n### Varf\\u00f6r v\\u00e4lja oss?\\n\\nVi \\u00e4r inte bara en webbutvecklingsbyr\\u00e5 \\u2013 vi \\u00e4r dina partners i digital innovation. Med v\\u00e5r expertis inom b\\u00e5de webbutveckling och AI kan vi hj\\u00e4lpa dig att dra nytta av de senaste teknologierna f\\u00f6r att skapa webbplatser som inte bara ser bra ut, utan som ocks\\u00e5 fungerar smartare och s\\u00e4krare.\\n\\n### Boka en konsultation idag\\n\\n\\u00c4r du redo att ta din webbplats till n\\u00e4sta niv\\u00e5 med AI och LLM:er ? Kontakta oss redan idag f\\u00f6r en kostnadsfri konsultation. Tillsammans utforskar vi dina behov, identifierar m\\u00f6jligheter och skapar en skr\\u00e4ddarsydd l\\u00f6sning som passar just dig.\"', 1, '2025-01-06 00:00:00', '2025-01-16 16:34:58', '2025-02-07 14:59:25', NULL, 'posts/01JHQZNDTFXG36X1S2E2TT5EZ3.webp', NULL, NULL, NULL, NULL, NULL, 'Framtidens webbutveckling 2025: AI och LLM:er som förändrar ', 'Upptäck hur AI och stora språkmodeller (LLM:er) revolutionerar webbutveckling 2025. Från AI-drivet kodande till personalisering och säkerhet – framtiden är här!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Agentic RAG: Framtidens AI-lösning för smartare och mer effektiva system', 'agentic-rag-framtidens-ai-losning-for-smartare-och-mer-effektiva-system', '\"AI-teknologin utvecklas i en rasande takt, och en av de mest sp\\u00e4nnande framstegen p\\u00e5 senare tid \\u00e4r Agentic RAG (Retrieval-Augmented Generation). Denna kombination av avancerad informationsh\\u00e4mtning och generativ AI \\u00f6ppnar upp helt nya m\\u00f6jligheter f\\u00f6r f\\u00f6retag att skapa intelligenta, datadrivna system. \\u00c5r 2025 kommer denna teknik att vara en game-changer f\\u00f6r branscher som vill vara i framkant.\\n\\nSom en byr\\u00e5 som specialiserar oss p\\u00e5 AI och webbutveckling \\u00e4r vi passionerade \\u00f6ver att hj\\u00e4lpa f\\u00f6retag att dra nytta av dessa banbrytande teknologier. L\\u00e5t oss utforska vad Agentic RAG \\u00e4r, hur det kan f\\u00f6r\\u00e4ndra ditt f\\u00f6retag \\u2013 och varf\\u00f6r du borde prata med oss redan idag.\\n\\n![](http:\\/\\/localhost:8000\\/storage\\/blog-images\\/CCLFk1BRaeiDhbKJ5DHAqPNVg7xfkfcmPj5NVwh9.jpg)\\n## Vad \\u00e4r Agentic RAG?\\n\\nAgentic RAG \\u00e4r en kraftfull kombination av tv\\u00e5 AI-tekniker:\\n\\n1. **Retrieval (H\\u00e4mtning)**: Systemet kan s\\u00f6ka igenom stora datam\\u00e4ngder och hitta relevant information i realtid.\\n\\n2. **Generativ AI**: Med hj\\u00e4lp av spr\\u00e5kmodeller som GPT-4 eller Claude kan systemet skapa meningsfullt och kontextanpassat inneh\\u00e5ll baserat p\\u00e5 den h\\u00e4mtade datan.\\n\\nTillsammans skapar detta en \\\"agent\\\" som inte bara kan svara p\\u00e5 fr\\u00e5gor, utan ocks\\u00e5 agera proaktivt \\u2013 genom att analysera data, dra slutsatser och ge rekommendationer.\\n\\n### Hur kan Agentic RAG gynna ditt f\\u00f6retag?\\n\\n1. **Superintelligent kundsupport**<br>\\nT\\u00e4nk dig en kundsupport som inte bara svarar p\\u00e5 fr\\u00e5gor, utan ocks\\u00e5 f\\u00f6rst\\u00e5r komplexa problem och ger detaljerade l\\u00f6sningar. Med Agentic RAG kan du skapa chatbots eller supportsystem som h\\u00e4mtar information fr\\u00e5n din databas, supportartiklar eller till och med externa k\\u00e4llor f\\u00f6r att ge korrekta och anv\\u00e4ndbara svar.\\n\\n2. **Effektivare beslutsfattande**<br>\\nF\\u00f6r f\\u00f6retag som hanterar stora m\\u00e4ngder data kan Agentic RAG agera som en virtuell analytiker. Systemet kan samla in data fr\\u00e5n olika k\\u00e4llor, analysera trender och presentera insikter p\\u00e5 ett l\\u00e4ttf\\u00f6rst\\u00e5eligt s\\u00e4tt. Detta inneb\\u00e4r snabbare och mer informerade beslut \\u2013 utan att beh\\u00f6va manuellt g\\u00e5 igenom tusentals rader data.\\n\\n3. **Dynamiskt inneh\\u00e5ll och personalisering**<br>\\nMed Agentic RAG kan du skapa webbplatser och applikationer som anpassar sig dynamiskt till varje anv\\u00e4ndare. Genom att kombinera anv\\u00e4ndardata med realtidsinformation kan systemet generera personligt anpassat inneh\\u00e5ll, erbjudanden och rekommendationer som \\u00f6kar engagemang och konvertering.\\n\\n4. **F\\u00f6rb\\u00e4ttrad informationshantering**<br>\\nF\\u00f6r f\\u00f6retag med stora kunskapsbaser eller dokumentarkiv kan Agentic RAG revolutionera hur information hanteras. Systemet kan snabbt hitta relevanta dokument, sammanfatta inneh\\u00e5ll och till och med skapa nya rapporter baserat p\\u00e5 befintlig data. Detta sparar tid och \\u00f6kar produktiviteten.\\n\\n5. **Proaktiv probleml\\u00f6sning**<br>\\nEn av de mest sp\\u00e4nnande aspekterna av Agentic RAG \\u00e4r dess f\\u00f6rm\\u00e5ga att agera proaktivt. Systemet kan identifiera potentiella problem innan de uppst\\u00e5r och f\\u00f6resl\\u00e5 l\\u00f6sningar. Till exempel kan det uppt\\u00e4cka trender i kundklagom\\u00e5l och rekommendera \\u00e5tg\\u00e4rder f\\u00f6r att f\\u00f6rb\\u00e4ttra kundn\\u00f6jdheten.\\n\\n### Varf\\u00f6r \\u00e4r Agentic RAG framtiden?\\n\\n\\u00c5r 2025 kommer AI-system att bli mer sj\\u00e4lvst\\u00e4ndiga och intelligenta \\u00e4n n\\u00e5gonsin tidigare. Agentic RAG representerar n\\u00e4sta steg i denna utveckling genom att kombinera datadriven h\\u00e4mtning med kreativ generering. Detta inneb\\u00e4r inte bara effektivare system \\u2013 det \\u00f6ppnar ocks\\u00e5 upp f\\u00f6r helt nya m\\u00f6jligheter att skapa v\\u00e4rde f\\u00f6r dina kunder och ditt f\\u00f6retag.\\n\\n### Varf\\u00f6r v\\u00e4lja oss?\\n\\nVi \\u00e4r mer \\u00e4n bara en webbutvecklingsbyr\\u00e5 \\u2013 vi \\u00e4r experter p\\u00e5 att skapa skr\\u00e4ddarsydda AI-l\\u00f6sningar som g\\u00f6r skillnad. Med v\\u00e5r djupa f\\u00f6rst\\u00e5else f\\u00f6r b\\u00e5de teknik och aff\\u00e4rsbehov kan vi hj\\u00e4lpa dig att implementera Agentic RAG p\\u00e5 ett s\\u00e4tt som passar just ditt f\\u00f6retag.\\n\\nOavsett om du vill f\\u00f6rb\\u00e4ttra din kundsupport, optimera dina beslutsprocesser eller skapa mer engagerande anv\\u00e4ndarupplevelser \\u00e4r vi h\\u00e4r f\\u00f6r att g\\u00f6ra det m\\u00f6jligt.\\n\"', 1, '2025-01-08 00:00:00', '2025-01-18 12:59:33', '2025-02-07 15:00:58', NULL, 'posts/01JHWRCBPHHD7MQK0MQRTQDSZQ.webp', NULL, NULL, NULL, NULL, NULL, 'Agentic RAG: Revolutionerande AI-lösning för smartare system', 'Agentic RAG kombinerar informationshämtning och generativ AI för smartare kundsupport, bättre beslut och dynamisk personalisering. Upptäck framtidens AI!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Är din verksamhet redo för den nya tillgänglighetslagen? – Vi hjälper dig navigera rätt!', 'ar-din-verksamhet-redo-for-den-nya-tillganglighetslagen-vi-hjalper-dig-navigera-ratt', '\"![](https:\\/\\/kalibr.se\\/storage\\/blog-images\\/DtHukgAsesvb6Hq4MbnmAkcIXPErbnFE5wy27qVg.webp)\\n\\nSnart tr\\u00e4der en viktig lag i kraft som kommer att p\\u00e5verka alla f\\u00f6retag som erbjuder digitala tj\\u00e4nster och produkter inom EU. Den 28 juni 2025 \\u00e4r datumet du beh\\u00f6ver markera i kalendern \\u2013 d\\u00e5 b\\u00f6rjar den svenska tillg\\u00e4nglighetslagen att g\\u00e4lla fullt ut. Denna lag \\u00e4r inte bara en formalitet, utan en n\\u00f6dv\\u00e4ndighet f\\u00f6r att skapa ett mer inkluderande och tillg\\u00e4ngligt samh\\u00e4lle f\\u00f6r alla, oavsett funktionsneds\\u00e4ttning.\\n\\nI det h\\u00e4r blogginl\\u00e4gget reder vi ut vad EU:s tillg\\u00e4nglighetsdirektiv och den svenska tillg\\u00e4nglighetslagen inneb\\u00e4r, vilka verksamheter som ber\\u00f6rs, och viktigast av allt, hur vi som erfaren utvecklings- och designbyr\\u00e5 kan hj\\u00e4lpa er att anpassa er verksamhet i tid.\\n\\n### Vad \\u00e4r EU:s tillg\\u00e4nglighetsdirektiv?\\n\\nEU:s tillg\\u00e4nglighetsdirektiv (Europeiska tillg\\u00e4nglighetsakten, EAA) \\u00e4r ett gemensamt regelverk inom EU som syftar till att harmonisera kraven p\\u00e5 tillg\\u00e4nglighet f\\u00f6r vissa produkter och tj\\u00e4nster.  Direktivet antogs 2019 och syftar till att f\\u00f6rb\\u00e4ttra den inre marknadens funktion genom att undanr\\u00f6ja hinder f\\u00f6r tillg\\u00e4ngliga produkter och tj\\u00e4nster.  Grundl\\u00e4ggande m\\u00e5let \\u00e4r att s\\u00e4kerst\\u00e4lla att personer med funktionsneds\\u00e4ttning har lika tillg\\u00e5ng till viktiga produkter och tj\\u00e4nster som alla andra.\\n\\n**Direktivet omfattar bland annat:**\\n\\n*   **Datorer och operativsystem:**  H\\u00e5rdvara och mjukvara som anv\\u00e4nds dagligen.\\n*   **Bankomater, biljettautomater och incheckningsautomater:** Sj\\u00e4lvbetj\\u00e4ningsterminaler som \\u00e4r viktiga i vardagen.\\n*   **Smarttelefoner:** Mobila enheter och tillh\\u00f6rande utrustning.\\n*   **TV-utrustning relaterad till digitala TV-tj\\u00e4nster:**  F\\u00f6r att s\\u00e4kerst\\u00e4lla tillg\\u00e4ngligt TV-tittande.\\n*   **E-b\\u00f6cker och e-boksl\\u00e4sare:**  F\\u00f6r att g\\u00f6ra litteratur tillg\\u00e4nglig f\\u00f6r fler.\\n*   **E-handel:**  Webbutiker och online-plattformar f\\u00f6r handel.\\n*   **Banktj\\u00e4nster:**  Online-banktj\\u00e4nster och mobilbankl\\u00f6sningar.\\n*   **E-kommunikationstj\\u00e4nster:**  Telefon, internet och andra digitala kommunikationstj\\u00e4nster.\\n*   **Tillg\\u00e5ng till audiovisuella medietj\\u00e4nster som TV-program och relaterad konsumentutrustning:**  F\\u00f6r tillg\\u00e4nglig media-konsumtion.\\n*   **Tj\\u00e4nster inom persontransporter:**  Information i realtid och webbplatser\\/appar f\\u00f6r transporttj\\u00e4nster (flyg, t\\u00e5g, buss, etc.).\\n\\nDirektivet st\\u00e4ller krav p\\u00e5 att dessa produkter och tj\\u00e4nster ska vara tillg\\u00e4ngliga i enlighet med harmoniserade europeiska standarder.  Detta inneb\\u00e4r att de ska vara **anv\\u00e4ndbara, begripliga, robusta och m\\u00f6jliga att uppfatta** f\\u00f6r personer med olika typer av funktionsneds\\u00e4ttningar.\\n\\n### Svenska tillg\\u00e4nglighetslagen \\u2013 Implementering i svensk r\\u00e4tt\\n\\nF\\u00f6r att EU:s tillg\\u00e4nglighetsdirektiv ska g\\u00e4lla i Sverige har vi f\\u00e5tt **Lagen om tillg\\u00e4nglighet till digital offentlig service** (SFS 2018:1937) och nu den nya **Tillg\\u00e4nglighetslagen (SFS 2021:670)**.  Den nya tillg\\u00e4nglighetslagen, som tr\\u00e4der i kraft den 28 juni 2025, \\u00e4r en utvidgning av den tidigare lagen och implementerar EU-direktivet i svensk r\\u00e4tt.\\n\\n**Viktiga punkter i den svenska tillg\\u00e4nglighetslagen:**\\n\\n*   **Bredare omfattning:** Till skillnad fr\\u00e5n den tidigare lagen som fr\\u00e4mst riktade sig mot offentlig sektor, ber\\u00f6r den nya lagen nu \\u00e4ven **privata akt\\u00f6rer** som erbjuder vissa produkter och tj\\u00e4nster p\\u00e5 den svenska marknaden.\\n*   **Lagkrav p\\u00e5 tillg\\u00e4nglighet:**  Lagen st\\u00e4ller tydliga krav p\\u00e5 att de produkter och tj\\u00e4nster som omfattas ska vara tillg\\u00e4ngliga f\\u00f6r personer med funktionsneds\\u00e4ttning. Detta inkluderar inte bara webbplatser och appar, utan \\u00e4ven en rad andra digitala och icke-digitala produkter och tj\\u00e4nster som n\\u00e4mnts ovan under EU-direktivet.\\n*   **Tidsfrist f\\u00f6r anpassning:**  Verksamheter som omfattas av lagen har tid fram till den 28 juni 2025 att s\\u00e4kerst\\u00e4lla att deras produkter och tj\\u00e4nster uppfyller kraven. F\\u00f6r vissa produkter och tj\\u00e4nster som redan finns p\\u00e5 marknaden finns det en l\\u00e4ngre \\u00f6verg\\u00e5ngsperiod.\\n*   **Tillsyn och sanktioner:**  Myndigheten f\\u00f6r digitalisering (DIGG) \\u00e4r tillsynsmyndighet och kommer att \\u00f6vervaka att lagen efterlevs. Vid bristande efterlevnad kan sanktioner i form av **vite** utd\\u00f6mas.\\n\\n### Vilka verksamheter p\\u00e5verkas av den nya lagen?\\n\\nDen svenska tillg\\u00e4nglighetslagen ber\\u00f6r en rad olika verksamheter inom den privata sektorn.  Det \\u00e4r viktigt att unders\\u00f6ka om just din verksamhet omfattas.  **I stora drag ber\\u00f6rs verksamheter som tillhandah\\u00e5ller:**\\n\\n*   **E-handel:**  Webbutiker som s\\u00e4ljer produkter och tj\\u00e4nster online.\\n*   **Banktj\\u00e4nster och finansiella tj\\u00e4nster:**  Online-banktj\\u00e4nster, mobilbank, investeringsplattformar etc.\\n*   **Telekommunikationstj\\u00e4nster:**  Internetleverant\\u00f6rer, mobiloperat\\u00f6rer etc.\\n*   **Transporttj\\u00e4nster f\\u00f6r passagerare:**  Flygbolag, t\\u00e5gbolag, bussbolag, f\\u00e4rjerederier som erbjuder online-bokning och information.\\n*   **Vissa medietj\\u00e4nster:**  Leverant\\u00f6rer av digitala TV-tj\\u00e4nster, streamingtj\\u00e4nster (vissa undantag kan finnas, kontrollera detaljerna i lagen).\\n*   **E-b\\u00f6cker och tillh\\u00f6rande programvara.**\\n\\n**OBS!** Det finns vissa undantag och detaljer i lagen som kan vara viktiga att k\\u00e4nna till. Det \\u00e4r rekommenderat att l\\u00e4sa den fullst\\u00e4ndiga lagtexten och kontakta DIGG eller en expert f\\u00f6r att s\\u00e4kerst\\u00e4lla att ni tolkar lagen korrekt f\\u00f6r just er verksamhet.  **Mikrof\\u00f6retag** (f\\u00e4rre \\u00e4n 10 anst\\u00e4llda och en \\u00e5rsoms\\u00e4ttning eller balansomslutning under 2 miljoner euro) kan i vissa fall vara undantagna fr\\u00e5n vissa delar av lagen, men \\u00e4ven h\\u00e4r finns det specifika regler och det \\u00e4r viktigt att kontrollera vad som g\\u00e4ller.\\n\\n![](https:\\/\\/kalibr.se\\/storage\\/blog-images\\/WrZnqjS5nC79sNSibr995FKa9Jrfl3IxYHqvTDHn.webp)\\n\\n### Hur kan vi som utvecklings- och designbyr\\u00e5 hj\\u00e4lpa er?\\n\\nVi \\u00e4r en erfaren utvecklings- och designbyr\\u00e5 med expertis inom digital tillg\\u00e4nglighet. Vi f\\u00f6rst\\u00e5r komplexiteten i den nya tillg\\u00e4nglighetslagen och hur den p\\u00e5verkar er verksamhet. Vi kan hj\\u00e4lpa er genom hela processen att anpassa era digitala tj\\u00e4nster och produkter s\\u00e5 att de uppfyller lagens krav i god tid f\\u00f6re den 28 juni 2025.\\n\\n**V\\u00e5ra tj\\u00e4nster inkluderar:**\\n\\n*   **Tillg\\u00e4nglighetsrevision och analys:**  Vi genomf\\u00f6r en grundlig granskning av era befintliga webbplatser, appar och andra digitala gr\\u00e4nssnitt f\\u00f6r att identifiera brister i tillg\\u00e4nglighet. Vi anv\\u00e4nder oss av vedertagna riktlinjer som **WCAG (Web Content Accessibility Guidelines)** och den senaste kunskapen inom omr\\u00e5det.\\n*   **Design och utveckling av tillg\\u00e4ngliga l\\u00f6sningar:**  Vi designar och utvecklar nya webbplatser, appar och digitala tj\\u00e4nster fr\\u00e5n grunden med tillg\\u00e4nglighet i fokus. Vi s\\u00e4kerst\\u00e4ller att alla aspekter, fr\\u00e5n kod till design, \\u00e4r optimerade f\\u00f6r tillg\\u00e4nglighet.\\n*   **\\u00c5tg\\u00e4rdande av befintliga l\\u00f6sningar (remediering):**  Vi hj\\u00e4lper er att \\u00e5tg\\u00e4rda befintliga webbplatser och appar som inte uppfyller tillg\\u00e4nglighetskraven. Vi utf\\u00f6r de n\\u00f6dv\\u00e4ndiga anpassningarna i kod, design och inneh\\u00e5ll.\\n*   **Testning av tillg\\u00e4nglighet:**  Vi utf\\u00f6r noggranna tester av era digitala l\\u00f6sningar med hj\\u00e4lp av b\\u00e5de automatiserade verktyg och manuell testning med experter och anv\\u00e4ndare med funktionsneds\\u00e4ttning f\\u00f6r att s\\u00e4kerst\\u00e4lla att de verkligen fungerar f\\u00f6r alla.\\n*   **L\\u00f6pande support och underh\\u00e5ll:**  Vi erbjuder l\\u00f6pande support och underh\\u00e5ll f\\u00f6r att s\\u00e4kerst\\u00e4lla att era digitala l\\u00f6sningar forts\\u00e4tter att vara tillg\\u00e4ngliga \\u00e4ven efter lansering och vid uppdateringar.\\n\\n### Ta steget mot tillg\\u00e4nglighet \\u2013 Kontakta oss idag!\\n\\nV\\u00e4nta inte tills sista minuten! Att anpassa er verksamhet till den nya tillg\\u00e4nglighetslagen \\u00e4r inte bara en fr\\u00e5ga om lagkrav, utan ocks\\u00e5 en m\\u00f6jlighet att n\\u00e5 ut till en bredare m\\u00e5lgrupp, f\\u00f6rb\\u00e4ttra anv\\u00e4ndarupplevelsen f\\u00f6r alla och st\\u00e4rka ert varum\\u00e4rke som inkluderande och ansvarstagande.\\n\\nKontakta oss p\\u00e5 **Kalibr** idag f\\u00f6r en **kostnadsfri konsultation**. Vi ber\\u00e4ttar mer om hur vi kan hj\\u00e4lpa er att navigera r\\u00e4tt i den nya lagstiftningen och s\\u00e4kerst\\u00e4lla att er verksamhet \\u00e4r redo f\\u00f6r framtiden.  L\\u00e5t oss tillsammans skapa ett mer tillg\\u00e4ngligt och inkluderande digitalt samh\\u00e4lle!\\n\"', 1, '2025-02-05 00:00:00', '2025-02-05 15:27:20', '2025-02-07 15:02:18', NULL, 'posts/01JKBCRRNTG8JXCV9A09K2TV0Z.webp', NULL, NULL, NULL, NULL, NULL, 'Är din verksamhet redo för den nya tillgänglighetslagen 2025', 'Den nya tillgänglighetslagen träder i kraft 28 juni 2025. Vi hjälper dig anpassa dina digitala tjänster för att uppfylla kraven och skapa inkluderande lösningar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'WordPress 2025: Framtidens möjligheter och revolutionerande utveckling', 'wordpress-2025-framtidens-mojligheter-och-revolutionerande-utveckling', '\"WordPress har l\\u00e4nge varit en av v\\u00e4rldens mest popul\\u00e4ra plattformar f\\u00f6r webbutveckling, och 2025 kommer den att bli kraftfullare, smartare och mer m\\u00e5ngsidigt \\u00e4n n\\u00e5gonsin. Med framsteg inom AI, automatisering och ny teknik \\u00f6ppnas helt nya d\\u00f6rrar f\\u00f6r f\\u00f6retag att skapa webbplatser som inte bara imponerar \\u2013 utan ocks\\u00e5 levererar exceptionella anv\\u00e4ndarupplevelser.\\n\\nSom en byr\\u00e5 med djup expertis inom WordPress och AI \\u00e4r vi h\\u00e4r f\\u00f6r att visa dig hur du kan dra nytta av dessa framsteg och positionera ditt f\\u00f6retag i framkant. L\\u00e5t oss utforska vad som v\\u00e4ntar inom WordPress-utveckling 2025 \\u2013 och hur vi kan hj\\u00e4lpa dig att bli en del av revolutionen.\\n\\n![](https:\\/\\/kalibr.se\\/storage\\/blog-images\\/DTZGGhkYnWFtoCxFNWXBBcBFzjJzDNN82Mc381gH.jpg)\\n\\n### 1. **AI-drivet inneh\\u00e5ll och automatisering**  \\nI \\u00e5r kommer AI att bli mer och mer integrerat i varje steg av WordPress-utvecklingen. Genom verktyg som **Jetpack AI Assistant**, **WordLift** och anpassade LLM:er (som GPT-4 och Claude 3) kan du:  \\n- **Generera inneh\\u00e5ll automatiskt**: Skapa blogginl\\u00e4gg, produktbeskrivningar och meta-taggar med enkel prompting.  \\n- **Optimera f\\u00f6r SEO i realtid**: F\\u00e5 AI-drivna f\\u00f6rslag p\\u00e5 nyckelord, strukturer och l\\u00e4nkar direkt i din redigerare.  \\n- **Personalisera anv\\u00e4ndarupplevelser**: Anv\\u00e4nd maskininl\\u00e4rning f\\u00f6r att dynamiskt anpassa inneh\\u00e5ll baserat p\\u00e5 bes\\u00f6kares beteenden.  \\n\\nDetta inneb\\u00e4r inte bara snabbare utveckling \\u2013 det frig\\u00f6r tid f\\u00f6r kreativa strategier och innovation.  \\n\\n\\n### 2. **Headless WordPress och omni-channel-upplevelser**  \\nHeadless-arkitektur (d\\u00e4r frontend och backend separeras) blir standard 2025. Med tekniker som **React**, **Vue.js** och **Next.js** kan du bygga:  \\n- **Blixksnabba webbplatser**: \\u00d6ka laddningstider och prestanda genom statiskt genererade sidor med **WP Engine Atlas**.  \\n- **Cross-platform-l\\u00f6sningar**: Samma WordPress-backend kan driva webbplatser, appar, digitala skyltar och till och med AR\\/VR-upplevelser.  \\n- **Framtidss\\u00e4krad flexibilitet**: Enkelt integrera nya tekniker utan att p\\u00e5verka din befintliga plattform.  \\n\\nVi hj\\u00e4lper dig att navigera denna \\u00f6verg\\u00e5ng och bygga system som v\\u00e4xer med dina behov.  \\n\\n\\n### 3. **Blockeditorns nya era**  \\nGutenberg-blockeditorn kommer att vara en komplett \\\"no-code\\\"-l\\u00f6sning med:  \\n- **AI-st\\u00f6dda block**: Skapa komplexa layouter med drag-and-drop och AI-f\\u00f6rslag.  \\n- **Dynamiskt inneh\\u00e5ll**: Koppla block direkt till CRM-system, databaser eller externa API:er.  \\n- **Interaktiva element**: Bygg animerade landing pages och mikroupplevelser utan kodning.  \\n\\nPlugins som **Elementor** och **Divi** kommer att erbjuda \\u00e4nnu mer avancerade verktyg f\\u00f6r att konkurrera med renodlade SaaS-plattformar.\\n\\n![](https:\\/\\/kalibr.se\\/storage\\/blog-images\\/1EQ40KljbsfOzzpuT7EwErLPSW1tYlm1mvlkcghR.webp)\\n\\n### 4. **Web3 och decentraliserade l\\u00f6sningar**  \\nWordPress m\\u00f6ter framtidens tekniker:  \\n- **NFT-integrering**: S\\u00e4lj digitala produkter eller skapa medlemscommunitys med blockchain-baserad autentisering.  \\n- **Decentraliserad hosting**: S\\u00e4kerst\\u00e4ll datasuver\\u00e4nitet med plattformar som **Polygon** eller **IPFS**.  \\n- **Smart Contracts**: Automatisera betalningar, licenser och avtal direkt via din webbplats.  \\n\\nVi utforskar dessa m\\u00f6jligheter tillsammans med dig f\\u00f6r att skapa unika l\\u00f6sningar som sticker ut.  \\n\\n\\n### 5. **H\\u00e5llbarhet och prestanda i fokus**  \\nMed \\u00f6kade krav p\\u00e5 CO2-avtryck och anv\\u00e4ndarupplevelser blir optimering avg\\u00f6rande:  \\n- **Gr\\u00f6n hosting**: V\\u00e4lj leverant\\u00f6rer som **GreenGeeks** eller **Kinsta** med f\\u00f6rnybar energi.  \\n- **Automatiserad prestandajustering**: AI-verktyg som **WP Rocket AI** analyserar och optimerar din webbplats kontinuerligt.  \\n- **Zero-waste design**: Minimalistiska teman och lazy loading blir standard f\\u00f6r att minska dataanv\\u00e4ndning.  \\n\\n\\n### **Varf\\u00f6r v\\u00e4lja oss?**  \\nVi \\u00e4r inte bara en WordPress-byr\\u00e5 \\u2013 vi \\u00e4r dina partners i att forma framtidens digitala n\\u00e4rvaro. Med v\\u00e5r kombination av tekniskt kunnande, AI-expertis och kreativitet hj\\u00e4lper vi dig att:  \\n- Implementera AI-drivna l\\u00f6sningar som sparar tid och \\u00f6kar konvertering.  \\n- Bygga headless- eller Web3-system som \\u00e4r redo f\\u00f6r morgondagen.  \\n- S\\u00e4kerst\\u00e4lla prestanda, s\\u00e4kerhet och h\\u00e5llbarhet i varje projekt.  \\n\\n  \\n\\u00c4r du redo att utnyttja den senaste tekniken f\\u00f6r att skapa en webbplats som inte bara m\\u00f6ter dagens krav, utan \\u00e4ven framtidens utmaningar? Kontakta oss idag f\\u00f6r en kostnadsfri konsultation. Tillsammans tar vi din digitala n\\u00e4rvaro till n\\u00e4sta niv\\u00e5.  \\n\\n\"', 1, '2025-02-04 00:00:00', '2025-02-05 18:48:24', '2025-02-05 19:02:12', NULL, 'posts/01JKBQ29XSXPBZD66JNDM4AXPP.webp', NULL, NULL, NULL, NULL, NULL, 'WordPress 2025: Framtidens möjligheter och revolutionerande ', 'Upptäck hur WordPress 2025 med AI, headless-arkitektur och Web3 revolutionerar webbutveckling. Framtidssäkra din plattform med smart automatisering & hållbarhet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1oEggBUczGGbcdTssUgXOeQrVYguS7llsUqnbJwL', NULL, '202.8.43.235', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRjdFMGxHak84bXBKaFJRUW9hYkR1N1k0Z09LUnFKN1ZicjJDZTlVRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8va2FsaWJyLnNlL2tvbnRha3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1739107595),
('5f4zKffFvC3liLvTUQoE01DTUPzzQ0A9m5NxDTed', NULL, '202.8.43.236', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM2FjYlFOOXhZM0FFNFMzNHNFaWdxd3JDSXdnWXRITHh4MG9FRVA4cSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTEzOiJodHRwczovL2thbGlici5zZS9ibG9nL2h1ci1haS1hdXRvbWF0aXNlcmluZy1rYW4tdHJhbnNmb3JtZXJhLWRpdHQtZm9yZXRhZy1vY2gtdmFyZm9yLWR1LWJvcmRlLXByYXRhLW1lZC1vc3MtaWRhZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739107587),
('6DfAd6iK1J3kYU4geHfcIMn2v1eanDuYN8Wtmnyy', NULL, '202.8.43.233', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEJJREJSSG5UT28yMmZDczJrR1V0OFRFWFRQU3FjODA2ZWlzV3prTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8va2FsaWJyLnNlL3NpdGVtYXAueG1sIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107569),
('6ZczPwxkaAxYDS1qLspFdp3qpBHIV2NuEi54preO', NULL, '202.8.43.235', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGtNTnBzMGlJVGtDWGRIelZZb2hVa1VHRUpiMTFoUlhhOERDMmhESCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8va2FsaWJyLnNlL2FudmFuZGFydmlsbGtvciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739107571),
('7q00zGGsusHRqpnrXB8FqRNSi7WzYiU5dLwQFznG', NULL, '2a06:98c0:3600::103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRGxsMTA3ZFVpaUxvZzlqV1FCdDFYZ0lhRjVrWmxqMEt1YkJ6ek42YSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8va2FsaWJyLnNlL3dwLWFkbWluL3NldHVwLWNvbmZpZy5waHAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1739115241),
('aUhEQCZWFGMA7j4NQpaTLm4JF7sV7bzueNu7tMkb', NULL, '35.95.103.37', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYjg5bjczOG5iVXY1d2t3UGVrVW04eVFUT3RYQmtIczZLaUVIQlYxSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8va2FsaWJyLnNlL2FwaS9zZXR0aW5ncyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739112480),
('Awtu6wN8ul1XWeTQ9iHhazcVBc2VOh8EZP49Be3d', NULL, '81.88.48.120', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQzRhbml1a3VSS3UxaXdVeFMxeVhIZXo2NWtZcW03WGY5TFlLS1U0eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8va2FsaWJyLnNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739104184),
('CFJjDzUGkg5yTUcORVBtA1XlK6Gw43tw9FSFeMzQ', NULL, '94.254.63.90', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmZyWHNYM2tIb3dSeU81cURCVk5wd2ZNclFmYzFrNnh5YVN4eWRDViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8va2FsaWJyLnNlL2FwaS9wb3N0cz9saW1pdD0zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739115426),
('dj1XZLRkqxIqTphrWmJgBdpfq7XcByXCowuXSDUe', NULL, '2a06:98c0:3600::103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaXJFM0hKVTRnOGx0MDgxTVA1WnRWZGNnWTd2ZTYza0wyUk9Qc3BTSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHBzOi8va2FsaWJyLnNlL3dvcmRwcmVzcy93cC1hZG1pbi9zZXR1cC1jb25maWcucGhwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739115199),
('FEXan1iI1t4jBpU9fDMfAgMM93AzfHgl9Hleo1M0', NULL, '202.8.43.233', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQW5hWVp3Mm1EN1RoeVBBOGp3V2JwNFFOcWhpVUxtalJXajhDbXBQYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8va2FsaWJyLnNlL29tLWthbGliciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739107599),
('g68D6mjaXOi1auIAYTtqpBz80OqcCdJcbXEUK28u', NULL, '202.8.43.234', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQXc3MVlLNWhvalRmRjE2S0I4bDdMSEFYNTZrWjJ2TkNHTGxOaGZ3OSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTI6Imh0dHBzOi8va2FsaWJyLnNlL2Jsb2cvd29yZHByZXNzLTIwMjUtZnJhbXRpZGVucy1tb2psaWdoZXRlci1vY2gtcmV2b2x1dGlvbmVyYW5kZS11dHZlY2tsaW5nIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107601),
('guC3dKhwOleSBVjPImfe9DHGpQDf9kyD2WDyxVuF', NULL, '202.8.43.232', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWdxV29LNUhTcHd4WWREODJMYmdNWjlHajNma2NjN0VtZkpaTlRxeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8va2FsaWJyLnNlL2ludGVncml0ZXRzcG9saWN5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107579),
('h9jdhAGGXh7IQ7LmPKL8sqYA5vvccKhaM0hfnm0s', NULL, '202.8.43.236', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMW50VDhUSTAwNEl4TkQ1Rk5sTkxBN1FhY1VwYUxGUElSdGNpOXlYdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8va2FsaWJyLnNlL2hlbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739107575),
('isrRMhlZS6X2TiIItgViK7FgZwodAzUVtB2rc8Lf', NULL, '202.8.43.234', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN09uTHpYOFhsQjVLRXVxMXpzVmRPUmhmempSWFB0TmM1Tm1oeUFLRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8va2FsaWJyLnNlL3NpdGVtYXBfaW5kZXgueG1sIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107563),
('LiomOoXpFtwNhdyeGqZSPIWzcTzse9viei0c0ppY', NULL, '15.204.161.7', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmpjazg2VWYyS1I5YlFQeWdQcjNnM3B4TEdPWHdFYWNVY1NYZk5tZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8va2FsaWJyLnNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739105877),
('Mb3S6BD0PeAQEhC39Q82avS3ZwapKNW14d5PuUt4', NULL, '202.8.43.234', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQllqaXhWTk1JQm1ldEo1c3F2UldEb0RKQm1KM0lURk51SDExY3J2TyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8va2FsaWJyLnNlL3V0dmVja2xpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1739107593),
('MG3dAdynW6bHhcxYsd3W68MMdr4FYA2dIdUFxWLj', NULL, '202.8.43.233', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMVlrbGZYUkxPS2paaFVwaGY4YldxUW1lUEtyNnFTODU3VzNpVWN3ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTQ6Imh0dHBzOi8va2FsaWJyLnNlL2Jsb2cvYWdlbnRpYy1yYWctZnJhbXRpZGVucy1haS1sb3NuaW5nLWZvci1zbWFydGFyZS1vY2gtbWVyLWVmZmVrdGl2YS1zeXN0ZW0iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1739107589),
('NcTBDdFoTd2AHX7BN3rSfUuGxxSMOSEyNGPDDq5k', NULL, '2a06:98c0:3600::103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicGtXZ2NoYW1ya3NpVjlyQkl3bTRXTzdXd3pTSkJ2SmNtN05rUmt5byI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHBzOi8va2FsaWJyLnNlL3dvcmRwcmVzcy93cC1hZG1pbi9zZXR1cC1jb25maWcucGhwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739115232),
('Ok3UBdXAse6vjKEkGyTmFsUm0zlZbhZruOm0xa0t', NULL, '202.8.43.236', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMlkyMGJJcVIyZ2s3Vk9kdkloNmJTaHBueFpKNmYwMWY5TFAyQXN4VyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8va2FsaWJyLnNlL2dlbmVyYXRpdi1haSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739107603),
('roZtndaV3YHYy0N5natbNZYZDvN1d7kwlsiZOp7w', NULL, '44.244.91.163', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidTFCdGg2Y1JkQnZ1RnU4Q25FM0lNdUdKWHQwcEc0aDBJNURJbzl5cSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8va2FsaWJyLnNlL2FwaS9zZXR0aW5ncyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739112480),
('TIarH4V3xuDwmsHnjgnadT5lJrNmbpOupwIpxkJf', NULL, '202.8.43.236', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSEtoSnczUmk2QjVPaGZ5TFozZm8zdzM2ZWMzeE56OUlhSXB6QTF6SiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8va2FsaWJyLnNlL3NpdGVtYXBfaW5kZXgueG1sIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107567),
('U7dk0WJW0BjrFtUVFPHFhS3cS1hdXLx82OEbYHhg', NULL, '202.8.43.233', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFFuQVdVaG8wZkx5M2M3QjVHYUF6cTNtTlJ5MXhrSGw2clJPRTVQUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8va2FsaWJyLnNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107573),
('uxVUMYbaqDwATwV3hMgGuMrabHvacaAHyoBM4leH', NULL, '202.8.43.235', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidld3cExxTEJPcTZ0dExtc1V0RDlmQ01lWXhNY25XQmZOaWtzWlg5MiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8va2FsaWJyLnNlL2Jsb2dnIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107577),
('VdPbQu5WkK7s2SvIl0KaiESPq8rYULlDex2NK76U', NULL, '202.8.43.234', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkRnbTN5QzJmZklJNVBGRk1NbmFnNDNMMktwTzVmTXBrTDdFVTk0ciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTA3OiJodHRwczovL2thbGlici5zZS9ibG9nL2FyLWRpbi12ZXJrc2FtaGV0LXJlZG8tZm9yLWRlbi1ueWEtdGlsbGdhbmdsaWdoZXRzbGFnZW4tdmktaGphbHBlci1kaWctbmF2aWdlcmEtcmF0dCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739107585),
('wE6EGV6CAAifTT6uywRPlJwIoCSXE6bkv2QRkitc', NULL, '202.8.43.234', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjg2a0RHdWlOZzNPTFJ6Q0pOUzQ1WDBjaVRBUTJkRXJreWpNS21sSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8va2FsaWJyLnNlL3VpdXgtZGVzaWduIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107591),
('WjfzuOVUYZ9RkAURjW95wH3uyYaESUdUe7oV8Btn', NULL, '2a06:98c0:3600::103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ2VENTN1VFRxd1FBWVVEeGJjZEp0QUlRQ3dPOHRmR2M2V3RPaXpVMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8va2FsaWJyLnNlL3dwLWFkbWluL3NldHVwLWNvbmZpZy5waHAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1739115318),
('Y39RorBOG51iBqqP1LtjgZNIOmAF0qpNTYTdIkc0', NULL, '202.8.43.232', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN29DYXh5Ulg0ME9FbjJjNXg1VzNDcmFOWW1wRWowMWFRczNuUENReCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vd3d3LmthbGlici5zZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739107607),
('YRg0xcaBekr4EDv6suycEkK4TRSMU1pGHWhIy1sE', NULL, '104.166.80.222', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidHZRMUZFWUtNMGNxTFp6dVlmQ2NFaEswM2RBTVdVem45b0Y0WVk1NiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTc6Imh0dHBzOi8va2FsaWJyLnNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739106858),
('zBS9z6k2VaQBTJDAhEBdCZeBGz8FaTCSJlf8V8SH', NULL, '202.8.43.235', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUmxPM05WZWhlMjI4U0FsV0NrMXdXNE9MVXlMbG9hS2FFV2FocktjMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTAwOiJodHRwczovL2thbGlici5zZS9ibG9nL3ZhcmZvci1mb3JldGFnLWJvci1zYXRzYS1wYS1jaGF0Ym90cy1vY2gtaHVyLWRldC1rYW4tZm9yYmF0dHJhLWRpbi12ZXJrc2FtaGV0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107597),
('zfbJQexHULg9naOh6Ej7iNPbzkWY7mqWhgw7Bfqu', NULL, '15.204.161.7', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzhzbG90OWVOUXV0MUVYTk1HVlBVZWJHWG5HVkxjbFdYU0JuQ1NMUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vd3d3LmthbGlici5zZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739105877),
('zNJX78H1G5FUe7GgBVNUxNW83axTeGkKwtMh8S0n', NULL, '202.8.43.236', 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.5359.128 Mobile Safari/537.36 (compatible; AhrefsSiteAudit/6.1; +http://ahrefs.com/robot/site-audit)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmp6VXpkYllrdk1nMEdaaDdWeG0wUW5SV1RGR1dYZVc4ZVBkQzhQdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTU6Imh0dHBzOi8va2FsaWJyLnNlL2Jsb2cvZnJhbXRpZGVucy13ZWJidXR2ZWNrbGluZy0yMDI1LWh1ci1haS1vY2gtbGxtZXItcmV2b2x1dGlvbmVyYXItYnJhbnNjaGVuIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739107583);

-- --------------------------------------------------------

--
-- Tabellstruktur `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `navigation_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `show_contact_in_menu` tinyint(1) NOT NULL DEFAULT '0',
  `show_social_in_menu` tinyint(1) NOT NULL DEFAULT '0',
  `menu_contact_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `settings`
--

INSERT INTO `settings` (`id`, `group`, `name`, `locked`, `payload`, `created_at`, `updated_at`, `navigation_type`, `show_contact_in_menu`, `show_social_in_menu`, `menu_contact_info`) VALUES
(1, 'general', 'site_name', 0, '\"Kaliber Creative Studio\"', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(2, 'general', 'tagline', 0, '\"UI/UX Design & Web Development\"', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(3, 'general', 'email', 0, '\"info@kalibr.se\"', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(4, 'general', 'phone', 0, 'null', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(5, 'general', 'address', 0, '\"Simrishamnsgatan 20a\\n214 23 Malmö\"', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(6, 'general', 'facebook_url', 0, '\"https://www.facebook.com/people/Kalibr/61572485891218/\"', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(7, 'general', 'instagram_url', 0, 'null', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(8, 'general', 'linkedin_url', 0, '\"https://www.linkedin.com/company/kalibr-se/\"', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(9, 'general', 'logo', 0, '\"branding/01JC4CZYT3J3A2GZGB058HYFNR.svg\"', '2024-10-31 21:13:44', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(11, 'general', 'navigation_type', 0, '\"normal\"', NULL, '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(12, 'general', 'show_contact_in_menu', 0, 'true', NULL, '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(13, 'general', 'show_social_in_menu', 0, 'true', NULL, '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(14, 'general', 'menu_contact_info', 0, 'null', NULL, '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(86, 'general', 'head_code_snippets', 0, '\"<script src=\\\"https://analytics.ahrefs.com/analytics.js\\\" data-key=\\\"TU5AcWEy4FhIRfpMqOciyQ\\\" async></script>\\n<meta name=\\\"ahrefs-site-verification\\\" content=\\\"0c69d06e6a077e1ea17addedb849946e71165111e070606ce4720836e1b76a4f\\\">\\n<link rel=\\\"icon\\\" type=\\\"image/png\\\" href=\\\"/favicon-96x96.png\\\" sizes=\\\"96x96\\\" />\\n<link rel=\\\"icon\\\" type=\\\"image/svg+xml\\\" href=\\\"/favicon.svg\\\" />\\n<link rel=\\\"shortcut icon\\\" href=\\\"/favicon.ico\\\" />\\n<link rel=\\\"apple-touch-icon\\\" sizes=\\\"180x180\\\" href=\\\"/apple-touch-icon.png\\\" />\\n<link rel=\\\"manifest\\\" href=\\\"/site.webmanifest\\\" />\"', '2025-02-06 10:54:48', '2025-02-07 14:54:40', 'normal', 0, 0, NULL),
(87, 'general', 'footer_code_snippets', 0, 'null', '2025-02-06 10:54:48', '2025-02-07 14:54:40', 'normal', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Kaliber Creative', 'info@kalibercreative.se', NULL, '$2y$12$N9a3mJfwRYEChkk1VCCGIOHF.6WoWMyCg9rCGNwpoO1RetSBUk4ny', NULL, '2024-10-31 21:26:15', '2024-10-31 21:26:15');

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index för tabell `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index för tabell `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Index för tabell `category_post`
--
ALTER TABLE `category_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_post_category_id_foreign` (`category_id`),
  ADD KEY `category_post_post_id_foreign` (`post_id`);

--
-- Index för tabell `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index för tabell `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index för tabell `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Index för tabell `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`),
  ADD KEY `pages_parent_id_foreign` (`parent_id`);

--
-- Index för tabell `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index för tabell `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `posts_slug_unique` (`slug`),
  ADD KEY `posts_author_id_foreign` (`author_id`),
  ADD KEY `posts_category_id_foreign` (`category_id`);

--
-- Index för tabell `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index för tabell `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_group_name_unique` (`group`,`name`);

--
-- Index för tabell `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT för tabell `category_post`
--
ALTER TABLE `category_post`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT för tabell `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT för tabell `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT för tabell `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT för tabell `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT för tabell `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `category_post`
--
ALTER TABLE `category_post`
  ADD CONSTRAINT `category_post_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_post_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL;

--
-- Restriktioner för tabell `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
