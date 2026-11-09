/**
 * KOPI KOHLIE STOCK & SUPPLY — OPERATIONAL ENGINE & UI CONTROLLER
 * Full Interactive Prototype covering all 17 pages and dual role simulation (Owner & Barista)
 */

// Application State Store
const state = {
  currentRole: 'owner', // 'owner' or 'barista'
  currentPage: 'dashboard_owner',
  sidebarOpen: false,
  notifPopoverOpen: false,
  unreadNotifs: 4,
  
  // Master Ingredients
  ingredients: [
    { id: 'ing-1', name: 'Susu UHT Plain', category: 'Dairy', currentStock: 2400, unit: 'ml', minStock: 3000, capacity: 10000, status: 'warning', expiry: '2026-10-23', supplier: 'PT Sukanda Djaya', price: 18500, daysLeft: 3 },
    { id: 'ing-2', name: 'Biji Kopi House Blend', category: 'Coffee', currentStock: 4800, unit: 'g', minStock: 2000, capacity: 10000, status: 'safe', expiry: '2026-12-15', supplier: 'Kolektif Roastery', price: 175000, daysLeft: 56 },
    { id: 'ing-3', name: 'Sirup Butterscotch Monin', category: 'Syrup', currentStock: 180, unit: 'ml', minStock: 500, capacity: 2000, status: 'warning', expiry: '2027-02-10', supplier: 'PT Toffin Indonesia', price: 145000, daysLeft: 112 },
    { id: 'ing-4', name: 'Matcha Uji Premium Powder', category: 'Powder', currentStock: 950, unit: 'g', minStock: 500, capacity: 2500, status: 'safe', expiry: '2026-11-30', supplier: 'Japan Green Tea Co', price: 290000, daysLeft: 41 },
    { id: 'ing-5', name: 'Sirup Karamel', category: 'Syrup', currentStock: 1200, unit: 'ml', minStock: 600, capacity: 3000, status: 'safe', expiry: '2027-03-01', supplier: 'PT Toffin Indonesia', price: 135000, daysLeft: 131 },
    { id: 'ing-6', name: 'Dark Chocolate Ganache', category: 'Sauce', currentStock: 320, unit: 'g', minStock: 600, capacity: 2500, status: 'warning', expiry: '2026-11-05', supplier: 'PT Sukanda Djaya', price: 95000, daysLeft: 16 },
    { id: 'ing-7', name: 'Krim Nabati (Non-Dairy Creamer)', category: 'Dairy', currentStock: 450, unit: 'g', minStock: 1000, capacity: 5000, status: 'warning', expiry: '2026-11-20', supplier: 'CV Sinar Abadi', price: 42000, daysLeft: 31 },
    { id: 'ing-8', name: 'Telur Ayam Omega (Egg Yolk)', category: 'Fresh', currentStock: 0, unit: 'butir', minStock: 20, capacity: 100, status: 'danger', expiry: '2026-10-22', supplier: 'Farm Fresh Lembang', price: 2800, daysLeft: 2 },
    { id: 'ing-9', name: 'Gula Aren Cair Organik', category: 'Sweetener', currentStock: 3200, unit: 'ml', minStock: 1500, capacity: 8000, status: 'safe', expiry: '2027-01-10', supplier: 'CV Aren Nusantara', price: 38000, daysLeft: 82 },
    { id: 'ing-10', name: 'Biji Kopi Single Origin Gayo', category: 'Coffee', currentStock: 0, unit: 'g', minStock: 1500, capacity: 5000, status: 'danger', expiry: '2026-11-10', supplier: 'Kolektif Roastery', price: 240000, daysLeft: 21 },
    { id: 'ing-11', name: 'Oat Milk Barista Edition', category: 'Dairy', currentStock: 0, unit: 'ml', minStock: 4000, capacity: 12000, status: 'danger', expiry: '2026-11-01', supplier: 'PT Sukanda Djaya', price: 44000, daysLeft: 12 }
  ],

  // Master Menus & Recipes
  menus: [
    {
      id: 'menu-1',
      name: 'Butterscotch Latte',
      category: 'Signature',
      price: 36000,
      image: 'images/butterscotch-latte.jpg',
      isSignature: true,
      ingredientCount: 4,
      description: 'Signature iced latte dengan sirup butterscotch premium, double shot house blend espresso, dan foam susu gurih lembut.',
      recipe: [
        { name: 'Susu UHT Plain', dosage: 150, unit: 'ml', stock: 2400 },
        { name: 'Biji Kopi House Blend', dosage: 18, unit: 'g', stock: 4800 },
        { name: 'Sirup Butterscotch Monin', dosage: 25, unit: 'ml', stock: 180 },
        { name: 'Gula Aren Cair Organik', dosage: 10, unit: 'ml', stock: 3200 }
      ]
    },
    {
      id: 'menu-2',
      name: 'Matcha Latte',
      category: 'Non-Coffee',
      price: 34000,
      image: 'images/matcha-latte.jpg',
      isSignature: false,
      ingredientCount: 3,
      description: 'Authentic Japanese Uji matcha berpadu dengan fresh milk dingin yang creamy dan manis seimbang.',
      recipe: [
        { name: 'Matcha Uji Premium Powder', dosage: 12, unit: 'g', stock: 950 },
        { name: 'Susu UHT Plain', dosage: 160, unit: 'ml', stock: 2400 },
        { name: 'Gula Aren Cair Organik', dosage: 15, unit: 'ml', stock: 3200 }
      ]
    },
    {
      id: 'menu-3',
      name: 'Americano',
      category: 'Coffee',
      price: 26000,
      image: 'images/americano.jpg',
      isSignature: false,
      ingredientCount: 2,
      description: 'Ekstraksi ganda espresso house blend khas Kohlie dengan air mineral dingin dan es kristal segar.',
      recipe: [
        { name: 'Biji Kopi House Blend', dosage: 18, unit: 'g', stock: 4800 },
        { name: 'Air Mineral RO', dosage: 180, unit: 'ml', stock: 50000 }
      ]
    },
    {
      id: 'menu-4',
      name: 'Kohlie Latte',
      category: 'Signature',
      price: 32000,
      image: 'images/kohlie-latte.jpg',
      isSignature: true,
      ingredientCount: 3,
      description: 'Kopi susu andalan Kohlie dengan rasa creamy legit khas gula aren lokal dan biji kopi pilihan.',
      recipe: [
        { name: 'Susu UHT Plain', dosage: 140, unit: 'ml', stock: 2400 },
        { name: 'Biji Kopi House Blend', dosage: 18, unit: 'g', stock: 4800 },
        { name: 'Gula Aren Cair Organik', dosage: 20, unit: 'ml', stock: 3200 }
      ]
    },
    {
      id: 'menu-5',
      name: 'Caramel Macchiato',
      category: 'Coffee',
      price: 38000,
      image: 'images/caramel-macchiato.jpg',
      isSignature: false,
      ingredientCount: 4,
      description: 'Fresh milk dingin dengan sirup vanilla lembut, espresso float tebal, dan drizzle saus karamel lezat di atasnya.',
      recipe: [
        { name: 'Susu UHT Plain', dosage: 140, unit: 'ml', stock: 2400 },
        { name: 'Biji Kopi House Blend', dosage: 18, unit: 'g', stock: 4800 },
        { name: 'Sirup Karamel', dosage: 25, unit: 'ml', stock: 1200 },
        { name: 'Krim Nabati', dosage: 10, unit: 'g', stock: 450 }
      ]
    },
    {
      id: 'menu-6',
      name: 'Belgian Chocolate',
      category: 'Non-Coffee',
      price: 35000,
      image: 'images/chocolate.jpg',
      isSignature: false,
      ingredientCount: 3,
      description: 'Cokelat Belgia pekat dengan ganache tebal dan susu sapi segar, disajikan dingin dengan taburan cocoa dust.',
      recipe: [
        { name: 'Dark Chocolate Ganache', dosage: 30, unit: 'g', stock: 320 },
        { name: 'Susu UHT Plain', dosage: 150, unit: 'ml', stock: 2400 },
        { name: 'Gula Aren Cair Organik', dosage: 10, unit: 'ml', stock: 3200 }
      ]
    }
  ],

  // Rekap Riwayat List
  recaps: [
    { id: 'RKP-20261020-003', cashierRef: 'TRX-POS-1042..1078', date: '2026-10-20', time: '12:15', shift: 'Pagi', barista: 'Dimas Kurnia', itemsCount: 48, status: 'Selesai' },
    { id: 'RKP-20261020-002', cashierRef: 'TRX-POS-1015..1041', date: '2026-10-20', time: '10:00', shift: 'Pagi', barista: 'Dimas Kurnia', itemsCount: 39, status: 'Selesai' },
    { id: 'RKP-20261019-002', cashierRef: 'TRX-POS-0980..1014', date: '2026-10-19', time: '21:30', shift: 'Malam', barista: 'Fajar Nugraha', itemsCount: 52, status: 'Selesai' },
    { id: 'RKP-20261019-001', cashierRef: 'TRX-POS-0925..0979', date: '2026-10-19', time: '14:45', shift: 'Siang', barista: 'Dimas Kurnia', itemsCount: 64, status: 'Selesai' },
    { id: 'RKP-20261018-002', cashierRef: 'TRX-POS-0870..0924', date: '2026-10-18', time: '21:40', shift: 'Malam', barista: 'Fajar Nugraha', itemsCount: 58, status: 'Selesai' },
    { id: 'RKP-20261018-001', cashierRef: 'TRX-POS-0810..0869', date: '2026-10-18', time: '14:20', shift: 'Siang', barista: 'Dimas Kurnia', itemsCount: 60, status: 'Selesai' }
  ],

  // Procurement Requests
  requests: [
    { id: 'REQ-2026-041', ingredient: 'Susu UHT Plain', qty: '40 Liter (4 Karton)', requestedBy: 'Dimas Kurnia', date: '2026-10-20 09:30', priority: 'Tinggi', status: 'Menunggu', note: 'Stok di chiller sisa 2.4L, perkiraan habis sebelum shift sore selesai.' },
    { id: 'REQ-2026-040', ingredient: 'Sirup Butterscotch Monin', qty: '6 Botol (700ml)', requestedBy: 'Dimas Kurnia', date: '2026-10-20 08:45', priority: 'Tinggi', status: 'Menunggu', note: 'Sisa botol aktif di station tinggal 180ml.' },
    { id: 'REQ-2026-039', ingredient: 'Telur Ayam Omega', qty: '5 Tray (150 butir)', requestedBy: 'Dimas Kurnia', date: '2026-10-20 08:30', priority: 'Tinggi', status: 'Menunggu', note: 'Stok kosong di pantry, resep egg foam tertunda.' },
    { id: 'REQ-2026-038', ingredient: 'Biji Kopi House Blend', qty: '20 Kg (1 Karung)', requestedBy: 'Fajar Nugraha', date: '2026-10-19 16:00', priority: 'Sedang', status: 'Diproses', note: 'Pengadaan mingguan rutin.' },
    { id: 'REQ-2026-037', ingredient: 'Oat Milk Barista Edition', qty: '12 Liter', requestedBy: 'Dimas Kurnia', date: '2026-10-18 11:20', priority: 'Sedang', status: 'Selesai', note: 'Sudah diterima faktur #INV-SKD-9921.' },
    { id: 'REQ-2026-036', ingredient: 'Sirup Hazelnut', qty: '4 Botol', requestedBy: 'Fajar Nugraha', date: '2026-10-17 14:10', priority: 'Rendah', status: 'Ditolak', note: 'Penolakan Owner: Varian sirup hazelnut sedang di-discontinue bulan ini.' }
  ],

  // Ongoing Procurement Pipelines (Owner Only)
  ongoingProcurements: [
    {
      id: 'PO-2026-018',
      ingredient: 'Susu UHT Diamond Plain 1L',
      qty: '50 Karton (600 Liter)',
      supplier: 'PT Sukanda Djaya',
      eta: '21 Oktober 2026, 10:00 WIB',
      totalCost: 'Rp 11.100.000',
      activeStep: 4, // 1: Alert, 2: Req, 3: Approval, 4: Order Supplier, 5: Diterima, 6: Stok Bertambah
      steps: [
        { title: 'System Alert', status: 'done', desc: 'Terdeteksi stok di bawah 3.000 ml' },
        { title: 'Request Dibuat', status: 'done', desc: 'Diajukan oleh Barista Dimas' },
        { title: 'Owner Approval', status: 'done', desc: 'Disetujui oleh Andra (PO diterbitkan)' },
        { title: 'Order Supplier', status: 'active', desc: 'Invoice dikirim ke PT Sukanda Djaya' },
        { title: 'Barang Diterima', status: 'pending', desc: 'Menunggu kiriman tiba di outlet' },
        { title: 'Stok Bertambah', status: 'pending', desc: 'Otomatis masuk buku stok' }
      ]
    },
    {
      id: 'PO-2026-019',
      ingredient: 'Biji Kopi House Blend Robusta-Arabica',
      qty: '30 Kg',
      supplier: 'Kolektif Roastery',
      eta: '22 Oktober 2026, 14:00 WIB',
      totalCost: 'Rp 5.250.000',
      activeStep: 3,
      steps: [
        { title: 'System Alert', status: 'done', desc: 'Reminder siklus roasting mingguan' },
        { title: 'Request Dibuat', status: 'done', desc: 'Diajukan oleh Barista Fajar' },
        { title: 'Owner Approval', status: 'active', desc: 'Sedang diverifikasi invoice & harga' },
        { title: 'Order Supplier', status: 'pending', desc: 'Penerbitan PO' },
        { title: 'Barang Diterima', status: 'pending', desc: 'Pengecekan tanggal roasting' },
        { title: 'Stok Bertambah', status: 'pending', desc: 'Integrasi inventory' }
      ]
    }
  ],

  // Stock Movement History
  stockMovements: [
    { datetime: '2026-10-20 12:15', ingredient: 'Susu UHT Plain', type: 'Pemakaian', change: -7200, unit: 'ml', finalStock: 2400, by: 'Dimas Kurnia', ref: 'RKP-20261020-003' },
    { datetime: '2026-10-20 12:15', ingredient: 'Biji Kopi House Blend', type: 'Pemakaian', change: -864, unit: 'g', finalStock: 4800, by: 'Dimas Kurnia', ref: 'RKP-20261020-003' },
    { datetime: '2026-10-20 12:15', ingredient: 'Sirup Butterscotch Monin', type: 'Pemakaian', change: -350, unit: 'ml', finalStock: 180, by: 'Dimas Kurnia', ref: 'RKP-20261020-003' },
    { datetime: '2026-10-20 09:00', ingredient: 'Gula Aren Cair Organik', type: 'Penerimaan', change: 2000, unit: 'ml', finalStock: 3200, by: 'Dimas Kurnia', ref: 'RCV-20261020-01' },
    { datetime: '2026-10-19 21:30', ingredient: 'Susu UHT Plain', type: 'Pemakaian', change: -6800, unit: 'ml', finalStock: 9600, by: 'Fajar Nugraha', ref: 'RKP-20261019-002' },
    { datetime: '2026-10-19 18:00', ingredient: 'Susu UHT Plain', type: 'Penyesuaian', change: -400, unit: 'ml', finalStock: 16400, by: 'Andra (Owner)', ref: 'ADJ-SPILL-04' },
    { datetime: '2026-10-18 10:00', ingredient: 'Oat Milk Barista Edition', type: 'Penerimaan', change: 12000, unit: 'ml', finalStock: 12000, by: 'Dimas Kurnia', ref: 'INV-SKD-9921' }
  ],

  // Users Management (Owner Only)
  users: [
    { id: 'usr-1', name: 'Andra Pratama', email: 'andra.owner@kopikohlie.com', role: 'Owner', status: 'Aktif', lastLogin: 'Hari ini, 08:12' },
    { id: 'usr-2', name: 'Dimas Kurnia', email: 'dimas.barista@kopikohlie.com', role: 'Barista', status: 'Aktif', lastLogin: 'Hari ini, 06:45' },
    { id: 'usr-3', name: 'Fajar Nugraha', email: 'fajar.barista@kopikohlie.com', role: 'Barista', status: 'Aktif', lastLogin: 'Kemarin, 14:30' },
    { id: 'usr-4', name: 'Siti Rahma', email: 'siti.barista@kopikohlie.com', role: 'Barista', status: 'Nonaktif', lastLogin: '14 Okt 2026' }
  ],

  // In-Memory Recap Drafting for Page 4
  currentDraftItems: [
    { menuId: 'menu-1', menuName: 'Butterscotch Latte', qty: 14 },
    { menuId: 'menu-4', menuName: 'Kohlie Latte', qty: 20 },
    { menuId: 'menu-3', menuName: 'Americano', qty: 8 }
  ]
};

