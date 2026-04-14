<?php
if ($userRoleInShop === 'Çalışan'):
?>
<div class="max-w-screen-xl mx-auto px-6 py-20 text-center"><div class="bg-error-container text-on-error-container p-12 border-4 border-error inline-block rounded-xl sketch-shadow -rotate-1"><span class="material-symbols-outlined text-6xl mb-4">block</span><h1 class="text-3xl font-headline font-black uppercase tracking-widest">Yetki Sınırı</h1><p class="mt-2 font-medium">Bu sayfaya giriş yetkiniz bulunmuyor. Yalnızca Patron yetkisine sahipseniz çalışanları yönetebilirsiniz.</p></div></div>
<?php elseif (!$user['is_plus']): ?>
<div class="max-w-screen-xl mx-auto px-6 py-20 text-center">
    <div class="bg-[#fefee5] border-4 border-black sketch-shadow p-12 inline-block -rotate-1 rounded-2xl">
        <span class="material-symbols-outlined text-8xl text-[#eab308] mb-4" style="font-variation-settings: 'FILL' 1;">stars</span>
        <h2 class="text-4xl font-headline font-black mb-4">Plus Üyeliğe Özel</h2>
        <p class="text-xl text-on-surface-variant mb-6 font-medium max-w-lg mx-auto">
            Plus üyelik ile dükkanınıza sınırsız çalışan ekleyebilir, işletme hacminizi anında artırabilirsiniz.
        </p>
        <button class="bg-black text-[#fbbf24] px-8 py-4 rounded-xl border-2 border-black font-black uppercase text-lg transition-transform hover:-translate-y-1">Plus'a Geç</button>
    </div>
</div>
<?php elseif (!$shop): ?>
<div class="max-w-screen-xl mx-auto px-6 py-20 text-center">
    <div class="bg-surface-container-lowest sketch-border p-12 inline-block rotate-1 border-4 border-black">
        <span class="material-symbols-outlined text-8xl text-secondary mb-4 opacity-80" data-icon="store_off">store_off</span>
        <h2 class="text-4xl font-headline font-black mb-4">Önce bir dükkan oluşturun</h2>
        <a href="?page=dukkan" class="bg-black text-white px-8 py-3 rounded-xl border-2 border-black font-bold uppercase transition-transform hover:-translate-y-1 inline-block mt-4">Dükkan Oluştur</a>
    </div>
