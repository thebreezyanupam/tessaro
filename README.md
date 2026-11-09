# Tessaro Terrazzo

A custom WordPress theme for Tessaro & Daughters, a fictional terrazzo workshop. Custom made by Anupam Pandey. The contents and writings on the site are fictional and AI-generated. No page builder or required plugins. Requires WordPress 6.4+ and PHP 7.4+.

## Install

Go to **Appearance > Themes > Add New > Upload Theme**, choose `tessaro-terrazzo.zip`, then activate it. Six sample floors and nine stones are created the first time the site loads with the theme active. If you delete them later they stay deleted.

Set **Settings > Permalinks** to "Post name" so project pages have clean addresses.

## Editing the site

| What | Where |
|---|---|
| Home page text, prices, history, phone, address | Appearance > Customize > Tessaro & Daughters |
| Projects, including each floor's recipe | Floors |
| Stones in the Mix Your Own tool and the stone list | Stones |
| Messages from the enquiry form | Enquiries |
| Navigation (optional, has sensible defaults) | Appearance > Menus, "Primary navigation" |

Set a featured image on a floor to show a real photo on the listing pages in place of the generated floor.

## Before using this for a real business

- Replace the fictional, AI-generated details: name, address, phone, email, prices, projects and history. The name inlaid in the hero and footer floors is set in `front-page.php` and `footer.php`, and the note in `footer.php` credits that the business and writings are fictional and AI-generated, and that the theme is custom made by Anupam Pandey.
- Enquiry emails go to the address in **Settings > General**. Enquiries are always saved in wp-admin first, so a failed email never loses one.
- Set up SMTP on the live host so those emails are delivered reliably.

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
