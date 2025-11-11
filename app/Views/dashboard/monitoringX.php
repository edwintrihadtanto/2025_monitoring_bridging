<!DOCTYPE html>
<html lang="id">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>    

    <link rel="stylesheet" href="<?= base_url('public/assets/dist/assets/extensions/simple-datatables/style.css'); ?> ">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/app.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/app-dark.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/iconly.css'); ?>">
</head>
<body>
<div class="container mt-4">
    <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>API Bridging BPJS Farmasi</h3>
                        <p class="text-subtitle text-muted">-----------</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Hal.</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Monitoring</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Log Monitoring
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Endpoint</th>
                                    <th>Method</th>
                                    <th>Response Code</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $log['created_at'] ?></td>
                                    <td><?= $log['endpoint'] ?></td>
                                    <td><?= $log['method'] ?></td>
                                    <td>
                                        <?php if ($log['response_code'] == 200): ?>
                                            <span class="badge badge-success"><?= $log['response_code'] ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><?= $log['response_code'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal" 
                                                data-request='<?= htmlspecialchars($log['request_header'] . "\n" . $log['request_body'], ENT_QUOTES, 'UTF-8') ?>'
                                                data-response='<?= htmlspecialchars($log['response_body'], ENT_QUOTES, 'UTF-8') ?>'>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </div>

    
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Request & Response</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6>Request:</h6>
        <pre id="modalRequest" class="bg-light p-3"></pre>
        <hr>
        <h6>Response:</h6>
        <pre id="modalResponse" class="bg-light p-3"></pre>
      </div>
    </div>
  </div>
</div>


<script src="<?= base_url('public/assets/dist/assets/static/js/components/dark.js'); ?>"></script>
<script src="<?= base_url('public/assets/dist/assets/compiled/js/app.js'); ?> "></script>
<script src="<?= base_url('public/assets/dist/assets/extensions/simple-datatables/umd/simple-datatables.js'); ?> "></script>
<script src="<?= base_url('public/assets/dist/assets/static/js/pages/simple-datatables.js'); ?> "></script>

<script>
 /*$('#detailModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var request = button.data('request')
  var response = button.data('response')
  var modal = $(this)
  modal.find('#modalRequest').text(request);
  modal.find('#modalResponse').text(response);
})*/

 $('#detailModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  
  // Ambil data dari atribut 'data-request' dan 'data-response'
  var request = button.data('request')
  var response = button.data('response')

  var modal = $(this)
  
  // Tampilkan request (ini biasanya sudah berupa string)
  modal.find('#modalRequest').text(request);
  
  // --- PERBAIKAN DI SINI ---
  // Ubah objek JavaScript 'response' menjadi string JSON yang terformat
  // JSON.stringify(objek, null, 2) akan membuat JSON dengan indentasi 2 spasi agar mudah dibaca
  modal.find('#modalResponse').text(JSON.stringify(response, null, 2));
})
</script>
</body>
</html>