// Quick Date Formatter
function getOperationalDateString() {
  return "Selasa, 20 Oktober 2026";
}

// Navigation Dispatcher
function navigateTo(pageId) {
  state.currentPage = pageId;
  closeSidebar();
  closeNotifPopover();
  renderApp();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Role Switcher Dispatcher
function switchRole(newRole) {
  state.currentRole = newRole;
  if (newRole === 'barista') {
    // If current page is owner-only, redirect to barista dashboard
    const ownerOnlyPages = ['dashboard_owner', 'pengadaan_berjalan', 'laporan', 'pengaturan'];
    if (ownerOnlyPages.includes(state.currentPage)) {
      state.currentPage = 'dashboard_barista';
    }
  } else if (newRole === 'owner') {
    if (state.currentPage === 'dashboard_barista' || state.currentPage === 'input_rekap') {
      state.currentPage = 'dashboard_owner';
    }
  }
  showToast(`Simulasi role diubah ke: ${newRole === 'owner' ? 'Owner (Andra)' : 'Barista (Dimas)'}`, 'info');
  renderApp();
}

// Mobile Sidebar Drawer Controls
function toggleSidebar() {
  state.sidebarOpen = !state.sidebarOpen;
  const sidebar = document.getElementById('app-sidebar');
  const backdrop = document.getElementById('sidebar-backdrop');
  if (sidebar && backdrop) {
    sidebar.classList.toggle('open', state.sidebarOpen);
    backdrop.classList.toggle('show', state.sidebarOpen);
  }
}

function closeSidebar() {
  state.sidebarOpen = false;
  const sidebar = document.getElementById('app-sidebar');
  const backdrop = document.getElementById('sidebar-backdrop');
  if (sidebar && backdrop) {
    sidebar.classList.remove('open');
    backdrop.classList.remove('show');
  }
}

// Notification Popover Controls
function toggleNotifPopover() {
  state.notifPopoverOpen = !state.notifPopoverOpen;
  const popover = document.getElementById('notif-popover');
  if (popover) {
    popover.classList.toggle('show', state.notifPopoverOpen);
  }
}

function closeNotifPopover() {
  state.notifPopoverOpen = false;
  const popover = document.getElementById('notif-popover');
  if (popover) {
    popover.classList.remove('show');
  }
}

// Toast Dispatcher
function showToast(message, type = 'success') {
  const container = document.getElementById('toast-container');
  if (!container) return;
  
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  
  let iconHtml = '<i data-lucide="check-circle-2" style="color: var(--success-green); width: 20px; height: 20px;"></i>';
  if (type === 'error') {
    iconHtml = '<i data-lucide="alert-octagon" style="color: var(--danger-red); width: 20px; height: 20px;"></i>';
  } else if (type === 'warning') {
    iconHtml = '<i data-lucide="alert-triangle" style="color: var(--warning-orange); width: 20px; height: 20px;"></i>';
  } else if (type === 'info') {
    iconHtml = '<i data-lucide="info" style="color: var(--primary-blue); width: 20px; height: 20px;"></i>';
  }

  toast.innerHTML = `
    ${iconHtml}
    <div style="flex: 1;">${message}</div>
    <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: var(--secondary-text); padding: 2px;">
      <i data-lucide="x" style="width: 14px; height: 14px;"></i>
    </button>
  `;
  container.appendChild(toast);
  
  if (window.lucide) {
    window.lucide.createIcons();
  }
  
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    toast.style.transition = 'all 200ms ease';
    setTimeout(() => toast.remove(), 200);
  }, 4000);
}

