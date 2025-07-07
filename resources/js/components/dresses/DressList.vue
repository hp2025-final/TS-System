<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-stone-900">Dresses</h1>
          <p class="text-stone-600 mt-1">Browse and manage your boutique inventory</p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="relative">
            <input 
              type="text" 
              placeholder="Search dresses..." 
              class="bg-stone-50 border border-stone-300 rounded-xl py-2 pl-10 pr-4 text-stone-900 focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-stone-500"
            />
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <button class="bg-gradient-to-r from-stone-600 to-stone-700 hover:from-stone-700 hover:to-stone-800 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
            <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Dress
          </button>
        </div>
      </div>
    </div>
    
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-stone-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-stone-600 font-medium">Loading dresses...</span>
      </div>
    </div>
    
    <!-- Dresses Grid -->
    <div v-else>
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <div 
          v-for="dress in dresses" 
          :key="dress.id"
          class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden hover:shadow-md transition-shadow duration-200"
        >
          <!-- Dress Image Area -->
          <div class="h-56 bg-gradient-to-r from-stone-100 to-stone-200 flex items-center justify-center relative">
            <svg class="h-20 w-20 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            
            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
              <span 
                :class="dress.status === 'active' 
                  ? 'bg-green-100 text-green-700 border-green-200' 
                  : 'bg-red-100 text-red-700 border-red-200'"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full border"
              >
                {{ dress.status }}
              </span>
            </div>
            
            <!-- Discount Badge -->
            <div v-if="dress.discount_active && dress.discount_percentage > 0" class="absolute top-3 left-3">
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                {{ dress.discount_percentage }}% OFF
              </span>
            </div>
          </div>
          
          <!-- Dress Info -->
          <div class="p-5">
            <h3 class="text-lg font-semibold text-stone-900 mb-1">{{ dress.name }}</h3>
            <p class="text-stone-600 text-sm mb-2">{{ dress.collection?.name || 'No Collection' }}</p>
            <p class="text-stone-500 text-xs mb-4">SKU: {{ dress.sku }}</p>
            
            <!-- Pricing -->
            <div class="mb-4">
              <div class="text-xl font-bold text-stone-900">
                PKR {{ dress.sale_price?.toLocaleString() }}
              </div>
              <div class="text-sm text-stone-500">
                Cost: PKR {{ dress.cost_price?.toLocaleString() }}
              </div>
            </div>
            
            <!-- Availability -->
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center text-stone-600 text-sm">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                {{ dress.dress_items_count || 0 }} items
              </div>
              
              <div class="flex items-center space-x-1">
                <button class="text-stone-500 hover:text-stone-700 p-1.5 rounded-lg hover:bg-stone-50 transition-colors">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </button>
                <button class="text-stone-500 hover:text-stone-700 p-1.5 rounded-lg hover:bg-stone-50 transition-colors">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex items-center space-x-2">
              <button class="flex-1 bg-stone-100 hover:bg-stone-200 text-stone-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                View Details
              </button>
              <button class="bg-gradient-to-r from-stone-600 to-stone-700 hover:from-stone-700 hover:to-stone-800 text-white py-2 px-4 rounded-lg text-sm font-medium transition-all duration-200">
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-if="dresses.length === 0" class="text-center py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-8">
          <svg class="h-16 w-16 text-stone-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <h3 class="text-lg font-medium text-stone-900 mb-2">No dresses found</h3>
          <p class="text-stone-600 mb-4">Add your first dress to start building your boutique inventory</p>
          <button class="bg-gradient-to-r from-stone-600 to-stone-700 hover:from-stone-700 hover:to-stone-800 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
            Add Dress
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const dresses = ref([]);
const loading = ref(true);

onMounted(async () => {
  await loadDresses();
});

const loadDresses = async () => {
  try {
    const response = await window.axios.get('/dresses');
    dresses.value = response.data;
  } catch (error) {
    console.error('Error loading dresses:', error);
  } finally {
    loading.value = false;
  }
};
</script>
