/**
 * IBAPAM Theme — TMDB Importer Script
 * Runs only on the TMDB Importer page template.
 * Uses the WordPress REST API to create posts with imported data.
 */
(function () {
    'use strict';

    if (typeof ibapamImporter === 'undefined') return;

    var TMDB_BASE = 'https://api.themoviedb.org/3';
    var IMG_BASE  = 'https://image.tmdb.org/t/p/';
    var nonce    = ibapamImporter.nonce;
    var restUrl  = ibapamImporter.restUrl;          // …/wp/v2
    var ibapamRestUrl = ibapamImporter.ibapamRestUrl; // …/ibapam/v1

    /* ── State ── */
    var searchResults = [];
    var selectedIds   = new Set();
    var postQueue     = [];
    var currentPage   = 1;
    var totalPages    = 1;

    /* ── Utility ── */
    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
    function sleep(ms) { return new Promise(function (r) { setTimeout(r, ms); }); }
    function getVal(id, fb) {
        var el = document.getElementById(id);
        return el ? (el.value.trim() || fb) : fb;
    }
    function getApiKey() {
        var k = getVal('tmdb-api-key', '');
        if (!k) { alert('Please enter your TMDB API key first.'); return null; }
        localStorage.setItem('ibapam_tmdb_key', k);
        return k;
    }

    /* ── Tabs ── */
    window.ibapamSwitchTab = function (name, el) {
        document.querySelectorAll('.importer-panel').forEach(function (p) { p.classList.remove('active'); });
        document.querySelectorAll('.importer-tab').forEach(function (b) { b.classList.remove('active'); });
        var panel = document.getElementById('importer-panel-' + name);
        if (panel) panel.classList.add('active');
        if (el) el.classList.add('active');
    };

    /* ── Progress ── */
    function showProgress(show) {
        var wrap = document.getElementById('import-progress-wrap');
        if (wrap) wrap.style.display = show ? 'block' : 'none';
    }
    function setProgress(done, total) {
        var bar  = document.getElementById('import-progress-bar');
        var txt  = document.getElementById('import-progress-text');
        var pct  = total ? Math.round(done / total * 100) : 0;
        if (bar) bar.style.width = pct + '%';
        if (txt) txt.textContent = done + ' / ' + total + ' (' + pct + '%)';
    }

    /* ── Log ── */
    function log(msg, type) {
        var box = document.getElementById('import-log');
        if (!box) return;
        box.style.display = 'block';
        var t = new Date().toLocaleTimeString();
        box.innerHTML += '<div class="log-' + (type || 'info') + '">' + t + ' — ' + esc(msg) + '</div>';
        box.scrollTop = box.scrollHeight;
    }

    /* ── TMDB Fetch ── */
    async function tmdbFetch(path, params) {
        var key = getApiKey();
        if (!key) throw new Error('No API key');
        var url = new URL(TMDB_BASE + path);
        url.searchParams.set('api_key', key);
        url.searchParams.set('language', getVal('tmdb-lang', 'hi-IN'));
        if (params) {
            Object.keys(params).forEach(function (k) { url.searchParams.set(k, params[k]); });
        }
        var res = await fetch(url.toString());
        if (!res.ok) throw new Error('TMDB error ' + res.status);
        return res.json();
    }

    /* ── Search ── */
    window.ibapamTMDBSearch = async function (page) {
        page = page || 1;
        var q    = getVal('tmdb-search-query', '');
        var year = getVal('tmdb-search-year', '');
        var type = getVal('tmdb-content-type', 'movie');
        if (!q) { alert('Enter a search term.'); return; }
        currentPage = page;
        var endpoint = type === 'multi' ? '/search/multi' : '/search/' + type;
        var params = { query: q, page: page };
        if (year && type === 'movie') params.year = year;
        if (year && type === 'tv')    params.first_air_date_year = year;
        try {
            var data = await tmdbFetch(endpoint, params);
            totalPages = data.total_pages || 1;
            searchResults = page === 1 ? (data.results || []) : searchResults.concat(data.results || []);
            renderSearchResults();
            var lmBtn = document.getElementById('tmdb-load-more');
            if (lmBtn) lmBtn.style.display = currentPage < totalPages ? 'inline-flex' : 'none';
        } catch (e) { alert('Search failed: ' + e.message); }
    };

    window.ibapamTMDBTrending  = async function () { var d = await tmdbFetch('/trending/movie/day'); searchResults = d.results || []; currentPage = 1; totalPages = 1; renderSearchResults(); };
    window.ibapamTMDBPopular   = async function () { var d = await tmdbFetch('/movie/popular');      searchResults = d.results || []; totalPages = d.total_pages || 1; currentPage = 1; renderSearchResults(); };
    window.ibapamTMDBNowPlay   = async function () { var d = await tmdbFetch('/movie/now_playing');  searchResults = d.results || []; totalPages = d.total_pages || 1; currentPage = 1; renderSearchResults(); };
    window.ibapamLoadMore      = function () { ibapamTMDBSearch(currentPage + 1); };

    function renderSearchResults() {
        var grid  = document.getElementById('tmdb-results-grid');
        var card  = document.getElementById('tmdb-results-card');
        var count = document.getElementById('tmdb-result-count');
        if (!grid) return;
        if (card)  card.style.display = 'block';
        if (count) count.textContent = searchResults.length + ' results';
        var type = getVal('tmdb-content-type', 'movie');
        grid.innerHTML = searchResults.map(function (item) {
            var id     = item.id;
            var title  = item.title || item.name || 'Unknown';
            var year   = (item.release_date || item.first_air_date || '').substring(0, 4);
            var poster = item.poster_path ? IMG_BASE + 'w200' + item.poster_path : '';
            var mt     = item.media_type || type;
            var sel    = selectedIds.has(id) ? 'selected' : '';
            var img    = poster ? '<img src="' + esc(poster) + '" alt="' + esc(title) + '" loading="lazy"/>'
                                : '<div style="width:100%;aspect-ratio:2/3;background:#111;display:flex;align-items:center;justify-content:center;color:#555"><i class="fas fa-film"></i></div>';
            return '<div class="importer-result-card ' + sel + '" id="irc_' + id + '" onclick="ibapamToggleSelect(' + id + ',\'' + esc(mt) + '\',\'' + esc(title) + '\')">'
                 + img
                 + '<div class="irc-check"><i class="fas fa-check"></i></div>'
                 + '<div class="irc-body"><div class="irc-title">' + esc(title) + '</div><div class="irc-meta">' + esc(year) + ' · ' + esc(mt) + '</div></div>'
                 + '</div>';
        }).join('');
    }

    window.ibapamToggleSelect = function (id, type, title) {
        var card = document.getElementById('irc_' + id);
        if (selectedIds.has(id)) {
            selectedIds.delete(id);
            if (card) card.classList.remove('selected');
        } else {
            selectedIds.add(id);
            if (card) card.classList.add('selected');
        }
    };
    window.ibapamSelectAll   = function () { searchResults.forEach(function (i) { selectedIds.add(i.id); document.getElementById('irc_' + i.id)?.classList.add('selected'); }); };
    window.ibapamDeselectAll = function () { searchResults.forEach(function (i) { selectedIds.delete(i.id); document.getElementById('irc_' + i.id)?.classList.remove('selected'); }); };

    /* ── Add to Queue ── */
    window.ibapamAddToQueue = function () {
        if (!selectedIds.size) { alert('Select at least one item.'); return; }
        var type = getVal('tmdb-content-type', 'movie');
        var added = 0;
        searchResults.forEach(function (item) {
            if (!selectedIds.has(item.id) || postQueue.find(function (q) { return q.id === item.id; })) return;
            var mt = item.media_type || type;
            postQueue.push({
                id:     item.id,
                type:   mt === 'multi' ? (item.media_type || 'movie') : mt,
                title:  item.title || item.name || 'Unknown',
                year:   (item.release_date || item.first_air_date || '').substring(0, 4),
                poster: item.poster_path ? IMG_BASE + 'w200' + item.poster_path : '',
                status: 'pending',
                wpPostId: null
            });
            added++;
        });
        updateQueueDisplay();
        alert(added + ' item(s) added to queue.');
    };

    /* ── Queue Display ── */
    function updateQueueDisplay() {
        var count = postQueue.length;
        ['queue-count', 'queue-count-badge'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = count;
        });
        var tbody = document.getElementById('import-queue-body');
        if (!tbody) return;
        if (!count) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#888;padding:30px">Queue is empty.</td></tr>';
            return;
        }
        tbody.innerHTML = postQueue.map(function (item, i) {
            var posterHtml = item.poster
                ? '<img src="' + esc(item.poster) + '" alt=""/>'
                : '<div style="width:38px;height:52px;background:#333;border-radius:4px"></div>';
            var actions = '<button class="btn btn-primary btn-sm" onclick="ibapamImportSingle(' + i + ')"><i class="fas fa-magic"></i> Import</button>';
            if (item.wpPostId) {
                actions += ' <a class="btn btn-secondary btn-sm" href="' + esc(restUrl.replace('/wp-json/wp/v2', '')) + '/?p=' + item.wpPostId + '" target="_blank" rel="noopener"><i class="fas fa-eye"></i></a>';
            }
            actions += ' <button class="btn btn-danger btn-sm" onclick="ibapamRemoveFromQueue(' + i + ')"><i class="fas fa-times"></i></button>';
            return '<tr>'
                 + '<td>' + (i + 1) + '</td>'
                 + '<td>' + posterHtml + '</td>'
                 + '<td style="font-weight:600;color:#fff">' + esc(item.title) + '</td>'
                 + '<td>' + esc(item.year) + '</td>'
                 + '<td><span class="status-pill s-pending">' + esc(item.type) + '</span></td>'
                 + '<td><span class="status-pill s-' + esc(item.status) + '">' + esc(item.status) + '</span></td>'
                 + '<td>' + actions + '</td>'
                 + '</tr>';
        }).join('');
    }

    window.ibapamRemoveFromQueue = function (i) { postQueue.splice(i, 1); updateQueueDisplay(); };
    window.ibapamClearQueue      = function () { if (confirm('Clear all items?')) { postQueue = []; updateQueueDisplay(); } };

    /* ── Fetch Full TMDB Details ── */
    async function fetchDetails(type, id) {
        var append = type === 'movie'
            ? 'credits,images,videos,keywords,release_dates'
            : 'credits,images,videos,keywords,content_ratings';
        return tmdbFetch('/' + type + '/' + id, { append_to_response: append });
    }

    /* ── Build WP Post Data ── */
    function buildPostData(data, type) {
        var isMovie = type === 'movie';
        var title   = data.title || data.name || 'Unknown';
        var year    = (data.release_date || data.first_air_date || '').substring(0, 4);
        var rating  = data.vote_average ? data.vote_average.toFixed(1) : 'N/A';
        var genres  = (data.genres || []).map(function (g) { return g.name; }).join(', ') || 'N/A';
        var runtime = isMovie
            ? (data.runtime ? data.runtime + ' min' : 'N/A')
            : (data.episode_run_time && data.episode_run_time[0] ? data.episode_run_time[0] + ' min/ep' : 'N/A');
        var cast    = (data.credits && data.credits.cast ? data.credits.cast : []).slice(0, 8).map(function (c) { return c.name; }).join(', ') || 'N/A';
        var director = isMovie
            ? ((data.credits && data.credits.crew ? data.credits.crew : []).find(function (c) { return c.job === 'Director'; }) || {}).name || 'N/A'
            : (data.created_by || []).map(function (c) { return c.name; }).join(', ') || 'N/A';
        var poster   = data.poster_path   ? IMG_BASE + 'w500'  + data.poster_path  : '';
        var backdrop = data.backdrop_path ? IMG_BASE + 'w1280' + data.backdrop_path : '';
        var screenshots = ((data.images && data.images.backdrops) ? data.images.backdrops : []).slice(0, 6).map(function (b) { return IMG_BASE + 'w780' + b.file_path; });
        var trailer  = ((data.videos && data.videos.results) ? data.videos.results : []).find(function (v) { return v.type === 'Trailer' && v.site === 'YouTube'; });
        var keywords = ((data.keywords && (data.keywords.keywords || data.keywords.results)) || []).slice(0, 14).map(function (k) { return k.name; }).join(', ');
        var seasons  = !isMovie && data.seasons
            ? data.seasons.filter(function (s) { return s.season_number > 0; }).map(function (s) { return 'S' + String(s.season_number).padStart(2, '0') + ' (' + s.episode_count + ' eps)'; }).join(' | ')
            : '';

        /* Settings */
        var siteName   = getVal('imp-site-name',     'IBAPAM');
        var telegram   = getVal('imp-telegram',       '@ibapam');
        var quality    = getVal('imp-quality',        'WEB-DL');
        var audio      = getVal('imp-audio',          'Hindi');
        var dlLink     = getVal('imp-dl-link',        '#');
        var disclaimer = getVal('imp-disclaimer',     '');
        var sizes      = getVal('imp-sizes',          '400MB, 700MB, 1.4GB').split(',').map(function (s) { return s.trim(); });

        var trailerEmbed = trailer
            ? '<div class="trailer-wrap"><h2>Official Trailer</h2><div class="trailer-embed"><iframe src="https://www.youtube.com/embed/' + esc(trailer.key) + '" allowfullscreen loading="lazy"></iframe></div></div>'
            : '';

        var ssGallery = screenshots.length
            ? '<div class="screenshots-wrap"><h2>Screenshots</h2><div class="screenshots-grid">' + screenshots.map(function (s) { return '<img src="' + esc(s) + '" alt="' + esc(title) + ' screenshot" loading="lazy"/>'; }).join('') + '</div></div>'
            : '';

        var dlButtons = sizes.map(function (size) {
            return '<a class="download-link-btn" href="' + esc(dlLink) + '" rel="nofollow" target="_blank">'
                 + '<div class="download-link-btn-left"><i class="fas fa-download"></i> <span>' + esc(title) + ' (' + esc(size) + ') [' + esc(quality) + '] [' + esc(audio) + ']</span></div>'
                 + '<span class="download-size-label">' + esc(size) + '</span></a>';
        }).join('\n');

        var content = ''
            + '<!-- movie-info-box -->\n'
            + '<div class="movie-info-box">\n'
            + (poster ? '  <div class="movie-poster"><img src="' + esc(poster) + '" alt="' + esc(title) + ' Poster" class="movie-poster-img"/></div>\n' : '')
            + '  <div class="movie-details-wrap">\n'
            + '    <table class="movie-table">\n'
            + '      <tr><td>Movie Name</td><td><strong>' + esc(title) + '</strong></td></tr>\n'
            + '      <tr><td>Release Year</td><td>' + esc(year) + '</td></tr>\n'
            + '      <tr><td>Genres</td><td>' + esc(genres) + '</td></tr>\n'
            + '      <tr><td>IMDB Rating</td><td><span class="rating-star"><i class="fas fa-star"></i> ' + esc(rating) + ' / 10</span></td></tr>\n'
            + '      <tr><td>Runtime</td><td>' + esc(runtime) + '</td></tr>\n'
            + '      <tr><td>Language</td><td>' + esc(data.original_language || '').toUpperCase() + '</td></tr>\n'
            + '      <tr><td>' + (isMovie ? 'Director' : 'Creator') + '</td><td>' + esc(director) + '</td></tr>\n'
            + '      <tr><td>Cast</td><td>' + esc(cast) + '</td></tr>\n'
            + (seasons ? '      <tr><td>Seasons</td><td>' + esc(seasons) + '</td></tr>\n' : '')
            + '      <tr><td>Quality</td><td>' + esc(quality) + '</td></tr>\n'
            + '      <tr><td>Audio</td><td>' + esc(audio) + '</td></tr>\n'
            + '      <tr><td>Download On</td><td>' + esc(siteName) + ' | Telegram: ' + esc(telegram) + '</td></tr>\n'
            + '    </table>\n'
            + '    <div class="movie-actions-row"><span class="badge badge-hd">' + esc(quality) + '</span><span class="badge badge-new">' + esc(year) + '</span><span class="rating-star"><i class="fas fa-star"></i> ' + esc(rating) + '</span></div>\n'
            + '  </div>\n'
            + '</div>\n\n'
            + '<!-- synopsis -->\n'
            + '<div class="movie-synopsis"><h2>Story / Synopsis</h2><p>' + esc(data.overview || 'Story coming soon...') + '</p></div>\n\n'
            + trailerEmbed + '\n'
            + ssGallery + '\n'
            + '<!-- downloads -->\n'
            + '<div class="download-section">\n'
            + '  <div class="download-section-title"><i class="fas fa-download"></i> Download ' + esc(title) + ' (' + esc(year) + ') ' + esc(quality) + '</div>\n'
            + '  <div class="download-links-grid">\n'
            + dlButtons + '\n'
            + '  </div>\n'
            + '</div>\n\n'
            + '<!-- note -->\n'
            + '<div class="note-box"><div class="note-box-title">📌 How to Download</div>Click a download button → open the page → download your preferred size. Broken link? Join our Telegram: ' + esc(telegram) + '</div>\n\n'
            + (disclaimer ? '<div class="footer-disclaimer">' + esc(disclaimer) + '</div>\n' : '');

        /* Tags / categories */
        var tagNames = [];
        if (genres) genres.split(', ').forEach(function (g) { tagNames.push(g); });
        if (keywords) keywords.split(', ').forEach(function (k) { tagNames.push(k); });

        var catName = getVal('imp-category', 'Movies');

        return {
            title:   title,
            content: content,
            status:  getVal('imp-post-status', 'draft'),
            tags:    tagNames,
            catName: catName,
            featured_media_url: poster || backdrop,
            meta: {
                tmdb_id:      data.id,
                tmdb_type:    type,
                movie_rating: rating,
                movie_year:   year,
                movie_genres: genres,
                movie_cast:   cast,
                movie_director: director,
                movie_runtime:  runtime
            }
        };
    }

    /* ── Import Single to WP ── */
    window.ibapamImportSingle = async function (i) {
        var item = postQueue[i];
        if (!item) return;
        try {
            item.status = 'importing';
            updateQueueDisplay();
            log('Fetching TMDB details: ' + item.title + '...');
            var details = await fetchDetails(item.type, item.id);
            var postData = buildPostData(details, item.type);
            log('Creating WordPress post...');
            var res = await fetch(restUrl + '/posts', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    title:   postData.title,
                    content: postData.content,
                    status:  postData.status
                })
            });
            if (!res.ok) {
                var err = await res.json();
                throw new Error(err.message || 'REST API error ' + res.status);
            }
            var wpPost = await res.json();
            item.wpPostId = wpPost.id;
            item.status = 'done';
            log('✅ Imported: ' + item.title + ' → Post ID #' + wpPost.id, 'ok');

            /* Set featured image from TMDB poster URL */
            if (postData.featured_media_url) {
                await setFeaturedImageFromUrl(wpPost.id, postData.featured_media_url, postData.title);
            }
        } catch (e) {
            item.status = 'error';
            log('❌ Error: ' + item.title + ' — ' + e.message, 'err');
        }
        updateQueueDisplay();
    };

    /* ── Import All ── */
    window.ibapamImportAll = async function () {
        if (!postQueue.length) { alert('Queue is empty.'); return; }
        showProgress(true);
        var total = postQueue.length;
        var delay = parseInt(getVal('imp-delay', '500'), 10) || 500;
        for (var i = 0; i < postQueue.length; i++) {
            if (postQueue[i].status !== 'done') {
                await ibapamImportSingle(i);
                await sleep(delay);
            }
            setProgress(i + 1, total);
        }
        showProgress(false);
        var done = postQueue.filter(function (q) { return q.status === 'done'; }).length;
        log('🎉 Done! ' + done + ' / ' + total + ' posts imported.', 'ok');
    };

    /* ── Set Featured Image from External URL (sideload) ── */
    async function setFeaturedImageFromUrl(postId, imgUrl, altText) {
        try {
            var res = await fetch(ibapamRestUrl + '/sideload-image', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ post_id: postId, image_url: imgUrl, alt_text: altText })
            });
            if (!res.ok) return;
            log('  → Featured image set for post #' + postId, 'info');
        } catch (e) { /* silent */ }
    }

    /* ── Bulk by IDs ── */
    window.ibapamBulkProcess = async function () {
        var raw = getVal('tmdb-bulk-ids', '');
        if (!raw) { alert('Enter TMDB IDs.'); return; }
        var lines = raw.split(/[\n,]+/).map(function (l) { return l.trim(); }).filter(Boolean);
        showProgress(true);
        var total = lines.length;
        var delay = parseInt(getVal('imp-delay', '500'), 10) || 500;
        for (var i = 0; i < lines.length; i++) {
            var line  = lines[i];
            var parts = line.includes(':') ? line.split(':') : ['movie', line];
            var type  = parts[0].trim().toLowerCase();
            var id    = parseInt(parts[1] ? parts[1].trim() : parts[0].trim(), 10);
            if (isNaN(id)) { log('Invalid ID: ' + line, 'err'); setProgress(i + 1, total); continue; }
            postQueue.push({ id: id, type: type, title: type + ':' + id, year: '', poster: '', status: 'pending', wpPostId: null });
            var qi = postQueue.length - 1;
            await ibapamImportSingle(qi);
            setProgress(i + 1, total);
            await sleep(delay);
        }
        showProgress(false);
        updateQueueDisplay();
    };

    /* ── Save Settings ── */
    window.ibapamSaveImporterSettings = function () {
        ['imp-site-name','imp-telegram','imp-quality','imp-audio','imp-dl-link','imp-category','imp-sizes','imp-disclaimer','imp-post-status','imp-delay','tmdb-lang'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) localStorage.setItem('ibapam_' + id, el.value);
        });
        alert('Settings saved!');
    };

    /* ── Load saved settings ── */
    (function loadSaved() {
        var savedKey = localStorage.getItem('ibapam_tmdb_key');
        if (savedKey) { var kEl = document.getElementById('tmdb-api-key'); if (kEl) kEl.value = savedKey; }
        ['imp-site-name','imp-telegram','imp-quality','imp-audio','imp-dl-link','imp-category','imp-sizes','imp-disclaimer','imp-post-status','imp-delay','tmdb-lang'].forEach(function (id) {
            var v = localStorage.getItem('ibapam_' + id);
            var el = document.getElementById(id);
            if (v && el) el.value = v;
        });
    })();

})();
