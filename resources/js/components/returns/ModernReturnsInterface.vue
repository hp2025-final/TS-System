<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-bold text-gray-900">Returns & Exchanges</h1>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
              <span>Today:</span>
              <span class="font-medium">{{ todayReturns }} returns</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 gap-6">
        
        <!-- Main Panel - Return Processing -->
        <div>
          <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            
            <!-- Search Section -->
            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Process Return/Exchange</h2>
              
              <div class="space-y-4">
                <!-- Barcode Search -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Search by Barcode or Invoice Number
                  </label>
                  <div class="flex space-x-3">
                    <div class="flex-1">
                      <input
                        v-model="searchQuery"
                        @keyup.enter="searchItem"
                        type="text"
                        placeholder="Scan barcode or enter invoice number..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                        :disabled="isSearching"
                      />
                    </div>
                    <button
                      @click="searchItem"
                      :disabled="!searchQuery.trim() || isSearching"
                      class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span v-if="isSearching">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                      </span>
                      <span v-else>Search</span>
                    </button>
                  </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex space-x-2">
                  <button
                    @click="clearSearch"
                    class="px-3 py-2 text-sm text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200"
                  >
                    Clear
                  </button>
                  <button
                    @click="scanBarcode"
                    class="px-3 py-2 text-sm text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200 flex items-center space-x-1"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Scan Barcode</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Search Results -->
            <div v-if="searchResults.length > 0" class="p-6 border-b">
              <h3 class="text-md font-semibold text-gray-900 mb-4">Found Items</h3>
              <div class="space-y-3">
                <div
                  v-for="item in searchResults"
                  :key="item.id"
                  @click="selectItem(item)"
                  class="p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-colors"
                  :class="{ 'border-blue-500 bg-blue-50': selectedItem && selectedItem.id === item.id }"
                >
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <div class="flex items-center space-x-3 mb-2">
                        <h4 class="font-medium text-gray-900">{{ item.dress_name }}</h4>
                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                          {{ item.size }}
                        </span>
                        <span :class="getStatusColor(item.status)" class="px-2 py-1 text-xs font-medium rounded">
                          {{ item.status }}
                        </span>
                      </div>
                      
                      <p class="text-sm text-gray-600 mb-2">
                        {{ item.collection_name }} • {{ item.barcode }}
                      </p>
                      
                      <div class="flex items-center space-x-4 text-sm">
                        <span class="text-gray-500">Sold: {{ formatDate(item.sale_date) }}</span>
                        <span class="text-gray-500">Invoice: {{ item.invoice_number }}</span>
                      </div>
                    </div>
                    
                    <!-- Price Summary -->
                    <div class="text-right ml-4">
                      <p class="text-lg font-bold text-gray-900">Rs. {{ item.item_total }}</p>
                      <p class="text-sm text-gray-500">Amount Paid</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Selected Item Details -->
            <div v-if="selectedItem" class="p-6">
              <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Selected Item</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Item Info -->
                  <div>
                    <h4 class="font-medium text-gray-900 mb-3">{{ selectedItem.dress_name }}</h4>
                    
                    <div class="space-y-2 text-sm">
                      <div class="flex justify-between">
                        <span class="text-gray-600">Collection:</span>
                        <span class="font-medium">{{ selectedItem.collection_name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-600">Size:</span>
                        <span class="font-medium">{{ selectedItem.size }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-600">Barcode:</span>
                        <span class="font-medium">{{ selectedItem.barcode }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-600">SKU:</span>
                        <span class="font-medium">{{ selectedItem.sku }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Pricing Breakdown (Like POS) -->
                  <div>
                    <h5 class="font-medium text-gray-900 mb-3">Price Breakdown</h5>
                    
                    <div class="space-y-2 text-sm">
                      <div class="flex justify-between">
                        <span class="text-gray-600">Original Price:</span>
                        <span class="font-medium">Rs. {{ selectedItem.original_price }}</span>
                      </div>
                      <div class="flex justify-between text-blue-600">
                        <span>GST ({{ selectedItem.tax_percentage }}%):</span>
                        <span>Rs. {{ selectedItem.tax_amount }}</span>
                      </div>
                      <div v-if="selectedItem.total_discount_amount > 0" class="flex justify-between text-red-600">
                        <span>Total Discount:</span>
                        <span>-Rs. {{ selectedItem.total_discount_amount }}</span>
                      </div>
                      <div class="flex justify-between font-bold text-green-600 border-t pt-2">
                        <span>Amount Paid:</span>
                        <span>Rs. {{ selectedItem.item_total }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Return/Exchange Options -->
              <div class="space-y-6">
                <!-- Return Type Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Return Type
                  </label>
                  <div class="grid grid-cols-2 gap-4">
                    <button
                      @click="returnType = 'return'"
                      :class="returnType === 'return' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-700'"
                      class="p-4 border-2 rounded-lg text-center hover:border-blue-400 transition-colors"
                    >
                      <div class="mb-2 flex justify-center">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                      </div>
                      <div class="font-medium">Return for Refund</div>
                      <div class="text-sm opacity-75">Get money back</div>
                    </button>
                    
                    <button
                      @click="returnType = 'exchange'"
                      :class="returnType === 'exchange' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-700'"
                      class="p-4 border-2 rounded-lg text-center hover:border-blue-400 transition-colors"
                    >
                      <div class="mb-2 flex justify-center">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                      </div>
                      <div class="font-medium">Exchange Item</div>
                      <div class="text-sm opacity-75">Swap for another item</div>
                    </button>
                  </div>
                </div>

                <!-- Exchange Item Selection -->
                <div v-if="returnType === 'exchange'" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Search Exchange Item
                    </label>
                    <div class="flex space-x-3">
                      <input
                        v-model="exchangeQuery"
                        @keyup.enter="searchExchangeItem"
                        type="text"
                        placeholder="Scan barcode for exchange item..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                      <button
                        @click="searchExchangeItem"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                      >
                        Find
                      </button>
                    </div>
                  </div>

                  <!-- Selected Exchange Item -->
                  <div v-if="exchangeItem" class="p-4 border-2 border-green-300 bg-green-50 rounded-lg">
                    <div class="flex justify-between items-start">
                      <div>
                        <h5 class="font-medium text-gray-900">{{ exchangeItem.dress.name }}</h5>
                        <p class="text-sm text-gray-600">
                          {{ exchangeItem.dress.collection.name }} • Size: {{ exchangeItem.dress.size }}
                        </p>
                        <p class="text-sm font-medium text-green-600 mt-1">
                          Rs. {{ exchangeItem.final_price_with_tax }}
                        </p>
                      </div>
                      <button
                        @click="exchangeItem = null"
                        class="text-red-600 hover:text-red-800"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Price Difference -->
                  <div v-if="exchangeItem" class="p-4 bg-gray-50 rounded-lg">
                    <h5 class="font-medium text-gray-900 mb-2">Exchange Summary</h5>
                    <div class="space-y-1 text-sm">
                      <div class="flex justify-between">
                        <span>Return Amount:</span>
                        <span>Rs. {{ selectedItem.item_total }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span>Exchange Item:</span>
                        <span>Rs. {{ exchangeItem.final_price_with_tax }}</span>
                      </div>
                      <div class="flex justify-between font-bold border-t pt-1" :class="exchangeDifference >= 0 ? 'text-green-600' : 'text-red-600'">
                        <span>{{ exchangeDifference >= 0 ? 'Refund Due:' : 'Additional Payment:' }}</span>
                        <span>Rs. {{ Math.abs(exchangeDifference) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Return Reason -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Return Reason
                  </label>
                  <select
                    v-model="returnReason"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Select reason...</option>
                    <option value="defective">Defective Item</option>
                    <option value="wrong_size">Wrong Size</option>
                    <option value="wrong_item">Wrong Item</option>
                    <option value="customer_request">Customer Request</option>
                    <option value="quality_issue">Quality Issue</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <!-- Notes -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Notes (Optional)
                  </label>
                  <textarea
                    v-model="returnNotes"
                    rows="3"
                    placeholder="Additional notes about this return..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                  <button
                    @click="processReturn"
                    :disabled="!canProcessReturn"
                    class="flex-1 py-3 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span v-if="isProcessing">Processing...</span>
                    <span v-else>
                      {{ returnType === 'exchange' ? 'Process Exchange' : 'Process Return' }}
                    </span>
                  </button>
                  
                  <button
                    @click="generateThermalReturnSlip"
                    v-if="selectedItem"
                    class="px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center space-x-2"
                    title="Print Return Slip"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Print</span>
                  </button>
                  
                  <button
                    @click="clearForm"
                    class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">{{ successMessage.title }}</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">{{ successMessage.message }}</p>
          </div>
          <div class="items-center px-4 py-3">
            <button
              @click="closeSuccessModal"
              class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700"
            >
              OK
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50">
      <div class="flex">
        <div class="py-1">
          <svg class="fill-current h-4 w-4 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
          </svg>
        </div>
        <div>
          <p class="font-bold">Error</p>
          <p class="text-sm">{{ errorMessage }}</p>
        </div>
        <div class="pl-2">
          <button @click="clearError" class="text-red-500 hover:text-red-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { thermalReturnSlip } from '../../services/ThermalReturnSlipGenerator.js'

// Reactive state
const searchQuery = ref('')
const exchangeQuery = ref('')
const isSearching = ref(false)
const isProcessing = ref(false)
const searchResults = ref([])
const selectedItem = ref(null)
const exchangeItem = ref(null)
const returnType = ref('return')
const returnReason = ref('')
const returnNotes = ref('')
const errorMessage = ref('')
const showSuccessModal = ref(false)
const successMessage = ref({ title: '', message: '' })

// Stats
const todayReturns = ref(0)

// Computed properties
const exchangeDifference = computed(() => {
  if (!selectedItem.value || !exchangeItem.value) return 0
  return parseFloat(exchangeItem.value.final_price_with_tax) - parseFloat(selectedItem.value.item_total)
})

const canProcessReturn = computed(() => {
  return selectedItem.value && 
         returnReason.value && 
         (returnType.value === 'return' || (returnType.value === 'exchange' && exchangeItem.value)) &&
         !isProcessing.value
})

// Methods
const searchItem = async () => {
  if (!searchQuery.value.trim()) return
  
  isSearching.value = true
  clearError()
  
  try {
    const response = await window.axios.get(`/api/returns/search/${encodeURIComponent(searchQuery.value)}`)
    
    if (response.data && response.data.items) {
      searchResults.value = response.data.items
      if (searchResults.value.length === 1) {
        selectItem(searchResults.value[0])
      }
    } else {
      searchResults.value = []
      setError('No items found with this barcode or invoice number')
    }
  } catch (error) {
    console.error('Search error:', error)
    searchResults.value = []
    if (error.response?.status === 404) {
      setError('Item not found or not eligible for return')
    } else {
      setError('Error searching for item. Please try again.')
    }
  } finally {
    isSearching.value = false
  }
}

const selectItem = (item) => {
  selectedItem.value = item
  // Reset form when selecting new item
  returnType.value = 'return'
  returnReason.value = ''
  returnNotes.value = ''
  exchangeItem.value = null
  exchangeQuery.value = ''
}

const searchExchangeItem = async () => {
  if (!exchangeQuery.value.trim()) return
  
  clearError()
  
  try {
    const response = await window.axios.get(`/api/dress-items/barcode/${exchangeQuery.value}`)
    
    if (response.data && (response.data.status === 'available' || response.data.status === 'returned_resaleable')) {
      exchangeItem.value = response.data
      exchangeQuery.value = ''
    } else {
      setError('This item is not available for exchange')
    }
  } catch (error) {
    console.error('Exchange search error:', error)
    if (error.response?.status === 404) {
      setError('Exchange item not found')
    } else {
      setError('Error searching for exchange item')
    }
  }
}

const processReturn = async () => {
  if (!canProcessReturn.value) return
  
  isProcessing.value = true
  clearError()
  
  try {
    const returnData = {
      sale_item_id: selectedItem.value.sale_item_id,
      return_type: returnType.value,
      reason: returnReason.value,
      notes: returnNotes.value,
    }
    
    if (returnType.value === 'exchange' && exchangeItem.value) {
      returnData.exchange_item_id = exchangeItem.value.id
    }
    
    const response = await window.axios.post('/api/returns', returnData)
    
    if (response.data && response.data.success) {
      // Generate thermal return slip
      await generateThermalReturnSlip()
      
      showSuccessModal.value = true
      successMessage.value = {
        title: returnType.value === 'exchange' ? 'Exchange Processed' : 'Return Processed',
        message: `${returnType.value === 'exchange' ? 'Exchange' : 'Return'} has been successfully processed. Return slip has been sent to printer.`
      }
      
      // Refresh data
      clearForm()
    }
  } catch (error) {
    console.error('Process return error:', error)
    if (error.response?.data?.message) {
      setError(error.response.data.message)
    } else {
      setError('Error processing return. Please try again.')
    }
  } finally {
    isProcessing.value = false
  }
}

const generateThermalReturnSlip = async () => {
  try {
    const returnData = {
      selectedItem: selectedItem.value,
      exchangeItem: exchangeItem.value,
      returnType: returnType.value,
      returnReason: returnReason.value,
      returnNotes: returnNotes.value
    }
    
    await thermalReturnSlip.generateReturnSlip(returnData)
  } catch (error) {
    console.error('Failed to generate thermal return slip:', error)
    // Don't throw error, just log it since return was successful
  }
}

const clearForm = () => {
  searchQuery.value = ''
  exchangeQuery.value = ''
  searchResults.value = []
  selectedItem.value = null
  exchangeItem.value = null
  returnType.value = 'return'
  returnReason.value = ''
  returnNotes.value = ''
  clearError()
}

const clearSearch = () => {
  searchQuery.value = ''
  searchResults.value = []
  selectedItem.value = null
}

const scanBarcode = () => {
  // Implement barcode scanning if needed
  setError('Barcode scanning feature coming soon')
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
  successMessage.value = { title: '', message: '' }
}

const setError = (message) => {
  errorMessage.value = message
  setTimeout(() => {
    clearError()
  }, 5000)
}

const clearError = () => {
  errorMessage.value = ''
}

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: '2-digit'
  })
}

const getStatusColor = (status) => {
  switch (status) {
    case 'sold': return 'bg-green-100 text-green-800'
    case 'available': return 'bg-blue-100 text-blue-800'
    case 'returned': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// Lifecycle - removed unused methods
onMounted(() => {
  // Component mounted
})
</script>

<style scoped>
/* Custom styles for modern appearance */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
