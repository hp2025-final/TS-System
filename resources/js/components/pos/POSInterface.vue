<template>
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Point of Sale</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Product Scanner/Search -->
      <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Scan/Search Products</h2>
          
          <!-- Barcode input -->
          <div class="mb-4">
            <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                type="text"
                id="barcode"
                ref="barcodeInputRef"
                v-model="barcodeInput"
                @keyup.enter="searchByBarcode"
                :disabled="isScanning"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Scan or enter barcode"
              />
              <div v-if="isScanning" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600"></div>
              </div>
            </div>
          </div>
          
          <!-- Camera scanner controls -->
          <div class="mb-4 flex gap-2">
            <button
              @click="toggleCameraScanning"
              :class="[
                'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white',
                isScanning ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700'
              ]"
            >
              <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ isScanning ? 'Stop Scanner' : 'Camera Scanner' }}
            </button>
            
            <button
              @click="focusBarcode"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              Focus Input
            </button>
          </div>
          
          <!-- Camera video element -->
          <div v-if="isScanning" class="mb-4">
            <video
              ref="videoElement"
              class="w-full max-w-md mx-auto border rounded-lg"
              autoplay
              playsinline
            ></video>
            <p class="text-sm text-gray-600 text-center mt-2">
              Position the barcode within the camera view
            </p>
          </div>
          
          <!-- Search results -->
          <div v-if="searchResults.length > 0" class="mt-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Search Results</h3>
            <div class="space-y-2">
              <div 
                v-for="item in searchResults" 
                :key="item.id"
                class="p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                @click="addToCart(item)"
              >
                <div class="flex justify-between">
                  <div>
                    <p class="font-medium">{{ item.dress?.name }}</p>
                    <p class="text-sm text-gray-500">{{ item.dress?.collection?.name }} - Size: {{ item.size }}</p>
                    <p class="text-sm text-gray-600">Barcode: {{ item.barcode }}</p>
                    <div v-if="item.discount_info" class="text-xs text-green-600 mt-1">
                      {{ item.discount_info }}
                    </div>
                  </div>
                  <div class="text-right">
                    <p v-if="item.original_price !== item.final_price" class="text-sm text-gray-400 line-through">
                      PKR {{ item.original_price }}
                    </p>
                    <p class="font-medium text-lg">PKR {{ item.final_price || item.dress?.sale_price }}</p>
                    <p class="text-sm" :class="item.status === 'available' ? 'text-green-600' : 'text-red-600'">
                      {{ item.status }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Error message -->
          <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
            <p class="text-sm text-red-700">{{ errorMessage }}</p>
          </div>
        </div>
      </div>
      
      <!-- Cart -->
      <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Cart ({{ cart.length }} items)</h2>
          
          <div v-if="cart.length === 0" class="text-gray-500 text-center py-8">
            <div class="text-4xl mb-2">
              <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.68 8.39a2 2 0 002.08 1.61h9.2a2 2 0 002.08-1.61L17 13M7 13v6a2 2 0 002 2h6a2 2 0 002-2v-6"/>
              </svg>
            </div>
            <p>Cart is empty</p>
            <p class="text-sm">Scan items to add them</p>
          </div>
          
          <div v-else>
            <!-- Cart items -->
            <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
              <div 
                v-for="(item, index) in cart" 
                :key="index"
                class="flex justify-between items-center p-3 border rounded-lg"
              >
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-sm truncate">{{ item.dress?.name }}</p>
                  <p class="text-xs text-gray-500">{{ item.dress?.collection?.name }}</p>
                  <p class="text-xs text-gray-500">Size: {{ item.size }}</p>
                  <div v-if="item.discount_info" class="text-xs text-green-600">
                    {{ item.discount_info }}
                  </div>
                </div>
                <div class="text-right ml-2">
                  <p v-if="item.original_price !== item.final_price" class="text-xs text-gray-400 line-through">
                    PKR {{ item.original_price }}
                  </p>
                  <p class="font-medium">PKR {{ item.final_price || item.dress?.sale_price }}</p>
                  <button 
                    @click="removeFromCart(index)"
                    class="text-red-600 text-xs hover:text-red-800 mt-1"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Cart summary -->
            <div class="border-t pt-4 space-y-2">
              <div class="flex justify-between text-sm">
                <span>Subtotal:</span>
                <span>PKR {{ cartSubtotal }}</span>
              </div>
              <div v-if="totalDiscount > 0" class="flex justify-between text-sm text-green-600">
                <span>Discount:</span>
                <span>-PKR {{ totalDiscount }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold border-t pt-2">
                <span>Total:</span>
                <span>PKR {{ cartTotal }}</span>
              </div>
            </div>
            
            <!-- Payment method -->
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
              <select
                v-model="paymentMethod"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="cash">Cash</option>
                <option value="bank_transfer">Bank Transfer</option>
              </select>
            </div>
            
            <!-- Checkout button -->
            <button 
              @click="checkout"
              :disabled="isProcessing"
              class="w-full mt-4 bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
            >
              <span v-if="isProcessing">Processing...</span>
              <span v-else>
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Checkout - PKR {{ cartTotal }}
              </span>
            </button>
            
            <!-- Clear cart button -->
            <button 
              @click="clearCart"
              class="w-full mt-2 bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 text-sm"
            >
              <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              Clear Cart
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Checkout Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">Sale Completed!</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">Sale ID: #{{ lastSaleId }}</p>
            <p class="text-sm text-gray-500">Total: PKR {{ lastSaleTotal }}</p>
            <p class="text-sm text-gray-500">Payment: {{ paymentMethod.replace('_', ' ').toUpperCase() }}</p>
          </div>
          <div class="items-center px-4 py-3">
            <button
              @click="closeSuccessModal"
              class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700"
            >
              Continue
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';

const barcodeInput = ref('');
const barcodeInputRef = ref(null);
const searchResults = ref([]);
const cart = ref([]);
const paymentMethod = ref('cash');
const isProcessing = ref(false);
const isScanning = ref(false);
const videoElement = ref(null);
const errorMessage = ref('');
const showSuccessModal = ref(false);
const lastSaleId = ref(null);
const lastSaleTotal = ref(0);

let stream = null;
let scanInterval = null;

// Computed properties
const cartTotal = computed(() => {
  return cart.value.reduce((total, item) => {
    return total + (item.final_price || item.dress?.sale_price || 0);
  }, 0);
});

const cartSubtotal = computed(() => {
  return cart.value.reduce((total, item) => {
    return total + (item.original_price || item.dress?.sale_price || 0);
  }, 0);
});

const totalDiscount = computed(() => {
  return cartSubtotal.value - cartTotal.value;
});

// Focus barcode input
const focusBarcode = () => {
  nextTick(() => {
    if (barcodeInputRef.value) {
      barcodeInputRef.value.focus();
    }
  });
};

// Search by barcode
const searchByBarcode = async () => {
  if (!barcodeInput.value.trim()) return;
  
  clearError();
  
  try {
    const response = await window.axios.get(`/api/dress-items/barcode/${barcodeInput.value}`);
    
    if (response.data) {
      // Check if item is already in cart
      const existingItem = cart.value.find(item => item.id === response.data.id);
      if (existingItem) {
        setError('This item is already in the cart');
        barcodeInput.value = '';
        return;
      }
      
      searchResults.value = [response.data];
      barcodeInput.value = '';
    } else {
      setError('Item not found');
      searchResults.value = [];
    }
  } catch (error) {
    console.error('Error searching by barcode:', error);
    if (error.response?.status === 404) {
      setError('Item not found or not available');
    } else {
      setError('Error searching for item. Please try again.');
    }
    searchResults.value = [];
  }
};

// Add item to cart
const addToCart = (item) => {
  if (item.status !== 'available') {
    setError('This item is not available for sale');
    return;
  }
  
  // Check if item is already in cart
  const existingItem = cart.value.find(cartItem => cartItem.id === item.id);
  if (existingItem) {
    setError('This item is already in the cart');
    return;
  }
  
  cart.value.push(item);
  searchResults.value = [];
  clearError();
  
  // Auto-focus barcode input for next scan
  focusBarcode();
};

// Remove item from cart
const removeFromCart = (index) => {
  cart.value.splice(index, 1);
  clearError();
};

// Clear entire cart
const clearCart = () => {
  if (confirm('Are you sure you want to clear the entire cart?')) {
    cart.value = [];
    clearError();
  }
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

// Camera scanning functionality
const toggleCameraScanning = async () => {
  if (isScanning.value) {
    stopCameraScanning();
  } else {
    await startCameraScanning();
  }
};

const startCameraScanning = async () => {
  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      setError('Camera access is not supported in this browser');
      return;
    }

    stream = await navigator.mediaDevices.getUserMedia({ 
      video: { 
        facingMode: 'environment', // Use back camera if available
        width: { ideal: 640 },
        height: { ideal: 480 }
      } 
    });
    
    isScanning.value = true;
    
    await nextTick();
    
    if (videoElement.value) {
      videoElement.value.srcObject = stream;
      
      // Start simulated barcode detection
      // In a real implementation, you would use a library like ZXing-js or QuaggaJS
      scanInterval = setInterval(() => {
        simulateBarcodeDetection();
      }, 1000);
    }
    
    clearError();
  } catch (error) {
    console.error('Error starting camera:', error);
    setError('Unable to access camera. Please check permissions.');
    isScanning.value = false;
  }
};

const stopCameraScanning = () => {
  if (stream) {
    stream.getTracks().forEach(track => track.stop());
    stream = null;
  }
  
  if (scanInterval) {
    clearInterval(scanInterval);
    scanInterval = null;
  }
  
  isScanning.value = false;
};

// Simulated barcode detection (replace with actual barcode library)
const simulateBarcodeDetection = () => {
  // This is a placeholder for actual barcode scanning
  // In production, you would integrate with ZXing-js or QuaggaJS
  console.log('Scanning for barcodes...');
  
  // Example: If you had a real barcode detected, you would call:
  // onBarcodeDetected(detectedBarcode);
};

const onBarcodeDetected = (detectedBarcode) => {
  barcodeInput.value = detectedBarcode;
  searchByBarcode();
  stopCameraScanning();
};

// Checkout process
const checkout = async () => {
  if (cart.value.length === 0) {
    setError('Cart is empty');
    return;
  }
  
  isProcessing.value = true;
  clearError();
  
  try {
    const saleData = {
      items: cart.value.map(item => ({
        dress_item_id: item.id,
        price: item.final_price || item.dress?.sale_price,
        original_price: item.original_price || item.dress?.sale_price
      })),
      payment_method: paymentMethod.value,
      total_amount: cartTotal.value,
      discount_amount: totalDiscount.value
    };
    
    const response = await window.axios.post('/api/sales', saleData);
    
    if (response.data && response.data.sale) {
      // Sale successful
      lastSaleId.value = response.data.sale.id;
      lastSaleTotal.value = cartTotal.value;
      showSuccessModal.value = true;
      
      // Clear cart
      cart.value = [];
      searchResults.value = [];
      
      console.log('Sale completed:', response.data);
    } else {
      setError('Sale processing failed. Please try again.');
    }
  } catch (error) {
    console.error('Error processing sale:', error);
    
    if (error.response?.data?.message) {
      setError(error.response.data.message);
    } else if (error.response?.data?.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat();
      setError(errorMessages.join(', '));
    } else {
      setError('Error processing sale. Please try again.');
    }
  } finally {
    isProcessing.value = false;
  }
};

// Close success modal
const closeSuccessModal = () => {
  showSuccessModal.value = false;
  lastSaleId.value = null;
  lastSaleTotal.value = 0;
  focusBarcode();
};

// Lifecycle hooks
onMounted(() => {
  focusBarcode();
  
  // Add keyboard shortcuts
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  stopCameraScanning();
  document.removeEventListener('keydown', handleKeydown);
});

// Keyboard shortcuts
const handleKeydown = (event) => {
  // F1 - Focus barcode input
  if (event.key === 'F1') {
    event.preventDefault();
    focusBarcode();
  }
  
  // F2 - Toggle camera
  if (event.key === 'F2') {
    event.preventDefault();
    toggleCameraScanning();
  }
  
  // F3 - Clear cart
  if (event.key === 'F3') {
    event.preventDefault();
    clearCart();
  }
  
  // Enter - Checkout (when not focused on barcode input)
  if (event.key === 'Enter' && event.ctrlKey && cart.value.length > 0) {
    event.preventDefault();
    checkout();
  }
};
</script>
