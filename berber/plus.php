<?php
// Zaten Plus üyeyse farklı içerik göster
$isAlreadyPlus = (bool)$user['is_plus'];
?>

<div class="max-w-screen-xl mx-auto px-6 py-6">

    <header class="mb-10 text-center">
        <div class="inline-block bg-black text-secondary px-4 py-1 border-2 border-black font-black text-xs uppercase tracking-widest mb-4 rotate-1">⭐ Plus Üyelik</div>
        <h1 class="text-5xl md:text-7xl font-headline font-extrabold text-black tracking-tighter italic">BerberBook <span class="text-secondary">Plus</span></h1>
        <p class="text-xl text-on-surface-variant font-medium mt-4 max-w-xl mx-auto">İşletmenizi bir üst seviyeye taşıyın. Daha fazla özellik, daha fazla kontrol.</p>
    </header>

    <?php if ($isAlreadyPlus): ?>
    <!-- Zaten Plus -->
    <div class="max-w-lg mx-auto text-center bg-secondary/10 border-4 border-secondary p-10 -rotate-1 mb-12">
        <span class="material-symbols-outlined text-6xl text-secondary mb-4" style="font-variation-settings:'FILL' 1;">workspace_premium</span>
        <h2 class="font-headline font-black text-3xl mb-3">Plus Üyesiniz!</h2>
        <p class="font-medium text-on-surface-variant mb-6">Tüm Plus ayrıcalıklarına sahipsiniz. Yüz yüze müşteri girişi ve çalışan yönetimi aktif.</p>
        <a href="?page=analiz" class="bg-secondary text-white px-8 py-3 border-2 border-black font-bold uppercase hover:-translate-y-1 transition-transform inline-block">Yüz Yüze Müşteri Girişi</a>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

        <!-- Sol: Özellikler -->
        <div class="space-y-6">
            <h2 class="font-headline font-black text-3xl uppercase border-b-4 border-black pb-3">Plus Avantajları</h2>

            <?php
            $features = [
                ['icon' => 'groups',            'title' => 'Çalışan Yönetimi',       'desc' => 'Dükkanınıza berber hesabı olan çalışanları ekleyin, randevuları personel bazında yönetin.'],
                ['icon' => 'person_add',        'title' => 'Yüz Yüze Müşteri Girişi','desc' => 'Sisteme kayıtlı olmayan müşterileri de randevu takibine dahil edin. İsim soyisim ile kolayca ekleyin.'],
                ['icon' => 'insights',          'title' => 'Gelişmiş Analiz',         'desc' => 'Günlük, haftalık kazanç grafikleri ve en çok tercih edilen hizmet analizleri.'],
                ['icon' => 'workspace_premium', 'title' => 'Öncelikli Destek',        'desc' => 'Plus üyeler destek taleplerinde öncelikli yanıt alır.'],
            ];
            foreach ($features as $f): ?>
            <div class="flex items-start gap-5 bg-surface-container-lowest border-2 border-black p-5 hover:-translate-y-0.5 transition-transform">
                <div class="w-12 h-12 bg-secondary flex items-center justify-center border-2 border-black shrink-0">
                    <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings:'FILL' 1;"><?= $f['icon'] ?></span>
                </div>
                <div>
                    <div class="font-headline font-black text-lg uppercase"><?= $f['title'] ?></div>
                    <div class="font-medium text-sm text-on-surface-variant mt-1 leading-relaxed"><?= $f['desc'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="border-4 border-black border-dashed p-6 text-center -rotate-1 bg-surface">
                <div class="font-headline font-black text-5xl text-secondary mb-1">₺299<span class="text-xl text-stone-500">/ay</span></div>
                <div class="font-bold text-sm text-stone-500 uppercase tracking-widest">Tek seferlik aktivasyon · İptal yok</div>
            </div>
        </div>

        <!-- Sağ: Ödeme Formu -->
        <?php if (!$isAlreadyPlus): ?>
        <div class="bg-surface-container-lowest border-4 border-black sketch-shadow p-8">
            <h2 class="font-headline font-black text-2xl uppercase mb-8 border-b-2 border-black pb-4">Ödeme Bilgileri</h2>

            <form id="plusForm" class="space-y-5" autocomplete="off">
                <!-- Kart üzerindeki isim -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest mb-2">Kart Üzerindeki Ad Soyad</label>
                    <input type="text" id="cardName" placeholder="AHMET YILMAZ"
                           class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors uppercase" required>
                </div>

                <!-- Kart numarası -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest mb-2">Kart Numarası</label>
                    <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19"
                           class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors font-mono tracking-widest" required>
                </div>

                <!-- Son kullanma / CVV -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest mb-2">Son Kullanma Tarihi</label>
                        <input type="text" id="cardExpiry" placeholder="AA/YY" maxlength="5"
                               class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors font-mono" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest mb-2">CVV</label>
                        <input type="text" id="cardCvv" placeholder="•••" maxlength="4"
                               class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors font-mono" required>
                    </div>
                </div>

                <div class="bg-[#fefee5] border-2 border-black p-4 text-xs font-medium text-stone-600 leading-relaxed">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">lock</span>
                    Ödeme bilgileriniz 256-bit SSL şifreleme ile korunmaktadır. Kart bilgileriniz sunucularımızda saklanmaz.
                </div>

                <button type="submit" id="plusSubmitBtn"
                        class="w-full bg-black text-white border-2 border-black px-6 py-4 font-headline font-black text-sm uppercase tracking-widest hover:-translate-y-0.5 transition-transform">
                    <span id="plusBtnText">₺299 Öde ve Plus'a Geç</span>
                    <span id="plusSpinner" class="hidden">İşleniyor…</span>
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="bg-surface-container-lowest border-4 border-black border-dashed p-8 text-center rotate-1">
            <span class="material-symbols-outlined text-6xl text-secondary mb-4" style="font-variation-settings:'FILL' 1;">check_circle</span>
            <h3 class="font-headline font-black text-2xl uppercase mb-2">Tüm Özellikler Aktif</h3>
            <p class="font-medium text-on-surface-variant text-sm">Çalışanlar ve Yüz Yüze Müşteri Girişi özelliklerine erişebilirsiniz.</p>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php if (!$isAlreadyPlus): ?>
<script>
// Kart numarası formatı
document.getElementById('cardNumber').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 16);
    this.value = v.replace(/(.{4})/g, '$1 ').trim();
});

