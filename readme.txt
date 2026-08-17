=== Woo Account Studio ===
Contributors: skyathena
Tags: woocommerce, my account, customer dashboard, mobile navigation, account page
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 4.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Replaces the default WooCommerce My Account page with a modern, mobile-first customer hub — eight journeys, a live template studio and seven languages.

== Description ==

Woo Account Studio turns the standard WooCommerce **My Account** page into an application-style customer hub: an order-first overview, a proper order centre, a digital library, address cards, a profile view and a support entry point.

The plugin renders the native WooCommerce endpoints and customer data. It does not duplicate orders, downloads or customer records, and it stores no personal data of its own.

= Eight customer journeys =

Commerce Hub, Luxury Concierge, Digital Library, Subscription Club, Service & Booking, Beauty & Wellness, Quick Reorder and Trade Portal. Each journey has its own desktop shell and primary modules — a rail, an editorial header, a tabbed library, a club treatment or a business console — not just a different colour scheme.

= Seven interface languages =

Arabic, English, Turkish, French, Spanish, German and Italian. The chosen language applies to both the customer hub and Template Studio, independently of the site's global locale. Arabic switches the hub to RTL automatically.

= Mobile bottom bar studio =

A four-destination floating dock with 58px touch targets and an accessible "More" bottom sheet. Eight distinct layouts (Glass, Liquid Glass, Midnight, Pill, Split, Solid, Outline, Minimal), floating or edge-attached placement, labels on or off, active-state treatments, four icon treatments, and individual icon selection for every destination. The dock never creates a competing mobile drawer and withdraws automatically near the site footer.

= Persistent merchant links =

Up to six links you control, shown in the desktop hub and the mobile More surface. Each link takes a built-in icon, an Iconify icon name, or an HTTPS image or SVG URL.

= Template Studio =

A real iframe preview of the current account page. Template shell, module visibility, brand, palette, layout and motion changes are pushed into the preview before you save.

= Design system =

Colours, card radius, content width, text scale, sidebar mode, light and dark mode, content modules, labels, support URL, optional motion, and scoped custom CSS.

= Theme hero control =

Removes the oversized My Account title or banner that many themes and page builders place above the account content, without touching the hub's own content. An optional, validated selector handles theme-specific banners.

= Developed by =

[Sky Athena Kft.](https://skyathena.com) — business development, software and technology solutions, environmental consulting, and digital transformation.

== Installation ==

1. Upload the `woo-account-studio` folder to `/wp-content/plugins/`, or install the ZIP from **Plugins → Add New → Upload Plugin**.
2. Make sure WooCommerce is installed and active.
3. Activate **Woo Account Studio**.
4. Go to **WooCommerce → Account Studio**, pick a journey in Template Studio, check the live preview, then save.
5. Open the WooCommerce **My Account** page while logged in to review the result.

== Frequently Asked Questions ==

= Does it work with my theme? =

In most cases yes. The plugin filters the WooCommerce account template. Any theme that replaces `myaccount/my-account.php` with bespoke functionality should be tested on staging before you enable the hub in production.

= Does it delete or duplicate my orders? =

No. It reads the native WooCommerce order, download and address data and renders it. Deactivating the plugin returns the account page to its previous state.

= Is it compatible with High-Performance Order Storage (HPOS)? =

Yes. Compatibility with HPOS and with the cart/checkout blocks is declared explicitly.

= Which languages are included? =

Arabic, English, Turkish, French, Spanish, German and Italian, bundled with the plugin. You can also translate it through the normal WordPress workflow using the included POT file.

= Does the plugin make external requests? =

Only if you choose an Iconify icon for a merchant link. That icon is then loaded from api.iconify.design on the customer's account page. Built-in icons and self-hosted image URLs make no third-party request.

== Screenshots ==

1. Template portal with a live preview of the Service & Booking journey.
2. The same store on the Beauty & Wellness journey — layout, modules and palette all change.
3. Brand and visual system: logo source, palette tokens, radius, content width and text scale.
4. Customer journey controls for the account home-screen modules.

== Changelog ==

= 4.0.0 =
* Seven bundled interface languages: Arabic, English, Turkish, French, Spanish, German and Italian, applied to both the customer hub and Template Studio.
* Up to six persistent merchant links with built-in icons, Iconify icon names, or HTTPS image/SVG URLs.
* Eight mobile bottom-bar layouts — Liquid Glass and Midnight added — plus active-state treatments.
* Dedicated theme-hero control with an optional, validated selector for theme-specific banners.
* Link to the developer's site on the Plugins screen.
* Removed the legacy version 2 settings screen and its unused sanitisation routines.

= 3.2.0 =
* First public release under Sky Athena.
* Eight customer journeys with distinct desktop shells.
* Mobile bottom bar studio with six layouts and per-destination icons.
* Template Studio with live iframe preview.
* Arabic/English interface modes with automatic RTL.
* Declared HPOS and cart/checkout blocks compatibility.
* Optional theme page-title suppression on the account page.

== Upgrade Notice ==

= 4.0.0 =
Adds five new interface languages, persistent merchant links and two new mobile bar layouts. Existing settings are preserved. Test on staging before updating a live store.
