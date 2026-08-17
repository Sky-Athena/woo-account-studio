# Woo Account Studio

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
![WordPress](https://img.shields.io/badge/WordPress-6.5%2B-21759b)
![WooCommerce](https://img.shields.io/badge/WooCommerce-8.0%2B-96588a)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)

An application-style, mobile-first WooCommerce customer hub that replaces the standard **My Account** view with a complete post-purchase experience.

Developed by **[Sky Athena Kft.](https://skyathena.com)**

---

## Why

The default WooCommerce account page is a list of links. Customers who come back after buying — to track an order, re-download a file, reorder, or fix an address — land on a page that was never designed for that moment, and almost never designed for a phone.

Woo Account Studio rebuilds that page around the thing the customer actually came for: the active order, its status, and its next action.

## Features

**Eight customer journeys** — Commerce Hub, Luxury Concierge, Digital Library, Subscription Club, Service & Booking, Beauty & Wellness, Quick Reorder, Trade Portal. Each has its own desktop shell and primary modules — a rail, an editorial header, a tabbed library, a club or business-console treatment — not just alternative colours.

**Order-first overview** — the active order, its current status and its next action come before anything else.

**Full account surface** — custom order centre, order details, digital library, address cards, profile view and support entry point.

**Mobile bottom bar studio** — a four-destination floating dock with 58px touch targets and an accessible "More" bottom sheet. Six genuinely different layouts (Glass, Pill, Split, Solid, Outline, Minimal), floating or edge-attached placement, labels on/off, four icon treatments, and individual icon selection per destination. It never creates a competing mobile drawer and withdraws automatically near the site footer.

**Template Studio** — a real iframe preview of the current account page. Template shell, module visibility, brand, palette, layout and motion changes are sent into the preview before saving.

**Design system** — colours, card radius, content width, text scale, sidebar mode, light/dark mode, content modules, labels, support URL, optional motion, and scoped custom CSS.

**Bilingual** — Arabic and English interface modes, automatic RTL layout, and compatibility with the normal WordPress translation workflow.

**Branding** — the site logo by default, or a custom Media Library logo, or initials-only mode.

**Theme title suppression** — an optional switch that removes the oversized My Account title/hero created by common themes and page builders, without touching the hub's own content.

## Requirements

| | |
|---|---|
| WordPress | 6.5 or newer |
| WooCommerce | 8.0 or newer |
| PHP | 7.4 or newer |

## Installation

**From the ZIP**

1. Download the latest release ZIP from the [Releases](https://github.com/Sky-Athena/woo-account-studio/releases) page.
2. In WordPress go to **Plugins → Add New → Upload Plugin** and select the ZIP.
3. Activate **Woo Account Studio** (WooCommerce must already be active).

**Manually**

```bash
cd wp-content/plugins
git clone https://github.com/Sky-Athena/woo-account-studio.git
```

Then activate the plugin from **Plugins**.

## Getting started

1. Open **WooCommerce → Account Studio**.
2. Choose a journey in Template Studio and inspect the live preview.
3. Adjust brand, palette, modules and the mobile bar, then save.
4. Open the WooCommerce **My Account** page while logged in to review the live result.

## Data and privacy

The plugin renders native WooCommerce endpoints and customer data. It does not duplicate orders, downloads or customer records, stores no personal data of its own, and makes no external requests. Settings live in a single `wcas_settings` option, removed on uninstall.

## Compatibility

Compatibility with WooCommerce **High-Performance Order Storage (HPOS)** and the **cart/checkout blocks** is declared explicitly.

Any theme that replaces `myaccount/my-account.php` with bespoke functionality should be tested on staging before the hub is enabled on a production store.

## Contributing

Issues and pull requests are welcome. Please open an issue describing the change before submitting a large pull request.

## License

Released under the [GNU General Public License v2.0 or later](LICENSE), the same license as WordPress and WooCommerce.

Copyright © 2026 Sky Athena Kft.
