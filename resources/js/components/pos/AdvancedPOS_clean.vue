<template>
  <div class="advanced-pos-container">
    <!-- Header -->
    <div class="pos-header">
      <h2 class="text-2xl font-bold mb-4">Advanced POS System</h2>
      <div class="flex items-center gap-4">
        <div class="relative">
          <input 
            v-model="searchTerm" 
            @input="searchProducts" 
            type="text" 
            placeholder="Search or scan barcode..."
            class="w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <div class="absolute right-2 top-2">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
        <button 
          @click="startBarcodeScanner" 
          class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
        >
          📷 Scan Barcode
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="pos-main grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Products Section -->
      <div class="products-section">
        <h3 class="text-xl font-semibold mb-4">Products</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div 
            v-for="product in filteredProducts" 
            :key="product.id"
            @click="addToCart(product)"
            class="product-card bg-white p-4 rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-shadow"
          >
            <div class="flex items-center gap-3">
              <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">{{ product.emoji || '📦' }}</span>
              </div>
              <div>
                <h4 class="font-semibold">{{ product.name }}</h4>
                <p class="text-gray-600">{{ product.barcode }}</p>
                <p class="text-lg font-bold text-blue-600">Rs. {{ product.price }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cart Section -->
      <div class="cart-section bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Shopping Cart</h3>
        
        <!-- Cart Items -->
        <div class="cart-items mb-6">
          <div 
            v-for="item in cart" 
            :key="item.id"
            class="cart-item flex items-center justify-between py-3 border-b"
          >
            <div class="flex items-center gap-3">
              <span class="text-lg">{{ item.emoji || '📦' }}</span>
              <div>
                <h4 class="font-semibold">{{ item.name }}</h4>
                <p class="text-gray-600">Rs. {{ item.price }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button 
                @click="decreaseQuantity(item)" 
                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300"
              >
                -
              </button>
              <span class="w-8 text-center">{{ item.quantity }}</span>
              <button 
                @click="increaseQuantity(item)" 
                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300"
              >
                +
              </button>
              <button 
                @click="removeFromCart(item)" 
                class="ml-2 text-red-500 hover:text-red-700"
              >
                🗑️
              </button>
            </div>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info mb-6">
          <h4 class="font-semibold mb-2">Customer Information</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input 
              v-model="customerInfo.name" 
              type="text" 
              placeholder="Customer Name"
              class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <input 
              v-model="customerInfo.phone" 
              type="tel" 
              placeholder="Phone Number"
              class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <textarea 
              v-model="customerInfo.address" 
              placeholder="Address"
              class="col-span-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="2"
            ></textarea>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="payment-method mb-6">
          <h4 class="font-semibold mb-2">Payment Method</h4>
          <div class="flex gap-2">
            <label class="flex items-center gap-2">
              <input 
                v-model="paymentMethod" 
                type="radio" 
                value="cash"
                class="form-radio"
              />
              <span>💵 Cash</span>
            </label>
            <label class="flex items-center gap-2">
              <input 
                v-model="paymentMethod" 
                type="radio" 
                value="card"
                class="form-radio"
              />
              <span>💳 Card</span>
            </label>
            <label class="flex items-center gap-2">
              <input 
                v-model="paymentMethod" 
                type="radio" 
                value="upi"
                class="form-radio"
              />
              <span>📱 UPI</span>
            </label>
          </div>
        </div>

        <!-- Total -->
        <div class="cart-total mb-6">
          <div class="flex justify-between mb-2">
            <span>Subtotal:</span>
            <span>Rs. {{ subtotal }}</span>
          </div>
          <div class="flex justify-between mb-2">
            <span>Tax ({{ taxRate }}%):</span>
            <span>Rs. {{ tax }}</span>
          </div>
          <div class="flex justify-between text-xl font-bold">
            <span>Total:</span>
            <span>Rs. {{ total }}</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="cart-actions flex gap-3">
          <button 
            @click="clearCart" 
            class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
          >
            Clear Cart
          </button>
          <button 
            @click="checkout" 
            :disabled="cart.length === 0"
            class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors disabled:bg-gray-300"
          >
            Checkout
          </button>
        </div>
      </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <div v-if="showScanner" class="scanner-modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
        <h3 class="text-xl font-semibold mb-4">Scan Barcode</h3>
        <div class="scanner-container">
          <video ref="scannerVideo" class="w-full h-64 bg-gray-100 rounded-lg"></video>
        </div>
        <div class="mt-4 flex gap-3">
          <button 
            @click="stopBarcodeScanner" 
            class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'AdvancedPOS',
  setup() {
    // Reactive data
    const searchTerm = ref('')
    const cart = ref([])
    const customerInfo = ref({
      name: '',
      phone: '',
      address: ''
    })
    const paymentMethod = ref('cash')
    const showScanner = ref(false)
    const scannerVideo = ref(null)
    const taxRate = ref(10)
    
    // Sample products
    const products = ref([
      { id: 1, name: 'Laptop Dell', barcode: '1234567890123', price: 75000, emoji: '💻' },
      { id: 2, name: 'iPhone 14', barcode: '2345678901234', price: 120000, emoji: '📱' },
      { id: 3, name: 'Headphones', barcode: '3456789012345', price: 5000, emoji: '🎧' },
      { id: 4, name: 'Keyboard', barcode: '4567890123456', price: 3000, emoji: '⌨️' },
      { id: 5, name: 'Mouse', barcode: '5678901234567', price: 1500, emoji: '🖱️' },
      { id: 6, name: 'Monitor', barcode: '6789012345678', price: 25000, emoji: '🖥️' },
      { id: 7, name: 'Printer', barcode: '7890123456789', price: 15000, emoji: '🖨️' },
      { id: 8, name: 'Tablet', barcode: '8901234567890', price: 35000, emoji: '📱' },
      { id: 9, name: 'Camera', barcode: '9012345678901', price: 45000, emoji: '📷' },
      { id: 10, name: 'Speaker', barcode: '0123456789012', price: 8000, emoji: '🔊' }
    ])

    // Computed properties
    const filteredProducts = computed(() => {
      if (!searchTerm.value) return products.value
      return products.value.filter(product => 
        product.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        product.barcode.includes(searchTerm.value)
      )
    })

    const subtotal = computed(() => {
      return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
    })

    const tax = computed(() => {
      return Math.round(subtotal.value * taxRate.value / 100)
    })

    const total = computed(() => {
      return subtotal.value + tax.value
    })

    // Methods
    const searchProducts = () => {
      // Search functionality is handled by computed property
    }

    const addToCart = (product) => {
      const existingItem = cart.value.find(item => item.id === product.id)
      if (existingItem) {
        existingItem.quantity += 1
      } else {
        cart.value.push({
          ...product,
          quantity: 1
        })
      }
    }

    const removeFromCart = (item) => {
      const index = cart.value.findIndex(cartItem => cartItem.id === item.id)
      if (index > -1) {
        cart.value.splice(index, 1)
      }
    }

    const increaseQuantity = (item) => {
      item.quantity += 1
    }

    const decreaseQuantity = (item) => {
      if (item.quantity > 1) {
        item.quantity -= 1
      } else {
        removeFromCart(item)
      }
    }

    const clearCart = () => {
      cart.value = []
    }

    const startBarcodeScanner = () => {
      showScanner.value = true
      // Initialize barcode scanner
      navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(stream => {
          if (scannerVideo.value) {
            scannerVideo.value.srcObject = stream
            scannerVideo.value.play()
          }
        })
        .catch(err => {
          console.error('Error accessing camera:', err)
          alert('Camera access is required for barcode scanning')
          showScanner.value = false
        })
    }

    const stopBarcodeScanner = () => {
      showScanner.value = false
      if (scannerVideo.value && scannerVideo.value.srcObject) {
        const tracks = scannerVideo.value.srcObject.getTracks()
        tracks.forEach(track => track.stop())
      }
    }

    const checkout = async () => {
      if (cart.value.length === 0) {
        alert('Cart is empty!')
        return
      }

      try {
        const saleData = {
          customer: customerInfo.value,
          items: cart.value,
          payment_method: paymentMethod.value,
          subtotal: subtotal.value,
          tax: tax.value,
          total: total.value,
          created_at: new Date().toISOString()
        }

        // Send to API
        const response = await fetch('/api/sales', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(saleData)
        })

        if (response.ok) {
          const result = await response.json()
          alert('Sale completed successfully!')
          
          // Print invoice
          printInvoice(result.sale_id)
          
          // Clear cart
          clearCart()
          customerInfo.value = { name: '', phone: '', address: '' }
          paymentMethod.value = 'cash'
        } else {
          throw new Error('Sale failed')
        }
      } catch (error) {
        console.error('Checkout error:', error)
        alert('Checkout failed. Please try again.')
      }
    }

    const printInvoice = (saleId) => {
      const invoiceContent = `<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Invoice</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 10px; }
    .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
    .company-name { font-size: 18px; font-weight: bold; }
    .company-details { font-size: 10px; margin-top: 5px; }
    .invoice-details { margin-bottom: 10px; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th, .items-table td { border: 1px solid #ccc; padding: 5px; text-align: left; }
    .items-table th { background-color: #f0f0f0; }
    .total-section { margin-top: 10px; text-align: right; }
    .footer { margin-top: 20px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; }
  </style>
</head>
<body>
  <div class="header">
    <div class="company-name">TASNEEM SHAMIM</div>
    <div class="company-details">
      Phone: +92 313 6520007<br>
      Address: Fazal Centre, Ground Floor, Main G.T Road, Gujranwala<br>
      Instagram: @tasneemshamim.pk
    </div>
  </div>
  
  <div class="invoice-details">
    <strong>Invoice #: ${saleId}</strong><br>
    Date: ${new Date().toLocaleDateString()}<br>
    Time: ${new Date().toLocaleTimeString()}<br>
    Customer: ${customerInfo.value.name || 'Walk-in Customer'}<br>
    Phone: ${customerInfo.value.phone || 'N/A'}<br>
    Payment Method: ${paymentMethod.value.toUpperCase()}
  </div>
  
  <table class="items-table">
    <thead>
      <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      ${cart.value.map(item => `
        <tr>
          <td>${item.name}</td>
          <td>${item.quantity}</td>
          <td>Rs. ${item.price}</td>
          <td>Rs. ${item.price * item.quantity}</td>
        </tr>
      `).join('')}
    </tbody>
  </table>
  
  <div class="total-section">
    <div>Subtotal: Rs. ${subtotal.value}</div>
    <div>Tax (${taxRate.value}%): Rs. ${tax.value}</div>
    <div><strong>Total: Rs. ${total.value}</strong></div>
  </div>
  
  <div class="footer">
    <p>Thank you for your business!</p>
    <p>Visit us again soon!</p>
  </div>
</body>
</html>`

      // Open print window
      const printWindow = window.open('', '_blank', 'width=300,height=600')
      printWindow.document.write(invoiceContent)
      printWindow.document.close()
      printWindow.print()
    }

    return {
      searchTerm,
      cart,
      customerInfo,
      paymentMethod,
      showScanner,
      scannerVideo,
      taxRate,
      products,
      filteredProducts,
      subtotal,
      tax,
      total,
      searchProducts,
      addToCart,
      removeFromCart,
      increaseQuantity,
      decreaseQuantity,
      clearCart,
      startBarcodeScanner,
      stopBarcodeScanner,
      checkout,
      printInvoice
    }
  }
}
</script>

<style scoped>
.advanced-pos-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.pos-header {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.pos-main {
  gap: 20px;
}

.products-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-card {
  transition: transform 0.2s;
}

.product-card:hover {
  transform: translateY(-2px);
}

.cart-section {
  height: fit-content;
}

.cart-item {
  transition: background-color 0.2s;
}

.cart-item:hover {
  background-color: #f9f9f9;
}

.scanner-modal {
  z-index: 1000;
}

@media (max-width: 768px) {
  .pos-main {
    grid-template-columns: 1fr;
  }
  
  .pos-header {
    text-align: center;
  }
  
  .pos-header .flex {
    flex-direction: column;
    gap: 10px;
  }
}
</style>
