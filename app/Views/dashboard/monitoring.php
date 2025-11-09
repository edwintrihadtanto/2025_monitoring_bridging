<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring API BPJS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Log API Bridging BPJS Farmasi</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
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

    <?= $pager->links('group1', 'default_full') ?>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
 $('#detailModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var request = button.data('request')
  var response = button.data('response')
  var modal = $(this)
  modal.find('#modalRequest').text(request);
  modal.find('#modalResponse').text(response);
})
</script>
</body>
</html>