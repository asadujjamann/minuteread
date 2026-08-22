=== MinuteRead - Reading Time for Posts ===
Contributors:      asad5516
Tags:              reading time, estimated reading time, read time, reading duration, minute read
Requires at least: 5.0
Tested up to:      7.1
Stable tag:        1.1.0
Requires PHP:      7.4
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Show estimated reading time on posts, pages, and custom post types. Customize label, format, position, and reading speed.

== Description ==

MinuteRead adds an estimated reading time label to your WordPress posts - helping readers decide whether to dive in or save it for later.

After activating the plugin, reading time appears automatically on your posts - and on any other post type you enable. Everything is configurable from **Settings > MinuteRead**.

**Features:**

* Choose which post types show reading time - posts, pages, or any custom post type
* Choose to show reading time before or after the content
* Configurable words-per-minute reading speed (default: 200, range: 50-1000)
* Custom label text - change "Estimated Reading Time" to anything you like
* Custom time format - use "%d min", "%d minutes", or any text with %d as placeholder
* Enable / disable without deactivating the plugin
* `[minuteread_time]` shortcode - place reading time anywhere, or `[minuteread_time id="123"]` for a specific post
* Works with non-Latin scripts (Bengali, Arabic, CJK, etc.)
* Lightweight: no jQuery, no external requests
* Developer-friendly: `minuteread_reading_time_output`, `minuteread_word_count`, `minuteread_output_html` filters
* Translation-ready

**Shortcode usage:**

Place `[minuteread_time]` in any post, page, or widget to show the reading time
for the current post. To target a specific post, pass its ID:
`[minuteread_time id="123"]`.

== Installation ==

1. Upload the `minuteread` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Visit **Settings > MinuteRead** to configure the plugin

== Frequently Asked Questions ==

= Does it work with all themes? =
Yes. The reading time is injected via the standard `the_content` filter, which is supported by every properly coded WordPress theme.

= Can I change the words-per-minute speed? =
Yes. Go to **Settings > MinuteRead** and update the "Words per minute" field. The default is 200 (average adult reading speed). Accepted range: 50-1000.

= Can I change the label text? =
Yes. Use the **Label** field in Settings to replace "Estimated Reading Time" with any text you prefer - including text in your own language.

= Can I change how the time is displayed? =
Yes. Use the **Time Format** field. The placeholder `%d` will be replaced with the number of minutes. Examples: `%d min`, `%d minutes`, `%d dakika`.

= Can I style the reading time? =
Yes. The output is wrapped in `.minuteread-reading-time` - a `<p>` for the
automatic output, a `<span>` for the shortcode. Add your own rules under
Appearance > Customize > Additional CSS (classic themes) or Appearance >
Editor > Styles > Additional CSS (block themes). For full control of the
markup, use the `minuteread_output_html` filter.

= Can I show the reading time only in specific locations? =
Yes. Untick every post type under "Show On" to turn off automatic insertion, then
place the `[minuteread_time]` shortcode exactly where you want it. Inside the loop
it uses the current post; outside it, pass an ID: `[minuteread_time id="123"]`.

= Does it work with Bengali, Arabic, or other non-Latin scripts? =
Yes. Words are counted in a Unicode-aware way, so text that mixes scripts - Bengali
with a few English terms, for example - is counted correctly. Chinese and Japanese,
which do not put spaces between words, are counted by character instead.

= Does it work with custom post types? =
Yes. Go to **Settings > MinuteRead** and tick the post types you want under
"Show On" - every public post type registered on your site is listed. Developers
can also override the list with the `minuteread_post_types` filter.

= Is it translation-ready? =
Yes. WordPress automatically loads translations for plugins hosted on WordPress.org.

== Why Use This Plugin? ==

- Improve user engagement
- Reduce bounce rate
- Help readers estimate time commitment

== Screenshots ==

1. Reading time displayed before post content on the frontend.
2. Admin settings page - choose post types, label, format, position, and reading speed.

== Changelog ==

= 1.1.0 =
* New: choose which post types show reading time (Settings > MinuteRead > Show On)
* New: `minuteread_post_types` filter for developers
* New: the shortcode accepts an ID - `[minuteread_time id="123"]`
* Fixed: wrong reading time on posts that mix a non-Latin script with a few Latin words, such as Bengali text containing English terms
* Fixed: Chinese and Japanese content is now counted by character instead of reporting one minute
* Fixed: the stylesheet now loads when the shortcode is used outside post content
* Fixed: the post type setting is removed on uninstall
* Changed: the reading time no longer forces its own bottom margin - spacing now follows your theme
* Docs: documented the CSS class and the output filter
* Tested up to WordPress 7.1
* Requires PHP 7.4 or higher (matches WordPress core's own minimum since 7.0)

= 1.0.0 =
* Initial release
* Automatic reading time display on single posts
* Before / after content position option
* Configurable words-per-minute setting (50-1000)
* Custom label and time format
* Enable / disable toggle
* Non-Latin language support (Bengali, Arabic, CJK)
* [minuteread_time] shortcode support
* Developer filters: minuteread_reading_time_output, minuteread_word_count, minuteread_output_html
* Translation-ready

== Upgrade Notice ==

= 1.1.0 =
Adds custom post type support and fixes reading times on posts that mix
Bengali, Arabic or CJK text with English. The reading time no longer forces
its own bottom margin, so spacing now follows your theme. Existing settings
are preserved.

= 1.0.0 =
Initial release.
