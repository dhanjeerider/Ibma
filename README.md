# IBAPAM — WordPress Movie Theme

A full-featured **WordPress theme** for movie/entertainment websites — matching the design of `vagam4u.com.in` and `ibapam.in` — complete with TMDB Bulk Post Importer, hero slider, mega-menu, movie cards, download section, and more.

---

## 📁 Repository Files

| File / Folder | Description |
|---------------|-------------|
| `ibapam-theme/` | ✅ **WordPress theme** — upload to `wp-content/themes/` |
| `theme.xml` | Blogger XML theme (legacy) |
| `tmdb-post-generator.html` | Standalone TMDB generator (no server needed) |
| `static-pages.html` | Ready HTML for About, Contact, Privacy, DMCA, Request pages |

---

## 🎨 WordPress Theme Features (`ibapam-theme/`)

### Design
- **Dark movie-site design** — same classes, HTML, CSS as `ibapam.in` / `vagam4u.com.in`
- **CSS Variables** — easy color/font customization
- **Responsive** — mobile, tablet, desktop (breakpoints: 1200/992/768/480px)
- **Smooth animations** — card hover effects, hero slider, fade-in

### Header & Navigation
- **Sticky header** — logo, search bar, Request button, hamburger (mobile)
- **Primary navbar** — full dropdown menus
- **Mobile slide-in drawer** — with sub-menu support
- **Live ticker** — latest posts auto-scrolling

### Homepage
- **Hero slider** — auto-playing 6-post slider with dot controls + swipe support
- **Filter tabs** — categories as tabs above the grid
- **Posts grid** — responsive 4-column movie card grid
- **Sidebar** — search, ad slot, categories, popular posts, tags

### Movie Cards
- Poster thumbnail (2:3 ratio)
- Quality badge (HDRip, WEB-DL, BluRay, 4K…)
- Rating badge (IMDB stars)
- Year badge
- Play button hover overlay

### Single Post (Movie Page)
- Breadcrumb navigation
- Full movie info table (year, genres, rating, runtime, director, cast, quality, audio)
- Poster image + details panel side-by-side
- Synopsis section
- Trailer embed (YouTube)
- Screenshots gallery (with lightbox)
- **Download section** with multiple quality/size buttons
- "How to Download" note box
- Share buttons (Telegram, WhatsApp, Facebook, Twitter)
- Tags row
- Related posts grid

### TMDB Importer (Admin Page)
- Search by title, year, content type (movie / TV / multi)
- Browse trending, popular, now-playing
- Bulk import by TMDB IDs (`movie:12345` or `tv:67890`)
- Import queue with per-item controls
- Full post generation: info table, synopsis, trailer embed, screenshots, download buttons
- Automatic featured image sideload from TMDB CDN
- Customizable settings: quality, audio, sizes, Telegram, disclaimer
- Import delay control (avoid TMDB rate limits)
- Settings persisted in localStorage

### SEO
- JSON-LD Movie schema for structured data
- Custom title tags: `Movie Name [Year] [Quality] Download`
- Clean permalink structure support

### Admin / Backend
- Movie details meta box in post editor
- Customizer options: header, footer, social links, TMDB defaults
- Custom image sizes: poster, card, hero, thumbnail, sidebar card
- 4 sidebar areas: Main + 3 Footer columns
- 4 nav menu locations: Primary, Mobile, Footer-Categories, Footer-Pages

---

## 🚀 Installation

### Method 1 — Upload ZIP
```
ibapam-theme/ → Compress to ibapam-theme.zip
WordPress Dashboard → Appearance → Themes → Add New → Upload Theme
```

### Method 2 — FTP / File Manager
```
Upload ibapam-theme/ folder to: wp-content/themes/ibapam-theme/
WordPress Dashboard → Appearance → Themes → Activate IBAPAM
```

---

## ⚙️ Initial Setup (after activation)

### 1. Assign Navigation Menus
`Appearance → Menus` → Create menus and assign to:
- **Primary Navigation** (desktop navbar)
- **Mobile Navigation** (mobile drawer)
- **Footer Categories**
- **Footer Pages**

Suggested Primary menu items:
- Home
- Bollywood Movies (category)
- Hollywood Movies (category)
- South Hindi Dubbed (category)
- Web Series → (sub: Netflix, Amazon Prime, Hotstar, SonyLIV)
- Hollywood Hindi Dubbed (category)
- Animation (category)
- Request (page)