</div>
<?php else:
    $stmt = $pdo->prepare("
        SELECT u.id, u.full_name, u.email, u.created_at
        FROM shop_employees se
        JOIN users u ON se.employee_id = u.id
        WHERE se.shop_id = ?
        ORDER BY u.full_name
    ");
    $stmt->execute([$shop['id']]);
    $employees = $stmt->fetchAll();
?>

<div class="max-w-screen-xl mx-auto px-6 py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b-4 border-black pb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-4xl font-headline font-black italic text-black">Personel</h2>
            <span class="bg-[#fbbf24] text-black border-2 border-black px-2 py-1 text-xs font-black uppercase tracking-widest rounded rotate-3">
                ⭐ PLUS
            </span>
        </div>
        <button class="bg-black text-white px-6 py-3 rounded-xl border-2 border-black font-bold flex items-center gap-2 hover:-translate-y-1 transition-transform hand-drawn-border" onclick="openAddEmployee()">
            <span class="material-symbols-outlined">person_add</span> YENİ ÇALIŞAN
        </button>
    </div>

    <?php if (empty($employees)): ?>
    <div class="bg-surface-container-highest border-4 border-black border-dashed p-16 text-center -rotate-1 rounded-xl">
        <span class="material-symbols-outlined text-6xl opacity-30 mb-4 text-black">groups</span>
        <h2 class="font-headline font-black text-3xl mb-2">Ekibinizi kurma zamanı</h2>
        <p class="font-medium text-on-surface-variant max-w-lg mx-auto mb-6">Sisteme daha önceden e-posta ile kayıt olmuş berberleri dükkanınıza davet ederek hemen randevu almalarını sağlayın.</p>
        <button class="bg-secondary text-white px-8 py-3 rounded-xl border-2 border-black font-black uppercase transition-transform hover:-translate-y-1" onclick="openAddEmployee()">İlk Çalışanı Ekle</button>
    </div>
    <?php else: ?>

    <div class="bg-surface-container-lowest border-4 border-black sketch-shadow rounded-xl overflow-hidden overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-widest font-black border-b-4 border-black">
                    <th class="px-6 py-4">AD SOYAD</th>
                    <th class="px-6 py-4">E-POSTA YAZIŞMASI</th>
                    <th class="px-6 py-4 text-center">KATILIM</th>
                    <th class="px-6 py-4 text-right">İŞLEM</th>
                </tr>
            </thead>
            <tbody id="employeesTable" class="divide-y-2 divide-black/10">
                <?php foreach ($employees as $emp): ?>
                <tr id="emp-<?= $emp['id'] ?>" class="hover:bg-surface-container-highest transition-colors group">
                    <td class="px-6 py-4 font-bold text-lg flex items-center gap-3">
                        <div class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center font-black text-xl border-2 border-black shrink-0">
                            <?= mb_strtoupper(mb_substr($emp['full_name'],0,1)) ?>
                        </div>
                        <?= htmlspecialchars($emp['full_name']) ?>
                    </td>
                    <td class="px-6 py-4 font-medium opacity-70">
                        <?= htmlspecialchars($emp['email']) ?>
                    </td>
                    <td class="px-6 py-4 text-center font-headline font-bold italic">
                        <?= date('d M Y', strtotime($emp['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="bg-error text-white font-bold uppercase text-xs px-4 py-2 rounded-lg border-2 border-black hover:-translate-y-1 transition-transform"
                            onclick="removeEmployee(<?= $emp['id'] ?>, <?= json_encode($emp['full_name']) ?>)">
                            ÇIKAR
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>
</div>

<!-- Çalışan Arama Modal Template -->
<template id="addEmployeeTemplate">
    <div class="flex flex-col gap-6 font-['Work_Sans']">
        <p class="font-medium text-sm text-on-surface-variant uppercase tracking-widest bg-surface-container p-4 border-l-4 border-black border-2 border-dashed border-black/20">
            Sisteme kayıtlı berberin e-posta adresini girerek arama yapın.
        </p>

        <div>
            <label class="block font-bold uppercase text-xs tracking-widest mb-2">Berber E-Posta</label>
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="email" id="empEmailInput" placeholder="ornek@berber.com"
                       class="w-full bg-surface-container-lowest border-2 border-black rounded-lg px-4 py-3 font-headline font-bold focus:outline-none focus:border-secondary transition-colors">
                <button type="button" class="w-full sm:w-auto bg-black text-white px-8 py-3 rounded-lg border-2 border-black font-black uppercase hover:-translate-y-1 transition-transform cursor-pointer flex items-center justify-center gap-2" onclick="searchEmployee()">
                    <span class="material-symbols-outlined text-xl">search</span> ARA
                </button>
            </div>
        </div>

        <div id="searchResult" class="hidden"></div>
    </div>
</template>

<script>
function openAddEmployee() {
    const tpl = document.getElementById('addEmployeeTemplate').content.cloneNode(true);
    openModal('Yeni Çalışan Ekle', tpl);
}

async function searchEmployee() {
    const email = document.getElementById('empEmailInput').value.trim();
    const result = document.getElementById('searchResult');

    if (!email) { showToast('error', 'Lütfen aramak için E-posta girin.'); return; }

    result.classList.remove('hidden');
    result.innerHTML = '<div class="text-center py-4 font-bold opacity-50 uppercase tracking-widest text-sm animate-pulse">Sistem aranıyor...</div>';

    const fd = new FormData();
    fd.set('action', 'search_employee');
    fd.set('email', email);

    const res  = await fetch('berber/api.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (!data.success) {
        result.innerHTML = `
            <div class="bg-error-container text-[#742410] border-2 border-[#a54731] p-4 font-bold rounded-lg flex items-center gap-2">
                <span class="material-symbols-outlined">error</span> ${data.message}
            </div>`;
        return;
    }

    const emp = data.employee;
    result.innerHTML = `
        <div class="bg-[#fefee5] border-2 border-black p-4 mt-2 hover:shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl">
            <div class="flex items-center gap-4 w-full">
                <div class="w-12 h-12 bg-secondary text-white rounded-full flex items-center justify-center font-black text-2xl border-2 border-black shrink-0">
                    ${emp.full_name[0].toUpperCase()}
                </div>
                <div class="flex flex-col">
                    <span class="font-headline font-black text-xl italic text-black leading-tight">${emp.full_name}</span>
                    <span class="text-sm font-medium opacity-60">${emp.email}</span>
                </div>
            </div>
            <button class="w-full sm:w-auto bg-secondary text-white px-6 py-2 rounded-lg border-2 border-black font-black uppercase hover:-translate-y-1 transition-transform" onclick="confirmAddEmployee(${emp.id})">EKLE</button>
        </div>`;
}

async function confirmAddEmployee(id) {
    const fd = new FormData();
    fd.set('action', 'add_employee');
    fd.set('employee_id', id);

    const res  = await fetch('berber/api.php', { method: 'POST', body: fd });
    const data = await res.json();
    showToast(data.success ? 'success' : 'error', data.message);
    if (data.success) { closeModal(); setTimeout(() => location.reload(), 800); }
}

async function removeEmployee(id, name) {
    if (!confirm(`${name} adlı çalışanı dükkanınızdan çıkarmak istediğinizden emin misiniz?`)) return;

    const fd = new FormData();
    fd.set('action', 'remove_employee');
    fd.set('employee_id', id);

    const res  = await fetch('berber/api.php', { method: 'POST', body: fd });
    const data = await res.json();
    showToast(data.success ? 'success' : 'error', data.message);
    if (data.success) document.getElementById('emp-' + id)?.remove();
}
</script>
<?php endif; ?>
