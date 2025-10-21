// JavaScript للفواتير
function deleteInvoice(invoiceId, invoiceNumber) {
    if (confirm(`هل أنت متأكد من حذف الفاتورة "${invoiceNumber}"؟\n\nملاحظة: لا يمكن حذف فاتورة لها مبيعات.`)) {
        // إنشاء form للحذف
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/invoices/${invoiceId}`;

        // إضافة CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        // إضافة method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}

// JavaScript للعملاء
function deleteCustomer(customerId, customerName) {
    if (confirm(`هل أنت متأكد من حذف العميل "${customerName}"؟\n\nملاحظة: لا يمكن حذف عميل له مشتريات.`)) {
        // إنشاء form للحذف
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/customers/${customerId}`;

        // إضافة CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        // إضافة method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}
