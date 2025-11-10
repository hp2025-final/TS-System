<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Dresses</h1>
          </div>
          <button
            @click="exportToExcel"
            class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700"
          >
            Export to Excel
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Dresses</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.totalDresses }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Active Dresses</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.activeDresses }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Available Items</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.totalItems }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-orange-100 rounded-lg">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Avg. Sale Price</p>
              <p class="text-2xl font-semibold text-gray-900">PKR {{ statsData.avgSalePrice }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Dresses Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">All Dresses</h3>
            <div class="flex items-center gap-4">
              <!-- Search -->
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input 
                  v-model="searchTerm"
                  type="text" 
                  placeholder="Search dresses..."
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
              
              <!-- Filter -->
              <select 
                v-model="statusFilter"
                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
              >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              
              <!-- Collection Filter -->
              <select 
                v-model="collectionFilter"
                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
              >
                <option value="">All Collections</option>
                <option v-for="collection in collections" :key="collection.id" :value="collection.id">
                  {{ collection.name }}
                </option>
              </select>
            </div>
          </div>
          
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Loading dresses...
            </div>
          </div>
          
          <!-- Table -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Dress
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Collection
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    SKU
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Size
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Available Items
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Sale Price
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    HS Code
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Discount
                  </th>
                  <th v-if="isAdmin" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="dress in filteredDresses" :key="dress.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                          <span class="text-white font-medium text-sm">{{ (dress.name || 'U').charAt(0) }}</span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ dress.name || 'Unknown' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ dress.collection?.name || 'No Collection' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                    {{ dress.sku || 'N/A' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ dress.size || 'N/A' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="dress.status === 'active' 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ dress.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="font-medium text-blue-600">
                      {{ dress.dress_items_count || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="font-medium text-green-600">
                      PKR {{ dress.sale_price?.toLocaleString() || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                    {{ dress.hs_code || 'N/A' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span v-if="dress.discount_active && dress.discount_percentage > 0" class="text-green-600 font-medium">
                      {{ dress.discount_percentage }}%
                    </span>
                    <span v-else class="text-gray-400">No discount</span>
                  </td>
                  <td v-if="isAdmin" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click="editDress(dress)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded"
                      title="Edit"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            
            <!-- Empty State -->
            <div v-if="filteredDresses.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No dresses found</h3>
              <p class="mt-1 text-sm text-gray-500">No dresses match your current search criteria.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEditing ? 'Edit Dress' : 'Create New Dress' }}
          </h3>
          <form @submit.prevent="saveDress">
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
              <input 
                v-model="formData.name"
                type="text" 
                id="name"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter dress name"
              />
            </div>
            
            <div class="mb-4">
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea 
                v-model="formData.description"
                id="description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter dress description"
              ></textarea>
            </div>
            
            <div class="mb-4">
              <label for="collection_id" class="block text-sm font-medium text-gray-700 mb-2">Collection</label>
              <select 
                v-model="formData.collection_id"
                id="collection_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Select a collection</option>
                <option v-for="collection in collections" :key="collection.id" :value="collection.id">
                  {{ collection.name }}
                </option>
              </select>
            </div>
            
            <div class="mb-4">
              <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Size</label>
              <select 
                v-model="formData.size"
                id="size"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Select size</option>
                <option value="XS">XS</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
                <option value="unstitched">Unstitched</option>
              </select>
            </div>
            
            <div class="mb-4">
              <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
              <input 
                v-model="formData.sku"
                type="text" 
                id="sku"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter SKU"
              />
            </div>
            
            <div class="mb-4">
              <label for="hs_code" class="block text-sm font-medium text-gray-700 mb-2">HS Code</label>
              <input 
                v-model="formData.hs_code"
                type="text" 
                id="hs_code"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter HS Code"
              />
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">Cost Price</label>
                <input 
                  v-model="formData.cost_price"
                  type="number" 
                  id="cost_price"
                  min="0"
                  step="0.01"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="0.00"
                />
              </div>
              <div>
                <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Sale Price</label>
                <input 
                  v-model="formData.sale_price"
                  type="number" 
                  id="sale_price"
                  min="0"
                  step="0.01"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="0.00"
                />
              </div>
            </div>
            
            <div class="mb-4">
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select 
                v-model="formData.status"
                id="status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            
            <div class="mb-4">
              <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage</label>
              <input 
                v-model="formData.discount_percentage"
                type="number" 
                id="discount"
                min="0"
                max="100"
                step="0.01"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="0.00"
              />
            </div>
            
            <div class="mb-6">
              <label class="flex items-center">
                <input 
                  v-model="formData.discount_active"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                />
                <span class="ml-2 text-sm text-gray-600">Enable discount</span>
              </label>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button 
                @click="closeModal"
                type="button"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
              >
                Cancel
              </button>
              <button 
                type="submit"
                :disabled="saving"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {{ saving ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../../stores/auth.js';

const authStore = useAuthStore();

const dresses = ref([]);
const collections = ref([]);
const loading = ref(true);
const searchTerm = ref('');
const statusFilter = ref('');
const collectionFilter = ref('');
const showCreateModal = ref(false);
const isEditing = ref(false);
const saving = ref(false);
const editingId = ref(null);

// Role-based access control
const isAdmin = computed(() => {
  try {
    const user = authStore.user;
    const isAuthenticated = authStore.isAuthenticated;
    
    if (!isAuthenticated || !user || !user.role) {
      return false;
    }
    
    return user.role.toLowerCase() === 'admin';
  } catch (error) {
    console.error('Error in isAdmin computed:', error);
    return false;
  }
});

const formData = ref({
  name: '',
  description: '',
  collection_id: '',
  size: '',
  sku: '',
  hs_code: '',
  cost_price: '',
  sale_price: '',
  status: 'active',
  discount_percentage: 0,
  discount_active: false
});

const statsData = computed(() => {
  if (!Array.isArray(dresses.value)) {
    return {
      totalDresses: 0,
      activeDresses: 0,
      totalItems: 0,
      avgSalePrice: '0'
    };
  }
  
  const total = dresses.value.length;
  const active = dresses.value.filter(d => d.status === 'active').length;
  const totalItems = dresses.value.reduce((sum, d) => sum + (d.dress_items_count || 0), 0);
  const avgPrice = dresses.value.length > 0 
    ? Math.round(dresses.value.reduce((sum, d) => sum + (d.sale_price || 0), 0) / dresses.value.length)
    : 0;
    
  return {
    totalDresses: total,
    activeDresses: active,
    totalItems: totalItems,
    avgSalePrice: avgPrice.toLocaleString()
  };
});

const filteredDresses = computed(() => {
  if (!Array.isArray(dresses.value)) {
    return [];
  }
  
  let filtered = dresses.value;
  
  if (searchTerm.value) {
    filtered = filtered.filter(dress => {
      const name = dress.name || '';
      const sku = dress.sku || '';
      return name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
             sku.toLowerCase().includes(searchTerm.value.toLowerCase());
    });
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(dress => dress.status === statusFilter.value);
  }
  
  if (collectionFilter.value) {
    filtered = filtered.filter(dress => dress.collection_id == collectionFilter.value);
  }
  
  return filtered;
});

const loadDresses = async () => {
  try {
    loading.value = true;
    const response = await window.axios.get('/api/dresses');
    dresses.value = response.data;
  } catch (error) {
    console.error('Error loading dresses:', error);
  } finally {
    loading.value = false;
  }
};

const loadCollections = async () => {
  try {
    const response = await window.axios.get('/api/collections');
    collections.value = response.data;
  } catch (error) {
    console.error('Error loading collections:', error);
  }
};

const editDress = (dress) => {
  if (!isAdmin.value) return;
  
  isEditing.value = true;
  editingId.value = dress.id;
  formData.value = {
    name: dress.name,
    description: dress.description || '',
    collection_id: dress.collection_id,
    size: dress.size,
    sku: dress.sku,
    hs_code: dress.hs_code || '',
    cost_price: dress.cost_price,
    sale_price: dress.sale_price,
    status: dress.status,
    discount_percentage: dress.discount_percentage || 0,
    discount_active: dress.discount_active || false
  };
  showCreateModal.value = true;
};

const viewDress = (dress) => {
  // Implement view functionality
  console.log('View dress:', dress);
};

const deleteDress = async (dress) => {
  if (!isAdmin.value) return;
  
  if (confirm(`Are you sure you want to delete "${dress.name}"?`)) {
    try {
      await window.axios.delete(`/api/dresses/${dress.id}`);
      await loadDresses();
    } catch (error) {
      console.error('Error deleting dress:', error);
    }
  }
};

const saveDress = async () => {
  try {
    saving.value = true;
    
    if (isEditing.value) {
      await window.axios.put(`/api/dresses/${editingId.value}`, formData.value);
    } else {
      await window.axios.post('/api/dresses', formData.value);
    }
    
    closeModal();
    await loadDresses();
  } catch (error) {
    console.error('Error saving dress:', error);
  } finally {
    saving.value = false;
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  isEditing.value = false;
  editingId.value = null;
  formData.value = {
    name: '',
    description: '',
    collection_id: '',
    size: '',
    sku: '',
    hs_code: '',
    cost_price: '',
    sale_price: '',
    status: 'active',
    discount_percentage: 0,
    discount_active: false
  };
};

const exportToExcel = () => {
  window.open('/api/dresses-export', '_blank');
};

onMounted(async () => {
  await Promise.all([loadDresses(), loadCollections()]);
});
</script>
