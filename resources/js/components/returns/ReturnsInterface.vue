<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-stone-900">Returns & Exchanges</h1>
          <p class="text-stone-600 mt-1">Process customer returns and exchanges</p>
        </div>
        <div class="flex items-center space-x-2">
          <div class="bg-stone-100 px-3 py-1 rounded-full">
            <span class="text-stone-600 text-sm font-medium">{{ returnsList.length }} Returns Today</span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Return/Exchange Form -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Process Return/Exchange</h2>
        
        <!-- Item Search -->
        <div class="mb-4">
          <label for="item-barcode" class="block text-sm font-medium text-gray-700 mb-2">
            Search Item by Barcode
          </label>
          <div class="flex gap-2">
            <input
              type="text"
              id="item-barcode"
              v-model="searchBarcode"
              @keyup.enter="searchItem"
              class="flex-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Enter barcode to find sold item"
            />
            <button
              @click="searchItem"
              :disabled="!searchBarcode.trim() || isSearching"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ isSearching ? '...' : 'Search' }}
            </button>
          </div>
        </div>
        
        <!-- Found Item -->
        <div v-if="foundItem" class="mb-6 p-4 border rounded-lg bg-gray-50">
          <h3 class="text-sm font-medium text-gray-900 mb-2">Found Item</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Dress:</span>
              <span class="text-sm font-medium">{{ foundItem.dress_name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Collection:</span>
              <span class="text-sm font-medium">{{ foundItem.collection_name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Size:</span>
              <span class="text-sm font-medium">{{ foundItem.size }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Barcode:</span>
              <span class="text-sm font-medium">{{ foundItem.barcode }}</span>
            </div>
            
            <!-- Pricing Breakdown (like POS) -->
            <div class="border-t pt-2 mt-2">
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Original Price:</span>
                <span class="text-sm font-medium">Rs. {{ foundItem.original_price }}</span>
              </div>
              <div class="flex justify-between text-blue-600">
                <span class="text-sm">GST ({{ foundItem.tax_percentage }}%):</span>
                <span class="text-sm">Rs. {{ foundItem.tax_amount }}</span>
              </div>
              <div v-if="foundItem.total_discount_amount > 0" class="flex justify-between text-red-600">
                <span class="text-sm">Discount:</span>
                <span class="text-sm">-Rs. {{ foundItem.total_discount_amount }}</span>
              </div>
              <div class="flex justify-between font-bold text-green-600 border-t pt-1">
                <span class="text-sm">Amount Paid:</span>
                <span class="text-sm">Rs. {{ foundItem.item_total }}</span>
              </div>
            </div>
            
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Sale Date:</span>
              <span class="text-sm font-medium">{{ formatDate(foundItem.sale_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Invoice:</span>
              <span class="text-sm font-medium">{{ foundItem.invoice_number }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Status:</span>
              <span :class="foundItem.status === 'sold' ? 'text-green-600' : 'text-red-600'" class="text-sm font-medium">
                {{ foundItem.status }}
              </span>
            </div>
            <div v-if="foundItem.days_remaining" class="flex justify-between">
              <span class="text-sm text-gray-600">Return Period:</span>
              <span class="text-sm font-medium text-orange-600">{{ foundItem.days_remaining }} days left</span>
            </div>
          </div>
        </div>
        
        <!-- Return/Exchange Form -->
        <div v-if="foundItem && foundItem.status === 'sold'">
          <!-- Return Type -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Return Type</label>
            <div class="space-y-2">
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  value="return"
                  v-model="returnType"
                  class="form-radio h-4 w-4 text-indigo-600"
                />
                <span class="ml-2 text-sm text-gray-700">
                  <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                  </svg>
                  Refund (Return money)
                </span>
              </label>
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  value="exchange"
                  v-model="returnType"
                  class="form-radio h-4 w-4 text-indigo-600"
                />
                <span class="ml-2 text-sm text-gray-700">
                  <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  Exchange (Replace item)
                </span>
              </label>
            </div>
          </div>
          
          <!-- Reason -->
          <div class="mb-4">
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
            <select
              id="reason"
              v-model="reason"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">Select reason</option>
              <option value="defective">Defective/Damaged</option>
              <option value="wrong_size">Wrong Size</option>
              <option value="not_as_described">Not as Described</option>
              <option value="customer_changed_mind">Customer Changed Mind</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <!-- Notes -->
          <div class="mb-4">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea
              id="notes"
              v-model="notes"
              rows="3"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Additional notes about the return/exchange..."
            ></textarea>
          </div>
          
          <!-- Exchange Item Selection (if exchange) -->
          <div v-if="returnType === 'exchange'" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Exchange With</label>
            <div class="flex gap-2">
              <input
                type="text"
                v-model="exchangeBarcode"
                @keyup.enter="searchExchangeItem"
                class="flex-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Enter barcode of replacement item"
              />
              <button
                @click="searchExchangeItem"
                :disabled="!exchangeBarcode.trim()"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
              >
                Find
              </button>
            </div>
            
            <!-- Exchange Item Preview -->
            <div v-if="exchangeItem" class="mt-3 p-3 border rounded-lg bg-green-50">
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-sm font-medium">{{ exchangeItem.dress?.name }}</p>
                  <p class="text-xs text-gray-600">{{ exchangeItem.dress?.collection?.name }} - Size: {{ exchangeItem.dress?.size }}</p>
                  <p class="text-xs text-green-600">Rs. {{ exchangeItem.final_price_with_tax }}</p>
                  <p v-if="exchangeItem.discount_info" class="text-xs text-orange-600">{{ exchangeItem.discount_info }}</p>
                </div>
                <button
                  @click="exchangeItem = null"
                  class="text-red-600 hover:text-red-800"
                >
                  ✖️
                </button>
              </div>
            </div>
          </div>
          
          <!-- Refund Amount (if refund) -->
          <div v-if="returnType === 'return'" class="mb-4">
            <label for="refund-amount" class="block text-sm font-medium text-gray-700 mb-2">Refund Amount</label>
            <div class="relative">
              <span class="absolute left-3 top-2 text-gray-500">Rs.</span>
              <input
                type="number"
                id="refund-amount"
                v-model="refundAmount"
                :max="foundItem.item_total"
                class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="0.00"
              />
            </div>
            <p class="text-xs text-gray-500 mt-1">
              Maximum: Rs. {{ foundItem.item_total }} (Amount Actually Paid)
            </p>
          </div>
          
          <!-- Submit Button -->
          <button
            @click="processReturn"
            :disabled="!canSubmit || isProcessing"
            class="w-full bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            <span v-if="isProcessing">Processing...</span>
            <span v-else-if="returnType === 'refund'">
              <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
              </svg>
              Process Refund - PKR {{ refundAmount || 0 }}
            </span>
            <span v-else>
              <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Process Exchange
            </span>
          </button>
        </div>
        
        <!-- Item not eligible message -->
        <div v-if="foundItem && foundItem.status !== 'sold'" class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-yellow-700">
                This item is not eligible for return. Only sold items can be returned or exchanged.
              </p>
            </div>
          </div>
        </div>
        
        <!-- Error Message -->
        <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
          <p class="text-sm text-red-700">{{ errorMessage }}</p>
        </div>
      </div>
      
      <!-- Recent Returns -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-medium text-gray-900">Recent Returns</h2>
          <button
            @click="loadRecentReturns"
            class="text-sm text-indigo-600 hover:text-indigo-800"
          >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
          </button>
        </div>
        
        <div v-if="recentReturns.length === 0" class="text-gray-500 text-center py-8">
          <div class="text-4xl mb-2">
            <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <p>No returns yet</p>
        </div>
        
        <div v-else class="space-y-4 max-h-96 overflow-y-auto">
          <div
            v-for="returnItem in recentReturns"
            :key="returnItem.id"
            class="p-4 border rounded-lg hover:bg-gray-50"
          >
            <div class="flex justify-between items-start mb-2">
              <div>
                <h3 class="text-sm font-medium text-gray-900">Return #{{ returnItem.id }}</h3>
                <p class="text-xs text-gray-500">{{ formatDateTime(returnItem.created_at) }}</p>
              </div>
              <span :class="returnItem.return_type === 'refund' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ returnItem.return_type }}
              </span>
            </div>
            
            <div class="space-y-1 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Item:</span>
                <span>{{ returnItem.item_name || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Reason:</span>
                <span>{{ returnItem.reason }}</span>
              </div>
              <div v-if="returnItem.return_type === 'refund'" class="flex justify-between">
                <span class="text-gray-600">Refund:</span>
                <span class="font-medium">PKR {{ returnItem.refund_amount }}</span>
              </div>
              <div v-if="returnItem.notes" class="mt-2">
                <span class="text-gray-600 text-xs">Notes:</span>
                <p class="text-xs text-gray-800 mt-1">{{ returnItem.notes }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
      <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          
          <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2">
            {{ successData.type === 'refund' ? 'Refund Processed' : 'Exchange Completed' }}
          </h3>
          
          <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Return ID:</span>
                <span class="font-medium">#{{ successData.id }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Type:</span>
                <span class="font-medium">{{ successData.type }}</span>
              </div>
              <div v-if="successData.type === 'refund'" class="flex justify-between">
                <span class="text-gray-600">Refund Amount:</span>
                <span class="font-bold text-green-600">PKR {{ successData.amount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Reason:</span>
                <span class="font-medium">{{ successData.reason }}</span>
              </div>
            </div>
          </div>
          
          <div class="mt-6">
            <button
              @click="closeSuccessModal"
              class="w-full px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700"
            >
              ✅ Continue
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

// Reactive state
const searchBarcode = ref('');
const exchangeBarcode = ref('');
const foundItem = ref(null);
const exchangeItem = ref(null);
const returnType = ref('return');
const reason = ref('');
const notes = ref('');
const refundAmount = ref(0);
const isSearching = ref(false);
const isProcessing = ref(false);
const errorMessage = ref('');
const showSuccessModal = ref(false);
const successData = ref({});
const recentReturns = ref([]);

// Computed properties
const canSubmit = computed(() => {
  if (!foundItem.value || !reason.value) return false;
  
  if (returnType.value === 'return') {
    return refundAmount.value > 0 && refundAmount.value <= foundItem.value.item_total;
  }
  
  if (returnType.value === 'exchange') {
    return exchangeItem.value !== null;
  }
  
  return false;
});

// Search for item
const searchItem = async () => {
  if (!searchBarcode.value.trim()) return;
  
  isSearching.value = true;
  clearError();
  
  try {
    // Use the new returns API endpoint for sold items
    const response = await window.axios.get(`/api/returns/search-sold-item/${searchBarcode.value}`);
    
    if (response.data) {
      foundItem.value = response.data;
      
      // Set default refund amount to the actual amount paid (item_total)
      refundAmount.value = foundItem.value.item_total;
    } else {
      setError('Sold item not found');
      foundItem.value = null;
    }
  } catch (error) {
    console.error('Error searching for sold item:', error);
    if (error.response?.data?.message) {
      setError(error.response.data.message);
    } else {
      setError('Error occurred during search');
    }
    foundItem.value = null;
  } finally {
    isSearching.value = false;
  }
};

// Search for exchange item
const searchExchangeItem = async () => {
  if (!exchangeBarcode.value.trim()) return;
  
  clearError();
  
  try {
    const response = await window.axios.get(`/api/dress-items/barcode/${exchangeBarcode.value}`);
    
    if (response.data && (response.data.status === 'available' || response.data.status === 'returned_resaleable')) {
      exchangeItem.value = response.data;
      exchangeBarcode.value = '';
    } else if (response.data) {
      setError('This item is not available for exchange');
    } else {
      setError('Exchange item not found');
    }
  } catch (error) {
    console.error('Error searching for exchange item:', error);
    if (error.response && error.response.status === 404) {
      setError('Exchange item not found');
    } else {
      setError('Error searching for exchange item');
    }
  }
};

// Process return/exchange
const processReturn = async () => {
  if (!canSubmit.value) return;
  
  isProcessing.value = true;
  clearError();
  
  try {
    const returnData = {
      sale_item_id: foundItem.value.sale_item_id,
      dress_item_id: foundItem.value.dress_item_id,
      return_type: returnType.value,
      return_reason: reason.value,
      notes: notes.value
    };
    
    if (returnType.value === 'return') {
      returnData.refund_amount = refundAmount.value;
    } else if (returnType.value === 'exchange') {
      returnData.exchange_item_id = exchangeItem.value.id;
    }
    
    const response = await window.axios.post('/api/returns', returnData);
    
    if (response.data && response.data.return) {
      // Success
      successData.value = {
        id: response.data.return.id,
        type: returnType.value,
        amount: refundAmount.value,
        reason: reason.value
      };
      
      showSuccessModal.value = true;
      
      // Reset form
      resetForm();
      
      // Refresh recent returns
      await loadRecentReturns();
    } else {
      setError('Return processing failed. Please try again.');
    }
  } catch (error) {
    console.error('Error processing return:', error);
    
    if (error.response?.data?.message) {
      setError(error.response.data.message);
    } else if (error.response?.data?.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat();
      setError(errorMessages.join(', '));
    } else {
      setError('Error processing return. Please try again.');
    }
  } finally {
    isProcessing.value = false;
  }
};

// Reset form
const resetForm = () => {
  searchBarcode.value = '';
  exchangeBarcode.value = '';
  foundItem.value = null;
  exchangeItem.value = null;
  returnType.value = 'return';
  reason.value = '';
  notes.value = '';
  refundAmount.value = 0;
  clearError();
};

// Load recent returns
const loadRecentReturns = async () => {
  try {
    const response = await window.axios.get('/api/returns');
    
    if (response.data && response.data.returns) {
      recentReturns.value = response.data.returns.slice(0, 10); // Show latest 10
    }
  } catch (error) {
    console.error('Error loading recent returns:', error);
  }
};

// Close success modal
const closeSuccessModal = () => {
  showSuccessModal.value = false;
  successData.value = {};
};

// Error handling
const setError = (message) => {
  errorMessage.value = message;
  setTimeout(() => {
    clearError();
  }, 5000);
};

const clearError = () => {
  errorMessage.value = '';
};

// Helper functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: '2-digit'
  });
};

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  });
};

// Lifecycle
onMounted(() => {
  loadRecentReturns();
});
</script>
