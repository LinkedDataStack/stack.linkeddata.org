=== Global Content Blocks===
Contributors: benz1
Donate link: 
https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=M27BDW5HXKAEQ
Tags: admin, shortcode, shortcodes, code, html, php, javascript, snippet, code snippet, iframe, reuse, reusable, adsense, paypal, insert, global, content block, raw html, formatting, pages, posts, editor, tinymce, form, forms, variables, global variables, modify output
Requires at least: 2.8.6
Tested up to: 4.0.0
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates shortcodes to add HTML, PHP, forms, opt-ins, iframes, Adsense, code snippets, reusable objects, etc, to posts/pages and preserves formatting.

== Description ==

**Global Content Blocks** lets you create your own shortcodes to insert reusable code snippets, PHP or HTML including forms, opt-in boxes, iframes, Adsense code, etc, into pages and posts as well as widgets and directly into php content. You can insert them directly using  shortcodes or via a button in the TinyMCE visual editor toolbar. You can also insert the entire content of the Content Block instead of the shortcode or use other shortcodes within the Content Block. You can also modify the output of the Content Block via a hook.

It is ideal for inserting reusable objects into your content or to prevent the WordPress editor from stripping out your code or otherwise changing your formatting. The shortcodes are masked as images to allow easy manipulation and non-html tags contamination.

You can also use your own variables, WordPress global variables and other shortcodes within Content Blocks, modify the output, use shortcodes in themes and widgets, nest shorcodes and a whole lot more. See http://wpxpert.com/category/gcb-advanced-usage/ for more information.

The plugin includes an Import/Export fuction to copy content blocks from one WordPress site to another.

Further information and screenshots are available on the plugin homepage at http://wpxpert.com/global-content-blocks/.

As featured on ManageWP Blog (http://managewp.com/global-content-blocks) and Spanky Media (http://spankymedia.com.au/using-wordpress-global-content-blocks-to-promote-your-content/).

If you have any feedback, issues or suggestions for improvements please leave a comment on the plugin homepage and if you like the plugin please give it a rating at http://wordpress.org/extend/plugins/global-content-blocks :-)

== Installation ==

1. Download the **global-content-blocks.zip** file to your local machine.
2. Either use the automatic plugin installer *(Plugins - Add New)* or Unzip the file and upload the **global-content-blocks** folder to your **/wp-content/plugins/** directory.
3. Activate the plugin through the Plugins menu
4. Visit the **Global Content Blocks** settings page *(Settings > Global Content Blocks)* to add or edit Content Blocks.
5. Insert the Content Block into pages or posts using the button on the editor tool bar or by inserting the shortcode **[contentblock id=xx]** where **xx** is the ID number of the Content Block.


== Frequently Asked Questions ==

= How big a content block can I add? =
The content block will hold up to 64,000 characters.

= Can I create content blocks with PHP code? =
Yes, just copy the PHP as normal into a content block without the &lt;?php, &lt;?, ?&gt; tags and insert the block into your content as normal.

= Can I create content blocks using a visual editor? =
Yes, the Settings page where the content blocks are created includes the standard WordPress visual/HTML editor.

= How do I use a shortcode within a content block? =
Simply add the shortcode within a block as normal, for example [gallery] to add a WordPress gallery.

= Can I use content blocks outside of posts and pages? =
Yes, just wrap it in the PHP function &lt;?php echo gcb(x);?&gt; where x is the content block ID. You can also use the longer form &lt;?php do_shortcode(&quot;[contentblock id=x]&quot;);?&gt;

= Is it possible to modify the output of inserted content blocks? =
Yes, you can add the filter 'gcb_block_output' to modify output by adding a PHP script to functions.php, for example:<br>
add_filter('gcb_block_output', 'alter_block_output');<br>
function alter_block_output($value) {<br>
//process the output here, e.g., convert text to lowercase<br>
$new_value = strtolower($value); <br>
return &quot;Processed output: &quot;.$new_value;<br>
}

= Can I use variables? =
Yes, You can use variables within the content block that will be replaced when the block is displayed. For example, if you create a content block, say id=1 with:
My name is %%name%%<br>
by using the shortcode [contentblock id=1 name=&quot;John Doe&quot;] when displayed it will appear as My name is John Doe.

= Can I use WordPress global variables? =
Yes, WordPress global variables can be used within content blocks, for example,
global $user_login;<br>
global $user_email;<br>
echo &quot;$user_login, you email is: $user_email&quot;;<br>
would output the username and email of the current logged in user, e.g., John, your email is john@youremail.com.

= Will I lose my content blocks if I change the theme or upgrade WordPress? =
No, the blocks are added to the WordPress database so are independent of the theme and unaffected by WordPress upgrades.

= Can it be completely uninstalled? =
Yes, there is an option to delete the database table if you want to completely remove the plugin.

= Can I copy any content blocks I've created to another WordPress site? =
Yes, an Import/Export function is included. Just Export form one site, install the plugin on the other site and import.

== Screenshots ==

1. The Settings page
2. Adding a new Content Block
3. Inserting a Content Block using the toolbar button
4. Inserting a Content Block using the shortcode

== Changelog ==
= 2.0.1 =
* Modified all unserialize calls into maybe_unserialize

= 2.0.0 =
* Removed all calls to mysql_real_escape_string as the extension is now deprecated with PHP 5.5.0
* Moved all data to the standard WP database structure, via the Options API
* Removed unused files

= 1.5.7 =
* Fixed bug when updating from old version

= 1.5.6 =
* Fixed bug introduced in previous release

= 1.5.5 =
* Default charset for the database table is now UTF-8
* Added optional custom ID for content blocks

= 1.5.3 =
* Minor fix to allow the TinyMCE editor GCB icon to coexist with icons from other plugins

= 1.5.2 =
* security patch applied

= 1.5.1 =
* Minor fix for issue experienced by some users

= 1.5 =
* Added a visual editor when creating content blocks
* Added function to include own variables in content blocks

= 1.4.1 =
* Home site moved to http://wpxpert.com

= 1.4 =
* Improvements to HTML content block
* New function to include shortcodes within content blocks
* Added shortened PHP function to use content blocks outside of pages or posts
* Added hook to modify output of inserted content block

= 1.3 =
* Fixed reported expoloit vulnerability
* Fixed export issue in some browsers
* Added option to show/hide the icon in the editor toolbar in case of conflict with other plugins.

= 1.2 =
* Added option to create a new content block while inserting the block in the page/post
* Tidied up code to avoid errors in debug mode

= 1.1.2 =
* Fixed bug, TinyMCE editor button replacing button of some other plugins

= 1.1.1 =
* Fixed bug, slashes being stripped when inserting code block
* Updated TinyMCE default manager for better cross platform compatibility

= 1.1 =
* Added ability to insert PHP blocks into content
* Option to insert to entire content block into pages/posts instead of the shortcode
* Option to select the block type, each represented by a different image block when inserted into content making it easier to identify on the page
* Added Import/Export function to export all or selected content blocks to an xml file that can be imported into the Global Content Blocks plugin on another WordPress site
* Added a link to the Settings page from the Plugins page listing
* Minor cosmetic changes
* Added a Donate button on the Settings page and some advertising

= 1.0.1 =
* Minor typo correction in install file

= 1.0 =
* Stable version released.

== Upgrade Notice ==

= 1.1 =
A major update adding several new features and functions including the ability to insert PHP blocks, insert the entire content block instead of the shortcode and export blocks to another site.

= 1.0.1 =
Minor typo fixed
