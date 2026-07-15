document.getElementById('download-btn').addEventListener('click', function () {
    const recibo = document.getElementById('recibo');
    const opt = {
        margin: 0.5,
        filename: this.dataset.filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().from(recibo).set(opt).save();
});
