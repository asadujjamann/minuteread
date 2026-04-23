=== MinuteRead — Reading Time for Posts ===
Contributors:      asad5516
Tags:              reading time, estimated reading time, read time, reading duration, minute read
Requires at least: 5.0
Tested up to:      6.9
Stable tag:        1.0.0
Requires PHP:      7.2
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Show estimated reading time on every post. Customize label, format, position, and reading speed from the settings page.

== Description ==

MinuteRead adds an estimated reading time label to your WordPress posts — helping readers decide whether to dive in or save it for later.

After activating the plugin, reading time appears automatically before every post. Everything is configurable from **Settings > MinuteRead**.

**Features:**

* Automatically displays reading time on all single posts
* Choose to show reading time before or after the content
* Configurable words-per-minute reading speed (default: 200, range: 50–1000)
* Custom label text — change "Estimated Reading Time" to anything you like
* Custom time format — use "%d min", "%d minutes", or any text with %d as placeholder
* Enable / disable without deactivating the plugin
* `[minuteread_time]` shortcode — place reading time anywhere
* Works with non-Latin scripts (Bengali, Arabic, CJK, etc.)
* Lightweight: no jQuery, no external requests
* Developer-friendly: `minuteread_reading_time_output`, `minuteread_word_count`, `minuteread_output_html` filters
* Translation-ready

**Shortcode usage:**

Place `[minuteread_time]` in any post, page, or widget to display the reading time for the current post.

== Installation ==

1. Upload the `minuteread` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Visit **Settings > MinuteRead** to configure the plugin

== Frequently Asked Questions ==

= Does it work with all themes? =
Yes. The reading time is injected via the standard `the_content` filter, which is supported by every properly coded WordPress theme.

= Can I change the words-per-minute speed? =
Yes. Go to **Settings > MinuteRead** and update the "Words per minute" field. The default is 200 (average adult reading speed). Accepted range: 50–1000.

= Can I change the label text? =
Yes. Use the **Label** field in Settings to replace "Estimated Reading Time" with any text you prefer — including text in your own language.

= Can I change how the time is displayed? =
Yes. Use the **Time Format** field. The placeholder `%d` will be replaced with the number of minutes. Examples: `%d min`, `%d minutes`, `%d dakika`.

= Can I show the reading time only in specific locations? =
Disable the automatic insertion by unchecking **Enable Plugin** in settings, then use the `[minuteread_time]` shortcode to place it exactly where you want.

= Does it work with Bengali, Arabic, or other non-Latin scripts? =
Yes. The plugin first tries the standard Latin word counter, then falls back to a whitespace-based counter that works for any language.

= Does it work with custom post types? =
Currently the automatic insertion works for posts only. Custom post type support is planned for a future update.

= Is it translation-ready? =
Yes. WordPress automatically loads translations for plugins hosted on WordPress.org.

== Why Use This Plugin? ==

- Improve user engagement
- Reduce bounce rate
- Help readers estimate time commitment

== Screenshots ==

1. Reading time displayed before post content on the frontend.
2. Admin settings page — configure label, format, position, and words per minute.

== Changelog ==

= 1.0.0 =
* Initial release
* Automatic reading time display on single posts
* Before / after content position option
* Configurable words-per-minute setting (50–1000)
* Custom label and time format
* Enable / disable toggle
* Non-Latin language support (Bengali, Arabic, CJK)
* [minuteread_time] shortcode support
* Developer filters: minuteread_reading_time_output, minuteread_word_count, minuteread_output_html
* Translation-ready

== Upgrade Notice ==

= 1.0.0 =
Initial release.