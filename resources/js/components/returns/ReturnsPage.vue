<template>
  <div>
    <ModernReturnsInterface />
  </div>
</template>

<script setup>
import ModernReturnsInterface from './ModernReturnsInterface.vue'
</script>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Global Messages -->
    <div v-if="formMessage && !showCreateModal" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
      <div 
        :class="{
          'bg-green-50 border border-green-200 text-green-800': formMessageType === 'success',
          'bg-red-50 border border-red-200 text-red-800': formMessageType === 'error'
        }"
        class="px-4 py-3 rounded-md flex items-center gap-2"
      >
        <svg v-if="formMessageType === 'success'" class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <svg v-if="formMessageType === 'error'" class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span>{{ formMessage }}</span>
        <button @click="formMessage = ''; formMessageType = ''" class="ml-auto text-current hover:opacity-75">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Returns</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.totalReturns }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Exchanges</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.totalExchanges }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Refunded</p>
              <p class="text-2xl font-semibold text-gray-900">PKR {{ statsData.totalRefunded }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Today's Returns</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.todayReturns }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Returns Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">All Returns & Exchanges</h3>
            <div class="flex items-center gap-4">
              <!-- Search -->
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input 
                  v-model="searchTerm"
                  type="text" 
                  placeholder="Search by invoice, barcode..."
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
              
              <!-- Filter -->
              <select 
                v-model="returnTypeFilter"
                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
              >
                <option value="">All Types</option>
                <option value="return">Returns</option>
                <option value="exchange">Exchanges</option>
              </select>
            </div>
          </div>
          
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Loading returns...
            </div>
          </div>
          
          <!-- Table -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Invoice #
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Item Details
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Reason
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer
                  </th>
                  <th v-if="isAdmin" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="returnItem in filteredReturns" :key="returnItem.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ returnItem.sale_item?.sale?.invoice_number || 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500">
                      FBR: {{ returnItem.sale_item?.sale?.fbr_invoice_number || 'N/A' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                          <span class="text-white font-medium text-sm">{{ returnItem.sale_item?.dress_name?.charAt(0) || 'D' }}</span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <!-- Returned Item -->
                        <div class="text-sm font-medium text-gray-900">
                          <span class="text-red-600">Returned:</span> {{ returnItem.sale_item?.dress_name || 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ returnItem.sale_item?.collection_name || 'N/A' }} | 
                          Size: {{ returnItem.sale_item?.size || 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-400">
                          Barcode: {{ returnItem.sale_item?.barcode || 'N/A' }}
                        </div>
                        
                        <!-- Exchange Item (if exchange) -->
                        <div v-if="returnItem.return_type === 'exchange' && returnItem.exchange_item" class="mt-2 pt-2 border-t border-gray-200">
                          <div class="text-sm font-medium text-gray-900">
                            <span class="text-green-600">Exchanged with:</span> {{ returnItem.exchange_item?.dress?.name || 'N/A' }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ returnItem.exchange_item?.dress?.collection?.name || 'N/A' }} | 
                            Size: {{ returnItem.exchange_item?.dress?.size || 'N/A' }}
                          </div>
                          <div class="text-xs text-gray-400">
                            Barcode: {{ returnItem.exchange_item?.barcode || 'N/A' }} |
                            Price: PKR {{ returnItem.exchange_item?.dress?.sale_price || '0.00' }}
                          </div>
                          <div v-if="returnItem.exchange_sale_id" class="text-xs text-blue-600 mt-1">
                            📄 Exchange Sale: {{ returnItem.exchange_sale?.invoice_number || `#${returnItem.exchange_sale_id}` }}
                            <span v-if="returnItem.exchange_sale_item_id" class="ml-2">
                              | Sale Item: #{{ returnItem.exchange_sale_item_id }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col gap-1">
                      <span 
                        :class="returnItem.return_type === 'return' 
                          ? 'bg-red-100 text-red-800' 
                          : 'bg-blue-100 text-blue-800'"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full w-fit"
                      >
                        {{ returnItem.return_type === 'return' ? 'Return' : 'Exchange' }}
                      </span>
                      <span 
                        :class="getReturnStatusInfo(returnItem).badgeClass"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full w-fit"
                      >
                        {{ getReturnStatusInfo(returnItem).statusText }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex flex-col">
                      <span class="font-medium">{{ formatReturnReason(returnItem.return_reason) }}</span>
                      <span :class="getReturnStatusInfo(returnItem).statusClass" class="text-xs mt-1">
                        {{ getReturnStatusInfo(returnItem).isResaleable ? 'Can be resold' : 'Cannot be resold' }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="font-medium text-green-600">PKR {{ returnItem.refund_amount || '0.00' }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(returnItem.return_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div>{{ returnItem.sale_item?.sale?.customer_name || 'Walk-in' }}</div>
                    <div class="text-xs text-gray-500">{{ returnItem.sale_item?.sale?.customer_phone || '' }}</div>
                  </td>
                  <td v-if="isAdmin" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click="viewReturn(returnItem)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded mr-2"
                      title="View Details"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <button 
                      @click="printReturnSlip(returnItem)"
                      class="text-green-600 hover:text-green-900 p-1 rounded"
                      title="Print Return Slip"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            
            <!-- Empty State -->
            <div v-if="filteredReturns.length === 0" class="text-center py-12">
              <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No returns found</h3>
              <p class="text-gray-500 mb-4">
                {{ searchTerm ? 'Try adjusting your search or filters.' : 'No returns have been processed yet.' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Process Return Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Process Return/Exchange</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form Messages -->
          <div v-if="formMessage" class="mb-4">
            <div 
              :class="{
                'bg-green-50 border border-green-200 text-green-800': formMessageType === 'success',
                'bg-red-50 border border-red-200 text-red-800': formMessageType === 'error'
              }"
              class="px-4 py-3 rounded-md flex items-center gap-2"
            >
              <svg v-if="formMessageType === 'success'" class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <svg v-if="formMessageType === 'error'" class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
              <span>{{ formMessage }}</span>
            </div>
          </div>
          
          <!-- Step 1: Search for Item -->
          <div v-if="currentStep === 1">
            <h4 class="text-md font-medium text-gray-900 mb-4">Step 1: Find Sold Item</h4>
            
            <div class="mb-4">
              <label for="search-barcode" class="block text-sm font-medium text-gray-700 mb-2">
                Search by Barcode or Invoice Number
              </label>
              <div class="flex gap-2">
                <input
                  v-model="searchQuery"
                  type="text"
                  id="search-barcode"
                  @keyup.enter="searchSoldItem"
                  class="flex-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Enter barcode or invoice number"
                />
                <button
                  @click="searchSoldItem"
                  :disabled="!searchQuery.trim() || isSearching"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                >
                  {{ isSearching ? 'Searching...' : 'Search' }}
                </button>
              </div>
            </div>

            <!-- Search Results -->
            <div v-if="searchResults.length > 0" class="mb-4">
              <h5 class="text-sm font-medium text-gray-900 mb-2">Found Items:</h5>
              <div v-if="selectedSoldItem" class="mb-2 p-2 bg-green-50 border border-green-200 rounded text-sm text-green-700 flex justify-between items-center">
                <span>✅ Item selected! Click "Next" to continue or select a different item.</span>
                <button 
                  @click="clearSearch"
                  class="text-xs text-blue-600 hover:text-blue-800 underline"
                >
                  Clear Search
                </button>
              </div>
              <div class="space-y-2 max-h-60 overflow-y-auto">
                <div 
                  v-for="item in searchResults" 
                  :key="item.id" 
                  @click="selectSoldItem(item)"
                  :class="{
                    'border-blue-500 bg-blue-50': selectedSoldItem && selectedSoldItem.id === item.id,
                    'border-gray-200 hover:bg-gray-50': !selectedSoldItem || selectedSoldItem.id !== item.id
                  }"
                  class="p-3 border rounded-lg cursor-pointer transition-colors"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <div class="font-medium text-gray-900">
                        {{ item.dress_name }}
                        <span v-if="selectedSoldItem && selectedSoldItem.id === item.id" 
                              class="ml-2 text-xs bg-blue-600 text-white px-2 py-1 rounded">
                          SELECTED
                        </span>
                      </div>
                      <div class="text-sm text-gray-500">
                        Collection: {{ item.collection_name }} | Size: {{ item.size }}
                      </div>
                      <div class="text-xs text-gray-400">
                        Barcode: {{ item.barcode }} | Invoice: {{ item.sale?.invoice_number }}
                      </div>
                      <div class="text-xs text-gray-400">
                        Sold: {{ formatDate(item.sale?.sale_date) }}
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-xs text-gray-600 space-y-1">
                        <div class="flex justify-between items-center min-w-32">
                          <span>Sale Price:</span>
                          <span class="font-medium">PKR {{ item.sale_price || '0.00' }}</span>
                        </div>
                        <div v-if="item.collection_discount_percentage && parseFloat(item.collection_discount_percentage) > 0" class="flex justify-between items-center text-red-600">
                          <span>Collection Discount ({{ item.collection_discount_percentage }}%):</span>
                          <span class="font-medium">-PKR {{ (parseFloat(item.sale_price || 0) * parseFloat(item.collection_discount_percentage || 0) / 100).toFixed(2) }}</span>
                        </div>
                        <div v-if="item.dress_discount_percentage && parseFloat(item.dress_discount_percentage) > 0" class="flex justify-between items-center text-red-600">
                          <span>Dress Discount ({{ item.dress_discount_percentage }}%):</span>
                          <span class="font-medium">-PKR {{ (parseFloat(item.sale_price || 0) * parseFloat(item.dress_discount_percentage || 0) / 100).toFixed(2) }}</span>
                        </div>
                        <div v-if="item.size_discount_percentage && parseFloat(item.size_discount_percentage) > 0" class="flex justify-between items-center text-red-600">
                          <span>Size Discount ({{ item.size_discount_percentage }}%):</span>
                          <span class="font-medium">-PKR {{ (parseFloat(item.sale_price || 0) * parseFloat(item.size_discount_percentage || 0) / 100).toFixed(2) }}</span>
                        </div>
                        <div v-if="item.total_discount_amount && parseFloat(item.total_discount_amount) > 0" class="flex justify-between items-center text-red-600 border-t border-red-200 pt-1">
                          <span>Total Discount:</span>
                          <span class="font-medium">-PKR {{ item.total_discount_amount || '0.00' }}</span>
                        </div>
                        <div v-if="item.tax_amount && parseFloat(item.tax_amount) > 0" class="flex justify-between items-center text-green-600">
                          <span>GST ({{ item.tax_percentage || 18 }}%):</span>
                          <span class="font-medium">+PKR {{ item.tax_amount || '0.00' }}</span>
                        </div>
                        <div class="flex justify-between items-center font-bold text-gray-900 pt-1 border-t border-gray-200">
                          <span>Total Paid:</span>
                          <span>PKR {{ item.item_total || item.sale_price }}</span>
                        </div>
                      </div>
                      <div class="text-sm text-gray-500 mt-1">{{ item.sale?.customer_name || 'Walk-in' }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Search Error -->
            <div v-if="searchError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
              <p class="text-sm text-red-600">{{ searchError }}</p>
            </div>
          </div>

          <!-- Step 2: Return Details -->
          <div v-if="currentStep === 2 && selectedSoldItem">
            <h4 class="text-md font-medium text-gray-900 mb-4">Step 2: Return Details</h4>
            
            <!-- Selected Item Display -->
            <div class="mb-4 p-3 bg-gray-50 border rounded-lg">
              <div class="font-medium text-gray-900">{{ selectedSoldItem.dress_name }}</div>
              <div class="text-sm text-gray-500">
                Collection: {{ selectedSoldItem.collection_name }} | Size: {{ selectedSoldItem.size }}
              </div>
              <div class="text-sm text-gray-500">
                Sold Price: PKR {{ selectedSoldItem.item_total || selectedSoldItem.sale_price }} | Invoice: {{ selectedSoldItem.sale?.invoice_number }}
              </div>
            </div>

            <div class="mb-4">
              <label for="return-type" class="block text-sm font-medium text-gray-700 mb-2">Return Type</label>
              <select 
                v-model="returnFormData.return_type"
                id="return-type"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Select return type</option>
                <option value="return">Return (Refund)</option>
                <option value="exchange">Exchange</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="return-reason" class="block text-sm font-medium text-gray-700 mb-2">Return Reason</label>
              <select 
                v-model="returnFormData.return_reason"
                id="return-reason"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Select reason</option>
                <option value="defective">Defective Item</option>
                <option value="wrong_size">Wrong Size</option>
                <option value="customer_request">Customer Request</option>
                <option value="damaged">Damaged Item</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
              <textarea 
                v-model="returnFormData.notes"
                id="notes"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Any additional notes..."
              ></textarea>
            </div>

            <!-- Exchange Item Selection (if exchange) -->
            <div v-if="returnFormData.return_type === 'exchange'" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Select Exchange Item</label>
              <div class="border rounded-lg p-3">
                <input
                  v-model="exchangeSearchQuery"
                  type="text"
                  @input="searchExchangeItems"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3"
                  placeholder="Search available items by name or barcode..."
                />
                
                <div v-if="exchangeItems.length > 0" class="space-y-2 max-h-40 overflow-y-auto">
                  <div 
                    v-for="item in exchangeItems" 
                    :key="item.id"
                    @click="selectExchangeItem(item)"
                    :class="{'bg-blue-50 border-blue-200': returnFormData.exchange_item_id === item.id}"
                    class="p-2 border rounded cursor-pointer hover:bg-gray-50 transition-colors"
                  >
                    <div class="flex justify-between items-center">
                      <div>
                        <div class="font-medium text-sm">{{ item.dress.name }}</div>
                        <div class="text-xs text-gray-500">
                          {{ item.dress.collection.name }} | Size: {{ item.dress.size }} | {{ item.barcode }}
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="font-medium text-sm">PKR {{ item.dress.sale_price }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div v-if="exchangeSearchQuery && exchangeItems.length === 0" class="text-sm text-gray-500 text-center py-2">
                  No available items found
                </div>
              </div>
            </div>

            <!-- Refund Amount -->
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
              <div class="flex justify-between items-center">
                <span class="font-medium text-gray-900">
                  {{ returnFormData.return_type === 'exchange' ? 'Price Difference:' : 'Refund Amount:' }}
                </span>
                <span class="font-bold text-lg text-green-600">
                  PKR {{ computedRefundAmount }}
                </span>
              </div>
              
              <!-- Detailed Breakdown with Proper Discount and GST Display -->
              <div v-if="refundBreakdown" class="text-sm text-gray-600 mt-2 space-y-1">
                <!-- Original Item Breakdown -->
                <div class="border-b pb-2 mb-2">
                  <div class="text-xs font-medium text-gray-700 mb-1">Original Item:</div>
                  <div class="flex justify-between">
                    <span>Base Price:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.original_item?.sale_price || 0).toFixed(2) }}</span>
                  </div>
                  <div v-if="(refundBreakdown.original_item?.total_discount_amount || 0) > 0" class="flex justify-between text-red-600">
                    <span>Discount Applied:</span>
                    <span>-PKR {{ parseFloat(refundBreakdown.original_item?.total_discount_amount || 0).toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Price After Discount:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.original_item?.price_after_discount || 0).toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>GST (on discounted price):</span>
                    <span>PKR {{ parseFloat(refundBreakdown.original_item?.tax_amount || 0).toFixed(2) }}</span>
                  </div>
                  <div class="border-t pt-1 flex justify-between font-medium">
                    <span>Total Paid:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.original_item?.total_paid || 0).toFixed(2) }}</span>
                  </div>
                </div>
                
                <!-- Exchange Details -->
                <div v-if="refundBreakdown.exchange_item" class="border-b pb-2 mb-2">
                  <div class="text-xs font-medium text-gray-700 mb-1">Exchange Item:</div>
                  <div class="flex justify-between">
                    <span>Base Price:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.exchange_item?.original_price || 0).toFixed(2) }}</span>
                  </div>
                  <div v-if="(refundBreakdown.exchange_item?.total_discount_amount || 0) > 0" class="flex justify-between text-red-600">
                    <span>Discount Applied:</span>
                    <span>-PKR {{ parseFloat(refundBreakdown.exchange_item?.total_discount_amount || 0).toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Price After Discount:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.exchange_item?.discounted_price || 0).toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>GST ({{ parseFloat(refundBreakdown.exchange_item?.tax_percentage || 0) }}%):</span>
                    <span>PKR {{ parseFloat(refundBreakdown.exchange_item?.tax_amount || 0).toFixed(2) }}</span>
                  </div>
                  <div class="border-t pt-1 flex justify-between font-medium">
                    <span>Exchange Total:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.exchange_item?.total_with_gst || 0).toFixed(2) }}</span>
                  </div>
                </div>
                
                <!-- Final Calculation -->
                <div class="bg-green-50 p-2 rounded">
                  <div v-if="refundBreakdown.additional_payment_required > 0" class="flex justify-between text-orange-600 font-medium">
                    <span>Customer Pays Additional:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.additional_payment_required).toFixed(2) }}</span>
                  </div>
                  <div v-else class="flex justify-between text-green-600 font-medium">
                    <span>Customer Receives:</span>
                    <span>PKR {{ parseFloat(refundBreakdown.refund_amount).toFixed(2) }}</span>
                  </div>
                  <div v-if="refundBreakdown.message" class="text-xs text-gray-600 mt-1">
                    {{ refundBreakdown.message }}
                  </div>
                  <div v-if="refundBreakdown.accounting_note" class="text-xs text-blue-600 mt-1 italic">
                    ℹ️ {{ refundBreakdown.accounting_note }}
                  </div>
                </div>
              </div>
              
              <!-- Fallback display -->
              <div v-else-if="returnFormData.return_type === 'exchange' && selectedExchangeItem" class="text-sm text-gray-600 mt-1">
                Original: PKR {{ selectedSoldItem.item_total || selectedSoldItem.sale_price }} | 
                Exchange: PKR {{ selectedExchangeItem.dress.sale_price }}
              </div>
            </div>
          </div>

          <!-- Modal Actions -->
          <div class="flex justify-between">
            <div>
              <button 
                v-if="currentStep > 1"
                @click="currentStep--"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Back
              </button>
            </div>
            <div class="flex gap-2">
              <button 
                @click="closeModal"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                Cancel
              </button>
              <button 
                v-if="currentStep === 1"
                @click="currentStep++"
                :disabled="!selectedSoldItem"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                Next
              </button>
              <button 
                v-if="currentStep === 2"
                @click="processReturn"
                :disabled="!canProcessReturn || isProcessing"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
              >
                {{ isProcessing ? 'Processing...' : 'Process Return' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View Return Modal -->
    <div v-if="showViewModal && selectedReturn" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Return Details</h3>
            <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Return Type</label>
                <p class="text-sm text-gray-900">{{ selectedReturn.return_type === 'return' ? 'Return (Refund)' : 'Exchange' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Return Date</label>
                <p class="text-sm text-gray-900">{{ formatDate(selectedReturn.return_date) }}</p>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Item Details</label>
              <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                <p class="font-medium">{{ selectedReturn.sale_item?.dress_name }}</p>
                <p class="text-sm text-gray-600">
                  Collection: {{ selectedReturn.sale_item?.collection_name }} | 
                  Size: {{ selectedReturn.sale_item?.size }}
                </p>
                <p class="text-sm text-gray-600">Barcode: {{ selectedReturn.sale_item?.barcode }}</p>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Original Sale</label>
              <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                <p class="font-medium">Invoice: {{ selectedReturn.sale_item?.sale?.invoice_number }}</p>
                <p class="text-sm text-gray-600">
                  Customer: {{ selectedReturn.sale_item?.sale?.customer_name || 'Walk-in' }}
                </p>
                <p class="text-sm text-gray-600">
                  Sale Date: {{ formatDate(selectedReturn.sale_item?.sale?.sale_date) }}
                </p>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Return Reason</label>
              <p class="text-sm text-gray-900">{{ formatReturnReason(selectedReturn.return_reason) }}</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700">Refund Amount</label>
              <p class="text-lg font-bold text-green-600">PKR {{ selectedReturn.refund_amount }}</p>
            </div>
            
            <div v-if="selectedReturn.notes">
              <label class="block text-sm font-medium text-gray-700">Notes</label>
              <p class="text-sm text-gray-900">{{ selectedReturn.notes }}</p>
            </div>
          </div>
          
          <div class="flex justify-end mt-6">
            <button 
              @click="showViewModal = false"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'

export default {
  name: 'ReturnsPage',
  setup() {
    const authStore = useAuthStore()
    
    // Reactive data
    const loading = ref(false)
    const returns = ref([])
    const searchTerm = ref('')
    const returnTypeFilter = ref('')
    
    // Stats
    const statsData = reactive({
      totalReturns: 0,
      totalExchanges: 0,
      totalRefunded: 0,
      todayReturns: 0
    })
    
    // Modal states
    const showCreateModal = ref(false)
    const showViewModal = ref(false)
    const selectedReturn = ref(null)
    
    // Process return form
    const currentStep = ref(1)
    const searchQuery = ref('')
    const isSearching = ref(false)
    const searchResults = ref([])
    const searchError = ref('')
    const selectedSoldItem = ref(null)
    
    // Form messages
    const formMessage = ref('')
    const formMessageType = ref('') // 'success' or 'error'
    
    const returnFormData = reactive({
      return_type: '',
      return_reason: '',
      notes: '',
      exchange_item_id: null
    })
    
    // Exchange items
    const exchangeSearchQuery = ref('')
    const exchangeItems = ref([])
    const selectedExchangeItem = ref(null)
    const isProcessing = ref(false)
    const refundBreakdown = ref(null)
    
    // Computed
    const isAdmin = computed(() => authStore.user?.role === 'admin')
    
    // Computed refund amount that updates reactively
    const computedRefundAmount = computed(() => {
      if (refundBreakdown.value) {
        return parseFloat(refundBreakdown.value.refund_amount).toFixed(2)
      }
      return '0.00'
    })
    
    const filteredReturns = computed(() => {
      let filtered = returns.value
      
      if (searchTerm.value) {
        const search = searchTerm.value.toLowerCase()
        filtered = filtered.filter(returnItem => 
          returnItem.sale_item?.sale?.invoice_number?.toLowerCase().includes(search) ||
          returnItem.sale_item?.barcode?.toLowerCase().includes(search) ||
          returnItem.sale_item?.dress_name?.toLowerCase().includes(search) ||
          returnItem.sale_item?.sale?.customer_name?.toLowerCase().includes(search)
        )
      }
      
      if (returnTypeFilter.value) {
        filtered = filtered.filter(returnItem => returnItem.return_type === returnTypeFilter.value)
      }
      
      return filtered
    })
    
    const canProcessReturn = computed(() => {
      if (!selectedSoldItem.value || !returnFormData.return_type || !returnFormData.return_reason) {
        return false
      }
      
      if (returnFormData.return_type === 'exchange' && !returnFormData.exchange_item_id) {
        return false
      }
      
      return true
    })
    
    // Methods
    const fetchReturns = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/returns')
        returns.value = response.data.data || response.data
        calculateStats()
      } catch (error) {
        console.error('Error fetching returns:', error)
      } finally {
        loading.value = false
      }
    }
    
    const calculateStats = () => {
      const today = new Date().toDateString()
      
      statsData.totalReturns = returns.value.filter(r => r.return_type === 'return').length
      statsData.totalExchanges = returns.value.filter(r => r.return_type === 'exchange').length
      statsData.totalRefunded = returns.value
        .filter(r => r.return_type === 'return')
        .reduce((sum, r) => sum + parseFloat(r.refund_amount || 0), 0)
        .toFixed(2)
      statsData.todayReturns = returns.value.filter(r => 
        new Date(r.return_date).toDateString() === today
      ).length
    }
    
    const searchSoldItem = async () => {
      if (!searchQuery.value.trim()) return
      
      isSearching.value = true
      searchError.value = ''
      searchResults.value = []
      selectedSoldItem.value = null // Clear previous selection
      
      try {
        const response = await axios.get(`/api/sales/search-items?query=${encodeURIComponent(searchQuery.value)}`)
        searchResults.value = response.data.data || response.data
        
        if (searchResults.value.length === 0) {
          searchError.value = 'No sold items found with this barcode or invoice number'
        } else if (searchResults.value.length === 1) {
          // Auto-select if only one result found
          await selectSoldItem(searchResults.value[0])
        }
      } catch (error) {
        searchError.value = 'Error searching for items. Please try again.'
        console.error('Error searching sold items:', error)
      } finally {
        isSearching.value = false
      }
    }
    
    const selectSoldItem = async (item) => {
      selectedSoldItem.value = item
      // Don't clear search results immediately - let user see what was selected
      
      // Calculate refund amount when item is selected
      if (returnFormData.return_type) {
        await calculateRefundAmount()
      }
    }

    const clearSearch = () => {
      searchResults.value = []
      searchQuery.value = ''
      selectedSoldItem.value = null
      searchError.value = ''
    }
    
    const searchExchangeItems = async () => {
      if (!exchangeSearchQuery.value.trim()) {
        exchangeItems.value = []
        return
      }
      
      try {
        const response = await axios.get(`/api/dress-items/available?search=${encodeURIComponent(exchangeSearchQuery.value)}`)
        exchangeItems.value = response.data.data || response.data
      } catch (error) {
        console.error('Error searching exchange items:', error)
        exchangeItems.value = []
      }
    }
    
    const selectExchangeItem = (item) => {
      selectedExchangeItem.value = item
      returnFormData.exchange_item_id = item.id
    }
    
    const calculateRefundAmount = async () => {
      if (!selectedSoldItem.value) {
        refundBreakdown.value = null
        return '0.00'
      }
      
      try {
        // Call backend API to get accurate refund calculation with proper discount and GST handling
        const response = await axios.post('/api/returns/calculate-refund', {
          sale_item_id: selectedSoldItem.value.id,
          return_type: returnFormData.return_type,
          exchange_item_id: returnFormData.return_type === 'exchange' ? returnFormData.exchange_item_id : null
        })
        
        if (response.data.success) {
          refundBreakdown.value = response.data.calculation
          const refundAmount = parseFloat(response.data.calculation.refund_amount)
          return refundAmount.toFixed(2)
        }
      } catch (error) {
        console.error('Error calculating refund:', error)
        refundBreakdown.value = null
        // Fallback to simplified calculation
        if (returnFormData.return_type === 'return') {
          // Use item_total which includes discount and GST
          return parseFloat(selectedSoldItem.value.item_total || selectedSoldItem.value.sale_price).toFixed(2)
        }
        
        if (returnFormData.return_type === 'exchange' && selectedExchangeItem.value) {
          const originalTotal = parseFloat(selectedSoldItem.value.item_total || selectedSoldItem.value.sale_price)
          const exchangePrice = parseFloat(selectedExchangeItem.value.dress.sale_price)
          // Note: This is a simplified fallback - doesn't include proper discount/GST calculation
          const difference = originalTotal - exchangePrice
          return Math.max(0, difference).toFixed(2)
        }
      }
      
      return '0.00'
    }
    
    // Watch for changes and recalculate refund
    watch([() => returnFormData.return_type, () => returnFormData.exchange_item_id], async () => {
      if (selectedSoldItem.value) {
        await calculateRefundAmount()
      }
    })
    
    const processReturn = async () => {
      if (!canProcessReturn.value) return
      
      isProcessing.value = true
      
      try {
        const payload = {
          sale_item_id: selectedSoldItem.value.id,
          dress_item_id: selectedSoldItem.value.dress_item_id,
          return_type: returnFormData.return_type,
          return_reason: returnFormData.return_reason,
          // Remove refund_amount - let backend calculate it automatically
          notes: returnFormData.notes,
          ...(returnFormData.exchange_item_id && { exchange_item_id: returnFormData.exchange_item_id })
        }
        
        const response = await axios.post('/api/returns', payload)
        
        // Refresh returns list
        await fetchReturns()
        
        // Print return slip
        printReturnSlip(response.data.return)
        
        // Close modal and reset
        closeModal()
        
        // Show success message
        formMessage.value = 'Return processed successfully!'
        formMessageType.value = 'success'
        
        // Clear success message after 5 seconds
        setTimeout(() => {
          formMessage.value = ''
          formMessageType.value = ''
        }, 5000)
        
      } catch (error) {
        console.error('Error processing return:', error)
        formMessage.value = error.response?.data?.message || 'Error processing return. Please try again.'
        formMessageType.value = 'error'
        
        // Clear error message after 7 seconds
        setTimeout(() => {
          formMessage.value = ''
          formMessageType.value = ''
        }, 7000)
      } finally {
        isProcessing.value = false
      }
    }
    
    const viewReturn = (returnItem) => {
      selectedReturn.value = returnItem
      showViewModal.value = true
    }

    const printReturnSlip = (returnItem) => {
      const printWindow = window.open('', '_blank', 'width=300,height=600')
      
      const isExchange = returnItem.return_type === 'exchange'
      const originalPrice = parseFloat(returnItem.sale_item?.sale_price || 0)
      const exchangePrice = isExchange ? parseFloat(returnItem.exchange_item?.dress?.sale_price || 0) : 0
      
      const printContent = `
        <html>
          <head>
            <title>Return Slip</title>
            <style>
              body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                margin: 0;
                padding: 10px;
                max-width: 280px;
              }
              .header {
                text-align: center;
                border-bottom: 1px dashed #000;
                padding-bottom: 10px;
                margin-bottom: 10px;
              }
              .header h1 {
                font-size: 16px;
                margin: 0;
                font-weight: bold;
              }
              .header p {
                margin: 2px 0;
                font-size: 10px;
              }
              .section {
                margin: 10px 0;
              }
              .section h3 {
                font-size: 12px;
                margin: 5px 0;
                text-transform: uppercase;
                border-bottom: 1px solid #000;
              }
              .row {
                display: flex;
                justify-content: space-between;
                margin: 3px 0;
              }
              .label {
                font-weight: bold;
              }
              .divider {
                border-top: 1px dashed #000;
                margin: 10px 0;
              }
              .total {
                font-size: 14px;
                font-weight: bold;
                text-align: center;
                padding: 5px;
                border: 1px solid #000;
                margin-top: 10px;
              }
              .footer {
                text-align: center;
                font-size: 10px;
                margin-top: 15px;
                border-top: 1px dashed #000;
                padding-top: 10px;
              }
              @media print {
                body { margin: 0; }
                .no-print { display: none; }
              }
            </style>
          </head>
          <body>
            <div class="header">
              <h1>TASNEEM SHAMIM</h1>
              <p>Returns & Exchanges</p>
              <p>Date: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</p>
            </div>

            <div class="section">
              <h3>Return Details</h3>
              <div class="row">
                <span class="label">Type:</span>
                <span>${isExchange ? 'EXCHANGE' : 'RETURN'}</span>
              </div>
              <div class="row">
                <span class="label">Reason:</span>
                <span>${returnItem.return_reason || 'N/A'}</span>
              </div>
              <div class="row">
                <span class="label">Invoice:</span>
                <span>${returnItem.sale_item?.sale?.invoice_number || 'N/A'}</span>
              </div>
              <div class="row">
                <span class="label">Customer:</span>
                <span>${returnItem.sale_item?.sale?.customer_name || 'Walk-in'}</span>
              </div>
            </div>

            <div class="section">
              <h3>Returned Item</h3>
              <div class="row">
                <span class="label">Name:</span>
                <span>${returnItem.sale_item?.dress_name || 'N/A'}</span>
              </div>
              <div class="row">
                <span class="label">Collection:</span>
                <span>${returnItem.sale_item?.collection_name || 'N/A'}</span>
              </div>
              <div class="row">
                <span class="label">Size:</span>
                <span>${returnItem.sale_item?.size || 'N/A'}</span>
              </div>
              <div class="row">
                <span class="label">Barcode:</span>
                <span>${returnItem.sale_item?.barcode || 'N/A'}</span>
              </div>
              <div class="row">
                <span class="label">Price:</span>
                <span>PKR ${originalPrice.toFixed(2)}</span>
              </div>
            </div>

            ${isExchange && returnItem.exchange_item ? `
              <div class="section">
                <h3>Exchange Item</h3>
                <div class="row">
                  <span class="label">Name:</span>
                  <span>${returnItem.exchange_item.dress?.name || 'N/A'}</span>
                </div>
                <div class="row">
                  <span class="label">Collection:</span>
                  <span>${returnItem.exchange_item.dress?.collection?.name || 'N/A'}</span>
                </div>
                <div class="row">
                  <span class="label">Size:</span>
                  <span>${returnItem.exchange_item.dress?.size || 'N/A'}</span>
                </div>
                <div class="row">
                  <span class="label">Barcode:</span>
                  <span>${returnItem.exchange_item.barcode || 'N/A'}</span>
                </div>
                <div class="row">
                  <span class="label">Price:</span>
                  <span>PKR ${exchangePrice.toFixed(2)}</span>
                </div>
              </div>
            ` : ''}

            <div class="divider"></div>

            <div class="section">
              ${isExchange ? `
                <div class="row">
                  <span class="label">Original Amount:</span>
                  <span>PKR ${originalPrice.toFixed(2)}</span>
                </div>
                <div class="row">
                  <span class="label">Exchange Amount:</span>
                  <span>PKR ${exchangePrice.toFixed(2)}</span>
                </div>
                <div class="row">
                  <span class="label">Difference:</span>
                  <span>PKR ${Math.abs(originalPrice - exchangePrice).toFixed(2)} ${originalPrice > exchangePrice ? '(Refund)' : '(Additional)'}</span>
                </div>
              ` : `
                <div class="row">
                  <span class="label">Refund Amount:</span>
                  <span>PKR ${parseFloat(returnItem.refund_amount || 0).toFixed(2)}</span>
                </div>
              `}
            </div>

            ${returnItem.notes ? `
              <div class="section">
                <h3>Notes</h3>
                <p>${returnItem.notes}</p>
              </div>
            ` : ''}

            <div class="total">
              ${isExchange ? 
                (originalPrice > exchangePrice ? 
                  `REFUND: PKR ${(originalPrice - exchangePrice).toFixed(2)}` : 
                  `ADDITIONAL: PKR ${(exchangePrice - originalPrice).toFixed(2)}`) :
                `REFUND: PKR ${parseFloat(returnItem.refund_amount || 0).toFixed(2)}`
              }
            </div>

            <div class="footer">
              <p>Thank you for your business!</p>
              <p>Return processed by: ${returnItem.user?.name || 'System'}</p>
              <p class="no-print">
                <button onclick="window.print(); window.close();" style="margin-top: 10px; padding: 5px 10px;">Print</button>
              </p>
            </div>
          </body>
        </html>
      `
      
      printWindow.document.write(printContent)
      printWindow.document.close()
      
      // Auto-print after a short delay
      setTimeout(() => {
        printWindow.print()
        printWindow.close()
      }, 500)
    }
    
    const closeModal = () => {
      showCreateModal.value = false
      currentStep.value = 1
      searchQuery.value = ''
      searchResults.value = []
      selectedSoldItem.value = null
      exchangeSearchQuery.value = ''
      exchangeItems.value = []
      selectedExchangeItem.value = null
      formMessage.value = ''
      formMessageType.value = ''
      Object.assign(returnFormData, {
        return_type: '',
        return_reason: '',
        notes: '',
        exchange_item_id: null
      })
    }
    
    const formatDate = (date) => {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    const formatReturnReason = (reason) => {
      const reasons = {
        'defective': 'Defective Item',
        'wrong_size': 'Wrong Size',
        'customer_request': 'Customer Request',
        'damaged': 'Damaged Item'
      }
      return reasons[reason] || reason
    }

    const getReturnStatusInfo = (returnItem) => {
      const resaleableReasons = ['wrong_size', 'customer_request']
      const isResaleable = resaleableReasons.includes(returnItem.return_reason)
      
      return {
        isResaleable,
        statusText: isResaleable ? 'Resaleable' : 'Non-Resaleable',
        statusClass: isResaleable ? 'text-green-600' : 'text-red-600',
        badgeClass: isResaleable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
      }
    }
    
    // Lifecycle
    onMounted(() => {
      fetchReturns()
    })
    
    return {
      // Reactive data
      loading,
      returns,
      searchTerm,
      returnTypeFilter,
      statsData,
      
      // Modal states
      showCreateModal,
      showViewModal,
      selectedReturn,
      
      // Process return
      currentStep,
      searchQuery,
      isSearching,
      searchResults,
      searchError,
      selectedSoldItem,
      returnFormData,
      exchangeSearchQuery,
      exchangeItems,
      selectedExchangeItem,
      isProcessing,
      formMessage,
      formMessageType,
      
      // Computed
      isAdmin,
      filteredReturns,
      canProcessReturn,
      
      // Methods
      fetchReturns,
      searchSoldItem,
      selectSoldItem,
      clearSearch,
      searchExchangeItems,
      selectExchangeItem,
      calculateRefundAmount,
      processReturn,
      viewReturn,
      printReturnSlip,
      closeModal,
      formatDate,
      formatReturnReason,
      getReturnStatusInfo
    }
  }
}
</script>
