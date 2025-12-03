// assets/js/script.js

document.addEventListener('DOMContentLoaded', function () {
    // transaksi: hitung total
    var selectObat = document.getElementById('id_obat');
    var qtyInput = document.getElementById('qty');
    var totalInput = document.getElementById('total');

    function updateTotal() {
        if (!selectObat) return;
        var opt = selectObat.options[selectObat.selectedIndex];
        var harga = opt ? parseInt(opt.getAttribute('data-harga') || 0) : 0;
        var qty = qtyInput ? parseInt(qtyInput.value || 0) : 0;
        var total = harga * qty;
        if (totalInput) totalInput.value = 'Rp ' + total.toLocaleString('id-ID');
    }

    if (selectObat) selectObat.addEventListener('change', updateTotal);
    if (qtyInput) qtyInput.addEventListener('input', updateTotal);

    updateTotal();
});
