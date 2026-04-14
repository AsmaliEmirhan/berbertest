<?php
if ($userRoleInShop === 'Çalışan') {
    echo '<div class="max-w-screen-xl mx-auto px-6 py-20 text-center"><div class="bg-error-container text-on-error-container p-12 border-4 border-error inline-block rounded-xl sketch-shadow -rotate-1"><span class="material-symbols-outlined text-6xl mb-4">block</span><h1 class="text-3xl font-headline font-black uppercase tracking-widest">Yetki Sınırı</h1><p class="mt-2 font-medium">İstatistik sayfasına giriş yetkiniz bulunmuyor. Yalnızca Patron yetkisine sahipseniz burayı görebilirsiniz.</p></div></div>';
    return;
}

if (!$user['is_plus']) {
    echo '<div class="text-center p-12"><h1 class="text-3xl font-black text-red-600">Sadece Plus Üyelere Özeldir</h1></div>';
    return;
}

if (!$shop) {
    echo '<div class="text-center p-12"><p class="text-xl font-bold">Önce bir dükkan oluşturmalısınız.</p></div>';
    return;
}

$shopId = $shop['id'];

// Toplam Ciro ve Toplam Tıraş Sayısı (Tüm Zamanlar)
$stmt = $pdo->prepare("SELECT COUNT(id) as total_appointments, COALESCE(SUM(price_at_that_time), 0) as total_revenue FROM appointments WHERE shop_id = ? AND status = 'tamamlandi'");
$stmt->execute([$shopId]);
$overall = $stmt->fetch();

// --- 1. AYLIK GRAFİK VERİSİ (Son 6 Ay) ---
// MySQL'de son 6 ayın randevularını aya göre gruplayalım
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(appointment_time, '%Y-%m') as month_str,
           COUNT(id) as count,
           SUM(price_at_that_time) as revenue
    FROM appointments 
    WHERE shop_id = ? AND status = 'tamamlandi' 
      AND appointment_time >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month_str
    ORDER BY month_str ASC
");
$stmt->execute([$shopId]);
$monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$chartLabels = [];
$chartCounts = [];
$chartRevenues = [];

foreach ($monthlyData as $row) {
    $dateObj   = DateTime::createFromFormat('Y-m', $row['month_str']);
    // Türkçe Ay
    $monthsTr  = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    $monthName = $monthsTr[(int)$dateObj->format('n') - 1];
    
    $chartLabels[]   = $monthName . ' ' . $dateObj->format('y');
    $chartCounts[]   = (int)$row['count'];
    $chartRevenues[] = (float)$row['revenue'];
}

