<?= view('templates/header', $data) ?>
<?= view('templates/sidebar', $data) ?>
<?= view('templates/navbar', $data) ?>

<div id="main-content">
    <?= $content ?>
</div>

<?= view('templates/footer', $data) ?>
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Log Bridging</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Request Data:</h6>
                <textarea id="modalRequest" class="bg-light p-3 border rounded mb-3" style="max-height: 200px; overflow-y: auto; width:100%;" readonly></textarea>
                <h6>Request Body:</h6>
                <textarea id="modalRequestBody" class="bg-light p-3 border rounded mb-3" style="min-height: 100px; max-height: 200px; overflow-y: auto; width:100%;" readonly></textarea>
                <h6>Response Data:</h6>
                <textarea id="modalResponse" class="bg-light p-3 border rounded" style="max-height: 300px; overflow-y: auto; width:100%;" readonly></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>