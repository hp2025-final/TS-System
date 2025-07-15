<template>
  <div class="advanced-pos-container">
    <!-- Compact Barcode Search Section -->
    <div class="barcode-search-section">
      <div class="flex items-center gap-2">
        <div class="relative flex-1">
          <input 
            v-model="searchTerm" 
            @input="onBarcodeInput"
            type="text" 
            placeholder="Scan or enter barcode..."
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            ref="barcodeInput"
          />
          <div class="absolute right-2 top-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4" />
            </svg>
          </div>
        </div>
        <button 
          @click="startBarcodeScanner" 
          class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="pos-main grid grid-cols-1 lg:grid-cols-2 gap-4">
      <!-- Compact Dress Information Section -->
      <div class="dress-info-section">
        <div v-if="loading" class="flex justify-center items-center h-20">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
        </div>
        
        <div v-else-if="foundDressItem && foundDressItem.dress" class="dress-details bg-white p-4 rounded-lg shadow-sm">
          <!-- Compact Dress Information -->
          <div class="dress-info space-y-2">
            <div class="flex justify-between items-start">
              <h4 class="text-sm font-bold text-gray-800">{{ foundDressItem.dress.name }}</h4>
              <span class="text-xs font-medium px-2 py-1 bg-gray-100 rounded text-gray-700">{{ foundDressItem.dress.size }}</span>
            </div>
            
            <div class="grid grid-cols-2 gap-2 text-xs">
              <div class="info-item">
                <span class="text-gray-600">Collection:</span>
                <span class="text-blue-600 font-medium">{{ foundDressItem.dress.collection.name }}</span>
              </div>
              
              <div class="info-item">
                <span class="text-gray-600">SKU:</span>
                <span class="font-mono">{{ foundDressItem.dress.sku }}</span>
              </div>
              
              <div class="info-item">
                <span class="text-gray-600">Barcode:</span>
                <span class="font-mono">{{ foundDressItem.barcode }}</span>
              </div>
              
              <div class="info-item">
                <span class="text-gray-600">Status:</span>
                <span :class="getStatusColorText(foundDressItem.status)" class="font-medium">
                  {{ foundDressItem.status.toUpperCase() }}
                </span>
              </div>
            </div>
            
            <!-- Compact Pricing Information -->
            <div class="pricing-section bg-gray-50 p-2 rounded text-xs">
              <div class="flex justify-between items-center mb-1">
                <span class="text-gray-600">Actual Price:</span>
                <span class="font-semibold">Rs. {{ foundDressItem.dress.sale_price }}</span>
              </div>
              
              <div class="flex justify-between items-center mb-1">
                <span class="text-gray-600">GST ({{ foundDressItem.tax_percentage ?? foundDressItem.dress.tax_percentage ?? 18 }}%):</span>
                <span class="text-blue-600 font-medium">+Rs. {{ foundDressItem.tax_amount ?? ((foundDressItem.dress.sale_price * (foundDressItem.dress.tax_percentage ?? 18)) / 100).toFixed(2) }}</span>
              </div>
              
              <div v-if="getHighestDiscount(foundDressItem)" class="flex justify-between items-center mb-1">
                <span class="text-gray-600">Discount ({{ getDiscountLevel(foundDressItem) }}):</span>
                <span class="text-red-500 font-medium">{{ getHighestDiscount(foundDressItem) }}% (-Rs. {{ foundDressItem.total_discount }})</span>
              </div>
              
              <div class="flex justify-between items-center border-t pt-1">
                <span class="text-gray-800 font-bold">Final Total:</span>
                <span class="text-xl font-bold text-green-700">Rs. {{ foundDressItem.final_price_with_tax ?? ((foundDressItem.final_price ?? foundDressItem.dress.sale_price) + parseFloat(foundDressItem.tax_amount ?? ((foundDressItem.dress.sale_price * (foundDressItem.dress.tax_percentage ?? 18)) / 100))).toFixed(2) }}</span>
              </div>
            </div>
            
            <!-- Compact Add to Cart Button -->
            <div class="mt-3">
              <!-- Cart Message -->
              <div v-if="cartMessage" class="mb-2 p-2 bg-amber-100 border border-amber-300 rounded text-xs text-amber-800 text-center">
                {{ cartMessage }}
              </div>
              
              <button 
                v-if="foundDressItem.status === 'available' || foundDressItem.status === 'returned_resaleable'"
                @click="addToCart(foundDressItem)"
                class="w-full px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-sm font-medium flex items-center justify-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5L12 8m0 0l3-3m-3 3l-3-3" />
                </svg>
                Add to Cart
                <span v-if="foundDressItem.status === 'returned_resaleable'" class="text-xs bg-white bg-opacity-20 px-1 rounded">
                  (Returned Item)
                </span>
              </button>
              <button 
                v-else
                disabled
                class="w-full px-3 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed text-sm font-medium flex items-center justify-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636" />
                </svg>
                {{ foundDressItem.status === 'sold' ? 'Already Sold' : 'Not Available' }}
              </button>
            </div>
          </div>
        </div>
        
        <div v-else-if="searchAttempted" class="no-results bg-white p-4 rounded-lg shadow-sm text-center">
          <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h4 class="text-sm font-medium mb-1">No Item Found</h4>
          <p class="text-xs text-gray-600">Check the barcode and try again</p>
        </div>
        
        <div v-else class="search-prompt bg-white p-4 rounded-lg shadow-sm text-center">
          <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4" />
          </svg>
          <h4 class="text-sm font-medium mb-1">Ready to Scan</h4>
          <p class="text-xs text-gray-600">Enter barcode to search for items</p>
        </div>
      </div>

      <!-- Compact Cart Section -->
      <div class="cart-section bg-white p-4 rounded-lg shadow-sm">
        <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5L12 8m0 0l3-3m-3 3l-3-3" />
          </svg>
          Cart ({{ cart.length }})
        </h3>
        
        <!-- Cart Message -->
        <div v-if="cartMessage" class="cart-message mb-3 p-2 rounded text-xs font-medium" :class="{
          'bg-green-100 text-green-800 border border-green-300': cartMessageType === 'success',
          'bg-red-100 text-red-800 border border-red-300': cartMessageType === 'error',
          'bg-blue-100 text-blue-800 border border-blue-300': cartMessageType === 'info'
        }">
          {{ cartMessage }}
        </div>
        
        <!-- Compact Cart Items -->
        <div class="cart-items mb-4 max-h-40 overflow-y-auto">
          <div 
            v-for="item in cart" 
            :key="item.id"
            class="cart-item flex py-2 border-b text-xs relative"
          >
            <button 
              @click="removeFromCart(item)" 
              class="absolute top-1 right-1 text-red-500 hover:text-red-700 p-1"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
            <div class="flex-1 pr-8">
              <h4 class="font-medium">{{ item.dress.name }}</h4>
              <p class="text-gray-600">{{ item.dress.collection.name }} • {{ item.dress.size }}</p>
              <p class="text-gray-500 font-mono">{{ item.barcode }}</p>
              <div class="flex flex-col gap-1 mt-1">
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Actual Price:</span>
                  <span class="text-gray-800">Rs. {{ item.dress.sale_price }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-blue-600">GST ({{ item.dress.tax_percentage || 18 }}%):</span>
                  <span class="text-blue-600">+Rs. {{ ((item.dress.sale_price * (item.dress.tax_percentage || 18)) / 100).toFixed(2) }}</span>
                </div>
                <div v-if="item.total_discount > 0" class="flex items-center justify-between">
                  <span class="text-red-500">Discount ({{ getDiscountLevel(item) }}): {{ getHighestDiscount(item) }}%</span>
                  <span class="text-red-500">(-Rs. {{ item.total_discount }})</span>
                </div>
                <div class="flex items-center justify-between border-t pt-1">
                  <span class="font-medium text-gray-800">Final Total:</span>
                  <span class="font-semibold text-green-600">Rs. {{ (parseFloat(item.dress.sale_price) + ((item.dress.sale_price * (item.dress.tax_percentage || 18)) / 100) - parseFloat(item.total_discount || 0)).toFixed(2) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Compact Customer Info -->
        <div class="customer-info mb-4">
          <h4 class="text-xs font-medium mb-2">Customer Info</h4>
          <div class="grid grid-cols-2 gap-2">
            <input 
              v-model="customerInfo.name" 
              type="text" 
              placeholder="Name"
              class="px-2 py-1 border rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
            <input 
              v-model="customerInfo.phone" 
              type="tel" 
              placeholder="Phone"
              class="px-2 py-1 border rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
          </div>
        </div>

        <!-- Compact Payment Method -->
        <div class="payment-method mb-4">
          <h4 class="text-xs font-medium mb-2">Payment</h4>
          <div class="flex gap-2">
            <label class="flex items-center gap-1 text-xs">
              <input 
                v-model="paymentMethod" 
                type="radio" 
                value="cash"
                class="form-radio"
              />
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Cash
            </label>
            <label class="flex items-center gap-1 text-xs">
              <input 
                v-model="paymentMethod" 
                type="radio" 
                value="bank_transfer"
                class="form-radio"
              />
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
              </svg>
              Bank
            </label>
          </div>
        </div>

        <!-- Compact Total -->
        <div class="cart-total mb-4 text-xs">
          <div class="flex justify-between text-sm font-bold border-t pt-1">
            <span>Final Total:</span>
            <span>Rs. {{ total.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Compact Actions -->
        <div class="cart-actions flex gap-2">
          <button 
            @click="clearCart" 
            class="flex-1 px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors text-xs"
          >
            Clear
          </button>
          <button 
            @click="checkout" 
            :disabled="cart.length === 0"
            class="flex-1 px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors disabled:bg-gray-300 text-xs font-medium"
          >
            Checkout
          </button>
        </div>
      </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <div v-if="showScanner" class="scanner-modal fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-2">
      <div class="bg-white rounded-lg w-full max-w-lg mx-auto max-h-screen overflow-y-auto">
        <!-- Header -->
        <div class="p-4 border-b">
          <h3 class="text-lg font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Advanced Scanner
            <span v-if="isScanning" class="ml-auto text-green-600 text-sm flex items-center gap-1">
              <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              Active
            </span>
          </h3>
        </div>
        
        <!-- Scanner Area -->
        <div class="p-4">
          <div class="scanner-container relative bg-black rounded-lg overflow-hidden">
            <video ref="scannerVideo" class="w-full h-64 sm:h-80 object-cover" autoplay playsinline muted></video>
            <canvas ref="scannerCanvas" class="hidden"></canvas>
            
            <!-- Optimized scanning overlay for Code128 and QR codes -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div class="relative w-4/5 h-20 sm:h-24">
                <!-- Corner indicators optimized for linear barcodes -->
                <div class="absolute top-0 left-0 w-8 h-8 border-t-3 border-l-3 border-red-400"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-3 border-r-3 border-red-400"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-3 border-l-3 border-red-400"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-3 border-r-3 border-red-400"></div>
                
                <!-- Scanning line animation optimized for linear barcodes -->
                <div v-if="isScanning" class="absolute inset-0 overflow-hidden">
                  <div class="scan-line w-full h-0.5 bg-red-400 opacity-80"></div>
                </div>
                
                <!-- Center line guide for linear barcodes -->
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-full h-0.5 bg-red-300 opacity-40"></div>
                </div>
              </div>
            </div>
            
            <!-- Status indicators -->
            <div v-if="isScanning" class="absolute top-3 left-3 bg-green-500 text-white px-3 py-1 rounded-full text-sm flex items-center gap-2">
              <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
              Scanning...
            </div>
            
            <div class="absolute top-3 right-3 flex gap-2">
              <button 
                v-if="currentStream && currentStream.getVideoTracks()[0]?.getCapabilities?.()?.torch"
                @click="toggleTorch"
                :class="torchEnabled ? 'bg-yellow-500' : 'bg-gray-600'"
                class="text-white px-2 py-1 rounded-full text-xs flex items-center gap-1"
              >
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
            
            <!-- Instructions overlay -->
            <div class="absolute bottom-3 left-3 right-3 bg-black bg-opacity-70 text-white p-2 rounded text-center text-sm">
              <div class="font-medium">Scan any barcode or QR code</div>
            </div>
          </div>
        </div>
        
        <!-- Info Section -->
        <div class="px-4 pb-4">
          <!-- Action buttons -->
          <div class="w-full">
            <button 
              @click="stopBarcodeScanner" 
              class="w-full px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm font-medium"
            >
              ❌ Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { thermalInvoice } from '../../services/ThermalInvoiceGenerator.js'
import { 
  BrowserMultiFormatReader, 
  NotFoundException, 
  DecodeHintType, 
  BarcodeFormat,
  Result 
} from '@zxing/library'

export default {
  name: 'AdvancedPOS',
  setup() {
    // Reactive data
    const searchTerm = ref('')
    const cart = ref([])
    const customerInfo = ref({
      name: '',
      phone: ''
    })
    const paymentMethod = ref('cash')
    const showScanner = ref(false)
    const scannerVideo = ref(null)
    const scannerCanvas = ref(null)
    const barcodeInput = ref(null)
    const taxRate = ref(0)
    const loading = ref(false)
    const foundDressItem = ref(null)
    const searchAttempted = ref(false)
    const searchTimeout = ref(null)
    const cartMessage = ref('')
    const cartMessageType = ref('info')
    const cartMessageTimeout = ref(null)
    
    // API data
    const dressItems = ref([])
    const collections = ref([])
    const stream = ref(null)
    
    // ZXing barcode scanner
    const codeReader = ref(null)
    const isScanning = ref(false)
    const torchEnabled = ref(false)
    const currentStream = ref(null)
    
    // Computed properties
    const originalPriceSum = computed(() => {
      return cart.value.reduce((sum, item) => {
        const originalPrice = item.dress.sale_price
        return sum + parseFloat(originalPrice)
      }, 0)
    })

    const subtotal = computed(() => {
      return cart.value.reduce((sum, item) => {
        const price = item.final_price || item.dress.sale_price
        return sum + parseFloat(price)
      }, 0)
    })

    const totalDiscount = computed(() => {
      return cart.value.reduce((sum, item) => {
        return sum + parseFloat(item.total_discount || 0)
      }, 0)
    })

    const tax = computed(() => {
      return cart.value.reduce((sum, item) => {
        // GST should be calculated on original price (before discount)
        const originalPrice = item.dress.sale_price
        const taxPercentage = item.dress.tax_percentage || 18
        return sum + (originalPrice * taxPercentage / 100)
      }, 0)
    })

    const total = computed(() => {
      // Correct calculation: (Original Price + GST) - Discounts
      return originalPriceSum.value + tax.value - totalDiscount.value
    })

    // Auto-search functionality
    const onBarcodeInput = () => {
      // Clear previous timeout
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }
      
      const trimmedValue = searchTerm.value.trim()
      
      if (trimmedValue) {
        // Search immediately on input
        searchByBarcode()
        
        // Set timeout to reset search bar only after 3 seconds
        searchTimeout.value = setTimeout(() => {
          resetSearchBar()
        }, 3000)
      } else {
        // If search term is empty, clear results
        clearResults()
      }
    }
    
    const resetSearchBar = () => {
      // Only reset the search bar, keep results visible
      searchTerm.value = ''
      if (barcodeInput.value) {
        barcodeInput.value.focus()
      }
    }
    
    const clearResults = () => {
      // Clear results and search state
      foundDressItem.value = null
      searchAttempted.value = false
    }
    
    const resetSearch = () => {
      // Full reset - used when adding to cart
      searchTerm.value = ''
      foundDressItem.value = null
      searchAttempted.value = false
      if (barcodeInput.value) {
        barcodeInput.value.focus()
      }
    }

    // Methods
    const searchByBarcode = async () => {
      if (!searchTerm.value.trim()) {
        return
      }
      
      loading.value = true
      searchAttempted.value = true
      foundDressItem.value = null
      
      try {
        console.log('Searching for barcode:', searchTerm.value.trim())
        console.log('API URL:', `/api/test-barcode/${searchTerm.value.trim()}`)
        console.log('Full URL:', `${axios.defaults.baseURL}/api/test-barcode/${searchTerm.value.trim()}`)
        
        const response = await axios.get(`/api/test-barcode/${searchTerm.value.trim()}`)
        console.log('Search response:', response.data)
        console.log('Response status:', response.status)
        
        // Validate response is an object with dress property
        if (response.data && typeof response.data === 'object' && response.data.dress) {
          foundDressItem.value = response.data
          console.log('Item found and set:', foundDressItem.value)
        } else {
          console.error('Invalid response format:', response.data)
          console.log('Response data type:', typeof response.data)
          console.log('Has dress property:', !!response.data?.dress)
          foundDressItem.value = null
        }
        
      } catch (error) {
        console.error('Search error:', error)
        console.log('Error response:', error.response)
        if (error.response?.status === 404) {
          foundDressItem.value = null
          console.log('Item not found - showing no results')
        } else {
          console.error('Error searching by barcode:', error)
          alert('Error searching for item. Please check your connection.')
        }
      } finally {
        loading.value = false
      }
    }

    const addToCart = (item) => {
      // Check if item is already in cart
      const existingItem = cart.value.find(cartItem => cartItem.id === item.id)
      if (existingItem) {
        showCartMessage('This item is already in the cart')
        return
      }
      
      // Check if item is available
      if (!['available', 'returned_resaleable'].includes(item.status)) {
        showCartMessage(`This item is ${item.status} and cannot be added to cart`)
        return
      }
      
      cart.value.push(item)
      
      // Clear search and reset focus - smooth transition without notification
      resetSearch()
    }

    const showCartMessage = (message, type = 'info') => {
      // Clear any existing timeout
      if (cartMessageTimeout.value) {
        clearTimeout(cartMessageTimeout.value)
      }
      
      // Set the message and type
      cartMessage.value = message
      cartMessageType.value = type
      
      // Clear the message after 3 seconds
      cartMessageTimeout.value = setTimeout(() => {
        cartMessage.value = ''
        cartMessageType.value = 'info'
      }, 3000)
    }

    const removeFromCart = (item) => {
      const index = cart.value.findIndex(cartItem => cartItem.id === item.id)
      if (index > -1) {
        cart.value.splice(index, 1)
      }
    }

    const clearCart = () => {
      cart.value = []
    }

    const getStatusColor = (status) => {
      switch (status) {
        case 'available': return 'border-green-500'
        case 'sold': return 'border-red-500'
        case 'returned': return 'border-yellow-500'
        case 'damaged': return 'border-gray-500'
        default: return 'border-gray-300'
      }
    }

    const getStatusColorText = (status) => {
      switch (status) {
        case 'available': return 'text-green-600'
        case 'sold': return 'text-red-600'
        case 'returned': return 'text-yellow-600'
        case 'damaged': return 'text-gray-600'
        default: return 'text-gray-600'
      }
    }

    const startBarcodeScanner = async () => {
      showScanner.value = true
      isScanning.value = true
      
      try {
        // Initialize ZXing code reader with optimized settings
        if (!codeReader.value) {
          codeReader.value = new BrowserMultiFormatReader()
          
          // Set decode hints for Code128 and QR codes only
          const hints = new Map()
          hints.set(DecodeHintType.POSSIBLE_FORMATS, [
            BarcodeFormat.QR_CODE,
            BarcodeFormat.CODE_128
          ])
          hints.set(DecodeHintType.TRY_HARDER, true)
          hints.set(DecodeHintType.ALSO_INVERTED, true)
          
          codeReader.value.hints = hints
        }
        
        // Get available video devices
        const videoInputDevices = await codeReader.value.listVideoInputDevices()
        console.log('Available cameras:', videoInputDevices.length)
        
        // Prefer back camera for mobile devices with better resolution
        let selectedDeviceId = undefined
        if (videoInputDevices.length > 1) {
          const backCamera = videoInputDevices.find(device => 
            device.label.toLowerCase().includes('back') || 
            device.label.toLowerCase().includes('environment') ||
            device.label.toLowerCase().includes('rear')
          )
          if (backCamera) {
            selectedDeviceId = backCamera.deviceId
            console.log('Using back camera:', backCamera.label)
          }
        }
        
        // Configure video constraints optimized for Code128 barcodes and QR codes
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
        
        const constraints = {
          video: {
            deviceId: selectedDeviceId ? { exact: selectedDeviceId } : undefined,
            width: isMobile ? { ideal: 1920, max: 4096 } : { ideal: 1280, max: 1920 },
            height: isMobile ? { ideal: 1080, max: 2160 } : { ideal: 720, max: 1080 },
            facingMode: selectedDeviceId ? undefined : { ideal: 'environment' },
            focusMode: { ideal: 'continuous' },
            exposureMode: { ideal: 'continuous' },
            whiteBalanceMode: { ideal: 'continuous' },
            zoom: { ideal: 1.0 }, // No zoom for better barcode reading
            torch: false // Disable torch initially
          }
        }
        
        console.log('Using constraints:', constraints)
        
        // Start decoding with enhanced mobile settings
        const scanResult = await codeReader.value.decodeFromConstraints(
          constraints,
          scannerVideo.value,
          (result, err) => {
            if (result) {
              // Successfully scanned a barcode/QR code
              const scannedCode = result.getText()
              const format = result.getBarcodeFormat()
              console.log('Scanned code:', scannedCode, 'Format:', format)
              
              // Validate scanned code (optimized for Code128 and QR codes)
              if (scannedCode && scannedCode.trim() && scannedCode.length >= 1) {
                // Clean the scanned code
                let cleanCode = scannedCode.trim()
                
                // Set the cleaned code to search term
                searchTerm.value = cleanCode
                
                // Provide haptic feedback on mobile devices
                if (navigator.vibrate) {
                  navigator.vibrate([200, 100, 200]) // Stronger vibration pattern
                }
                
                // Stop scanning and close modal
                stopBarcodeScanner()
                
                // Trigger search
                searchByBarcode()
                
                // Show success message with format info
                const formatName = format ? format.toString().replace('_', '-') : 'Code'
                showCartMessage(`${formatName} scanned: ${cleanCode}`, 'success')
              }
              
            } else if (err && !(err instanceof NotFoundException)) {
              console.warn('Scanning error:', err)
            }
          }
        )
        
        // Store the stream for torch control
        if (scannerVideo.value && scannerVideo.value.srcObject) {
          currentStream.value = scannerVideo.value.srcObject
        }
        
        console.log('Scanner started successfully')
        
      } catch (err) {
        console.error('Error starting barcode scanner:', err)
        let errorMessage = 'Failed to start camera. '
        
        if (err.name === 'NotAllowedError') {
          errorMessage += 'Please allow camera access and try again.'
        } else if (err.name === 'NotFoundError') {
          errorMessage += 'No camera found on this device.'
        } else if (err.name === 'NotSupportedError') {
          errorMessage += 'Camera not supported in this browser.'
        } else if (err.name === 'NotReadableError') {
          errorMessage += 'Camera is being used by another application.'
        } else {
          errorMessage += 'Please ensure you\'re using HTTPS and have camera permissions.'
        }
        
        alert(errorMessage)
        showScanner.value = false
        isScanning.value = false
      }
    }

    const stopBarcodeScanner = () => {
      showScanner.value = false
      isScanning.value = false
      torchEnabled.value = false
      
      // Stop ZXing scanner
      if (codeReader.value) {
        codeReader.value.reset()
      }
      
      // Stop camera stream if exists
      if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop())
        stream.value = null
      }
      
      // Stop current stream
      if (currentStream.value) {
        currentStream.value.getTracks().forEach(track => track.stop())
        currentStream.value = null
      }
      
      // Clear video source
      if (scannerVideo.value) {
        scannerVideo.value.srcObject = null
      }
    }

    const toggleTorch = async () => {
      if (!currentStream.value) return
      
      const track = currentStream.value.getVideoTracks()[0]
      if (!track || !track.getCapabilities().torch) return
      
      try {
        torchEnabled.value = !torchEnabled.value
        await track.applyConstraints({
          advanced: [{ torch: torchEnabled.value }]
        })
        console.log('Torch toggled:', torchEnabled.value)
      } catch (err) {
        console.error('Failed to toggle torch:', err)
        torchEnabled.value = false
      }
    }

    const captureBarcode = () => {
      // Allow manual barcode entry as fallback
      const manualBarcode = prompt('Enter the barcode manually:')
      if (manualBarcode && manualBarcode.trim()) {
        searchTerm.value = manualBarcode.trim()
        stopBarcodeScanner()
        searchByBarcode()
        showCartMessage('Manual barcode entered successfully!', 'success')
      }
    }

    const checkout = async () => {
      if (cart.value.length === 0) {
        showCartMessage('Cart is empty!', 'error')
        return
      }

      try {
        const saleData = {
          items: cart.value.map(item => ({
            dress_item_id: item.id
          })),
          payment_method: paymentMethod.value,
          customer_name: customerInfo.value.name || null,
          customer_phone: customerInfo.value.phone || null,
          subtotal: subtotal.value,
          tax_amount: tax.value,
          total_amount: total.value
        }

        const response = await axios.post('/api/sales', saleData)

        if (response.data) {
          showCartMessage('Sale completed successfully!', 'success')
          
          // Generate thermal invoice using the separated service
          const invoiceData = {
            sale: response.data.sale,
            items: cart.value,
            customer: customerInfo.value,
            paymentMethod: paymentMethod.value,
            subtotal: subtotal.value,
            tax: tax.value,
            total: total.value,
            totalDiscount: totalDiscount.value
          }
          
          thermalInvoice.generateInvoice(invoiceData)
          
          clearCart()
          customerInfo.value = { name: '', phone: '' }
          paymentMethod.value = 'cash'
          resetSearch()
        }
      } catch (error) {
        console.error('Checkout error:', error)
        showCartMessage('Checkout failed. Please try again.', 'error')
      }
    }

    // Discount calculation functions
    const getHighestDiscount = (item) => {
      if (!item) return null
      
      // For items with discount_info (from API), extract the percentage
      if (item.discount_info) {
        const discountMatch = item.discount_info.match(/-?(\d+\.?\d*)%/)
        if (discountMatch) {
          return parseFloat(discountMatch[1])
        }
      }
      
      // For cart items without discount_info, calculate highest discount manually
      if (item.dress) {
        let highestDiscount = 0
        
        // Check collection discount
        if (item.dress.collection?.discount_active && item.dress.collection?.discount_percentage > 0) {
          highestDiscount = Math.max(highestDiscount, item.dress.collection.discount_percentage)
        }
        
        // Check dress discount
        if (item.dress.discount_active && item.dress.discount_percentage > 0) {
          highestDiscount = Math.max(highestDiscount, item.dress.discount_percentage)
        }
        
        // Check size discount (if available in cart item)
        if (item.size_discount_active && item.size_discount_percentage > 0) {
          highestDiscount = Math.max(highestDiscount, item.size_discount_percentage)
        }
        
        return highestDiscount > 0 ? highestDiscount : null
      }
      
      return null
    }
    
    const getDiscountLevel = (item) => {
      if (!item) return ''
      
      // For items with discount_info (from API), extract the source
      if (item.discount_info) {
        if (item.discount_info.includes('Size:')) {
          return 'Size'
        } else if (item.discount_info.includes('Style:')) {
          return 'Style'
        } else if (item.discount_info.includes('Collection:')) {
          return 'Collection'
        }
      }
      
      // For cart items without discount_info, determine highest discount source
      if (item.dress) {
        let highestDiscount = 0
        let discountSource = ''
        
        // Check collection discount
        if (item.dress.collection?.discount_active && item.dress.collection?.discount_percentage > 0) {
          if (item.dress.collection.discount_percentage > highestDiscount) {
            highestDiscount = item.dress.collection.discount_percentage
            discountSource = 'Collection'
          }
        }
        
        // Check dress discount
        if (item.dress.discount_active && item.dress.discount_percentage > 0) {
          if (item.dress.discount_percentage > highestDiscount) {
            highestDiscount = item.dress.discount_percentage
            discountSource = 'Style'
          }
        }
        
        // Check size discount
        if (item.size_discount_active && item.size_discount_percentage > 0) {
          if (item.size_discount_percentage > highestDiscount) {
            highestDiscount = item.size_discount_percentage
            discountSource = 'Size'
          }
        }
        
        return discountSource
      }
      
      return ''
    }

    // Initialize data on component mount
    onMounted(async () => {
      console.log('Compact POS System ready')
      
      // Setup axios defaults
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token
      }
      
      // Set base URL to Laravel backend
      axios.defaults.baseURL = window.location.origin
      console.log('Axios base URL set to:', axios.defaults.baseURL)
      
      // Auto-focus on barcode input
      if (barcodeInput.value) {
        barcodeInput.value.focus()
      }
    })

    return {
      searchTerm,
      cart,
      customerInfo,
      paymentMethod,
      showScanner,
      scannerVideo,
      scannerCanvas,
      barcodeInput,
      taxRate,
      loading,
      foundDressItem,
      searchAttempted,
      cartMessage,
      cartMessageType,
      dressItems,
      collections,
      originalPriceSum,
      subtotal,
      totalDiscount,
      tax,
      total,
      torchEnabled,
      currentStream,
      onBarcodeInput,
      resetSearchBar,
      clearResults,
      resetSearch,
      searchByBarcode,
      addToCart,
      showCartMessage,
      removeFromCart,
      clearCart,
      getStatusColor,
      getStatusColorText,
      startBarcodeScanner,
      stopBarcodeScanner,
      toggleTorch,
      captureBarcode,
      checkout,
      getHighestDiscount,
      getDiscountLevel
    }
  }
}
</script>

<style scoped>
.advanced-pos-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 16px;
}

