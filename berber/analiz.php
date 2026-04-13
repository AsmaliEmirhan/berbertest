<?php
// Plus kontrolü
if (!$user['is_plus']):
?>
<div class="max-w-screen-xl mx-auto px-6 py-20 text-center">
    <div class="bg-surface-container-lowest border-4 border-black p-12 inline-block rotate-1">
        <span class="material-symbols-outlined text-8xl text-secondary mb-4 opacity-80">workspace_premium</span>
        <h2 class="text-4xl font-headline font-black mb-4">Plus Üyelik Gerekli</h2>
        <p class="text-lg text-on-surface-variant mb-8 font-medium">Yüz yüze müşteri girişi yalnızca Plus üyelere özeldir.</p>
        <a href="?page=plus" class="bg-black text-white px-8 py-3 border-2 border-black font-bold uppercase hover:-translate-y-1 transition-transform inline-block">⭐ Plus'a Geç</a>
    </div>
</div>
<?php return; endif; ?>

<?php if (!$shop): ?>
<div class="max-w-screen-xl mx-auto px-6 py-20 text-center">
    <div class="bg-surface-container-lowest border-4 border-black p-12 inline-block -rotate-1">
        <h2 class="text-4xl font-headline font-black mb-4">Önce bir dükkan oluşturun</h2>
        <a href="?page=dukkan" class="bg-black text-white px-8 py-3 border-2 border-black font-bold uppercase hover:-translate-y-1 transition-transform inline-block mt-4">Dükkan Oluştur</a>
    </div>
</div>
<?php return; endif; ?>

<?php
// Hizmetler
$stmtSv = $pdo->prepare('SELECT id, service_name, price, duration_minutes FROM services WHERE shop_id = ? ORDER BY service_name');
$stmtSv->execute([$shop['id']]);
$services = $stmtSv->fetchAll();

