// تسجيل Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => {
                console.log('Service Worker مسجل بنجاح:', registration.scope);
            })
            .catch(error => {
                console.log('فشل تسجيل Service Worker:', error);
            });
    });
}

// التعامل مع حدث التثبيت
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // إظهار زر التثبيت المخصص
    const installButton = document.getElementById('install-button');
    if (installButton) {
        installButton.style.display = 'block';

        installButton.addEventListener('click', () => {
            installButton.style.display = 'none';
            deferredPrompt.prompt();

            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('المستخدم وافق على التثبيت');
                }
                deferredPrompt = null;
            });
        });
    }
});

// عند اكتمال التثبيت
window.addEventListener('appinstalled', () => {
    console.log('تم تثبيت التطبيق بنجاح');
    deferredPrompt = null;
});
