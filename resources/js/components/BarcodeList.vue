<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Barcode List</h1>
      <p class="mt-2 text-sm text-gray-600">View and filter all barcode items</p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Date From -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
          <input
            v-model="filters.date_from"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Date To -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
          <input
            v-model="filters.date_to"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Collection -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Collection</label>
          <select
            v-model="filters.collection_id"
            @change="onCollectionChange"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">All Collections</option>
            <option v-for="collection in filterOptions.collections" :key="collection.id" :value="collection.id">
              {{ collection.name }}
            </option>
          </select>
        </div>

        <!-- Dress -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Dress</label>
          <select
            v-model="filters.dress_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">All Dresses</option>
            <option v-for="dress in filteredDresses" :key="dress.id" :value="dress.id">
              {{ dress.name }}
            </option>
          </select>
        </div>

        <!-- Status -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="filters.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">All Statuses</option>
            <option v-for="status in filterOptions.statuses" :key="status.value" :value="status.value">
              {{ status.label }}
            </option>
          </select>
        </div>
      </div>

      <!-- Filter Actions -->
      <div class="mt-4 flex justify-end space-x-3">
        <button
          @click="clearFilters"
          class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          Clear Filters
        </button>
        <button
          @click="applyFilters"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700"
        >
          Apply Filters
        </button>
        <button
          @click="exportToExcel"
          class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700"
        >
          Export to Excel
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      <p class="mt-2 text-sm text-gray-600">Loading...</p>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date (Created)</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date (Updated)</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date (Sold)</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date (Returned)</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="items.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                No items found
              </td>
            </tr>
            <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.created_at }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.updated_at }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ item.barcode }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(item.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ formatStatus(item.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">PKR {{ item.sale_price }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.sold_at || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.returned_at || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing page {{ pagination.current_page }} of {{ pagination.last_page }}
            ({{ pagination.total }} total items)
          </div>
          <div class="flex space-x-2">
            <button
              @click="goToPage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <button
              @click="goToPage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const loading = ref(false);
const items = ref([]);
const pagination = ref({
  total: 0,
  per_page: 50,
  current_page: 1,
  last_page: 1,
});

const filters = ref({
  date_from: '',
  date_to: '',
  collection_id: '',
  dress_id: '',
  status: '',
});

const filterOptions = ref({
  collections: [],
  dresses: [],
  statuses: [],
});

const filteredDresses = computed(() => {
  if (!filters.value.collection_id) {
    return filterOptions.value.dresses;
  }
  return filterOptions.value.dresses.filter(
    dress => dress.collection_id == filters.value.collection_id
  );
});

// Load filter options
const loadFilterOptions = async () => {
  try {
    const response = await window.axios.get('/api/barcode-list/filter-options');
    filterOptions.value = response.data;
  } catch (error) {
    console.error('Error loading filter options:', error);
  }
};

// Load items
const loadItems = async (page = 1) => {
  loading.value = true;
  
  try {
    const params = {
      ...filters.value,
      page,
    };
    
    const response = await window.axios.get('/api/barcode-list', { params });
    items.value = response.data.items;
    pagination.value = response.data.pagination;
  } catch (error) {
    console.error('Error loading items:', error);
  } finally {
    loading.value = false;
  }
};

// Apply filters
const applyFilters = () => {
  loadItems(1);
};

// Clear filters
const clearFilters = () => {
  filters.value = {
    date_from: '',
    date_to: '',
    collection_id: '',
    dress_id: '',
    status: '',
  };
  loadItems(1);
};

// Collection change handler
const onCollectionChange = () => {
  filters.value.dress_id = '';
};

// Pagination
const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadItems(page);
  }
};

// Export to Excel
const exportToExcel = async () => {
  try {
    const params = new URLSearchParams(filters.value).toString();
    const url = `/api/barcode-list/export?${params}`;
    window.open(url, '_blank');
  } catch (error) {
    console.error('Error exporting:', error);
    alert('Failed to export data');
  }
};

// Format status
const formatStatus = (status) => {
  const statusMap = {
    'available': 'Available',
    'sold': 'Sold',
    'returned_defective': 'Returned Defective',
    'returned_resaleable': 'Returned Resaleable',
    'damaged': 'Damaged',
    'retrieved_ho': 'Retrieved HO',
  };
  return statusMap[status] || status;
};

// Get status class
const getStatusClass = (status) => {
  const classMap = {
    'available': 'bg-green-100 text-green-800',
    'sold': 'bg-blue-100 text-blue-800',
    'returned_defective': 'bg-red-100 text-red-800',
    'returned_resaleable': 'bg-yellow-100 text-yellow-800',
    'damaged': 'bg-gray-100 text-gray-800',
    'retrieved_ho': 'bg-purple-100 text-purple-800',
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
};

// Initialize
onMounted(() => {
  loadFilterOptions();
  loadItems();
});
</script>