// Personeller: dükkan sahibi + çalışanlar
$stmtEmp = $pdo->prepare("
    SELECT u.id, u.full_name FROM users u
    WHERE u.id = ?
    UNION
    SELECT u.id, u.full_name FROM users u
    JOIN shop_employees se ON se.employee_id = u.id
    WHERE se.shop_id = ?
    ORDER BY full_name
");
$stmtEmp->execute([$_SESSION['user_id'], $shop['id']]);
$employees = $stmtEmp->fetchAll();
?>

<div class="max-w-screen-xl mx-auto px-6 py-6">
    <header class="mb-10 text-center md:text-left">
        <div class="inline-block bg-secondary text-white px-4 py-1 border-2 border-black font-bold text-xs uppercase tracking-widest mb-4 -rotate-1">Plus Özellik</div>
        <h1 class="text-5xl font-headline font-extrabold text-black tracking-tighter mb-2 italic">Yüz Yüze Müşteri</h1>
        <p class="text-lg text-on-surface-variant font-medium uppercase tracking-widest">Dükkanınıza gelen müşterileri sisteme ekleyin.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">

        <!-- Form -->
        <div class="lg:col-span-3 bg-surface-container-lowest border-4 border-black sketch-shadow p-8">
            <h2 class="font-headline font-black text-2xl mb-8 uppercase border-b-2 border-black pb-4">Yeni Randevu Girişi</h2>

            <form id="walkinForm" class="space-y-6">
                <!-- İsim / Soyisim -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest mb-2">İsim</label>
                        <input type="text" id="wFirstName" placeholder="Ahmet"
                               class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest mb-2">Soyisim</label>
                        <input type="text" id="wLastName" placeholder="Yılmaz"
                               class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors" required>
                    </div>
                </div>

                <!-- Hizmet -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest mb-2">Hizmet</label>
                    <?php if (empty($services)): ?>
                        <div class="border-2 border-black px-4 py-3 bg-surface-container text-stone-400 font-bold text-sm">Henüz hizmet eklenmemiş.</div>
                    <?php else: ?>
                    <select id="wService" class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors appearance-none" required>
                        <option value="">Hizmet seçin…</option>
                        <?php foreach ($services as $sv): ?>
                        <option value="<?= $sv['id'] ?>" data-price="<?= $sv['price'] ?>" data-dur="<?= $sv['duration_minutes'] ?>">
                            <?= htmlspecialchars($sv['service_name']) ?> — ₺<?= number_format($sv['price'], 0) ?> (<?= $sv['duration_minutes'] ?> dk)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </div>

                <!-- Personel -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest mb-2">Personel</label>
                    <select id="wEmployee" class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors appearance-none" required>
                        <option value="">Personel seçin…</option>
                        <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tarih / Saat -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest mb-2">Tarih</label>
                        <input type="date" id="wDate" max="<?= date('Y-m-d') ?>"
                               class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest mb-2">Saat</label>
                        <input type="time" id="wTime" min="09:00" max="19:00"
                               class="w-full border-2 border-black px-4 py-3 font-bold bg-white focus:outline-none focus:border-secondary transition-colors" required>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="walkinSubmitBtn"
                        class="w-full bg-black text-white border-2 border-black px-6 py-4 font-headline font-black text-sm uppercase tracking-widest hover:-translate-y-0.5 transition-transform">
                    <span id="walkinBtnText">Randevuyu Kaydet</span>
                    <span id="walkinSpinner" class="hidden">Kaydediliyor…</span>
                </button>
            </form>
        </div>

        <!-- Bilgi kutusu -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-high border-4 border-black border-dashed p-6 -rotate-1">
                <h3 class="font-headline font-black text-xl mb-4 uppercase">Nasıl Çalışır?</h3>
                <ul class="space-y-3 font-medium text-sm text-on-surface-variant">
                    <li class="flex items-start gap-2"><span class="font-black text-secondary mt-0.5">1.</span> Müşterinin adını ve soyismini girin.</li>
                    <li class="flex items-start gap-2"><span class="font-black text-secondary mt-0.5">2.</span> Yapılacak işlemi seçin.</li>
                    <li class="flex items-start gap-2"><span class="font-black text-secondary mt-0.5">3.</span> İşlemi yapacak personeli belirtin.</li>
                    <li class="flex items-start gap-2"><span class="font-black text-secondary mt-0.5">4.</span> Tarih ve saati girin, kaydedin.</li>
                </ul>
            </div>

            <div class="bg-surface-container-lowest border-4 border-black p-6 rotate-1">
                <h3 class="font-headline font-black text-xl mb-3 uppercase flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">info</span> Not
                </h3>
                <p class="font-medium text-sm text-on-surface-variant leading-relaxed">
                    Yüz yüze müşteriler randevu listesinde <span class="font-black bg-surface-container-highest border border-black px-1.5 py-0.5 rounded text-[11px] uppercase">Yüz Yüze</span> etiketi ile gösterilir. Bu randevular müşteri hesabına bağlı değildir.
                </p>
            </div>

            <div id="lastWalkin" class="hidden bg-secondary/10 border-2 border-secondary p-6">
                <h3 class="font-headline font-black text-lg mb-2 text-secondary uppercase">Son Eklenen</h3>
                <div id="lastWalkinContent" class="font-medium text-sm"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('walkinForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn     = document.getElementById('walkinSubmitBtn');
    const btnTxt  = document.getElementById('walkinBtnText');
    const spinner = document.getElementById('walkinSpinner');

    const firstName  = document.getElementById('wFirstName').value.trim();
    const lastName   = document.getElementById('wLastName').value.trim();
    const serviceId  = document.getElementById('wService').value;
    const employeeId = document.getElementById('wEmployee').value;
    const date       = document.getElementById('wDate').value;
    const time       = document.getElementById('wTime').value;

    if (!firstName || !lastName || !serviceId || !employeeId || !date || !time) {
        showToast('error', 'Lütfen tüm alanları doldurun.');
        return;
    }

    btn.disabled = true;
    btnTxt.classList.add('hidden');
    spinner.classList.remove('hidden');

    const fd = new FormData();
    fd.set('action',      'add_walkin');
    fd.set('first_name',  firstName);
    fd.set('last_name',   lastName);
    fd.set('service_id',  serviceId);
    fd.set('employee_id', employeeId);
    fd.set('date',        date);
    fd.set('time',        time);

    try {
        const res  = await fetch('berber/api.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            showToast('success', data.message);
            document.getElementById('wFirstName').value = '';
            document.getElementById('wLastName').value  = '';
            document.getElementById('wService').value   = '';
            document.getElementById('wEmployee').value  = '';
            document.getElementById('wDate').value      = '';
            document.getElementById('wTime').value      = '';

            const svcOpt = document.getElementById('wService').options;
            const svcName = [...svcOpt].find(o => o.value == serviceId)?.text.split(' —')[0] || '';
            const empOpt  = document.getElementById('wEmployee').options;
            const empName = [...empOpt].find(o => o.value == employeeId)?.text || '';

            document.getElementById('lastWalkin').classList.remove('hidden');
            document.getElementById('lastWalkinContent').innerHTML =
                `<b>${firstName} ${lastName}</b> — ${svcName}<br><small>${empName} · ${date} ${time}</small>`;
        } else {
            showToast('error', data.message);
        }
    } catch {
        showToast('error', 'Sunucuya ulaşılamadı.');
    } finally {
        btn.disabled = false;
        btnTxt.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
});
</script>
