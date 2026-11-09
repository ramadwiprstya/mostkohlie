/**
 * KOPI KOHLIE STOCK & SUPPLY — 17 PAGE IMPLEMENTATIONS & MODALS
 */

// ==========================================
// HALAMAN 1 — LOGIN
// ==========================================
function renderLoginPage() {
  return `
    <div class="login-screen">
      <!-- Left Column: Warm Cafe Atmosphere with Blue Overlay -->
      <div class="login-hero" style="background-image: url('images/login-hero.jpg');">
        <div class="login-hero-content">
          <div class="brand-wrapper" style="margin-bottom: 24px;">
            <img src="images/logo.png" alt="Kopi Kohlie Logo" style="width: 48px; height: 48px; border-radius: var(--radius-md); object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: var(--shadow-md); flex-shrink: 0;">
            <div class="brand-text">
              <h1 style="font-size: 20px; color: #FFF;">KOPI KOHLIE</h1>
              <span style="color: #E8E1D6;">Stock & Supply</span>
            </div>
          </div>
          <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #FFF; margin-bottom: 16px;">
            Operasional Kopi Kohlie
          </span>
        </div>

        <div class="login-hero-content">
          <p class="login-quote">“Bahan yang terjaga, untuk setiap cangkir yang istimewa.”</p>
          <p style="font-size: 13px; color: #E8E1D6; max-width: 420px;">
            Sistem terintegrasi rekap operasional kasir, pemakaian bahan baku, monitoring stok waktu-nyata, dan pengadaan coffee shop.
          </p>
        </div>
      </div>

      <!-- Right Column: Login Form -->
      <div class="login-form-panel">
        <div style="max-width: 400px; width: 100%; margin: 0 auto;">
          <div style="margin-bottom: 28px;">
            <span class="badge badge-blue" style="margin-bottom: 10px;">Monitoring Persediaan</span>
            <h2 style="font-family: var(--font-serif); font-size: 32px; font-weight: 700; color: var(--primary-text); margin-bottom: 6px;">
              Welcome back.
            </h2>
            <p style="font-size: 13.5px; color: var(--secondary-text);">
              Silakan masuk ke akun operasional Anda untuk mengelola inventaris atau rekap transaksi.
            </p>
          </div>

          <form onsubmit="event.preventDefault(); navigateTo(state.currentRole === 'owner' ? 'dashboard_owner' : 'dashboard_barista'); showToast('Berhasil masuk ke sistem.');">
            <div class="form-group">
              <label class="form-label">Email Operasional <span class="required">*</span></label>
              <div class="search-input-wrapper">
                <i data-lucide="mail" class="search-icon" style="width: 16px; height: 16px;"></i>
                <input type="email" class="input-text" value="andra.owner@kopikohlie.com" id="login-email" required>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Kata Sandi <span class="required">*</span></label>
              <div class="search-input-wrapper">
                <i data-lucide="lock" class="search-icon" style="width: 16px; height: 16px;"></i>
                <input type="password" class="input-text" value="••••••••••••" id="login-pass" required style="padding-right: 40px;">
                <button type="button" onclick="const p = document.getElementById('login-pass'); p.type = p.type === 'password' ? 'text' : 'password';" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: var(--secondary-text);">
                  <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                </button>
              </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; font-size: 13px;">
              <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--secondary-text);">
                <input type="checkbox" checked style="accent-color: var(--primary-blue);">
                <span>Ingat saya</span>
              </label>
              <a href="javascript:void(0)" onclick="showToast('Tautan reset sandi telah dikirim ke email administrator.', 'info');" style="color: var(--primary-blue); font-weight: 600;">Lupa password?</a>
            </div>

            <div style="margin-bottom: 20px;">
              <label class="form-label">Pilih Simulasi Role Masuk</label>
              <div class="grid-2" style="gap: 10px;">
                <button type="button" class="btn ${state.currentRole === 'owner' ? 'btn-primary' : 'btn-secondary'}" onclick="state.currentRole = 'owner'; document.getElementById('login-email').value = 'andra.owner@kopikohlie.com'; renderApp();">
                  <i data-lucide="crown" style="width: 15px; height: 15px;"></i>
                  <span>Role: Owner</span>
                </button>
                <button type="button" class="btn ${state.currentRole === 'barista' ? 'btn-primary' : 'btn-secondary'}" onclick="state.currentRole = 'barista'; document.getElementById('login-email').value = 'dimas.barista@kopikohlie.com'; renderApp();">
                  <i data-lucide="coffee" style="width: 15px; height: 15px;"></i>
                  <span>Role: Barista</span>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
              <span>Masuk ke Dashboard</span>
              <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
            </button>
          </form>

          <div style="margin-top: 36px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center; font-size: 12px; color: var(--secondary-text);">
            &copy; 2026 Kopi Kohlie Stock & Supply &bull; Hak Cipta Dilindungi
          </div>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 2 — DASHBOARD OWNER
// ==========================================
function renderDashboardOwner() {
  return `
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
      <span>Beranda</span>
      <span class="separator">/</span>
      <span class="current">Dashboard Owner</span>
    </nav>

    <!-- Page Heading -->
    <div class="page-header">
      <div class="page-title-group">
        <h2>Good morning, Andra.</h2>
        <p>Pemantauan operasional persediaan, estimasi bahan terpakai, dan persetujuan pengadaan terkini.</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="navigateTo('riwayat_rekap')">
          <i data-lucide="list" style="width: 16px; height: 16px;"></i>
          <span>Lihat Rekap</span>
        </button>
        <button class="btn btn-primary" onclick="navigateTo('request_pengadaan')">
          <i data-lucide="check-square" style="width: 16px; height: 16px;"></i>
          <span>Review Pengadaan (3)</span>
        </button>
      </div>
    </div>

    <!-- 4 Statistics Cards -->
    <div class="grid-4" style="margin-bottom: 24px;">
      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Rekap Pesanan Hari Ini</span>
          <div class="stat-icon" style="background: rgba(48, 87, 133, 0.1); color: var(--primary-blue);">
            <i data-lucide="receipt" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value">87 <span style="font-size: 16px; font-weight: normal; color: var(--secondary-text);">order</span></div>
        <div class="stat-trend" style="color: var(--success-green);">
          <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i>
          <span>+14.2% dari shift kemarin</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Pemakaian Bahan</span>
          <div class="stat-icon" style="background: var(--success-bg); color: var(--success-green);">
            <i data-lucide="droplet" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value">12,4 L <span style="font-size: 18px; color: var(--secondary-text);">/ 3,2 Kg</span></div>
        <div class="stat-trend" style="color: var(--secondary-text);">
          <span>Tercatat dari 3 rekap kasir</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Stok Menipis</span>
          <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning-orange);">
            <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value" style="color: var(--warning-orange);">8 <span style="font-size: 16px; font-weight: normal; color: var(--secondary-text);">bahan</span></div>
        <div class="stat-trend" style="color: var(--danger-red);">
          <i data-lucide="alert-circle" style="width: 14px; height: 14px;"></i>
          <span>3 bahan sudah kosong</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Request Pengadaan</span>
          <div class="stat-icon" style="background: var(--danger-bg); color: var(--danger-red);">
            <i data-lucide="file-check-2" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value" style="color: var(--danger-red);">3 <span style="font-size: 16px; font-weight: normal; color: var(--secondary-text);">menunggu</span></div>
        <div class="stat-trend" style="color: var(--primary-blue);">
          <span>Perlu persetujuan Anda</span>
        </div>
      </div>
    </div>

    <!-- Middle Section: Rekap Terbaru & Card Rekomendasi Pengadaan -->
    <div class="grid-2" style="margin-bottom: 24px;">
      <!-- Panel Rekap Terbaru -->
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Rekap Transaksi Terbaru</h3>
            <p class="card-subtitle">Data transaksi kasir yang baru divalidasi oleh Barista</p>
          </div>
          <button class="btn btn-ghost btn-sm" onclick="navigateTo('riwayat_rekap')">Lihat Semua &rarr;</button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
          ${state.recaps.slice(0, 3).map(r => `
            <div style="padding: 12px 14px; background: #FAF8F5; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between;">
              <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: var(--radius-md); background: rgba(48, 87, 133, 0.1); color: var(--primary-blue); display: flex; align-items: center; justify-content: center;">
                  <i data-lucide="receipt" style="width: 18px; height: 18px;"></i>
                </div>
                <div>
                  <div style="font-weight: 700; font-size: 13.5px; color: var(--primary-text);">${r.id}</div>
                  <div style="font-size: 11.5px; color: var(--secondary-text);">${r.cashierRef} &bull; Jam ${r.time} (${r.shift})</div>
                </div>
              </div>
              <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge badge-safe"><span class="badge-dot"></span>${r.status}</span>
                <button class="btn btn-secondary btn-sm" onclick="navigateTo('detail_rekap')">Detail</button>
              </div>
            </div>
          `).join('')}
        </div>
      </div>

      <!-- Card Rekomendasi Pengadaan (System Flow: Stok Menipis -> System Alert -> Request) -->
      <div class="card" style="border-left: 4px solid var(--warning-orange);">
        <div class="card-header">
          <div>
            <span class="badge badge-warning" style="margin-bottom: 6px;">Rekomendasi Cerdas Sistem</span>
            <h3 class="card-title">Kebutuhan Pengadaan Mendesak</h3>
            <p class="card-subtitle">3 bahan baku diprediksi habis sebelum akhir pekan ini</p>
          </div>
          <button class="btn btn-primary btn-sm" onclick="navigateTo('request_pengadaan')">Review Pengadaan</button>
        </div>

        <!-- Visual Flow Diagram -->
        <div style="background: var(--warning-bg); border-radius: var(--radius-md); padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 600; color: #694112;">
          <div style="display: flex; align-items: center; gap: 6px;">
            <i data-lucide="trending-down" style="width: 16px; height: 16px; color: var(--danger-red);"></i>
            <span>Stok Menipis</span>
          </div>
          <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--warning-orange);"></i>
          <div style="display: flex; align-items: center; gap: 6px;">
            <i data-lucide="bell-ring" style="width: 16px; height: 16px; color: var(--warning-orange);"></i>
            <span>System Alert</span>
          </div>
          <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--warning-orange);"></i>
          <div style="display: flex; align-items: center; gap: 6px;">
            <i data-lucide="file-check" style="width: 16px; height: 16px; color: var(--primary-blue);"></i>
            <span>Request Diajukan</span>
          </div>
        </div>

        <!-- 3 Urgent Items -->
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #FAF8F5; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 12.5px;">
            <div>
              <strong>Susu UHT Plain (Diamond)</strong>
              <div style="font-size: 11px; color: var(--secondary-text);">Sisa 2.400 ml &bull; Batas aman 3.000 ml</div>
            </div>
            <span class="badge badge-warning">Menipis</span>
          </div>

          <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #FAF8F5; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 12.5px;">
            <div>
              <strong>Sirup Butterscotch Monin</strong>
              <div style="font-size: 11px; color: var(--secondary-text);">Sisa 180 ml &bull; Batas aman 500 ml</div>
            </div>
            <span class="badge badge-warning">Menipis</span>
          </div>

          <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #FAF8F5; border-radius: var(--radius-sm); border: 1px solid var(--border-color); font-size: 12.5px;">
            <div>
              <strong>Telur Ayam Omega (Egg Yolk)</strong>
              <div style="font-size: 11px; color: var(--secondary-text);">Sisa 0 butir &bull; Batas aman 20 butir</div>
            </div>
            <span class="badge badge-danger">Habis</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section: Pemakaian Bahan Hari Ini & Kondisi Persediaan -->
    <div class="grid-2">
      <!-- Panel Pemakaian Bahan Hari Ini -->
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Pemakaian Bahan Hari Ini</h3>
            <p class="card-subtitle">Kalkulasi otomatis dari master resep atas seluruh pesanan</p>
          </div>
          <span class="badge badge-neutral">3 Shift Operasional</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Susu UHT Plain</span>
              <span style="color: var(--secondary-text); font-weight: 500;"><strong>7.200 ml</strong> / Kuota Shift 10.000 ml (72%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill warning" style="width: 72%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Biji Kopi House Blend</span>
              <span style="color: var(--secondary-text); font-weight: 500;"><strong>1.850 g</strong> / Kuota Shift 4.000 g (46%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill safe" style="width: 46%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Sirup Butterscotch</span>
              <span style="color: var(--secondary-text); font-weight: 500;"><strong>420 ml</strong> / Kuota Shift 500 ml (84%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill danger" style="width: 84%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Matcha Uji Premium</span>
              <span style="color: var(--secondary-text); font-weight: 500;"><strong>240 g</strong> / Kuota Shift 800 g (30%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill safe" style="width: 30%;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Kondisi Persediaan -->
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Kondisi Kapasitas Persediaan</h3>
            <p class="card-subtitle">Status stok gudang saat ini dibanding batas maksimum</p>
          </div>
          <button class="btn btn-secondary btn-sm" onclick="navigateTo('monitoring_stok')">Kelola Stok</button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Biji Kopi House Blend (4.800 g / 10.000 g)</span>
              <span class="badge badge-safe"><span class="badge-dot"></span>Aman (48%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill safe" style="width: 48%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Susu UHT Plain (2.400 ml / 10.000 ml)</span>
              <span class="badge badge-warning"><span class="badge-dot"></span>Menipis (24%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill warning" style="width: 24%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Sirup Butterscotch (180 ml / 2.000 ml)</span>
              <span class="badge badge-warning"><span class="badge-dot"></span>Menipis (9%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill danger" style="width: 9%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Telur Ayam Omega (0 butir / 100 butir)</span>
              <span class="badge badge-danger"><span class="badge-dot"></span>Habis (0%)</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill danger" style="width: 0%;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 3 — DASHBOARD BARISTA
// ==========================================
function renderDashboardBarista() {
  return `
    <nav class="breadcrumb-nav">
      <span>Beranda</span>
      <span class="separator">/</span>
      <span class="current">Dashboard Barista</span>
    </nav>

    <!-- Header Operasional Barista -->
    <div class="page-header">
      <div class="page-title-group">
        <h2>Selamat datang, Dimas.</h2>
        <p>Shift Pagi Aktif &bull; Segera rekap struk transaksi kasir dan pantau ketersediaan bahan station.</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-primary btn-lg" onclick="navigateTo('input_rekap')" style="box-shadow: 0 4px 16px rgba(48, 87, 133, 0.35);">
          <i data-lucide="clipboard-pen" style="width: 18px; height: 18px;"></i>
          <span>+ Input Rekap Pesanan</span>
        </button>
      </div>
    </div>

    <!-- 4 Statistics Cards Barista -->
    <div class="grid-4" style="margin-bottom: 24px;">
      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Rekap Hari Ini</span>
          <div class="stat-icon" style="background: rgba(48, 87, 133, 0.1); color: var(--primary-blue);">
            <i data-lucide="clipboard-list" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value">2 <span style="font-size: 16px; font-weight: normal; color: var(--secondary-text);">batch</span></div>
        <div class="stat-trend" style="color: var(--secondary-text);">87 pesanan divalidasi</div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Stok Menipis di Bar</span>
          <div class="stat-icon" style="background: var(--warning-bg); color: var(--warning-orange);">
            <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value" style="color: var(--warning-orange);">3 <span style="font-size: 16px; font-weight: normal; color: var(--secondary-text);">bahan</span></div>
        <div class="stat-trend" style="color: var(--danger-red);">Susu, Krimer, Telur</div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Request Saya</span>
          <div class="stat-icon" style="background: var(--success-bg); color: var(--success-green);">
            <i data-lucide="shopping-cart" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value">3 <span style="font-size: 16px; font-weight: normal; color: var(--secondary-text);">diajukan</span></div>
        <div class="stat-trend" style="color: var(--warning-orange);">Menunggu Owner review</div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <span class="stat-label">Penerimaan Hari Ini</span>
          <div class="stat-icon" style="background: rgba(57, 113, 90, 0.1); color: var(--success-green);">
            <i data-lucide="package-check" style="width: 18px; height: 18px;"></i>
          </div>
        </div>
        <div class="stat-value">1 <span style="font-size: 16px; font-weight: normal; color: var(--secondary-text);">faktur</span></div>
        <div class="stat-trend" style="color: var(--success-green);">Gula aren 2.000 ml</div>
      </div>
    </div>

    <!-- Card Perhatian Stok Kritis (Susu UHT) -->
    <div class="card" style="margin-bottom: 24px; border-left: 5px solid var(--danger-red); background: #FFFDFB;">
      <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: gap: 16px;">
        <div style="display: flex; align-items: flex-start; gap: 16px;">
          <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--danger-bg); color: var(--danger-red); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="alert-octagon" style="width: 24px; height: 24px;"></i>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
              <h3 style="font-family: var(--font-serif); font-size: 18px; font-weight: 700; color: var(--primary-text);">Perhatian Stok: Susu UHT Plain</h3>
              <span class="badge badge-warning">Menipis</span>
              <span class="badge badge-expiry-warning">Kedaluwarsa 3 Hari Lagi</span>
            </div>
            <p style="font-size: 13.5px; color: var(--secondary-text);">
              Stok tersisa <strong>2.400 ml</strong> (Batas minimum: <strong>3.000 ml</strong>). Diperkirakan hanya cukup untuk <strong>16 porsi latte</strong> lagi.
            </p>
          </div>
        </div>
        <div>
          <button class="btn btn-warning" onclick="navigateTo('request_pengadaan'); showToast('Membuka form request pengadaan susu.', 'info');">
            <i data-lucide="file-plus" style="width: 16px; height: 16px;"></i>
            <span>Buat Request Pengadaan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Akses Cepat Operasional (6 Quick Actions) -->
    <div style="margin-bottom: 24px;">
      <h3 style="font-family: var(--font-serif); font-size: 17px; font-weight: 700; color: var(--primary-text); margin-bottom: 12px;">Akses Cepat Operasional</h3>
      <div class="grid-3" style="gap: 14px;">
        <div class="quick-action-card" onclick="navigateTo('input_rekap')">
          <div class="quick-action-icon" style="background: rgba(48, 87, 133, 0.12); color: var(--primary-blue);">
            <i data-lucide="clipboard-pen" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <strong style="font-size: 13.5px; display: block;">Input Rekap</strong>
            <span style="font-size: 11.5px; color: var(--secondary-text);">Rekap struk transaksi kasir</span>
          </div>
        </div>

        <div class="quick-action-card" onclick="navigateTo('riwayat_rekap')">
          <div class="quick-action-icon" style="background: rgba(76, 116, 153, 0.12); color: var(--secondary-blue);">
            <i data-lucide="receipt" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <strong style="font-size: 13.5px; display: block;">Riwayat Rekap</strong>
            <span style="font-size: 11.5px; color: var(--secondary-text);">Lihat batch rekap tersimpan</span>
          </div>
        </div>

        <div class="quick-action-card" onclick="navigateTo('monitoring_stok')">
          <div class="quick-action-icon" style="background: rgba(57, 113, 90, 0.12); color: var(--success-green);">
            <i data-lucide="activity" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <strong style="font-size: 13.5px; display: block;">Monitoring Stok</strong>
            <span style="font-size: 11.5px; color: var(--secondary-text);">Cek fisik bahan di chiller & bar</span>
          </div>
        </div>

        <div class="quick-action-card" onclick="navigateTo('penerimaan_bahan')">
          <div class="quick-action-icon" style="background: rgba(161, 106, 40, 0.12); color: var(--warning-orange);">
            <i data-lucide="package-plus" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <strong style="font-size: 13.5px; display: block;">Catat Penerimaan</strong>
            <span style="font-size: 11.5px; color: var(--secondary-text);">Terima bahan dari supplier</span>
          </div>
        </div>

        <div class="quick-action-card" onclick="navigateTo('request_pengadaan')">
          <div class="quick-action-icon" style="background: rgba(166, 80, 67, 0.12); color: var(--danger-red);">
            <i data-lucide="shopping-cart" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <strong style="font-size: 13.5px; display: block;">Request Pengadaan</strong>
            <span style="font-size: 11.5px; color: var(--secondary-text);">Ajukan stok menipis ke Owner</span>
          </div>
        </div>

        <div class="quick-action-card" onclick="navigateTo('pembelian_darurat')">
          <div class="quick-action-icon" style="background: rgba(36, 54, 74, 0.1); color: var(--primary-text);">
            <i data-lucide="shield-alert" style="width: 20px; height: 20px;"></i>
          </div>
          <div>
            <strong style="font-size: 13.5px; display: block;">Pembelian Darurat</strong>
            <span style="font-size: 11.5px; color: var(--secondary-text);">Catat beli langsung di supermarket</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel Stok Perlu Perhatian & Pemakaian Shift Ini -->
    <div class="grid-2">
      <!-- Panel Stok Perlu Perhatian -->
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Stok Perlu Perhatian Segera</h3>
            <p class="card-subtitle">Periksa fisik bahan di station sebelum melayani antrean ramai</p>
          </div>
          <span class="badge badge-danger">3 Kritis</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div>
              <div style="font-weight: 700; font-size: 13.5px;">Susu UHT Plain (Diamond)</div>
              <div style="font-size: 11.5px; color: var(--secondary-text);">Stok: <strong>2.400 ml</strong> &bull; Batas Min: 3.000 ml</div>
            </div>
            <span class="badge badge-warning">Menipis</span>
          </div>

          <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div>
              <div style="font-weight: 700; font-size: 13.5px;">Krim Nabati (Non-Dairy)</div>
              <div style="font-size: 11.5px; color: var(--secondary-text);">Stok: <strong>450 g</strong> &bull; Batas Min: 1.000 g</div>
            </div>
            <span class="badge badge-warning">Menipis</span>
          </div>

          <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div>
              <div style="font-weight: 700; font-size: 13.5px;">Telur Ayam Omega (Egg Yolk)</div>
              <div style="font-size: 11.5px; color: var(--secondary-text);">Stok: <strong>0 butir</strong> &bull; Batas Min: 20 butir</div>
            </div>
            <span class="badge badge-danger">Habis</span>
          </div>
        </div>
      </div>

      <!-- Panel Pemakaian Shift Ini -->
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Pemakaian Shift Pagi Ini</h3>
            <p class="card-subtitle">Estimasi kuota bahan habis terpakai selama shift berjalan</p>
          </div>
          <span class="badge badge-neutral">07:00 - 15:00 WIB</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Susu (3.200 ml terpakai)</span>
              <span style="color: var(--secondary-text);">Estimasi shift: 64%</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill warning" style="width: 64%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Biji Kopi House Blend (820 g terpakai)</span>
              <span style="color: var(--secondary-text);">Estimasi shift: 41%</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill safe" style="width: 41%;"></div>
            </div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
              <span style="font-weight: 600;">Sirup Butterscotch (180 ml terpakai)</span>
              <span style="color: var(--secondary-text);">Estimasi shift: 72%</span>
            </div>
            <div class="progress-container">
              <div class="progress-fill danger" style="width: 72%;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 4 — INPUT REKAP PESANAN
// ==========================================
function renderInputRekap() {
  const usage = calculateDraftUsage();
  const totalMenuQty = state.currentDraftItems.reduce((acc, cur) => acc + cur.qty, 0);

  return `
    <nav class="breadcrumb-nav">
      <span>Rekap Pesanan</span>
      <span class="separator">/</span>
      <span class="current">Input Rekap Kasir</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Input Rekap Pesanan Kasir</h2>
        <p>Catat transaksi dari struk POS kasir secara berkala untuk menghitung pemakaian bahan baku.</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="showToast('Draft rekap berhasil disimpan lokal.', 'info');">
          <i data-lucide="save" style="width: 16px; height: 16px;"></i>
          <span>Simpan Draft</span>
        </button>
        <button class="btn btn-primary" onclick="openModal('modal-validate-recap-success')">
          <i data-lucide="calculator" style="width: 16px; height: 16px;"></i>
          <span>Validasi & Simpan Rekap</span>
        </button>
      </div>
    </div>

    <!-- Alert Banner: Not POS Disclaimer -->
    <div class="callout-box callout-info">
      <i data-lucide="info" style="width: 20px; height: 20px; color: var(--primary-blue); flex-shrink: 0; margin-top: 2px;"></i>
      <div>
        <strong>Perhatian Operasional Barista:</strong>
        <p style="margin-top: 2px;">
          Sistem ini <strong>bukan sistem kasir/POS</strong>. Masukkan rekap penjualan fisik atau rangkuman kasir per rentang transaksi. Sistem akan membaca master resep, mengkalkulasi pengurangan bahan baku, dan memperbarui buku stok otomatis.
        </p>
      </div>
    </div>

    <div class="grid-2" style="align-items: start; margin-bottom: 24px;">
      <!-- Kolom Kiri: Form Informasi Rekap & Tambah Menu -->
      <div>
        <div class="card" style="margin-bottom: 20px;">
          <h3 class="card-title" style="margin-bottom: 16px;">1. Informasi Rekap Transaksi</h3>
          <div class="grid-2" style="gap: 14px;">
            <div class="form-group">
              <label class="form-label">Tanggal Operasional</label>
              <input type="date" class="input-text" value="2026-10-20">
            </div>

            <div class="form-group">
              <label class="form-label">Shift Kerja</label>
              <select class="input-select">
                <option selected>Pagi (07:00 - 15:00)</option>
                <option>Siang (15:00 - 20:00)</option>
                <option>Malam (20:00 - 23:00)</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Nomor / Referensi Transaksi Kasir <span class="required">*</span></label>
              <input type="text" class="input-text" value="TRX-POS-1042..1078" placeholder="Contoh: TRX-001 s/d TRX-045">
            </div>

            <div class="form-group">
              <label class="form-label">Nama Barista Perekap</label>
              <input type="text" class="input-text" value="Dimas Kurnia" readonly style="background: var(--supporting-sand);">
            </div>
          </div>
        </div>

        <!-- Form Tambah Menu -->
        <div class="card">
          <h3 class="card-title" style="margin-bottom: 16px;">2. Tambah Menu dari Struk Kasir</h3>
          <div style="display: flex; gap: 12px; align-items: flex-end; margin-bottom: 16px;">
            <div style="flex: 2;">
              <label class="form-label">Pilih Menu</label>
              <select class="input-select" id="select-menu-add">
                ${state.menus.map(m => `<option value="${m.id}">${m.name} (${m.category}) - Rp ${m.price.toLocaleString('id-ID')}</option>`).join('')}
              </select>
            </div>
            <div style="flex: 1;">
              <label class="form-label">Jumlah (Cup)</label>
              <input type="number" class="input-text" id="input-qty-add" value="5" min="1">
            </div>
            <button class="btn btn-primary" onclick="addMenuToDraft()" style="height: 40px;">
              <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
              <span>Tambah</span>
            </button>
          </div>

          <!-- Table of Added Menu -->
          <div class="table-responsive">
            <table class="table-custom">
              <thead>
                <tr>
                  <th>Nama Menu</th>
                  <th style="text-align: center;">Jumlah</th>
                  <th style="text-align: right;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                ${state.currentDraftItems.map((item, idx) => `
                  <tr>
                    <td><strong>${item.menuName}</strong></td>
                    <td style="text-align: center;">${item.qty} cup</td>
                    <td style="text-align: right;">
                      <button class="btn btn-ghost btn-sm" onclick="removeDraftItem(${idx})" style="color: var(--danger-red);">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                      </button>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>

          <div style="margin-top: 14px; display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid var(--border-color); font-size: 13px;">
            <span>Total Item Menu: <strong>${totalMenuQty} Cup</strong></span>
            <span class="badge badge-warning">Status: Draft Belum Divalidasi</span>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Panel Estimasi Pemakaian Bahan Otomatis -->
      <div>
        <div class="card" style="border-top: 4px solid var(--primary-blue);">
          <div class="card-header">
            <div>
              <span class="badge badge-blue" style="margin-bottom: 6px;">Kalkulasi Master Resep</span>
              <h3 class="card-title">Estimasi Pemakaian Bahan Baku</h3>
              <p class="card-subtitle">Dihitung otomatis per cup menu yang diinputkan</p>
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 14px;">
            ${Object.entries(usage).map(([name, val]) => {
              const isShortage = val.need > val.available;
              return `
                <div style="padding: 12px 14px; background: #FAF8F5; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <strong>${name}</strong>
                    <span style="font-size: 13px; font-weight: 700; color: ${isShortage ? 'var(--danger-red)' : 'var(--primary-text)'};">
                      Kebutuhan: ${val.need.toLocaleString('id-ID')} ${val.unit}
                    </span>
                  </div>
                  <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: var(--secondary-text); margin-bottom: 6px;">
                    <span>Stok Saat Ini: ${val.available.toLocaleString('id-ID')} ${val.unit}</span>
                    <span>Sisa Pasca Rekap: ${(val.available - val.need).toLocaleString('id-ID')} ${val.unit}</span>
                  </div>
                  <div class="progress-container">
                    <div class="progress-fill ${isShortage ? 'danger' : 'safe'}" style="width: ${Math.min(100, (val.need / val.available) * 100)}%;"></div>
                  </div>
                </div>
              `;
            }).join('')}
          </div>

          <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
            <button class="btn btn-primary btn-lg" onclick="openModal('modal-validate-recap-success')" style="width: 100%;">
              <i data-lucide="check-check" style="width: 18px; height: 18px;"></i>
              <span>Simpan & Kurangi Stok Otomatis</span>
            </button>
            <button class="btn btn-secondary btn-sm" onclick="openModal('modal-validate-recap-deficit')" style="color: var(--danger-red);">
              <i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i>
              <span>Simulasi Uji: Kondisi Stok Tidak Mencukupi</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
}

// Rekap In-Memory Handlers
function addMenuToDraft() {
  const select = document.getElementById('select-menu-add');
  const qtyInput = document.getElementById('input-qty-add');
  if (!select || !qtyInput) return;

  const menu = state.menus.find(m => m.id === select.value);
  const qty = parseInt(qtyInput.value) || 1;

  const existing = state.currentDraftItems.find(i => i.menuId === menu.id);
  if (existing) {
    existing.qty += qty;
  } else {
    state.currentDraftItems.push({
      menuId: menu.id,
      menuName: menu.name,
      qty: qty
    });
  }

  showToast(`Berhasil menambahkan ${qty} cup ${menu.name}`);
  renderApp();
}

function removeDraftItem(index) {
  state.currentDraftItems.splice(index, 1);
  renderApp();
}

// ==========================================
// HALAMAN 5 — RIWAYAT REKAP
// ==========================================
function renderRiwayatRekap() {
  const isOwner = state.currentRole === 'owner';

  return `
    <nav class="breadcrumb-nav">
      <span>Rekap Pesanan</span>
      <span class="separator">/</span>
      <span class="current">Riwayat Rekap</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Riwayat Rekap Pesanan Kasir</h2>
        <p>Arsip histori batch input transaksi kasir dan mutasi pemakaian bahan baku.</p>
      </div>
      <div class="page-actions">
        ${!isOwner ? `
          <button class="btn btn-primary" onclick="navigateTo('input_rekap')">
            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
            <span>Input Rekap Baru</span>
          </button>
        ` : ''}
      </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="card" style="margin-bottom: 20px; padding: 16px;">
      <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
        <div class="search-input-wrapper">
          <i data-lucide="search" class="search-icon" style="width: 16px; height: 16px;"></i>
          <input type="text" class="input-text" placeholder="Cari nomor rekap atau referensi kasir...">
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
          <select class="input-select" style="width: 150px;">
            <option>Semua Shift</option>
            <option>Shift Pagi</option>
            <option>Shift Siang</option>
            <option>Shift Malam</option>
          </select>

          <input type="date" class="input-text" value="2026-10-20" style="width: 160px;">

          <button class="btn btn-secondary">
            <i data-lucide="filter" style="width: 15px; height: 15px;"></i>
            <span>Filter</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Table of Recaps -->
    <div class="table-responsive">
      <table class="table-custom">
        <thead>
          <tr>
            <th>Nomor Rekap</th>
            <th>Tanggal & Shift</th>
            <th>Referensi Transaksi</th>
            <th>Total Menu</th>
            <th>Direkap Oleh</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          ${state.recaps.map(r => `
            <tr>
              <td><strong style="color: var(--primary-blue);">${r.id}</strong></td>
              <td>${r.date} &bull; <span class="badge badge-neutral">${r.shift} (${r.time})</span></td>
              <td><code>${r.cashierRef}</code></td>
              <td><strong>${r.itemsCount} Cup</strong></td>
              <td>${r.barista}</td>
              <td><span class="badge badge-safe"><span class="badge-dot"></span>${r.status}</span></td>
              <td style="text-align: right;">
                <button class="btn btn-secondary btn-sm" onclick="navigateTo('detail_rekap')">
                  <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                  <span>Detail</span>
                </button>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="pagination-wrapper">
        <span>Menampilkan 1 - 6 dari 42 rekap tersimpan</span>
        <div class="pagination-pages">
          <button class="page-btn"><i data-lucide="chevron-left" style="width: 14px; height: 14px;"></i></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
          <button class="page-btn"><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></button>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 6 — DETAIL REKAP
// ==========================================
function renderDetailRekap() {
  return `
    <nav class="breadcrumb-nav">
      <a onclick="navigateTo('riwayat_rekap')">Riwayat Rekap</a>
      <span class="separator">/</span>
      <span class="current">Detail Rekap #RKP-20261020-003</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Detail Rekap #RKP-20261020-003</h2>
        <p>Rincian pesanan kasir dan mutasi pengurangan bahan baku otomatis.</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="navigateTo('riwayat_stok')">
          <i data-lucide="history" style="width: 16px; height: 16px;"></i>
          <span>Lihat di Riwayat Stok</span>
        </button>
        <button class="btn btn-secondary" onclick="window.print()">
          <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
          <span>Cetak Bukti</span>
        </button>
      </div>
    </div>

    <div class="grid-2" style="margin-bottom: 24px; align-items: start;">
      <!-- Panel Detail Transaksi -->
      <div class="card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Informasi Batch Transaksi Kasir</h3>
            <p class="card-subtitle">Dicatat dari printer POS kasir</p>
          </div>
          <span class="badge badge-safe"><span class="badge-dot"></span>Selesai Divalidasi</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; margin-bottom: 18px; font-size: 13px;">
          <div>
            <span style="color: var(--secondary-text); display: block; font-size: 11.5px;">Nomor Rekap</span>
            <strong>RKP-20261020-003</strong>
          </div>
          <div>
            <span style="color: var(--secondary-text); display: block; font-size: 11.5px;">Referensi Kasir POS</span>
            <code>TRX-POS-1042..1078 (37 Transaksi)</code>
          </div>
          <div>
            <span style="color: var(--secondary-text); display: block; font-size: 11.5px;">Waktu & Shift</span>
            <span>20 Oktober 2026, 12:15 WIB (Shift Pagi)</span>
          </div>
          <div>
            <span style="color: var(--secondary-text); display: block; font-size: 11.5px;">Barista Perekap</span>
            <strong>Dimas Kurnia</strong>
          </div>
        </div>

        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 10px; border-top: 1px solid var(--border-color); padding-top: 14px;">Daftar Menu Terjual:</h4>
        <div class="table-responsive">
          <table class="table-custom">
            <thead>
              <tr>
                <th>Menu</th>
                <th style="text-align: center;">Jumlah</th>
                <th style="text-align: right;">Total Estimasi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Butterscotch Latte (Signature)</td>
                <td style="text-align: center;">18 Cup</td>
                <td style="text-align: right;">Rp 648.000</td>
              </tr>
              <tr>
                <td>Kohlie Latte (Signature)</td>
                <td style="text-align: center;">20 Cup</td>
                <td style="text-align: right;">Rp 640.000</td>
              </tr>
              <tr>
                <td>Americano</td>
                <td style="text-align: center;">10 Cup</td>
                <td style="text-align: right;">Rp 260.000</td>
              </tr>
            </tbody>
            <tfoot>
              <tr style="background: #FAF8F5; font-weight: 700;">
                <td>Total Keseluruhan</td>
                <td style="text-align: center;">48 Cup</td>
                <td style="text-align: right;">Rp 1.548.000</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Panel Pemakaian Bahan & Formula Resep -->
      <div class="card" style="border-top: 4px solid var(--success-green);">
        <div class="card-header">
          <div>
            <h3 class="card-title">Pemakaian Bahan Berdasarkan Resep</h3>
            <p class="card-subtitle">Formula otomatis pemotongan inventaris</p>
          </div>
          <span class="badge badge-safe">Stok Diperbarui</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px;">
          <div style="padding: 12px 14px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <strong>Susu UHT Plain</strong>
              <span class="badge badge-danger">-7.200 ml</span>
            </div>
            <div style="font-size: 12px; color: var(--secondary-text);">
              Formula: (18 cup × 150ml) + (20 cup × 140ml) = 5.500ml + Buffer latte art 1.700ml
            </div>
          </div>

          <div style="padding: 12px 14px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <strong>Biji Kopi House Blend</strong>
              <span class="badge badge-danger">-864 g</span>
            </div>
            <div style="font-size: 12px; color: var(--secondary-text);">
              Formula: 48 cup espresso × 18 g/dose = 864 gram
            </div>
          </div>

          <div style="padding: 12px 14px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <strong>Sirup Butterscotch Monin</strong>
              <span class="badge badge-danger">-350 ml</span>
            </div>
            <div style="font-size: 12px; color: var(--secondary-text);">
              Formula: 18 cup Butterscotch Latte × 25 ml/cup
            </div>
          </div>

          <div style="padding: 12px 14px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <strong>Gula Aren Cair Organik</strong>
              <span class="badge badge-danger">-580 ml</span>
            </div>
            <div style="font-size: 12px; color: var(--secondary-text);">
              Formula: (18 cup × 10ml) + (20 cup × 20ml)
            </div>
          </div>
        </div>

        <div style="margin-top: 18px; padding: 12px; background: var(--success-bg); border-radius: var(--radius-md); border: 1px solid rgba(57, 113, 90, 0.2); font-size: 12.5px; color: #1F4535; display: flex; align-items: center; gap: 10px;">
          <i data-lucide="check-circle" style="width: 18px; height: 18px; color: var(--success-green); flex-shrink: 0;"></i>
          <span>Pemakaian ini telah tervalidasi dan tersimpan permanen dalam Riwayat Mutasi Stok.</span>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 7 — DAFTAR MENU
// ==========================================
function renderDaftarMenu() {
  const isOwner = state.currentRole === 'owner';

  return `
    <nav class="breadcrumb-nav">
      <span>Menu & Resep</span>
      <span class="separator">/</span>
      <span class="current">Daftar Menu</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Daftar Menu Minuman</h2>
        <p>Koleksi menu coffee shop Kopi Kohlie yang terhubung dengan master komposisi resep bahan.</p>
      </div>
      <div class="page-actions">
        ${isOwner ? `
          <button class="btn btn-primary" onclick="openModal('modal-add-menu')">
            <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
            <span>+ Tambah Menu Baru</span>
          </button>
        ` : `
          <span class="badge badge-neutral" title="Hanya Owner yang dapat menambah atau mengubah master menu">
            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
            Akses Barista: Hanya Lihat (Read-Only)
          </span>
        `}
      </div>
    </div>

    <!-- Filter & Search Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
      <div class="nav-tabs" style="margin-bottom: 0;">
        <button class="tab-btn active">Semua Menu (6)</button>
        <button class="tab-btn">Signature (2)</button>
        <button class="tab-btn">Coffee (2)</button>
        <button class="tab-btn">Non-Coffee (2)</button>
      </div>

      <div class="search-input-wrapper">
        <i data-lucide="search" class="search-icon" style="width: 16px; height: 16px;"></i>
        <input type="text" class="input-text" placeholder="Cari nama menu...">
      </div>
    </div>

    <!-- Menu Cards Grid -->
    <div class="grid-3" style="gap: 22px;">
      ${state.menus.map(menu => `
        <div class="menu-card" onclick="navigateTo('detail_resep')">
          <div style="position: relative;">
            <img src="${menu.image}" alt="${menu.name}" class="menu-card-img">
            ${menu.isSignature ? `
              <span class="badge" style="position: absolute; top: 12px; left: 12px; background: #382718; color: #F5D38E; border: 1px solid #F5D38E; font-weight: 700;">
                ★ Signature Kohlie
              </span>
            ` : ''}
            <span class="badge badge-neutral" style="position: absolute; top: 12px; right: 12px; background: rgba(255, 255, 255, 0.9);">
              ${menu.category}
            </span>
          </div>

          <div class="menu-card-body">
            <h3 class="menu-card-title">${menu.name}</h3>
            <p style="font-size: 12px; color: var(--secondary-text); margin-bottom: 12px; line-height: 1.4; flex: 1;">
              ${menu.description}
            </p>

            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid var(--border-color); font-size: 13px;">
              <div>
                <span style="font-size: 11px; color: var(--secondary-text); display: block;">Harga Jual</span>
                <strong style="font-size: 15px; color: var(--primary-text);">Rp ${menu.price.toLocaleString('id-ID')}</strong>
              </div>
              <div style="text-align: right;">
                <span class="badge badge-blue">${menu.ingredientCount} Bahan Baku</span>
              </div>
            </div>
          </div>
        </div>
      `).join('')}
    </div>
  `;
}

// ==========================================
// HALAMAN 8 — DETAIL RESEP
// ==========================================
function renderDetailResep() {
  const isOwner = state.currentRole === 'owner';
  const menu = state.menus[0]; // Butterscotch Latte

  return `
    <nav class="breadcrumb-nav">
      <a onclick="navigateTo('daftar_menu')">Daftar Menu</a>
      <span class="separator">/</span>
      <span class="current">Resep: ${menu.name}</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Komposisi Resep: ${menu.name}</h2>
        <p>Master takaran per serving untuk perhitungan otomatis pemakaian bahan.</p>
      </div>
      <div class="page-actions">
        ${isOwner ? `
          <button class="btn btn-secondary" onclick="showToast('Mode ubah resep diaktifkan.', 'info');">
            <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
            <span>Edit Resep</span>
          </button>
          <button class="btn btn-primary" onclick="showToast('Buka modal tambah bahan baku resep.', 'info');">
            <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
            <span>Tambah Bahan</span>
          </button>
        ` : `
          <span class="badge badge-neutral">
            <i data-lucide="lock" style="width: 13px; height: 13px;"></i>
            Barista Akses Baca (Read-Only)
          </span>
        `}
      </div>
    </div>

    <!-- Top Card: Visual & Capacity Calculation -->
    <div class="card" style="margin-bottom: 24px; padding: 24px;">
      <div style="display: flex; gap: 24px; align-items: center; flex-wrap: wrap;">
        <img src="${menu.image}" alt="${menu.name}" style="width: 140px; height: 140px; border-radius: var(--radius-lg); object-fit: cover; border: 1px solid var(--border-color);">
        <div style="flex: 1; min-width: 240px;">
          <div style="display: flex; gap: 8px; margin-bottom: 6px;">
            <span class="badge badge-safe">Resep Aktif</span>
            <span class="badge badge-blue">${menu.category}</span>
          </div>
          <h3 style="font-family: var(--font-serif); font-size: 24px; font-weight: 700; color: var(--primary-text); margin-bottom: 4px;">
            ${menu.name}
          </h3>
          <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 12px;">
            ${menu.description}
          </p>
          <div style="font-size: 14px; font-weight: 700; color: var(--primary-blue);">
            Harga Jual: Rp ${menu.price.toLocaleString('id-ID')}
          </div>
        </div>

        <!-- KPI: Estimated Remaining Cups Capacity -->
        <div style="background: var(--warning-bg); border: 1px solid rgba(161, 106, 40, 0.25); border-radius: var(--radius-lg); padding: 18px 22px; text-align: center; min-width: 200px;">
          <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--warning-orange);">Estimasi Kapasitas</span>
          <div style="font-family: var(--font-serif); font-size: 34px; font-weight: 700; color: var(--primary-text); line-height: 1.1; margin: 4px 0;">
            7 Cup
          </div>
          <div style="font-size: 11.5px; color: var(--danger-red); font-weight: 600;">
            Pembatas: Sirup Butterscotch (180 ml)
          </div>
        </div>
      </div>
    </div>

    <!-- Table of Ingredients Composition -->
    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="card-title">Tabel Takaran Bahan Per Serving (1 Cup)</h3>
          <p class="card-subtitle">Takaran baku yang dipakai sistem saat barista merekap penjualan</p>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Bahan Baku</th>
              <th>Takaran Per Cup</th>
              <th>Stok Tersedia di Gudang</th>
              <th>Status Stok</th>
              <th>Dapat Dibuat</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Susu UHT Plain</strong></td>
              <td>150 ml</td>
              <td>2.400 ml</td>
              <td><span class="badge badge-warning">Menipis</span></td>
              <td>16 Cup</td>
            </tr>
            <tr>
              <td><strong>Sirup Butterscotch Monin</strong></td>
              <td>25 ml</td>
              <td>180 ml</td>
              <td><span class="badge badge-danger">Kritis</span></td>
              <td><strong>7 Cup (Bottleneck)</strong></td>
            </tr>
            <tr>
              <td><strong>Biji Kopi House Blend</strong></td>
              <td>18 gram</td>
              <td>4.800 gram</td>
              <td><span class="badge badge-safe">Aman</span></td>
              <td>266 Cup</td>
            </tr>
            <tr>
              <td><strong>Gula Aren Cair Organik</strong></td>
              <td>10 ml</td>
              <td>3.200 ml</td>
              <td><span class="badge badge-safe">Aman</span></td>
              <td>320 Cup</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 9 — MONITORING PERSEDIAAN
// ==========================================
function renderMonitoringStok() {
  const isOwner = state.currentRole === 'owner';

  return `
    <nav class="breadcrumb-nav">
      <span>Persediaan</span>
      <span class="separator">/</span>
      <span class="current">Monitoring Stok</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Monitoring Persediaan Bahan</h2>
        <p>Status stok aktual, ambang batas minimum, tanggal kedaluwarsa, dan reminder pengadaan.</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="navigateTo('penerimaan_bahan')">
          <i data-lucide="package-plus" style="width: 15px; height: 15px;"></i>
          <span>Tambah Penerimaan</span>
        </button>
        ${isOwner ? `
          <button class="btn btn-primary" onclick="openModal('modal-set-min-stock')">
            <i data-lucide="sliders" style="width: 15px; height: 15px;"></i>
            <span>Atur Batas Aman</span>
          </button>
        ` : ''}
      </div>
    </div>

    <!-- Summary Badges Pills (Exact 45, 34, 8, 3) -->
    <div class="grid-4" style="margin-bottom: 24px;">
      <div class="stat-card" style="padding: 16px 20px;">
        <span class="stat-label">Total Bahan Baku</span>
        <div class="stat-value" style="font-size: 26px;">45 <span style="font-size: 13px; color: var(--secondary-text);">SKU</span></div>
      </div>
      <div class="stat-card" style="padding: 16px 20px; border-left: 4px solid var(--success-green);">
        <span class="stat-label">Stok Aman</span>
        <div class="stat-value" style="font-size: 26px; color: var(--success-green);">34 <span style="font-size: 13px; color: var(--secondary-text);">SKU</span></div>
      </div>
      <div class="stat-card" style="padding: 16px 20px; border-left: 4px solid var(--warning-orange);">
        <span class="stat-label">Stok Menipis</span>
        <div class="stat-value" style="font-size: 26px; color: var(--warning-orange);">8 <span style="font-size: 13px; color: var(--secondary-text);">SKU</span></div>
      </div>
      <div class="stat-card" style="padding: 16px 20px; border-left: 4px solid var(--danger-red);">
        <span class="stat-label">Stok Habis / Kosong</span>
        <div class="stat-value" style="font-size: 26px; color: var(--danger-red);">3 <span style="font-size: 13px; color: var(--secondary-text);">SKU</span></div>
      </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card" style="margin-bottom: 20px; padding: 16px;">
      <div style="display: flex; gap: 14px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
        <div class="search-input-wrapper">
          <i data-lucide="search" class="search-icon" style="width: 16px; height: 16px;"></i>
          <input type="text" class="input-text" placeholder="Cari nama bahan atau supplier...">
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
          <select class="input-select" style="width: 150px;">
            <option>Semua Kategori</option>
            <option>Dairy (Susu)</option>
            <option>Coffee (Biji Kopi)</option>
            <option>Syrup & Flavour</option>
            <option>Powder</option>
          </select>

          <select class="input-select" style="width: 140px;">
            <option>Semua Status</option>
            <option>Aman</option>
            <option>Menipis</option>
            <option>Habis</option>
          </select>

          <button class="btn btn-secondary">
            <i data-lucide="filter" style="width: 15px; height: 15px;"></i>
            <span>Filter</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Table of Inventory -->
    <div class="table-responsive">
      <table class="table-custom">
        <thead>
          <tr>
            <th>Nama Bahan</th>
            <th>Kategori</th>
            <th>Stok Saat Ini</th>
            <th>Batas Aman</th>
            <th>Status Stok</th>
            <th>Kedaluwarsa</th>
            <th>Reminder</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          ${state.ingredients.map(ing => `
            <tr>
              <td>
                <a onclick="navigateTo('detail_bahan')" style="font-weight: 700; color: var(--primary-text); cursor: pointer;" class="hover-link">
                  ${ing.name}
                </a>
              </td>
              <td><span class="badge badge-neutral">${ing.category}</span></td>
              <td><strong>${ing.currentStock.toLocaleString('id-ID')} ${ing.unit}</strong></td>
              <td>${ing.minStock.toLocaleString('id-ID')} ${ing.unit}</td>
              <td>
                ${ing.status === 'safe' ? '<span class="badge badge-safe"><span class="badge-dot"></span>Aman</span>' : ''}
                ${ing.status === 'warning' ? '<span class="badge badge-warning"><span class="badge-dot"></span>Menipis</span>' : ''}
                ${ing.status === 'danger' ? '<span class="badge badge-danger"><span class="badge-dot"></span>Habis</span>' : ''}
              </td>
              <td>
                <span class="badge ${ing.daysLeft <= 3 ? 'badge-expiry-danger' : (ing.daysLeft <= 14 ? 'badge-expiry-warning' : 'badge-expiry-safe')}">
                  ${ing.expiry} (${ing.daysLeft} hari)
                </span>
              </td>
              <td>
                ${ing.daysLeft <= 3 ? '<span style="font-size: 11px; color: var(--danger-red); font-weight: 600;">Alert Aktif</span>' : '<span style="font-size: 11px; color: var(--secondary-text);">Normal</span>'}
              </td>
              <td style="text-align: right;">
                <button class="btn btn-secondary btn-sm" onclick="navigateTo('detail_bahan')">
                  <span>Detail</span>
                </button>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div class="pagination-wrapper">
        <span>Menampilkan 1 - 11 dari 45 bahan</span>
        <div class="pagination-pages">
          <button class="page-btn"><i data-lucide="chevron-left" style="width: 14px; height: 14px;"></i></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
          <button class="page-btn"><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></button>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 10 — DETAIL BAHAN (SUSU UHT)
// ==========================================
function renderDetailBahan() {
  const ing = state.ingredients[0]; // Susu UHT Plain

  return `
    <nav class="breadcrumb-nav">
      <a onclick="navigateTo('monitoring_stok')">Monitoring Stok</a>
      <span class="separator">/</span>
      <span class="current">Detail Bahan: ${ing.name}</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>${ing.name}</h2>
        <p>Dairy &bull; Master Bahan Baku Utama Minuman Espresso Based</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="navigateTo('penerimaan_bahan')">
          <i data-lucide="package-plus" style="width: 15px; height: 15px;"></i>
          <span>Restock Susu</span>
        </button>
        <button class="btn btn-primary" onclick="navigateTo('request_pengadaan')">
          <i data-lucide="shopping-cart" style="width: 15px; height: 15px;"></i>
          <span>Buat Request Pengadaan</span>
        </button>
      </div>
    </div>

    <!-- Double Warning Banners -->
    <div class="grid-2" style="margin-bottom: 24px;">
      <div class="callout-box callout-danger" style="margin-bottom: 0;">
        <i data-lucide="alert-triangle" style="width: 22px; height: 22px; color: var(--danger-red); flex-shrink: 0;"></i>
        <div>
          <strong>Peringatan: Stok Melewati Batas Aman</strong>
          <p style="margin-top: 2px;">Stok tersisa 2.400 ml, berada di bawah batas minimum 3.000 ml. Segera lakukan pengadaan.</p>
        </div>
      </div>

      <div class="callout-box callout-warning" style="margin-bottom: 0;">
        <i data-lucide="clock" style="width: 22px; height: 22px; color: var(--warning-orange); flex-shrink: 0;"></i>
        <div>
          <strong>Mendekati Masa Kedaluwarsa (3 Hari Lagi)</strong>
          <p style="margin-top: 2px;">Batch penerimaan #SKD-09 akan kedaluwarsa pada 23 Oktober 2026. Utamakan pemakaian (FIFO).</p>
        </div>
      </div>
    </div>

    <!-- Ingredient Specs & Automatic Tracking Flow -->
    <div class="grid-2" style="margin-bottom: 24px; align-items: start;">
      <div class="card">
        <h3 class="card-title" style="margin-bottom: 16px;">Spesifikasi & Master Bahan</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; font-size: 13px;">
          <div>
            <span style="color: var(--secondary-text); font-size: 11.5px; display: block;">Kategori</span>
            <strong>${ing.category}</strong>
          </div>
          <div>
            <span style="color: var(--secondary-text); font-size: 11.5px; display: block;">Stok Aktual (Current Stock)</span>
            <strong style="color: var(--warning-orange); font-size: 16px;">${ing.currentStock.toLocaleString('id-ID')} ${ing.unit}</strong>
          </div>
          <div>
            <span style="color: var(--secondary-text); font-size: 11.5px; display: block;">Batas Minimum (Min Stock)</span>
            <strong>${ing.minStock.toLocaleString('id-ID')} ${ing.unit}</strong>
          </div>
          <div>
            <span style="color: var(--secondary-text); font-size: 11.5px; display: block;">Supplier Utama</span>
            <strong>${ing.supplier}</strong>
          </div>
          <div>
            <span style="color: var(--secondary-text); font-size: 11.5px; display: block;">Harga Beli Terakhir</span>
            <strong>Rp ${ing.price.toLocaleString('id-ID')} / Liter</strong>
          </div>
          <div>
            <span style="color: var(--secondary-text); font-size: 11.5px; display: block;">Tanggal Kedaluwarsa Terdekat</span>
            <span class="badge badge-expiry-danger">${ing.expiry}</span>
          </div>
        </div>
      </div>

      <!-- Visual Automatic Tracking Flowchart -->
      <div class="card" style="border-top: 4px solid var(--primary-blue);">
        <h3 class="card-title" style="margin-bottom: 6px;">Alur Pelacakan Otomatis (Automatic Tracking)</h3>
        <p class="card-subtitle" style="margin-bottom: 16px;">Bagaimana sistem memperbarui stok tanpa kasir terhubung langsung</p>

        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="padding: 10px 14px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">1</div>
            <div>
              <strong style="font-size: 13px;">Rekap Transaksi Kasir</strong>
              <div style="font-size: 11.5px; color: var(--secondary-text);">Barista menginput total porsi minuman dari struk POS kasir</div>
            </div>
          </div>

          <div style="text-align: center; color: var(--secondary-text);">
            <i data-lucide="arrow-down" style="width: 16px; height: 16px;"></i>
          </div>

          <div style="padding: 10px 14px; background: #FAF8F5; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--secondary-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">2</div>
            <div>
              <strong style="font-size: 13px;">Kalkulasi Master Resep</strong>
              <div style="font-size: 11.5px; color: var(--secondary-text);">Sistem membaca takaran (150ml/cup) dan mengalikan jumlah order</div>
            </div>
          </div>

          <div style="text-align: center; color: var(--secondary-text);">
            <i data-lucide="arrow-down" style="width: 16px; height: 16px;"></i>
          </div>

          <div style="padding: 10px 14px; background: var(--success-bg); border-radius: var(--radius-md); border: 1px solid rgba(57, 113, 90, 0.3); display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--success-green); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">3</div>
            <div>
              <strong style="font-size: 13px; color: #1F4535;">Inventory Updated & Warning Trigger</strong>
              <div style="font-size: 11.5px; color: #2E634D;">Stok dikurangi, riwayat mutasi dibuat, alert terbit jika < 3.000 ml</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Grafik Perubahan Stok 7 Hari (SVG Chart) -->
    <div class="card" style="margin-bottom: 24px;">
      <div class="card-header">
        <div>
          <h3 class="card-title">Tren Perubahan Stok 7 Hari Terakhir (ml)</h3>
          <p class="card-subtitle">Penurunan harian akibat pemakaian dan lonjakan saat restock</p>
        </div>
        <span class="badge badge-neutral">14 Okt - 20 Okt 2026</span>
      </div>

      <!-- SVG Line Chart Mockup -->
      <div style="width: 100%; height: 180px; position: relative;">
        <svg viewBox="0 0 700 180" style="width: 100%; height: 100%; overflow: visible;">
          <!-- Threshold line 3000 ml -->
          <line x1="40" y1="120" x2="680" y2="120" stroke="var(--danger-red)" stroke-dasharray="4" stroke-width="1.5" />
          <text x="685" y="124" font-size="10" fill="var(--danger-red)" font-weight="bold">Min: 3.000 ml</text>

          <!-- Grid Lines -->
          <line x1="40" y1="30" x2="680" y2="30" stroke="#E8E1D6" stroke-width="1" />
          <line x1="40" y1="75" x2="680" y2="75" stroke="#E8E1D6" stroke-width="1" />

          <!-- Chart Area Fill -->
          <polygon points="50,60 150,90 250,50 350,110 450,40 550,100 650,130 650,160 50,160" fill="rgba(48, 87, 133, 0.08)" />

          <!-- Chart Line -->
          <polyline points="50,60 150,90 250,50 350,110 450,40 550,100 650,130" fill="none" stroke="var(--primary-blue)" stroke-width="3" />

          <!-- Dots -->
          <circle cx="50" cy="60" r="4" fill="var(--primary-blue)" />
          <circle cx="150" cy="90" r="4" fill="var(--primary-blue)" />
          <circle cx="250" cy="50" r="4" fill="var(--success-green)" />
          <circle cx="350" cy="110" r="4" fill="var(--primary-blue)" />
          <circle cx="450" cy="40" r="4" fill="var(--success-green)" />
          <circle cx="550" cy="100" r="4" fill="var(--primary-blue)" />
          <circle cx="650" cy="130" r="5" fill="var(--danger-red)" />

          <!-- Labels -->
          <text x="45" y="175" font-size="11" fill="var(--secondary-text)">14 Okt</text>
          <text x="145" y="175" font-size="11" fill="var(--secondary-text)">15 Okt</text>
          <text x="245" y="175" font-size="11" fill="var(--secondary-text)">16 Okt (Restock)</text>
          <text x="345" y="175" font-size="11" fill="var(--secondary-text)">17 Okt</text>
          <text x="445" y="175" font-size="11" fill="var(--secondary-text)">18 Okt (Restock)</text>
          <text x="545" y="175" font-size="11" fill="var(--secondary-text)">19 Okt</text>
          <text x="635" y="175" font-size="11" fill="var(--danger-red)" font-weight="bold">20 Okt (2.4L)</text>
        </svg>
      </div>
    </div>

    <!-- Riwayat Aktivitas Susu UHT -->
    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="card-title">Riwayat Mutasi Susu UHT Terakhir</h3>
          <p class="card-subtitle">Log aktivitas keluar masuk bahan ini</p>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Jenis Aktivitas</th>
              <th>Perubahan Stok</th>
              <th>Stok Akhir</th>
              <th>Dicatat Oleh</th>
              <th>Referensi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>20 Okt 2026, 12:15</td>
              <td><span class="badge badge-warning">Pemakaian Resep</span></td>
              <td><strong style="color: var(--danger-red);">-7.200 ml</strong></td>
              <td>2.400 ml</td>
              <td>Dimas Kurnia</td>
              <td><code>RKP-20261020-003</code></td>
            </tr>
            <tr>
              <td>19 Okt 2026, 21:30</td>
              <td><span class="badge badge-warning">Pemakaian Resep</span></td>
              <td><strong style="color: var(--danger-red);">-6.800 ml</strong></td>
              <td>9.600 ml</td>
              <td>Fajar Nugraha</td>
              <td><code>RKP-20261019-002</code></td>
            </tr>
            <tr>
              <td>19 Okt 2026, 18:00</td>
              <td><span class="badge badge-neutral">Penyesuaian (Spill)</span></td>
              <td><strong style="color: var(--danger-red);">-400 ml</strong></td>
              <td>16.400 ml</td>
              <td>Andra (Owner)</td>
              <td><code>ADJ-SPILL-04</code></td>
            </tr>
            <tr>
              <td>18 Okt 2026, 09:30</td>
              <td><span class="badge badge-safe">Penerimaan Supplier</span></td>
              <td><strong style="color: var(--success-green);">+20.000 ml</strong></td>
              <td>16.800 ml</td>
              <td>Dimas Kurnia</td>
              <td><code>INV-SKD-9910</code></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 11 — PENERIMAAN BAHAN
// ==========================================
function renderPenerimaanBahan() {
  return `
    <nav class="breadcrumb-nav">
      <span>Persediaan</span>
      <span class="separator">/</span>
      <span class="current">Penerimaan Bahan</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Form Penerimaan Bahan Baku</h2>
        <p>Catat barang masuk dari supplier untuk menambah stok dan mengaktifkan reminder kedaluwarsa.</p>
      </div>
    </div>

    <div class="grid-2" style="align-items: start;">
      <div class="card">
        <form onsubmit="event.preventDefault(); showToast('Penerimaan bahan berhasil disimpan. Stok telah bertambah!'); navigateTo('monitoring_stok');">
          <div class="form-group">
            <label class="form-label">Nama Supplier / Vendor <span class="required">*</span></label>
            <select class="input-select" required>
              <option value="PT Sukanda Djaya">PT Sukanda Djaya (Dairy & Syrup)</option>
              <option value="Kolektif Roastery">Kolektif Roastery (Coffee Beans)</option>
              <option value="PT Toffin Indonesia">PT Toffin Indonesia (Syrup & Powder)</option>
              <option value="CV Aren Nusantara">CV Aren Nusantara (Palm Sugar)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Nama Bahan Baku <span class="required">*</span></label>
            <select class="input-select" required>
              ${state.ingredients.map(i => `<option value="${i.id}">${i.name} (Satuan: ${i.unit})</option>`).join('')}
            </select>
          </div>

          <div class="grid-2" style="gap: 14px;">
            <div class="form-group">
              <label class="form-label">Jumlah Masuk <span class="required">*</span></label>
              <input type="number" class="input-text" placeholder="Contoh: 10000" required>
            </div>

            <div class="form-group">
              <label class="form-label">Nomor Invoice / Surat Jalan <span class="required">*</span></label>
              <input type="text" class="input-text" placeholder="Contoh: SJ-SKD-2026-981" required>
            </div>
          </div>

          <div class="grid-2" style="gap: 14px;">
            <div class="form-group">
              <label class="form-label">Tanggal Penerimaan <span class="required">*</span></label>
              <input type="date" class="input-text" value="2026-10-20" required>
            </div>

            <div class="form-group">
              <label class="form-label">Tanggal Kedaluwarsa (Batch Expired) <span class="required">*</span></label>
              <input type="date" class="input-text" value="2026-12-31" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Catatan Kondisi Barang</label>
            <textarea class="input-textarea" placeholder="Contoh: Segel utuh, suhu chiller terjaga, kemasan tidak ada yang penyok."></textarea>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="navigateTo('monitoring_stok')">Batal</button>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="check" style="width: 16px; height: 16px;"></i>
              <span>Simpan Penerimaan</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Information Card: Auto Update Guarantee -->
      <div>
        <div class="card" style="background: #FAF8F5; border-left: 4px solid var(--success-green); margin-bottom: 20px;">
          <h3 class="card-title" style="margin-bottom: 12px; color: var(--success-green);">
            <i data-lucide="shield-check" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Otomasi Sistem Setelah Disimpan:
          </h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 13px; color: var(--primary-text);">
            <li style="display: flex; gap: 10px;">
              <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: var(--success-green); flex-shrink: 0;"></i>
              <span><strong>Stok Otomatis Bertambah:</strong> Angka kuantitas langsung masuk ke master monitoring persediaan.</span>
            </li>
            <li style="display: flex; gap: 10px;">
              <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: var(--success-green); flex-shrink: 0;"></i>
              <span><strong>Riwayat Stok Dibuat:</strong> Tercatat mutasi masuk dengan jenis 'Penerimaan' dan nama pencatat Anda.</span>
            </li>
            <li style="display: flex; gap: 10px;">
              <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: var(--success-green); flex-shrink: 0;"></i>
              <span><strong>Reminder Kedaluwarsa Aktif:</strong> Sistem menghitung mundur H-3 sebelum tanggal expired untuk memicu notifikasi.</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 12 — RIWAYAT STOK
// ==========================================
function renderRiwayatStok() {
  return `
    <nav class="breadcrumb-nav">
      <span>Persediaan</span>
      <span class="separator">/</span>
      <span class="current">Riwayat Stok</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Riwayat Mutasi Stok</h2>
        <p>Buku log keluar-masuk persediaan: pemakaian resep, penerimaan supplier, dan penyesuaian fisik.</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="showToast('Ekspor riwayat mutasi stok (CSV/Excel) berhasil disiapkan.', 'success');">
          <i data-lucide="download" style="width: 15px; height: 15px;"></i>
          <span>Export Riwayat</span>
        </button>
      </div>
    </div>

    <!-- Summary Statistics Bar -->
    <div class="grid-4" style="margin-bottom: 24px;">
      <div class="stat-card" style="padding: 16px 20px;">
        <span class="stat-label">Aktivitas Bulan Ini</span>
        <div class="stat-value" style="font-size: 26px;">142 <span style="font-size: 13px; color: var(--secondary-text);">kali</span></div>
      </div>
      <div class="stat-card" style="padding: 16px 20px; border-left: 4px solid var(--success-green);">
        <span class="stat-label">Total Stok Masuk</span>
        <div class="stat-value" style="font-size: 24px; color: var(--success-green);">+420 L <span style="font-size: 14px; color: var(--secondary-text);">/ 95 Kg</span></div>
      </div>
      <div class="stat-card" style="padding: 16px 20px; border-left: 4px solid var(--danger-red);">
        <span class="stat-label">Total Stok Keluar</span>
        <div class="stat-value" style="font-size: 24px; color: var(--danger-red);">-380 L <span style="font-size: 14px; color: var(--secondary-text);">/ 82 Kg</span></div>
      </div>
      <div class="stat-card" style="padding: 16px 20px; border-left: 4px solid var(--warning-orange);">
        <span class="stat-label">Penyesuaian (Spill)</span>
        <div class="stat-value" style="font-size: 24px; color: var(--warning-orange);">+5 L <span style="font-size: 14px; color: var(--secondary-text);">koreksi</span></div>
      </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="card" style="margin-bottom: 20px; padding: 16px;">
      <div style="display: flex; gap: 14px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
        <div class="search-input-wrapper">
          <i data-lucide="search" class="search-icon" style="width: 16px; height: 16px;"></i>
          <input type="text" class="input-text" placeholder="Cari nama bahan atau referensi rekap...">
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
          <select class="input-select" style="width: 160px;">
            <option>Semua Aktivitas</option>
            <option>Pemakaian Resep</option>
            <option>Penerimaan Supplier</option>
            <option>Penyesuaian Fisik</option>
          </select>

          <select class="input-select" style="width: 140px;">
            <option>Bulan Ini (Okt 2026)</option>
            <option>Bulan Lalu (Sep 2026)</option>
            <option>Rentang Kustom</option>
          </select>

          <button class="btn btn-secondary">
            <i data-lucide="filter" style="width: 15px; height: 15px;"></i>
            <span>Filter</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Table of Stock Movements -->
    <div class="table-responsive">
      <table class="table-custom">
        <thead>
          <tr>
            <th>Tanggal & Waktu</th>
            <th>Nama Bahan</th>
            <th>Jenis Aktivitas</th>
            <th>Perubahan (+ / -)</th>
            <th>Stok Akhir</th>
            <th>Dicatat Oleh</th>
            <th>Referensi</th>
          </tr>
        </thead>
        <tbody>
          ${state.stockMovements.map(m => `
            <tr>
              <td>${m.datetime}</td>
              <td><strong>${m.ingredient}</strong></td>
              <td>
                ${m.type === 'Penerimaan' ? '<span class="badge badge-safe">Penerimaan</span>' : ''}
                ${m.type === 'Pemakaian' ? '<span class="badge badge-warning">Pemakaian Resep</span>' : ''}
                ${m.type === 'Penyesuaian' ? '<span class="badge badge-neutral">Penyesuaian</span>' : ''}
              </td>
              <td>
                <strong style="color: ${m.change > 0 ? 'var(--success-green)' : 'var(--danger-red)'}; font-size: 14px;">
                  ${m.change > 0 ? '+' : ''}${m.change.toLocaleString('id-ID')} ${m.unit}
                </strong>
              </td>
              <td>${m.finalStock.toLocaleString('id-ID')} ${m.unit}</td>
              <td>${m.by}</td>
              <td><code>${m.ref}</code></td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div class="pagination-wrapper">
        <span>Menampilkan 1 - 7 dari 142 mutasi tersimpan</span>
        <div class="pagination-pages">
          <button class="page-btn"><i data-lucide="chevron-left" style="width: 14px; height: 14px;"></i></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn"><i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i></button>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 13 — REQUEST PENGADAAN
// ==========================================
function renderRequestPengadaan() {
  const isOwner = state.currentRole === 'owner';

  return `
    <nav class="breadcrumb-nav">
      <span>Pengadaan</span>
      <span class="separator">/</span>
      <span class="current">Request Pengadaan</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Request Pengadaan Bahan</h2>
        <p>${isOwner ? 'Verifikasi dan setujui usulan pengadaan bahan dari barista operasional.' : 'Ajukan kebutuhan bahan baku yang menipis atau habis di coffee bar.'}</p>
      </div>
      <div class="page-actions">
        ${!isOwner ? `
          <button class="btn btn-primary" onclick="openModal('modal-create-request')">
            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
            <span>+ Buat Request Pengadaan</span>
          </button>
        ` : `
          <span class="badge badge-safe">
            <i data-lucide="shield-check" style="width: 14px; height: 14px;"></i>
            Owner: Otoritas Persetujuan Penuh
          </span>
        `}
      </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="nav-tabs">
      <button class="tab-btn active">Semua Request (${state.requests.length})</button>
      <button class="tab-btn">Menunggu (3)</button>
      <button class="tab-btn">Diproses (1)</button>
      <button class="tab-btn">Selesai (1)</button>
      <button class="tab-btn">Ditolak (1)</button>
    </div>

    <!-- Request Cards Grid -->
    <div style="display: flex; flex-direction: column; gap: 14px;">
      ${state.requests.map(req => `
        <div class="card" style="padding: 18px 22px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
            <div>
              <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <span style="font-family: monospace; font-size: 12px; font-weight: 700; color: var(--secondary-text);">${req.id}</span>
                <h3 style="font-family: var(--font-serif); font-size: 17px; font-weight: 700; color: var(--primary-text); margin: 0;">${req.ingredient}</h3>
                <span class="badge ${req.priority === 'Tinggi' ? 'badge-danger' : 'badge-neutral'}">Prioritas: ${req.priority}</span>
              </div>
              <div style="font-size: 12px; color: var(--secondary-text);">
                Diajukan oleh: <strong>${req.requestedBy}</strong> &bull; Tanggal: ${req.date}
              </div>
            </div>

            <div>
              ${req.status === 'Menunggu' ? '<span class="badge badge-warning">Menunggu Review</span>' : ''}
              ${req.status === 'Diproses' ? '<span class="badge badge-blue">Sedang Diproses PO</span>' : ''}
              ${req.status === 'Selesai' ? '<span class="badge badge-safe">Selesai Diterima</span>' : ''}
              ${req.status === 'Ditolak' ? '<span class="badge badge-danger">Ditolak</span>' : ''}
            </div>
          </div>

          <div style="background: #FAF8F5; border-radius: var(--radius-md); padding: 12px 14px; font-size: 13px; margin-bottom: 14px; border: 1px solid var(--border-color);">
            <div style="margin-bottom: 4px;"><strong>Jumlah Kebutuhan:</strong> <span style="font-size: 14px; font-weight: 700; color: var(--primary-blue);">${req.qty}</span></div>
            <div style="color: var(--secondary-text);"><strong>Catatan Alasan:</strong> ${req.note}</div>
          </div>

          <!-- Role Action Buttons: Only Owner Sees Approve/Reject -->
          ${isOwner && req.status === 'Menunggu' ? `
            <div style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 10px; border-top: 1px solid var(--border-color);">
              <button class="btn btn-secondary btn-sm" style="color: var(--danger-red);" onclick="openModal('modal-reject-request')">
                <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                <span>Tolak Request</span>
              </button>
              <button class="btn btn-primary btn-sm" onclick="openModal('modal-approve-request')">
                <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                <span>Setujui & Teruskan ke PO</span>
              </button>
            </div>
          ` : ''}

          ${!isOwner ? `
            <div style="font-size: 11.5px; color: var(--secondary-text); text-align: right; padding-top: 6px;">
              Request Anda terpantau langsung di dashboard Owner Andra.
            </div>
          ` : ''}
        </div>
      `).join('')}
    </div>
  `;
}

// ==========================================
// HALAMAN 14 — PENGADAAN BERJALAN (OWNER ONLY)
// ==========================================
function renderPengadaanBerjalan() {
  const isOwner = state.currentRole === 'owner';

  // Guard for Owner Only
  if (!isOwner) {
    return `
      <div class="card" style="text-align: center; padding: 48px 24px; max-width: 500px; margin: 40px auto;">
        <i data-lucide="shield-alert" style="width: 48px; height: 48px; color: var(--danger-red); margin-bottom: 16px;"></i>
        <h2 style="font-family: var(--font-serif); font-size: 22px; margin-bottom: 8px;">Akses Khusus Owner</h2>
        <p style="font-size: 13.5px; color: var(--secondary-text); margin-bottom: 20px;">
          Halaman Pengadaan Berjalan hanya dapat diakses oleh Owner untuk memantau siklus PO ke supplier dan persetujuan faktur.
        </p>
        <button class="btn btn-primary" onclick="switchRole('owner')">
          <span>Ganti ke Role Owner untuk Mencoba</span>
        </button>
      </div>
    `;
  }

  return `
    <nav class="breadcrumb-nav">
      <span>Pengadaan</span>
      <span class="separator">/</span>
      <span class="current">Pengadaan Berjalan</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Siklus Pengadaan Berjalan</h2>
        <p>Pemantauan alur pemenuhan kebutuhan stok dari alert sistem hingga barang masuk gudang.</p>
      </div>
      <div class="page-actions">
        <span class="badge badge-safe">Owner Only</span>
      </div>
    </div>

    <!-- Visual Alur Pipeline Guide -->
    <div class="card" style="margin-bottom: 24px; background: #FAF8F5;">
      <h4 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--secondary-text); margin-bottom: 8px; font-weight: 700;">
        Panduan Alur Sistem (Pipeline Flowchart):
      </h4>
      <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; font-size: 12.5px; font-weight: 600;">
        <span class="badge badge-neutral">1. Stok Menipis</span>
        <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--secondary-text);"></i>
        <span class="badge badge-neutral">2. System Alert</span>
        <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--secondary-text);"></i>
        <span class="badge badge-neutral">3. Recommendation</span>
        <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--secondary-text);"></i>
        <span class="badge badge-neutral">4. Request</span>
        <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--secondary-text);"></i>
        <span class="badge badge-blue">5. Approval</span>
        <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--secondary-text);"></i>
        <span class="badge badge-neutral">6. Supplier Order</span>
        <i data-lucide="arrow-right" style="width: 14px; height: 14px; color: var(--secondary-text);"></i>
        <span class="badge badge-safe">7. Received</span>
      </div>
    </div>

    <!-- Active Procurement Cards with 6-stage Stepper -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
      ${state.ongoingProcurements.map(po => `
        <div class="card">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <div>
              <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <span class="badge badge-blue">${po.id}</span>
                <h3 style="font-family: var(--font-serif); font-size: 19px; font-weight: 700; color: var(--primary-text); margin: 0;">${po.ingredient}</h3>
              </div>
              <div style="font-size: 13px; color: var(--secondary-text);">
                Supplier: <strong>${po.supplier}</strong> &bull; Estimasi Tiba: <strong>${po.eta}</strong>
              </div>
            </div>

            <div style="text-align: right;">
              <span style="font-size: 11px; color: var(--secondary-text); display: block;">Total Nilai PO</span>
              <strong style="font-size: 18px; color: var(--primary-text);">${po.totalCost}</strong>
            </div>
          </div>

          <!-- 6-Stage Stepper Component -->
          <div class="stepper-pipeline">
            ${po.steps.map((st, sIdx) => {
              const stepNumber = sIdx + 1;
              let stepClass = 'pending';
              if (stepNumber < po.activeStep) stepClass = 'done';
              else if (stepNumber === po.activeStep) stepClass = 'active';

              return `
                <div class="step-node ${stepClass}">
                  <div class="step-circle">
                    ${stepClass === 'done' ? '<i data-lucide="check" style="width: 16px; height: 16px;"></i>' : stepNumber}
                  </div>
                  <div>
                    <div class="step-label">${st.title}</div>
                    <div style="font-size: 9.5px; color: var(--secondary-text); max-width: 90px;">${st.desc}</div>
                  </div>
                </div>
              `;
            }).join('')}
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border-color);">
            <button class="btn btn-secondary btn-sm" onclick="showToast('Invoice & PO PDF telah dibuka.', 'info');">
              <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
              <span>Lihat Purchase Order</span>
            </button>
            <button class="btn btn-primary btn-sm" onclick="navigateTo('penerimaan_bahan'); showToast('Buka formulir penerimaan barang.', 'info');">
              <i data-lucide="package-check" style="width: 14px; height: 14px;"></i>
              <span>Konfirmasi Barang Diterima</span>
            </button>
          </div>
        </div>
      `).join('')}
    </div>
  `;
}

// ==========================================
// HALAMAN 15 — PEMBELIAN DARURAT
// ==========================================
function renderPembelianDarurat() {
  return `
    <nav class="breadcrumb-nav">
      <span>Pengadaan</span>
      <span class="separator">/</span>
      <span class="current">Pembelian Darurat</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Catat Pembelian Darurat (Direct Store Purchase)</h2>
        <p>Gunakan saat stok mendesak habis di tengah jam ramai dan dibeli langsung di toko/supermarket terdekat.</p>
      </div>
    </div>

    <div class="grid-2" style="align-items: start;">
      <div class="card">
        <form onsubmit="event.preventDefault(); showToast('Pembelian darurat berhasil dicatat! Notifikasi terkirim ke Owner Andra.', 'success'); navigateTo('riwayat_stok');">
          <div class="form-group">
            <label class="form-label">Nama Bahan Baku <span class="required">*</span></label>
            <select class="input-select" required>
              ${state.ingredients.map(i => `<option value="${i.id}">${i.name} (Satuan: ${i.unit})</option>`).join('')}
            </select>
          </div>

          <div class="grid-2" style="gap: 14px;">
            <div class="form-group">
              <label class="form-label">Jumlah Yang Dibeli <span class="required">*</span></label>
              <input type="number" class="input-text" placeholder="Contoh: 5000" required>
            </div>

            <div class="form-group">
              <label class="form-label">Total Biaya Pembelian (Rp) <span class="required">*</span></label>
              <input type="text" class="input-text" placeholder="Contoh: 125.000" required>
            </div>
          </div>

          <div class="grid-2" style="gap: 14px;">
            <div class="form-group">
              <label class="form-label">Tempat / Toko Pembelian <span class="required">*</span></label>
              <input type="text" class="input-text" placeholder="Contoh: Superindo / Borma Dago" required>
            </div>

            <div class="form-group">
              <label class="form-label">Tanggal Pembelian <span class="required">*</span></label>
              <input type="date" class="input-text" value="2026-10-20" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Alasan Pembelian Darurat <span class="required">*</span></label>
            <textarea class="input-textarea" placeholder="Contoh: Lonjakan pesanan Butterscotch Latte saat jam makan siang, stok susu chiller habis sebelum jadwal supplier tiba." required></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Upload Bukti Struk / Nota (JPG, PNG, PDF) <span class="required">*</span></label>
            <div class="file-upload-dropzone" onclick="document.getElementById('receipt-upload').click();">
              <i data-lucide="upload-cloud" style="width: 32px; height: 32px; color: var(--primary-blue); margin-bottom: 8px;"></i>
              <p style="font-size: 13px; font-weight: 600; color: var(--primary-text);">Klik untuk unggah foto struk kasir toko</p>
              <p style="font-size: 11px; color: var(--secondary-text);">Mendukung JPG, PNG, atau scan PDF (Maksimal 5MB)</p>
              <input type="file" id="receipt-upload" style="display: none;" onchange="showToast('Struk kasir supermarket berhasil dipilih.', 'info');">
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="navigateTo('dashboard_barista')">Batal</button>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="send" style="width: 16px; height: 16px;"></i>
              <span>Simpan & Kirim Notifikasi ke Owner</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Information Callout -->
      <div>
        <div class="card" style="background: #FAF8F5; border-left: 4px solid var(--warning-orange);">
          <h3 class="card-title" style="margin-bottom: 12px; color: var(--warning-orange);">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Ketentuan Pembelian Darurat:
          </h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
            <li style="display: flex; gap: 10px;">
              <i data-lucide="bell" style="width: 18px; height: 18px; color: var(--warning-orange); flex-shrink: 0;"></i>
              <span><strong>Owner Menerima Notifikasi:</strong> Setiap pencatatan darurat langsung muncul di popover notifikasi Owner Andra.</span>
            </li>
            <li style="display: flex; gap: 10px;">
              <i data-lucide="history" style="width: 18px; height: 18px; color: var(--warning-orange); flex-shrink: 0;"></i>
              <span><strong>Masuk Buku Stok Seketika:</strong> Kuantitas bahan langsung ditambahkan ke stok aktif bar agar resep tidak terganggu.</span>
            </li>
            <li style="display: flex; gap: 10px;">
              <i data-lucide="file-spreadsheet" style="width: 18px; height: 18px; color: var(--warning-orange); flex-shrink: 0;"></i>
              <span><strong>Tercatat di Laporan Biaya:</strong> Pembelian darurat masuk kategori beban pengeluaran mendesak pada laporan bulanan.</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 16 — LAPORAN (OWNER ONLY)
// ==========================================
function renderLaporan() {
  const isOwner = state.currentRole === 'owner';

  if (!isOwner) {
    return `
      <div class="card" style="text-align: center; padding: 48px 24px; max-width: 500px; margin: 40px auto;">
        <i data-lucide="shield-alert" style="width: 48px; height: 48px; color: var(--danger-red); margin-bottom: 16px;"></i>
        <h2 style="font-family: var(--font-serif); font-size: 22px; margin-bottom: 8px;">Akses Khusus Owner</h2>
        <p style="font-size: 13.5px; color: var(--secondary-text); margin-bottom: 20px;">
          Laporan nilai inventaris dan pengeluaran bahan hanya dapat diakses oleh Owner.
        </p>
        <button class="btn btn-primary" onclick="switchRole('owner')">Ganti ke Role Owner</button>
      </div>
    `;
  }

  return `
    <nav class="breadcrumb-nav">
      <span>Lainnya</span>
      <span class="separator">/</span>
      <span class="current">Laporan Persediaan & Operasional</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Laporan Persediaan & Operasional</h2>
        <p>Ringkasan valuasi stok gudang, tren pemakaian bahan baku, dan riwayat realisasi pengadaan.</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="showToast('Mengunduh Laporan Format Microsoft Excel (.xlsx)...', 'success');">
          <i data-lucide="file-spreadsheet" style="width: 15px; height: 15px; color: var(--success-green);"></i>
          <span>Export Excel</span>
        </button>
        <button class="btn btn-secondary" onclick="showToast('Menyiapkan dokumen Laporan Resmi PDF...', 'info');">
          <i data-lucide="file-text" style="width: 15px; height: 15px; color: var(--danger-red);"></i>
          <span>Export PDF</span>
        </button>
      </div>
    </div>

    <!-- Filter Bar: Periode & Jenis Laporan -->
    <div class="card" style="margin-bottom: 24px; padding: 18px;">
      <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 180px;">
          <label class="form-label">Periode Tanggal</label>
          <select class="input-select">
            <option>Bulan Ini (1 - 20 Oktober 2026)</option>
            <option>7 Hari Terakhir</option>
            <option>Bulan Lalu (September 2026)</option>
            <option>Kustom Rentang Tanggal</option>
          </select>
        </div>

        <div style="flex: 1; min-width: 200px;">
          <label class="form-label">Jenis Laporan</label>
          <select class="input-select">
            <option>Laporan Lengkap Seluruh Persediaan</option>
            <option>Laporan Pemakaian Bahan (Cost of Goods)</option>
            <option>Laporan Pembelian & Pengadaan PO</option>
            <option>Laporan Pembelian Darurat Kasir</option>
          </select>
        </div>

        <button class="btn btn-primary" style="height: 40px;" onclick="showToast('Filter laporan berhasil diterapkan.', 'info');">
          <i data-lucide="filter" style="width: 15px; height: 15px;"></i>
          <span>Terapkan Filter</span>
        </button>
      </div>
    </div>

    <!-- 3 Summary KPIs (Rp 48.650.000, Rp 14.820.000, Rp 18.500.000) -->
    <div class="grid-3" style="margin-bottom: 24px;">
      <div class="stat-card" style="border-top: 4px solid var(--primary-blue);">
        <span class="stat-label">Total Nilai Persediaan Aktif</span>
        <div class="stat-value" style="font-size: 28px; color: var(--primary-text);">Rp 48.650.000</div>
        <div class="stat-trend" style="color: var(--secondary-text);">Valuasi 45 item di chiller & gudang</div>
      </div>

      <div class="stat-card" style="border-top: 4px solid var(--warning-orange);">
        <span class="stat-label">Total Pemakaian Bahan (COGS)</span>
        <div class="stat-value" style="font-size: 28px; color: var(--warning-orange);">Rp 14.820.000</div>
        <div class="stat-trend" style="color: var(--success-green);">Efisiensi resep 97.4%</div>
      </div>

      <div class="stat-card" style="border-top: 4px solid var(--success-green);">
        <span class="stat-label">Total Realisasi Pengadaan</span>
        <div class="stat-value" style="font-size: 28px; color: var(--success-green);">Rp 18.500.000</div>
        <div class="stat-trend" style="color: var(--secondary-text);">4 invoice supplier terlunasi</div>
      </div>
    </div>

    <!-- Chart: Pergerakan Nilai Persediaan -->
    <div class="card" style="margin-bottom: 24px;">
      <div class="card-header">
        <div>
          <h3 class="card-title">Pergerakan Nilai Persediaan & Pengeluaran Bahan</h3>
          <p class="card-subtitle">Tren fluktuasi mingguan bulan Oktober 2026</p>
        </div>
      </div>

      <div style="height: 200px; width: 100%; position: relative;">
        <svg viewBox="0 0 700 200" style="width: 100%; height: 100%;">
          <line x1="50" y1="40" x2="680" y2="40" stroke="#E8E1D6" stroke-dasharray="2" />
          <line x1="50" y1="90" x2="680" y2="90" stroke="#E8E1D6" stroke-dasharray="2" />
          <line x1="50" y1="140" x2="680" y2="140" stroke="#E8E1D6" stroke-dasharray="2" />

          <!-- Bars for Weekly COGS -->
          <rect x="110" y="70" width="36" height="90" fill="var(--primary-blue)" rx="4" />
          <rect x="250" y="55" width="36" height="105" fill="var(--primary-blue)" rx="4" />
          <rect x="390" y="45" width="36" height="115" fill="var(--primary-blue)" rx="4" />
          <rect x="530" y="60" width="36" height="100" fill="var(--primary-blue)" rx="4" />

          <!-- Labels -->
          <text x="105" y="180" font-size="11" fill="var(--secondary-text)">Minggu 1</text>
          <text x="245" y="180" font-size="11" fill="var(--secondary-text)">Minggu 2</text>
          <text x="385" y="180" font-size="11" fill="var(--secondary-text)">Minggu 3</text>
          <text x="525" y="180" font-size="11" fill="var(--secondary-text)">Minggu 4 (Est)</text>
        </svg>
      </div>
    </div>
  `;
}

// ==========================================
// HALAMAN 17 — PENGATURAN (OWNER ONLY)
// ==========================================
function renderPengaturan() {
  const isOwner = state.currentRole === 'owner';

  if (!isOwner) {
    return `
      <div class="card" style="text-align: center; padding: 48px 24px; max-width: 500px; margin: 40px auto;">
        <i data-lucide="shield-alert" style="width: 48px; height: 48px; color: var(--danger-red); margin-bottom: 16px;"></i>
        <h2 style="font-family: var(--font-serif); font-size: 22px; margin-bottom: 8px;">Akses Khusus Owner</h2>
        <p style="font-size: 13.5px; color: var(--secondary-text); margin-bottom: 20px;">
          Pengaturan pengguna dan konfigurasi reminder hanya dapat diubah oleh Owner.
        </p>
        <button class="btn btn-primary" onclick="switchRole('owner')">Ganti ke Role Owner</button>
      </div>
    `;
  }

  return `
    <nav class="breadcrumb-nav">
      <span>Lainnya</span>
      <span class="separator">/</span>
      <span class="current">Pengaturan Sistem</span>
    </nav>

    <div class="page-header">
      <div class="page-title-group">
        <h2>Pengaturan Sistem & Pengguna</h2>
        <p>Kelola hak akses akun barista, batas reminder kedaluwarsa, dan notifikasi stok.</p>
      </div>
    </div>

    <div class="grid-2" style="grid-template-columns: 240px 1fr; align-items: start; gap: 24px;">
      <!-- Navigasi Pengaturan di Kiri -->
      <div class="card" style="padding: 12px;">
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <a class="nav-item active" style="padding: 10px 14px; border-radius: var(--radius-md); font-weight: 600; display: flex; align-items: center; gap: 10px; background: var(--primary-blue); color: #fff;">
            <i data-lucide="users" style="width: 16px; height: 16px;"></i>
            <span>Manajemen Pengguna</span>
          </a>
          <a class="nav-item" style="padding: 10px 14px; border-radius: var(--radius-md); font-weight: 500; display: flex; align-items: center; gap: 10px; color: var(--primary-text);" onclick="showToast('Beralih ke tab reminder.', 'info');">
            <i data-lucide="bell" style="width: 16px; height: 16px;"></i>
            <span>Reminder & Notifikasi</span>
          </a>
        </div>
      </div>

      <!-- Panel Konten Tab Manajemen Pengguna & Reminder -->
      <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Tab 1: Manajemen Pengguna -->
        <div class="card">
          <div class="card-header">
            <div>
              <h3 class="card-title">Daftar Pengguna Operasional</h3>
              <p class="card-subtitle">Akun resmi Owner dan Barista yang berhak mengakses sistem</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-add-user')">
              <i data-lucide="user-plus" style="width: 15px; height: 15px;"></i>
              <span>+ Tambah Pengguna</span>
            </button>
          </div>

          <div class="table-responsive">
            <table class="table-custom">
              <thead>
                <tr>
                  <th>Nama Lengkap</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Login Terakhir</th>
                  <th style="text-align: right;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                ${state.users.map(u => `
                  <tr>
                    <td><strong>${u.name}</strong></td>
                    <td>${u.email}</td>
                    <td>
                      <span class="badge ${u.role === 'Owner' ? 'badge-warning' : 'badge-blue'}">${u.role}</span>
                    </td>
                    <td>
                      <span class="badge ${u.status === 'Aktif' ? 'badge-safe' : 'badge-neutral'}">${u.status}</span>
                    </td>
                    <td><span style="font-size: 11.5px; color: var(--secondary-text);">${u.lastLogin}</span></td>
                    <td style="text-align: right;">
                      <button class="btn btn-ghost btn-sm" onclick="showToast('Ubah detail akun ${u.name}.', 'info');">
                        <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                      </button>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tab 2: Reminder & Parameter Ambang Batas -->
        <div class="card">
          <div class="card-header">
            <div>
              <h3 class="card-title">Konfigurasi Parameter Reminder</h3>
              <p class="card-subtitle">Aturan otomatis peringatan stok dan tanggal kedaluwarsa</p>
            </div>
          </div>

          <form onsubmit="event.preventDefault(); showToast('Parameter reminder berhasil diperbarui!', 'success');">
            <div class="form-group">
              <label class="form-label">Pemicu Reminder Sebelum Kedaluwarsa (Hari)</label>
              <div style="display: flex; align-items: center; gap: 12px;">
                <input type="number" class="input-text" value="3" style="width: 100px;">
                <span style="font-size: 13px; color: var(--secondary-text);">Hari sebelum tanggal expired (H-3)</span>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Notifikasi Stok Minimum Aktif</label>
              <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px;">
                <input type="checkbox" checked style="accent-color: var(--primary-blue); width: 16px; height: 16px;">
                <span>Aktifkan alarm merah di topbar jika stok bahan berada &le; batas aman</span>
              </label>
            </div>

            <div class="form-group">
              <label class="form-label">Penerima Email Rekap Pengadaan Harian</label>
              <input type="email" class="input-text" value="andra.owner@kopikohlie.com">
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
              <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                <span>Simpan Pengaturan</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  `;
}

// ==========================================
// MODAL DIALOGS RENDERER
// ==========================================
function renderAllModals() {
  return `
    <!-- Modal 1: Validasi Rekap Berhasil (Kondisi Bahan Cukup) -->
    <div id="modal-validate-recap-success" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-header">
          <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--success-bg); color: var(--success-green); display: flex; align-items: center; justify-content: center;">
              <i data-lucide="check" style="width: 18px; height: 18px;"></i>
            </div>
            <h3 style="margin: 0;">Validasi Rekap Berhasil</h3>
          </div>
          <button class="btn-icon" onclick="closeModal('modal-validate-recap-success')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <p style="font-size: 13.5px; color: var(--primary-text); margin-bottom: 14px;">
            Seluruh kebutuhan bahan baku untuk <strong>${state.currentDraftItems.reduce((a, b) => a + b.qty, 0)} cup menu</strong> dalam batch ini <strong>mencukupi di gudang</strong>.
          </p>
          <div style="background: #FAF8F5; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 12px 16px; font-size: 12.5px; margin-bottom: 16px;">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 6px;">
              <li>&bull; Susu UHT akan dikurangi: <strong>4.900 ml</strong></li>
              <li>&bull; House Blend akan dikurangi: <strong>756 g</strong></li>
              <li>&bull; Sirup Butterscotch akan dikurangi: <strong>350 ml</strong></li>
            </ul>
          </div>
          <p style="font-size: 12px; color: var(--secondary-text);">
            Setelah disimpan, stok akan langsung dikurangi dan tercatat dalam Riwayat Rekap #RKP-20261020-004.
          </p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-validate-recap-success')">Kembali</button>
          <button class="btn btn-primary" onclick="closeModal('modal-validate-recap-success'); showToast('Rekap pesanan berhasil disimpan & stok diperbarui!', 'success'); navigateTo('riwayat_rekap');">
            <span>Konfirmasi & Simpan Rekap</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 2: Validasi Rekap Gagal (Kondisi Stok Tidak Mencukupi) -->
    <div id="modal-validate-recap-deficit" class="modal-backdrop">
      <div class="modal-dialog" style="border-top: 5px solid var(--danger-red);">
        <div class="modal-header">
          <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--danger-bg); color: var(--danger-red); display: flex; align-items: center; justify-content: center;">
              <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
            </div>
            <h3 style="margin: 0; color: var(--danger-red);">Peringatan: Stok Tidak Mencukupi</h3>
          </div>
          <button class="btn-icon" onclick="closeModal('modal-validate-recap-deficit')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <p style="font-size: 13px; color: var(--primary-text); margin-bottom: 12px;">
            Sistem mendeteksi kekurangan pada bahan berikut untuk rekap transaksi yang dimasukkan:
          </p>
          <div class="table-responsive" style="margin-bottom: 14px;">
            <table class="table-custom">
              <thead>
                <tr>
                  <th>Bahan Baku</th>
                  <th>Stok Tersedia</th>
                  <th>Kebutuhan</th>
                  <th>Kekurangan</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Sirup Butterscotch</strong></td>
                  <td>180 ml</td>
                  <td>350 ml</td>
                  <td><strong style="color: var(--danger-red);">-170 ml</strong></td>
                </tr>
                <tr>
                  <td><strong>Telur Omega</strong></td>
                  <td>0 butir</td>
                  <td>12 butir</td>
                  <td><strong style="color: var(--danger-red);">-12 butir</strong></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="callout-box callout-danger" style="font-size: 12px; margin-bottom: 0;">
            <strong>Catatan Audit:</strong> Jika Anda tetap mencatat, stok akan tercatat minus / defisit, dan notifikasi darurat prioritas tinggi akan dikirimkan langsung ke Owner Andra untuk diverifikasi.
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-validate-recap-deficit')">Batalkan</button>
          <button class="btn btn-danger" onclick="closeModal('modal-validate-recap-deficit'); showToast('Rekap dicatat dengan catatan kekurangan stok. Owner telah dinotifikasi.', 'warning'); navigateTo('riwayat_rekap');">
            <span>Catat dengan Stok Tidak Mencukupi</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 3: Atur Batas Aman & Reminder (Owner Only) -->
    <div id="modal-set-min-stock" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3>Atur Batas Aman & Aturan Reminder</h3>
          <button class="btn-icon" onclick="closeModal('modal-set-min-stock')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Pilihan Produk / Bahan Baku</label>
            <select class="input-select">
              ${state.ingredients.map(i => `<option>${i.name} (Saat ini: ${i.minStock} ${i.unit})</option>`).join('')}
            </select>
          </div>
          <div class="grid-2" style="gap: 12px;">
            <div class="form-group">
              <label class="form-label">Nilai Batas Aman Baru</label>
              <input type="number" class="input-text" value="3500">
            </div>
            <div class="form-group">
              <label class="form-label">Jumlah Hari Reminder</label>
              <input type="number" class="input-text" value="3">
            </div>
          </div>
          <div style="background: #FAF8F5; border-radius: var(--radius-md); padding: 12px; font-size: 12px; color: var(--secondary-text); border: 1px solid var(--border-color);">
            <strong>Preview Aturan:</strong> Notifikasi oranye akan muncul jika stok &le; 3.500 ml atau masa expired tersisa &le; 3 hari kalender.
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-set-min-stock')">Batal</button>
          <button class="btn btn-primary" onclick="closeModal('modal-set-min-stock'); showToast('Batas aman stok berhasil diperbarui!', 'success');">
            <span>Simpan Pengaturan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 4: Approval Pengadaan (Owner Only) -->
    <div id="modal-approve-request" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3>Persetujuan Request Pengadaan</h3>
          <button class="btn-icon" onclick="closeModal('modal-approve-request')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <p style="font-size: 13.5px; margin-bottom: 12px;">
            Anda akan menyetujui request pengadaan <strong>40 Liter Susu UHT Plain</strong> yang diajukan oleh Barista Dimas.
          </p>
          <div class="callout-box callout-info" style="font-size: 12.5px; margin-bottom: 0;">
            Setelah disetujui, nomor Purchase Order baru (PO-2026-020) akan otomatis diterbitkan dan diteruskan ke PT Sukanda Djaya.
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-approve-request')">Batal</button>
          <button class="btn btn-success" onclick="closeModal('modal-approve-request'); showToast('Request pengadaan disetujui & PO diterbitkan!', 'success'); navigateTo('pengadaan_berjalan');">
            <span>Setujui & Teruskan ke PO</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 5: Tolak Request Pengadaan (Owner Only) -->
    <div id="modal-reject-request" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3 style="color: var(--danger-red);">Tolak Request Pengadaan</h3>
          <button class="btn-icon" onclick="closeModal('modal-reject-request')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <p style="font-size: 13px; margin-bottom: 14px;">
            Berikan alasan penolakan agar barista operasional mengetahui alasan penundaan pengadaan:
          </p>
          <div class="form-group">
            <label class="form-label">Alasan Penolakan <span class="required">*</span></label>
            <textarea class="input-textarea" placeholder="Contoh: Stok cadangan masih tersedia di freezer lantai 2 atau anggaran dialokasikan untuk roasted beans." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-reject-request')">Batal</button>
          <button class="btn btn-danger" onclick="closeModal('modal-reject-request'); showToast('Request pengadaan telah ditolak dengan catatan alasan.', 'info');">
            <span>Konfirmasi Tolak Request</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 6: Tambah Menu Baru (Owner Only) -->
    <div id="modal-add-menu" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3>Tambah Menu Minuman Baru</h3>
          <button class="btn-icon" onclick="closeModal('modal-add-menu')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Nama Menu <span class="required">*</span></label>
            <input type="text" class="input-text" placeholder="Contoh: Sea Salt Caramel Latte" required>
          </div>
          <div class="grid-2" style="gap: 12px;">
            <div class="form-group">
              <label class="form-label">Kategori</label>
              <select class="input-select">
                <option>Signature</option>
                <option>Coffee</option>
                <option>Non-Coffee</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Harga Jual (Rp)</label>
              <input type="number" class="input-text" placeholder="38000" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Deskripsi Minuman</label>
            <textarea class="input-textarea" placeholder="Deskripsi rasa dan bahan penyusun menu."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-add-menu')">Batal</button>
          <button class="btn btn-primary" onclick="closeModal('modal-add-menu'); showToast('Menu baru berhasil ditambahkan ke katalog!', 'success');">
            <span>Simpan Menu Baru</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 7: Tambah Pengguna Baru (Owner Only) -->
    <div id="modal-add-user" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3>Tambah Akun Pengguna Baru</h3>
          <button class="btn-icon" onclick="closeModal('modal-add-user')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
            <input type="text" class="input-text" placeholder="Contoh: Rian Anggara" required>
          </div>
          <div class="form-group">
            <label class="form-label">Alamat Email Operasional <span class="required">*</span></label>
            <input type="email" class="input-text" placeholder="rian.barista@kopikohlie.com" required>
          </div>
          <div class="grid-2" style="gap: 12px;">
            <div class="form-group">
              <label class="form-label">Role Akses</label>
              <select class="input-select">
                <option selected>Barista</option>
                <option>Owner</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Password Awal</label>
              <input type="text" class="input-text" value="KohlieBarista#2026">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-add-user')">Batal</button>
          <button class="btn btn-primary" onclick="closeModal('modal-add-user'); showToast('Akun pengguna baru berhasil dibuat!', 'success');">
            <span>Tambah Pengguna</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 8: Buat Request Pengadaan (Barista) -->
    <div id="modal-create-request" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-header">
          <h3>Buat Request Pengadaan Baru</h3>
          <button class="btn-icon" onclick="closeModal('modal-create-request')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Pilih Bahan Baku <span class="required">*</span></label>
            <select class="input-select">
              ${state.ingredients.map(i => `<option>${i.name} (Sisa stok: ${i.currentStock} ${i.unit})</option>`).join('')}
            </select>
          </div>
          <div class="grid-2" style="gap: 12px;">
            <div class="form-group">
              <label class="form-label">Kuantitas Kebutuhan <span class="required">*</span></label>
              <input type="text" class="input-text" placeholder="Contoh: 30 Liter" required>
            </div>
            <div class="form-group">
              <label class="form-label">Tingkat Prioritas</label>
              <select class="input-select">
                <option>Tinggi (Stok < 1 Shift)</option>
                <option selected>Sedang (Kebutuhan Mingguan)</option>
                <option>Rendah (Penyimpanan Cadangan)</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Catatan Alasan Pengajuan <span class="required">*</span></label>
            <textarea class="input-textarea" placeholder="Jelaskan kondisi persediaan fisik di bar atau prediksi event ramai." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('modal-create-request')">Batal</button>
          <button class="btn btn-primary" onclick="closeModal('modal-create-request'); showToast('Request pengadaan berhasil diajukan ke Owner Andra!', 'success');">
            <span>Kirim Request ke Owner</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal 9: Directory of all 17 Pages for Rapid Evaluation -->
    <div id="modal-page-directory" class="modal-backdrop">
      <div class="modal-dialog" style="max-width: 650px;">
        <div class="modal-header">
          <div>
            <h3 style="font-family: var(--font-serif); font-size: 19px;">Direktori 17 Halaman UI Kopi Kohlie</h3>
            <p style="font-size: 12px; color: var(--secondary-text);">Klik nama halaman untuk langsung melompat dan menguji fitur</p>
          </div>
          <button class="btn-icon" onclick="closeModal('modal-page-directory')"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
        </div>
        <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-size: 13px;">
            <a onclick="navigateTo('login'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>1. Halaman Login</span>
            </a>
            <a onclick="navigateTo('dashboard_owner'); switchRole('owner'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>2. Dashboard Owner</span>
            </a>
            <a onclick="navigateTo('dashboard_barista'); switchRole('barista'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>3. Dashboard Barista</span>
            </a>
            <a onclick="navigateTo('input_rekap'); switchRole('barista'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>4. Input Rekap Kasir</span>
            </a>
            <a onclick="navigateTo('riwayat_rekap'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>5. Riwayat Rekap</span>
            </a>
            <a onclick="navigateTo('detail_rekap'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>6. Detail Rekap</span>
            </a>
            <a onclick="navigateTo('daftar_menu'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>7. Daftar Menu</span>
            </a>
            <a onclick="navigateTo('detail_resep'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>8. Detail Resep</span>
            </a>
            <a onclick="navigateTo('monitoring_stok'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>9. Monitoring Persediaan</span>
            </a>
            <a onclick="navigateTo('detail_bahan'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>10. Detail Bahan (Susu UHT)</span>
            </a>
            <a onclick="navigateTo('penerimaan_bahan'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>11. Penerimaan Bahan</span>
            </a>
            <a onclick="navigateTo('riwayat_stok'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>12. Riwayat Mutasi Stok</span>
            </a>
            <a onclick="navigateTo('request_pengadaan'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>13. Request Pengadaan</span>
            </a>
            <a onclick="navigateTo('pengadaan_berjalan'); switchRole('owner'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>14. Pengadaan Berjalan (Owner)</span>
            </a>
            <a onclick="navigateTo('pembelian_darurat'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>15. Pembelian Darurat</span>
            </a>
            <a onclick="navigateTo('laporan'); switchRole('owner'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>16. Laporan (Owner)</span>
            </a>
            <a onclick="navigateTo('pengaturan'); switchRole('owner'); closeModal('modal-page-directory');" class="quick-action-card" style="padding: 10px;">
              <span>17. Pengaturan (Owner)</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  `;
}
