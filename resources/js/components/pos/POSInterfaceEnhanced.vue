<template>
  <div class="min-h-screen bg-stone-50">
    <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div class="flex items-center mb-4 sm:mb-0">
          <svg class="w-8 h-8 mr-3 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          <h1 class="text-3xl font-bold text-stone-900">Point of Sale</h1>
        </div>
        <div class="hidden lg:block">
          <div class="bg-stone-100 rounded-lg px-4 py-2 text-sm text-stone-600">
            <span class="font-medium">Quick Keys:</span>
            <span class="mx-2">F1: Focus</span>
            <span class="mx-2">F2: Camera</span>
            <span class="mx-2">F3: Clear</span>
            <span class="mx-2">Ctrl+Enter: Checkout</span>
          </div>
        </div>
      </div>
      
      <!-- Mobile View Toggle Buttons -->
      <div class="lg:hidden mb-6">
        <div class="flex space-x-2">
          <button
            @click="mobileView = 'scanner'"
            :class="mobileView === 'scanner' ? 'bg-stone-600 text-white shadow-lg' : 'bg-white text-stone-700 border border-stone-300'"
            class="flex-1 py-3 px-4 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Scanner
          </button>
          <button
            @click="mobileView = 'cart'"
            :class="mobileView === 'cart' ? 'bg-stone-600 text-white shadow-lg' : 'bg-white text-stone-700 border border-stone-300'"
            class="flex-1 py-3 px-4 rounded-lg text-sm font-medium transition-all duration-200 relative flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M7 13v4a2 2 0 002 2h6a2 2 0 002-2v-4m-8 2a2 2 0 11-4 0 2 2 0 014 0zm0 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Cart
            <span v-if="cart.length > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cart.length }}
            </span>
          </button>
        </div>
      </div>
    
      <!-- Desktop Layout -->
      <div class="hidden lg:grid lg:grid-cols-3 gap-6">
        <!-- Product Scanner/Search -->
        <div class="lg:col-span-2">
          <div class="bg-white shadow-md rounded-xl p-6 border border-stone-200">
            <div class="flex items-center mb-6">
              <svg class="w-6 h-6 mr-3 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4m-4 0v4m-4-4h.01M8 16h.01"></path>
              </svg>
              <h2 class="text-xl font-semibold text-stone-900">Product Scanner</h2>
            </div>
            
            <!-- Barcode input -->
            <div class="mb-6">
              <label for="barcode" class="block text-sm font-medium text-stone-700 mb-2">
                Barcode Scanner
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4m-4 0v4m-4-4h.01M8 16h.01"></path>
                  </svg>
                </div>
                <input
                  type="text"
                  id="barcode"
                  ref="barcodeInputRef"
                  v-model="barcodeInput"
                  @keyup.enter="searchByBarcode"
                  @input="onBarcodeInput"
                  :disabled="isScanning"
                  class="block w-full pl-10 pr-20 py-3 border border-stone-300 rounded-lg shadow-sm focus:ring-2 focus:ring-stone-500 focus:border-stone-500 sm:text-sm bg-stone-50 focus:bg-white transition-colors"
                  placeholder="Scan or enter barcode (auto-search after 3+ characters)"
                  autofocus
                />
                <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <div class="flex items-center space-x-2">
                    <span class="text-stone-500 text-sm">Searching...</span>
                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-stone-300 border-t-stone-600"></div>
                  </div>
                </div>
              </div>
            </div>
          
            <!-- Camera Scanner -->
            <div class="mb-6">
              <BarcodeScanner
                @detected="onBarcodeDetected"
                @error="onScannerError"
                :continuous="true"
                ref="scannerRef"
              />
            </div>
            
            <!-- Quick Actions -->
            <div class="mb-6 flex gap-3 flex-wrap">
              <button
                @click="focusBarcode"
                class="inline-flex items-center px-4 py-2 border border-stone-300 text-sm font-medium rounded-lg text-stone-700 bg-white hover:bg-stone-50 transition-colors shadow-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Focus Input (F1)
              </button>
              
              <button
                @click="clearSearch"
                v-if="searchResults.length > 0"
                class="inline-flex items-center px-4 py-2 border border-stone-300 text-sm font-medium rounded-lg text-stone-700 bg-white hover:bg-stone-50 transition-colors shadow-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Clear Results
              </button>
              
              <button
                @click="showRecentSales = !showRecentSales"
                class="inline-flex items-center px-4 py-2 border border-stone-300 text-sm font-medium rounded-lg text-stone-700 bg-white hover:bg-stone-50 transition-colors shadow-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ showRecentSales ? 'Hide' : 'Show' }} Recent Sales
              </button>
            </div>

            <!-- Search results -->
            <div v-if="searchResults.length > 0" class="mb-6">
              <h3 class="text-lg font-medium text-stone-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Search Results
                <span class="ml-2 text-sm text-stone-500 bg-stone-100 px-2 py-1 rounded-full">{{ searchResults.length }} found</span>
              </h3>
              <div class="space-y-4">
                <div 
                  v-for="item in searchResults" 
                  :key="item.id"
                  class="border border-stone-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-200 bg-white"
                >
                  <div class="p-6">
                    <div class="flex justify-between items-start">
                      <div class="flex-1 min-w-0">
                        <!-- Product Name -->
                        <h4 class="text-xl font-bold text-stone-900 mb-2">{{ item.dress?.name }}</h4>
                        
                        <!-- Collection & SKU -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                          <div class="space-y-2">
                            <div class="flex items-center text-sm text-stone-600">
                              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v2a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2"></path>
                              </svg>
                              <span class="font-medium">Collection:</span>
                              <span class="ml-1">{{ item.dress?.collection?.name }}</span>
                            </div>
                            <div class="flex items-center text-sm text-stone-600">
                              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                              </svg>
                              <span class="font-medium">SKU:</span>
                              <span class="ml-1">{{ item.dress?.sku }}</span>
                            </div>
                          </div>
                          <div class="space-y-2">
                            <div class="flex items-center text-sm text-stone-600">
                              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                              </svg>
                              <span class="font-medium">Size:</span>
                              <span class="ml-1">{{ item.size }}</span>
                            </div>
                            <div class="flex items-center text-sm text-stone-600">
                              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4m-4 0v4m-4-4h.01M8 16h.01"></path>
                              </svg>
                              <span class="font-medium">Barcode:</span>
                              <span class="ml-1 font-mono text-xs">{{ item.barcode }}</span>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Description -->
                        <div v-if="item.dress?.description" class="mb-4">
                          <p class="text-sm text-stone-700 bg-stone-50 p-3 rounded-lg">{{ item.dress.description }}</p>
                        </div>
                        
                        <!-- Status -->
                        <div class="mb-4">
                          <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full" 
                                :class="getStatusClass(item.status)">
                            {{ getStatusText(item.status) }}
                          </span>
                        </div>
                        
                        <!-- Discount Info -->
                        <div v-if="item.discount_info" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                          <p class="text-sm text-green-800 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ item.discount_info }}
                          </p>
                        </div>
                      </div>
                      
                      <!-- Price & Action -->
                      <div class="ml-6 text-right">
                        <div class="bg-stone-50 p-4 rounded-lg">
                          <!-- Pricing -->
                          <div v-if="item.original_price !== item.final_price" class="space-y-1">
                            <p class="text-lg text-stone-400 line-through">PKR {{ item.original_price }}</p>
                            <p class="text-3xl font-bold text-green-600">PKR {{ item.final_price }}</p>
                            <p class="text-sm text-green-600 font-medium bg-green-100 px-2 py-1 rounded">
                              Save PKR {{ item.original_price - item.final_price }}
                            </p>
                          </div>
                          <div v-else>
                            <p class="text-3xl font-bold text-stone-900">PKR {{ item.final_price || item.dress?.sale_price }}</p>
                          </div>
                          
                          <!-- Add to Cart Button -->
                          <button
                            @click="addToCart(item)"
                            :disabled="item.status !== 'available'"
                            class="w-full mt-4 bg-stone-600 text-white px-6 py-3 rounded-lg hover:bg-stone-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors shadow-sm"
                          >
                            <span v-if="item.status === 'available'" class="flex items-center justify-center">
                              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0L12 18m0 0h7"></path>
                              </svg>
                              Add to Cart
                            </span>
                            <span v-else class="flex items-center justify-center">
                              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                              </svg>
                              Not Available
                            </span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Error message -->
            <div v-if="errorMessage" class="p-4 bg-red-50 border border-red-200 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-red-400 mr-3" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-red-700 font-medium">{{ errorMessage }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Cart -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow-md rounded-xl p-6 sticky top-6 border border-stone-200">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M7 13v4a2 2 0 002 2h6a2 2 0 002-2v-4m-8 2a2 2 0 11-4 0 2 2 0 014 0zm0 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-stone-900">Shopping Cart</h2>
              </div>
              <span class="text-sm text-stone-500 bg-stone-100 px-2 py-1 rounded-full">{{ cart.length }} items</span>
            </div>
            
            <div v-if="cart.length === 0" class="text-center py-12">
              <svg class="w-20 h-20 mx-auto mb-4 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M7 13v4a2 2 0 002 2h6a2 2 0 002-2v-4m-8 2a2 2 0 11-4 0 2 2 0 014 0zm0 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              <p class="font-medium text-stone-600 mb-2">Cart is empty</p>
              <p class="text-sm text-stone-400">Scan items to add them to your cart</p>
            </div>
            
            <div v-else>
              <!-- Cart items -->
              <div class="space-y-4 mb-6 max-h-80 overflow-y-auto">
                <div 
                  v-for="(item, index) in cart" 
                  :key="index"
                  class="flex justify-between items-start p-4 border border-stone-200 rounded-lg hover:bg-stone-50 transition-colors"
                >
                  <div class="flex-1 min-w-0 mr-4">
                    <p class="font-medium text-stone-900 truncate">{{ item.dress?.name }}</p>
                    <p class="text-sm text-stone-600">{{ item.dress?.collection?.name }}</p>
                    <p class="text-sm text-stone-600">Size: {{ item.size }}</p>
                    <p class="text-xs text-stone-400 font-mono">{{ item.barcode }}</p>
                    <div v-if="item.discount_info" class="text-xs text-green-600 mt-2 flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      {{ item.discount_info }}
                    </div>
                  </div>
                  <div class="text-right">
                    <div v-if="item.original_price !== item.final_price" class="space-y-1">
                      <p class="text-sm text-stone-400 line-through">PKR {{ item.original_price }}</p>
                      <p class="font-bold text-green-600">PKR {{ item.final_price }}</p>
                    </div>
                    <div v-else>
                      <p class="font-bold text-stone-900">PKR {{ item.final_price || item.dress?.sale_price }}</p>
                    </div>
                    <button 
                      @click="removeFromCart(index)"
                      class="text-red-600 text-sm hover:text-red-800 mt-2 bg-red-50 px-3 py-1 rounded-full transition-colors"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Cart summary -->
            <div class="border-t border-stone-200 pt-6 space-y-3">
              <div class="flex justify-between text-sm text-stone-600">
                <span>Subtotal ({{ cart.length }} items):</span>
                <span>PKR {{ cartSubtotal }}</span>
              </div>
              <div v-if="totalDiscount > 0" class="flex justify-between text-sm text-green-600 font-medium">
                <span>Total Discount:</span>
                <span>-PKR {{ totalDiscount }}</span>
              </div>
              <div class="flex justify-between text-xl font-bold border-t border-stone-200 pt-3">
                <span>Total:</span>
                <span class="text-stone-900">PKR {{ cartTotal }}</span>
              </div>
            </div>
            
            <!-- Payment method -->
            <div class="mt-6">
              <label class="block text-sm font-medium text-stone-700 mb-2">Payment Method</label>
              <div class="relative">
                <select
                  v-model="paymentMethod"
                  class="block w-full pl-3 pr-10 py-3 text-base border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-stone-500 bg-white"
                >
                  <option value="cash">Cash Payment</option>
                  <option value="bank_transfer">Bank Transfer</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                  <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </div>
              </div>
            </div>
            
            <!-- Action buttons -->
            <div class="mt-6 space-y-3">
              <!-- Checkout button -->
              <button 
                @click="checkout"
                :disabled="isProcessing || cart.length === 0"
                class="w-full bg-stone-600 text-white py-4 px-6 rounded-lg hover:bg-stone-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold text-lg transition-colors shadow-lg"
              >
                <span v-if="isProcessing" class="flex items-center justify-center">
                  <svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  Processing...
                </span>
                <span v-else class="flex items-center justify-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                  </svg>
                  Checkout
                </span>
              </button>
              
              <!-- Clear cart button -->
              <button 
                @click="clearCart"
                :disabled="isProcessing"
                class="w-full bg-stone-300 text-stone-700 py-3 px-6 rounded-lg hover:bg-stone-400 text-sm disabled:opacity-50 transition-colors font-medium"
              >
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Clear Cart (F3)
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import BarcodeScanner from './BarcodeScanner.vue';

