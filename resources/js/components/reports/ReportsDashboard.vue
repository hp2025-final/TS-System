<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-stone-900">Reports & Analytics</h1>
          <p class="text-stone-600 mt-1">Track performance and monitor your boutique</p>
        </div>
        <button
          @click="refreshAllReports"
          :disabled="isLoading"
          class="bg-gradient-to-r from-stone-600 to-stone-700 hover:from-stone-700 hover:to-stone-800 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
        >
          <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          {{ isLoading ? 'Loading...' : 'Refresh Reports' }}
        </button>
      </div>
    </div>

    <!-- Report Tabs -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
      <nav class="flex space-x-1 bg-stone-100 p-1 rounded-xl" aria-label="Tabs">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all duration-200',
            activeTab === tab.id
              ? 'bg-white text-stone-900 shadow-sm'
              : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50'
          ]"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <!-- Sales Report -->
    <div v-if="activeTab === 'sales'" class="space-y-6">
      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 flex-1">
              <dt class="text-sm font-medium text-stone-600">Today's Sales</dt>
              <dd class="text-2xl font-bold text-stone-900">PKR {{ salesReport.today_total || 0 }}</dd>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 flex-1">
              <dt class="text-sm font-medium text-stone-600">Items Sold</dt>
              <dd class="text-2xl font-bold text-stone-900">{{ salesReport.today_items || 0 }}</dd>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 flex-1">
              <dt class="text-sm font-medium text-stone-600">Today's Profit</dt>
              <dd class="text-2xl font-bold text-stone-900">PKR {{ salesReport.today_profit || 0 }}</dd>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0L12 18m0 0h7"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 flex-1">
              <dt class="text-sm font-medium text-stone-600">Transactions</dt>
              <dd class="text-2xl font-bold text-stone-900">{{ salesReport.today_transactions || 0 }}</dd>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Sales -->
      <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-200 bg-stone-50">
          <h3 class="text-lg font-semibold text-stone-900">Recent Sales</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Sale ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Profit</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-stone-200">
              <tr v-for="sale in salesReport.recent_sales" :key="sale.id" class="hover:bg-stone-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-900">#{{ sale.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-600">{{ formatDateTime(sale.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-600">{{ sale.items_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-900">PKR {{ sale.total_amount }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getPaymentMethodClass(sale.payment_method)">
                    {{ formatPaymentMethod(sale.payment_method) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">PKR {{ sale.profit || 0 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Inventory Report -->
    <div v-if="activeTab === 'inventory'" class="space-y-6">
      <!-- Inventory Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ inventoryReport.total_items || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                  <span class="text-green-600 font-bold">✅</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Available</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ inventoryReport.available_items || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                  <span class="text-red-600 font-bold">❌</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Sold Items</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ inventoryReport.sold_items || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Collection Breakdown -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Inventory by Collection</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value (Available)</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="collection in inventoryReport.by_collection" :key="collection.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="text-sm font-medium text-gray-900">{{ collection.name }}</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ collection.total_items }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">{{ collection.available_items }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ collection.sold_items }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">PKR {{ collection.available_value }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Low Stock Report -->
    <div v-if="activeTab === 'low-stock'" class="space-y-6">
      <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Low Stock Alert</h3>
            <div class="mt-2 text-sm text-yellow-700">
              <p>Items with low or no stock remaining. Consider restocking these items.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Low Stock Items</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dress</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in lowStockReport.items" :key="item.dress_id + '-' + item.dress.size">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.dress_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.collection_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.dress.size }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="item.available_count === 0 ? 'text-red-600' : 'text-yellow-600'">
                  {{ item.available_count }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="item.available_count === 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ item.available_count === 0 ? 'Out of Stock' : 'Low Stock' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Returns Report -->
    <div v-if="activeTab === 'returns'" class="space-y-6">
      <!-- Returns Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                  <span class="text-orange-600 font-bold">↩️</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Returns</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ returnsReport.total_returns || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                  <span class="text-red-600 font-bold">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Refund Amount</dt>
                  <dd class="text-lg font-semibold text-gray-900">PKR {{ returnsReport.total_refund || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 font-bold">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                  </span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Exchanges</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ returnsReport.total_exchanges || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Returns -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Recent Returns</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="returnItem in returnsReport.recent_returns" :key="returnItem.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ returnItem.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDateTime(returnItem.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="returnItem.return_type === 'refund' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ returnItem.return_type }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ returnItem.reason }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">PKR {{ returnItem.refund_amount || 0 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
      <p class="text-sm text-red-700">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Reactive state
const activeTab = ref('sales');
const isLoading = ref(false);
const errorMessage = ref('');

const salesReport = ref({});
const inventoryReport = ref({});
const lowStockReport = ref({});
const returnsReport = ref({});

const tabs = [
  { id: 'sales', name: 'Sales Report' },
  { id: 'inventory', name: 'Inventory' },
  { id: 'low-stock', name: 'Low Stock' },
  { id: 'returns', name: 'Returns' }
];

// Load all reports
const refreshAllReports = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  
  try {
    await Promise.all([
      loadSalesReport(),
      loadInventoryReport(),
      loadLowStockReport(),
      loadReturnsReport()
    ]);
  } catch (error) {
    console.error('Error loading reports:', error);
    setError('Error loading reports. Please try again.');
  } finally {
    isLoading.value = false;
  }
};

// Load sales report
const loadSalesReport = async () => {
  try {
    const response = await window.axios.get('/api/reports/sales');
    salesReport.value = response.data || {};
  } catch (error) {
    console.error('Error loading sales report:', error);
    throw error;
  }
};

// Load inventory report
const loadInventoryReport = async () => {
  try {
    const response = await window.axios.get('/api/reports/inventory');
    inventoryReport.value = response.data || {};
  } catch (error) {
    console.error('Error loading inventory report:', error);
    throw error;
  }
};

// Load low stock report
const loadLowStockReport = async () => {
  try {
    const response = await window.axios.get('/api/reports/low-stock');
    lowStockReport.value = response.data || {};
  } catch (error) {
    console.error('Error loading low stock report:', error);
    throw error;
  }
};

// Load returns report
const loadReturnsReport = async () => {
  try {
    const response = await window.axios.get('/api/reports/returns');
    returnsReport.value = response.data || {};
  } catch (error) {
    console.error('Error loading returns report:', error);
    throw error;
  }
};

// Helper functions
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

const formatPaymentMethod = (method) => {
  switch (method) {
    case 'cash':
      return 'Cash';
    case 'bank_transfer':
      return 'Bank Transfer';
    default:
      return method;
  }
};

const getPaymentMethodClass = (method) => {
  const baseClass = 'inline-flex px-2 py-1 text-xs font-semibold rounded-full';
  switch (method) {
    case 'cash':
      return `${baseClass} bg-green-100 text-green-800`;
    case 'bank_transfer':
      return `${baseClass} bg-blue-100 text-blue-800`;
    default:
      return `${baseClass} bg-gray-100 text-gray-800`;
  }
};

const setError = (message) => {
  errorMessage.value = message;
  setTimeout(() => {
    errorMessage.value = '';
  }, 5000);
};

// Lifecycle
onMounted(() => {
  refreshAllReports();
});
</script>
