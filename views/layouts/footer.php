</main>

<!-- Footer -->
<footer class="bg-light text-center text-muted py-4 mt-5">
    <div class="container">
        <p class="mb-0">
            &copy; <?= date('Y') ?> AMDS - <?= $lang->get('footer.rights', 'Tüm hakları saklıdır') ?>
        </p>
        <p class="small">
            <span data-lang-key="footer.powered_by"><?= $lang->get('footer.powered_by', 'Powered by') ?></span> AMDS v1.0
        </p>
    </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Dil Helper -->
<script src="/assets/js/language-helper.js"></script>

<!-- Dil sistemini başlat -->
<script>
document.addEventListener('DOMContentLoaded', async () => {
    // LanguageHelper'ı başlat
    const languageHelper = new LanguageHelper({
        apiBaseUrl: '/api/languages',
        autoDetect: true,
        autoApply: true
    });

    await languageHelper.init();

    // Sayfa çevirilerini yükle
    const currentPage = '<?= $currentPage ?? 'common' ?>';
    await languageHelper.loadPageTranslations(currentPage);

    // Çevirileri uygula
    languageHelper.applyTranslations();

    // Dil seçiciyi render et
    const languageSwitcher = new LanguageSwitcher(languageHelper, 'language-switcher');
    languageSwitcher.render();

    // Global erişim için
    window.lang = languageHelper;

    console.log('Language system initialized:', languageHelper.currentLang);
});
</script>

<!-- Sayfa özel scriptler -->
<?php if (isset($pageScripts)): ?>
    <?= $pageScripts ?>
<?php endif; ?>

</body>
</html>
