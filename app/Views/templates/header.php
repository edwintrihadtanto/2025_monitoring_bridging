<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?><?php echo SITE_NAME; ?></title>
    
    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="shortcut icon" href="" type="image/png">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/app.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/app-dark.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/iconly.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/dist/assets/extensions/simple-datatables/style.css'); ?> ">
</head>
<body>
    <script src="<?= base_url('public/assets/dist/assets/static/js/initTheme.js'); ?>"></script>
    <div id="app">