// Son kullanma tarihi formatı
document.getElementById('cardExpiry').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 4);
    if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
    this.value = v;
});

// CVV: sadece rakam
document.getElementById('cardCvv').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 4);
});

document.getElementById('plusForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const cardName   = document.getElementById('cardName').value.trim();
    const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
    const cardExpiry = document.getElementById('cardExpiry').value.trim();
    const cardCvv    = document.getElementById('cardCvv').value.trim();

    if (cardNumber.length < 16) { showToast('error', 'Geçerli bir kart numarası girin.'); return; }
    if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) { showToast('error', 'Son kullanma tarihi AA/YY formatında olmalı.'); return; }
    if (cardCvv.length < 3) { showToast('error', 'Geçerli bir CVV girin.'); return; }

    const btn     = document.getElementById('plusSubmitBtn');
    const btnTxt  = document.getElementById('plusBtnText');
    const spinner = document.getElementById('plusSpinner');

    btn.disabled = true;
    btnTxt.classList.add('hidden');
    spinner.classList.remove('hidden');

    // Ödeme simülasyonu (2 sn gecikme)
    await new Promise(r => setTimeout(r, 2000));

    const fd = new FormData();
    fd.set('action', 'activate_plus');

    try {
        const res  = await fetch('berber/api.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            showToast('success', 'Plus üyeliğiniz aktifleştirildi! Sayfanız yenileniyor…');
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast('error', data.message);
            btn.disabled = false;
            btnTxt.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    } catch {
        showToast('error', 'Sunucuya ulaşılamadı.');
        btn.disabled = false;
        btnTxt.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
});
</script>
<?php endif; ?>
