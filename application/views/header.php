
<head>
<?= $meta ?>
<?= $css ?>
<?= $head_tags ?>
<title><?=  $title ?></title>
<? if(!empty($base)) { ?><base href="<?=$base ?>" /><? } ?>
<?= $head_tags ?>
<?= $head_js ?>
</head>
<body<?= (!empty($body_class)) ? "class=\"" . $body_class . "\"" : ""; ?><?= (!empty($body_id)) ? " id=\"" . $body_id . "\"" : ""; ?>>