// --- 2. EN ÇOK KESİM YAPAN ÇALIŞANLAR (Top 5) ---
$stmt = $pdo->prepare("
    SELECT u.full_name, COUNT(a.id) as cut_count, SUM(a.price_at_that_time) as total_earned
    FROM appointments a
    JOIN users u ON a.employee_id = u.id
    WHERE a.shop_id = ? AND a.status = 'tamamlandi'
    GROUP BY a.employee_id
    ORDER BY cut_count DESC
    LIMIT 5
");
$stmt->execute([$shopId]);
$topEmployees = $stmt->fetchAll();

// --- 3. EN SIK GELEN MÜŞTERİLER (Top 5 - Sadece uygulamalı olanlar) ---
$stmt = $pdo->prepare("
    SELECT u.full_name, COUNT(a.id) as visit_count, SUM(a.price_at_that_time) as total_spent
    FROM appointments a
    JOIN users u ON a.customer_id = u.id
    WHERE a.shop_id = ? AND a.status = 'tamamlandi' AND a.customer_id IS NOT NULL
    GROUP BY a.customer_id
    ORDER BY visit_count DESC
    LIMIT 5
");
$stmt->execute([$shopId]);
$topCustomers = $stmt->fetchAll();

?>

<div class="max-w-screen-xl mx-auto px-6 py-6 pb-20">
    <header class="mb-10 text-center md:text-left">
        <h1 class="text-5xl font-headline font-extrabold text-black tracking-tighter mb-2 italic">Dükkan İstatistikleri</h1>
        <p class="text-lg text-on-surface-variant font-medium uppercase tracking-widest">Performansınızı Analiz Edin</p>
    </header>

    <!-- Genel Özet -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <div class="bg-surface-container-highest border-4 border-black p-8 shadow-[6px_6px_0px_rgba(0,0,0,1)]">
            <h3 class="font-headline font-black text-2xl uppercase opacity-80 mb-2">Toplam Ciro (Tüm Zamanlar)</h3>
            <p class="font-headline font-extrabold text-5xl text-secondary">₺<?= number_format($overall['total_revenue'], 2, ',', '.') ?></p>
        </div>
        <div class="bg-[#e7edb4] border-4 border-black p-8 shadow-[6px_6px_0px_rgba(0,0,0,1)] -rotate-1">
            <h3 class="font-headline font-black text-2xl uppercase opacity-80 mb-2">Toplam Tamamlanan Kesim</h3>
            <p class="font-headline font-extrabold text-5xl text-black"><?= number_format($overall['total_appointments']) ?> Tıraş</p>
        </div>
    </div>

    <!-- Grafik Bölümü -->
    <div class="bg-white border-4 border-black p-6 rounded-2xl shadow-[8px_8px_0px_rgba(0,0,0,1)] mb-12">
        <h2 class="font-headline font-black text-3xl mb-6">Son 6 Aylık Trend</h2>
        <div class="w-full" style="height: 400px;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Liderlik Tabloları -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        <!-- En Çok Kesim Yapanlar -->
        <div class="bg-surface-container-lowest border-4 border-black p-8 shadow-[6px_6px_0px_rgba(0,0,0,1)] rotate-1">
            <h2 class="font-headline font-black text-2xl uppercase mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-4xl text-secondary" style="font-variation-settings: 'FILL' 1;">content_cut</span>
                Yıldız Çalışanlar
            </h2>
            <?php if(empty($topEmployees)): ?>
                <p class="text-on-surface-variant font-medium italic">Henüz tamamlanan bir randevu bulunmuyor.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach($topEmployees as $index => $emp): ?>
                    <li class="flex items-center justify-between p-4 border-2 border-black bg-white group hover:-translate-y-1 transition-transform">
                        <div class="flex items-center gap-4">
                            <span class="font-headline font-black text-2xl opacity-40 group-hover:text-secondary group-hover:opacity-100 transition-colors">#<?= $index + 1 ?></span>
                            <div>
                                <h4 class="font-bold text-lg leading-tight uppercase"><?= htmlspecialchars($emp['full_name']) ?></h4>
                                <span class="text-xs font-bold tracking-widest opacity-60"><?= number_format($emp['cut_count']) ?> KESİM</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-black text-xl text-[#006017]">₺<?= number_format($emp['total_earned'], 0, '', '.') ?></span>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- En Sık Gelen Müşteriler -->
        <div class="bg-[#fafcda] border-4 border-black p-8 shadow-[6px_6px_0px_rgba(0,0,0,1)] -rotate-1">
            <h2 class="font-headline font-black text-2xl uppercase mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-4xl text-black" style="font-variation-settings: 'FILL' 1;">diamond</span>
                Sadık Müşteriler
            </h2>
            <?php if(empty($topCustomers)): ?>
                <p class="text-on-surface-variant font-medium italic">Henüz kayıtlı müşteri randevusu bulunmuyor.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach($topCustomers as $index => $cus): ?>
                    <li class="flex items-center justify-between p-4 border-2 border-dashed border-black bg-white group hover:translate-x-2 transition-transform">
                        <div class="flex items-center gap-4">
                            <span class="font-headline font-black text-2xl opacity-40 group-hover:text-black group-hover:opacity-100 transition-colors">#<?= $index + 1 ?></span>
                            <div>
                                <h4 class="font-bold text-lg leading-tight uppercase"><?= htmlspecialchars($cus['full_name']) ?></h4>
                                <span class="text-xs font-bold tracking-widest opacity-60"><?= number_format($cus['visit_count']) ?> ZİYARET</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-black text-xl text-black">₺<?= number_format($cus['total_spent'], 0, '', '.') ?></span>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    // Verileri PHP'den JS'e alıyoruz
    const labels = <?= json_encode($chartLabels) ?>;
    const counts = <?= json_encode($chartCounts) ?>;
    const revenues = <?= json_encode($chartRevenues) ?>;
    
    // Chart konfigürasyonu
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Tamamlanan Randevular (Sağ Eksen)',
                    data: counts,
                    backgroundColor: '#000000', // Siyah Barlar
                    borderColor: '#000000',
                    yAxisID: 'y1',
                    order: 2
                },
                {
                    label: 'Gelir (TL - Sol Eksen)',
                    data: revenues,
                    type: 'line',
                    borderColor: '#94f990', // İkincil renk
                    backgroundColor: '#94f990',
                    borderWidth: 4,
                    tension: 0.3,
                    pointBackgroundColor: '#000',
                    pointBorderColor: '#94f990',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    yAxisID: 'y',
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                tooltip: {
                    usePointStyle: true,
                    titleFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 13, weight: 'normal' }
                },
                legend: {
                    labels: {
                        font: { family: 'Plus Jakarta Sans', size: 13, weight: 'bold' }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', weight: 'bold' } }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Gelir (TL)', font: { weight: 'bold'} },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Randevu Sayısı', font: { weight: 'bold'} },
                    grid: { drawOnChartArea: false } // Sadece soldaki gridi çizdir
                }
            }
        }
    });
});
</script>
