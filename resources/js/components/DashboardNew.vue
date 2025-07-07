<template>
  <div class="min-h-screen bg-stone-50">
    <!-- Header -->
    <div class="bg-white border-b border-stone-200 shadow-sm">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div>
            <h1 class="text-2xl font-bold text-stone-800">Dashboard</h1>
            <p class="text-sm text-stone-600">Welcome back, {{ authStore.user?.name || 'User' }}</p>
          </div>
          <div class="text-sm text-stone-500">
            {{ currentDate }}
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats Overview -->
      <div class="section">
        <h2 class="section-title">Business Overview</h2>
        <div class="grid-stats">
          <div class="card">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-stone-600">Today's Sales</p>
                  <p class="text-2xl font-bold text-stone-900">PKR {{ stats.todaySales || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-stone-600">Items Sold</p>
                  <p class="text-2xl font-bold text-stone-900">{{ stats.itemsSold || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-stone-600">Inventory</p>
                  <p class="text-2xl font-bold text-stone-900">{{ stats.totalInventory || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-stone-600">Low Stock</p>
                  <p class="text-2xl font-bold text-stone-900">{{ stats.lowStock || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="section">
        <h2 class="section-title">Quick Actions</h2>
        <div class="grid-auto">
          <router-link to="/pos" class="card hover:shadow-md transition-shadow duration-200">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-stone-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-stone-900">New Sale</h3>
                  <p class="text-sm text-stone-600">Process a new customer sale</p>
                </div>
              </div>
            </div>
          </router-link>

          <router-link to="/returns" class="card hover:shadow-md transition-shadow duration-200">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-stone-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-stone-900">Process Return</h3>
                  <p class="text-sm text-stone-600">Handle customer returns</p>
                </div>
              </div>
            </div>
          </router-link>

          <router-link to="/collections" class="card hover:shadow-md transition-shadow duration-200">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-stone-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-stone-900">Manage Inventory</h3>
                  <p class="text-sm text-stone-600">Update collections and stock</p>
                </div>
              </div>
            </div>
          </router-link>

          <router-link to="/reports" class="card hover:shadow-md transition-shadow duration-200">
            <div class="card-body">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-stone-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-stone-900">View Reports</h3>
                  <p class="text-sm text-stone-600">Sales and analytics reports</p>
                </div>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Sales -->
        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-medium text-stone-900">Recent Sales</h3>
          </div>
          <div class="card-body">
            <div v-if="recentSales.length === 0" class="text-center py-8">
              <svg class="w-12 h-12 text-stone-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="text-stone-500">No recent sales</p>
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="sale in recentSales"
                :key="sale.id"
                class="flex items-center justify-between p-3 bg-stone-50 rounded-lg"
              >
                <div>
                  <p class="text-sm font-medium text-stone-900">Sale #{{ sale.id }}</p>
                  <p class="text-xs text-stone-600">{{ formatDate(sale.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-stone-900">PKR {{ sale.total_amount }}</p>
                  <p class="text-xs text-stone-600">{{ sale.items_count }} items</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Collections -->
        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-medium text-stone-900">Top Collections</h3>
          </div>
          <div class="card-body">
            <div v-if="topCollections.length === 0" class="text-center py-8">
              <svg class="w-12 h-12 text-stone-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
              </svg>
              <p class="text-stone-500">No collection data available</p>
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="collection in topCollections"
                :key="collection.id"
                class="flex items-center justify-between p-3 bg-stone-50 rounded-lg"
              >
                <div>
                  <p class="text-sm font-medium text-stone-900">{{ collection.name }}</p>
                  <p class="text-xs text-stone-600">{{ collection.items_count }} items</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-stone-900">{{ collection.sales_count }} sales</p>
                  <p class="text-xs text-stone-600">PKR {{ collection.total_revenue }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const stats = ref({
  todaySales: 0,
  itemsSold: 0,
  totalInventory: 0,
  lowStock: 0
});
const recentSales = ref([]);
const topCollections = ref([]);

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const loadDashboardData = async () => {
  try {
    // Load dashboard stats
    const statsResponse = await window.axios.get('/reports/dashboard-stats');
    if (statsResponse.data) {
      stats.value = {
        todaySales: statsResponse.data.today_sales || 0,
        itemsSold: statsResponse.data.items_sold || 0,
        totalInventory: statsResponse.data.total_inventory || 0,
        lowStock: statsResponse.data.low_stock || 0
      };
    }

    // Load recent sales
    const salesResponse = await window.axios.get('/reports/recent-sales');
    if (salesResponse.data) {
      recentSales.value = salesResponse.data.slice(0, 5);
    }

    // Load top collections
    const collectionsResponse = await window.axios.get('/reports/top-collections');
    if (collectionsResponse.data) {
      topCollections.value = collectionsResponse.data.slice(0, 5);
    }
  } catch (error) {
    console.error('Error loading dashboard data:', error);
    // Set fallback data
    stats.value = {
      todaySales: 0,
      itemsSold: 0,
      totalInventory: 0,
      lowStock: 0
    };
    recentSales.value = [];
    topCollections.value = [];
  }
};

onMounted(() => {
  loadDashboardData();
});
</script>

<style scoped>
.section {
  @apply mb-8;
}

.section-title {
  @apply text-xl font-semibold text-stone-800 mb-4;
}

.grid-stats {
  @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4;
}

.grid-auto {
  @apply grid grid-cols-1 md:grid-cols-2 gap-4;
}

.card {
  @apply bg-white rounded-xl shadow-sm border border-stone-200;
}

.card-header {
  @apply px-6 py-4 border-b border-stone-200;
}

.card-body {
  @apply px-6 py-4;
}

/* Hover effects */
.card:hover {
  @apply shadow-md;
}

/* Mobile responsive */
@media (max-width: 768px) {
  .grid-stats {
    @apply grid-cols-1 gap-3;
  }
  
  .grid-auto {
    @apply grid-cols-1 gap-3;
  }
  
  .card-body {
    @apply px-4 py-3;
  }
}
</style>