// Modal System
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('show');
    if (window.lucide) window.lucide.createIcons();
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('show');
  }
}

// Helper: Calculate Recipe Ingredient Consumption for Draft
function calculateDraftUsage() {
  let totals = {
    'Susu UHT Plain': { need: 0, unit: 'ml', available: 2400 },
    'Biji Kopi House Blend': { need: 0, unit: 'g', available: 4800 },
    'Sirup Butterscotch Monin': { need: 0, unit: 'ml', available: 180 },
    'Gula Aren Cair Organik': { need: 0, unit: 'ml', available: 3200 },
    'Air Mineral RO': { need: 0, unit: 'ml', available: 50000 },
    'Matcha Uji Premium Powder': { need: 0, unit: 'g', available: 950 }
  };

  state.currentDraftItems.forEach(item => {
    const menu = state.menus.find(m => m.id === item.menuId);
    if (menu) {
      menu.recipe.forEach(r => {
        if (!totals[r.name]) {
          totals[r.name] = { need: 0, unit: r.unit, available: r.stock };
        }
        totals[r.name].need += (r.dosage * item.qty);
      });
    }
  });

  return totals;
}

// Render Core Application View
function renderApp() {
  const root = document.getElementById('app-root');
  if (!root) return;

  // If on login page, render full screen login without sidebar/topbar
  if (state.currentPage === 'login') {
    root.innerHTML = renderLoginPage();
    if (window.lucide) window.lucide.createIcons();
    return;
  }

  // Shell Layout for logged-in screens
  root.innerHTML = `
    <div class="app-container">
      <!-- Backdrop for mobile drawer -->
      <div id="sidebar-backdrop" class="sidebar-backdrop" onclick="closeSidebar()"></div>

      <!-- Left Sidebar -->
      ${renderSidebar()}

      <!-- Main Content Area -->
      <div class="main-wrapper">
        <!-- Sticky Topbar -->
        ${renderTopbar()}

        <!-- Page Specific Content -->
        <main class="content-area">
          ${renderCurrentPage()}
        </main>
      </div>

      <!-- Quick Demo Navigator Floating Button (Allows jumping to any of the 17 pages) -->
      <div class="demo-floating-nav" onclick="openModal('modal-page-directory')" title="Lihat Daftar 17 Halaman">
        <i data-lucide="layers" style="width: 16px; height: 16px; color: #E8E1D6;"></i>
        <span>17 Halaman UI</span>
      </div>
    </div>

    <!-- Modals Container -->
    <div id="global-modals">
      ${renderAllModals()}
    </div>
  `;

  if (window.lucide) {
    window.lucide.createIcons();
  }
}

