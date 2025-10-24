// إدارة بيع الفواتير
let customerCount = 0;
let customers = [];

// تهيئة البيانات عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // الحصول على قائمة العملاء من البيانات الممررة
    customers = window.customersData || [];

    // إضافة عميل واحد عند تحميل الصفحة
    addCustomer();
});

function addCustomer() {
    customerCount++;
    const container = document.getElementById('customersContainer');

    const customerDiv = document.createElement('div');
    customerDiv.className = 'border rounded p-3 mb-3';
    customerDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6>عميل ${customerCount}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeCustomer(this)">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">اختيار العميل</label>
                    <select class="form-select customer-select" name="customers[${customerCount}][customer_id]" required>
                        <option value="">اختر عميل</option>
                        ${customers.map(customer => `<option value="${customer.id}">${customer.name}</option>`).join('')}
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">المبلغ بالدولار</label>
                    <input type="number" step="0.01" class="form-control amount-usd" name="customers[${customerCount}][amount_usd]" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">سعر الصرف</label>
                    <input type="number" step="0.0001" class="form-control exchange-rate" name="customers[${customerCount}][exchange_rate]" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">المبلغ بالدينار</label>
                    <input type="text" class="form-control amount-iqd" readonly>
                </div>
            </div>
        </div>
    `;

    container.appendChild(customerDiv);

    // إضافة مستمعي الأحداث للحساب التلقائي
    const amountUsdInput = customerDiv.querySelector('.amount-usd');
    const exchangeRateInput = customerDiv.querySelector('.exchange-rate');
    const amountIqdInput = customerDiv.querySelector('.amount-iqd');

    function calculateCustomerAmount() {
        const amountUsd = parseFloat(amountUsdInput.value) || 0;
        const exchangeRate = parseFloat(exchangeRateInput.value) || 0;
        const amountIqd = amountUsd * exchangeRate;
        amountIqdInput.value = Math.round(amountIqd);
        calculateTotals();
    }

    amountUsdInput.addEventListener('input', calculateCustomerAmount);
    exchangeRateInput.addEventListener('input', calculateCustomerAmount);
}

function removeCustomer(button) {
    button.closest('.border').remove();
    calculateTotals();
}

function calculateTotals() {
    let totalAmountUsd = 0;
    let totalSaleIqd = 0;

    document.querySelectorAll('.amount-usd').forEach(input => {
        totalAmountUsd += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.amount-iqd').forEach(input => {
        totalSaleIqd += parseFloat(input.value) || 0;
    });

    // الحصول على سعر صرف الفاتورة الأصلية
    const invoiceExchangeRate = parseFloat(document.querySelector('meta[name="invoice-exchange-rate"]')?.getAttribute('content') || '0');

    // حساب تكلفة الكمية المباعة (بدون ضريبة)
    const costAmountIqd = totalAmountUsd * invoiceExchangeRate;

    // الربح = سعر البيع - التكلفة (بدون ضريبة)
    const expectedProfit = totalSaleIqd - costAmountIqd;

    document.getElementById('totalAmountUsd').value = Math.round(totalAmountUsd);
    document.getElementById('totalAmountIqd').value = Math.round(totalSaleIqd);
    document.getElementById('expectedProfit').value = Math.round(expectedProfit);
}

function addNewCustomer() {
    const name = document.getElementById('newCustomerName').value;

    if (!name) {
        alert('يرجى إدخال اسم العميل');
        return;
    }

    fetch('/customers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // إضافة العميل الجديد إلى قائمة العملاء
            customers.push(data.customer);

            // تحديث جميع قوائم العملاء
            document.querySelectorAll('.customer-select').forEach(select => {
                const option = document.createElement('option');
                option.value = data.customer.id;
                option.textContent = data.customer.name;
                select.appendChild(option);
            });

            // مسح النموذج
            document.getElementById('newCustomerForm').reset();

            alert('تم إضافة العميل بنجاح');
        } else {
            alert('حدث خطأ في إضافة العميل');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في إضافة العميل: ' + error.message);
    });
}
