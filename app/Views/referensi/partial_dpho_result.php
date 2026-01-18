<?php
if (!empty($dphoList)): 
?>

<div class="alert alert-info">
    <i class="bi bi-list-check me-2"></i>
    Ditemukan <strong><?= count($dphoList) ?></strong> Data Obat DPHO.
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Daftar Obat & Plafon Harga</h4>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped datatable" id="table-dpho" width="100%">
            <thead>
                <tr>
                    <th>Kode Obat</th>
                    <th>Nama Obat</th>
                    <th>Generik</th>
                    <th>Kategori (PRB/Kronis/Kemo)</th>
                    <th>Harga</th>
                    <th>Stok / Sedia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dphoList as $item): ?>
                <tr>
                    <td><strong><?= $item['kodeobat'] ?></strong></td>
                    <td><?= $item['namaobat'] ?></td>
                    <td><?= $item['generik'] ?></td>
                    <td>
                        <?php 
                        $badges = [];
                        if ($item['prb'] === 'True') $badges[] = '<span class="badge bg-info me-1">PRB</span>';
                        if ($item['kronis'] === 'True') $badges[] = '<span class="badge bg-warning me-1">Kronis</span>';
                        if ($item['kemo'] === 'True') $badges[] = '<span class="badge bg-danger">Kemo</span>';
                        echo implode('', $badges);
                        ?>
                    </td>
                    <td class="text-end">
                        <span class="fw-bold text-success">
                            Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">
                            S: <?= $item['stok'] ?> / A: <?= $item['sedia'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading">Data DPHO Tidak Ditemukan</h4>
        <p>Silakan coba refresh halaman atau periksa koneksi API.</p>
    </div>
<?php endif; ?>