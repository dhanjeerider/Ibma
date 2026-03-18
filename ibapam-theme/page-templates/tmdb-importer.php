<?php
/**
 * IBAPAM Theme — TMDB Importer Page Template
 *
 * Template Name: TMDB Importer
 * Template Post Type: page
 *
 * How to use:
 * 1. Create a new Page in WordPress
 * 2. Set the Page Template to "TMDB Importer"
 * 3. Publish the page
 * 4. Visit the page (must be logged in as admin/editor)
 *
 * @package ibapam
 */

/* Access check — only users who can publish posts AND upload media */
if ( ! is_user_logged_in() || ! current_user_can( 'publish_posts' ) || ! current_user_can( 'upload_files' ) ) {
	auth_redirect();
	exit;
}

/* Enqueue importer JS */
add_action( 'wp_footer', function () {
	wp_enqueue_script(
		'ibapam-tmdb-importer',
		get_template_directory_uri() . '/assets/js/tmdb-importer.js',
		[],
		IBAPAM_VERSION,
		true
	);
	wp_localize_script( 'ibapam-tmdb-importer', 'ibapamImporter', [
		'nonce'         => wp_create_nonce( 'wp_rest' ),
		'restUrl'       => esc_url_raw( rest_url( 'wp/v2' ) ),
		'ibapamRestUrl' => esc_url_raw( rest_url( 'ibapam/v1' ) ),
		'siteUrl'       => esc_url( home_url() ),
		/* Default values from Customizer */
		'defaults' => [
			'quality'  => get_theme_mod( 'ibapam_default_quality',  'WEB-DL' ),
			'audio'    => get_theme_mod( 'ibapam_default_audio',     'Hindi' ),
			'dl_link'  => get_theme_mod( 'ibapam_default_dl_link',   '#' ),
			'sizes'    => get_theme_mod( 'ibapam_default_sizes',     '400MB, 700MB, 1.4GB, 2.8GB' ),
			'telegram' => get_theme_mod( 'ibapam_telegram_channel',  '@ibapam' ),
			'siteName' => get_bloginfo( 'name' ),
		],
	] );
} );

get_header();
?>