// Reactive state
const barcodeInput = ref('');
const barcodeInputRef = ref(null);
const scannerRef = ref(null);
const searchResults = ref([]);
const cart = ref([]);
const paymentMethod = ref('cash');
const isProcessing = ref(false);
const isScanning = ref(false);
const isSearching = ref(false);
const errorMessage = ref('');
const showRecentSales = ref(false);
const mobileView = ref('scanner');

// Computed properties
const cartTotal = computed(() => {
  return cart.value.reduce((total, item) => {
    const price = parseFloat(item.final_price || item.dress?.sale_price || 0);
    return total + price;
  }, 0);
});

const cartSubtotal = computed(() => {
  return cart.value.reduce((total, item) => {
    const price = parseFloat(item.original_price || item.dress?.sale_price || 0);
    return total + price;
  }, 0);
});

const totalDiscount = computed(() => {
  return cartSubtotal.value - cartTotal.value;
});

// Methods
const focusBarcode = () => {
  nextTick(() => {
    if (barcodeInputRef.value) {
      barcodeInputRef.value.focus();
    }
  });
};

// Auto-search timeout
let searchTimeout = null;

const onBarcodeInput = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  
  // Auto-search after 500ms of no typing
  if (barcodeInput.value.trim().length >= 3) {
    searchTimeout = setTimeout(() => {
      searchByBarcode();
    }, 500);
  }
};

