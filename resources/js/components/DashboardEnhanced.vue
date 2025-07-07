<template>
  <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
      <p class="mt-2 text-sm sm:text-base text-gray-600">Welcome back! Here's what's happening in your store today.</p>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Today's Sales -->
      <div class="bg-gradient-to-r from-green-400 to-green-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-white bg-opacity-30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-green-100 truncate">Today's Sales</dt>
                <dd class="text-2xl font-bold text-white">PKR {{ formatNumber(dashboardData.today_sales || 0) }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-green-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/pos" class="font-medium text-green-700 hover:text-green-900">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M7 13v4a2 2 0 002 2h6a2 2 0 002-2v-4m-8 2a2 2 0 11-4 0 2 2 0 014 0zm0 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Go to POS →
            </router-link>
          </div>
        </div>
      </div>

      <!-- Items Sold -->
      <div class="bg-gradient-to-r from-blue-400 to-blue-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-white bg-opacity-30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-blue-100 truncate">Items Sold Today</dt>
                <dd class="text-2xl font-bold text-white">{{ dashboardData.today_items || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-blue-50 px-5 py-3">
          <div class="text-sm">
            <span class="font-medium text-blue-700">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ dashboardData.today_transactions || 0 }} transactions
            </span>
          </div>
        </div>
      </div>

      <!-- Available Inventory -->
      <div class="bg-gradient-to-r from-purple-400 to-purple-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-white bg-opacity-30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4 4 4 0 004-4V5z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-purple-100 truncate">Available Items</dt>
                <dd class="text-2xl font-bold text-white">{{ dashboardData.available_items || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-purple-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/dresses" class="font-medium text-purple-700 hover:text-purple-900">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
              Manage Inventory →
            </router-link>
          </div>
        </div>
      </div>

      <!-- Low Stock Alert -->
      <div class="bg-gradient-to-r from-yellow-400 to-orange-500 overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-white bg-opacity-30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-yellow-100 truncate">Low Stock Items</dt>
                <dd class="text-2xl font-bold text-white">{{ dashboardData.low_stock_count || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-yellow-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/reports" class="font-medium text-yellow-700 hover:text-yellow-900">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              View Reports →
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent Sales -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Sales</h3>
            <router-link to="/reports" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
              View all →
            </router-link>
          </div>
        </div>
        <div class="px-6 py-4">
          <div v-if="recentSales.length === 0" class="text-center py-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293A1 1 0 005 16h12M7 13v4a2 2 0 002 2h6a2 2 0 002-2v-4m-8 2a2 2 0 11-4 0 2 2 0 014 0zm0 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-gray-500">No sales today yet</p>
            <router-link to="/pos" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Start Selling
            </router-link>
          </div>
          <div v-else class="space-y-3">
            <div v-for="sale in recentSales.slice(0, 5)" :key="sale.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p class="text-sm font-medium text-gray-900">Sale #{{ sale.id }}</p>
                <p class="text-xs text-gray-500">{{ formatTime(sale.created_at) }} • {{ sale.items_count }} items</p>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900">PKR {{ sale.total_amount }}</p>
                <p class="text-xs text-gray-500">{{ formatPaymentMethod(sale.payment_method) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Collections -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Top Collections</h3>
            <router-link to="/collections" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
              View all →
            </router-link>
          </div>
        </div>
        <div class="px-6 py-4">
          <div v-if="topCollections.length === 0" class="text-center py-8">
            <div class="text-4xl mb-2">
              <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
            <p class="text-gray-500">No collections available</p>
            <router-link to="/collections" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Add Collections
            </router-link>
          </div>
          <div v-else class="space-y-3">
            <div v-for="collection in topCollections.slice(0, 5)" :key="collection.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p class="text-sm font-medium text-gray-900">{{ collection.name }}</p>
                <p class="text-xs text-gray-500">{{ collection.available_items }} available • {{ collection.total_items }} total</p>
              </div>
              <div class="text-right">
                <p class="text-sm font-semibold text-gray-900">{{ collection.sold_items || 0 }} sold</p>
                <div v-if="collection.discount_percentage && collection.discount_active" class="text-xs text-green-600">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                  {{ collection.discount_percentage }}% off
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link to="/pos" class="flex flex-col items-center p-4 border-2 border-dashed border-green-300 rounded-lg hover:border-green-400 hover:bg-green-50 transition-colors">
          <div class="text-3xl mb-2">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-900">New Sale</span>
        </router-link>
        
        <router-link to="/returns" class="flex flex-col items-center p-4 border-2 border-dashed border-orange-300 rounded-lg hover:border-orange-400 hover:bg-orange-50 transition-colors">
          <div class="text-3xl mb-2">
            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m0 0l4 4M9 3L5 7"></path>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-900">Process Return</span>
        </router-link>
        
        <router-link to="/dresses" class="flex flex-col items-center p-4 border-2 border-dashed border-purple-300 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition-colors">
          <div class="text-3xl mb-2">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-900">Add Inventory</span>
        </router-link>
        
        <router-link to="/reports" class="flex flex-col items-center p-4 border-2 border-dashed border-blue-300 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition-colors">
          <div class="text-3xl mb-2">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <span class="text-sm font-medium text-gray-900">View Reports</span>
        </router-link>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="isLoading" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-600">Loading dashboard...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Reactive state
const isLoading = ref(false);
const dashboardData = ref({});
const recentSales = ref([]);
const topCollections = ref([]);

// Load dashboard data
const loadDashboard = async () => {
  isLoading.value = true;
  
  try {
    // Load daily report for dashboard stats
    const dailyResponse = await window.axios.get('/api/reports/daily');
    if (dailyResponse.data) {
      dashboardData.value = dailyResponse.data;
      recentSales.value = dailyResponse.data.recent_sales || [];
    }

    // Load inventory data for collections
    const inventoryResponse = await window.axios.get('/api/reports/inventory');
    if (inventoryResponse.data && inventoryResponse.data.by_collection) {
      topCollections.value = inventoryResponse.data.by_collection;
    }
  } catch (error) {
    console.error('Error loading dashboard:', error);
  } finally {
    isLoading.value = false;
  }
};

// Helper functions
const formatNumber = (number) => {
  return new Intl.NumberFormat('en-US').format(number);
};

const formatTime = (dateTime) => {
  return new Date(dateTime).toLocaleTimeString('en-US', {
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

// Lifecycle
onMounted(() => {
  loadDashboard();
});
</script>
