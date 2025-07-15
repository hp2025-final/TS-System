<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Resaleable Items Management</h1>
      <div class="flex gap-2">
        <button
          @click="refreshItems"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
          Refresh
        </button>
      </div>
    </div>

    <!-- Status Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                <span class="text-white font-bold">{{ resaleableItems.length }}</span>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Awaiting Quality Check
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  Items Returned as Resaleable
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
      
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                <span class="text-white font-bold">{{ availableItems.length }}</span>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Ready for Sale
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  Quality Approved Items
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                <span class="text-white font-bold">{{ rejectedItems.length }}</span>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Quality Rejected
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  Marked as Damaged
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="-mb-px flex space-x-8">
        <button
          @click="activeTab = 'pending'"
          :class="activeTab === 'pending' 
            ? 'border-indigo-500 text-indigo-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Pending Quality Check ({{ resaleableItems.length }})
        </button>
        <button
          @click="activeTab = 'approved'"
          :class="activeTab === 'approved' 
            ? 'border-indigo-500 text-indigo-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Quality Approved ({{ availableItems.length }})
        </button>
        <button
          @click="activeTab = 'rejected'"
          :class="activeTab === 'rejected' 
            ? 'border-indigo-500 text-indigo-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Quality Rejected ({{ rejectedItems.length }})
        </button>
      </nav>
    </div>

    <!-- Items Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="currentItems.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No items found</h3>
        <p class="mt-1 text-sm text-gray-500">No items in this category.</p>
      </div>
      
      <ul v-else class="divide-y divide-gray-200">
        <li v-for="item in currentItems" :key="item.id" class="px-6 py-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-20 w-20">
                <img 
                  v-if="item.image_url" 
                  :src="item.image_url" 
                  :alt="item.name"
                  class="h-20 w-20 object-cover rounded-lg"
                >
                <div 
                  v-else 
                  class="h-20 w-20 bg-gray-200 rounded-lg flex items-center justify-center"
                >
                  <span class="text-gray-400 text-xs">No Image</span>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                <div class="text-sm text-gray-500">
                  <span class="font-medium">Size:</span> {{ item.size }}
                </div>
                <div class="text-sm text-gray-500">
                  <span class="font-medium">Barcode:</span> {{ item.barcode }}
                </div>
                <div class="text-sm text-gray-500">
                  <span class="font-medium">Price:</span> ${{ parseFloat(item.price).toFixed(2) }}
                </div>
                <div v-if="item.return_info" class="text-xs text-gray-400 mt-1">
                  <span class="font-medium">Return Reason:</span> {{ formatReturnReason(item.return_info.return_reason) }}
                  <br>
                  <span class="font-medium">Returned:</span> {{ formatDate(item.return_info.created_at) }}
                </div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
              <div v-if="activeTab === 'pending'" class="flex space-x-2">
                <button
                  @click="approveForSale(item)"
                  :disabled="processing"
                  class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm disabled:opacity-50"
                >
                  ✓ Approve for Sale
                </button>
                <button
                  @click="rejectItem(item)"
                  :disabled="processing"
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm disabled:opacity-50"
                >
                  ✗ Mark as Damaged
                </button>
              </div>
              
              <div v-else-if="activeTab === 'approved'" class="flex space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Ready for Sale
                </span>
                <button
                  @click="rejectItem(item)"
                  :disabled="processing"
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs disabled:opacity-50"
                >
                  Mark Damaged
                </button>
              </div>
              
              <div v-else-if="activeTab === 'rejected'">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                  Quality Rejected
                </span>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Success/Error Messages -->
    <div 
      v-if="message" 
      :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
      class="mt-4 border rounded-md p-4"
    >
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Reactive data
const resaleableItems = ref([])
const availableItems = ref([])
const rejectedItems = ref([])
const activeTab = ref('pending')
const processing = ref(false)
const message = ref('')
const messageType = ref('success')

// Computed properties
const currentItems = computed(() => {
  switch (activeTab.value) {
    case 'pending':
      return resaleableItems.value
    case 'approved':
      return availableItems.value
    case 'rejected':
      return rejectedItems.value
    default:
      return []
  }
})

// Methods
const formatReturnReason = (reason) => {
  return reason.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const showMessage = (msg, type = 'success') => {
  message.value = msg
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

const refreshItems = async () => {
  try {
    processing.value = true
    
    // Fetch resaleable items
    const resaleableResponse = await axios.get('/api/dress-items/resaleable')
    resaleableItems.value = resaleableResponse.data.data || []
    
    // Fetch available items (quality approved)
    const availableResponse = await axios.get('/api/dress-items', {
      params: { status: 'available' }
    })
    availableItems.value = (availableResponse.data.data || []).filter(item => 
      item.was_returned && item.status === 'available'
    )
    
    // Fetch damaged items (quality rejected)
    const damagedResponse = await axios.get('/api/dress-items', {
      params: { status: 'damaged' }
    })
    rejectedItems.value = (damagedResponse.data.data || []).filter(item => 
      item.was_returned
    )
    
  } catch (error) {
    console.error('Error fetching items:', error)
    showMessage('Error loading items: ' + (error.response?.data?.message || 'Unknown error'), 'error')
  } finally {
    processing.value = false
  }
}

const approveForSale = async (item) => {
  if (!confirm(`Are you sure you want to approve "${item.name}" for sale?`)) {
    return
  }
  
  try {
    processing.value = true
    await axios.put(`/api/dress-items/${item.id}/make-available`)
    
    showMessage(`${item.name} has been approved for sale!`, 'success')
    await refreshItems()
    
  } catch (error) {
    console.error('Error approving item:', error)
    showMessage('Error approving item: ' + (error.response?.data?.message || 'Unknown error'), 'error')
  } finally {
    processing.value = false
  }
}

const rejectItem = async (item) => {
  if (!confirm(`Are you sure you want to mark "${item.name}" as damaged? This action cannot be undone.`)) {
    return
  }
  
  try {
    processing.value = true
    await axios.put(`/api/dress-items/${item.id}/mark-damaged`)
    
    showMessage(`${item.name} has been marked as damaged.`, 'success')
    await refreshItems()
    
  } catch (error) {
    console.error('Error rejecting item:', error)
    showMessage('Error marking item as damaged: ' + (error.response?.data?.message || 'Unknown error'), 'error')
  } finally {
    processing.value = false
  }
}

// Lifecycle hooks
onMounted(() => {
  refreshItems()
})
</script>
