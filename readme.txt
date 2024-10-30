=== mher list Subpages ===
Contributors: mher30
Donate link: https://ko-fi.com/mher30
Tags: page subpages navigation shortcode
Requires at least: 6.5
Tested up to: 6.6
Stable tag: 1.0.1
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licences/gpl-2.0.html

Lists the subpages of the current page with multiple options, including defining your own templates for the listings

== Description ==

## Configurable Settings

* Default Fallback image in case the subpage does not have a featured image and on shortcode level no fallback image has been set
* Custom html-templates for listing the subpages
  + accessable via number or name
  + placeholders to use in the templates
     - `{{ url }}` - url of the subpage
     - `{{ title }}` - title of the subpage
     - `{{ img }}` - html-code for the featured image of the subpage
     - `{{ img_url }}` - just the url of the featured image of the subpage
     - `{{ blocks }}` - the via name selected blocks of the subpage
  + For security reasons no javascript is allowed in the user defined templates and will be silently removed after rendering.

## Shortcode Options

- `template=`     - either the number or the name of the custom template
- `blocks_named=` - fetches and renders all blocks of the corresponding subpage and replaces {{ blocks }} in the selected template - default for blocks_named: teaser - to make sure no blocks are used, use the dash, like: *blocks=-*
- `image_id=`     - id of an image in the media library to be used as a fallback image in case a subpage does not have a featured image
- `image_size=`   - any registered image size possible - default: thumbnail
- `list=`         - a comma-separated list of page-ids that should be listed INSTEAD of the subpages of the current page (combinable with *include=* and *exclude=* even though this does not make much sense)
- `exclude=`      - a comma-separated list of page-ids that should NOT be listed
- `prepend=`      - a comma-separated list of page-ids that should be listed before the subpages (combinable with `exclude=` and `append=` and even `list=`, although this makes little sense)
- `append=`      - a comma-separated list of page-ids that should be listed after the subpages (combinable with `exclude=` and `prepend=` and even `list=`, although this makes little sense)

Subpages, that do not have blocks, i.e. "classic" pages, and subpages that do not have blocks with the given name, will be shown without content.

== Frequently Asked Questions ==

= How can I avoid that any blocks are shown?=

There are actually two ways to accomplish this: a) explicitly set `blocks_named=-` b) create your own template and don't use the `{{ blocks }}` placeholder

= Why does the sequence not reflect the sequence of the pages in my menu? =

You have to change the sequence of the pages within their parent page to match the sequence of your menu.

= How can I change the sequence? =

Currently only by changing the sequence of the pages within their parent page.

== Changelog ==

= 1.0.1 =
Minor Bugfix

= 1.0.0 =
* Initial release

== Installation ==

Starting with **mher list Subpages** is very simple and if you've ever installed a plugin you know the drill:

= Install mher list Subpages from within Wordpress =

1. Visit the plugins page within your dashboard and select 'Add New'
2. Seach for 'mher list Subpages'
3. Select install
4. Continue with 'After Installation' below

= Install mher list Subpages manually =

1. Upload the 'mher-list-subpages' folder to the /wp-content/plugins/ directory
2. Continue with 'After Installation' below

= After Installation =

1. Activate **mher list Subpages** from your Plugins page
2. Use the shortcode `[mher_subpages]` in your pages (with subpages)
3. Visit the Configuration Page to set up options

== Upgrade Notice ==

= 1.0.1 =
Minor Bugfix

= 1.0.0 =
Initial release

== Screenshots ==

1. The Options Configuration Page
2. The Default Output in the Frontend