<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Bulk Upload</h1>
      <p class="mt-2 text-sm text-gray-600">Upload CSV file to add multiple items to inventory</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Collections</dt>
                <dd class="text-2xl font-bold text-gray-900">{{ stats.total_collections || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Dresses</dt>
                <dd class="text-2xl font-bold text-gray-900">{{ stats.total_dresses || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                <dd class="text-2xl font-bold text-gray-900">{{ stats.total_dress_items || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Available</dt>
                <dd class="text-2xl font-bold text-green-600">{{ stats.available_items || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="w-full">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Sold</dt>
                <dd class="text-2xl font-bold text-blue-600">{{ stats.sold_items || 0 }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload CSV File</h2>
      
      <!-- CSV Format Instructions -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">CSV File Format</h3>
        <p class="text-sm text-blue-800 mb-2">Your CSV file must have the following columns in this exact order:</p>
        <div class="bg-white rounded p-3 font-mono text-xs overflow-x-auto">
          <code>Collection_Name, Dress_Name, SKU, Dress_Item_Barcode, Cost_Price, Sale_Price, Collection_Discount_%, Dress_Discount_%, Dress_Item_Discount_%</code>
        </div>
        <div class="mt-3">
          <button
            @click="downloadTemplate"
            class="text-sm font-medium text-blue-700 hover:text-blue-900 underline"
          >
            Download Sample Template
          </button>
        </div>
      </div>

      <!-- File Upload Area -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Select CSV File
        </label>
        <div
          @dragover.prevent="dragOver = true"
          @dragleave.prevent="dragOver = false"
          @drop.prevent="handleDrop"
          :class="[
            'border-2 border-dashed rounded-lg p-8 text-center transition-colors',
            dragOver ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 bg-gray-50'
          ]"
        >
          <input
            ref="fileInput"
            type="file"
            accept=".csv"
            @change="handleFileSelect"
            class="hidden"
          />
          
          <div v-if="!selectedFile">
            <p class="text-gray-600 mb-2">Drag and drop your CSV file here, or</p>
            <button
              @click="$refs.fileInput.click()"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
            >
              Browse Files
            </button>
          </div>
          
          <div v-else class="space-y-2">
            <p class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</p>
            <p class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
            <button
              @click="clearFile"
              class="text-sm text-red-600 hover:text-red-800"
            >
              Remove
            </button>
          </div>
        </div>
      </div>

      <!-- Upload Button -->
      <div class="flex justify-end">
        <button
          @click="uploadFile"
          :disabled="!selectedFile || uploading"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          <span v-if="uploading" class="mr-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          {{ uploading ? 'Uploading...' : 'Upload CSV' }}
        </button>
      </div>
    </div>

    <!-- Upload Results -->
    <div v-if="uploadResults" class="bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Results</h2>
      
      <!-- Success Message -->
      <div v-if="uploadResults.success > 0" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <p class="text-green-800 font-medium">{{ successMessage }}</p>
      </div>

      <!-- Success/Error Summary -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
          <p class="text-sm text-gray-600">Total Rows</p>
          <p class="text-2xl font-bold text-gray-900">{{ uploadResults.total }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
          <p class="text-sm text-green-600">Created</p>
          <p class="text-2xl font-bold text-green-700">{{ uploadResults.success }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4">
          <p class="text-sm text-yellow-600">Skipped</p>
          <p class="text-2xl font-bold text-yellow-700">{{ uploadResults.skipped }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4">
          <p class="text-sm text-red-600">Failed</p>
          <p class="text-2xl font-bold text-red-700">{{ uploadResults.failed }}</p>
        </div>
      </div>

      <!-- Created Items Summary -->
      <div v-if="uploadResults.created" class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-3">Items Created</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-blue-600">Collections</p>
            <p class="text-xl font-bold text-blue-700">{{ uploadResults.created.collections }}</p>
          </div>
          <div class="bg-purple-50 rounded-lg p-4">
            <p class="text-sm text-purple-600">Dresses</p>
            <p class="text-xl font-bold text-purple-700">{{ uploadResults.created.dresses }}</p>
          </div>
          <div class="bg-indigo-50 rounded-lg p-4">
            <p class="text-sm text-indigo-600">Dress Items</p>
            <p class="text-xl font-bold text-indigo-700">{{ uploadResults.created.dress_items }}</p>
          </div>
        </div>
      </div>

      <!-- Existing Items Report -->
      <div v-if="uploadResults.existing && uploadResults.existing.length > 0" class="mt-6">
        <h3 class="text-lg font-medium text-yellow-900 mb-3">Existing Items (Skipped) - {{ uploadResults.existing.length }}</h3>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-h-96 overflow-y-auto">
          <table class="min-w-full divide-y divide-yellow-200">
            <thead>
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-900 uppercase">Row</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-900 uppercase">Barcode</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-900 uppercase">Collection</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-900 uppercase">Dress</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-900 uppercase">SKU</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-900 uppercase">Message</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-yellow-200">
              <tr v-for="(item, index) in uploadResults.existing" :key="index">
                <td class="px-4 py-2 text-sm text-yellow-800">{{ item.row }}</td>
                <td class="px-4 py-2 text-sm font-mono text-yellow-800">{{ item.barcode }}</td>
                <td class="px-4 py-2 text-sm text-yellow-800">{{ item.collection }}</td>
                <td class="px-4 py-2 text-sm text-yellow-800">{{ item.dress }}</td>
                <td class="px-4 py-2 text-sm font-mono text-yellow-800">{{ item.sku }}</td>
                <td class="px-4 py-2 text-sm text-yellow-800">{{ item.message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Errors List -->
      <div v-if="uploadResults.errors && uploadResults.errors.length > 0" class="mt-6">
        <h3 class="text-lg font-medium text-red-900 mb-3">Errors ({{ uploadResults.errors.length }})</h3>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-96 overflow-y-auto">
          <ul class="space-y-2">
            <li v-for="(error, index) in uploadResults.errors" :key="index" class="text-sm text-red-800">
              {{ error }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

// Check if user is admin
onMounted(async () => {
  await authStore.checkAuth();
  
  if (authStore.user?.email !== 'admin@tspos.com') {
    router.push('/');
  }
  
  loadStats();
});

const stats = ref({});
const selectedFile = ref(null);
const uploading = ref(false);
const uploadResults = ref(null);
const dragOver = ref(false);
const fileInput = ref(null);

const successMessage = computed(() => {
  if (!uploadResults.value) return '';
  
  let msg = `Upload completed successfully! ${uploadResults.value.success} items created`;
  if (uploadResults.value.skipped > 0) {
    msg += `, ${uploadResults.value.skipped} skipped (already exist)`;
  }
  if (uploadResults.value.failed > 0) {
    msg += `, ${uploadResults.value.failed} failed`;
  }
  return msg;
});

// Load statistics
const loadStats = async () => {
  try {
    const response = await window.axios.get('/api/bulk-upload/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Error loading stats:', error);
  }
};

// Handle file selection
const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file && file.name.endsWith('.csv')) {
    selectedFile.value = file;
    uploadResults.value = null;
  } else {
    alert('Please select a valid CSV file');
  }
};

// Handle drag and drop
const handleDrop = (event) => {
  dragOver.value = false;
  const file = event.dataTransfer.files[0];
  if (file && file.name.endsWith('.csv')) {
    selectedFile.value = file;
    uploadResults.value = null;
  } else {
    alert('Please drop a valid CSV file');
  }
};

// Clear selected file
const clearFile = () => {
  selectedFile.value = null;
  uploadResults.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

// Upload file
const uploadFile = async () => {
  if (!selectedFile.value) return;

  uploading.value = true;
  uploadResults.value = null;

  const formData = new FormData();
  formData.append('file', selectedFile.value);

  try {
    const response = await window.axios.post('/api/bulk-upload/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    uploadResults.value = response.data.results;
    
    // Reload stats
    await loadStats();
    
    // Clear file
    selectedFile.value = null;
    
  } catch (error) {
    console.error('Upload error:', error);
    
    if (error.response?.data?.results) {
      uploadResults.value = error.response.data.results;
    } else {
      alert(error.response?.data?.message || 'Upload failed. Please try again.');
    }
  } finally {
    uploading.value = false;
  }
};

// Download template
const downloadTemplate = async () => {
  try {
    const response = await window.axios.get('/api/bulk-upload/template', {
      responseType: 'blob'
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'bulk_upload_template.csv');
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Error downloading template:', error);
    alert('Failed to download template');
  }
};

// Format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};
</script>
