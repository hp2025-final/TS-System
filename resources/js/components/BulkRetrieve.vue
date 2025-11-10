<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Bulk Retrieve</h1>
      <p class="mt-2 text-sm text-gray-600">Upload CSV file with barcodes to mark items as retrieved to HO</p>
    </div>

    <!-- Upload Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Barcode CSV File</h2>
      
      <!-- CSV Format Instructions -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">CSV File Format</h3>
        <p class="text-sm text-blue-800 mb-2">Your CSV file must have a single column with header:</p>
        <div class="bg-white rounded p-3 font-mono text-xs overflow-x-auto">
          <code>Barcode</code>
        </div>
        <p class="text-sm text-blue-800 mt-2">Each row should contain one barcode number.</p>
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

      <!-- Retrieve Button -->
      <div class="flex justify-end">
        <button
          @click="retrieveItems"
          :disabled="!selectedFile || retrieving"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          <span v-if="retrieving" class="mr-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          {{ retrieving ? 'Processing...' : 'Retrieve Items' }}
        </button>
      </div>
    </div>

    <!-- Retrieve Results -->
    <div v-if="retrieveResults" class="bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Retrieve Results</h2>
      
      <!-- Success Message -->
      <div v-if="retrieveResults.success > 0" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <p class="text-green-800 font-medium">{{ successMessage }}</p>
      </div>

      <!-- Summary -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
          <p class="text-sm text-gray-600">Total Barcodes</p>
          <p class="text-2xl font-bold text-gray-900">{{ retrieveResults.total }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
          <p class="text-sm text-green-600">Successfully Retrieved</p>
          <p class="text-2xl font-bold text-green-700">{{ retrieveResults.success }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4">
          <p class="text-sm text-red-600">Not Found</p>
          <p class="text-2xl font-bold text-red-700">{{ retrieveResults.not_found }}</p>
        </div>
      </div>

      <!-- Not Found Barcodes List -->
      <div v-if="retrieveResults.not_found_barcodes && retrieveResults.not_found_barcodes.length > 0" class="mt-6">
        <h3 class="text-lg font-medium text-red-900 mb-3">Barcodes Not Found ({{ retrieveResults.not_found_barcodes.length }})</h3>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-96 overflow-y-auto">
          <table class="min-w-full divide-y divide-red-200">
            <thead>
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-red-900 uppercase">Row</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-red-900 uppercase">Barcode</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-red-900 uppercase">Message</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-red-200">
              <tr v-for="(item, index) in retrieveResults.not_found_barcodes" :key="index">
                <td class="px-4 py-2 text-sm text-red-800">{{ item.row }}</td>
                <td class="px-4 py-2 text-sm font-mono text-red-800">{{ item.barcode }}</td>
                <td class="px-4 py-2 text-sm text-red-800">{{ item.message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Errors List -->
      <div v-if="retrieveResults.errors && retrieveResults.errors.length > 0" class="mt-6">
        <h3 class="text-lg font-medium text-red-900 mb-3">Errors ({{ retrieveResults.errors.length }})</h3>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-96 overflow-y-auto">
          <ul class="space-y-2">
            <li v-for="(error, index) in retrieveResults.errors" :key="index" class="text-sm text-red-800">
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
});

const selectedFile = ref(null);
const retrieving = ref(false);
const retrieveResults = ref(null);
const dragOver = ref(false);
const fileInput = ref(null);

const successMessage = computed(() => {
  if (!retrieveResults.value) return '';
  
  let msg = `Retrieve completed successfully! ${retrieveResults.value.success} items retrieved`;
  if (retrieveResults.value.not_found > 0) {
    msg += `, ${retrieveResults.value.not_found} barcodes not found`;
  }
  return msg;
});

// Handle file selection
const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file && file.name.endsWith('.csv')) {
    selectedFile.value = file;
    retrieveResults.value = null;
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
    retrieveResults.value = null;
  } else {
    alert('Please drop a valid CSV file');
  }
};

// Clear selected file
const clearFile = () => {
  selectedFile.value = null;
  retrieveResults.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

// Retrieve items
const retrieveItems = async () => {
  if (!selectedFile.value) return;

  retrieving.value = true;
  retrieveResults.value = null;

  const formData = new FormData();
  formData.append('file', selectedFile.value);

  try {
    const response = await window.axios.post('/api/bulk-retrieve/retrieve', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    retrieveResults.value = response.data.results;
    
    // Clear file
    selectedFile.value = null;
    
  } catch (error) {
    console.error('Retrieve error:', error);
    
    if (error.response?.data?.results) {
      retrieveResults.value = error.response.data.results;
    } else {
      alert(error.response?.data?.message || 'Retrieve failed. Please try again.');
    }
  } finally {
    retrieving.value = false;
  }
};

// Download template
const downloadTemplate = async () => {
  try {
    const response = await window.axios.get('/api/bulk-retrieve/template', {
      responseType: 'blob'
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'bulk_retrieve_template.csv');
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
