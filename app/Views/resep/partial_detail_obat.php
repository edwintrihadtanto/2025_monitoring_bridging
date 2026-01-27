<?php if (empty($detail)): ?>
    <div class="text-muted small">Tidak ada detail obat</div>
<?php else: ?>
<ul class="list-group list-group-flush small">
    <?php foreach ($detail as $d): ?>
    <li class="list-group-item px-0 py-1 d-flex justify-content-between">
        <span><?= esc($d['nama_obat']) ?></span>
        <span class="fw-semibold"><?= esc($d['jml_out']) ?></span>
    </li>
    <?php endforeach ?>
</ul>
<?php endif ?>