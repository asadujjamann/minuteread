# MinuteRead - Reading Time for Posts

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/minuteread?style=flat-square)](https://wordpress.org/plugins/minuteread/)
[![WordPress Plugin Tested](https://img.shields.io/wordpress/plugin/tested/minuteread?style=flat-square)](https://wordpress.org/plugins/minuteread/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/minuteread?style=flat-square)](https://wordpress.org/plugins/minuteread/advanced/)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

A small WordPress plugin that shows an estimated reading time on your posts, pages and custom post types - so readers can decide whether to dive in or save it for later.

**[Download from the WordPress.org plugin directory](https://wordpress.org/plugins/minuteread/)**

---

## What it does

After activation, MinuteRead prints a line like `Estimated Reading Time: 5 min` above (or below) your post content. Everything about it - the label, the format, the reading speed, the position, and which post types it runs on - is configurable from **Settings > MinuteRead**.

No jQuery. No external requests. No database tables. One small stylesheet, loaded only where the output actually appears.

## Features

- **Choose your post types** - posts, pages, or any public custom post type
- **Configurable reading speed** - 50 to 1000 words per minute, default 200
- **Custom label** - replace "Estimated Reading Time" with anything, in any language
- **Custom format** - `%d min`, `%d minutes`, `%d dakika`, whatever fits
- **Before or after** the content
- **Enable / disable** without deactivating the plugin
- **`[minuteread_time]` shortcode** - place the reading time anywhere
- **Correct counting for non-Latin scripts** - Bengali, Arabic, Chinese, Japanese and mixed-script text
- **Theme-friendly** - the plugin does not force its own spacing
- **Four developer filters** for full control
- **Translation-ready**

## Installation

**From WordPress (recommended)**

1. Go to **Plugins > Add New Plugin**
2. Search for **MinuteRead**
3. Click **Install Now**, then **Activate**
4. Visit **Settings > MinuteRead**

**Manually**

1. Download the latest release, or clone this repository
2. Copy the folder into `wp-content/plugins/minuteread`
3. Activate it from the **Plugins** screen

Requires WordPress 5.0 or newer and PHP 7.4 or newer.

## Settings

| Setting | What it does | Default |
|---|---|---|
| Enable Plugin | Hides the reading time without deactivating the plugin | On |
| Words Per Minute | Reading speed used for the calculation (50-1000) | 200 |
| Show On | Post types where the reading time is inserted automatically | Posts |
| Label | Text shown before the time | Estimated Reading Time |
| Time Format | `%d` is replaced with the number of minutes | `%d min` |
| Display Position | Before or after the content | Before |

Options are stored as `minuteread_enable`, `minuteread_wpm`, `minuteread_post_types`, `minuteread_label`, `minuteread_format` and `minuteread_position`. All of them are removed when the plugin is deleted.

## Shortcode

```
[minuteread_time]
```

Inside the loop it uses the current post. To target a specific post, pass its ID:

```
[minuteread_time id="123"]
```

It works in posts, pages, widgets and templates:

```php
echo do_shortcode( '[minuteread_time id="123"]' );
```

## Styling

The output is wrapped in `.minuteread-reading-time` - a `<p>` for the automatic output, a `<span>` for the shortcode.

```css
.minuteread-reading-time {
	color: #444;
	font-style: normal;
	background: #f5f6fa;
	border-left: 3px solid #2c5f4f;
	padding: 8px 12px;
}
```

Add your rules under **Appearance > Customize > Additional CSS** on a classic theme, or **Appearance > Editor > Styles > Additional CSS** on a block theme.

The plugin deliberately does not set a margin on the element, so vertical spacing follows your theme.

## Developer filters

**Change which post types it runs on** - overrides the setting:

```php
add_filter( 'minuteread_post_types', function ( $types ) {
	return array( 'post', 'docs' );
} );
```

**Adjust the word count** - for example, add 12 seconds of reading time per image:

```php
add_filter( 'minuteread_word_count', function ( $words, $content ) {
	$images = substr_count( $content, '<img' );
	return $words + ( $images * 40 );
}, 10, 2 );
```

**Adjust the final number of minutes**:

```php
add_filter( 'minuteread_reading_time_output', function ( $minutes, $words ) {
	return $minutes + 1; // a little padding
}, 10, 2 );
```

**Replace the markup entirely**:

```php
add_filter( 'minuteread_output_html', function ( $html, $minutes, $wrapper ) {
	return sprintf(
		'<div class="my-reading-time"><span class="icon"></span>%d min read</div>',
		absint( $minutes )
	);
}, 10, 3 );
```

The result of `minuteread_output_html` is passed through `wp_kses_post()`, so only post-safe HTML survives.

## Counting words in every language

Most reading time plugins count words with `str_word_count()`, which only understands Latin letters. Give it Bengali or Arabic and it returns zero; give it Bengali with a few English words mixed in and it returns a small wrong number, which is worse - a three thousand word article ends up reporting one minute.

MinuteRead counts words in a Unicode-aware way instead:

- Scripts that separate words with spaces - Latin, Bengali, Arabic, Cyrillic, Korean and most others - are counted by token
- Chinese ideographs and Japanese kana are counted by character and converted at roughly 500 characters per minute, since those languages do not use spaces between words
- Mixed-script text is handled correctly, because the two counts are simply added together

Thai, Khmer and Lao also write without spaces and need dictionary-based segmentation, which the plugin does not attempt yet.

## Changelog

### 1.1.0

- **New:** choose which post types show reading time
- **New:** `minuteread_post_types` filter
- **New:** the shortcode accepts an ID - `[minuteread_time id="123"]`
- **Fixed:** wrong reading time on posts that mix a non-Latin script with a few Latin words
- **Fixed:** Chinese and Japanese content is now counted by character instead of reporting one minute
- **Fixed:** the stylesheet now loads when the shortcode is used outside post content
- **Fixed:** the post type setting is removed on uninstall
- **Changed:** the reading time no longer forces its own bottom margin - spacing follows your theme
- Tested up to WordPress 7.1, requires PHP 7.4

### 1.0.0

- Initial release

The full changelog lives on the [plugin page](https://wordpress.org/plugins/minuteread/#developers).

## Contributing

Issues and pull requests are welcome. For bug reports from users, the [WordPress.org support forum](https://wordpress.org/support/plugin/minuteread/) is the best place - it is checked regularly.

## License

GPL v2 or later. See [gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html).

Built by [Asadujjaman](https://asadsabuj.com).
