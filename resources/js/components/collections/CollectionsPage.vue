<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Collections</h1>
          </div>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Collections</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.totalCollections }}</p>
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
              <p class="text-sm font-medium text-gray-600">Active Collections</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.activeCollections }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="p-2 bg-orange-100 rounded-lg">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Available Items</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statsData.totalItems }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Collections Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">All Collections</h3>
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
                  placeholder="Search collections..."
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
            </div>
          </div>
          
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Loading collections...
            </div>
          </div>
          
          <!-- Table -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Collection
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Dresses
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Available Items
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    XS
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    S
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    M
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    L
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    XL
                  </th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Unstitched
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
                <tr v-for="collection in filteredCollections" :key="collection.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                          <span class="text-white font-medium text-sm">{{ collection.name.charAt(0) }}</span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ collection.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="collection.status === 'active' 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ collection.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ collection.dresses_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="font-medium text-blue-600">{{ collection.total_items || 0 }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ collection.size_breakdown?.XS || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ collection.size_breakdown?.S || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ collection.size_breakdown?.M || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ collection.size_breakdown?.L || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ collection.size_breakdown?.XL || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ collection.size_breakdown?.unstitched || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span v-if="collection.discount_active" class="text-green-600 font-medium">
                      {{ collection.discount_percentage }}%
                    </span>
                    <span v-else class="text-gray-400">No discount</span>
                  </td>
                  <td v-if="isAdmin" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click="editCollection(collection)"
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
            <div v-if="filteredCollections.length === 0" class="text-center py-12">
              <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No collections found</h3>
              <p class="text-gray-500 mb-4">
                {{ searchTerm ? 'Try adjusting your search or filters.' : 'No collections found.' }}
              </p>
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
            {{ isEditing ? 'Edit Collection' : 'Create New Collection' }}
          </h3>
          <form @submit.prevent="submitForm">
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Collection Name</label>
              <input 
                v-model="formData.name"
                type="text" 
                id="name"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter collection name"
              />
            </div>
            
            <div class="mb-4">
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea 
                v-model="formData.description"
                id="description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter collection description"
              ></textarea>
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

const collections = ref([]);
const loading = ref(true);
const searchTerm = ref('');
const statusFilter = ref('');
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
  status: 'active',
  discount_percentage: 0,
  discount_active: false
});

const statsData = computed(() => {
  const total = collections.value.length;
  const active = collections.value.filter(c => c.status === 'active').length;
  const totalDresses = collections.value.reduce((sum, c) => sum + (c.dresses_count || 0), 0);
  const totalItems = collections.value.reduce((sum, c) => sum + (c.total_items || 0), 0);
  
  return {
    totalCollections: total,
    activeCollections: active,
    totalDresses,
    totalItems
  };
});

const filteredCollections = computed(() => {
  let filtered = collections.value;
  
  if (searchTerm.value) {
    filtered = filtered.filter(collection => 
      collection.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      (collection.description && collection.description.toLowerCase().includes(searchTerm.value.toLowerCase()))
    );
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(collection => collection.status === statusFilter.value);
  }
  
  return filtered;
});

const loadCollections = async () => {
  try {
    loading.value = true;
    const response = await window.axios.get('/api/collections');
    collections.value = Array.isArray(response.data) ? response.data : [];
  } catch (error) {
    console.error('Error loading collections:', error);
    collections.value = [];
  } finally {
    loading.value = false;
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  isEditing.value = false;
  editingId.value = null;
  formData.value = {
    name: '',
    description: '',
    status: 'active',
    discount_percentage: 0,
    discount_active: false
  };
};

const submitForm = async () => {
  try {
    saving.value = true;
    
    const payload = {
      name: formData.value.name,
      description: formData.value.description || null,
      status: formData.value.status,
      discount_percentage: formData.value.discount_percentage || 0,
      discount_active: formData.value.discount_active
    };
    
    if (isEditing.value) {
      await window.axios.put(`/api/collections/${editingId.value}`, payload);
    } else {
      await window.axios.post('/api/collections', payload);
    }
    
    await loadCollections();
    closeModal();
  } catch (error) {
    console.error('Error saving collection:', error);
    alert('Error saving collection. Please try again.');
  } finally {
    saving.value = false;
  }
};

const editCollection = (collection) => {
  isEditing.value = true;
  editingId.value = collection.id;
  formData.value = {
    name: collection.name,
    description: collection.description || '',
    status: collection.status,
    discount_percentage: parseFloat(collection.discount_percentage) || 0,
    discount_active: collection.discount_active
  };
  showCreateModal.value = true;
};

const viewCollection = (collection) => {
  alert(`View collection: ${collection.name}\nThis feature will be implemented soon.`);
};

const deleteCollection = async (collection) => {
  if (confirm(`Are you sure you want to delete "${collection.name}"?`)) {
    try {
      await window.axios.delete(`/api/collections/${collection.id}`);
      await loadCollections();
    } catch (error) {
      console.error('Error deleting collection:', error);
      alert('Error deleting collection. Please try again.');
    }
  }
};

onMounted(async () => {
  try {
    await authStore.checkAuth();
    await loadCollections();
  } catch (error) {
    console.error('Error during component initialization:', error);
    loading.value = false;
  }
});
</script>
