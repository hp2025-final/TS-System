<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-stone-900">Collections</h1>
          <p class="text-stone-600 mt-1">Manage your boutique collections with size breakdown</p>
        </div>
        <div class="flex items-center space-x-4">
          <button class="bg-gradient-to-r from-stone-600 to-stone-700 hover:from-stone-700 hover:to-stone-800 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
            <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Collection
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
        <span class="text-stone-600 font-medium">Loading collections...</span>
      </div>
    </div>
    
    <!-- Collections Table -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-200">
          <thead class="bg-stone-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Collection
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Total Dresses
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Available Items
              </th>
              <!-- Dynamic size columns -->
              <th 
                v-for="size in allSizes" 
                :key="size"
                class="px-3 py-3 text-center text-xs font-medium text-stone-500 uppercase tracking-wider"
              >
                {{ size }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-stone-200">
            <tr v-for="collection in collections" :key="collection.id" class="hover:bg-stone-50">
              <!-- Collection Name & Description -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-stone-400 to-stone-500 flex items-center justify-center">
                      <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-stone-900">{{ collection.name }}</div>
                    <div class="text-sm text-stone-500 truncate max-w-xs">{{ collection.description || 'No description' }}</div>
                  </div>
                </div>
              </td>
              
              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="collection.status === 'active' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ collection.status }}
                </span>
                <div v-if="collection.discount_active && collection.discount_percentage > 0" class="mt-1">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                    {{ collection.discount_percentage }}% OFF
                  </span>
                </div>
              </td>
              
              <!-- Total Dresses -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-900">
                {{ collection.dresses_count || 0 }}
              </td>
              
              <!-- Available Items -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-900">
                <span class="font-medium text-green-600">{{ collection.total_items || 0 }}</span>
                <span class="text-stone-500">/ {{ collection.dresses_count || 0 }}</span>
              </td>
              
              <!-- Size breakdown columns -->
              <td 
                v-for="size in allSizes" 
                :key="size"
                class="px-3 py-4 whitespace-nowrap text-center text-sm"
              >
                <div class="text-stone-900 font-medium">
                  {{ collection.size_breakdown && collection.size_breakdown[size] || 0 }}
                </div>
              </td>
              
              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <button class="text-stone-400 hover:text-stone-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                  </button>
                  <button class="text-stone-400 hover:text-stone-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Empty State -->
      <div v-if="collections.length === 0" class="text-center py-12">
        <svg class="h-16 w-16 text-stone-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="text-lg font-medium text-stone-900 mb-2">No collections found</h3>
        <p class="text-stone-600 mb-4">Create your first collection to organize your boutique inventory</p>
        <button class="bg-gradient-to-r from-stone-600 to-stone-700 hover:from-stone-700 hover:to-stone-800 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
          Create Collection
        </button>
      </div>
    </div>
    
    <!-- Summary Cards -->
    <div v-if="collections.length > 0" class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
      <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-stone-600">Total Collections</div>
            <div class="text-2xl font-bold text-stone-900">{{ collections.length }}</div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </div>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-stone-600">Total Dresses</div>
            <div class="text-2xl font-bold text-stone-900">{{ totalDresses }}</div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-stone-600">Available Items</div>
            <div class="text-2xl font-bold text-stone-900">{{ totalAvailableItems }}</div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-stone-600">Total Items</div>
            <div class="text-2xl font-bold text-stone-900">{{ totalItems }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const collections = ref([]);
const allSizes = ref([]);
const loading = ref(true);

const totalDresses = computed(() => {
  if (!Array.isArray(collections.value)) return 0;
  return collections.value.reduce((sum, collection) => sum + (collection.dresses_count || 0), 0);
});

const totalAvailableItems = computed(() => {
  if (!Array.isArray(collections.value)) return 0;
  return collections.value.reduce((sum, collection) => sum + (collection.total_items || 0), 0);
});

const totalItems = computed(() => {
  if (!Array.isArray(collections.value)) return 0;
  return collections.value.reduce((sum, collection) => sum + (collection.total_items || 0), 0);
});

onMounted(async () => {
  await loadCollections();
});

const loadCollections = async () => {
  try {
    const response = await window.axios.get('/api/collections');
    collections.value = Array.isArray(response.data) ? response.data : [];
    allSizes.value = ['XS', 'S', 'M', 'L', 'XL'];
  } catch (error) {
    console.error('Error loading collections:', error);
    collections.value = [];
  } finally {
    loading.value = false;
  }
};
</script>
