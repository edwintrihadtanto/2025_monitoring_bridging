<?php
    $status = $check['status'] ?? 'down';
    $style = [
        'ok'      => ['class' => 'is-ok', 'badge' => 'bg-success', 'icon' => 'bi-check-circle', 'text' => 'Normal'],
        'warning' => ['class' => 'is-warning', 'badge' => 'bg-warning', 'icon' => 'bi-exclamation-triangle', 'text' => 'Perlu Dicek'],
        'down'    => ['class' => 'is-down', 'badge' => 'bg-danger', 'icon' => 'bi-x-circle', 'text' => 'Gangguan'],
    ][$status] ?? ['class' => '', 'badge' => 'bg-secondary', 'icon' => 'bi-question-circle', 'text' => 'Tidak Diketahui'];
?>

<div class="col-12 col-md-6 col-xl-3 koneksi-card" data-key="<?= esc($check['key'] ?? '') ?>">
    <div class="card h-100 connection-card <?= esc($style['class']) ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <small class="connection-label text-uppercase fw-semibold" style="letter-spacing:.04em;">
                        <?= esc($check['label'] ?? '-') ?>
                    </small>
                    <div class="mt-2">
                        <span class="connection-status">
                            <i class="bi <?= esc($style['icon']) ?>"></i>
                            <?= esc($style['text']) ?>
                        </span>
                    </div>
                </div>
                <div class="badge <?= esc($style['badge']) ?> flex-shrink-0">
                    <i class="bi <?= esc($style['icon']) ?>"></i>
                </div>
            </div>

            <p class="connection-message small mb-3" style="min-height: 48px;">
                <?= esc($check['message'] ?? '-') ?>
            </p>

            <div class="d-flex flex-wrap gap-2">
                <?php if ($check['http_code'] !== null): ?>
                    <span class="connection-meta">
                        <i class="bi bi-globe2"></i>
                        HTTP <?= esc($check['http_code']) ?>
                    </span>
                <?php endif; ?>

                <?php if ($check['duration_ms'] !== null): ?>
                    <span class="connection-meta">
                        <i class="bi bi-stopwatch"></i>
                        <?= esc($check['duration_ms']) ?> ms
                    </span>
                <?php endif; ?>

                <?php if (!empty($check['speed'])): ?>
                    <span class="speed-chip <?= esc($check['speed']['class']) ?>">
                        <i class="bi <?= esc($check['speed']['icon']) ?>"></i>
                        <?= esc($check['speed']['label']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (!empty($check['endpoint'])): ?>
                <div class="connection-endpoint text-truncate mt-3" title="<?= esc($check['endpoint']) ?>">
                    <?= esc($check['endpoint']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
