<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen p-4 md:p-8 font-poppins"
    x-data="productManager()">

    <div class="max-w-7xl mx-auto">

        <!-- Header Card -->
        <div class="glass-card rounded-2xl md:rounded-3xl shadow-xl p-4 md:p-8 mb-4 md:mb-8 border border-white/50">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1
                        class="text-xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent flex items-center gap-2 md:gap-3">
                        <span
                            class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl md:rounded-2xl flex items-center justify-center text-white text-lg md:text-xl shadow-lg">
                            📦
                        </span>
                        Manajemen Produk
                    </h1>
                    <a href="{{ route('admin.products.index') }}"
                        class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 mt-2 md:mt-3 font-medium transition-colors text-sm md:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
                <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.orders.history') }}"
                        class="flex-1 sm:flex-none bg-white text-indigo-600 border-2 border-indigo-600 px-3 md:px-5 py-2 md:py-3 rounded-xl md:rounded-2xl hover:bg-indigo-50 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm md:text-base">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span class="hidden sm:inline">Riwayat</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="flex-1 sm:flex-none">
                        @csrf
                        <button type="submit"
                            class="w-full bg-white text-red-600 border-2 border-red-600 px-3 md:px-5 py-2 md:py-3 rounded-xl md:rounded-2xl hover:bg-red-50 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm md:text-base">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                            </svg>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>

                    <button @click="openModal('create')"
                        class="flex-1 sm:flex-none bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-3 md:px-6 py-2 md:py-3 rounded-xl md:rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm md:text-base">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="hidden sm:inline">Tambah</span> Produk
                    </button>
                </div>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="glass-card rounded-2xl md:rounded-3xl shadow-xl p-4 md:p-5 mb-4 md:mb-8 border border-white/50">
            <div class="flex flex-wrap gap-3 md:gap-4 items-end">
                <!-- Search Input -->
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Cari Produk</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Ketik nama produk..."
                            class="w-full pl-10 pr-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="w-[calc(50%-0.375rem)] md:w-36">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Kategori</label>
                    <select x-model="filterCategory"
                        class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none bg-white text-sm md:text-base">
                        <option value="">Semua</option>
                        <option value="drink">🥤 Minuman</option>
                        <option value="food">🍽️ Makanan</option>
                        <option value="snack">🍿 Snack</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div class="w-[calc(50%-0.375rem)] md:w-36">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Urutkan</label>
                    <select x-model="sortField"
                        class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none bg-white text-sm md:text-base">
                        <option value="created_at">Terbaru</option>
                        <option value="name">Nama</option>
                        <option value="price">Harga</option>
                        <option value="stock">Stok</option>
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
                        x-show="searchQuery || filterCategory || sortField !== 'created_at' || sortDirection !== 'desc'"
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
            <div x-show="searchQuery || filterCategory"
                class="mt-3 flex items-center gap-2 text-xs md:text-sm text-gray-500">
                <span>Menampilkan</span>
                <span class="font-bold text-indigo-600" x-text="filteredProducts.length"></span>
                <span>produk</span>
                <span x-show="searchQuery" class="hidden sm:inline">untuk "<span class="font-semibold"
                        x-text="searchQuery"></span>"</span>
            </div>
        </div>

        <!-- Products Table Card - Desktop -->
        <div
            class="glass-card rounded-2xl md:rounded-3xl shadow-xl overflow-hidden border border-white/50 hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-gray-100">
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Gambar
                            </th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama
                                Produk</th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori
                            </th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga
                            </th>
                            <th class="p-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="p-5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <tr class="table-row-hover">
                                <td class="p-5">
                                    <template x-if="product.image">
                                        <img :src="'/storage/' + product.image"
                                            class="w-14 h-14 object-cover rounded-2xl shadow-sm border border-gray-100">
                                    </template>
                                    <template x-if="!product.image">
                                        <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center text-2xl"
                                            x-text="product.category == 'drink' ? '☕' : (product.category == 'food' ? '🍔' : '🍿')">
                                        </div>
                                    </template>
                                </td>
                                <td class="p-5">
                                    <span class="font-semibold text-gray-800" x-text="product.name"></span>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="px-3 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-1"
                                        :class="{
                                            'bg-blue-100 text-blue-700': product.category == 'drink',
                                            'bg-orange-100 text-orange-700': product.category == 'food',
                                            'bg-purple-100 text-purple-700': product.category == 'snack'
                                        }">
                                        <span
                                            x-text="product.category == 'drink' ? '🥤' : (product.category == 'food' ? '🍽️' : '🍿')"></span>
                                        <span
                                            x-text="product.category.charAt(0).toUpperCase() + product.category.slice(1)"></span>
                                    </span>
                                </td>
                                <td class="p-5">
                                    <span class="font-bold text-gray-800">Rp <span
                                            x-text="formatRupiah(product.price)"></span></span>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="px-3 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-1"
                                        :class="product.stock < 5 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'">
                                        📦 <span x-text="product.stock"></span>
                                        <span x-show="product.stock < 5" class="text-red-500">⚠️</span>
                                    </span>
                                </td>
                                <td class="p-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openModal('edit', product)"
                                            class="p-2.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-xl transition-colors"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>

                                        <button type="button" @click="confirmDelete(product.id)"
                                            class="p-2.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-xl transition-colors"
                                            title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State Desktop -->
            <template x-if="filteredProducts.length === 0">
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400 font-medium">Produk tidak ditemukan</p>
                    <p class="text-gray-300 text-sm mt-1">Coba kata kunci atau filter lain</p>
                </div>
            </template>
        </div>

        <!-- Products Card Grid - Mobile -->
        <div class="md:hidden space-y-3">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="glass-card rounded-2xl shadow-lg p-4 border border-white/50">
                    <div class="flex gap-3">
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            <template x-if="product.image">
                                <img :src="'/storage/' + product.image"
                                    class="w-16 h-16 object-cover rounded-xl shadow-sm border border-gray-100">
                            </template>
                            <template x-if="!product.image">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center text-2xl"
                                    x-text="product.category == 'drink' ? '☕' : (product.category == 'food' ? '🍔' : '🍿')">
                                </div>
                            </template>
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-gray-800 text-sm truncate" x-text="product.name"></h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0" :class="{
                                        'bg-blue-100 text-blue-700': product.category == 'drink',
                                        'bg-orange-100 text-orange-700': product.category == 'food',
                                        'bg-purple-100 text-purple-700': product.category == 'snack'
                                    }"
                                    x-text="product.category == 'drink' ? '🥤 Minuman' : (product.category == 'food' ? '🍽️ Makanan' : '🍿 Snack')">
                                </span>
                            </div>

                            <div class="flex items-center gap-3 mt-1">
                                <span class="font-bold text-indigo-600 text-sm">Rp <span
                                        x-text="formatRupiah(product.price)"></span></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="product.stock < 5 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'">
                                    📦 <span x-text="product.stock"></span>
                                    <span x-show="product.stock < 5">⚠️</span>
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 mt-3">
                                <button @click="openModal('edit', product)"
                                    class="flex-1 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg text-xs font-semibold flex items-center justify-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" @click="confirmDelete(product.id)"
                                    class="flex-1 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-semibold flex items-center justify-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State Mobile -->
            <template x-if="filteredProducts.length === 0">
                <div class="glass-card rounded-2xl shadow-lg p-8 border border-white/50 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400 font-medium text-sm">Produk tidak ditemukan</p>
                    <p class="text-gray-300 text-xs mt-1">Coba kata kunci atau filter lain</p>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        style="display: none;">
        <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white p-5 md:p-8 rounded-2xl md:rounded-3xl w-full max-w-[420px] shadow-2xl max-h-[90vh] overflow-y-auto">

            <div class="flex items-center gap-3 mb-4 md:mb-6">
                <div
                    class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl md:rounded-2xl flex items-center justify-center text-white text-lg md:text-xl shadow-lg">
                    <span x-text="mode === 'create' ? '➕' : '✏️'"></span>
                </div>
                <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"
                    x-text="mode === 'create' ? 'Tambah Produk' : 'Edit Produk'"></h2>
            </div>

            <form :action="formAction" method="POST" enctype="multipart/form-data">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-3 md:space-y-4">
                    <div>
                        <label class="text-xs md:text-sm text-gray-500 font-medium mb-1 block">Nama Produk</label>
                        <input type="text" x-model="form.name" name="name"
                            class="w-full border-2 border-gray-200 p-2.5 md:p-3 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base"
                            placeholder="ex: Es Teh Manis" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="text-xs md:text-sm text-gray-500 font-medium mb-1 block">Harga</label>
                            <input type="number" x-model="form.price" name="price"
                                class="w-full border-2 border-gray-200 p-2.5 md:p-3 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base"
                                placeholder="15000" required>
                        </div>
                        <div>
                            <label class="text-xs md:text-sm text-gray-500 font-medium mb-1 block">Stok</label>
                            <input type="number" x-model="form.stock" name="stock"
                                class="w-full border-2 border-gray-200 p-2.5 md:p-3 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base"
                                placeholder="100" required>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs md:text-sm text-gray-500 font-medium mb-1 block">Kategori</label>
                        <select x-model="form.category" name="category"
                            class="w-full border-2 border-gray-200 p-2.5 md:p-3 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none text-sm md:text-base">
                            <option value="drink">🥤 Minuman</option>
                            <option value="food">🍽️ Makanan</option>
                            <option value="snack">🍿 Snack</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs md:text-sm text-gray-500 font-medium mb-1 block">Gambar <span
                                class="text-gray-400">(Opsional)</span></label>
                        <input type="file" name="image"
                            class="w-full text-xs md:text-sm text-gray-500 file:mr-3 md:file:mr-4 file:py-2 md:file:py-2.5 file:px-3 md:file:px-4 file:rounded-xl file:border-0 file:text-xs md:file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                    </div>
                </div>

                <div class="mt-6 md:mt-8 flex flex-col-reverse sm:flex-row justify-end gap-2 md:gap-3">
                    <button type="button" @click="isOpen = false"
                        class="px-5 md:px-6 py-2.5 md:py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition-colors text-sm md:text-base text-center">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 md:px-6 py-2.5 md:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg flex items-center justify-center gap-2 text-sm md:text-base">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show success message with SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#6366f1',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        function productManager() {
            return {
                // All products from database
                allProducts: @json($products),

                // Filter & Search state
                searchQuery: '',
                filterCategory: '',
                sortField: 'created_at',
                sortDirection: 'desc',

                isOpen: false,
                mode: 'create',
                formAction: '',
                form: {
                    name: '',
                    price: '',
                    stock: '',
                    category: 'drink'
                },

                // Computed filtered products
                get filteredProducts() {
                    let products = [...this.allProducts];

                    // Search filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        products = products.filter(p => p.name.toLowerCase().includes(query));
                    }

                    // Category filter
                    if (this.filterCategory) {
                        products = products.filter(p => p.category === this.filterCategory);
                    }

                    // Sorting
                    products.sort((a, b) => {
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

                    return products;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.filterCategory = '';
                    this.sortField = 'created_at';
                    this.sortDirection = 'desc';
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                },

                confirmDelete(productId) {
                    Swal.fire({
                        title: 'Hapus Produk?',
                        text: 'Produk yang dihapus tidak dapat dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '🗑️ Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create and submit form
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/admin/products/' + productId;

                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = '{{ csrf_token() }}';
                            form.appendChild(csrfInput);

                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';
                            form.appendChild(methodInput);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                },

                openModal(mode, product = null) {
                    this.mode = mode;
                    this.isOpen = true;

                    if (mode === 'create') {
                        this.formAction = "{{ route('admin.products.store') }}";
                        this.form = { name: '', price: '', stock: '', category: 'drink' };
                    } else {
                        this.formAction = "/admin/products/" + product.id;
                        this.form = {
                            name: product.name,
                            price: product.price,
                            stock: product.stock,
                            category: product.category
                        };
                    }
                }
            }
        }
    </script>
</body>

</html>