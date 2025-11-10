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
            <div class="w-full">
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
              Go to POS →
            </router-link>
          </div>
        </div>
      </div>

      <!-- Items Sold -->
      <div class="bg-gradient-to-r from-blue-400 to-blue-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
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
              {{ dashboardData.today_transactions || 0 }} transactions
            </span>
          </div>
        </div>
      </div>

      <!-- Available Inventory -->
      <div class="bg-gradient-to-r from-purple-400 to-purple-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
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
              Manage Inventory →
            </router-link>
          </div>
        </div>
      </div>

      <!-- Low Stock Alert -->
      <div class="bg-gradient-to-r from-yellow-400 to-orange-500 overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
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
          <span class="text-sm font-medium text-gray-900">New Sale</span>
        </router-link>
        
        <router-link to="/returns" class="flex flex-col items-center p-4 border-2 border-dashed border-orange-300 rounded-lg hover:border-orange-400 hover:bg-orange-50 transition-colors">
          <span class="text-sm font-medium text-gray-900">Process Return</span>
        </router-link>
        
        <router-link to="/dresses" class="flex flex-col items-center p-4 border-2 border-dashed border-purple-300 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition-colors">
          <span class="text-sm font-medium text-gray-900">Add Inventory</span>
        </router-link>
        
        <router-link to="/reports" class="flex flex-col items-center p-4 border-2 border-dashed border-blue-300 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition-colors">
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
