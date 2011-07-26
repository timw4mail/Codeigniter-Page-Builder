<?php
/*
|--------------------------------------------------------------------------
| Content Domain
|--------------------------------------------------------------------------
|
| This is the domain used for serving content, such as css, javascript.
|
*/
$config['content_domain'] = '';

/*
|--------------------------------------------------------------------------
| Content Domain
|--------------------------------------------------------------------------
|
| This is the domain/subdomain used for serving images.
|
*/
$config['image_domain'] = '';

/*
|--------------------------------------------------------------------------
| Static Lib Path
|--------------------------------------------------------------------------
|
| This is the path where the css.php and js.php files are on the static domain.
|
*/
$config['static_lib_path'] = '';

/*
|--------------------------------------------------------------------------
| Group Style Path
|--------------------------------------------------------------------------
|
| This is the path that is used to determine the relative path to the
| stylesheet minifier. This should not need to be changed.
|
*/
$config['group_style_path'] = $config['static_lib_path'] . '/css.php/g/';

/*
|--------------------------------------------------------------------------
| Group JS Path
|--------------------------------------------------------------------------
|
| This is the path that is used to determine the relative path to the
| stylesheet minifier. This should not need to be changed.
|
*/
$config['group_js_path'] = $config['static_lib_path'] . '/js.php/g/';
	
/*
|--------------------------------------------------------------------------
| Password Salt
|--------------------------------------------------------------------------
|
| This is the string used as a password salt for checking passwords. This
| should never be changed once it is set. This must be 12 characters
| long, and start with '$1$'
|
*/
$config['salt'] = '$1$3f6iuhj89';


/*
|--------------------------------------------------------------------------
| Default title
|--------------------------------------------------------------------------
|
| Default title for webpages
|
*/

$config['default_title'] = "";

/*
|--------------------------------------------------------------------------
| Default css group
|--------------------------------------------------------------------------
|
| Default css group
|
*/

$config['default_css_group'] = "css";
