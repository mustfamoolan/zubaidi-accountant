// إدارة الفواتير المباعة
function showSaleDetails(saleId) {
    // جلب تفاصيل العملاء من الخادم
    fetch(`/api/sales/${saleId}/customers`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySaleDetails(data.sale, data.customers);
            } else {
                alert('حدث خطأ في جلب تفاصيل العملاء');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في جلب تفاصيل العملاء');
        });
}

function displaySaleDetails(sale, customers) {
    let content = `
        <div class="row">
            <div class="col-md-6">
                <h6>تفاصيل الفاتورة:</h6>
                <p><strong>رقم الفاتورة:</strong> ${sale.invoice_number}</p>
                <p><strong>تاريخ البيع:</strong> ${sale.sale_date}</p>
                <p><strong>إجمالي المبلغ بالدولار:</strong> ${parseFloat(sale.total_amount_usd).toLocaleString()} $</p>
                <p><strong>إجمالي المبلغ بالدينار:</strong> ${parseFloat(sale.total_amount_iqd).toLocaleString()} د.ع</p>
                <p><strong>المجموع مع الضريبة:</strong> ${parseFloat(sale.total_with_tax_iqd).toLocaleString()} د.ع</p>
                <p><strong>الربح:</strong> <span class="text-success fw-bold">${parseFloat(sale.profit_iqd).toLocaleString()} د.ع</span></p>
            </div>
            <div class="col-md-6">
                <h6>تفاصيل العملاء:</h6>
    `;

    customers.forEach((customer, index) => {
        content += `
            <div class="border rounded p-2 mb-2">
                <p class="mb-1"><strong>العميل ${index + 1}:</strong> ${customer.name}</p>
                <p class="mb-1"><strong>المبلغ بالدولار:</strong> ${parseFloat(customer.amount_usd).toLocaleString()} $</p>
                <p class="mb-1"><strong>سعر الصرف:</strong> ${parseFloat(customer.exchange_rate).toFixed(4)}</p>
                <p class="mb-0"><strong>المبلغ بالدينار:</strong> ${parseFloat(customer.amount_iqd).toLocaleString()} د.ع</p>
            </div>
        `;
    });

    content += `
            </div>
        </div>
    `;

    document.getElementById('saleDetailsContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('saleDetailsModal')).show();
}
