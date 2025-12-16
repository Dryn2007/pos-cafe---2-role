<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        .glass-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .table-row-hover {
            transition: all 0.2s ease;
        }

        .table-row-hover:hover {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        }

        /* Fix SweetAlert causing body to shrink */
        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
            overflow: auto !important;
            padding-right: 0 !important;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen p-4 md:p-8 font-poppins">

    <div class="max-w-7xl mx-auto" x-data="historyManager()">

        <!-- Header Card -->
        <div class="glass-card rounded-2xl md:rounded-3xl shadow-xl p-4 md:p-8 mb-4 md:mb-8 border border-white/50">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1
                        class="text-xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent flex items-center gap-2 md:gap-3">
                        <span
                            class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl md:rounded-2xl flex items-center justify-center text-white text-lg md:text-xl shadow-lg">
                            📋
                        </span>
                        Riwayat Transaksi
                    </h1>
                    <a href="{{ auth()->user()?->role === 'admin' ? route('admin.products.index') : route('pos.index') }}"
                        class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 mt-2 md:mt-3 font-medium transition-colors text-sm md:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
                <div class="flex items-start sm:items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                    <div class="text-left sm:text-right">
                        <p class="text-xs md:text-sm text-gray-500">Total Transaksi</p>
                        <p class="text-xl md:text-2xl font-bold text-indigo-600" x-text="filteredOrders.length"></p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-white text-red-600 border-2 border-red-600 px-3 md:px-5 py-2 md:py-3 rounded-xl md:rounded-2xl hover:bg-red-50 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm md:text-base">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="glass-card rounded-2xl md:rounded-3xl shadow-xl p-4 md:p-5 mb-4 md:mb-8 border border-white/50">
            <div class="flex flex-wrap gap-3 md:gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Cari</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Invoice atau pelanggan..."
                            class="w-full pl-10 pr-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="w-[calc(50%-0.375rem)] md:w-32">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Status</label>
                    <select x-model="filterStatus"
                        class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none bg-white text-sm md:text-base">
                        <option value="">Semua</option>
                        <option value="paid">✅ Lunas</option>
                        <option value="pending">⏳ Pending</option>
                    </select>
                </div>

                <!-- Date From -->
                <div class="w-[calc(50%-0.375rem)] md:w-36">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Dari</label>
                    <input type="date" x-model="dateFrom"
                        class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base">
                </div>

                <!-- Date To -->
                <div class="w-[calc(50%-0.375rem)] md:w-36">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Sampai</label>
                    <input type="date" x-model="dateTo"
                        class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base">
                </div>

                <!-- Sort By -->
                <div class="w-[calc(50%-0.375rem)] md:w-32">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Urutkan</label>
                    <select x-model="sortField"
                        class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none bg-white text-sm md:text-base">
                        <option value="created_at">Tanggal</option>
                        <option value="invoice_number">Invoice</option>
                        <option value="customer_name">Pelanggan</option>
                        <option value="total_price">Total</option>
                    </select>
                </div>

                <!-- Sort Direction - Hidden on mobile -->
                <div class="hidden md:block w-32">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Arah</label>
                    <select x-model="sortDirection"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none bg-white">
                        <option value="desc">↓ Menurun</option>
                        <option value="asc">↑ Menaik</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="flex gap-2">
                    <button type="button" @click="resetFilters()"
                        x-show="searchQuery || filterStatus || dateFrom || dateTo || sortField !== 'created_at' || sortDirection !== 'desc'"
                        class="bg-gray-200 text-gray-700 px-4 md:px-5 py-2.5 md:py-3 rounded-xl md:rounded-2xl font-semibold hover:bg-gray-300 transition-all duration-300 flex items-center gap-2 text-sm md:text-base">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="hidden sm:inline">Reset</span>
                    </button>
                </div>
            </div>

            <!-- Active Filters Info -->
            <div x-show="searchQuery || filterStatus || dateFrom || dateTo"
                class="mt-3 flex items-center gap-2 text-xs md:text-sm text-gray-500">
                <span>Menampilkan</span>
                <span class="font-bold text-indigo-600" x-text="filteredOrders.length"></span>
                <span>transaksi</span>
                <span x-show="searchQuery" class="hidden sm:inline">untuk "<span class="font-semibold"
                        x-text="searchQuery"></span>"</span>
            </div>
        </div>

        <!-- Orders Table Card - Desktop -->
        <div
            class="glass-card rounded-2xl md:rounded-3xl shadow-xl overflow-hidden border border-white/50 hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-gray-100">
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice
                            </th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal
                            </th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan
                            </th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total
                                Item</th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total
                                Harga</th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Metode
                            </th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"></th>
                            Status
                            </th>
                            <th class="p-5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="order in filteredOrders" :key="order.id">
                            <tr class="table-row-hover">
                                <td class="p-5">
                                    <span class="font-mono font-bold text-gray-800"
                                        x-text="order.invoice_number"></span>
                                </td>
                                <td class="p-5">
                                    <div>
                                        <p class="font-medium text-gray-800" x-text="formatDate(order.created_at)"></p>
                                        <p class="text-sm text-gray-500" x-text="formatTime(order.created_at) + ' WIB'">
                                        </p>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <span class="text-gray-800" x-text="order.customer_name"></span>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold"
                                        x-text="getTotalItems(order) + ' item'"></span>
                                </td>
                                <td class="p-5">
                                    <span class="font-bold text-gray-800">Rp <span
                                            x-text="formatRupiah(order.total_price)"></span></span>
                                </td>
                                <td class="p-5">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-full text-xs font-bold inline-flex items-center gap-1 w-fit"
                                            x-text="(order.payment_method || 'qris').toUpperCase()"></span>
                                        <template x-if="(order.payment_method || 'qris') === 'cash'">
                                            <div class="text-xs text-gray-600 leading-snug">
                                                <div>Tunai: <span class="font-semibold">Rp <span
                                                            x-text="formatRupiah(order.amount_paid || 0)"></span></span>
                                                </div>
                                                <div>Kembalian: <span class="font-semibold">Rp <span
                                                            x-text="formatRupiah(order.change_amount || 0)"></span></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="px-3 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-1"
                                        :class="order.status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                                        <template x-if="order.status == 'paid'">
                                            <span>✅ Lunas</span>
                                        </template>
                                        <template x-if="order.status != 'paid'">
                                            <span>⏳ Pending</span>
                                        </template>
                                    </span>
                                </td>
                                <td class="p-5 text-center">
                                    <button @click="showDetail(order.id)"
                                        class="p-2.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-xl transition-colors"
                                        title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State Desktop -->
            <template x-if="filteredOrders.length === 0">
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400 font-medium">Transaksi tidak ditemukan</p>
                    <p class="text-gray-300 text-sm mt-1">Coba kata kunci atau filter lain</p>
                </div>
            </template>
        </div>

        <!-- Orders Card Grid - Mobile -->
        <div class="md:hidden space-y-3">
            <template x-for="order in filteredOrders" :key="order.id">
                <div class="glass-card rounded-2xl shadow-lg p-4 border border-white/50">
                    <!-- Header Row -->
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span class="font-mono font-bold text-gray-800 text-sm"
                                x-text="order.invoice_number"></span>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-xs text-gray-500"
                                    x-text="formatDate(order.created_at) + ' • ' + formatTime(order.created_at)"></p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                            :class="order.status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                            x-text="order.status == 'paid' ? '✅ Lunas' : '⏳ Pending'">
                        </span>
                    </div>

                    <!-- Customer & Items -->
                    <div class="flex items-center justify-between text-sm mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600" x-text="order.customer_name"></span>
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-[10px] font-bold"
                                x-text="getTotalItems(order) + ' item'"></span>
                        </div>
                    </div>

                    <!-- Payment Method (Mobile) -->
                    <div class="flex items-start justify-between text-xs text-gray-600 mb-3">
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full text-[10px] font-bold"
                            x-text="(order.payment_method || 'qris').toUpperCase()"></span>
                        <template x-if="(order.payment_method || 'qris') === 'cash'">
                            <div class="text-right leading-snug">
                                <div>Tunai: <span class="font-semibold">Rp <span
                                            x-text="formatRupiah(order.amount_paid || 0)"></span></span></div>
                                <div>Kembalian: <span class="font-semibold">Rp <span
                                            x-text="formatRupiah(order.change_amount || 0)"></span></span></div>
                            </div>
                        </template>
                    </div>

                    <!-- Total & Action -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="font-bold text-indigo-600">Rp <span
                                x-text="formatRupiah(order.total_price)"></span></span>
                        <button @click="showDetail(order.id)"
                            class="px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            Detail
                        </button>
                    </div>
                </div>
            </template>

            <!-- Empty State Mobile -->
            <template x-if="filteredOrders.length === 0">
                <div class="glass-card rounded-2xl shadow-lg p-8 border border-white/50 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400 font-medium text-sm">Transaksi tidak ditemukan</p>
                    <p class="text-gray-300 text-xs mt-1">Coba kata kunci atau filter lain</p>
                </div>
            </template>
        </div>
    </div>

    <script>
        function historyManager() {
            return {
                // All orders from database
                allOrders: @json($orders->items()),

                // Filter & Search state
                searchQuery: '',
                filterStatus: '',
                dateFrom: '',
                dateTo: '',
                sortField: 'created_at',
                sortDirection: 'desc',

                // Computed filtered orders
                get filteredOrders() {
                    let orders = [...this.allOrders];

                    // Search filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        orders = orders.filter(o =>
                            o.invoice_number.toLowerCase().includes(query) ||
                            o.customer_name.toLowerCase().includes(query)
                        );
                    }

                    // Status filter
                    if (this.filterStatus) {
                        orders = orders.filter(o => o.status === this.filterStatus);
                    }

                    // Date from filter
                    if (this.dateFrom) {
                        orders = orders.filter(o => {
                            const orderDate = new Date(o.created_at).toISOString().split('T')[0];
                            return orderDate >= this.dateFrom;
                        });
                    }

                    // Date to filter
                    if (this.dateTo) {
                        orders = orders.filter(o => {
                            const orderDate = new Date(o.created_at).toISOString().split('T')[0];
                            return orderDate <= this.dateTo;
                        });
                    }

                    // Sorting
                    orders.sort((a, b) => {
                        let aVal = a[this.sortField];
                        let bVal = b[this.sortField];

                        // Handle string comparison
                        if (typeof aVal === 'string') {
                            aVal = aVal.toLowerCase();
                            bVal = bVal.toLowerCase();
                        }

                        if (this.sortDirection === 'asc') {
                            return aVal > bVal ? 1 : -1;
                        } else {
                            return aVal < bVal ? 1 : -1;
                        }
                    });

                    return orders;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.filterStatus = '';
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.sortField = 'created_at';
                    this.sortDirection = 'desc';
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                },

                formatTime(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                },

                getTotalItems(order) {
                    return order.items ? order.items.reduce((sum, item) => sum + item.quantity, 0) : 0;
                },

                showDetail(orderId) {
                    // Show loading
                    Swal.fire({
                        title: 'Memuat...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const basePath = window.location.pathname.startsWith('/admin') ? '/admin/history/' : '/history/';
                    axios.get(basePath + orderId)
                        .then(response => {
                            const order = response.data;
                            const items = order.items;

                            let itemsHtml = items.map(item => `
                                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                    <div>
                                        <strong>${item.product?.name || 'Produk Dihapus'}</strong>
                                        <br>
                                        <small style="color: #666;">${item.quantity} x Rp ${this.formatRupiah(item.price)}</small>
                                    </div>
                                    <div style="text-align: right; font-weight: 600;">
                                        Rp ${this.formatRupiah(item.quantity * item.price)}
                                    </div>
                                </div>
                            `).join('');

                            Swal.fire({
                                title: '<span style="font-size: 18px;">📋 Detail Transaksi</span>',
                                html: `
                                    <div style="text-align: left; font-size: 14px;">
                                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                                            <div style="font-size: 12px; opacity: 0.8;">Invoice</div>
                                            <div style="font-weight: bold; font-size: 16px;">${order.invoice_number}</div>
                                        </div>
                                        
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #666;">
                                            <span>Tanggal</span>
                                            <span>${new Date(order.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666;">
                                            <span>Waktu</span>
                                            <span>${new Date(order.created_at).toLocaleTimeString('id-ID')}</span>
                                        </div>
                                        
                                        <div style="border-top: 2px dashed #e0e0e0; padding-top: 15px; margin-bottom: 15px;">
                                            <strong style="color: #333;">Item Pesanan:</strong>
                                        </div>
                                        
                                        ${itemsHtml}
                                        
                                        <div style="border-top: 2px dashed #e0e0e0; margin-top: 15px; padding-top: 15px;">
                                            <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold;">
                                                <span>TOTAL</span>
                                                <span style="color: #6366f1;">Rp ${this.formatRupiah(order.total_price)}</span>
                                            </div>
                                        </div>

                                        <div style="border-top: 2px dashed #e0e0e0; margin-top: 12px; padding-top: 12px;">
                                            <div style="display:flex; justify-content: space-between; margin-bottom: 6px; color: #666;">
                                                <span>Metode</span>
                                                <span style="font-weight: 700; text-transform: uppercase;">${(order.payment_method || 'qris')}</span>
                                            </div>
                                            ${(order.payment_method || 'qris') === 'cash' ? `
                                                <div style="display:flex; justify-content: space-between; margin-bottom: 6px; color: #666;">
                                                    <span>Tunai</span>
                                                    <span style="font-weight: 600;">Rp ${this.formatRupiah(order.amount_paid || 0)}</span>
                                                </div>
                                                <div style="display:flex; justify-content: space-between; color: #666;">
                                                    <span>Kembalian</span>
                                                    <span style="font-weight: 700;">Rp ${this.formatRupiah(order.change_amount || 0)}</span>
                                                </div>
                                            ` : ''}
                                        </div>
                                        
                                        <div style="background: #d1fae5; padding: 10px; border-radius: 8px; margin-top: 15px; text-align: center;">
                                            <span style="color: #065f46; font-weight: 600;">✓ Pembayaran Berhasil</span>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#6366f1',
                                width: 450
                            });
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal memuat detail transaksi!',
                                confirmButtonColor: '#6366f1'
                            });
                        });
                }
            }
        }
    </script>

</body>

</html>