<main id="main-content" class="container" style="padding-top:20px;padding-bottom:40px">

  <div class="tmdb-importer-wrap">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;flex-wrap:wrap">
      <div>
        <h1 style="font-family:'Oswald',sans-serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:4px">
          <i class="fas fa-magic" style="color:var(--primary)"></i>
          <?php esc_html_e( 'TMDB Bulk Post Importer', 'ibapam' ); ?>
        </h1>
        <p style="color:var(--text-muted);font-size:.88rem">
          <?php esc_html_e( 'Search TMDB, select movies/series, and auto-generate complete WordPress posts with poster, synopsis, trailer, screenshots &amp; download section.', 'ibapam' ); ?>
        </p>
      </div>
    </div>

    <!-- API Key + Language -->
    <div class="importer-card">
      <div class="importer-card-title"><i class="fas fa-key"></i> <?php esc_html_e( 'TMDB API Settings', 'ibapam' ); ?></div>
      <div class="importer-form-row">
        <div class="importer-field">
          <label for="tmdb-api-key"><?php esc_html_e( 'TMDB API Key (v3 auth) *', 'ibapam' ); ?></label>
          <input type="password" id="tmdb-api-key" placeholder="<?php esc_attr_e( 'Enter your TMDB API key...', 'ibapam' ); ?>" autocomplete="off"/>
          <small style="color:var(--text-muted);font-size:.78rem">
            <?php printf( esc_html__( 'Get a free key at %s', 'ibapam' ), '<a href="https://www.themoviedb.org/settings/api" target="_blank" rel="noopener noreferrer">themoviedb.org</a>' ); ?>
          </small>
        </div>
        <div class="importer-field">
          <label for="tmdb-lang"><?php esc_html_e( 'Language Code', 'ibapam' ); ?></label>
          <select id="tmdb-lang">
            <option value="hi-IN">Hindi (hi-IN)</option>
            <option value="en-US">English (en-US)</option>
            <option value="te-IN">Telugu (te-IN)</option>
            <option value="ta-IN">Tamil (ta-IN)</option>
            <option value="ml-IN">Malayalam (ml-IN)</option>
            <option value="kn-IN">Kannada (kn-IN)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Main Tabs -->
    <div class="importer-tabs" role="tablist">
      <button class="importer-tab active" onclick="ibapamSwitchTab('search',this)" role="tab" aria-selected="true">
        <i class="fas fa-search"></i> <?php esc_html_e( 'Search &amp; Browse', 'ibapam' ); ?>
      </button>
      <button class="importer-tab" onclick="ibapamSwitchTab('bulk',this)" role="tab" aria-selected="false">
        <i class="fas fa-list"></i> <?php esc_html_e( 'Bulk by IDs', 'ibapam' ); ?>
      </button>
      <button class="importer-tab" onclick="ibapamSwitchTab('queue',this)" role="tab" aria-selected="false">
        <i class="fas fa-tasks"></i> <?php esc_html_e( 'Import Queue', 'ibapam' ); ?>
        <span id="queue-count-badge" style="background:var(--primary);color:#fff;padding:1px 7px;border-radius:10px;font-size:.74rem;margin-left:5px">0</span>
      </button>
      <button class="importer-tab" onclick="ibapamSwitchTab('settings',this)" role="tab" aria-selected="false">
        <i class="fas fa-cog"></i> <?php esc_html_e( 'Post Settings', 'ibapam' ); ?>
      </button>
    </div>

    <!-- ── Tab: Search & Browse ── -->
    <div class="importer-panel active" id="importer-panel-search">

      <div class="importer-card">
        <div class="importer-card-title"><i class="fas fa-search"></i> <?php esc_html_e( 'Search TMDB', 'ibapam' ); ?></div>

        <div class="importer-form-row cols-3">
          <div class="importer-field" style="grid-column:span 2">
            <label for="tmdb-search-query"><?php esc_html_e( 'Movie / Series Title', 'ibapam' ); ?></label>
            <input type="text" id="tmdb-search-query" placeholder="<?php esc_attr_e( 'e.g. Jawan, Kalki 2898 AD, Mirzapur...', 'ibapam' ); ?>"
                   onkeydown="if(event.key==='Enter'){ibapamTMDBSearch(1)}"/>
          </div>
          <div class="importer-field">
            <label for="tmdb-search-year"><?php esc_html_e( 'Year (optional)', 'ibapam' ); ?></label>
            <input type="text" id="tmdb-search-year" placeholder="2024" maxlength="4"/>
          </div>
        </div>

        <div class="importer-form-row">
          <div class="importer-field">
            <label for="tmdb-content-type"><?php esc_html_e( 'Content Type', 'ibapam' ); ?></label>
            <select id="tmdb-content-type">
              <option value="movie">🎬 Movie</option>
              <option value="tv">📺 TV / Web Series</option>
              <option value="multi">🔍 Multi (auto-detect)</option>
            </select>
          </div>
        </div>

        <div class="importer-btn-group">
          <button class="btn btn-primary" onclick="ibapamTMDBSearch(1)">
            <i class="fas fa-search"></i> <?php esc_html_e( 'Search', 'ibapam' ); ?>
          </button>
          <button class="btn btn-outline" onclick="ibapamTMDBTrending()">
            <i class="fas fa-fire"></i> <?php esc_html_e( 'Trending Today', 'ibapam' ); ?>
          </button>
          <button class="btn btn-outline" onclick="ibapamTMDBPopular()">
            <i class="fas fa-star"></i> <?php esc_html_e( 'Popular', 'ibapam' ); ?>
          </button>
          <button class="btn btn-outline" onclick="ibapamTMDBNowPlay()">
            <i class="fas fa-play"></i> <?php esc_html_e( 'Now Playing', 'ibapam' ); ?>
          </button>
        </div>
      </div>

      <!-- Results -->
      <div class="importer-card" id="tmdb-results-card" style="display:none">
        <div class="importer-card-title" style="justify-content:space-between">
          <span><i class="fas fa-film"></i> <?php esc_html_e( 'Results', 'ibapam' ); ?> <small id="tmdb-result-count" style="font-size:.78rem;color:var(--text-muted);font-weight:400"></small></span>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn btn-outline" onclick="ibapamSelectAll()" style="padding:5px 12px;font-size:.8rem"><i class="fas fa-check-double"></i> <?php esc_html_e( 'All', 'ibapam' ); ?></button>
            <button class="btn btn-outline" onclick="ibapamDeselectAll()" style="padding:5px 12px;font-size:.8rem"><i class="fas fa-times"></i> <?php esc_html_e( 'None', 'ibapam' ); ?></button>
            <button class="btn btn-primary" onclick="ibapamAddToQueue()" style="padding:5px 14px;font-size:.8rem"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add Selected to Queue', 'ibapam' ); ?></button>
          </div>
        </div>
        <div class="importer-results-grid" id="tmdb-results-grid"></div>
        <div style="text-align:center;margin-top:14px">
          <button class="btn btn-outline" id="tmdb-load-more" onclick="ibapamLoadMore()" style="display:none">
            <i class="fas fa-plus"></i> <?php esc_html_e( 'Load More', 'ibapam' ); ?>
          </button>
        </div>
      </div>

    </div><!-- /#importer-panel-search -->

    <!-- ── Tab: Bulk by IDs ── -->
    <div class="importer-panel" id="importer-panel-bulk">
      <div class="importer-card">
        <div class="importer-card-title"><i class="fas fa-list"></i> <?php esc_html_e( 'Bulk Import by TMDB IDs', 'ibapam' ); ?></div>
        <p style="color:var(--text-muted);font-size:.88rem;margin-bottom:14px">
          <?php esc_html_e( 'Paste TMDB IDs one per line. Format: ', 'ibapam' ); ?>
          <code style="background:#111;padding:2px 6px;border-radius:4px;font-size:.82rem">movie:12345</code>
          <?php esc_html_e( ' or just ', 'ibapam' ); ?>
          <code style="background:#111;padding:2px 6px;border-radius:4px;font-size:.82rem">12345</code>
          <?php esc_html_e( ' for movies, ', 'ibapam' ); ?>
          <code style="background:#111;padding:2px 6px;border-radius:4px;font-size:.82rem">tv:67890</code>
          <?php esc_html_e( ' for TV series.', 'ibapam' ); ?>
        </p>
        <div class="importer-field">
          <label for="tmdb-bulk-ids"><?php esc_html_e( 'TMDB IDs (one per line or comma-separated)', 'ibapam' ); ?></label>
          <textarea id="tmdb-bulk-ids" rows="10" placeholder="movie:872585&#10;movie:762430&#10;tv:94997&#10;tv:71712&#10;..."><?php ?></textarea>
        </div>
        <div class="importer-btn-group">
          <button class="btn btn-primary" onclick="ibapamBulkProcess()">
            <i class="fas fa-magic"></i> <?php esc_html_e( 'Import All', 'ibapam' ); ?>
          </button>
        </div>

        <!-- Progress -->
        <div class="import-progress-wrap" id="import-progress-wrap">
          <div class="import-progress-outer">
            <div class="import-progress-inner" id="import-progress-bar"></div>
          </div>
          <div class="import-progress-info">
            <span id="import-progress-text">0 / 0</span>
            <span><?php esc_html_e( 'Processing...', 'ibapam' ); ?></span>
          </div>
        </div>
        <div class="import-log" id="import-log"></div>
      </div>
    </div><!-- /#importer-panel-bulk -->

    <!-- ── Tab: Queue ── -->
    <div class="importer-panel" id="importer-panel-queue">
      <div class="importer-card">
        <div class="importer-card-title" style="justify-content:space-between">
          <span><i class="fas fa-tasks"></i> <?php esc_html_e( 'Import Queue', 'ibapam' ); ?> (<span id="queue-count">0</span>)</span>
          <div style="display:flex;gap:8px">
            <button class="btn btn-primary" onclick="ibapamImportAll()">
              <i class="fas fa-magic"></i> <?php esc_html_e( 'Import All', 'ibapam' ); ?>
            </button>
            <button class="btn btn-outline" onclick="ibapamClearQueue()" style="border-color:#e74c3c;color:#e74c3c">
              <i class="fas fa-trash"></i> <?php esc_html_e( 'Clear', 'ibapam' ); ?>
            </button>
          </div>
        </div>

        <!-- Progress -->
        <div class="import-progress-wrap" id="import-progress-wrap">
          <div class="import-progress-outer"><div class="import-progress-inner" id="import-progress-bar"></div></div>
          <div class="import-progress-info"><span id="import-progress-text">0 / 0</span><span><?php esc_html_e( 'Processing...', 'ibapam' ); ?></span></div>
        </div>
        <div class="import-log" id="import-log"></div>

        <div style="overflow-x:auto;margin-top:14px">
          <table class="queue-tbl">
            <thead>
              <tr>
                <th>#</th>
                <th><?php esc_html_e( 'Poster', 'ibapam' ); ?></th>
                <th><?php esc_html_e( 'Title', 'ibapam' ); ?></th>
                <th><?php esc_html_e( 'Year', 'ibapam' ); ?></th>
                <th><?php esc_html_e( 'Type', 'ibapam' ); ?></th>
                <th><?php esc_html_e( 'Status', 'ibapam' ); ?></th>
                <th><?php esc_html_e( 'Actions', 'ibapam' ); ?></th>
              </tr>
            </thead>
            <tbody id="import-queue-body">
              <tr><td colspan="7" style="text-align:center;color:#888;padding:30px"><?php esc_html_e( 'Queue is empty. Search &amp; add items first.', 'ibapam' ); ?></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- /#importer-panel-queue -->

    <!-- ── Tab: Post Settings ── -->
    <div class="importer-panel" id="importer-panel-settings">
      <div class="importer-card">
        <div class="importer-card-title"><i class="fas fa-cog"></i> <?php esc_html_e( 'Post Generation Settings', 'ibapam' ); ?></div>

        <div class="importer-form-row">
          <div class="importer-field">
            <label for="imp-site-name"><?php esc_html_e( 'Site Name', 'ibapam' ); ?></label>
            <input type="text" id="imp-site-name" value="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
          </div>
          <div class="importer-field">
            <label for="imp-telegram"><?php esc_html_e( 'Telegram Channel', 'ibapam' ); ?></label>
            <input type="text" id="imp-telegram" value="<?php echo esc_attr( get_theme_mod( 'ibapam_telegram_channel', '@ibapam' ) ); ?>" placeholder="@yourchannel"/>
          </div>
        </div>

        <div class="importer-form-row">
          <div class="importer-field">
            <label for="imp-quality"><?php esc_html_e( 'Default Quality', 'ibapam' ); ?></label>
            <select id="imp-quality">
              <option value="WEB-DL">WEB-DL</option>
              <option value="HDRip">HDRip</option>
              <option value="BluRay">BluRay</option>
              <option value="WEBRip">WEBRip</option>
              <option value="DVDRip">DVDRip</option>
              <option value="CAMRip">CAMRip</option>
              <option value="4K">4K UHD</option>
            </select>
          </div>
          <div class="importer-field">
            <label for="imp-audio"><?php esc_html_e( 'Default Audio', 'ibapam' ); ?></label>
            <input type="text" id="imp-audio" value="Hindi" placeholder="Hindi, English, Hindi + English"/>
          </div>
        </div>

        <div class="importer-form-row">
          <div class="importer-field">
            <label for="imp-dl-link"><?php esc_html_e( 'Default Download URL', 'ibapam' ); ?></label>
            <input type="url" id="imp-dl-link" placeholder="https://t.me/yourchannel" value="<?php echo esc_attr( get_theme_mod( 'ibapam_default_dl_link', '#' ) ); ?>"/>
          </div>
          <div class="importer-field">
            <label for="imp-sizes"><?php esc_html_e( 'Download Sizes (comma separated)', 'ibapam' ); ?></label>
            <input type="text" id="imp-sizes" value="<?php echo esc_attr( get_theme_mod( 'ibapam_default_sizes', '400MB, 700MB, 1.4GB, 2.8GB' ) ); ?>" placeholder="400MB, 700MB, 1.4GB, 2.8GB"/>
          </div>
        </div>

        <div class="importer-form-row">
          <div class="importer-field">
            <label for="imp-post-status"><?php esc_html_e( 'Post Status', 'ibapam' ); ?></label>
            <select id="imp-post-status">
              <option value="draft">Draft</option>
              <option value="publish">Publish</option>
              <option value="pending">Pending Review</option>
            </select>
          </div>
          <div class="importer-field">
            <label for="imp-category"><?php esc_html_e( 'Default Category', 'ibapam' ); ?></label>
            <select id="imp-category">
              <?php
                $all_cats = get_categories( [ 'hide_empty' => false ] );
                foreach ( $all_cats as $c ) {
                    printf( '<option value="%s">%s</option>', esc_attr( $c->name ), esc_html( $c->name ) );
                }
              ?>
            </select>
          </div>
        </div>

        <div class="importer-form-row">
          <div class="importer-field">
            <label for="imp-delay"><?php esc_html_e( 'Delay between imports (ms)', 'ibapam' ); ?></label>
            <input type="number" id="imp-delay" value="500" min="200" max="5000" step="100"/>
            <small style="color:var(--text-muted);font-size:.78rem"><?php esc_html_e( 'Increase if you hit TMDB rate limits.', 'ibapam' ); ?></small>
          </div>
        </div>

        <div class="importer-form-row cols-1">
          <div class="importer-field">
            <label for="imp-disclaimer"><?php esc_html_e( 'Post Disclaimer Text', 'ibapam' ); ?></label>
            <textarea id="imp-disclaimer" rows="3" placeholder="<?php esc_attr_e( 'Disclaimer: We do not host any files...', 'ibapam' ); ?>"><?php echo esc_textarea( get_theme_mod( 'ibapam_footer_disclaimer', '' ) ); ?></textarea>
          </div>
        </div>

        <div class="importer-btn-group">
          <button class="btn btn-primary" onclick="ibapamSaveImporterSettings()">
            <i class="fas fa-save"></i> <?php esc_html_e( 'Save Settings', 'ibapam' ); ?>
          </button>
        </div>
        <p style="color:var(--text-muted);font-size:.78rem;margin-top:10px">
          <i class="fas fa-info-circle"></i> <?php esc_html_e( 'Settings are saved in your browser (localStorage). They persist between sessions on this device.', 'ibapam' ); ?>
        </p>
      </div>
    </div><!-- /#importer-panel-settings -->

  </div><!-- /.tmdb-importer-wrap -->
</main>

<?php get_footer(); ?>