.barcode-search-section {
  background: white;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 16px;
}

.pos-main {
  gap: 16px;
}

.dress-info-section {
  background: transparent;
}

.dress-details {
  border: 1px solid #e5e7eb;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.pricing-section {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
}

.search-prompt, .no-results {
  border: 2px dashed #d1d5db;
}

.cart-section {
  height: fit-content;
}

.cart-item {
  transition: background-color 0.2s;
}

.cart-item:hover {
  background-color: #f9fafb;
}

.scanner-modal {
  z-index: 1000;
}

.scanner-container {
  position: relative;
  overflow: hidden;
  border-radius: 8px;
}

/* Scanner animations */
@keyframes scan-line {
  0% { transform: translateY(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(400%); opacity: 0; }
}

.scan-line {
  animation: scan-line 2s ease-in-out infinite;
}

/* Mobile optimizations */
@media (max-width: 768px) {
  .scanner-modal {
    padding: 8px;
  }
  
  .scanner-container video {
    height: 280px !important;
  }
  
  /* Ensure proper aspect ratio on mobile */
  .scanner-container {
    aspect-ratio: 16/9;
  }
}

/* Enhanced border styles */
.border-t-3 { border-top-width: 3px; }
.border-l-3 { border-left-width: 3px; }
.border-r-3 { border-right-width: 3px; }
.border-b-3 { border-bottom-width: 3px; }

/* Pulse animation for active status */
@keyframes pulse-grow {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.1); opacity: 0.8; }
  100% { transform: scale(1); opacity: 1; }
}

@media (max-width: 768px) {
  .pos-main {
    grid-template-columns: 1fr;
  }
  
  .barcode-search-section .flex {
    flex-direction: row;
    gap: 8px;
  }
  
  .advanced-pos-container {
    padding: 12px;
  }
}
</style>
