<?php
if (!$shop):
?>
<div class="max-w-screen-xl mx-auto px-6 py-20 text-center">
    <div class="bg-surface-container-lowest sketch-border p-12 inline-block rotate-1 border-4 border-black">
        <span class="material-symbols-outlined text-8xl text-secondary mb-4 opacity-80" data-icon="store_off">content_cut</span>
        <h2 class="text-4xl font-headline font-black mb-4">Önce bir dükkan oluşturun</h2>
        <a href="?page=dukkan" class="bg-black text-white px-8 py-3 rounded-xl border-2 border-black font-bold uppercase transition-transform hover:-translate-y-1 inline-block mt-4">Dükkan Oluştur</a>
    </div>
</div>
<?php else:
    $stmt = $pdo->prepare('SELECT * FROM services WHERE shop_id = ? ORDER BY service_name');
    $stmt->execute([$shop['id']]);
    $services = $stmt->fetchAll();
?>

<div class="max-w-screen-xl mx-auto px-6 py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b-4 border-black pb-4">
        <div>
            <h2 class="text-4xl font-headline font-black italic text-black">Hizmetler</h2>
            <p class="text-on-surface-variant font-bold uppercase tracking-widest text-sm">Sunulan işlemler ve detayları</p>
        </div>
        <button class="bg-secondary text-white px-6 py-3 rounded-xl border-2 border-black font-bold flex items-center gap-2 hover:-translate-y-1 transition-transform hand-drawn-border" onclick="openAddService()">
            <span class="material-symbols-outlined font-bold">add</span> YENİ HİZMET EKLE
        </button>
    </div>

    <?php if (empty($services)): ?>
    <div class="bg-surface-container-highest border-4 border-black border-dashed p-16 text-center rotate-1 rounded-xl">
        <span class="material-symbols-outlined text-6xl opacity-30 mb-4" data-icon="content_cut">content_cut</span>
        <h2 class="font-headline font-black text-3xl">Henüz hizmet eklemediniz</h2>
        <p class="font-medium text-on-surface-variant mt-2 mb-6">Müşterilerinize sunacağınız saç, sakal, cilt bakımı vb. işlemleri listeleyin.</p>
        <button class="bg-black text-white px-8 py-3 rounded-xl border-2 border-black font-bold uppercase transition-transform hover:-translate-y-1" onclick="openAddService()">İlk Hizmeti Ekle</button>
    </div>
    <?php else: ?>

    <div class="bg-surface-container-lowest border-4 border-black sketch-shadow rounded-xl overflow-hidden overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-widest font-black border-b-4 border-black">
                    <th class="px-6 py-4">Hizmet Adı</th>
                    <th class="px-6 py-4">Fiyat</th>
                    <th class="px-6 py-4">Süre</th>
                    <th class="px-6 py-4 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody id="servicesTable" class="divide-y-2 divide-black/10">
                <?php foreach ($services as $s): ?>
                <tr id="svc-<?= $s['id'] ?>" class="hover:bg-[#fefee5] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="inline-flex items-center gap-2 font-bold text-lg">
                            <span class="bg-surface-container border-2 border-black rounded-lg p-1 aspect-square flex items-center justify-center mr-2"><span class="material-symbols-outlined">content_cut</span></span>
                            <?= htmlspecialchars($s['service_name']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-headline font-black text-2xl text-secondary">
                        ₺<?= number_format($s['price'], 2) ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-surface-container-highest border-2 border-black px-3 py-1 font-bold text-sm tracking-widest whitespace-nowrap">
                            <?= $s['duration_minutes'] ?> DK
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="w-10 h-10 bg-white border-2 border-black rounded-lg hover:-translate-y-1 flex items-center justify-center transition-transform hover:bg-[#e7edb4]"
                                onclick="openEditService(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['service_name'])) ?>', <?= $s['price'] ?>, <?= $s['duration_minutes'] ?>)" title="Düzenle">
                                <span class="material-symbols-outlined" style="font-size:20px">edit</span>
                            </button>
                            <button class="w-10 h-10 bg-[#fe8b70] text-[#742410] border-2 border-black rounded-lg hover:-translate-y-1 flex items-center justify-center transition-transform hover:bg-[#a54731] hover:text-white"
                                onclick="deleteService(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['service_name'])) ?>')" title="Sil">
                                <span class="material-symbols-outlined" style="font-size:20px">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>
</div>

<!-- Hizmet Ekle/Düzenle Formu -->
<template id="serviceFormTemplate">
    <form id="serviceForm" class="flex flex-col gap-6">
        <input type="hidden" name="service_id" id="serviceIdInput">

        <div>
            <label class="block font-bold uppercase text-xs tracking-widest mb-2">Hizmet Adı <span class="text-error">*</span></label>
            <input type="text" name="service_name" id="serviceNameInput" list="presetServices" required maxlength="200" placeholder="Aşağıdan seçin veya yazın..."
                   class="w-full bg-surface-container-lowest border-2 border-black rounded-lg px-4 py-3 font-headline font-bold focus:outline-none focus:border-secondary transition-colors">
            <datalist id="presetServices">
                <option value="Saç Kesimi">
                <option value="Sakal Tıraşı">
                <option value="Saç Yıkama">
                <option value="Cilt Bakımı">
                <option value="Maske">
                <option value="Kaş Alma">
                <option value="Ağda">
                <option value="Saç Boyama">
            </datalist>
            
            <div class="mt-3 flex flex-wrap gap-2" id="presetButtons">
                <button type="button" class="bg-[#e7edb4] border border-black hover:bg-black hover:text-white px-3 py-1 rounded text-xs font-bold transition-colors" onclick="document.getElementById('serviceNameInput').value='Saç Kesimi'; document.getElementById('servicePriceInput').focus();">Saç Kesimi</button>
                <button type="button" class="bg-[#e7edb4] border border-black hover:bg-black hover:text-white px-3 py-1 rounded text-xs font-bold transition-colors" onclick="document.getElementById('serviceNameInput').value='Sakal Tıraşı'; document.getElementById('servicePriceInput').focus();">Sakal Tıraşı</button>
                <button type="button" class="bg-[#e7edb4] border border-black hover:bg-black hover:text-white px-3 py-1 rounded text-xs font-bold transition-colors" onclick="document.getElementById('serviceNameInput').value='Cilt Bakımı'; document.getElementById('servicePriceInput').focus();">Cilt Bakımı</button>
                <button type="button" class="bg-[#e7edb4] border border-black hover:bg-black hover:text-white px-3 py-1 rounded text-xs font-bold transition-colors" onclick="document.getElementById('serviceNameInput').value='Maske'; document.getElementById('servicePriceInput').focus();">Maske</button>
                <button type="button" class="bg-[#e7edb4] border border-black hover:bg-black hover:text-white px-3 py-1 rounded text-xs font-bold transition-colors" onclick="document.getElementById('serviceNameInput').value='Kaş Alma'; document.getElementById('servicePriceInput').focus();">Kaş Alma</button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block font-bold uppercase text-xs tracking-widest mb-2">Fiyat (₺) <span class="text-error">*</span></label>
                <input type="number" name="price" id="servicePriceInput" min="0" step="0.50" placeholder="150.00" required
                       class="w-full bg-surface-container-lowest border-2 border-black rounded-lg px-4 py-3 font-headline font-black focus:outline-none focus:border-secondary transition-colors text-secondary placeholder:opacity-50 placeholder:font-bold">
            </div>
            <div>
                <label class="block font-bold uppercase text-xs tracking-widest mb-2">Süre (dakika) <span class="text-error">*</span></label>
                <input type="number" name="duration_minutes" id="serviceDurInput" min="5" step="5" placeholder="30" required
                       class="w-full bg-surface-container-lowest border-2 border-black rounded-lg px-4 py-3 font-headline font-bold focus:outline-none focus:border-secondary transition-colors">
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-4 border-t-2 border-dashed border-black/20 pt-6">
            <button type="button" class="px-6 py-3 border-2 border-black rounded-xl font-bold uppercase hover:bg-black hover:text-white transition-colors" onclick="closeModal()">İPTAL</button>
            <button type="submit" class="bg-secondary text-white px-8 py-3 rounded-xl border-2 border-black font-bold uppercase hover:-translate-y-1 transition-transform flex items-center gap-2" id="serviceSaveBtn">KAYDET</button>
        </div>
    </form>
</template>

<script>
function openAddService() {
    const tpl = document.getElementById('serviceFormTemplate').content.cloneNode(true);
    openModal('Yeni Hizmet Ekle', tpl);
    document.getElementById('serviceForm').addEventListener('submit', submitServiceForm.bind(null, 'add_service'));
}

function openEditService(id, name, price, duration) {
    const tpl = document.getElementById('serviceFormTemplate').content.cloneNode(true);
    openModal('Hizmet Düzenle', tpl);
    document.getElementById('serviceIdInput').value   = id;
    document.getElementById('serviceNameInput').value = name;
    document.getElementById('servicePriceInput').value = price;
    document.getElementById('serviceDurInput').value   = duration;
    document.getElementById('serviceForm').addEventListener('submit', submitServiceForm.bind(null, 'edit_service'));
}

async function submitServiceForm(action, e) {
    e.preventDefault();
    const btn = document.getElementById('serviceSaveBtn');
    btn.disabled = true;

    const fd = new FormData(document.getElementById('serviceForm'));
    fd.set('action', action);

    const res  = await fetch('berber/api.php', { method: 'POST', body: fd });
    const data = await res.json();
    showToast(data.success ? 'success' : 'error', data.message);
    btn.disabled = false;
    if (data.success) { closeModal(); setTimeout(() => location.reload(), 800); }
}

async function deleteService(id, name) {
    if (!confirm(`"${name}" hizmetini silmek istediğinize emin misiniz?`)) return;
    const fd = new FormData();
    fd.set('action', 'delete_service');
    fd.set('service_id', id);
    const res  = await fetch('berber/api.php', { method: 'POST', body: fd });
    const data = await res.json();
    showToast(data.success ? 'success' : 'error', data.message);
    if (data.success) document.getElementById('svc-' + id)?.remove();
}
</script>
<?php endif; ?>
