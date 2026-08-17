# Changelog

All notable changes to Woo Account Studio are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project uses [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.2.0] — 2026-08-18

First public release under Sky Athena.

### Added

- Eight customer journeys with distinct desktop shells: Commerce Hub, Luxury Concierge, Digital Library, Subscription Club, Service & Booking, Beauty & Wellness, Quick Reorder, Trade Portal.
- Order-first customer overview with active order, status and next action.
- Custom order centre, order details, digital library, address cards, profile view and support entry point.
- Mobile bottom bar studio: six layouts, floating or edge-attached placement, optional labels, four icon treatments and per-destination icon selection.
- Template Studio with a live iframe preview of the account page.
- Design system controls: colours, radius, content width, text scale, sidebar mode, light/dark mode, modules, labels, support URL, motion and scoped custom CSS.
- Arabic and English interface modes with automatic RTL layout.
- Logo handling: site logo, custom Media Library logo, or initials-only mode.
- Optional theme page-title suppression on the account page.
- Declared compatibility with WooCommerce High-Performance Order Storage and the cart/checkout blocks.
- `uninstall.php` that removes plugin options, including on multisite.

### Changed

- Plugin header completed for WordPress and WooCommerce publishing requirements: plugin URI, minimum WordPress/PHP versions, license fields and WooCommerce version support.
- Admin notice now explains the idle state when WooCommerce is not active, instead of failing silently.

### Removed

- Legacy version 2 settings screen and its unused sanitisation routines.
