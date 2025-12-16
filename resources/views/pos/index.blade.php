<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        /* Fix SweetAlert causing body to shrink */
        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
            overflow: hidden !important;
            padding-right: 0 !important;
            height: 100vh !important;
        }

        /* Mobile cart slide animation */
        .cart-slide {
            transition: transform 0.3s ease-in-out;
        }

        @media (max-width: 1023px) {
            .card-hover:hover {
                transform: none;
            }
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen lg:h-screen lg:overflow-hidden font-poppins">

    <div class="flex flex-col lg:flex-row h-full" x-data="posSystem()" x-init="init()">

        <!-- Mobile Cart Toggle Button -->
        <button @click="showCart = !showCart"
            class="lg:hidden fixed bottom-4 right-4 z-50 w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full shadow-2xl flex items-center justify-center text-white">
            <div class="relative">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <span x-show="cart.length > 0"
                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"
                    x-text="cart.length"></span>
            </div>
        </button>

        <!-- Menu Section -->
        <div class="w-full lg:w-2/3 p-4 md:p-6 lg:p-8 overflow-y-auto scrollbar-hide pb-24 lg:pb-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 lg:mb-8">
                <div>
                    <h1
                        class="text-2xl md:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        ☕ Cafe Menu
                    </h1>
                    <p class="text-gray-500 mt-1 text-sm md:text-base">Pilih menu favorit Anda</p>
                </div>
                <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto">
                    <a href="{{ route('orders.history') }}"
                        class="flex-1 sm:flex-none bg-white text-indigo-600 border-2 border-indigo-600 px-3 md:px-5 py-2 md:py-3 rounded-xl md:rounded-2xl hover:bg-indigo-50 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm md:text-base">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span class="hidden xs:inline">Riwayat</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="flex-1 sm:flex-none">
                        @csrf
                        <button type="submit"
                            class="w-full bg-white text-red-600 border-2 border-red-600 px-3 md:px-5 py-2 md:py-3 rounded-xl md:rounded-2xl hover:bg-red-50 font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm md:text-base">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                            </svg>
                            <span class="hidden xs:inline">Logout</span>
                        </button>
                    </form>

                    @if(auth()->user()?->role === 'admin')
                        <a href="{{ route('admin.products.index') }}"
                            class="flex-1 sm:flex-none bg-gradient-to-r from-slate-700 to-slate-900 text-white px-3 md:px-5 py-2 md:py-3 rounded-xl md:rounded-2xl hover:from-slate-800 hover:to-black font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm md:text-base">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="hidden xs:inline">Kelola</span>
                        </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div
                    class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-6 border border-emerald-200 flex items-center gap-3 shadow-sm">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search & Filter Section -->
            <div class="bg-white rounded-2xl md:rounded-3xl shadow-lg p-4 md:p-5 mb-4 md:mb-6 border border-gray-100">
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

                    <!-- Sort Direction - Hidden on mobile, combined with sort -->
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

            <!-- Product Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="product.stock > 0 ? addToCart(product) : null"
                        class="bg-white p-3 md:p-5 rounded-2xl md:rounded-3xl shadow-lg border border-gray-100 relative group card-hover cursor-pointer"
                        :class="{ 'opacity-60 cursor-not-allowed grayscale': product.stock == 0 }">

                        <template x-if="product.stock == 0">
                            <div
                                class="absolute inset-0 flex items-center justify-center z-10 rounded-2xl md:rounded-3xl bg-black/10">
                                <span
                                    class="bg-red-500 text-white px-2 md:px-4 py-1 md:py-2 rounded-full text-xs md:text-sm font-bold shadow-lg animate-pulse">
                                    HABIS
                                </span>
                            </div>
                        </template>

                        <!-- Category Badge -->
                        <div class="absolute top-2 md:top-4 right-2 md:right-4 z-5">
                            <span
                                class="px-2 md:px-3 py-0.5 md:py-1 rounded-full text-[10px] md:text-xs font-bold shadow-sm"
                                :class="{
                                    'bg-blue-100 text-blue-700': product.category == 'drink',
                                    'bg-orange-100 text-orange-700': product.category == 'food',
                                    'bg-purple-100 text-purple-700': product.category == 'snack'
                                }">
                                <span
                                    x-text="product.category == 'drink' ? '🥤' : (product.category == 'food' ? '🍽️' : '🍿')"></span>
                                <span class="hidden sm:inline"
                                    x-text="product.category.charAt(0).toUpperCase() + product.category.slice(1)"></span>
                            </span>
                        </div>

                        <!-- Product Image -->
                        <div
                            class="h-24 md:h-36 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl md:rounded-2xl mb-2 md:mb-4 flex items-center justify-center overflow-hidden">
                            <template x-if="product.image">
                                <img :src="'/storage/' + product.image"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </template>
                            <template x-if="!product.image">
                                <span class="text-3xl md:text-5xl opacity-50"
                                    x-text="product.category == 'drink' ? '☕' : (product.category == 'food' ? '🍔' : '🍿')"></span>
                            </template>
                        </div>

                        <!-- Product Info -->
                        <h3 class="font-bold text-sm md:text-lg text-gray-800 truncate mb-1 md:mb-2"
                            x-text="product.name"></h3>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">
                            <p
                                class="text-sm md:text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                Rp <span x-text="formatRupiah(product.price)"></span>
                            </p>
                            <span
                                class="text-[10px] md:text-xs font-semibold px-1.5 md:px-2 py-0.5 md:py-1 rounded-full w-fit"
                                :class="product.stock < 5 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'">
                                📦 <span x-text="product.stock"></span>
                            </span>
                        </div>

                        <!-- Add Button Overlay - Hidden on mobile, tap to add -->
                        <template x-if="product.stock > 0">
                            <div
                                class="hidden md:flex absolute inset-0 bg-gradient-to-t from-indigo-600/90 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 rounded-3xl items-end justify-center pb-6">
                                <span class="text-white font-bold flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah ke Keranjang
                                </span>
                            </div>
                        </template>

                        <!-- Mobile Add Indicator -->
                        <template x-if="product.stock > 0">
                            <div
                                class="md:hidden absolute bottom-2 right-2 w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v12m6-6H6"></path>
                                </svg>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Empty State -->
                <template x-if="filteredProducts.length === 0">
                    <div class="col-span-3 text-center py-16">
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
        </div>

        <!-- Cart Section -->
        <div x-show="showCart" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full lg:translate-x-0" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full lg:translate-x-0"
            @click.away="showCart = window.innerWidth >= 1024 ? true : false"
            class="fixed inset-0 z-50 lg:relative lg:z-auto w-full lg:w-1/3 glass-effect shadow-2xl flex flex-col border-l border-gray-200 bg-gray-50">

            <!-- Cart Header -->
            <div class="p-4 md:p-6 bg-gradient-to-r from-indigo-600 to-purple-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 md:w-12 md:h-12 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-white">Pesanan Anda</h2>
                            <p class="text-indigo-200 text-xs md:text-sm" x-text="cart.length + ' item'"></p>
                        </div>
                    </div>
                    <!-- Close button for mobile -->
                    <button @click="showCart = false"
                        class="lg:hidden w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 md:p-5 space-y-3 md:space-y-4 scrollbar-hide">
                <template x-if="cart.length === 0">
                    <div class="text-center py-12 md:py-16">
                        <div
                            class="w-20 h-20 md:w-24 md:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 md:w-12 md:h-12 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-400 font-medium">Keranjang Kosong</p>
                        <p class="text-gray-300 text-sm mt-1">Klik menu untuk menambahkan</p>
                    </div>
                </template>
                <template x-for="(item, index) in cart" :key="item.id">
                    <div
                        class="bg-white rounded-xl md:rounded-2xl p-3 md:p-4 shadow-sm border border-gray-100 flex justify-between items-center hover:shadow-md transition-shadow">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-800 text-sm md:text-base truncate" x-text="item.name"></h4>
                            <div class="text-xs md:text-sm text-indigo-600 font-semibold mt-1">
                                Rp <span x-text="formatRupiah(item.price * item.qty)"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 md:gap-2 ml-2">
                            <button @click="updateQty(index, -1)"
                                class="w-8 h-8 md:w-9 md:h-9 bg-gray-100 hover:bg-red-100 hover:text-red-600 rounded-lg md:rounded-xl font-bold transition-colors flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                    </path>
                                </svg>
                            </button>
                            <span class="font-bold w-6 md:w-8 text-center text-base md:text-lg"
                                x-text="item.qty"></span>
                            <button @click="updateQty(index, 1)"
                                class="w-8 h-8 md:w-9 md:h-9 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 rounded-lg md:rounded-xl font-bold transition-colors flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Cart Footer -->
            <div class="p-4 md:p-6 bg-white border-t border-gray-100">
                <div class="flex justify-between items-center mb-4 md:mb-5">
                    <span class="text-gray-500 font-medium text-sm md:text-base">Total Pembayaran</span>
                    <span
                        class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Rp <span x-text="formatRupiah(totalPrice)"></span>
                    </span>
                </div>
                <button @click="processCheckout()" :disabled="cart.length === 0"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-3 md:py-4 rounded-xl md:rounded-2xl font-bold text-base md:text-lg shadow-lg hover:shadow-xl transition-all duration-300 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2 md:gap-3">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    BAYAR SEKARANG
                </button>
            </div>
        </div>

        <!-- Mobile Floating Cart Button -->
        <button @click="showCart = true" x-show="!showCart" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
            class="lg:hidden fixed bottom-4 right-4 z-40 w-14 h-14 md:w-16 md:h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full shadow-2xl flex items-center justify-center text-white active:scale-95 transition-transform">
            <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            <!-- Cart Badge -->
            <span x-show="cart.length > 0"
                class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 rounded-full text-xs font-bold flex items-center justify-center shadow-lg"
                x-text="cart.length"></span>
        </button>

    </div>

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function posSystem() {
            return {
                // All products from database
                allProducts: @json($products),

                // Filter & Search state
                searchQuery: '',
                filterCategory: '',
                sortField: 'created_at',
                sortDirection: 'desc',

                // Mobile state
                showCart: window.innerWidth >= 1024,

                cart: [],
                receiptData: {
                    transactionId: '',
                    date: '',
                    time: '',
                    items: [],
                    subtotal: 0,
                    total: 0
                },

                // Initialize
                init() {
                    // Handle window resize
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) {
                            this.showCart = true;
                        }
                    });
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

                generateTransactionId() {
                    const now = new Date();
                    const dateStr = now.getFullYear().toString() +
                        (now.getMonth() + 1).toString().padStart(2, '0') +
                        now.getDate().toString().padStart(2, '0');
                    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                    return 'TRX-' + dateStr + '-' + random;
                },

                addToCart(product) {
                    const existingItem = this.cart.find(item => item.id === product.id);
                    const currentQtyInCart = existingItem ? existingItem.qty : 0;

                    if (currentQtyInCart >= product.stock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stok Tidak Cukup!',
                            text: 'Stok produk ini sudah mencapai batas maksimal',
                            confirmButtonColor: '#6366f1'
                        });
                        return;
                    }

                    if (existingItem) {
                        existingItem.qty++;
                    } else {
                        this.cart.push({ ...product, qty: 1 });
                    }

                    // Toast notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: product.name + ' ditambahkan'
                    });
                },

                updateQty(index, change) {
                    const item = this.cart[index];
                    if (change > 0 && item.qty >= item.stock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maksimal Stok!',
                            text: 'Jumlah pesanan sudah mencapai stok tersedia',
                            confirmButtonColor: '#6366f1'
                        });
                        return;
                    }

                    this.cart[index].qty += change;
                    if (this.cart[index].qty <= 0) {
                        this.cart.splice(index, 1);
                    }
                },

                get totalPrice() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                processCheckout() {
                    if (this.cart.length === 0) return;

                    const self = this;
                    const qrCodeSvg = `
                        <svg viewBox="0 0 200 200" style="width: 200px; height: 200px; margin: 0 auto;">
                            <rect fill="#000" x="10" y="10" width="40" height="40"/>
                            <rect fill="#fff" x="18" y="18" width="24" height="24"/>
                            <rect fill="#000" x="24" y="24" width="12" height="12"/>
                            <rect fill="#000" x="150" y="10" width="40" height="40"/>
                            <rect fill="#fff" x="158" y="18" width="24" height="24"/>
                            <rect fill="#000" x="164" y="24" width="12" height="12"/>
                            <rect fill="#000" x="10" y="150" width="40" height="40"/>
                            <rect fill="#fff" x="18" y="158" width="24" height="24"/>
                            <rect fill="#000" x="24" y="164" width="12" height="12"/>
                            <rect fill="#000" x="60" y="10" width="10" height="10"/>
                            <rect fill="#000" x="80" y="10" width="10" height="10"/>
                            <rect fill="#000" x="100" y="10" width="10" height="10"/>
                            <rect fill="#000" x="120" y="10" width="10" height="10"/>
                            <rect fill="#000" x="60" y="30" width="10" height="10"/>
                            <rect fill="#000" x="90" y="30" width="10" height="10"/>
                            <rect fill="#000" x="130" y="30" width="10" height="10"/>
                            <rect fill="#000" x="10" y="60" width="10" height="10"/>
                            <rect fill="#000" x="30" y="60" width="10" height="10"/>
                            <rect fill="#000" x="10" y="80" width="10" height="10"/>
                            <rect fill="#000" x="40" y="80" width="10" height="10"/>
                            <rect fill="#000" x="60" y="60" width="80" height="80" rx="8"/>
                            <rect fill="#fff" x="65" y="65" width="70" height="70" rx="6"/>
                            <text x="100" y="105" text-anchor="middle" font-size="14" font-weight="bold" fill="#6366f1">CAFE</text>
                            <rect fill="#000" x="150" y="60" width="10" height="10"/>
                            <rect fill="#000" x="170" y="60" width="10" height="10"/>
                            <rect fill="#000" x="180" y="80" width="10" height="10"/>
                            <rect fill="#000" x="160" y="100" width="10" height="10"/>
                            <rect fill="#000" x="60" y="150" width="10" height="10"/>
                            <rect fill="#000" x="80" y="160" width="10" height="10"/>
                            <rect fill="#000" x="100" y="150" width="10" height="10"/>
                            <rect fill="#000" x="150" y="150" width="10" height="10"/>
                            <rect fill="#000" x="170" y="160" width="10" height="10"/>
                            <rect fill="#000" x="150" y="180" width="10" height="10"/>
                            <rect fill="#000" x="180" y="180" width="10" height="10"/>
                        </svg>
                    `;

                    Swal.fire({
                        title: '<span style="font-size: 20px;">💳 Pembayaran QRIS</span>',
                        html: `
                            <div style="text-align: center;">
                                <div style="background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 100%); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                                    <p style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Total Pembayaran</p>
                                    <p style="font-size: 28px; font-weight: bold; background: linear-gradient(90deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                        Rp ${self.formatRupiah(self.totalPrice)}
                                    </p>
                                </div>
                                <div style="background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 15px; margin-bottom: 15px;">
                                    ${qrCodeSvg}
                                </div>
                                <p style="color: #6b7280; font-size: 13px;">Scan dengan e-wallet atau mobile banking</p>
                                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 10px;">
                                    <span style="background: #f3f4f6; padding: 5px 12px; border-radius: 20px; font-size: 12px;">GoPay</span>
                                    <span style="background: #f3f4f6; padding: 5px 12px; border-radius: 20px; font-size: 12px;">OVO</span>
                                    <span style="background: #f3f4f6; padding: 5px 12px; border-radius: 20px; font-size: 12px;">DANA</span>
                                    <span style="background: #f3f4f6; padding: 5px 12px; border-radius: 20px; font-size: 12px;">ShopeePay</span>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '✓ Konfirmasi Bayar',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        width: 420
                    }).then((result) => {
                        if (result.isConfirmed) {
                            self.confirmPayment();
                        }
                    });
                },

                confirmPayment() {
                    const self = this;

                    // Show loading
                    Swal.fire({
                        title: 'Memproses Pembayaran...',
                        html: '<div style="font-size: 14px; color: #6b7280;">Mohon tunggu sebentar</div>',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    axios.post('/checkout', {
                        cart: this.cart,
                        total_amount: this.totalPrice,
                        customer_name: 'Pelanggan'
                    })
                        .then(response => {
                            // Generate receipt data
                            const now = new Date();
                            self.receiptData = {
                                transactionId: self.generateTransactionId(),
                                date: now.toLocaleDateString('id-ID', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }),
                                time: now.toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                }),
                                items: [...self.cart],
                                subtotal: self.totalPrice,
                                total: self.totalPrice
                            };

                            // Show receipt
                            self.showReceipt();
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal!',
                                text: error.response?.data?.message || 'Terjadi kesalahan sistem',
                                confirmButtonColor: '#6366f1'
                            });
                        });
                },

                showReceipt() {
                    const self = this;
                    const itemsHtml = self.receiptData.items.map(item => `
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                            <div style="text-align: left;">
                                <strong>${item.name}</strong><br>
                                <small style="color: #666;">${item.qty} x Rp ${self.formatRupiah(item.price)}</small>
                            </div>
                            <div style="font-weight: 600;">Rp ${self.formatRupiah(item.price * item.qty)}</div>
                        </div>
                    `).join('');

                    Swal.fire({
                        title: '<span style="font-size: 20px;">🧾 Struk Digital</span>',
                        html: `
                            <div style="text-align: left; font-size: 14px; max-height: 400px; overflow-y: auto;">
                                <div style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; text-align: center;">
                                    <div style="font-size: 30px; margin-bottom: 5px;">☕</div>
                                    <div style="font-size: 20px; font-weight: bold;">CAFE POS</div>
                                    <div style="font-size: 12px; opacity: 0.8;">Jl. Contoh Alamat No. 123</div>
                                    <div style="font-size: 12px; opacity: 0.8;">Telp: (021) 123-4567</div>
                                </div>
                                
                                <div style="border-bottom: 2px dashed #e5e7eb; padding-bottom: 12px; margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="color: #6b7280;">No. Transaksi</span>
                                        <span style="font-weight: 600; font-family: monospace;">${self.receiptData.transactionId}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="color: #6b7280;">Tanggal</span>
                                        <span>${self.receiptData.date}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="color: #6b7280;">Waktu</span>
                                        <span>${self.receiptData.time}</span>
                                    </div>
                                </div>
                                
                                <div style="border-bottom: 2px dashed #e5e7eb; padding-bottom: 12px; margin-bottom: 12px;">
                                    ${itemsHtml}
                                </div>
                                
                                <div style="margin-bottom: 15px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #6b7280;">
                                        <span>Subtotal</span>
                                        <span>Rp ${self.formatRupiah(self.receiptData.subtotal)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #6b7280;">
                                        <span>Pajak (0%)</span>
                                        <span>Rp 0</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                                        <span>TOTAL</span>
                                        <span style="color: #6366f1;">Rp ${self.formatRupiah(self.receiptData.total)}</span>
                                    </div>
                                </div>
                                
                                <div style="background: #d1fae5; padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 15px;">
                                    <span style="color: #065f46; font-weight: 600;">✓ Pembayaran Berhasil via QRIS</span>
                                </div>
                                
                                <div style="text-align: center; color: #9ca3af; font-size: 12px;">
                                    <p>================================</p>
                                    <p style="font-weight: 500;">Terima Kasih Atas Kunjungan Anda!</p>
                                    <p>Simpan struk ini sebagai bukti pembayaran</p>
                                    <p>================================</p>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '🖨️ Cetak Struk',
                        cancelButtonText: '✓ Selesai',
                        confirmButtonColor: '#6366f1',
                        cancelButtonColor: '#10b981',
                        width: 420,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            self.printReceipt();
                        } else {
                            self.cart = [];
                            window.location.reload();
                        }
                    });
                },

                printReceipt() {
                    const self = this;
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Struk Pembayaran</title>
                            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                            <style>
                                * { margin: 0; padding: 0; box-sizing: border-box; }
                                body { 
                                    font-family: 'Poppins', sans-serif; 
                                    padding: 20px;
                                    max-width: 300px;
                                    margin: 0 auto;
                                }
                                .header {
                                    text-align: center;
                                    padding-bottom: 15px;
                                    border-bottom: 2px dashed #ccc;
                                    margin-bottom: 15px;
                                }
                                .header h1 { font-size: 24px; margin-bottom: 5px; }
                                .header p { font-size: 12px; color: #666; }
                                .info { margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px dashed #ccc; }
                                .info div { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
                                .items { margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px dashed #ccc; }
                                .item { margin-bottom: 10px; }
                                .item-name { font-weight: 600; }
                                .item-detail { display: flex; justify-content: space-between; font-size: 12px; color: #666; }
                                .total-section { margin-bottom: 15px; }
                                .total-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
                                .total-final { display: flex; justify-content: space-between; font-weight: bold; font-size: 16px; padding-top: 10px; border-top: 1px solid #ccc; }
                                .footer { text-align: center; font-size: 12px; color: #666; }
                                .success { background: #d1fae5; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px; }
                                .success p { color: #065f46; font-weight: 600; }
                                @media print { body { padding: 0; } }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>☕ CAFE POS</h1>
                                <p>Jl. Contoh Alamat No. 123</p>
                                <p>Telp: (021) 123-4567</p>
                            </div>
                            <div class="info">
                                <div><span>No. Transaksi</span><span>${self.receiptData.transactionId}</span></div>
                                <div><span>Tanggal</span><span>${self.receiptData.date}</span></div>
                                <div><span>Waktu</span><span>${self.receiptData.time}</span></div>
                            </div>
                            <div class="items">
                                ${self.receiptData.items.map(item => `
                                    <div class="item">
                                        <div class="item-name">${item.name}</div>
                                        <div class="item-detail">
                                            <span>${item.qty} x Rp ${self.formatRupiah(item.price)}</span>
                                            <span>Rp ${self.formatRupiah(item.price * item.qty)}</span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="total-section">
                                <div class="total-row"><span>Subtotal</span><span>Rp ${self.formatRupiah(self.receiptData.subtotal)}</span></div>
                                <div class="total-row"><span>Pajak (0%)</span><span>Rp 0</span></div>
                                <div class="total-final"><span>TOTAL</span><span>Rp ${self.formatRupiah(self.receiptData.total)}</span></div>
                            </div>
                            <div class="success">
                                <p>✓ Pembayaran Berhasil via QRIS</p>
                            </div>
                            <div class="footer">
                                <p>================================</p>
                                <p><strong>Terima Kasih Atas Kunjungan Anda!</strong></p>
                                <p>Simpan struk ini sebagai bukti pembayaran</p>
                                <p>================================</p>
                            </div>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => {
                        printWindow.print();
                        printWindow.close();
                        self.cart = [];
                        window.location.reload();
                    }, 250);
                }
            }
        }
    </script>

</body>

</html>