// Topbar Component
function renderTopbar() {
  const isOwner = state.currentRole === 'owner';
  const userName = isOwner ? 'Andra Pratama' : 'Dimas Kurnia';
  const roleName = isOwner ? 'Owner' : 'Barista';
  const userInitials = isOwner ? 'AP' : 'DK';

  return `
    <header class="topbar">
      <div class="topbar-left">
        <button class="btn-hamburger" onclick="toggleSidebar()" aria-label="Toggle Menu">
          <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
        </button>
        <span class="topbar-badge">
          <span style="width: 7px; height: 7px; border-radius: 50%; background: var(--success-green); display: inline-block;"></span>
          Sistem Persediaan
        </span>
        <div class="topbar-date" style="margin-left: 8px;">
          <i data-lucide="calendar" style="width: 14px; height: 14px; color: var(--secondary-text);"></i>
          <span>${getOperationalDateString()}</span>
        </div>
      </div>

      <div class="topbar-right">
        <!-- Interactive Role Switcher Widget -->
        <div class="role-switcher-box" title="Simulasi Pergantian Role">
          <button class="role-pill-btn ${isOwner ? 'active' : ''}" onclick="switchRole('owner')">
            <i data-lucide="crown" style="width: 13px; height: 13px;"></i>
            <span>Owner</span>
          </button>
          <button class="role-pill-btn ${!isOwner ? 'active' : ''}" onclick="switchRole('barista')">
            <i data-lucide="coffee" style="width: 13px; height: 13px;"></i>
            <span>Barista</span>
          </button>
        </div>

        <!-- Notification Bell with Popover -->
        <div class="notif-container">
          <button class="btn-notif" onclick="toggleNotifPopover()" title="Notifikasi Stok">
            <i data-lucide="bell" style="width: 18px; height: 18px;"></i>
            ${state.unreadNotifs > 0 ? '<span class="notif-indicator"></span>' : ''}
          </button>

          <!-- Popover Panel -->
          <div id="notif-popover" class="notif-popover">
            <div class="notif-header">
              <h4>Pemberitahuan Stok</h4>
              <span class="badge badge-warning">${state.unreadNotifs} Perlu Ditindak</span>
            </div>
            <div class="notif-list">
              <div class="notif-item" onclick="navigateTo('detail_bahan')">
                <div class="notif-icon-badge danger">
                  <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                </div>
                <div class="notif-content">
                  <p class="notif-title">Stok Susu UHT mulai menipis</p>
                  <p class="notif-meta">Tersisa 2.400 ml (Batas aman: 3.000 ml) &bull; 10 mnt lalu</p>
                </div>
              </div>

              <div class="notif-item" onclick="navigateTo('detail_bahan')">
                <div class="notif-icon-badge warning">
                  <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                </div>
                <div class="notif-content">
                  <p class="notif-title">Susu UHT mendekati kedaluwarsa</p>
                  <p class="notif-meta">Batch #SKD-09 kadaluarsa dalam 3 hari &bull; 25 mnt lalu</p>
                </div>
              </div>

              <div class="notif-item" onclick="navigateTo('request_pengadaan')">
                <div class="notif-icon-badge info">
                  <i data-lucide="file-plus" style="width: 16px; height: 16px;"></i>
                </div>
                <div class="notif-content">
                  <p class="notif-title">Request pengadaan baru</p>
                  <p class="notif-meta">Barista Dimas mengajukan 40L Susu UHT &bull; 1 jam lalu</p>
                </div>
              </div>

              <div class="notif-item" onclick="navigateTo('monitoring_stok')">
                <div class="notif-icon-badge warning">
                  <i data-lucide="alert-triangle" style="width: 16px; height: 16px;"></i>
                </div>
                <div class="notif-content">
                  <p class="notif-title">Sirup Butterscotch hampir habis</p>
                  <p class="notif-meta">Tersisa 180 ml di bar station &bull; 2 jam lalu</p>
                </div>
              </div>
            </div>
            <div style="padding: 10px 16px; text-align: center; background: #FAF8F5; border-top: 1px solid var(--border-color);">
              <a href="javascript:void(0)" onclick="navigateTo('monitoring_stok'); closeNotifPopover();" style="font-size: 12px; font-weight: 600; color: var(--primary-blue);">Lihat Seluruh Monitoring Persediaan &rarr;</a>
            </div>
          </div>
        </div>

        <!-- User Profile Widget -->
        <div class="user-profile-widget" onclick="openModal('modal-role-info')">
          <div class="user-profile-avatar">${userInitials}</div>
          <div class="user-profile-detail">
            <span class="user-profile-name">${userName}</span>
            <span class="user-profile-role">${roleName}</span>
          </div>
          <i data-lucide="chevron-down" style="width: 14px; height: 14px; color: var(--secondary-text); margin-left: 4px;"></i>
        </div>
      </div>
    </header>
  `;
}