const searchByBarcode = async () => {
  if (!barcodeInput.value.trim()) return;
  
  errorMessage.value = '';
  isSearching.value = true;
  searchResults.value = []; // Clear previous results
  
  try {
    // Use test endpoint for barcode search (no auth required)
    const response = await window.axios.get(`/api/test-barcode/${barcodeInput.value}`);
    
    console.log('=== BARCODE SEARCH DEBUG ===');
    console.log('Searched barcode:', barcodeInput.value);
    console.log('Response data:', response.data);
    
    // The API returns data directly, not wrapped in a 'data' key
    const itemData = response.data;
    
    console.log('Item data:', itemData);
    console.log('Item data has id?', !!itemData?.id);
    console.log('Item data id:', itemData?.id);
    
    if (itemData && itemData.id) {
      console.log('Item found, checking if already in cart...');
      
      // Check if item is already in cart
      const existingItem = cart.value.find(item => item.id === itemData.id);
      if (existingItem) {
        console.log('Item already in cart');
        errorMessage.value = 'This item is already in the cart';
        barcodeInput.value = '';
        return;
      }
      
      console.log('Adding item to search results...');
      // The item data is already a plain object.
      // Vue will make it reactive when assigned to searchResults.
      searchResults.value = [itemData];
      console.log('Search results after assignment:', searchResults.value);
      
      barcodeInput.value = ''; // Clear input after successful search
    } else {
      console.log('Invalid response data structure or no id found');
      console.log('itemData:', itemData);
      errorMessage.value = 'Item not found or invalid data received';
    }
  } catch (error) {
    console.error('=== BARCODE SEARCH ERROR ===');
    console.error('Error searching by barcode:', error);
    console.error('Error response:', error.response?.data);
    console.error('Error status:', error.response?.status);
    if (error.response?.status === 404) {
      errorMessage.value = 'Item not found or not available';
    } else {
      errorMessage.value = 'Error searching for item. Please try again.';
    }
  } finally {
    isSearching.value = false;
  }
};

