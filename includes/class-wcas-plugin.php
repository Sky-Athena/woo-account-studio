<?php
defined( 'ABSPATH' ) || exit;

/** Core application shell for the customer hub. */
final class WCAS_Plugin {
	private static $instance;
	private $option_key = 'wcas_settings';

	public static function instance() {
		if ( ! self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_filter( 'option_page_capability_wcas_settings_group', array( $this, 'settings_capability' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		add_filter( 'woocommerce_locate_template', array( $this, 'template' ), 30, 3 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'gettext', array( $this, 'builtin_arabic' ), 20, 3 );
	}

	public static function activate() { flush_rewrite_rules(); }
	public static function deactivate() { flush_rewrite_rules(); }
	public function i18n() { load_plugin_textdomain( 'woo-account-studio', false, dirname( plugin_basename( WCAS_FILE ) ) . '/languages' ); }
	public function defaults() {
		return array(
			'enabled' => 'yes', 'experience' => 'commerce', 'language_mode' => 'auto', 'appearance' => 'light', 'mobile_nav' => 'dock',
			'mobile_nav_style' => 'glass', 'mobile_nav_labels' => 'yes', 'mobile_icon_style' => 'line', 'hide_page_title' => 'no',
			'mobile_icon_dashboard' => 'grid', 'mobile_icon_orders' => 'bag', 'mobile_icon_library' => 'download', 'mobile_icon_addresses' => 'pin', 'mobile_icon_profile' => 'user', 'mobile_icon_more' => 'user',
			'logo_source' => 'site', 'logo_id' => 0, 'brand_name' => get_bloginfo( 'name' ), 'brand_tagline' => '',
			'primary' => '#5e5ce6', 'accent' => '#ff785a', 'background' => '#f5f6fa', 'surface' => '#ffffff',
			'text' => '#172036', 'sidebar' => '#101523', 'radius' => '22', 'content_width' => '1060', 'font_scale' => '100',
			'navigation' => 'sidebar', 'support_url' => '', 'support_label' => '', 'hero_title' => '', 'hero_description' => '',
			'show_order_focus' => 'yes', 'show_action_cards' => 'yes', 'show_rewards' => 'yes', 'show_recommendations' => 'yes', 'show_template_story' => 'yes',
			'enable_motion' => 'yes', 'custom_css' => '',
			'label_dashboard' => '', 'label_orders' => '', 'label_library' => '', 'label_addresses' => '', 'label_profile' => '', 'label_more' => '',
		);
	}
	public function config() {
		$config = wp_parse_args( (array) get_option( $this->option_key, array() ), $this->defaults() );
		$preview_template = $this->preview_template();
		if ( $preview_template ) $config['experience'] = $preview_template;
		return $config;
	}
	public function active() { $s = $this->config(); return 'yes' === $s['enabled']; }
	public function presets() {
		return array(
			'commerce' => array( 'name' => __( 'Commerce Hub', 'woo-account-studio' ), 'tag' => __( 'Orders first', 'woo-account-studio' ), 'description' => __( 'A post-purchase command centre for everyday stores.', 'woo-account-studio' ), 'shell' => 'rail', 'primary' => '#5e5ce6', 'accent' => '#ff785a', 'background' => '#f5f6fa', 'surface' => '#ffffff', 'text' => '#172036', 'sidebar' => '#101523' ),
			'luxury' => array( 'name' => __( 'Luxury Concierge', 'woo-account-studio' ), 'tag' => __( 'Relationship led', 'woo-account-studio' ), 'description' => __( 'A high-touch account experience with concierge support.', 'woo-account-studio' ), 'shell' => 'editorial', 'primary' => '#a77942', 'accent' => '#e7c379', 'background' => '#f7f4ef', 'surface' => '#fffdf9', 'text' => '#261d17', 'sidebar' => '#17120e' ),
			'digital' => array( 'name' => __( 'Digital Library', 'woo-account-studio' ), 'tag' => __( 'Content led', 'woo-account-studio' ), 'description' => __( 'Bring downloads, courses and licences to the foreground.', 'woo-account-studio' ), 'shell' => 'tabs', 'primary' => '#147c72', 'accent' => '#50c5a4', 'background' => '#eff9f7', 'surface' => '#ffffff', 'text' => '#143b3b', 'sidebar' => '#103632' ),
			'subscription' => array( 'name' => __( 'Subscription Club', 'woo-account-studio' ), 'tag' => __( 'Retention led', 'woo-account-studio' ), 'description' => __( 'A member-style home for recurring purchases and benefits.', 'woo-account-studio' ), 'shell' => 'club', 'primary' => '#7b4ee2', 'accent' => '#ed84b4', 'background' => '#f8f4ff', 'surface' => '#ffffff', 'text' => '#2d1d52', 'sidebar' => '#211541' ),
			'service' => array( 'name' => __( 'Service & Booking', 'woo-account-studio' ), 'tag' => __( 'Service led', 'woo-account-studio' ), 'description' => __( 'A calm control centre for appointments and service orders.', 'woo-account-studio' ), 'shell' => 'console', 'primary' => '#187c9b', 'accent' => '#63c4cc', 'background' => '#f1fafb', 'surface' => '#ffffff', 'text' => '#143642', 'sidebar' => '#113b49' ),
			'beauty' => array( 'name' => __( 'Beauty & Wellness', 'woo-account-studio' ), 'tag' => __( 'Personal led', 'woo-account-studio' ), 'description' => __( 'A warm, editorial account for beauty and wellness brands.', 'woo-account-studio' ), 'shell' => 'editorial', 'primary' => '#c25679', 'accent' => '#e9aaad', 'background' => '#fff7f8', 'surface' => '#ffffff', 'text' => '#472130', 'sidebar' => '#421c2b' ),
			'grocery' => array( 'name' => __( 'Quick Reorder', 'woo-account-studio' ), 'tag' => __( 'Convenience led', 'woo-account-studio' ), 'description' => __( 'A practical account optimised for repeat orders and delivery.', 'woo-account-studio' ), 'shell' => 'rail', 'primary' => '#287c4c', 'accent' => '#8ec55b', 'background' => '#f4faf1', 'surface' => '#ffffff', 'text' => '#203b2a', 'sidebar' => '#173d27' ),
			'wholesale' => array( 'name' => __( 'Trade Portal', 'woo-account-studio' ), 'tag' => __( 'Business led', 'woo-account-studio' ), 'description' => __( 'A structured workspace for wholesale and business customers.', 'woo-account-studio' ), 'shell' => 'console', 'primary' => '#365eaa', 'accent' => '#75a9e8', 'background' => '#f3f7fd', 'surface' => '#ffffff', 'text' => '#192b4d', 'sidebar' => '#172744' ),
		);
	}
	public function preset( $id = null ) { $presets = $this->presets(); $id = $id ?: $this->config()['experience']; return $presets[ $id ] ?? $presets['commerce']; }

	public function assets() {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || ! is_user_logged_in() || ! $this->active() ) return;
		$s = $this->config();
		wp_enqueue_style( 'wcas-hub', WCAS_URL . 'assets/css/hub.css', array(), WCAS_VERSION );
		wp_enqueue_script( 'wcas-hub', WCAS_URL . 'assets/js/hub.js', array(), WCAS_VERSION, true );
		wp_localize_script( 'wcas-hub', 'wcasHub', array( 'motion' => 'yes' === $s['enable_motion'] ) );
		wp_add_inline_style( 'wcas-hub', sprintf( '.wcas-app{--hub-primary:%1$s;--hub-accent:%2$s;--hub-bg:%3$s;--hub-surface:%4$s;--hub-ink:%5$s;--hub-sidebar:%6$s;--hub-radius:%7$dpx;--hub-content-width:%8$dpx;--hub-scale:%9$.2f;}%10$s', esc_attr( $s['primary'] ), esc_attr( $s['accent'] ), esc_attr( $s['background'] ), esc_attr( $s['surface'] ), esc_attr( $s['text'] ), esc_attr( $s['sidebar'] ), absint( $s['radius'] ), absint( $s['content_width'] ), max( 0.85, min( 1.2, absint( $s['font_scale'] ) / 100 ) ), $s['custom_css'] ) );
	}
	public function admin_assets( $hook ) {
		if ( 'woocommerce_page_woo-account-studio' !== $hook ) return;
		wp_enqueue_media();
		wp_enqueue_style( 'wcas-admin', WCAS_URL . 'assets/css/admin.css', array(), WCAS_VERSION );
		wp_enqueue_script( 'wcas-admin', WCAS_URL . 'assets/js/admin.js', array( 'jquery' ), WCAS_VERSION, true );
		wp_localize_script( 'wcas-admin', 'wcasStudio', array( 'autoLanguage' => $this->use_arabic() ? 'ar' : 'en' ) );
	}
	public function body_class( $classes ) {
		if ( function_exists( 'is_account_page' ) && is_account_page() && is_user_logged_in() && $this->active() ) {
			$config = $this->config();
			$classes[] = 'wcas-hub-page'; $classes[] = 'wcas-experience-' . sanitize_html_class( $config['experience'] ); $classes[] = 'wcas-nav-' . sanitize_html_class( $config['navigation'] ); $classes[] = 'wcas-appearance-' . sanitize_html_class( $config['appearance'] ); $classes[] = 'wcas-mobile-nav-' . sanitize_html_class( $config['mobile_nav'] ); $classes[] = 'wcas-mobile-style-' . sanitize_html_class( $config['mobile_nav_style'] ); $classes[] = 'wcas-mobile-labels-' . sanitize_html_class( $config['mobile_nav_labels'] ); $classes[] = 'wcas-mobile-icons-' . sanitize_html_class( $config['mobile_icon_style'] );
			if ( 'yes' === $config['hide_page_title'] ) $classes[] = 'wcas-hide-page-title';
			if ( isset( $_GET['wcas-preview'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['wcas-preview'] ) ) ) $classes[] = 'wcas-preview-frame';
		}
		return $classes;
	}
	public function template( $located, $name ) {
		if ( 'myaccount/my-account.php' === $name && function_exists( 'is_account_page' ) && is_account_page() && is_user_logged_in() && $this->active() ) return WCAS_PATH . 'templates/hub/app.php';
		return $located;
	}

	public function nav_items() {
		$s = $this->config();
		return array(
			'dashboard' => array( $s['label_dashboard'] ?: __( 'Overview', 'woo-account-studio' ), 'grid' ),
			'orders' => array( $s['label_orders'] ?: __( 'Orders', 'woo-account-studio' ), 'bag' ),
			'downloads' => array( $s['label_library'] ?: __( 'My library', 'woo-account-studio' ), 'download' ),
			'edit-address' => array( $s['label_addresses'] ?: __( 'Addresses', 'woo-account-studio' ), 'pin' ),
			'edit-account' => array( $s['label_profile'] ?: __( 'Profile & security', 'woo-account-studio' ), 'user' ),
		);
	}
	public function mobile_nav_items() {
		$items = $this->nav_items(); $settings = $this->config(); $experience = $settings['experience'];
		$icons = array( 'dashboard' => 'mobile_icon_dashboard', 'orders' => 'mobile_icon_orders', 'downloads' => 'mobile_icon_library', 'edit-address' => 'mobile_icon_addresses', 'edit-account' => 'mobile_icon_profile' );
		foreach ( $icons as $endpoint => $setting ) if ( isset( $items[ $endpoint ] ) ) $items[ $endpoint ][1] = $this->mobile_icon( str_replace( 'mobile_icon_', '', $setting ), $items[ $endpoint ][1] );
		if ( 'digital' === $experience ) return array( 'dashboard' => $items['dashboard'], 'downloads' => $items['downloads'], 'orders' => $items['orders'] );
		if ( in_array( $experience, array( 'service', 'beauty' ), true ) ) return array( 'dashboard' => $items['dashboard'], 'orders' => $items['orders'], 'edit-account' => $items['edit-account'] );
		return array( 'dashboard' => $items['dashboard'], 'orders' => $items['orders'], 'edit-address' => $items['edit-address'] );
	}
	public function mobile_icon( $item, $fallback = 'grid' ) {
		$key = 'mobile_icon_' . sanitize_key( $item ); $settings = $this->config(); $icons = $this->mobile_icon_options(); $selected = $settings[ $key ] ?? $fallback;
		return isset( $icons[ $selected ] ) ? $selected : $fallback;
	}
	public function mobile_icon_options() {
		return array(
			'grid' => __( 'App grid', 'woo-account-studio' ), 'home' => __( 'Home', 'woo-account-studio' ), 'sparkles' => __( 'Sparkles', 'woo-account-studio' ), 'compass' => __( 'Compass', 'woo-account-studio' ),
			'bag' => __( 'Shopping bag', 'woo-account-studio' ), 'receipt' => __( 'Receipt', 'woo-account-studio' ), 'box' => __( 'Package', 'woo-account-studio' ), 'truck' => __( 'Delivery truck', 'woo-account-studio' ),
			'download' => __( 'Download', 'woo-account-studio' ), 'book' => __( 'Book', 'woo-account-studio' ), 'folder' => __( 'Folder', 'woo-account-studio' ), 'play' => __( 'Play', 'woo-account-studio' ),
			'pin' => __( 'Location pin', 'woo-account-studio' ), 'map' => __( 'Map', 'woo-account-studio' ), 'home-pin' => __( 'Home address', 'woo-account-studio' ),
			'user' => __( 'Profile', 'woo-account-studio' ), 'shield' => __( 'Shield', 'woo-account-studio' ), 'heart' => __( 'Heart', 'woo-account-studio' ),
			'menu' => __( 'Menu', 'woo-account-studio' ), 'dots' => __( 'More dots', 'woo-account-studio' ), 'star' => __( 'Star', 'woo-account-studio' ),
		);
	}
	public function icon( $name, $size = 20 ) {
		$paths = array(
			'grid' => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
			'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z"/><path d="M9 21v-6h6v6"/>', 'sparkles' => '<path d="m12 3 1.5 5.5L19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3Z"/><path d="m19 16 .6 2.4L22 19l-2.4.6L19 22l-.6-2.4L16 19l2.4-.6L19 16Z"/>', 'compass' => '<circle cx="12" cy="12" r="9"/><path d="m15.8 8.2-2.1 5.5-5.5 2.1 2.1-5.5 5.5-2.1Z"/>',
			'bag' => '<path d="M5 8h14l-1 13H6L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/>',
			'receipt' => '<path d="M5 3h14v18l-2.5-1.5L14 21l-2-1.5L10 21l-2.5-1.5L5 21V3Z"/><path d="M8 8h8M8 12h8M8 16h5"/>', 'truck' => '<path d="M3 6h11v10H3V6Z"/><path d="M14 10h4l3 3v3h-7v-6Z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
			'download' => '<path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M4 20h16"/>',
			'book' => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 19h16"/><path d="M9 7h6"/>', 'folder' => '<path d="M3 6h7l2 2h9v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6Z"/><path d="M3 10h18"/>', 'play' => '<circle cx="12" cy="12" r="9"/><path d="m10 8 6 4-6 4V8Z"/>',
			'pin' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
			'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15m6-12v15"/>', 'home-pin' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z"/><path d="M12 21v-6"/><circle cx="17" cy="13" r="2.5"/>',
			'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 3.7-6 8-6s7 2 8 6"/>',
			'shield' => '<path d="M12 3 20 6v5c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/>', 'heart' => '<path d="M20.8 8.7c0 5.2-8.8 10.3-8.8 10.3S3.2 13.9 3.2 8.7A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.8 2.3Z"/>',
			'help' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.7 2.7 0 1 1 4.3 2.2c-1.5 1.1-1.8 1.5-1.8 3"/><path d="M12 17h.01"/>',
			'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>', 'dots' => '<circle cx="5" cy="12" r="1" fill="currentColor"/><circle cx="12" cy="12" r="1" fill="currentColor"/><circle cx="19" cy="12" r="1" fill="currentColor"/>', 'star' => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>',
			'chevron' => '<path d="m9 18 6-6-6-6"/>', 'box' => '<path d="m3 7 9-4 9 4v10l-9 4-9-4V7Z"/><path d="m3 7 9 4 9-4M12 11v10"/>',
		);
		return '<svg aria-hidden="true" width="' . absint( $size ) . '" height="' . absint( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($paths[$name] ?? $paths['grid']) . '</svg>';
	}
	public function endpoint() {
		if ( isset( $_GET['wcas-section'] ) && 'more' === sanitize_key( wp_unslash( $_GET['wcas-section'] ) ) ) return 'more';
		global $wp;
		$endpoints = array_unique( array_merge( array( 'view-order', 'orders', 'downloads', 'edit-address', 'edit-account' ), array_keys( wc_get_account_menu_items() ) ) );
		foreach ( $endpoints as $endpoint ) if ( isset( $wp->query_vars[ $endpoint ] ) ) return $endpoint;
		return 'dashboard';
	}
	public function account_url( $endpoint = '' ) { return $endpoint ? wc_get_account_endpoint_url( $endpoint ) : wc_get_page_permalink( 'myaccount' ); }
	public function more_url() { return add_query_arg( 'wcas-section', 'more', $this->account_url() ); }
	public function current_user() { return wp_get_current_user(); }
	public function initials( $name ) { $first = function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 ); return strtoupper( $first ); }
	private function preview_template() {
		if ( ! isset( $_GET['wcas-preview'], $_GET['wcas-preview-template'] ) || '1' !== sanitize_text_field( wp_unslash( $_GET['wcas-preview'] ) ) || ! current_user_can( 'manage_woocommerce' ) ) return '';
		$template = sanitize_key( wp_unslash( $_GET['wcas-preview-template'] ) );
		return in_array( $template, array( 'commerce', 'luxury', 'digital', 'subscription', 'service', 'beauty', 'grocery', 'wholesale' ), true ) ? $template : '';
	}
	private function preview_language() {
		if ( ! isset( $_GET['wcas-preview'], $_GET['wcas-preview-language'] ) || '1' !== sanitize_text_field( wp_unslash( $_GET['wcas-preview'] ) ) || ! current_user_can( 'manage_woocommerce' ) ) return '';
		$preview_language = sanitize_key( wp_unslash( $_GET['wcas-preview-language'] ) );
		return in_array( $preview_language, array( 'ar', 'en' ), true ) ? $preview_language : '';
	}
	public function use_arabic() {
		$preview_language = $this->preview_language();
		if ( $preview_language ) return 'ar' === $preview_language;
		$mode = $this->config()['language_mode'];
		return 'ar' === $mode || ( 'auto' === $mode && 0 === strpos( determine_locale(), 'ar' ) );
	}
	public function interface_is_rtl() {
		$preview_language = $this->preview_language(); $mode = $this->config()['language_mode'];
		if ( 'en' === $preview_language || 'en' === $mode ) return false;
		if ( 'ar' === $preview_language || 'ar' === $mode ) return true;
		return is_rtl();
	}
	public function builtin_arabic( $translation, $text, $domain ) {
		if ( 'woo-account-studio' !== $domain || ! $this->use_arabic() ) return $translation;
		$arabic = array(
			'Overview' => 'الرئيسية', 'Orders' => 'الطلبات', 'My library' => 'مكتبتي', 'Addresses' => 'العناوين', 'Profile & security' => 'الملف الشخصي والأمان',
			'Customer account' => 'حساب العميل', 'Account navigation' => 'تنقّل الحساب', 'Help & support' => 'المساعدة والدعم', 'Sign out' => 'تسجيل الخروج',
			'Continue shopping' => 'متابعة التسوق', 'Mobile account navigation' => 'تنقّل الحساب على الجوال', 'Customer hub' => 'مركز حسابك',
			'Everything about your orders and account, in one place.' => 'كل ما يخص طلباتك وحسابك في مكان واحد.', 'Latest order' => 'أحدث طلب', 'Track order' => 'تتبع الطلب',
			'Confirmed' => 'تم التأكيد', 'Delivered' => 'تم التسليم', 'Ready when you are' => 'حين تكون مستعدًا', 'Your next favourite is waiting.' => 'منتجك المفضل القادم بانتظارك.',
			'Discover products selected for you and return anytime to manage your account.' => 'اكتشف منتجات مختارة لك، وعد في أي وقت لإدارة حسابك.', 'Start shopping' => 'ابدأ التسوق',
			'Your space' => 'مساحتك', 'Delivery details' => 'تفاصيل التوصيل', 'Files & purchases' => 'الملفات والمشتريات', 'Support' => 'الدعم', 'We are here to help' => 'نحن هنا لمساعدتك',
			'Member benefits' => 'مزايا العضوية', 'Your next order deserves something extra.' => 'طلبك القادم يستحق ميزة إضافية.', 'Keep shopping to unlock personalised benefits and offers.' => 'تابع التسوق لفتح مزايا وعروض مخصصة لك.',
			'Explore the store' => 'استكشف المتجر', 'Order centre' => 'مركز الطلبات', 'Your orders' => 'طلباتك', 'View your purchases, follow delivery and order again.' => 'استعرض مشترياتك، وتابع التوصيل، وأعد الطلب بسهولة.',
			'No orders yet' => 'لا توجد طلبات بعد', 'When you place an order, its progress will appear here.' => 'بعد إتمام طلبك ستظهر تفاصيله هنا.', 'Browse products' => 'تصفح المنتجات',
			'Order items' => 'عناصر الطلب', 'View order' => 'عرض الطلب', 'Order not found.' => 'لم يتم العثور على الطلب.', 'Order details' => 'تفاصيل الطلب',
			'Order placed' => 'تم إنشاء الطلب', 'Items in this order' => 'منتجات هذا الطلب', 'Quantity: %s' => 'الكمية: %s', 'Total paid' => 'المبلغ المدفوع',
			'Delivery address' => 'عنوان التوصيل', 'Need help with this order?' => 'هل تحتاج إلى مساعدة بخصوص هذا الطلب؟', 'Our support team can help with delivery, returns or payments.' => 'يمكن لفريق الدعم مساعدتك في التوصيل أو الإرجاع أو الدفع.', 'Contact support' => 'تواصل مع الدعم',
			'Digital library' => 'المكتبة الرقمية', 'Your purchased files, courses and digital products.' => 'ملفاتك ودوراتك ومنتجاتك الرقمية التي اشتريتها.', 'Your library is empty' => 'مكتبتك فارغة', 'Your downloadable purchases will be kept safely here.' => 'ستظهر مشترياتك القابلة للتنزيل هنا بأمان.',
			'Unlimited downloads' => 'تنزيلات غير محدودة', '%s downloads remaining' => 'التنزيلات المتبقية: %s', 'Download' => 'تنزيل', 'Your addresses' => 'عناوينك', 'Keep delivery and billing details up to date.' => 'حافظ على تحديث عناوين التوصيل والفواتير.',
			'Billing address' => 'عنوان الفاتورة', 'Shipping address' => 'عنوان الشحن', 'No address saved yet.' => 'لم يتم حفظ عنوان بعد.', 'Edit address' => 'تعديل العنوان',
			'Personal area' => 'المساحة الشخصية', 'More' => 'المزيد', 'Account settings and help, all in one place.' => 'إعدادات الحساب والمساعدة في مكان واحد.', 'Manage your personal details and password.' => 'أدر بياناتك الشخصية وكلمة المرور.', 'Developed by Sky Athena' => 'من تطوير سكاي أثينا',
			'Good to see you, %s' => 'مرحبًا بعودتك، %s', 'Order #%s is %s' => 'الطلب #%s حالته %s', 'Placed on %s · %s' => 'تم الطلب في %s · %s',
			'Experience' => 'التجربة', 'Choose the dashboard foundation for this store.' => 'اختر أساس لوحة العميل لهذا المتجر.', 'Enable Customer Hub' => 'تفعيل مركز العميل', 'Experience template' => 'نموذج التجربة', 'Interface language' => 'لغة الواجهة', 'Follow site language' => 'اتباع لغة الموقع', 'Arabic' => 'العربية', 'English' => 'الإنجليزية', 'Desktop navigation' => 'تنقّل سطح المكتب', 'Full sidebar' => 'شريط جانبي كامل', 'Compact sidebar' => 'شريط جانبي مختصر',
			'Brand identity' => 'هوية العلامة التجارية', 'The hub inherits your store identity by default.' => 'يرث المركز هوية متجرك تلقائيًا.', 'Brand name' => 'اسم العلامة التجارية', 'Brand tagline' => 'وصف قصير للعلامة', 'Logo source' => 'مصدر الشعار', 'Use the site logo' => 'استخدام شعار الموقع', 'Use a custom logo' => 'استخدام شعار مخصص', 'Use initials only' => 'استخدام الأحرف الأولى فقط', 'Select custom logo' => 'اختر شعارًا مخصصًا', 'Remove' => 'إزالة',
			'Design system' => 'نظام التصميم', 'Control the visual tokens without writing code.' => 'تحكّم بعناصر التصميم دون كتابة كود.', 'Primary' => 'اللون الأساسي', 'Accent' => 'لون التمييز', 'Background' => 'الخلفية', 'Card surface' => 'خلفية البطاقة', 'Text' => 'النص', 'Sidebar' => 'الشريط الجانبي', 'Corner radius' => 'استدارة الزوايا', 'Content width' => 'عرض المحتوى', 'Text scale' => 'حجم النص',
			'Dashboard modules' => 'وحدات لوحة العميل', 'Choose which parts of the overview are visible to customers.' => 'اختر الأجزاء التي تظهر للعميل في الصفحة الرئيسية.', 'Active order focus' => 'بطاقة الطلب النشط', 'Quick action cards' => 'بطاقات الإجراءات السريعة', 'Member benefits panel' => 'لوحة مزايا العضوية', 'Subtle interface motion' => 'حركات واجهة هادئة',
				'Content and labels' => 'المحتوى والتسميات', 'Rename navigation items and set a direct support channel.' => 'أعد تسمية عناصر التنقل وحدد قناة دعم مباشرة.', 'Support or WhatsApp URL' => 'رابط الدعم أو واتساب', 'Overview label' => 'تسمية الرئيسية', 'Orders label' => 'تسمية الطلبات', 'Library label' => 'تسمية المكتبة', 'Addresses label' => 'تسمية العناوين', 'Profile label' => 'تسمية الملف الشخصي', 'More label' => 'تسمية المزيد', 'Advanced' => 'متقدم', 'Optional refinements for designers.' => 'تحسينات اختيارية للمصممين.', 'Additional CSS' => 'CSS إضافي', 'Applied only inside the customer hub.' => 'يُطبّق داخل مركز العميل فقط.', 'Save Customer Hub' => 'حفظ مركز العميل',
			'Commerce Hub' => 'مركز التجارة', 'Luxury Concierge' => 'الخدمة الفاخرة', 'Digital Library' => 'المكتبة الرقمية', 'Subscription Club' => 'نادي الاشتراكات', 'Service & Booking' => 'الخدمات والحجوزات', 'Mobile-first' => 'الجوال أولًا', 'No competing mobile drawer.' => 'لا توجد قائمة جوال متعارضة.', 'The plugin uses an in-app bottom bar on phones, so it never collides with the theme menu.' => 'تستخدم الإضافة شريطًا سفليًا داخل الحساب على الجوال، لذلك لا يتعارض مع قائمة القالب.', 'Build a customer hub that belongs to your brand.' => 'ابنِ مركز عميل يحمل هوية علامتك التجارية.', 'Manage this part of your account in a focused space.' => 'أدر هذا الجزء من حسابك في مساحة مركزة.',
			'Orders first' => 'الطلبات أولًا', 'Relationship led' => 'علاقة شخصية', 'Content led' => 'المحتوى أولًا', 'Retention led' => 'الاحتفاظ بالعميل', 'Service led' => 'الخدمة أولًا', 'Personal led' => 'تجربة شخصية', 'Convenience led' => 'السهولة أولًا', 'Business led' => 'الأعمال أولًا',
			'A post-purchase command centre for everyday stores.' => 'مركز ما بعد الشراء للمتاجر اليومية.', 'A high-touch account experience with concierge support.' => 'تجربة حساب شخصية مع دعم مميز.', 'Bring downloads, courses and licences to the foreground.' => 'ضع التنزيلات والدورات والتراخيص في المقدمة.', 'A member-style home for recurring purchases and benefits.' => 'واجهة عضوية للمشتريات المتكررة والمزايا.', 'A calm control centre for appointments and service orders.' => 'مركز هادئ للمواعيد وطلبات الخدمة.', 'A warm, editorial account for beauty and wellness brands.' => 'حساب دافئ وتحريري لعلامات الجمال والعناية.', 'A practical account optimised for repeat orders and delivery.' => 'حساب عملي للطلبات المتكررة والتوصيل.', 'A structured workspace for wholesale and business customers.' => 'مساحة منظمة لعملاء الجملة والأعمال.',
			'Template Studio' => 'استديو القوالب', 'Choose an experience, shape it around your brand, then preview the real customer hub before publishing.' => 'اختر تجربة، وشكّلها حسب علامتك التجارية، ثم عاين حساب العميل الحقيقي قبل النشر.', 'Open live account' => 'فتح الحساب المباشر', 'Template portal' => 'بوابة القوالب', 'These are different customer journeys, not colour variations.' => 'هذه رحلات عميل مختلفة وليست اختلافات في الألوان.', 'Brand and visual system' => 'الهوية والنظام البصري', 'Use the site identity by default or take full control.' => 'استخدم هوية الموقع افتراضيًا أو تحكم بها بالكامل.', 'Customer journey' => 'رحلة العميل', 'Control the priority and presence of each home-screen module.' => 'تحكم بأولوية وظهور كل وحدة في الصفحة الرئيسية.', 'Navigation, content and motion' => 'التنقل والمحتوى والحركة', 'Tune the desktop shell and the mobile app dock independently.' => 'اضبط هيكل سطح المكتب وشريط تطبيق الجوال بشكل مستقل.', 'Advanced styling' => 'تنسيق متقدم', 'For design refinements after the design system controls.' => 'لتحسينات التصميم بعد استخدام عناصر النظام البصري.', 'Live preview' => 'معاينة حية', 'Your real account' => 'حسابك الحقيقي', 'Loading your account preview…' => 'جارٍ تحميل معاينة حسابك…', 'Changes are reflected here before you save. The preview uses your current administrator account and does not publish anything.' => 'تنعكس التغييرات هنا قبل الحفظ. تستخدم المعاينة حساب المدير الحالي ولا تنشر أي شيء.',
				'Light' => 'فاتح', 'Dark' => 'داكن', 'Floating app dock' => 'شريط تطبيق عائم', 'Minimal bottom bar' => 'شريط سفلي بسيط', 'Mobile navigation' => 'تنقل الجوال', 'Custom welcome title' => 'عنوان ترحيب مخصص', 'Custom welcome description' => 'وصف ترحيب مخصص', 'Show priority order card' => 'إظهار بطاقة الطلب ذات الأولوية', 'Show template-specific module' => 'إظهار وحدة القالب الخاصة', 'Show quick actions' => 'إظهار الإجراءات السريعة', 'Show benefits module' => 'إظهار وحدة المزايا', 'Context hub label' => 'تسمية المركز السياقي', 'Save and publish changes' => 'حفظ ونشر التغييرات', 'Use selectors inside .wcas-app only.' => 'استخدم محددات داخل .wcas-app فقط.',
				'Mobile bottom bar studio' => 'استديو الشريط السفلي للجوال', 'Choose a distinct navigation style, decide whether labels appear, and tune every icon customers touch.' => 'اختر نمط تنقل مميزًا، وحدد ظهور التسميات، واضبط كل أيقونة يلمسها العميل.', 'Mobile bar placement' => 'موضع شريط الجوال', 'Floating above page content' => 'عائم فوق محتوى الصفحة', 'Attached to screen edge' => 'ملتصق بحافة الشاشة', 'Bottom bar template' => 'قالب الشريط السفلي', 'Glass dock' => 'شريط زجاجي عائم', 'Pill navigator' => 'تنقل كبسولي', 'Split action dock' => 'شريط إجراءات مقسم', 'Solid app bar' => 'شريط تطبيق ممتلئ', 'Outline bar' => 'شريط بإطار', 'Bare essentials' => 'الأساسيات فقط', 'Each template changes the bar shape, active state and visual weight.' => 'يغير كل قالب شكل الشريط وحالة العنصر النشط ووزنه البصري.', 'Show labels under mobile icons' => 'إظهار التسميات تحت أيقونات الجوال', 'Icon treatment' => 'معالجة الأيقونات', 'Fine line' => 'خط رفيع', 'Bold line' => 'خط عريض', 'Orbit accent' => 'حلقة مميزة', 'Soft filled' => 'تعبئة ناعمة', 'Hide the theme page title' => 'إخفاء عنوان صفحة القالب', 'Removes the large My Account title or hero supplied by your theme above the customer hub.' => 'يزيل عنوان «حسابي» الكبير أو الغلاف الذي يضيفه القالب فوق مركز العميل.', 'Icon set for the bottom bar' => 'مجموعة أيقونات الشريط السفلي', 'The three main destinations adapt to the chosen account template. These choices keep their icon language consistent.' => 'تتغير الوجهات الثلاث الرئيسية حسب قالب الحساب المختار. تحافظ هذه الخيارات على لغة أيقونات متناسقة.', 'Overview icon' => 'أيقونة الرئيسية', 'Orders icon' => 'أيقونة الطلبات', 'Library icon' => 'أيقونة المكتبة', 'Addresses icon' => 'أيقونة العناوين', 'Profile icon' => 'أيقونة الملف الشخصي', 'More menu icon' => 'أيقونة قائمة المزيد',
				'App grid' => 'شبكة التطبيقات', 'Home' => 'المنزل', 'Sparkles' => 'لمعات', 'Compass' => 'بوصلة', 'Shopping bag' => 'حقيبة تسوق', 'Receipt' => 'إيصال', 'Package' => 'طرد', 'Delivery truck' => 'شاحنة توصيل', 'Book' => 'كتاب', 'Folder' => 'مجلد', 'Play' => 'تشغيل', 'Location pin' => 'دبوس الموقع', 'Map' => 'خريطة', 'Home address' => 'عنوان المنزل', 'Profile' => 'الملف الشخصي', 'Shield' => 'درع', 'Heart' => 'قلب', 'Menu' => 'قائمة', 'More dots' => 'نقاط المزيد', 'Star' => 'نجمة',
			'Account' => 'الحساب', 'Help' => 'المساعدة', 'Track an order' => 'تتبع طلب', 'Close' => 'إغلاق', 'Appearance' => 'المظهر', 'Brand tagline' => 'وصف العلامة التجارية', 'Small mobile preview' => 'معاينة جوال صغيرة', 'Mobile preview' => 'معاينة الجوال', 'Desktop preview' => 'معاينة سطح المكتب', 'If your theme logo is not registered as a WordPress Custom Logo, choose it from the Media Library.' => 'إذا لم يكن شعار القالب مسجّلًا كشعار ووردبريس مخصص، اختره من مكتبة الوسائط.',
			'Everything after checkout, in one view.' => 'كل ما بعد الشراء في مكان واحد.', '%s stays close: purchases, delivery details and fast support.' => 'يبقى %s قريبًا منك: المشتريات وتفاصيل التوصيل والدعم السريع.',
			'A more personal kind of service.' => 'خدمة أكثر قربًا وشخصية.', 'Your account keeps orders, delivery and direct support beautifully organised.' => 'يجمع حسابك الطلبات والتوصيل والدعم المباشر بشكل منظم.', 'Talk to us' => 'تحدث معنا',
			'Your digital library, ready whenever you are.' => 'مكتبتك الرقمية جاهزة متى احتجتها.', '%s item available to download.' => 'يتوفر عنصر واحد للتنزيل: %s', '%s items available to download.' => 'تتوفر عناصر للتنزيل: %s', 'Your files and learning products will live here after purchase.' => 'ستظهر ملفاتك ومنتجاتك التعليمية هنا بعد الشراء.', 'Open library' => 'افتح المكتبة',
			'Keep your favourites coming.' => 'حافظ على وصول مفضلاتك.', 'Review recurring purchases, delivery details and member benefits from one home.' => 'راجع مشترياتك المتكررة وتفاصيل التوصيل ومزايا العضوية من مكان واحد.', 'Manage purchases' => 'إدارة المشتريات',
			'Your service details stay close.' => 'تفاصيل خدمتك في متناول اليد.', 'Use this space to find service orders, delivery details and quick help.' => 'استخدم هذه المساحة للعثور على طلبات الخدمة وتفاصيل التوصيل والمساعدة السريعة.', 'Get support' => 'الحصول على الدعم',
			'Your routine, kept beautifully simple.' => 'روتينك، ببساطة وأناقة.', 'A calm personal space for your orders, delivery preferences and support.' => 'مساحة شخصية هادئة لطلباتك وتفضيلات التوصيل والدعم.',
			'A faster way back to your favourites.' => 'طريق أسرع للعودة إلى مفضلاتك.', 'Review your latest delivery, then return to the store when you are ready.' => 'راجع آخر توصيل لك، ثم عد إلى المتجر عندما تكون مستعدًا.', 'Your latest deliveries and repeat purchases will be easy to reach here.' => 'ستكون آخر عمليات التوصيل والطلبات المتكررة سهلة الوصول هنا.', 'Shop again' => 'تسوّق مجددًا',
			'A clearer workspace for business orders.' => 'مساحة عمل أوضح لطلبات الأعمال.', 'Keep purchase history, delivery details and your account information together.' => 'احتفظ بسجل المشتريات وتفاصيل التوصيل ومعلومات حسابك في مكان واحد.',
		);
		return isset( $arabic[ $text ] ) ? $arabic[ $text ] : $translation;
	}
	public function brand_mark() {
		$s = $this->config(); $logo_id = 0;
		if ( 'upload' === $s['logo_source'] ) $logo_id = absint( $s['logo_id'] );
		if ( 'site' === $s['logo_source'] ) $logo_id = absint( get_theme_mod( 'custom_logo' ) );
		if ( $logo_id ) return wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'wcas-site-logo', 'alt' => $s['brand_name'] ) );
		if ( 'site' === $s['logo_source'] && has_site_icon() ) return '<img class="wcas-site-logo" src="' . esc_url( get_site_icon_url( 512 ) ) . '" alt="' . esc_attr( $s['brand_name'] ) . '">';
		return '<span class="wcas-brand-fallback">' . esc_html( $this->initials( $s['brand_name'] ?: get_bloginfo( 'name' ) ) ) . '</span>';
	}

