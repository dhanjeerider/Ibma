<?php
/**
 * IBAPAM Theme — 404.php
 *
 * @package ibapam
 */

get_header();
?>

<main id="main-content" class="container" style="padding:60px 0">
  <div class="error-404-wrap">
    <div class="error-404-num">404</div>
    <h2><?php esc_html_e( 'Page Not Found', 'ibapam' ); ?></h2>
    <p><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'ibapam' ); ?></p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <i class="fas fa-home"></i> <?php esc_html_e( 'Back to Home', 'ibapam' ); ?>
      </a>
      <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:flex;gap:8px">
        <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search...', 'ibapam' ); ?>" style="padding:10px 16px;background:#1a1a1a;border:1px solid #2a2a2a;border-radius:8px;color:#e0e0e0;font-size:.9rem;outline:none"/>
        <button type="submit" class="btn btn-outline"><i class="fas fa-search"></i></button>
      </form>
    </div>
  </div>
</main>

<?php get_footer(); ?>