const onBarcodeDetected = (barcode) => {
  barcodeInput.value = barcode;
  searchByBarcode();
};

const onScannerError = (error) => {
  errorMessage.value = error;
};

const addToCart = (item) => {
  if (item.status !== 'available') {
    errorMessage.value = 'This item is not available for sale';
    return;
  }
  
  // Check if item is already in cart
  const existingItem = cart.value.find(cartItem => cartItem.id === item.id);
  if (existingItem) {
    errorMessage.value = 'This item is already in the cart';
    return;
  }
  
  cart.value.push(item);
  searchResults.value = [];
  errorMessage.value = '';
  
  // Auto-focus barcode input for next scan
  focusBarcode();
};

// Get status styling
const getStatusClass = (status) => {
  switch (status) {
    case 'available':
      return 'bg-green-100 text-green-800';
    case 'sold':
      return 'bg-red-100 text-red-800';
    case 'reserved':
      return 'bg-yellow-100 text-yellow-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

const getStatusText = (status) => {
  switch (status) {
    case 'available':
      return 'Available';
    case 'sold':
      return 'Sold';
    case 'reserved':
      return 'Reserved';
    default:
      return status;
  }
};

const removeFromCart = (index) => {
  cart.value.splice(index, 1);
};

const clearCart = () => {
  if (confirm('Are you sure you want to clear the entire cart?')) {
    cart.value = [];
  }
};

const clearSearch = () => {
  searchResults.value = [];
  barcodeInput.value = '';
  errorMessage.value = '';
  focusBarcode();
};

const checkout = async () => {
  // Checkout logic
};

// Lifecycle hooks
onMounted(() => {
  focusBarcode();
});

onUnmounted(() => {
  // Cleanup search timeout
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
});
</script>

<style scoped>
/* Custom scrollbar styles */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>
