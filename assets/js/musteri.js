/* ============================================================
   Müşteri Paneli — Randevu Booking Controller
   ============================================================ */
(function () {
    'use strict';

    /* ---- Booking State ---- */
    const B = {
        shopId:       null,
        serviceId:    null,
        serviceName:  null,
        servicePrice: 0,
        serviceDur:   0,
        extras:       [], // {id, name, price, duration}
        employeeId:   null,
        employeeName: null,
        date:         null,
        time:         null,
    };

    let currentStep = 1;

    /* ============================================================
       openBooking — Randevu modalını aç (Adım 1: Hizmet Seç)
    ============================================================ */
    window.openBooking = function (shopId) {
        B.shopId       = shopId;
        B.serviceId    = null;
        B.serviceName  = null;
        B.servicePrice = 0;
        B.serviceDur   = 0;
        B.extras       = [];
        B.employeeId   = null;
        B.employeeName = null;
        B.date         = null;
        B.time         = null;
        currentStep    = 1;

        const tpl = document.getElementById('bookingTemplate').content.cloneNode(true);
        openModal('Randevu Al', tpl);

        populateServices();
        populateEmployees();
    };

    /* ---- Adım 1: Hizmet listesini doldur ---- */
    function populateServices() {
        const container = document.getElementById('servicesContainer');
        if (!container) return;

        const services = window._shopServices || [];
        if (services.length === 0) {
            container.innerHTML = '<p class="text-sm font-medium opacity-50 text-center py-4">Bu dükkanda henüz hizmet bulunmuyor.</p>';
            return;
        }

        container.innerHTML = services.map(s => `
            <button type="button"
                class="service-select-btn flex items-center justify-between p-4 border-2 border-black rounded-xl hover:bg-[#fefee5] transition-colors text-left w-full"
                data-id="${s.id}" data-name="${s.service_name}" data-price="${s.price}" data-dur="${s.duration_minutes}">
                <div>
                    <div class="font-black uppercase tracking-widest text-sm">${s.service_name}</div>
                    <div class="text-xs font-bold opacity-60 mt-0.5">⏱ ${s.duration_minutes} dk</div>
                </div>
                <div class="svc-price font-black text-secondary text-lg ml-4 shrink-0">₺${parseFloat(s.price).toFixed(2)}</div>
            </button>
        `).join('');

        container.querySelectorAll('.service-select-btn').forEach(btn => {
            btn.addEventListener('click', () => selectService(btn));
        });
    }

    function selectService(btn) {
        document.querySelectorAll('.service-select-btn').forEach(b => {
            b.classList.remove('selected-service');
            b.style.background = '';
            b.style.color = '';
            b.querySelector('.svc-price').style.color = '';
        });

        btn.classList.add('selected-service');

        B.serviceId    = parseInt(btn.dataset.id);
        B.serviceName  = btn.dataset.name;
        B.servicePrice = parseFloat(btn.dataset.price);
        B.serviceDur   = parseInt(btn.dataset.dur);

        const nextBtn = document.getElementById('serviceNextBtn');
        if (nextBtn) nextBtn.disabled = false;
    }

    /* ---- Adım 2: Ekstralar kurulumu ---- */
    function setupExtras() {
        // Seçilen hizmet kutusu
        const box = document.getElementById('selectedServiceBox');
        if (box) {
            box.innerHTML = `
                <div>
                    <div class="font-bold uppercase text-[10px] tracking-widest">Ana Hizmet</div>
                    <div class="font-black text-xl text-black">✂️ ${B.serviceName}</div>
                </div>
                <div class="font-black text-xl">₺${B.servicePrice.toFixed(2)}</div>
            `;
        }

        populateExtras(B.serviceId);
        recalcTotals();
    }

    function populateExtras(excludedServiceId) {
        const container = document.getElementById('extraServicesContainer');
        if (!container) return;

        const services = window._shopServices || [];
        const addons = services.filter(s => s.id != excludedServiceId);

        if (addons.length === 0) {
            container.innerHTML = '<p class="text-sm font-medium opacity-50">Bu hizmete eklenebilecek ek seçenek bulunamadı.</p>';
            return;
        }

        container.innerHTML = addons.map(s => `
            <label class="flex items-center justify-between p-3 border-2 border-black rounded-lg cursor-pointer hover:bg-surface-container-highest transition-colors">
                <div class="flex items-center gap-3">
                    <input type="checkbox" class="w-5 h-5 border-2 border-black rounded text-black focus:ring-black extra-cb"
                           value="${s.id}" data-name="${s.service_name}" data-price="${s.price}" data-dur="${s.duration_minutes}">
                    <span class="font-bold text-sm uppercase tracking-widest">${s.service_name}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold font-body opacity-60">⏱ ${s.duration_minutes}dk</span>
                    <span class="font-black text-secondary">+₺${parseFloat(s.price).toFixed(2)}</span>
                </div>
            </label>
        `).join('');

        container.querySelectorAll('.extra-cb').forEach(cb => {
            cb.addEventListener('change', (e) => {
                if (e.target.checked) {
                    B.extras.push({
                        id:    parseInt(e.target.value),
                        name:  e.target.dataset.name,
                        price: parseFloat(e.target.dataset.price),
                        dur:   parseInt(e.target.dataset.dur)
                    });
                } else {
                    B.extras = B.extras.filter(ex => ex.id != e.target.value);
                }
                recalcTotals();
            });
        });
    }

    function recalcTotals() {
        let tPrice = B.servicePrice;
        let tDur   = B.serviceDur;
        B.extras.forEach(ex => {
            tPrice += ex.price;
            tDur   += ex.dur;
        });
        const c = document.getElementById('cumulPriceDur');
        if (c) c.innerHTML = `₺${tPrice.toFixed(2)} <span class="text-xs text-on-surface-variant">(${tDur} dk)</span>`;
    }

    /* ---- Personel dropdown ---- */
    function populateEmployees() {
        const sel = document.getElementById('employeeSelect');
        if (!sel) return;

        const employees = window._shopEmployees || [];

        if (employees.length === 0) {
            sel.innerHTML = '<option value="">Bu dükkanda personel bulunamadı.</option>';
            return;
        }

        sel.innerHTML = '<option value="">Personel seçin…</option>' +
            employees.map(e => `<option value="${e.id}" data-name="${e.full_name}">${e.full_name}</option>`).join('');
    }

    /* ============================================================
       bookingNextStep — Adımlar arası geçiş (1-6)
    ============================================================ */
    window.bookingNextStep = function (targetStep) {

        // Step 1 → 2: Hizmet seçildi mi?
        if (targetStep === 2 && currentStep === 1) {
            if (!B.serviceId) {
                showToast('error', 'Lütfen bir hizmet seçin.');
                return;
            }
            setupExtras();
        }

        // Step 3 → 4: Personel seçildi mi?
        if (targetStep === 4 && currentStep === 3) {
            const sel = document.getElementById('employeeSelect');
            if (!sel || !sel.value) {
                showToast('error', 'Lütfen bir personel seçin.');
                return;
            }
            B.employeeId   = parseInt(sel.value);
            B.employeeName = sel.options[sel.selectedIndex].dataset.name;
        }

        // Step 4 → 5: Tarih seçildi mi?
        if (targetStep === 5 && currentStep === 4) {
            const dateInput = document.getElementById('dateInput');
            if (!dateInput || !dateInput.value) {
                showToast('error', 'Lütfen bir tarih seçin.');
                return;
            }
            B.date = dateInput.value;
            B.time = null;
            loadSlots();

            const nxtBtn = document.getElementById('slotNextBtn');
            if (nxtBtn) nxtBtn.disabled = true;
        }

        // Step 5 → 6: Saat seçildi mi?
        if (targetStep === 6 && currentStep === 5) {
            if (!B.time) {
                showToast('error', 'Lütfen bir saat seçin.');
                return;
            }
            renderSummary();
        }

        // Panel geçişi
        document.getElementById('stepPanel' + currentStep)?.classList.remove('active');
        document.getElementById('stepPanel' + targetStep)?.classList.add('active');

        updateStepDots(targetStep);
        currentStep = targetStep;
    };

    function updateStepDots(active) {
        document.querySelectorAll('.step-dot').forEach(dot => {
            const n = parseInt(dot.dataset.step);
            dot.classList.remove('active', 'done');
            if (n < active)  dot.classList.add('done');
            if (n === active) dot.classList.add('active');
        });
        document.querySelectorAll('.step-line').forEach((line, i) => {
            line.classList.toggle('done', i + 1 < active);
        });
    }

    /* ============================================================
       loadSlots — AJAX ile uygun saatleri getir
    ============================================================ */
    async function loadSlots() {
        const container = document.getElementById('slotsContainer');
        if (!container) return;

        container.innerHTML = '<p class="text-muted center">Saatler yükleniyor…</p>';

        const fd = new FormData();
        fd.set('action',         'get_slots');
        fd.set('shop_id',        B.shopId);
        fd.set('employee_id',    B.employeeId);
        fd.set('service_id',     B.serviceId);
        fd.set('date',           B.date);
        fd.set('extra_services', JSON.stringify(B.extras.map(e => e.id)));

        try {
            const res  = await fetch('musteri/api.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (!data.success) {
                container.innerHTML = `<p class="text-muted center font-bold text-error">⚠️ ${data.message}</p>`;
                return;
            }

            renderSlots(data.slots);
        } catch {
            container.innerHTML = '<p class="text-muted center">Sunucuya ulaşılamadı.</p>';
        }
    }

    function renderSlots(slots) {
        const container = document.getElementById('slotsContainer');
        if (!container) return;

        const available = slots.filter(s => s.available);
        if (available.length === 0) {
            container.innerHTML = '<p class="text-sm font-bold opacity-60 text-center" style="padding:20px">Bu tarihte müsait saat bulunmuyor.<br>Lütfen başka bir tarih deneyin.</p>';
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'slots-grid';

        slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = slot.time;
            btn.className = 'slot-btn ' + (slot.available ? 'available' : 'unavailable');
            btn.disabled = !slot.available;

            if (slot.available) {
                btn.addEventListener('click', () => selectSlot(slot.time, btn));
            }

            grid.appendChild(btn);
        });

        container.innerHTML = '';
        container.appendChild(grid);
    }

    function selectSlot(time, btn) {
        document.querySelectorAll('.slot-btn.selected').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        B.time = time;

        const nxtBtn = document.getElementById('slotNextBtn');
        if (nxtBtn) nxtBtn.disabled = false;
    }

    window.selectManualTime = function () {
        const input = document.getElementById('manualTimeInput');
        if (!input || !input.value) {
            showToast('error', 'Lütfen geçerli bir saat girin.');
            return;
        }

        if (!/^\d{2}:\d{2}$/.test(input.value)) {
            showToast('error', 'Saat formatı HH:MM olmalı.');
            return;
        }

        document.querySelectorAll('.slot-btn.selected').forEach(b => b.classList.remove('selected'));

        B.time = input.value;

        const nxtBtn = document.getElementById('slotNextBtn');
        if (nxtBtn) nxtBtn.disabled = false;

        showToast('success', input.value + ' saati seçildi.');
    };

    /* ============================================================
       renderSummary — Step 6 özet kartı
    ============================================================ */
    function renderSummary() {
        const el = document.getElementById('bookingSummary');
        if (!el) return;

        let tPrice = B.servicePrice;
        let tDur   = B.serviceDur;
        let extrasHtml = '';

        if (B.extras.length > 0) {
            extrasHtml = B.extras.map(e => `
                <div class="summary-row opacity-80 pl-4 border-none pb-0 text-sm">
                    <span class="summary-key">+ ${e.name}</span>
                    <span class="summary-val">+₺${e.price.toFixed(2)}</span>
                </div>
            `).join('');

            B.extras.forEach(e => {
                tPrice += e.price;
                tDur   += e.dur;
            });
        }

        const dateFormatted = new Date(B.date + 'T' + B.time + ':00').toLocaleDateString('tr-TR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });

        el.innerHTML = `
            <div class="summary-row">
                <span class="summary-key">✂️ Hizmet</span>
                <span class="summary-val font-bold">${B.serviceName}</span>
            </div>
            ${extrasHtml}
            <div class="summary-row mt-2">
                <span class="summary-key">👤 Personel</span>
                <span class="summary-val">${B.employeeName}</span>
            </div>
            <div class="summary-row">
                <span class="summary-key">📅 Tarih</span>
                <span class="summary-val">${dateFormatted}</span>
            </div>
            <div class="summary-row">
                <span class="summary-key">🕐 Saat</span>
                <span class="summary-val">${B.time}</span>
            </div>
            <div class="summary-row">
                <span class="summary-key">⏱ Toplam Süre</span>
                <span class="summary-val">${tDur} dakika</span>
            </div>
            <div class="summary-row border-none mt-2 pt-2 border-t-2 border-black">
                <span class="summary-key">💰 Toplam Ücret</span>
                <span class="summary-price text-secondary">₺${tPrice.toFixed(2)}</span>
            </div>
        `;
    }

    /* ============================================================
       confirmBooking — Randevuyu kaydet
    ============================================================ */
    window.confirmBooking = async function () {
        const btn     = document.getElementById('confirmBtn');
        const spinner = document.getElementById('confirmSpinner');

        if (!btn || !B.time || !B.date) return;

        btn.disabled = true;
        spinner?.classList.remove('hidden');

        const datetime = B.date + ' ' + B.time + ':00';

        const fd = new FormData();
        fd.set('action',           'book_appointment');
        fd.set('shop_id',          B.shopId);
        fd.set('employee_id',      B.employeeId);
        fd.set('service_id',       B.serviceId);
        fd.set('appointment_time', datetime);
        fd.set('extra_services',   JSON.stringify(B.extras.map(e => e.id)));

        try {
            const res  = await fetch('musteri/api.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                closeModal();
                showToast('success', data.message);
                setTimeout(() => {
                    window.location.href = 'musteri_paneli.php?page=randevularim';
                }, 1500);
            } else {
                showToast('error', data.message);
                btn.disabled = false;
                spinner?.classList.add('hidden');
            }
        } catch {
            showToast('error', 'Sunucuya ulaşılamadı.');
            btn.disabled = false;
            spinner?.classList.add('hidden');
        }
    };

})();