### 2. Set Social Links
`Appearance → Customize → IBAPAM Theme Options → Footer`
- Add Telegram, YouTube, Instagram, Facebook, Twitter URLs

### 3. Configure TMDB Defaults
`Appearance → Customize → IBAPAM Theme Options → TMDB Importer Defaults`
- Default quality, audio, download URL, sizes, Telegram channel

### 4. Set Site Identity
`Appearance → Customize → Site Identity`
- Upload your logo (recommended: 200×60px, PNG transparent)
- Set tagline

### 5. Create Static Pages
Go to `Pages → Add New` for each:

| Page | Slug |
|------|------|
| About Us | `/about` |
| Contact Us | `/contact` |
| Privacy Policy | `/privacy-policy` |
| Disclaimer | `/disclaimer` |
| DMCA | `/dmca` |
| Request Movie | `/request` |

Copy HTML from `static-pages.html` → paste in each page (HTML editor mode).

### 6. Set Reading Settings
`Settings → Reading`:
- Set homepage to show latest posts (or create a static front page)
- Posts per page: 24

### 7. Install TMDB Importer Page
`Pages → Add New`:
- Title: **TMDB Importer**
- Page Template: **TMDB Importer** (in sidebar)
- Publish → Visit page (you must be logged in as Admin/Editor)

---

## 🎬 Using the TMDB Importer

1. Get a free API key at [themoviedb.org/settings/api](https://www.themoviedb.org/settings/api)
2. Go to your **TMDB Importer** page (must be logged in)
3. Enter your API key in **TMDB API Settings**
4. **Search & Browse** tab → search for movies → select → **Add to Queue**
5. **Bulk by IDs** tab → paste TMDB IDs (e.g. `movie:872585`) → **Import All**
6. **Post Settings** tab → set quality, audio, download URL, sizes, disclaimer
7. **Import Queue** tab → **Import All** or import one-by-one
8. After import: edit each post to add real download links in the **Movie Details** meta box

### After Import — Add Download Links
Each imported post has a **Movie Details** meta box. In **Download Links** field, add JSON:
```json
[
  {"label": "Movie Name 480p", "url": "https://t.me/...", "size": "400MB", "quality": "WEB-DL"},
  {"label": "Movie Name 720p", "url": "https://t.me/...", "size": "700MB", "quality": "WEB-DL"},
  {"label": "Movie Name 1080p", "url": "https://t.me/...", "size": "1.4GB", "quality": "WEB-DL"}
]
```

---

## 📋 Recommended Label / Category Setup

```
Bollywood Movies        Hollywood Movies         South Hindi Dubbed
Hollywood Hindi Dubbed  Hindi Web Series         English Web Series
Netflix                 Amazon Prime             Disney Hotstar
SonyLIV                 ZEE5                     Animation
Tamil Hindi Dubbed      Telugu Hindi Dubbed      Punjabi Movies
```

---

## 🔑 Theme File Structure

```
ibapam-theme/
├── style.css                    ← Theme metadata
├── functions.php                ← Setup, enqueue, meta, REST API, helpers
├── index.php                    ← Homepage (hero + filter tabs + grid)
├── single.php                   ← Single movie post
├── archive.php                  ← Category / tag archives
├── search.php                   ← Search results
├── page.php                     ← Static pages
├── sidebar.php                  ← Sidebar (search, categories, popular, tags)
├── header.php                   ← Sticky header + navbar + ticker + mobile nav
├── footer.php                   ← 4-column footer + social + back-to-top
├── 404.php                      ← 404 error page
├── assets/
│   ├── css/main.css             ← All styles (dark theme, cards, slider...)
│   ├── js/main.js               ← Slider, mobile nav, lightbox, back-to-top
│   ├── js/tmdb-importer.js      ← TMDB importer logic (REST API + post generation)
│   └── img/no-poster.svg        ← Fallback poster image
├── template-parts/
│   ├── content-card.php         ← Movie card (used in grids)
│   └── content-single.php       ← Single post full layout
└── page-templates/
    └── tmdb-importer.php        ← TMDB Importer page template
```

---

## ⚠️ Disclaimer

This theme is for informational/educational purposes. Ensure you comply with all applicable laws and TMDB's [Terms of Use](https://www.themoviedb.org/terms-of-use) when using their API.

---

## 📖 Blogger Theme (Legacy)

The original Blogger XML theme is in `theme.xml`. To install:
1. Blogger Dashboard → Theme → Customise → Restore → Upload `theme.xml`


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
