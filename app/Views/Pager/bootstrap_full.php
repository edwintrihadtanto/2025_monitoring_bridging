<?php
// DEBUGGING: Jika object bukan PagerRenderer, berhenti dan tampilkan error jelas
if (!$pager instanceof \CodeIgniter\Pager\PagerRenderer) {
    die('Error: Variabel $pager bukan PagerRenderer. Pastikan di Controller Anda mengirim $logModel->pager, bukan service(pager).');
}
?>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <!-- Tombol FIRST -->
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" class="page-link" aria-label="First">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                </a>
            </li>
        <?php endif ?>

        <!-- Tombol PREVIOUS -->
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" class="page-link" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif ?>

        <!-- NOMOR HALAMAN -->
        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <!-- Tombol NEXT -->
        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" class="page-link" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif ?>

        <!-- Tombol LAST -->
        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" class="page-link" aria-label="Last">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>