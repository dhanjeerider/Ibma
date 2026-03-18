# IBAPAM — Blogger Movie Theme

A full-featured **Blogger XML theme** for movie/entertainment websites (matching the design of `vagam4u.com.in` / `ibapam.in`), plus a standalone **TMDB Bulk Post Generator** tool.

---

## 📁 Files

| File | Description |
|------|-------------|
| `theme.xml` | Complete Blogger XML theme — import directly into Blogger |
| `tmdb-post-generator.html` | Standalone HTML tool to bulk-generate posts from TMDB API |
| `static-pages.html` | Ready-to-use HTML for About, Contact, Privacy Policy, DMCA, Disclaimer, Request pages |

---

## 🎨 Theme Features (`theme.xml`)

- **Dark movie-site design** — same classes, HTML structure, and CSS as the reference sites
- **Responsive** — works on mobile, tablet, desktop
- **Header** — sticky header with logo, search bar, Request button, hamburger menu
- **Navigation** — full dropdown + mega-menu navbar (Bollywood, Hollywood, Web Series, South Hindi, Animation)
- **Hero Slider** — auto-playing featured post slider with dot controls
- **Filter Tabs** — filter posts by category on homepage
- **Post Cards** — poster-style cards with hover effects, rating badge, quality badge
- **Sidebar** — search, ad slot, categories, popular posts, tags, archive widgets
- **Single Post Layout** — movie info table, synopsis, screenshots gallery, download section with multiple quality buttons
- **Share Buttons** — Telegram, Facebook, Twitter, WhatsApp
- **Related Posts** — auto-loaded via Blogger JSON API
- **Live Ticker** — latest posts scrolling ticker
- **Footer** — 4-column footer with social links, categories, pages, disclaimer
- **SEO** — JSON-LD structured data for Movie schema
- **Back to Top** button
- **Mobile Nav** — slide-in drawer navigation

---

## 🛠️ How to Install the Theme

1. Go to your **Blogger Dashboard**
2. Click **Theme → Customise → Restore → Upload**
3. Upload `theme.xml`
4. Click **Apply to Blog**

> ⚠️ Back up your current theme before installing.

---

## 🎬 TMDB Bulk Post Generator (`tmdb-post-generator.html`)

Open `tmdb-post-generator.html` in any browser — no server needed.

### Steps:
1. **Get a free TMDB API key** from [themoviedb.org/settings/api](https://www.themoviedb.org/settings/api)
2. Enter your API key in the tool
3. **Search** for movies/series OR paste TMDB IDs in the Bulk tab
4. **Select** the items you want
5. Click **Add to Queue** → **Generate All Posts**
6. Copy the generated HTML and paste it into your Blogger post editor (HTML mode)

### Features:
- Search by title, year, content type (movie / TV / multi)
- Discover trending, popular, now-playing
- Bulk process by TMDB IDs (`movie:12345` or `tv:67890`)
- Generates complete post HTML: movie info table, synopsis, trailer embed, screenshots, download buttons
- Customisable settings: quality, audio, download sizes, site name, Telegram channel, disclaimer
- Settings saved in browser localStorage
- Export all generated posts to clipboard or `.txt` file

---

## 📄 Post HTML Structure

Each generated post includes:

```html
<!-- Movie Info Box (poster + details table) -->
<!-- Synopsis -->
<!-- Official Trailer (YouTube embed) -->
<!-- Screenshots Gallery -->
<!-- Download Section (multiple quality links) -->
<!-- How-to-download note -->
<!-- Tags -->
<!-- Disclaimer -->
```

---

## 📋 Label / Category Setup

Create these labels in Blogger for the navigation to work:

- `Bollywood Movies` · `Hollywood Movies` · `South Hindi Dubbed`
- `Hollywood Hindi Dubbed` · `Hindi Web Series` · `English Web Series`
- `Netflix` · `Amazon Prime` · `Disney Hotstar` · `SonyLIV` · `ZEE5`
- `Animation` · `Tamil Hindi Dubbed` · `Telugu Hindi Dubbed`

---

## 📌 Important Pages (create in Blogger → Pages)

| Page | URL Slug |
|------|----------|
| About Us | `/p/about.html` |
| Contact Us | `/p/contact.html` |
| Privacy Policy | `/p/privacy-policy.html` |
| Disclaimer | `/p/disclaimer.html` |
| DMCA | `/p/dmca.html` |
| Request Movie | `/p/request.html` |

---

## 🔑 Customisation

Edit these values in `theme.xml` or the generator settings:

- **Site name**: Replace `IBAPAM` with your blog name
- **Telegram**: Replace `@ibapam` with your channel
- **AdSense**: Replace `ca-pub-XXXXXXXXXXXXXXXX` with your publisher ID
- **Social links**: Update URLs in the footer section

---

## ⚠️ Disclaimer

This theme is for informational/educational purposes. Ensure you comply with all applicable laws and TMDB's [Terms of Use](https://www.themoviedb.org/terms-of-use) when using their API.
