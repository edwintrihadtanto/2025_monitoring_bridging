<?= view('templates/header', $data) ?>
<?= view('templates/sidebar', $data) ?>
<?= view('templates/navbar', $data) ?>

<div id="main-content">
    <?= $content ?>
</div>

<?= view('templates/footer', $data) ?>
<!-- <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
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
</div> -->

<div class="modal fade monitoring-theme" id="detailModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header border-0">
                <h6 class="modal-title">
                    <i class="bi bi-terminal"></i> Detail Log API
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body pt-0">

                <!-- REQUEST URL -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Endpoint</small>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-target="modalRequest">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <pre id="modalRequest" class="code-box"></pre>
                </div>

                <!-- REQUEST BODY -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Request Body</small>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-target="modalRequestBody">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <pre id="modalRequestBody" class="code-box"></pre>
                </div>

                <!-- RESPONSE -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Response</small>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-target="modalResponse">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <pre id="modalResponse" class="code-box response-box"></pre>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>