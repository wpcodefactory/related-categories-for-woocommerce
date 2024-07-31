=== Related Categories for WooCommerce ===
Contributors: wpcodefactory, omardabbas, karzin, anbinder, algoritmika, kousikmukherjeeli
Tags: woocommerce, related, categories, related categories, woo commerce
Requires at least: 4.7
Tested up to: 6.6
Stable tag: 1.9.7
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add "Related categories" section to single product and/or shop pages in WooCommerce.

== Description ==

**Related Categories for WooCommerce** plugin lets you add "Related categories" section (similar to the standard "Related products" section) to the single product and/or shop (i.e. archives) pages in WooCommerce.

### &#9989; Main Features ###

* Automatically add "related categories" section to the **single product** pages and/or to **shop** pages.
* Set categories **limit** (i.e. number of categories).
* Set number of **columns**.
* **Sort** categories by name, ID, random, count, etc.
* **Automatically relate categories** by siblings, parents, children, etc.
* Set **position** for the "related categories" section.
* **Customize templates**, e.g. set header, footer and item HTML templates.
* Optionally display related categories with a **widget** and/or with a **shortcode**.
* And more...

### &#127942; Premium Version ###

With [Related Categories for WooCommerce Pro](https://wpfactory.com/item/related-categories-for-woocommerce/) you can **manually relate** categories on:

* per **product** basis,
* per product **category** basis,
* per product **tag** basis, and/or
* per product **custom taxonomy** (e.g. product brands) basis.

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/related-categories-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Related Categories".

== Frequently Asked Questions ==

= Does your plugin support multi-language? =

Yes, for WPML and Polylang translations, you can use our `[alg_wc_related_categories_translate]` shortcode directly in Templates.

== Screenshots ==

1. Related Categories for WooCommerce - Frontend.

== Changelog ==

= 1.9.7 - 31/07/2023 =
* WC tested up to: 9.1.
* Tested up to: 6.6.

= 1.9.6 - 26/09/2023 =
* WC tested up to: 8.1.
* Tested up to: 6.3.
* Update plugin logo, banner.

= 1.9.5 - 02/07/2023 =
* Dev – "High-Performance Order Storage (HPOS)" compatibility.

= 1.9.4 - 18/06/2023 =
* WC tested up to: 7.8.
* Tested up to: 6.2.

= 1.9.3 - 14/11/2022 =
* WC tested up to: 7.1.
* Tested up to: 6.1.
* Readme.txt updated.
* Deploy script added.

= 1.9.2 - 13/04/2022 =
* Dev - Single - Position Options - "Before single product" and "After single product" positions added.
* Tested up to: 5.9.
* WC tested up to: 6.4.

= 1.9.1 - 15/11/2021 =
* Dev - Developers - `alg_wc_related_categories_single_product_category_ids` filter added.
* Dev - Settings - Descriptions updated.
* WC tested up to: 5.9.

= 1.9.0 - 22/09/2021 =
* Fix - Possible PHP parse error fixed.
* Dev - All admin settings input is properly sanitized now.
* Dev - Plugin is initialized on the `plugins_loaded` action now.
* Dev - Code refactoring.
* WC tested up to: 5.7.
* Tested up to: 5.8.

= 1.8.1 - 06/05/2021 =
* Fix - Template Options - Template type - Custom - "Hide empty" option fixed.
* WC tested up to: 5.2.

= 1.8.0 - 16/03/2021 =
* Fix - Frontend - `output_related_categories_single()` - `global $product` removed.
* Dev - Advanced - "Transients" options added.
* Dev - Archives - Relate Options - Current - "Always show first" option added.
* Dev - Archives - Template Options - Template type: Custom - `%is_active%` placeholder added.
* Dev - Archives - Advanced - "Visibility" option added. Same option added to the "Related Categories: Archives" widget settings as well.
* Dev - Widget - 'Override "Relate Options" in widget settings' option added.
* Dev - Widget - Template Options - Default values updated.
* Dev - Widget - Settings restyled (section titles added).
* Dev - Settings - Relate Options - Section split into "Relate Options" and "Relate Manually".
* Dev - Settings - Descriptions updated.
* Dev - Settings - `alg_wc_related_categories_after_save_settings` action added.
* Dev - Code refactoring.
* WC tested up to: 5.1.
* Tested up to: 5.7.

= 1.7.0 - 17/01/2021 =
* Fix - Image Options - Image size - Was applied only if the "Placeholder image" option was not empty. This is fixed now.
* Fix - Loading "per product" and "per category" settings only if section ("Single" or "Archives") is enabled.
* Dev - Relate Options - "Per tag" option added.
* Dev - Relate Options - "Per custom taxonomy" option added.
* Dev - Position Options - "Widget" option added.
* Dev - Template Options - "Template type", "Template type: Custom" and "Template type: Custom: Glue" options added.
* Dev - Image Options - "Remove image" option added.
* Dev - Shortcodes - Atts are now customizable in `[alg_wc_related_categories_single]` and `[alg_wc_related_categories_loop]` shortcodes.
* Dev - Localization - `load_plugin_textdomain()` moved to the `init` action.
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.
* Dev - Developers - `alg_wc_related_categories_single` and `alg_wc_related_categories_loop` filters added.
* WC tested up to: 4.9.

= 1.6.0 - 17/12/2020 =
* Dev - Advanced - "Multi-language" options added.
* Dev - Settings - Order by - Description updated.
* Dev - Code refactoring.
* Dev - Free plugin version released.

= 1.5.0 - 14/12/2020 =
* Dev - General Options - Order by - "Count" option added.
* Dev - Image Options - "Image size" option added.
* Dev - Image Options - "Placeholder image" option added.
* Tested up to: 5.6.
* WC tested up to: 4.8.

= 1.4.0 - 17/04/2020 =
* Fix - Settings - Per product - Bug (when empty "Categories" value is not being saved) fixed.
* Dev - Relate Options - Siblings - "Include grandparents" option added (defaults to `yes`).
* Dev - Relate Options - Siblings - "Include top-level" option added (defaults to `no`).
* Dev - Relate Options - Siblings - "Include grandchildren" option added (defaults to `yes`).
* Dev - Relate Options - Children - "Include grandchildren" option added (defaults to `yes`).
* Dev - Relate Options - Parents - "Include grandparents" option added (defaults to `yes`).
* Dev - Single - Advanced Options - 'Hide "Related products"' option added.
* Dev - Settings - Restyled (`show_if_checked` properties added).
* Dev - Settings - "Reset settings" notice updated.
* Dev - Admin action link description updated.
* Dev - Code refactoring.
* Requires at least: 4.7.
* WC tested up to: 4.0.
* Tested up to: 5.4.

= 1.3.3 - 19/02/2020 =
* Dev - `get_related_categories_single()` - Additional `$product` safe checks added (produced log errors on some servers).

= 1.3.2 - 31/01/2020 =
* Fix - Relate Options - Siblings - Current category removed.
* Dev - `[alg_wc_related_categories_single]` and `[alg_wc_related_categories_loop]` shortcodes added (and "Disable" option added to "Position" in both single and archives settings).
* Dev - Admin settings descriptions updated.

= 1.3.1 - 30/01/2020 =
* Dev - Archives - Position - "Before main content" and "After main content" positions added.

= 1.3.0 - 22/01/2020 =
* Dev - "Archives" options section added.
* Dev - "General" options section renamed to "Single".
* Dev - "Plugin enabled" option removed.
* Dev - Code refactoring.
* WC tested up to: 3.9.

= 1.2.0 - 21/01/2020 =
* Dev - Relate Options - "Per category" option added.
* Dev - Relate Options - Per product - "Override" option added.
* Dev - Code refactoring.

= 1.1.0 - 03/01/2020 =
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* WC tested up to: 3.8.
* Tested up to: 5.3.

= 1.0.0 - 13/05/2019 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
