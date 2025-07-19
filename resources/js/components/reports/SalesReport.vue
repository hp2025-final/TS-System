<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Sales Reports</h1>
          <p class="text-gray-600 mt-1">Comprehensive sales analytics and insights</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="exportData"
            :disabled="isLoading"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
          >
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export
          </button>
          <button
            @click="loadData"
            :disabled="isLoading"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
          >
            <svg v-if="isLoading" class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            {{ isLoading ? 'Loading...' : 'Refresh' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
          <input
            v-model="filters.dateFrom"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
          <input
            v-model="filters.dateTo"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Group By</label>
          <select
            v-model="filters.groupBy"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="day">Daily</option>
            <option value="week">Weekly</option>
            <option value="month">Monthly</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="loadData"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div v-if="salesData.summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ formatNumber(salesData.summary.total_revenue) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Transactions</p>
            <p class="text-2xl font-bold text-gray-900">{{ salesData.summary.total_transactions }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Average Transaction</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ formatNumber(salesData.summary.average_transaction) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Items Sold</p>
            <p class="text-2xl font-bold text-gray-900">{{ salesData.summary.items_sold }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Sales Chart -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Trend</h3>
        <div class="h-64 flex items-center justify-center text-gray-500">
          <div v-if="salesData.sales_data && salesData.sales_data.length > 0">
            <!-- Simple chart representation -->
            <div class="w-full">
              <div v-for="(item, index) in salesData.sales_data.slice(0, 7)" :key="index" class="flex items-center mb-2">
                <div class="w-20 text-xs text-gray-600">{{ formatDate(item.sale_date) }}</div>
                <div class="flex-1 mx-2">
                  <div class="bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-blue-600 rounded-full h-2" 
                      :style="{ width: getPercentage(item.total_revenue, maxRevenue) + '%' }"
                    ></div>
                  </div>
                </div>
                <div class="w-24 text-xs text-right">Rs. {{ formatNumber(item.total_revenue) }}</div>
              </div>
            </div>
          </div>
          <div v-else>No chart data available</div>
        </div>
      </div>

      <!-- Hourly Sales -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hourly Sales Pattern</h3>
        <div class="h-64 flex items-center justify-center text-gray-500">
          <div v-if="salesData.hourly_data && salesData.hourly_data.length > 0">
            <div class="w-full">
              <div v-for="(item, index) in salesData.hourly_data" :key="index" class="flex items-center mb-1">
                <div class="w-12 text-xs text-gray-600">{{ item.hour }}:00</div>
                <div class="flex-1 mx-2">
                  <div class="bg-gray-200 rounded-full h-1">
                    <div 
                      class="bg-green-600 rounded-full h-1" 
                      :style="{ width: getPercentage(item.transaction_count, maxTransactions) + '%' }"
                    ></div>
                  </div>
                </div>
                <div class="w-8 text-xs text-right">{{ item.transaction_count }}</div>
              </div>
            </div>
          </div>
          <div v-else>No hourly data available</div>
        </div>
      </div>
    </div>

    <!-- Top Items and Collections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Top Selling Items -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Selling Items</h3>
        <div class="space-y-3">
          <div v-for="(item, index) in salesData.top_items" :key="index" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1">
              <p class="font-medium text-gray-900">{{ item.dress_name }}</p>
              <p class="text-sm text-gray-600">{{ item.collection_name }} • Size: {{ item.size }}</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-gray-900">{{ item.quantity_sold }} sold</p>
              <p class="text-sm text-gray-600">Rs. {{ formatNumber(item.total_revenue) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Collections -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Collections</h3>
        <div class="space-y-3">
          <div v-for="(collection, index) in salesData.top_collections" :key="index" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1">
              <p class="font-medium text-gray-900">{{ collection.collection_name }}</p>
              <p class="text-sm text-gray-600">{{ collection.unique_items }} unique items</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-gray-900">{{ collection.quantity_sold }} sold</p>
              <p class="text-sm text-gray-600">Rs. {{ formatNumber(collection.total_revenue) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Analytics</h3>
      <div v-if="salesData.summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="text-center">
          <p class="text-3xl font-bold text-blue-600">Rs. {{ formatNumber(salesData.summary.total_profit) }}</p>
          <p class="text-sm text-gray-600">Total Profit</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-green-600">Rs. {{ formatNumber(salesData.summary.total_tax) }}</p>
          <p class="text-sm text-gray-600">Total Tax Collected</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-red-600">Rs. {{ formatNumber(salesData.summary.total_discounts) }}</p>
          <p class="text-sm text-gray-600">Total Discounts</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-purple-600">Rs. {{ formatNumber(salesData.summary.daily_average) }}</p>
          <p class="text-sm text-gray-600">Daily Average</p>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive state
const isLoading = ref(false)
const errorMessage = ref('')
const salesData = ref({})

// Filters
const filters = ref({
  dateFrom: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
  dateTo: new Date().toISOString().split('T')[0],
  groupBy: 'day'
})

// Computed properties
const maxRevenue = computed(() => {
  if (!salesData.value.sales_data) return 0
  return Math.max(...salesData.value.sales_data.map(item => parseFloat(item.total_revenue)))
})

const maxTransactions = computed(() => {
  if (!salesData.value.hourly_data) return 0
  return Math.max(...salesData.value.hourly_data.map(item => parseInt(item.transaction_count)))
})

// Methods
const loadData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const params = new URLSearchParams({
      date_from: filters.value.dateFrom,
      date_to: filters.value.dateTo,
      group_by: filters.value.groupBy
    })
    
    const response = await window.axios.get(`/api/reports/sales?${params}`)
    
    if (response.data && response.data.success) {
      salesData.value = response.data.data
    } else {
      errorMessage.value = 'Failed to load sales data'
    }
  } catch (error) {
    console.error('Error loading sales data:', error)
    errorMessage.value = error.response?.data?.message || 'Error loading sales data'
  } finally {
    isLoading.value = false
  }
}

const exportData = async () => {
  try {
    const params = new URLSearchParams({
      date_from: filters.value.dateFrom,
      date_to: filters.value.dateTo
    })
    
    const response = await window.axios.get(`/api/reports/sales/export?${params}`)
    
    if (response.data && response.data.success) {
      // Create and download CSV
      const csvContent = convertToCSV(response.data.data)
      downloadCSV(csvContent, `sales-report-${filters.value.dateFrom}-${filters.value.dateTo}.csv`)
    }
  } catch (error) {
    console.error('Error exporting data:', error)
    errorMessage.value = 'Error exporting data'
  }
}

const convertToCSV = (data) => {
  if (!data || data.length === 0) return ''
  
  const headers = Object.keys(data[0]).join(',')
  const rows = data.map(row => Object.values(row).join(','))
  
  return [headers, ...rows].join('\n')
}

const downloadCSV = (content, filename) => {
  const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  
  link.setAttribute('href', url)
  link.setAttribute('download', filename)
  link.style.visibility = 'hidden'
  
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const formatNumber = (number) => {
  return new Intl.NumberFormat('en-PK').format(Math.round(number || 0))
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-GB', { 
    month: 'short', 
    day: '2-digit' 
  })
}

const getPercentage = (value, max) => {
  if (!max || max === 0) return 0
  return Math.round((value / max) * 100)
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>
