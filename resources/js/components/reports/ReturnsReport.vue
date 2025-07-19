<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Returns & Exchanges Report</h1>
          <p class="text-gray-600 mt-1">Track returns, exchanges, and refunds</p>
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
    <div v-if="returnsData.summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100 text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Returns</p>
            <p class="text-2xl font-bold text-gray-900">{{ returnsData.summary.total_returns }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Refunds</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ formatNumber(returnsData.summary.total_refund_amount) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Exchanges</p>
            <p class="text-2xl font-bold text-gray-900">{{ returnsData.summary.total_exchanges }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Return Rate</p>
            <p class="text-2xl font-bold text-gray-900">{{ returnsData.summary.return_rate_percentage }}%</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Returns Trend -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Returns Trend</h3>
        <div class="h-64 flex items-center justify-center text-gray-500">
          <div v-if="returnsData.returns_data && returnsData.returns_data.length > 0">
            <div class="w-full">
              <div v-for="(item, index) in returnsData.returns_data.slice(0, 7)" :key="index" class="flex items-center mb-2">
                <div class="w-20 text-xs text-gray-600">{{ formatDate(item.return_date) }}</div>
                <div class="flex-1 mx-2">
                  <div class="bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-red-600 rounded-full h-2" 
                      :style="{ width: getPercentage(item.total_returns, maxReturns) + '%' }"
                    ></div>
                  </div>
                </div>
                <div class="w-16 text-xs text-right">{{ item.total_returns }}</div>
              </div>
            </div>
          </div>
          <div v-else>No returns data available</div>
        </div>
      </div>

      <!-- Return Reasons -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Return Reasons</h3>
        <div class="space-y-3">
          <div v-for="(reason, index) in returnsData.reasons_breakdown" :key="index" class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-gray-900 capitalize">{{ reason.return_reason }}</span>
                <span class="text-sm text-gray-600">{{ reason.percentage }}%</span>
              </div>
              <div class="bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-orange-600 rounded-full h-2" 
                  :style="{ width: reason.percentage + '%' }"
                ></div>
              </div>
            </div>
            <div class="ml-4 text-right">
              <p class="text-sm font-bold text-gray-900">{{ reason.count }}</p>
              <p class="text-xs text-gray-600">Rs. {{ formatNumber(reason.total_refund) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Returned Items -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Returned Items</h3>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div v-for="(item, index) in returnsData.top_returned_items" :key="index" class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
          <div class="flex-1">
            <p class="font-medium text-gray-900">{{ item.dress_name }}</p>
            <p class="text-sm text-gray-600">{{ item.collection_name }} • Size: {{ item.size }}</p>
            <p class="text-xs text-red-600 mt-1">Common reason: {{ item.common_reason }}</p>
          </div>
          <div class="text-right">
            <p class="font-bold text-red-600">{{ item.return_count }} returns</p>
            <p class="text-sm text-gray-600">Rs. {{ formatNumber(item.total_refunds) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Exchange Analysis -->
    <div v-if="returnsData.exchange_data" class="bg-white rounded-lg shadow-sm border p-6 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Exchange Analysis</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="text-center">
          <p class="text-3xl font-bold text-blue-600">{{ returnsData.exchange_data.summary.total_exchanges }}</p>
          <p class="text-sm text-gray-600">Total Exchanges</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-green-600">{{ returnsData.exchange_data.summary.upgrades_count }}</p>
          <p class="text-sm text-gray-600">Upgrades</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-red-600">{{ returnsData.exchange_data.summary.downgrades_count }}</p>
          <p class="text-sm text-gray-600">Downgrades</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-purple-600">Rs. {{ formatNumber(returnsData.exchange_data.summary.net_difference) }}</p>
          <p class="text-sm text-gray-600">Net Difference</p>
        </div>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Metrics</h3>
      <div v-if="returnsData.summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="text-center">
          <p class="text-3xl font-bold text-blue-600">Rs. {{ formatNumber(returnsData.summary.average_refund) }}</p>
          <p class="text-sm text-gray-600">Average Refund</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-green-600">{{ returnsData.summary.exchange_rate }}%</p>
          <p class="text-sm text-gray-600">Exchange Rate</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-red-600">{{ returnsData.summary.refund_rate_percentage }}%</p>
          <p class="text-sm text-gray-600">Refund Rate</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-purple-600">Rs. {{ formatNumber(returnsData.summary.daily_average_refunds) }}</p>
          <p class="text-sm text-gray-600">Daily Avg Refunds</p>
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
const returnsData = ref({})

// Filters
const filters = ref({
  dateFrom: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
  dateTo: new Date().toISOString().split('T')[0],
  groupBy: 'day'
})

// Computed properties
const maxReturns = computed(() => {
  if (!returnsData.value.returns_data) return 0
  return Math.max(...returnsData.value.returns_data.map(item => parseInt(item.total_returns)))
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
    
    const response = await window.axios.get(`/api/reports/returns?${params}`)
    
    if (response.data && response.data.success) {
      returnsData.value = response.data.data
    } else {
      errorMessage.value = 'Failed to load returns data'
    }
  } catch (error) {
    console.error('Error loading returns data:', error)
    errorMessage.value = error.response?.data?.message || 'Error loading returns data'
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
    
    const response = await window.axios.get(`/api/reports/returns/export?${params}`)
    
    if (response.data && response.data.success) {
      // Create and download CSV
      const csvContent = convertToCSV(response.data.data)
      downloadCSV(csvContent, `returns-report-${filters.value.dateFrom}-${filters.value.dateTo}.csv`)
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
