function initListResepPage() {

    const resultContainer = document.getElementById('result-container');
    if (!resultContainer) return;

    const spesialisUrl = resultContainer.dataset.url;
    const alertContainer = document.getElementById('alert-container');

    const formData = new FormData();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    resultContainer.innerHTML = '<div class="text-center p-5"><div class="spinner-grow text-primary"></div><h6>sedang memuat halaman...</h6></div>';

    fetch(spesialisUrl, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.status) {
            throw new Error(data.message || 'Gagal memuat Spesialistik');
        }
        resultContainer.innerHTML = data.html;
    })
    .catch(err => {
        console.error(err);
        isDPHOLoaded = false;
        if (alertContainer) {
            alertContainer.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
        }
    });
}