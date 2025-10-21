// إدارة شراء الفواتير
document.addEventListener('DOMContentLoaded', function() {
    const amountUsdInput = document.querySelector('input[name="amount_usd"]');
    const exchangeRateInput = document.querySelector('input[name="exchange_rate"]');
    const taxIqdInput = document.querySelector('input[name="tax_iqd"]');
    const amountIqdDisplay = document.getElementById('amount_iqd');
    const totalIqdDisplay = document.getElementById('total_iqd');

    function calculateAmounts() {
        const amountUsd = parseFloat(amountUsdInput.value) || 0;
        const exchangeRate = parseFloat(exchangeRateInput.value) || 0;
        const taxIqd = parseFloat(taxIqdInput.value) || 0;

        const amountIqd = amountUsd * exchangeRate;
        const totalIqd = amountIqd + taxIqd;

        amountIqdDisplay.value = Math.round(amountIqd);
        totalIqdDisplay.value = Math.round(totalIqd);
    }

    amountUsdInput.addEventListener('input', calculateAmounts);
    exchangeRateInput.addEventListener('input', calculateAmounts);
    taxIqdInput.addEventListener('input', calculateAmounts);

    // حساب أولي عند تحميل الصفحة
    calculateAmounts();
});
