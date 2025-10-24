// إدارة شراء الفواتير
document.addEventListener('DOMContentLoaded', function() {
    const amountUsdInput = document.querySelector('input[name="amount_usd"]');
    const exchangeRateInput = document.querySelector('input[name="exchange_rate"]');
    const taxPercentageInput = document.querySelector('input[name="tax_percentage"]');
    const amountIqdDisplay = document.getElementById('amount_iqd');
    const totalIqdDisplay = document.getElementById('total_iqd');

    function calculateAmounts() {
        const amountUsd = parseFloat(amountUsdInput.value) || 0;
        const exchangeRate = parseFloat(exchangeRateInput.value) || 0;
        const taxPercentage = parseFloat(taxPercentageInput.value) || 0;

        const amountIqd = amountUsd * exchangeRate;
        const taxAmount = amountIqd * (taxPercentage / 100);
        const totalIqd = amountIqd + taxAmount;

        amountIqdDisplay.value = Math.round(amountIqd);
        totalIqdDisplay.value = Math.round(totalIqd);
    }

    amountUsdInput.addEventListener('input', calculateAmounts);
    exchangeRateInput.addEventListener('input', calculateAmounts);
    taxPercentageInput.addEventListener('input', calculateAmounts);

    // حساب أولي عند تحميل الصفحة
    calculateAmounts();
});
