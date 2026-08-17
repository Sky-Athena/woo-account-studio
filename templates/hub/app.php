<?php
defined( 'ABSPATH' ) || exit;
$hub = WCAS_Plugin::instance(); $user = $hub->current_user(); $screen = $hub->endpoint(); $config = $hub->config(); $preset = $hub->preset(); $shell = $preset['shell'] ?? 'rail'; $mobile_more_label = $config['label_more'] ?: __( 'More', 'woo-account-studio' );
?>
<div class="wcas-app wcas-experience-<?php echo esc_attr( $config['experience'] ); ?> wcas-shell-<?php echo esc_attr( $shell ); ?> wcas-appearance-<?php echo esc_attr( $config['appearance'] ); ?> wcas-nav-<?php echo esc_attr( $config['navigation'] ); ?>" dir="<?php echo $hub->interface_is_rtl() ? 'rtl' : 'ltr'; ?>" data-wcas-motion="<?php echo 'yes' === $config['enable_motion'] ? 'on' : 'off'; ?>" data-wcas-theme-hero-selector="<?php echo esc_attr( $config['theme_hero_selector'] ); ?>">
  <aside class="wcas-sidebar">
    <a class="wcas-brand" href="<?php echo esc_url( $hub->account_url() ); ?>"><span class="wcas-brand-mark"><?php echo wp_kses_post( $hub->brand_mark() ); ?></span><span class="wcas-brand-copy"><strong class="wcas-brand-name"><?php echo esc_html( $config['brand_name'] ); ?></strong><small class="wcas-brand-tagline"><?php echo esc_html( $config['brand_tagline'] ); ?></small></span></a>
    <div class="wcas-user-mini"><span class="wcas-avatar"><?php echo esc_html( $hub->initials( $user->display_name ) ); ?></span><div><b><?php echo esc_html( $user->display_name ); ?></b><small><?php echo esc_html__( 'Customer account', 'woo-account-studio' ); ?></small></div></div>
    <nav class="wcas-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'woo-account-studio' ); ?>"><?php foreach ( $hub->nav_items() as $key => $item ) : ?><a class="<?php echo $screen === $key || ( 'view-order' === $screen && 'orders' === $key ) ? 'is-active' : ''; ?>" data-wcas-nav-item="<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $hub->account_url( 'dashboard' === $key ? '' : $key ) ); ?>"><?php echo $hub->icon( $item[1] ); ?><span><?php echo esc_html( $item[0] ); ?></span></a><?php endforeach; ?></nav>
    <?php $hub->render_custom_links( 'sidebar' ); ?>
    <a class="wcas-support-link" href="<?php echo esc_url( $config['support_url'] ?: $hub->account_url() ); ?>"><?php echo $hub->icon( 'help' ); ?><span><?php esc_html_e( 'Help & support', 'woo-account-studio' ); ?></span></a>
    <a class="wcas-logout" href="<?php echo esc_url( wc_logout_url() ); ?>"><?php esc_html_e( 'Sign out', 'woo-account-studio' ); ?></a>
  </aside>
  <main class="wcas-main"><div class="wcas-topbar"><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'woo-account-studio' ); ?> <?php echo $hub->icon( 'chevron', 16 ); ?></a><span><?php echo esc_html( $config['brand_name'] ); ?></span></div><nav class="wcas-template-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'woo-account-studio' ); ?>"><?php foreach ( $hub->nav_items() as $key => $item ) : ?><a class="<?php echo $screen === $key || ( 'view-order' === $screen && 'orders' === $key ) ? 'is-active' : ''; ?>" data-wcas-nav-item="<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $hub->account_url( 'dashboard' === $key ? '' : $key ) ); ?>"><?php echo $hub->icon( $item[1], 16 ); ?><span><?php echo esc_html( $item[0] ); ?></span></a><?php endforeach; ?></nav><div class="wcas-screen"><?php $hub->render_screen( $screen ); ?></div></main>
  <nav class="wcas-mobile-nav" aria-label="<?php esc_attr_e( 'Mobile account navigation', 'woo-account-studio' ); ?>">
    <?php foreach ( $hub->mobile_nav_items() as $key => $item ) : ?>
      <a class="<?php echo $screen === $key || ( 'view-order' === $screen && 'orders' === $key ) ? 'is-active' : ''; ?>" data-wcas-mobile-item="<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( $item[0] ); ?>" href="<?php echo esc_url( $hub->account_url( 'dashboard' === $key ? '' : $key ) ); ?>"><?php echo $hub->icon( $item[1] ); ?><small><?php echo esc_html( $item[0] ); ?></small></a>
    <?php endforeach; ?>
    <button class="wcas-mobile-more-trigger" data-wcas-mobile-item="more" type="button" aria-label="<?php echo esc_attr( $mobile_more_label ); ?>" aria-expanded="false" aria-controls="wcas-mobile-more-sheet"><?php echo $hub->icon( $hub->mobile_icon( 'more', 'user' ) ); ?><small><?php echo esc_html( $mobile_more_label ); ?></small></button>
  </nav>
  <?php $hub->mobile_more_sheet(); ?>
</div>
