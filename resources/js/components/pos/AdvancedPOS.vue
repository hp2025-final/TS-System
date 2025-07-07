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
              <span class="text-xs font-medium px-2 py-1 bg-gray-100 rounded text-gray-700">{{ foundDressItem.size }}</span>
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
              
              <div v-if="getHighestDiscount(foundDressItem)" class="flex justify-between items-center mb-1">
                <span class="text-gray-600">Discount ({{ getDiscountLevel(foundDressItem) }}):</span>
                <span class="text-red-500 font-medium">{{ getHighestDiscount(foundDressItem) }}% (-Rs. {{ foundDressItem.total_discount }})</span>
              </div>
              
              <div class="flex justify-between items-center border-t pt-1">
                <span class="text-gray-800 font-medium">Discounted Price:</span>
                <span class="text-lg font-bold text-green-600">Rs. {{ foundDressItem.final_price || foundDressItem.dress.sale_price }}</span>
              </div>
            </div>
            
            <!-- Compact Add to Cart Button -->
            <div class="mt-3">
              <!-- Cart Message -->
              <div v-if="cartMessage" class="mb-2 p-2 bg-amber-100 border border-amber-300 rounded text-xs text-amber-800 text-center">
                {{ cartMessage }}
              </div>
              
              <button 
                v-if="foundDressItem.status === 'available'"
                @click="addToCart(foundDressItem)"
                class="w-full px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-sm font-medium flex items-center justify-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5L12 8m0 0l3-3m-3 3l-3-3" />
                </svg>
                Add to Cart
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
              <p class="text-gray-600">{{ item.dress.collection.name }} • {{ item.size }}</p>
              <p class="text-gray-500 font-mono">{{ item.barcode }}</p>
              <div class="flex flex-col gap-1 mt-1">
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Actual Price:</span>
                  <span class="text-gray-800">Rs. {{ item.dress.sale_price }}</span>
                </div>
                <div v-if="item.total_discount > 0" class="flex items-center justify-between">
                  <span class="text-gray-600">Discount:</span>
                  <span class="text-red-500">-Rs. {{ item.total_discount }}</span>
                </div>
                <div class="flex items-center justify-between border-t pt-1">
                  <span class="font-medium text-gray-800">Discounted Price:</span>
                  <span class="font-semibold text-green-600">Rs. {{ item.final_price || item.dress.sale_price }}</span>
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
          <div class="flex justify-between mb-1">
            <span>Subtotal:</span>
            <span>Rs. {{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between mb-1">
            <span>Tax:</span>
            <span>Rs. {{ tax.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-sm font-bold border-t pt-1">
            <span>Total:</span>
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
    <div v-if="showScanner" class="scanner-modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-4 rounded-lg max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-3 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Barcode Scanner
        </h3>
        <div class="scanner-container">
          <video ref="scannerVideo" class="w-full h-48 bg-gray-100 rounded-lg" autoplay playsinline></video>
          <canvas ref="scannerCanvas" class="hidden"></canvas>
        </div>
        <div class="mt-4">
          <p class="text-sm text-gray-600 mb-3">Position barcode within camera view</p>
          <div class="flex gap-2">
            <button 
              @click="stopBarcodeScanner" 
              class="flex-1 px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors text-sm"
            >
              Cancel
            </button>
            <button 
              @click="captureBarcode" 
              class="flex-1 px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-sm"
            >
              Capture
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
    
    // Computed properties
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
        const price = item.final_price || item.dress.sale_price
        const taxPercentage = item.dress.tax_percentage || 0
        return sum + (price * taxPercentage / 100)
      }, 0)
    })

    const total = computed(() => {
      return subtotal.value + tax.value
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
        const response = await axios.get(`/api/test-barcode/${searchTerm.value.trim()}`)
        console.log('Search response:', response.data)
        
        // Validate response is an object with dress property
        if (response.data && typeof response.data === 'object' && response.data.dress) {
          foundDressItem.value = response.data
        } else {
          console.error('Invalid response format:', response.data)
          foundDressItem.value = null
        }
        
      } catch (error) {
        console.error('Search error:', error)
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
      if (item.status !== 'available') {
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
      
      try {
        const constraints = {
          video: {
            facingMode: { ideal: 'environment' },
            width: { ideal: 1280 },
            height: { ideal: 720 }
          }
        }
        
        stream.value = await navigator.mediaDevices.getUserMedia(constraints)
        
        if (scannerVideo.value) {
          scannerVideo.value.srcObject = stream.value
          scannerVideo.value.play()
        }
        
      } catch (err) {
        console.error('Error accessing camera:', err)
        alert('Camera access required for barcode scanning. Please allow camera permissions.')
        showScanner.value = false
      }
    }

    const stopBarcodeScanner = () => {
      showScanner.value = false
      if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop())
        stream.value = null
      }
    }

    const captureBarcode = () => {
      if (!scannerVideo.value || !scannerCanvas.value) return
      
      const canvas = scannerCanvas.value
      const video = scannerVideo.value
      const context = canvas.getContext('2d')
      
      canvas.width = video.videoWidth
      canvas.height = video.videoHeight
      
      context.drawImage(video, 0, 0, canvas.width, canvas.height)
      
      const manualBarcode = prompt('Enter the barcode manually:')
      if (manualBarcode) {
        searchTerm.value = manualBarcode
        stopBarcodeScanner()
        searchByBarcode()
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
      if (!item || !item.discount_info) return null
      
      // Parse the discount_info string (e.g., "Size: -25.00%")
      const discountMatch = item.discount_info.match(/-?(\d+\.?\d*)%/)
      if (discountMatch) {
        return parseFloat(discountMatch[1])
      }
      
      return null
    }
    
    const getDiscountLevel = (item) => {
      if (!item || !item.discount_info) return ''
      
      // Parse the discount_info string to get the level
      if (item.discount_info.includes('Size:')) {
        return 'Size'
      } else if (item.discount_info.includes('Style:')) {
        return 'Style'
      } else if (item.discount_info.includes('Collection:')) {
        return 'Collection'
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
      axios.defaults.baseURL = 'http://127.0.0.1:8000'
      
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
      subtotal,
      totalDiscount,
      tax,
      total,
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