	public function render_screen( $screen ) {
		if ( 'more' === $screen ) return $this->more();
		if ( 'view-order' === $screen ) return $this->view_order();
		if ( 'orders' === $screen ) return $this->orders();
		if ( 'downloads' === $screen ) return $this->downloads();
		if ( 'edit-address' === $screen ) return $this->addresses();
		if ( 'edit-account' === $screen ) return $this->profile();
		if ( 'dashboard' !== $screen ) return $this->generic_endpoint( $screen );
		return $this->dashboard();
	}
	public function generic_endpoint( $screen ) {
		$menu = wc_get_account_menu_items(); $label = $menu[ $screen ] ?? ucwords( str_replace( '-', ' ', $screen ) );
		$this->title( __( 'Customer hub', 'woo-account-studio' ), $label, __( 'Manage this part of your account in a focused space.', 'woo-account-studio' ) );
		echo '<div class="wcas-native-form wcas-native-endpoint">'; do_action( 'woocommerce_account_' . $screen . '_endpoint', get_query_var( $screen ) ); echo '</div>';
	}
	public function more() {
		$user = $this->current_user(); $s = $this->config();
		$this->title( __( 'Personal area', 'woo-account-studio' ), __( 'More', 'woo-account-studio' ), __( 'Account settings and help, all in one place.', 'woo-account-studio' ) );
		echo '<section class="wcas-more-profile"><span class="wcas-avatar">' . esc_html( $this->initials( $user->display_name ) ) . '</span><div><h2>' . esc_html( $user->display_name ) . '</h2><p>' . esc_html( $user->user_email ) . '</p></div></section>';
		echo '<div class="wcas-more-group"><h2>' . esc_html__( 'Account', 'woo-account-studio' ) . '</h2><section class="wcas-more-list">';
		$this->more_link( 'pin', __( 'Addresses', 'woo-account-studio' ), $this->account_url( 'edit-address' ) );
		$this->more_link( 'user', __( 'Profile & security', 'woo-account-studio' ), $this->account_url( 'edit-account' ) );
		echo '</section></div><div class="wcas-more-group"><h2>' . esc_html__( 'Help', 'woo-account-studio' ) . '</h2><section class="wcas-more-list">';
		$this->more_link( 'bag', __( 'Track an order', 'woo-account-studio' ), $this->account_url( 'orders' ) );
		$this->more_link( 'help', __( 'Help & support', 'woo-account-studio' ), $s['support_url'] ?: $this->account_url() );
		echo '</section></div><section class="wcas-more-list wcas-more-session"><a class="wcas-more-signout" href="' . esc_url( wc_logout_url() ) . '">' . esc_html__( 'Sign out', 'woo-account-studio' ) . '</a></section>';
	}
	public function mobile_more_sheet() {
		$user = $this->current_user(); $s = $this->config();
		echo '<dialog class="wcas-mobile-more-sheet" id="wcas-mobile-more-sheet" aria-label="' . esc_attr__( 'More', 'woo-account-studio' ) . '"><div class="wcas-sheet-handle" aria-hidden="true"></div><header class="wcas-sheet-head"><div><span>' . esc_html__( 'Customer account', 'woo-account-studio' ) . '</span><h2>' . esc_html__( 'More', 'woo-account-studio' ) . '</h2></div><button class="wcas-sheet-close" type="button" aria-label="' . esc_attr__( 'Close', 'woo-account-studio' ) . '">×</button></header><section class="wcas-sheet-profile"><span class="wcas-avatar">' . esc_html( $this->initials( $user->display_name ) ) . '</span><div><b>' . esc_html( $user->display_name ) . '</b><small>' . esc_html( $user->user_email ) . '</small></div></section><div class="wcas-sheet-group"><span>' . esc_html__( 'Account', 'woo-account-studio' ) . '</span><a href="' . esc_url( $this->account_url( 'edit-address' ) ) . '">' . $this->icon( 'pin' ) . '<b>' . esc_html__( 'Addresses', 'woo-account-studio' ) . '</b>' . $this->icon( 'chevron' ) . '</a><a href="' . esc_url( $this->account_url( 'edit-account' ) ) . '">' . $this->icon( 'user' ) . '<b>' . esc_html__( 'Profile & security', 'woo-account-studio' ) . '</b>' . $this->icon( 'chevron' ) . '</a></div><div class="wcas-sheet-group"><span>' . esc_html__( 'Help', 'woo-account-studio' ) . '</span><a href="' . esc_url( $this->account_url( 'orders' ) ) . '">' . $this->icon( 'bag' ) . '<b>' . esc_html__( 'Track an order', 'woo-account-studio' ) . '</b>' . $this->icon( 'chevron' ) . '</a><a href="' . esc_url( $s['support_url'] ?: $this->account_url() ) . '">' . $this->icon( 'help' ) . '<b>' . esc_html__( 'Help & support', 'woo-account-studio' ) . '</b>' . $this->icon( 'chevron' ) . '</a></div><a class="wcas-sheet-signout" href="' . esc_url( wc_logout_url() ) . '">' . esc_html__( 'Sign out', 'woo-account-studio' ) . '</a></dialog>';
	}
	private function more_link( $icon, $label, $url ) { echo '<a href="' . esc_url( $url ) . '">' . $this->icon( $icon ) . '<span>' . esc_html( $label ) . '</span>' . $this->icon( 'chevron' ) . '</a>'; }
	private function title( $eyebrow, $title, $text = '' ) {
		echo '<header class="wcas-screen-heading"><span>' . esc_html( $eyebrow ) . '</span><h1>' . esc_html( $title ) . '</h1>' . ( $text ? '<p>' . esc_html( $text ) . '</p>' : '' ) . '</header>';
	}
	private function customer_orders( $limit = 8 ) { return wc_get_orders( array( 'customer_id' => get_current_user_id(), 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC' ) ); }
	private function order_count_label( $count ) {
		if ( $this->use_arabic() ) return sprintf( '%s %s', number_format_i18n( $count ), 1 === (int) $count ? 'طلب' : 'طلبات' );
		return sprintf( _n( '%s order', '%s orders', $count, 'woo-account-studio' ), number_format_i18n( $count ) );
	}
	private function active_order() {
		$orders = wc_get_orders( array( 'customer_id' => get_current_user_id(), 'limit' => 1, 'status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed' ), 'orderby' => 'date', 'order' => 'DESC' ) );
		return $orders ? $orders[0] : false;
	}
	public function dashboard() {
		$user = $this->current_user(); $order = $this->active_order(); $s = $this->config(); $count = wc_get_customer_order_count( $user->ID ); $preset = $this->preset();
		echo '<div class="wcas-home wcas-home-' . esc_attr( $s['experience'] ) . '">';
		$this->title( __( 'Customer hub', 'woo-account-studio' ), $s['hero_title'] ?: sprintf( __( 'Good to see you, %s', 'woo-account-studio' ), $user->display_name ), $s['hero_description'] ?: __( 'Everything about your orders and account, in one place.', 'woo-account-studio' ) );
		if ( 'yes' === $s['show_order_focus'] ) { if ( $order ) $this->order_focus( $order ); else $this->empty_focus(); }
		if ( 'yes' === $s['show_template_story'] ) $this->template_story( $s['experience'], $order, $preset );
		if ( 'yes' === $s['show_action_cards'] ) {
			echo '<div class="wcas-dashboard-actions"><div class="wcas-section-title"><h2>' . esc_html__( 'Your space', 'woo-account-studio' ) . '</h2></div><section class="wcas-action-grid">';
			$this->action_card( 'bag', __( 'Orders', 'woo-account-studio' ), $this->order_count_label( $count ), $this->account_url( 'orders' ), 'primary' );
			$this->action_card( 'pin', __( 'Addresses', 'woo-account-studio' ), __( 'Delivery details', 'woo-account-studio' ), $this->account_url( 'edit-address' ), '' );
			$this->action_card( 'download', __( 'My library', 'woo-account-studio' ), __( 'Files & purchases', 'woo-account-studio' ), $this->account_url( 'downloads' ), '' );
			$this->action_card( 'help', __( 'Support', 'woo-account-studio' ), __( 'We are here to help', 'woo-account-studio' ), ! empty( $s['support_url'] ) ? $s['support_url'] : $this->account_url( 'edit-account' ), 'accent' );
			echo '</section></div>';
		}
		if ( 'yes' === $s['show_rewards'] ) echo '<section class="wcas-reward"><div><span>' . esc_html__( 'Member benefits', 'woo-account-studio' ) . '</span><h2>' . esc_html__( 'Your next order deserves something extra.', 'woo-account-studio' ) . '</h2><p>' . esc_html__( 'Keep shopping to unlock personalised benefits and offers.', 'woo-account-studio' ) . '</p></div><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Explore the store', 'woo-account-studio' ) . ' ' . $this->icon( 'chevron', 16 ) . '</a></section>';
		echo '</div>';
	}
	private function order_focus( $order ) {
		$status = wc_get_order_status_name( $order->get_status() ); $url = $this->account_url( 'view-order' ) . $order->get_id() . '/';
		echo '<section class="wcas-order-focus"><div class="wcas-focus-copy"><span class="wcas-live-dot"></span><span class="wcas-focus-label">' . esc_html__( 'Latest order', 'woo-account-studio' ) . '</span><h2>' . sprintf( esc_html__( 'Order #%s is %s', 'woo-account-studio' ), esc_html( $order->get_order_number() ), esc_html( $status ) ) . '</h2><p>' . sprintf( esc_html__( 'Placed on %s · %s', 'woo-account-studio' ), esc_html( wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) ) ), wp_kses_post( $order->get_formatted_order_total() ) ) . '</p><a class="wcas-button" href="' . esc_url( $url ) . '">' . esc_html__( 'Track order', 'woo-account-studio' ) . '</a></div><div class="wcas-order-steps"><span class="is-done">1<small>' . esc_html__( 'Confirmed', 'woo-account-studio' ) . '</small></span><i></i><span class="is-current">2<small>' . esc_html( $status ) . '</small></span><i></i><span>3<small>' . esc_html__( 'Delivered', 'woo-account-studio' ) . '</small></span></div></section>';
	}
	private function empty_focus() { echo '<section class="wcas-order-focus wcas-empty-focus"><div class="wcas-focus-copy"><span class="wcas-focus-label">' . esc_html__( 'Ready when you are', 'woo-account-studio' ) . '</span><h2>' . esc_html__( 'Your next favourite is waiting.', 'woo-account-studio' ) . '</h2><p>' . esc_html__( 'Discover products selected for you and return anytime to manage your account.', 'woo-account-studio' ) . '</p><a class="wcas-button" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Start shopping', 'woo-account-studio' ) . '</a></div></section>'; }
	private function action_card( $icon, $title, $text, $url, $class ) { echo '<a class="wcas-action-card ' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '"><span class="wcas-action-icon">' . $this->icon( $icon ) . '</span><div><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $text ) . '</p></div>' . $this->icon( 'chevron', 18 ) . '</a>'; }
	private function template_story( $template, $order, $preset ) {
		$settings = $this->config(); $downloads = wc_get_customer_available_downloads( get_current_user_id() ); $orders = wc_get_customer_order_count( get_current_user_id() ); $presets = $this->presets();
		$stories = array(
			'commerce' => array( 'title' => __( 'Everything after checkout, in one view.', 'woo-account-studio' ), 'text' => sprintf( __( '%s stays close: purchases, delivery details and fast support.', 'woo-account-studio' ), $this->order_count_label( $orders ) ), 'url' => $this->account_url( 'orders' ), 'button' => __( 'View orders', 'woo-account-studio' ), 'icon' => 'bag' ),
			'luxury' => array( 'title' => __( 'A more personal kind of service.', 'woo-account-studio' ), 'text' => __( 'Your account keeps orders, delivery and direct support beautifully organised.', 'woo-account-studio' ), 'url' => $settings['support_url'] ?: $this->more_url(), 'button' => __( 'Talk to us', 'woo-account-studio' ), 'icon' => 'help' ),
			'digital' => array( 'title' => __( 'Your digital library, ready whenever you are.', 'woo-account-studio' ), 'text' => $downloads ? sprintf( _n( '%s item available to download.', '%s items available to download.', count( $downloads ), 'woo-account-studio' ), count( $downloads ) ) : __( 'Your files and learning products will live here after purchase.', 'woo-account-studio' ), 'url' => $this->account_url( 'downloads' ), 'button' => __( 'Open library', 'woo-account-studio' ), 'icon' => 'download' ),
			'subscription' => array( 'title' => __( 'Keep your favourites coming.', 'woo-account-studio' ), 'text' => __( 'Review recurring purchases, delivery details and member benefits from one home.', 'woo-account-studio' ), 'url' => $this->account_url( 'orders' ), 'button' => __( 'Manage purchases', 'woo-account-studio' ), 'icon' => 'box' ),
			'service' => array( 'title' => __( 'Your service details stay close.', 'woo-account-studio' ), 'text' => __( 'Use this space to find service orders, delivery details and quick help.', 'woo-account-studio' ), 'url' => $settings['support_url'] ?: $this->account_url( 'orders' ), 'button' => __( 'Get support', 'woo-account-studio' ), 'icon' => 'help' ),
			'beauty' => array( 'title' => __( 'Your routine, kept beautifully simple.', 'woo-account-studio' ), 'text' => __( 'A calm personal space for your orders, delivery preferences and support.', 'woo-account-studio' ), 'url' => $settings['support_url'] ?: $this->more_url(), 'button' => __( 'Talk to us', 'woo-account-studio' ), 'icon' => 'help' ),
			'grocery' => array( 'title' => __( 'A faster way back to your favourites.', 'woo-account-studio' ), 'text' => $order ? __( 'Review your latest delivery, then return to the store when you are ready.', 'woo-account-studio' ) : __( 'Your latest deliveries and repeat purchases will be easy to reach here.', 'woo-account-studio' ), 'url' => wc_get_page_permalink( 'shop' ), 'button' => __( 'Shop again', 'woo-account-studio' ), 'icon' => 'bag' ),
			'wholesale' => array( 'title' => __( 'A clearer workspace for business orders.', 'woo-account-studio' ), 'text' => __( 'Keep purchase history, delivery details and your account information together.', 'woo-account-studio' ), 'url' => $this->account_url( 'orders' ), 'button' => __( 'View orders', 'woo-account-studio' ), 'icon' => 'box' ),
		);
		echo '<div class="wcas-template-stories">';
		foreach ( $stories as $id => $story ) {
			$label = $presets[ $id ]['name'];
			echo '<section class="wcas-template-story wcas-template-story--' . esc_attr( $id ) . '"><div class="wcas-story-icon">' . $this->icon( $story['icon'] ) . '</div><div class="wcas-story-copy"><span>' . esc_html( $label ) . '</span><h2>' . esc_html( $story['title'] ) . '</h2><p>' . esc_html( $story['text'] ) . '</p></div>' . $this->story_stage( $id, $orders, count( $downloads ) ) . '<a href="' . esc_url( $story['url'] ) . '">' . esc_html( $story['button'] ) . ' ' . $this->icon( 'chevron', 16 ) . '</a></section>';
		}
		echo '</div>';
	}
	private function story_stage( $id, $orders, $downloads ) {
		if ( 'digital' === $id ) return '<div class="wcas-story-stage wcas-story-stage-digital" aria-hidden="true"><i></i><i></i><i></i></div>';
		if ( 'subscription' === $id ) return '<div class="wcas-story-stage wcas-story-stage-subscription" aria-hidden="true"><b>✓</b><span>•••</span></div>';
		if ( 'service' === $id ) return '<div class="wcas-story-stage wcas-story-stage-service" aria-hidden="true"><b>1</b><i></i><b>2</b><i></i><b>3</b></div>';
		if ( 'grocery' === $id ) return '<div class="wcas-story-stage wcas-story-stage-grocery" aria-hidden="true"><b>↻</b><span>' . esc_html( number_format_i18n( $orders ) ) . '</span></div>';
		if ( 'wholesale' === $id ) return '<div class="wcas-story-stage wcas-story-stage-wholesale" aria-hidden="true"><i></i><i></i><i></i></div>';
		if ( 'luxury' === $id || 'beauty' === $id ) return '<div class="wcas-story-stage wcas-story-stage-concierge" aria-hidden="true"><b>✦</b><i></i></div>';
		return '<div class="wcas-story-stage wcas-story-stage-commerce" aria-hidden="true"><i></i><i></i><i></i></div>';
	}
	public function orders() {
		$this->title( __( 'Order centre', 'woo-account-studio' ), __( 'Your orders', 'woo-account-studio' ), __( 'View your purchases, follow delivery and order again.', 'woo-account-studio' ) ); $orders = $this->customer_orders();
		if ( ! $orders ) { echo '<div class="wcas-empty-state"><div>' . $this->icon( 'bag', 32 ) . '</div><h2>' . esc_html__( 'No orders yet', 'woo-account-studio' ) . '</h2><p>' . esc_html__( 'When you place an order, its progress will appear here.', 'woo-account-studio' ) . '</p><a class="wcas-button" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Browse products', 'woo-account-studio' ) . '</a></div>'; return; }
		echo '<div class="wcas-orders-list">'; foreach ( $orders as $order ) { $items = $order->get_items(); $first = reset( $items ); $product = $first ? $first->get_product() : false; $image = $product ? $product->get_image( 'thumbnail' ) : wc_placeholder_img( 'thumbnail' ); echo '<article class="wcas-order-row"><div class="wcas-product-thumb">' . wp_kses_post( $image ) . '</div><div class="wcas-order-main"><span>' . esc_html( wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) ) ) . '</span><h2>#' . esc_html( $order->get_order_number() ) . '</h2><p>' . esc_html( $first ? $first->get_name() : __( 'Order items', 'woo-account-studio' ) ) . ( count( $items ) > 1 ? ' +' . ( count( $items ) - 1 ) : '' ) . '</p></div><div class="wcas-order-meta"><mark class="wcas-status ' . esc_attr( $order->get_status() ) . '">' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</mark><strong>' . wp_kses_post( $order->get_formatted_order_total() ) . '</strong></div><a class="wcas-row-link" aria-label="' . esc_attr__( 'View order', 'woo-account-studio' ) . '" href="' . esc_url( $this->account_url( 'view-order' ) . $order->get_id() . '/' ) . '">' . $this->icon( 'chevron' ) . '</a></article>'; } echo '</div>';
	}
	public function view_order() {
		global $wp; $id = absint( $wp->query_vars['view-order'] ?? 0 ); $order = wc_get_order( $id );
		if ( ! $order || (int) $order->get_user_id() !== get_current_user_id() ) { wc_print_notice( __( 'Order not found.', 'woo-account-studio' ), 'error' ); return; }
		$this->title( __( 'Order details', 'woo-account-studio' ), sprintf( __( 'Order #%s', 'woo-account-studio' ), $order->get_order_number() ), sprintf( __( '%s · %s', 'woo-account-studio' ), wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) ), wc_get_order_status_name( $order->get_status() ) ) );
		echo '<section class="wcas-detail-progress"><span class="is-done"><b>✓</b>' . esc_html__( 'Order placed', 'woo-account-studio' ) . '</span><i></i><span class="is-done"><b>✓</b>' . esc_html__( 'Confirmed', 'woo-account-studio' ) . '</span><i></i><span class="is-current"><b>3</b>' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</span></section><section class="wcas-detail-card"><div class="wcas-detail-card-head"><h2>' . esc_html__( 'Items in this order', 'woo-account-studio' ) . '</h2><mark class="wcas-status ' . esc_attr( $order->get_status() ) . '">' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</mark></div>';
		foreach ( $order->get_items() as $item ) { $product = $item->get_product(); echo '<div class="wcas-line-item"><div>' . wp_kses_post( $product ? $product->get_image( 'thumbnail' ) : wc_placeholder_img( 'thumbnail' ) ) . '</div><p><strong>' . esc_html( $item->get_name() ) . '</strong><span>' . sprintf( esc_html__( 'Quantity: %s', 'woo-account-studio' ), $item->get_quantity() ) . '</span></p><b>' . wp_kses_post( $order->get_formatted_line_subtotal( $item ) ) . '</b></div>'; }
		echo '<div class="wcas-total"><span>' . esc_html__( 'Total paid', 'woo-account-studio' ) . '</span><strong>' . wp_kses_post( $order->get_formatted_order_total() ) . '</strong></div></section><section class="wcas-detail-columns"><article><span>' . esc_html__( 'Delivery address', 'woo-account-studio' ) . '</span><p>' . wp_kses_post( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() ) . '</p></article><article><span>' . esc_html__( 'Need help with this order?', 'woo-account-studio' ) . '</span><p>' . esc_html__( 'Our support team can help with delivery, returns or payments.', 'woo-account-studio' ) . '</p><a href="' . esc_url( $this->config()['support_url'] ?: $this->account_url() ) . '">' . esc_html__( 'Contact support', 'woo-account-studio' ) . '</a></article></section>';
	}
	public function downloads() {
		$this->title( __( 'Digital library', 'woo-account-studio' ), __( 'My library', 'woo-account-studio' ), __( 'Your purchased files, courses and digital products.', 'woo-account-studio' ) ); $downloads = wc_get_customer_available_downloads( get_current_user_id() );
		if ( ! $downloads ) { echo '<div class="wcas-empty-state"><div>' . $this->icon( 'download', 32 ) . '</div><h2>' . esc_html__( 'Your library is empty', 'woo-account-studio' ) . '</h2><p>' . esc_html__( 'Your downloadable purchases will be kept safely here.', 'woo-account-studio' ) . '</p></div>'; return; }
		echo '<div class="wcas-library-grid">'; foreach ( $downloads as $download ) echo '<article><div class="wcas-file-icon">' . $this->icon( 'download' ) . '</div><span>' . esc_html( $download['product_name'] ) . '</span><h2>' . esc_html( $download['download_name'] ) . '</h2><p>' . ( $download['downloads_remaining'] ? sprintf( esc_html__( '%s downloads remaining', 'woo-account-studio' ), $download['downloads_remaining'] ) : esc_html__( 'Unlimited downloads', 'woo-account-studio' ) ) . '</p><a class="wcas-button" href="' . esc_url( $download['download_url'] ) . '">' . esc_html__( 'Download', 'woo-account-studio' ) . '</a></article>'; echo '</div>';
	}
	public function addresses() {
		$this->title( __( 'Delivery details', 'woo-account-studio' ), __( 'Your addresses', 'woo-account-studio' ), __( 'Keep delivery and billing details up to date.', 'woo-account-studio' ) );
		$current = get_query_var( 'edit-address' );
		if ( in_array( $current, array( 'billing', 'shipping' ), true ) ) { echo '<div class="wcas-native-form">'; do_action( 'woocommerce_account_edit-address_endpoint', $current ); echo '</div>'; return; }
		echo '<div class="wcas-address-grid">'; foreach ( array( 'billing', 'shipping' ) as $type ) { $address = wc_get_account_formatted_address( $type ); echo '<article class="wcas-address-card"><div class="wcas-address-card-head">' . $this->icon( 'pin' ) . '<span>' . esc_html( 'billing' === $type ? __( 'Billing address', 'woo-account-studio' ) : __( 'Shipping address', 'woo-account-studio' ) ) . '</span></div><p>' . ( $address ? wp_kses_post( $address ) : esc_html__( 'No address saved yet.', 'woo-account-studio' ) ) . '</p><a href="' . esc_url( $this->account_url( 'edit-address' ) . $type . '/' ) . '">' . esc_html__( 'Edit address', 'woo-account-studio' ) . '</a></article>'; } echo '</div>';
	}
	public function profile() { $this->title( __( 'Personal area', 'woo-account-studio' ), __( 'Profile & security', 'woo-account-studio' ), __( 'Manage your personal details and password.', 'woo-account-studio' ) ); echo '<div class="wcas-native-form">'; wc_get_template( 'myaccount/form-edit-account.php' ); echo '</div>'; }

	public function admin_menu() { add_submenu_page( 'woocommerce', __( 'Account Studio', 'woo-account-studio' ), __( 'Account Studio', 'woo-account-studio' ), 'manage_woocommerce', 'woo-account-studio', array( $this, 'render_admin_page' ) ); }
	public function settings() { register_setting( 'wcas_settings_group', $this->option_key, array( $this, 'sanitize_settings' ) ); }
	public function settings_capability() { return 'manage_woocommerce'; }

	private function sanitize_base( $input ) {
		$defaults = $this->defaults();
		$out = $defaults;
		$out['enabled'] = isset( $input['enabled'] ) ? 'yes' : 'no';
		$out['experience'] = in_array( $input['experience'] ?? '', array( 'commerce', 'luxury', 'digital', 'subscription', 'service' ), true ) ? $input['experience'] : $defaults['experience'];
		$out['language_mode'] = in_array( $input['language_mode'] ?? '', array( 'auto', 'ar', 'en' ), true ) ? $input['language_mode'] : 'auto';
		$out['logo_source'] = in_array( $input['logo_source'] ?? '', array( 'site', 'upload', 'none' ), true ) ? $input['logo_source'] : 'site';
		$out['logo_id'] = absint( $input['logo_id'] ?? 0 );
		foreach ( array( 'brand_name', 'brand_tagline', 'support_label', 'label_dashboard', 'label_orders', 'label_library', 'label_addresses', 'label_profile', 'label_more' ) as $key ) $out[ $key ] = sanitize_text_field( $input[ $key ] ?? '' );
		$out['support_url'] = esc_url_raw( $input['support_url'] ?? '' );
		foreach ( array( 'primary', 'accent', 'background', 'surface', 'text', 'sidebar' ) as $color ) $out[ $color ] = sanitize_hex_color( $input[ $color ] ?? '' ) ?: $defaults[ $color ];
		$out['radius'] = min( 32, max( 0, absint( $input['radius'] ?? $defaults['radius'] ) ) );
		$out['content_width'] = min( 1440, max( 760, absint( $input['content_width'] ?? $defaults['content_width'] ) ) );
		$out['font_scale'] = min( 120, max( 85, absint( $input['font_scale'] ?? $defaults['font_scale'] ) ) );
		$out['navigation'] = in_array( $input['navigation'] ?? '', array( 'sidebar', 'compact' ), true ) ? $input['navigation'] : 'sidebar';
		foreach ( array( 'show_order_focus', 'show_action_cards', 'show_rewards', 'show_recommendations', 'enable_motion' ) as $flag ) $out[ $flag ] = isset( $input[ $flag ] ) ? 'yes' : 'no';
		$out['custom_css'] = current_user_can( 'unfiltered_html' ) ? wp_strip_all_tags( $input['custom_css'] ?? '' ) : '';
		return $out;
	}

	private function admin_input( $settings, $key, $label, $type = 'text', $choices = array(), $description = '' ) {
		$name = esc_attr( $this->option_key . '[' . $key . ']' ); $value = $settings[ $key ] ?? '';
		echo '<div class="wcas-control">';
		if ( 'checkbox' === $type ) {
			echo '<label class="wcas-switch"><input type="checkbox" name="' . $name . '" ' . checked( 'yes', $value, false ) . '><span></span><b>' . esc_html( $label ) . '</b></label>';
		} else {
			echo '<label for="wcas-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label>';
			if ( 'select' === $type ) { echo '<select id="wcas-' . esc_attr( $key ) . '" name="' . $name . '">'; foreach ( $choices as $id => $choice ) echo '<option value="' . esc_attr( $id ) . '" ' . selected( $value, $id, false ) . '>' . esc_html( $choice ) . '</option>'; echo '</select>'; }
			elseif ( 'textarea' === $type ) echo '<textarea id="wcas-' . esc_attr( $key ) . '" name="' . $name . '" rows="5">' . esc_textarea( $value ) . '</textarea>';
			else echo '<input id="wcas-' . esc_attr( $key ) . '" type="' . esc_attr( $type ) . '" name="' . $name . '" value="' . esc_attr( $value ) . '">';
		}
		if ( $description ) echo '<p>' . esc_html( $description ) . '</p>';
		echo '</div>';
	}

	public function sanitize_settings( $input ) {
		$out = $this->sanitize_base( $input );
		$defaults = $this->defaults();
		$out['experience'] = array_key_exists( $input['experience'] ?? '', $this->presets() ) ? $input['experience'] : 'commerce';
		$out['appearance'] = in_array( $input['appearance'] ?? '', array( 'light', 'dark' ), true ) ? $input['appearance'] : 'light';
		$out['mobile_nav'] = in_array( $input['mobile_nav'] ?? '', array( 'dock', 'minimal' ), true ) ? $input['mobile_nav'] : 'dock';
		$out['mobile_nav_style'] = in_array( $input['mobile_nav_style'] ?? '', array( 'glass', 'pill', 'split', 'solid', 'outline', 'minimal' ), true ) ? $input['mobile_nav_style'] : $defaults['mobile_nav_style'];
		$out['mobile_nav_labels'] = isset( $input['mobile_nav_labels'] ) ? 'yes' : 'no';
		$out['mobile_icon_style'] = in_array( $input['mobile_icon_style'] ?? '', array( 'line', 'bold', 'orbit', 'soft' ), true ) ? $input['mobile_icon_style'] : $defaults['mobile_icon_style'];
		$out['hide_page_title'] = isset( $input['hide_page_title'] ) ? 'yes' : 'no';
		$icons = $this->mobile_icon_options();
		foreach ( array( 'dashboard', 'orders', 'library', 'addresses', 'profile', 'more' ) as $item ) {
			$key = 'mobile_icon_' . $item;
			$out[ $key ] = isset( $icons[ $input[ $key ] ?? '' ] ) ? $input[ $key ] : $defaults[ $key ];
		}
		$out['hero_title'] = sanitize_text_field( $input['hero_title'] ?? '' );
		$out['hero_description'] = sanitize_textarea_field( $input['hero_description'] ?? '' );
		$out['show_template_story'] = isset( $input['show_template_story'] ) ? 'yes' : 'no';
		if ( '' === $out['brand_name'] ) $out['brand_name'] = get_bloginfo( 'name' );
		return wp_parse_args( $out, $defaults );
	}

	private function template_cards( $settings ) {
		foreach ( $this->presets() as $id => $preset ) {
			$palette = wp_json_encode( array_intersect_key( $preset, array_flip( array( 'primary', 'accent', 'background', 'surface', 'text', 'sidebar' ) ) ) );
			echo '<label class="wcas-template-card ' . ( $settings['experience'] === $id ? 'is-selected' : '' ) . '" data-palette="' . esc_attr( $palette ) . '" data-template="' . esc_attr( $id ) . '"><input type="radio" name="wcas_settings[experience]" value="' . esc_attr( $id ) . '" ' . checked( $settings['experience'], $id, false ) . '><span class="wcas-template-art wcas-template-art-' . esc_attr( $id ) . '"><i></i><b></b><em></em><small></small></span><span class="wcas-template-meta"><b>' . esc_html( $preset['name'] ) . '</b><small>' . esc_html( $preset['tag'] ) . '</small><p>' . esc_html( $preset['description'] ) . '</p></span><span class="wcas-template-check">✓</span></label>';
		}
	}

	public function render_admin_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) return;
		$s = $this->config(); $preview_url = add_query_arg( 'wcas-preview', '1', $this->account_url() );
		?>
		<div class="wrap wcas-studio-admin wcas-studio-v4">
			<header class="wcas-studio-hero"><div><span><?php esc_html_e( 'Developed by Sky Athena', 'woo-account-studio' ); ?></span><h1><?php esc_html_e( 'Template Studio', 'woo-account-studio' ); ?> <em>3.2</em></h1><p><?php esc_html_e( 'Choose an experience, shape it around your brand, then preview the real customer hub before publishing.', 'woo-account-studio' ); ?></p></div><a class="button button-secondary" href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open live account', 'woo-account-studio' ); ?></a></header>
			<form method="post" action="options.php" id="wcas-studio-form">
				<?php settings_fields( 'wcas_settings_group' ); ?>
				<div class="wcas-studio-layout"><main class="wcas-studio-main">
					<section class="wcas-portal-section" id="wcas-template-portal"><div class="wcas-portal-heading"><span>01</span><div><h2><?php esc_html_e( 'Template portal', 'woo-account-studio' ); ?></h2><p><?php esc_html_e( 'These are different customer journeys, not colour variations.', 'woo-account-studio' ); ?></p></div></div><div class="wcas-template-grid"><?php $this->template_cards( $s ); ?></div></section>
					<details class="wcas-portal-section" open><summary><span>02</span><div><h2><?php esc_html_e( 'Brand and visual system', 'woo-account-studio' ); ?></h2><p><?php esc_html_e( 'Use the site identity by default or take full control.', 'woo-account-studio' ); ?></p></div><b>⌄</b></summary><div class="wcas-portal-content"><div class="wcas-controls-grid"><?php
						$this->admin_input( $s, 'enabled', __( 'Enable Customer Hub', 'woo-account-studio' ), 'checkbox' );
						$this->admin_input( $s, 'language_mode', __( 'Interface language', 'woo-account-studio' ), 'select', array( 'auto' => __( 'Follow site language', 'woo-account-studio' ), 'ar' => __( 'Arabic', 'woo-account-studio' ), 'en' => __( 'English', 'woo-account-studio' ) ) );
						$this->admin_input( $s, 'brand_name', __( 'Brand name', 'woo-account-studio' ) );
						$this->admin_input( $s, 'brand_tagline', __( 'Brand tagline', 'woo-account-studio' ) );
						$this->admin_input( $s, 'logo_source', __( 'Logo source', 'woo-account-studio' ), 'select', array( 'site' => __( 'Use the site logo', 'woo-account-studio' ), 'upload' => __( 'Use a custom logo', 'woo-account-studio' ), 'none' => __( 'Use initials only', 'woo-account-studio' ) ) );
						$this->admin_input( $s, 'appearance', __( 'Appearance', 'woo-account-studio' ), 'select', array( 'light' => __( 'Light', 'woo-account-studio' ), 'dark' => __( 'Dark', 'woo-account-studio' ) ) );
					?></div><div class="wcas-logo-upload"><input type="hidden" id="wcas-logo-id" name="wcas_settings[logo_id]" value="<?php echo esc_attr( $s['logo_id'] ); ?>"><button type="button" class="button" id="wcas-select-logo"><?php esc_html_e( 'Select custom logo', 'woo-account-studio' ); ?></button><button type="button" class="button-link-delete" id="wcas-remove-logo"><?php esc_html_e( 'Remove', 'woo-account-studio' ); ?></button><span id="wcas-logo-preview"><?php echo $s['logo_id'] ? wp_get_attachment_image( absint( $s['logo_id'] ), 'thumbnail' ) : ''; ?></span></div><div class="wcas-controls-grid wcas-color-grid wcas-token-grid"><?php
						foreach ( array( 'primary' => __( 'Primary', 'woo-account-studio' ), 'accent' => __( 'Accent', 'woo-account-studio' ), 'background' => __( 'Background', 'woo-account-studio' ), 'surface' => __( 'Card surface', 'woo-account-studio' ), 'text' => __( 'Text', 'woo-account-studio' ), 'sidebar' => __( 'Sidebar', 'woo-account-studio' ) ) as $key => $label ) $this->admin_input( $s, $key, $label, 'color' );
						$this->admin_input( $s, 'radius', __( 'Corner radius', 'woo-account-studio' ), 'number', array(), __( '0–32 pixels', 'woo-account-studio' ) );
						$this->admin_input( $s, 'content_width', __( 'Content width', 'woo-account-studio' ), 'number', array(), __( '760–1440 pixels', 'woo-account-studio' ) );
						$this->admin_input( $s, 'font_scale', __( 'Text scale', 'woo-account-studio' ), 'number', array(), __( '85–120 percent', 'woo-account-studio' ) );
					?></div></div></details>
					<details class="wcas-portal-section" open><summary><span>03</span><div><h2><?php esc_html_e( 'Customer journey', 'woo-account-studio' ); ?></h2><p><?php esc_html_e( 'Control the priority and presence of each home-screen module.', 'woo-account-studio' ); ?></p></div><b>⌄</b></summary><div class="wcas-portal-content"><div class="wcas-controls-grid"><?php
						$this->admin_input( $s, 'hero_title', __( 'Custom welcome title', 'woo-account-studio' ) );
						$this->admin_input( $s, 'hero_description', __( 'Custom welcome description', 'woo-account-studio' ), 'textarea' );
						$this->admin_input( $s, 'show_order_focus', __( 'Show priority order card', 'woo-account-studio' ), 'checkbox' );
						$this->admin_input( $s, 'show_template_story', __( 'Show template-specific module', 'woo-account-studio' ), 'checkbox' );
						$this->admin_input( $s, 'show_action_cards', __( 'Show quick actions', 'woo-account-studio' ), 'checkbox' );
						$this->admin_input( $s, 'show_rewards', __( 'Show benefits module', 'woo-account-studio' ), 'checkbox' );
					?></div></div></details>
					<details class="wcas-portal-section"><summary><span>04</span><div><h2><?php esc_html_e( 'Navigation, content and motion', 'woo-account-studio' ); ?></h2><p><?php esc_html_e( 'Tune the desktop shell and the mobile app dock independently.', 'woo-account-studio' ); ?></p></div><b>⌄</b></summary><div class="wcas-portal-content"><div class="wcas-controls-grid"><?php
						$this->admin_input( $s, 'navigation', __( 'Desktop navigation', 'woo-account-studio' ), 'select', array( 'sidebar' => __( 'Full sidebar', 'woo-account-studio' ), 'compact' => __( 'Compact sidebar', 'woo-account-studio' ) ) );
						$this->admin_input( $s, 'support_url', __( 'Support or WhatsApp URL', 'woo-account-studio' ), 'url' );
						$this->admin_input( $s, 'enable_motion', __( 'Subtle interface motion', 'woo-account-studio' ), 'checkbox' );
						$this->admin_input( $s, 'label_dashboard', __( 'Overview label', 'woo-account-studio' ) );
						$this->admin_input( $s, 'label_orders', __( 'Orders label', 'woo-account-studio' ) );
						$this->admin_input( $s, 'label_library', __( 'Context hub label', 'woo-account-studio' ) );
						$this->admin_input( $s, 'label_addresses', __( 'Addresses label', 'woo-account-studio' ) );
						$this->admin_input( $s, 'label_profile', __( 'Profile label', 'woo-account-studio' ) );
						$this->admin_input( $s, 'label_more', __( 'More label', 'woo-account-studio' ) );
					?></div><section class="wcas-mobile-customizer" aria-labelledby="wcas-mobile-customizer-title"><div class="wcas-mobile-customizer-heading"><span><?php echo wp_kses_post( $this->icon( 'sparkles', 18 ) ); ?></span><div><h3 id="wcas-mobile-customizer-title"><?php esc_html_e( 'Mobile bottom bar studio', 'woo-account-studio' ); ?></h3><p><?php esc_html_e( 'Choose a distinct navigation style, decide whether labels appear, and tune every icon customers touch.', 'woo-account-studio' ); ?></p></div></div><div class="wcas-controls-grid wcas-mobile-layout-controls"><?php
						$this->admin_input( $s, 'mobile_nav', __( 'Mobile bar placement', 'woo-account-studio' ), 'select', array( 'dock' => __( 'Floating above page content', 'woo-account-studio' ), 'minimal' => __( 'Attached to screen edge', 'woo-account-studio' ) ) );
						$this->admin_input( $s, 'mobile_nav_style', __( 'Bottom bar template', 'woo-account-studio' ), 'select', array( 'glass' => __( 'Glass dock', 'woo-account-studio' ), 'pill' => __( 'Pill navigator', 'woo-account-studio' ), 'split' => __( 'Split action dock', 'woo-account-studio' ), 'solid' => __( 'Solid app bar', 'woo-account-studio' ), 'outline' => __( 'Outline bar', 'woo-account-studio' ), 'minimal' => __( 'Bare essentials', 'woo-account-studio' ) ), __( 'Each template changes the bar shape, active state and visual weight.', 'woo-account-studio' ) );
						$this->admin_input( $s, 'mobile_nav_labels', __( 'Show labels under mobile icons', 'woo-account-studio' ), 'checkbox' );
						$this->admin_input( $s, 'mobile_icon_style', __( 'Icon treatment', 'woo-account-studio' ), 'select', array( 'line' => __( 'Fine line', 'woo-account-studio' ), 'bold' => __( 'Bold line', 'woo-account-studio' ), 'orbit' => __( 'Orbit accent', 'woo-account-studio' ), 'soft' => __( 'Soft filled', 'woo-account-studio' ) ) );
						$this->admin_input( $s, 'hide_page_title', __( 'Hide the theme page title', 'woo-account-studio' ), 'checkbox', array(), __( 'Removes the large My Account title or hero supplied by your theme above the customer hub.', 'woo-account-studio' ) );
					?></div><div class="wcas-mobile-icon-library"><div class="wcas-mobile-icon-library-heading"><div><h3><?php esc_html_e( 'Icon set for the bottom bar', 'woo-account-studio' ); ?></h3><p><?php esc_html_e( 'The three main destinations adapt to the chosen account template. These choices keep their icon language consistent.', 'woo-account-studio' ); ?></p></div><span><?php echo wp_kses_post( $this->icon( 'grid', 20 ) ); ?></span></div><div class="wcas-controls-grid wcas-icon-controls"><?php
						$mobile_icons = $this->mobile_icon_options();
						$this->admin_input( $s, 'mobile_icon_dashboard', __( 'Overview icon', 'woo-account-studio' ), 'select', $mobile_icons );
						$this->admin_input( $s, 'mobile_icon_orders', __( 'Orders icon', 'woo-account-studio' ), 'select', $mobile_icons );
						$this->admin_input( $s, 'mobile_icon_library', __( 'Library icon', 'woo-account-studio' ), 'select', $mobile_icons );
						$this->admin_input( $s, 'mobile_icon_addresses', __( 'Addresses icon', 'woo-account-studio' ), 'select', $mobile_icons );
						$this->admin_input( $s, 'mobile_icon_profile', __( 'Profile icon', 'woo-account-studio' ), 'select', $mobile_icons );
						$this->admin_input( $s, 'mobile_icon_more', __( 'More menu icon', 'woo-account-studio' ), 'select', $mobile_icons );
					?></div></div></section></div></details>
					<details class="wcas-portal-section"><summary><span>05</span><div><h2><?php esc_html_e( 'Advanced styling', 'woo-account-studio' ); ?></h2><p><?php esc_html_e( 'For design refinements after the design system controls.', 'woo-account-studio' ); ?></p></div><b>⌄</b></summary><div class="wcas-portal-content"><?php $this->admin_input( $s, 'custom_css', __( 'Additional CSS', 'woo-account-studio' ), 'textarea', array(), __( 'Use selectors inside .wcas-app only.', 'woo-account-studio' ) ); ?></div></details>
					<?php submit_button( __( 'Save and publish changes', 'woo-account-studio' ), 'primary large' ); ?>
				</main>
					<aside class="wcas-live-preview-panel"><div class="wcas-preview-head"><div><span><?php esc_html_e( 'Live preview', 'woo-account-studio' ); ?></span><strong><?php esc_html_e( 'Your real account', 'woo-account-studio' ); ?></strong></div><div class="wcas-device-toggle"><button type="button" data-preview-device="small" aria-label="<?php esc_attr_e( 'Small mobile preview', 'woo-account-studio' ); ?>" aria-pressed="false">▯</button><button type="button" data-preview-device="mobile" class="is-active" aria-label="<?php esc_attr_e( 'Mobile preview', 'woo-account-studio' ); ?>" aria-pressed="true">▯</button><button type="button" data-preview-device="desktop" aria-label="<?php esc_attr_e( 'Desktop preview', 'woo-account-studio' ); ?>" aria-pressed="false">▭</button></div></div><div class="wcas-preview-stage" data-device="mobile"><iframe id="wcas-live-iframe" title="<?php esc_attr_e( 'Live customer hub preview', 'woo-account-studio' ); ?>" src="<?php echo esc_url( $preview_url ); ?>"></iframe><div class="wcas-preview-loader"><span></span><?php esc_html_e( 'Loading your account preview…', 'woo-account-studio' ); ?></div></div><p class="wcas-preview-note"><?php esc_html_e( 'Changes are reflected here before you save. The preview uses your current administrator account and does not publish anything.', 'woo-account-studio' ); ?></p></aside>
				</div>
			</form>
		</div>
		<?php
	}
}