// Sidebar Component
function renderSidebar() {
  const isOwner = state.currentRole === 'owner';
  const userName = isOwner ? 'Andra Pratama' : 'Dimas Kurnia';
  const roleName = isOwner ? 'Owner' : 'Barista';
  const userInitials = isOwner ? 'AP' : 'DK';

  return `
    <aside id="app-sidebar" class="sidebar">
      <!-- Brand Logo & Header -->
      <div class="sidebar-header">
        <div class="brand-wrapper">
          <img src="images/logo.png" alt="Kopi Kohlie Logo" style="width: 38px; height: 38px; border-radius: var(--radius-md); object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: var(--shadow-sm); flex-shrink: 0;">
          <div class="brand-text">
            <h1>KOPI KOHLIE</h1>
            <span>Stock & Supply</span>
          </div>
        </div>
        <button class="btn-sidebar-logout" onclick="closeSidebar()" style="display: none;" id="sidebar-close-btn">
          <i data-lucide="x" style="width: 18px; height: 18px;"></i>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="sidebar-nav">
        <!-- Beranda -->
        <div>
          <div class="nav-group-title">Beranda</div>
          <ul class="nav-list">
            <li class="nav-item ${state.currentPage === 'dashboard_owner' || state.currentPage === 'dashboard_barista' ? 'active' : ''}">
              <a onclick="navigateTo(state.currentRole === 'owner' ? 'dashboard_owner' : 'dashboard_barista')">
                <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                <span>Dashboard</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Rekap Pesanan -->
        <div>
          <div class="nav-group-title">Rekap Pesanan</div>
          <ul class="nav-list">
            ${isOwner ? `
              <li class="nav-item locked" title="Hanya untuk Barista operasional">
                <a onclick="showToast('Halaman Input Rekap hanya untuk Barista operasional kasir.', 'warning')">
                  <i data-lucide="clipboard-pen-line" style="width: 18px; height: 18px;"></i>
                  <span>Input Rekap</span>
                  <i data-lucide="lock" style="width: 13px; height: 13px; margin-left: auto;"></i>
                </a>
              </li>
            ` : `
              <li class="nav-item ${state.currentPage === 'input_rekap' ? 'active' : ''}">
                <a onclick="navigateTo('input_rekap')">
                  <i data-lucide="clipboard-pen-line" style="width: 18px; height: 18px;"></i>
                  <span>Input Rekap</span>
                  <span class="badge-pill" style="background: var(--warning-orange); color: #fff;">Shift Ini</span>
                </a>
              </li>
            `}

            <li class="nav-item ${state.currentPage === 'riwayat_rekap' || state.currentPage === 'detail_rekap' ? 'active' : ''}">
              <a onclick="navigateTo('riwayat_rekap')">
                <i data-lucide="receipt-text" style="width: 18px; height: 18px;"></i>
                <span>Riwayat Rekap</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Menu & Resep -->
        <div>
          <div class="nav-group-title">Menu & Resep</div>
          <ul class="nav-list">
            <li class="nav-item ${state.currentPage === 'daftar_menu' ? 'active' : ''}">
              <a onclick="navigateTo('daftar_menu')">
                <i data-lucide="book-open" style="width: 18px; height: 18px;"></i>
                <span>Daftar Menu</span>
              </a>
            </li>
            <li class="nav-item ${state.currentPage === 'detail_resep' ? 'active' : ''}">
              <a onclick="navigateTo('detail_resep')">
                <i data-lucide="flask-conical" style="width: 18px; height: 18px;"></i>
                <span>Komposisi Bahan</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Persediaan -->
        <div>
          <div class="nav-group-title">Persediaan</div>
          <ul class="nav-list">
            <li class="nav-item ${state.currentPage === 'monitoring_stok' || state.currentPage === 'detail_bahan' ? 'active' : ''}">
              <a onclick="navigateTo('monitoring_stok')">
                <i data-lucide="boxes" style="width: 18px; height: 18px;"></i>
                <span>Monitoring Stok</span>
                <span class="badge-pill" style="background: var(--danger-red); color: #fff;">8</span>
              </a>
            </li>
            <li class="nav-item ${state.currentPage === 'penerimaan_bahan' ? 'active' : ''}">
              <a onclick="navigateTo('penerimaan_bahan')">
                <i data-lucide="package-plus" style="width: 18px; height: 18px;"></i>
                <span>Penerimaan Bahan</span>
              </a>
            </li>
            <li class="nav-item ${state.currentPage === 'riwayat_stok' ? 'active' : ''}">
              <a onclick="navigateTo('riwayat_stok')">
                <i data-lucide="history" style="width: 18px; height: 18px;"></i>
                <span>Riwayat Stok</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Pengadaan -->
        <div>
          <div class="nav-group-title">Pengadaan</div>
          <ul class="nav-list">
            <li class="nav-item ${state.currentPage === 'request_pengadaan' ? 'active' : ''}">
              <a onclick="navigateTo('request_pengadaan')">
                <i data-lucide="shopping-cart" style="width: 18px; height: 18px;"></i>
                <span>Request Pengadaan</span>
                <span class="badge-pill" style="background: var(--warning-orange); color: #fff;">3</span>
              </a>
            </li>

            ${isOwner ? `
              <li class="nav-item ${state.currentPage === 'pengadaan_berjalan' ? 'active' : ''}">
                <a onclick="navigateTo('pengadaan_berjalan')">
                  <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
                  <span>Pengadaan Berjalan</span>
                </a>
              </li>
            ` : `
              <li class="nav-item locked" title="Khusus Owner">
                <a onclick="showToast('Pengadaan Berjalan hanya dapat diakses oleh Owner.', 'warning')">
                  <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
                  <span>Pengadaan Berjalan</span>
                  <i data-lucide="lock" style="width: 13px; height: 13px; margin-left: auto;"></i>
                </a>
              </li>
            `}

            <li class="nav-item ${state.currentPage === 'pembelian_darurat' ? 'active' : ''}">
              <a onclick="navigateTo('pembelian_darurat')">
                <i data-lucide="shield-alert" style="width: 18px; height: 18px;"></i>
                <span>Pembelian Darurat</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Lainnya (Owner Only) -->
        ${isOwner ? `
          <div>
            <div class="nav-group-title">Lainnya</div>
            <ul class="nav-list">
              <li class="nav-item ${state.currentPage === 'laporan' ? 'active' : ''}">
                <a onclick="navigateTo('laporan')">
                  <i data-lucide="bar-chart-3" style="width: 18px; height: 18px;"></i>
                  <span>Laporan</span>
                </a>
              </li>
              <li class="nav-item ${state.currentPage === 'pengaturan' ? 'active' : ''}">
                <a onclick="navigateTo('pengaturan')">
                  <i data-lucide="settings" style="width: 18px; height: 18px;"></i>
                  <span>Pengaturan</span>
                </a>
              </li>
            </ul>
          </div>
        ` : `
          <div>
            <div class="nav-group-title">Lainnya</div>
            <ul class="nav-list">
              <li class="nav-item locked" title="Khusus Owner">
                <a onclick="showToast('Laporan analitik hanya dapat diakses oleh Owner.', 'warning')">
                  <i data-lucide="bar-chart-3" style="width: 18px; height: 18px;"></i>
                  <span>Laporan</span>
                  <i data-lucide="lock" style="width: 13px; height: 13px; margin-left: auto;"></i>
                </a>
              </li>
              <li class="nav-item locked" title="Khusus Owner">
                <a onclick="showToast('Pengaturan sistem dan pengguna hanya untuk Owner.', 'warning')">
                  <i data-lucide="settings" style="width: 18px; height: 18px;"></i>
                  <span>Pengaturan</span>
                  <i data-lucide="lock" style="width: 13px; height: 13px; margin-left: auto;"></i>
                </a>
              </li>
            </ul>
          </div>
        `}
      </nav>

      <!-- Sidebar Footer (User Info & Logout) -->
      <div class="sidebar-footer">
        <div class="user-avatar-mini">${userInitials}</div>
        <div class="user-info-text">
          <div class="user-name-label">${userName}</div>
          <span class="user-role-badge ${isOwner ? 'owner' : 'barista'}">${roleName}</span>
        </div>
        <button class="btn-sidebar-logout" onclick="navigateTo('login')" title="Keluar">
          <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
        </button>
      </div>
    </aside>
  `;
}

// Router: Render Current Page Content
function renderCurrentPage() {
  switch (state.currentPage) {
    case 'dashboard_owner': return renderDashboardOwner();
    case 'dashboard_barista': return renderDashboardBarista();
    case 'input_rekap': return renderInputRekap();
    case 'riwayat_rekap': return renderRiwayatRekap();
    case 'detail_rekap': return renderDetailRekap();
    case 'daftar_menu': return renderDaftarMenu();
    case 'detail_resep': return renderDetailResep();
    case 'monitoring_stok': return renderMonitoringStok();
    case 'detail_bahan': return renderDetailBahan();
    case 'penerimaan_bahan': return renderPenerimaanBahan();
    case 'riwayat_stok': return renderRiwayatStok();
    case 'request_pengadaan': return renderRequestPengadaan();
    case 'pengadaan_berjalan': return renderPengadaanBerjalan();
    case 'pembelian_darurat': return renderPembelianDarurat();
    case 'laporan': return renderLaporan();
    case 'pengaturan': return renderPengaturan();
    default: return renderDashboardOwner();
  }
}
