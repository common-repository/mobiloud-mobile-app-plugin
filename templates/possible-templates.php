<?php
/**
 * This file include templates related documentation.
 */

die();
// phpcs:disable Squiz.PHP.NonExecutableCode.Unreachable -- this file is manual.
?>
1. Templates

This directory (templates) include default templates:

1.1.
For Paywall screen:
paywall/paywall.php

If you need to include this block to your code, please use:
<?php
ml_get_paywall()->ml_paywall_block();
?>

1.2.
For sections:
sections/sections.php


1.3. For lists:
1.3.1 Main list template.
list/loop.php
This is main file. It always included as first file for any lists.
What does it do?
- detect type of list template using requests parameters, and include one of files:
 list/search.php - if search parameter presented,
 list/favorites.php - if we show predefined list of posts,
 list/custom.php - if we choose some post_type at the request (1),
 list/regular.php - this is default file if no parameters exists or other chosen file not found.
 The file name (ex: "search") saved in $list_type variable.

 (1) this type of request try to use customized version of template using parameter of post_type.
 If we received request with ?post_type=custompost then we will try to load file with suffix "-custompost" (it saved in $list_slug variable).

 Examples:
 - We received request with "?post_ids=686,681", so we will show those 2 favorites posts. Files load order is:
 list/favorites.php
 list/regular.php

 - We received request with "?post_type=jokes", so we will try to load files in this order (we will use this example in this document):
 list/custom-jokes.php
 list/custom.php
 list/regular.php

 In general,
 - we fill $list_type and $list_slug (with value starting from '-' or empty value ) from request;
 - we load files in order
 - list/$list_type$list_slug.php
 - list/$list_type.php
 - list/regular.php

1.3.2 Parts of list templates.
The list/regular.php (and hope all it's copies: favorites, search, custom) will load templates with parts:
"head" - has everything between <head> </head> tags,
"body" - contain html code of initial page content, starting from <ons-page ...> and ending with </ons-page>,
"footer" - contain request to MLApiController and all the JS related code.

The same order of template names used, for example for "head" part:
 - list/$list_type + '_head' + $list_slug.php === list/custom_head-jokes.php
 - list/$list_type + '_head' + .php           === list/custom_head.php
 - list/regular + '_head' + .php              === list/regular.php


2. Extend or replace templates by your own copies.

2.1 Create or modify extension plugin. Create directory ("my-templates" at this example) inside it.

2.2 Register this dir in MobiLoud News plugin using filter.
<?php
add_filter( 'mobiloud_templates_paths', 'ext_mobiloud_templates_paths' );
/**
 * Register our "my-templates" subdirectory as custom directory for search templates.
 *
 * @param array $paths_array Array with directories, without trailing slash.
 */
function ext_mobiloud_templates_paths( $paths_array ) {
	// we want to search in our plugin's subdirectory _before_ the all other path, so add our path to the begin of array.
	array_unshift( $paths_array, dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'my-templates' ); // without trailing slash.
	return $paths_array;
}
?>

Create a directories in "my-templates". If we want to replace default "list/favorites.php" file, we must create same structure in our dir.
my-templates/list/favorites.php - MobiLoud News will use this file instead of built in.

2.3 Mobiloud News plugin use static method for find required template: Mobiloud::use_template($template_type, $template_names, $load = true, $require_once = true)
Example of use from list/regular.php file, where we include a body part:
<?php
	// include body part.
	$_names   = [ "{$list_type}_body{$list_slug}", "{$list_type}_body", 'regular_body' ];
	$template = Mobiloud::use_template( 'list', $_names, false );
if ( '' !== $template ) {
	require $template;
}
?>
