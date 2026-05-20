function initKoneksiPage() {
    const root = document.getElementById('koneksi-monitor');
    if (!root || root.dataset.bound === '1') return;

    root.dataset.bound = '1';

    const btn = document.getElementById('btnRefreshKoneksi');
    const cards = document.getElementById('koneksiCards');
    const checkedAt = document.getElementById('koneksiCheckedAt');

    if (!btn || !cards) return;

    btn.addEventListener('click', function () {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(BASE_URL + 'koneksi/status', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.status) {
                throw new Error(data.message || 'Gagal mengecek koneksi');
            }

            if (checkedAt) {
                checkedAt.textContent = data.checked_at;
            }

            cards.innerHTML = data.checks.map(renderKoneksiCard).join('');
        })
        .catch(error => {
            console.error(error);
            if (window.AppToast) {
                window.AppToast({
                    text: 'Gagal memperbarui status koneksi.',
                    type: 'error'
                });
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        });
    });
}

function renderKoneksiCard(check) {
    const styles = {
        ok: { className: 'is-ok', badge: 'bg-success', icon: 'bi-check-circle', text: 'Normal' },
        warning: { className: 'is-warning', badge: 'bg-warning', icon: 'bi-exclamation-triangle', text: 'Perlu Dicek' },
        down: { className: 'is-down', badge: 'bg-danger', icon: 'bi-x-circle', text: 'Gangguan' }
    };
    const style = styles[check.status] || { className: '', badge: 'bg-secondary', icon: 'bi-question-circle', text: 'Tidak Diketahui' };
    const httpCode = check.http_code !== null ? `<span class="connection-meta"><i class="bi bi-globe2"></i>HTTP ${escapeHtml(check.http_code)}</span>` : '';
    const duration = check.duration_ms !== null ? `<span class="connection-meta"><i class="bi bi-stopwatch"></i>${escapeHtml(check.duration_ms)} ms</span>` : '';
    const speed = check.speed ? `<span class="speed-chip ${escapeHtml(check.speed.class)}"><i class="bi ${escapeHtml(check.speed.icon)}"></i>${escapeHtml(check.speed.label)}</span>` : '';
    const endpoint = check.endpoint ? `
        <div class="connection-endpoint text-truncate mt-3" title="${escapeHtml(check.endpoint)}">
            ${escapeHtml(check.endpoint)}
        </div>` : '';

    return `
        <div class="col-12 col-md-6 col-xl-3 koneksi-card" data-key="${escapeHtml(check.key || '')}">
            <div class="card h-100 connection-card ${style.className}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <small class="connection-label text-uppercase fw-semibold" style="letter-spacing:.04em;">${escapeHtml(check.label || '-')}</small>
                            <div class="mt-2">
                                <span class="connection-status">
                                    <i class="bi ${style.icon}"></i>
                                    ${style.text}
                                </span>
                            </div>
                        </div>
                        <div class="badge ${style.badge} flex-shrink-0">
                            <i class="bi ${style.icon}"></i>
                        </div>
                    </div>
                    <p class="connection-message small mb-3" style="min-height: 48px;">
                        ${escapeHtml(check.message || '-')}
                    </p>
                    <div class="d-flex flex-wrap gap-2">${httpCode}${duration}${speed}</div>
                    ${endpoint}
                </div>
            </div>
        </div>`;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
