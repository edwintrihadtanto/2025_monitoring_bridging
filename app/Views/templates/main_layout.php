<?= view('templates/header', $data) ?>
<?= view('templates/sidebar', $data) ?>
<?= view('templates/navbar', $data) ?>

<div id="main-content">
    <?= $content ?>
</div>

<?= view('templates/footer', $data) ?>