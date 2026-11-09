# Tessaro Terrazzo

A custom WordPress theme I built from scratch for **Tessaro & Daughters**, a fictional terrazzo workshop. Every floor on the site is drawn live in WebGL from stones and projects the owner manages in wp-admin. No page builder, no required plugins.

**[▶ Open the live demo](https://playground.wordpress.net/#%7B%22landingPage%22%3A%22%2F%22%2C%22preferredVersions%22%3A%7B%22php%22%3A%228.3%22%2C%22wp%22%3A%22latest%22%7D%2C%22features%22%3A%7B%22networking%22%3Atrue%7D%2C%22steps%22%3A%5B%7B%22step%22%3A%22login%22%7D%2C%7B%22step%22%3A%22installTheme%22%2C%22themeData%22%3A%7B%22resource%22%3A%22git%3Adirectory%22%2C%22url%22%3A%22https%3A%2F%2Fgithub.com%2Fthebreezyanupam%2Ftessaro%22%2C%22ref%22%3A%22main%22%2C%22refType%22%3A%22branch%22%7D%2C%22options%22%3A%7B%22activate%22%3Atrue%7D%7D%2C%7B%22step%22%3A%22setSiteOptions%22%2C%22options%22%3A%7B%22permalink_structure%22%3A%22%2F%25postname%25%2F%22%7D%7D%2C%7B%22step%22%3A%22runPHP%22%2C%22code%22%3A%22%3C%3Fphp%20require%20%27%2Fwordpress%2Fwp-load.php%27%3B%20flush_rewrite_rules%28%29%3B%20%3F%3E%22%7D%5D%7D)**: a full WordPress site with the theme installed, running in your browser through WordPress Playground. It takes about 15 seconds to start, and you're logged in as admin so you can look around wp-admin too.

> The business, its history, prices and projects are made up and the site copy is AI-generated. The code, design and build are mine.

## What's inside

- **Custom theme templates** for the home page, the Floors archive, single project pages and a 404 page
- **Custom post types** for Floors, Stones and Enquiries, edited through native meta boxes, so the owner updates content without touching code
- **Customizer settings** for every piece of home page text, prices, history and contact details
- **A WebGL2 floor renderer** that turns a recipe of binder colour and stones into a polished terrazzo floor, with the business name inlaid in brass
- **Mix Your Own**: visitors design a floor from the real stone list and send it straight to the enquiry form
- **An enquiry form** over the WordPress REST API, with nonce checks, sanitised input, per-connection rate limiting, and every enquiry saved in wp-admin before the email goes out, so a failed email never loses one
- **Accessibility**: a skip link, labelled controls, and full support for reduced motion. Without WebGL2 or with reduced motion on, every bit of content stays readable and the pinned process section becomes a plain list
- **No third-party requests**: GSAP, Lenis and both fonts are bundled locally

**Built with:** PHP, WordPress 6.4+, JavaScript, WebGL2, GSAP (ScrollTrigger, SplitText), Lenis, CSS

## Install

1. On this page, click **Code > Download ZIP**.
2. In WordPress, go to **Appearance > Themes > Add New > Upload Theme**, choose the ZIP, then **Activate**.
3. Set **Settings > Permalinks** to "Post name" so project pages get clean addresses.

Six sample floors and nine stones are created the first time the site loads with the theme active. If you delete them later, they stay deleted.

## Editing the site

| What | Where |
|---|---|
| Home page text, prices, history, phone, address | Appearance > Customize > Tessaro & Daughters |
| Projects, including each floor's recipe | Floors |
| Stones in the Mix Your Own tool and the stone list | Stones |
| Messages from the enquiry form | Enquiries |
| Navigation (optional, has sensible defaults) | Appearance > Menus, "Primary navigation" |

Set a featured image on a floor to show a real photo on the listing pages in place of the generated floor.

## Code map

- `assets/js/slab.js`: WebGL2 floor renderer (stone pattern, inlaid letters, grinding, polish).
- `assets/js/core.js`: shared helpers, smooth scrolling, in-page links, the header.
- `assets/js/floors.js`: connects floors to the page and draws the project cards.
- `assets/js/hero.js`: the brush pass, stickers and the statement section.
- `assets/js/process.js`: the pinned four-stage section.
- `assets/js/mix.js`: the Mix Your Own tool.
- `assets/js/type.js`: text animation.
- `assets/js/enquiry.js`: enquiry form submission.
- `assets/js/main.js`: starts everything once the display font has loaded.
- `inc/fields.php`: every Customizer field and its default text.
- `inc/content-types.php`: Floors and Stones, with their editor boxes.
- `inc/enquiries.php`: enquiry storage, validation, email.
- `inc/seed.php`: first-run sample content.

GSAP and Lenis are bundled in `assets/vendor`, and the fonts (Anybody, Newsreader) in `assets/fonts`, so the site makes no third-party requests.

If a visitor's browser has no WebGL2 or asks for reduced motion, the site still works: all content stays readable, and the pinned process section becomes a plain stacked list.

## License

Copyright (c) 2026 Anupam Pandey. All rights reserved. The code is public to view and evaluate, not to reuse. See [LICENSE](LICENSE). Bundled libraries and fonts keep their own licenses, listed in [THIRD-PARTY-NOTICES.md](THIRD-PARTY-NOTICES.md).
