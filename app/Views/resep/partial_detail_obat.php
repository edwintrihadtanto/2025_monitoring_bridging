<?php if (empty($detail)): ?>
    <div class="text-muted small">Tidak ada detail obat</div>
<?php else: ?>
<ul class="list-group list-group-flush small">    
    <table class="table table-striped table-hover" width="100%">
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Obat</th>
                <th>Qty</th>                    
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detail as $d => $item): ?>
            <tr>                
                <td class="text-truncate">
                    <?= $item['kd_prd'] ?>
                </td>
                <td>
                    <strong><?= $item['nama_obat'] ?></strong>                    
                </td>                
                <td><?= $item['jml_out'] ?></td>                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
</ul>
<?php endif ?>