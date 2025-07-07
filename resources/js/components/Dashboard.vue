<template>
  <div class="w-full max-w-7xl mx-auto">
    <!-- Page header -->
    <div class="mb-6 sm:mb-8">
      <h1 class="text-2xl sm:text-3xl font-bold text-stone-900">Dashboard</h1>
      <p class="mt-1 sm:mt-2 text-sm sm:text-base text-stone-600">
        Welcome back! Here's what's happening in your store today.
      </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
      <!-- Today's Sales -->
      <div class="bg-gradient-to-r from-green-400 to-green-600 overflow-hidden shadow-lg rounded-lg sm:rounded-xl text-white">
        <div class="p-4 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 sm:ml-5 flex-1">
              <div class="text-xs sm:text-sm text-white/80 uppercase tracking-wide font-medium">Today's Sales</div>
              <div class="text-2xl sm:text-3xl font-bold text-white">PKR 0</div>
            </div>
          </div>
        </div>
        <div class="bg-white/10 px-4 sm:px-6 py-2 sm:py-3">
          <router-link to="/pos" class="text-xs sm:text-sm text-white/90 hover:text-white flex items-center">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Go to POS →
          </router-link>
        </div>
      </div>

      <!-- Items Sold Today -->
      <div class="bg-gradient-to-r from-blue-400 to-blue-600 overflow-hidden shadow-lg rounded-lg sm:rounded-xl text-white">
        <div class="p-4 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 sm:ml-5 flex-1">
              <div class="text-xs sm:text-sm text-white/80 uppercase tracking-wide font-medium">Items Sold Today</div>
              <div class="text-2xl sm:text-3xl font-bold text-white">0</div>
            </div>
          </div>
        </div>
        <div class="bg-white/10 px-4 sm:px-6 py-2 sm:py-3">
          <div class="text-xs sm:text-sm text-white/90">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            0 transactions
          </div>
        </div>
      </div>

      <!-- Available Items -->
      <div class="bg-gradient-to-r from-purple-400 to-purple-600 overflow-hidden shadow-lg rounded-lg sm:rounded-xl text-white">
        <div class="p-4 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 sm:ml-5 flex-1">
              <div class="text-xs sm:text-sm text-white/80 uppercase tracking-wide font-medium">Available Items</div>
              <div class="text-2xl sm:text-3xl font-bold text-white">0</div>
            </div>
          </div>
        </div>
        <div class="bg-white/10 px-4 sm:px-6 py-2 sm:py-3">
          <router-link to="/dresses" class="text-xs sm:text-sm text-white/90 hover:text-white flex items-center">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Manage Inventory →
          </router-link>
        </div>
      </div>

      <!-- Low Stock Items -->
      <div class="bg-gradient-to-r from-yellow-400 to-orange-500 overflow-hidden shadow-lg rounded-lg sm:rounded-xl text-white">
        <div class="p-4 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4 sm:ml-5 flex-1">
              <div class="text-xs sm:text-sm text-white/80 uppercase tracking-wide font-medium">Low Stock Items</div>
              <div class="text-2xl sm:text-3xl font-bold text-white">0</div>
            </div>
          </div>
        </div>
        <div class="bg-white/10 px-4 sm:px-6 py-2 sm:py-3">
          <router-link to="/reports" class="text-xs sm:text-sm text-white/90 hover:text-white flex items-center">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            View Reports →
          </router-link>
        </div>
      </div>
    </div>

    <!-- Two Column Layout for Mobile/Tablet -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
      <!-- Recent Sales -->
      <div class="bg-white shadow-lg rounded-lg sm:rounded-xl">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-stone-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg sm:text-xl font-semibold text-stone-900">Recent Sales</h3>
            <router-link to="/reports" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
              View all →
            </router-link>
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <div class="text-center py-8 sm:py-12">
            <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h4 class="mt-2 text-base sm:text-lg font-medium text-stone-900">No sales today yet</h4>
            <p class="mt-1 text-sm text-stone-500">Get started by making your first sale!</p>
            <div class="mt-4 sm:mt-6">
              <router-link
                to="/pos"
                class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
              >
                Start Selling
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Collections -->
      <div class="bg-white shadow-lg rounded-lg sm:rounded-xl">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-stone-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg sm:text-xl font-semibold text-stone-900">Top Collections</h3>
            <router-link to="/collections" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
              View all →
            </router-link>
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <div class="text-center py-8 sm:py-12">
            <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h4 class="mt-2 text-base sm:text-lg font-medium text-stone-900">No collections available</h4>
            <p class="mt-1 text-sm text-stone-500">Create your first collection to organize your inventory.</p>
            <div class="mt-4 sm:mt-6">
              <router-link
                to="/collections"
                class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-lg text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
              >
                Add Collections
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

// You can add reactive data here for stats
const stats = ref({
  todaysSales: 0,
  itemsSold: 0,
  availableItems: 0,
  lowStockItems: 0
})

onMounted(async () => {
  // Load dashboard data here
  // await loadDashboardData()
})
</script>
