// تنسيق الأرقام بالفواصل
function formatNumber(input) {
    // إزالة أي فواصل موجودة والاحتفاظ بالأرقام والنقطة العشرية فقط
    let value = input.value.replace(/,/g, '');

    // التحقق من وجود رقم صالح
    if (value === '' || isNaN(value.replace(/\./g, ''))) {
        return;
    }

    // فصل الجزء الصحيح عن العشري
    let parts = value.split('.');
    let integerPart = parts[0];
    let decimalPart = parts.length > 1 ? '.' + parts[1] : '';

    // إضافة الفواصل للجزء الصحيح
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // تحديث قيمة الحقل
    input.value = integerPart + decimalPart;
}

// إزالة الفواصل قبل إرسال النموذج
function removeFormatting(input) {
    input.value = input.value.replace(/,/g, '');
}

// تطبيق التنسيق على جميع حقول المبالغ
document.addEventListener('DOMContentLoaded', function() {
    // اختيار جميع حقول الإدخال للمبالغ
    const amountInputs = document.querySelectorAll('input[name="amount"], input[name="amount_usd"], input[name="exchange_rate"], input[name="tax_percentage"]');

    amountInputs.forEach(function(input) {
        // تنسيق القيمة الحالية إذا كانت موجودة
        if (input.value) {
            formatNumber(input);
        }

        // إضافة مستمع للإدخال (أثناء الكتابة)
        input.addEventListener('input', function(e) {
            // حفظ موضع المؤشر
            let cursorPosition = this.selectionStart;
            let oldLength = this.value.length;

            formatNumber(this);

            // ضبط موضع المؤشر بعد التنسيق
            let newLength = this.value.length;
            let diff = newLength - oldLength;
            this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        });

        // إزالة التنسيق عند التركيز (للتعديل السهل)
        input.addEventListener('focus', function() {
            this.value = this.value.replace(/,/g, '');
        });

        // إعادة التنسيق عند فقدان التركيز
        input.addEventListener('blur', function() {
            if (this.value) {
                formatNumber(this);
            }
        });
    });

    // إزالة الفواصل من جميع الحقول قبل إرسال أي نموذج
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            amountInputs.forEach(function(input) {
                removeFormatting(input);
            });
        });
    });
});
