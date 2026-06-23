<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Inventory Audit</h1>
      <p class="mt-2 text-sm text-gray-600">Scan QR codes to track and audit dress items</p>
    </div>

    <!-- QR Scanner Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">QR Code Scanner</h2>

      <!-- Scanner Container -->
      <div class="mb-6">
        <div class="relative border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50" style="height: 400px;">
          <!-- Video Preview -->
          <video
            ref="videoElement"
            v-show="scannerActive"
            class="absolute inset-0 w-full h-full object-cover"
            playsinline
          ></video>

          <!-- Scanning Overlay -->
          <div v-if="scannerActive" class="absolute inset-0 pointer-events-none">
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="border-4 border-green-500 rounded-lg" style="width: 250px; height: 250px;">
                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-green-500"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-green-500"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-green-500"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-green-500"></div>
              </div>
            </div>
            <p class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white bg-black bg-opacity-75 px-4 py-2 rounded-full text-sm font-medium">
              Position QR code within the frame
            </p>
          </div>

          <!-- Manual Input Option -->
          <div v-if="!scannerActive" class="absolute inset-0 flex flex-col items-center justify-center p-8">
            <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
            </svg>
            <p class="text-gray-600 mb-4">Camera not active</p>
            
            <!-- Manual Barcode Input -->
            <div class="w-full max-w-md">
              <label class="block text-sm font-medium text-gray-700 mb-2">Or Enter Barcode Manually</label>
              <div class="flex gap-2">
                <input
                  ref="barcodeInput"
                  v-model="manualBarcode"
                  type="text"
                  placeholder="Scan or type barcode..."
                  class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg"
                  @input="handleBarcodeInput"
                  :disabled="scanning"
                />
              </div>
              <p class="mt-2 text-xs text-gray-500">
                ⚡ Type or scan barcode - auto-submits when complete
              </p>
            </div>
          </div>

          <!-- Loading Indicator -->
          <div v-if="scanning" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 text-center">
              <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-700 font-medium">Processing scan...</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Scanner Controls -->
      <div class="flex justify-center gap-4">
        <button
          v-if="!scannerActive"
          @click="startScanner"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm"
        >
          <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
          </svg>
          Start Camera Scanner
        </button>
        <button
          v-else
          @click="stopScanner"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors shadow-sm"
        >
          <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
          </svg>
          Stop Camera
        </button>
      </div>
    </div>

    <!-- Success/Error Message -->
    <div v-if="lastScanMessage" class="mb-8 animate-fade-in">
      <div
        :class="[
          'rounded-lg p-4 flex items-start border-2',
          lastScanSuccess ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300'
        ]"
      >
        <div
          :class="[
            'flex-shrink-0 rounded-full p-2',
            lastScanSuccess ? 'bg-green-500' : 'bg-red-500'
          ]"
        >
          <svg
            class="h-6 w-6 text-white"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
          >
            <path
              v-if="lastScanSuccess"
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M5 13l4 4L19 7"
            />
            <path
              v-else
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </div>
        <div class="ml-4 flex-1">
          <h3 :class="['text-lg font-bold', lastScanSuccess ? 'text-green-900' : 'text-red-900']">
            {{ lastScanSuccess ? '✓ Scan Successful!' : '✗ Scan Failed' }}
          </h3>
          <p :class="['mt-1 text-sm font-medium', lastScanSuccess ? 'text-green-800' : 'text-red-800']">
            {{ lastScanMessage }}
          </p>
          <div v-if="lastScanSuccess && lastScanData" class="mt-3 bg-white rounded-lg p-4 shadow-sm border border-green-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Dress Name</p>
                <p class="text-sm font-bold text-gray-900">{{ lastScanData.dress?.name }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Collection</p>
                <p class="text-sm font-bold text-gray-900">{{ lastScanData.dress?.collection?.name }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Barcode</p>
                <p class="text-sm font-mono font-bold text-gray-900">{{ lastScanData.barcode }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Status</p>
                <span :class="getStatusBadgeClass(lastScanData.status)" class="inline-block text-sm">
                  {{ lastScanData.status }}
                </span>
              </div>
              <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Size / Color</p>
                <p class="text-sm font-semibold text-gray-900">{{ lastScanData.dress?.size }} / {{ lastScanData.dress?.color }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Price</p>
                <p class="text-sm font-semibold text-gray-900">Rs. {{ lastScanData.dress?.sale_price }}</p>
              </div>
            </div>
          </div>
        </div>
        <button 
          @click="clearMessage" 
          :class="[
            'ml-4 p-1 rounded-full transition-colors',
            lastScanSuccess ? 'text-green-600 hover:text-green-800 hover:bg-green-100' : 'text-red-600 hover:text-red-800 hover:bg-red-100'
          ]"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Audit Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500">Total Scans</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_scans || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500">Today's Scans</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.today_scans || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500">Unique Items</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.unique_items || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500">Top Scanner</p>
            <p class="text-lg font-semibold text-gray-900 truncate">
              {{ stats.top_scanner?.scanned_by?.name || 'N/A' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Scans -->
    <div class="bg-white shadow rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900">Recent Scans (Live Update)</h2>
        <div class="flex items-center gap-3">
          <button
            @click="exportToCSV"
            :disabled="recentScans.length === 0"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
          >
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Export CSV
          </button>
          <span class="flex items-center text-sm text-gray-500">
            <span class="relative flex h-3 w-3 mr-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            Live
          </span>
          <button
            @click="loadRecentScans"
            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center"
          >
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Refresh
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scan Time</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dress</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scanned By</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="audit in recentScans" 
              :key="audit.id" 
              class="hover:bg-gray-50 transition-colors"
              :class="{ 'bg-green-50': audit.id === lastScannedId }"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                {{ formatDate(audit.scan_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-indigo-600 font-bold">
                {{ audit.barcode }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ audit.collection_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                {{ audit.dress_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">
                {{ audit.size || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusBadgeClass(audit.status)">
                  {{ audit.status || 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ audit.scanned_by?.name || 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <button
                  @click="deleteAudit(audit.id)"
                  class="text-red-600 hover:text-red-900 transition-colors"
                  title="Delete audit record"
                >
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                  </svg>
                </button>
              </td>
            </tr>
            <tr v-if="recentScans.length === 0">
              <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                </svg>
                <p class="font-medium">No scans recorded yet</p>
                <p class="text-xs mt-1">Start scanning to see data here</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

// Scanner state
const videoElement = ref(null);
const barcodeInput = ref(null);
const scannerActive = ref(false);
const scanning = ref(false);
const manualBarcode = ref('');
const codeReader = ref(null);
const scannerControls = ref(null);
const barcodeInputTimeout = ref(null);

// Scan results
const lastScanMessage = ref('');
const lastScanSuccess = ref(false);
const lastScanData = ref(null);

// Data
const stats = ref({});
const recentScans = ref([]);
const lastScannedId = ref(null);

// Initialize
onMounted(async () => {
  await authStore.checkAuth();
  loadStats();
  loadRecentScans();
  
  // Initialize QR code reader
  const { BrowserQRCodeReader } = await import('@zxing/library');
  codeReader.value = new BrowserQRCodeReader();
});

onBeforeUnmount(() => {
  stopScanner();
});

// Start QR scanner
const startScanner = async () => {
  try {
    scannerActive.value = true;
    
    const videoInputDevices = await codeReader.value.listVideoInputDevices();
    
    if (videoInputDevices.length === 0) {
      alert('No camera found. Please use manual barcode entry.');
      scannerActive.value = false;
      return;
    }

    // Use the first camera (or back camera if available)
    const selectedDeviceId = videoInputDevices[0].deviceId;

    scannerControls.value = await codeReader.value.decodeFromVideoDevice(
      selectedDeviceId,
      videoElement.value,
      (result, error) => {
        if (result) {
          handleScan(result.getText());
        }
      }
    );
  } catch (error) {
    console.error('Scanner error:', error);
    alert('Failed to start camera. Please check permissions or use manual entry.');
    scannerActive.value = false;
  }
};

// Stop QR scanner
const stopScanner = () => {
  if (scannerControls.value) {
    scannerControls.value.stop();
    scannerControls.value = null;
  }
  if (codeReader.value) {
    codeReader.value.reset();
  }
  scannerActive.value = false;
};

// Handle scanned barcode
const handleScan = async (barcode) => {
  if (scanning.value || !barcode) return;

  scanning.value = true;
  lastScanMessage.value = '';
  lastScanSuccess.value = false;
  lastScanData.value = null;

  try {
    const response = await window.axios.post('/api/audit/scan', { barcode });

    if (response.data.success) {
      lastScanSuccess.value = true;
      lastScanMessage.value = `Successfully scanned barcode: ${barcode}`;
      lastScanData.value = response.data.data.dress_item;
      lastScannedId.value = response.data.data.audit.id;

      // Play success sound
      playSuccessSound();

      // Clear the manual input field
      manualBarcode.value = '';

      // Reload stats and recent scans
      await loadStats();
      await loadRecentScans();

      // Auto-clear message after 3 seconds for faster scanning
      setTimeout(() => {
        if (lastScanSuccess.value) {
          clearMessage();
        }
      }, 3000);

      // Focus back to input for next scan
      if (barcodeInput.value) {
        setTimeout(() => {
          barcodeInput.value.focus();
        }, 100);
      }
    }
  } catch (error) {
    console.error('Scan error:', error);
    lastScanSuccess.value = false;
    
    // Check for duplicate scan (409 conflict)
    if (error.response?.status === 409) {
      lastScanMessage.value = `⚠️ ${error.response.data.message}`;
      if (error.response.data.data?.scanned_at) {
        lastScanMessage.value += ` (at ${error.response.data.data.scanned_at})`;
      }
    } else if (error.response?.status === 404) {
      lastScanMessage.value = `Barcode "${barcode}" not found in inventory`;
    } else if (error.response?.data?.message) {
      lastScanMessage.value = error.response.data.message;
    } else {
      lastScanMessage.value = 'Failed to process scan. Please try again.';
    }
    
    // Play error sound
    playErrorSound();

    // Auto-clear error message after 3 seconds
    setTimeout(() => {
      if (!lastScanSuccess.value) {
        clearMessage();
      }
    }, 3000);

    // Clear input on error too
    manualBarcode.value = '';
    
    // Focus back to input
    if (barcodeInput.value) {
      setTimeout(() => {
        barcodeInput.value.focus();
      }, 100);
    }
  } finally {
    scanning.value = false;
  }
};

// Handle barcode input with auto-submit
const handleBarcodeInput = () => {
  // Clear existing timeout
  if (barcodeInputTimeout.value) {
    clearTimeout(barcodeInputTimeout.value);
  }

  // If barcode has content, wait 500ms after last keystroke then auto-submit
  if (manualBarcode.value && manualBarcode.value.length >= 3) {
    barcodeInputTimeout.value = setTimeout(() => {
      if (manualBarcode.value && !scanning.value) {
        handleScan(manualBarcode.value.trim());
      }
    }, 500); // Auto-submit after 500ms of no typing
  }
};

// Clear message
const clearMessage = () => {
  lastScanMessage.value = '';
  lastScanSuccess.value = false;
  lastScanData.value = null;
  lastScannedId.value = null;
};

// Manual barcode submission (for Enter key)
const submitManualBarcode = () => {
  if (manualBarcode.value.trim()) {
    // Clear timeout if exists
    if (barcodeInputTimeout.value) {
      clearTimeout(barcodeInputTimeout.value);
    }
    handleScan(manualBarcode.value.trim());
  }
};

// Load statistics
const loadStats = async () => {
  try {
    const response = await window.axios.get('/api/audit/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Failed to load stats:', error);
  }
};

// Load recent scans
const loadRecentScans = async () => {
  try {
    const response = await window.axios.get('/api/audit/recent', {
      params: { limit: 50 }
    });
    recentScans.value = response.data;
    
    // Clear highlight after 3 seconds
    if (lastScannedId.value) {
      setTimeout(() => {
        lastScannedId.value = null;
      }, 3000);
    }
  } catch (error) {
    console.error('Failed to load recent scans:', error);
  }
};

// Delete audit record
const deleteAudit = async (auditId) => {
  if (!confirm('Are you sure you want to delete this audit record?')) {
    return;
  }

  try {
    await window.axios.delete(`/api/audit/${auditId}`);
    
    // Remove from local array
    recentScans.value = recentScans.value.filter(audit => audit.id !== auditId);
    
    // Reload stats
    loadStats();
  } catch (error) {
    console.error('Failed to delete audit:', error);
    alert('Failed to delete audit record');
  }
};

// Export to CSV
const exportToCSV = async () => {
  try {
    const response = await window.axios.get('/api/audit/export', {
      responseType: 'blob'
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `audit_export_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Export failed:', error);
    alert('Failed to export audit data');
  }
};

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Status badge styling
const getStatusBadgeClass = (status) => {
  const baseClasses = 'px-2 py-1 text-xs font-medium rounded-full';
  
  switch (status) {
    case 'available':
    case 'returned_resaleable':
      return `${baseClasses} bg-green-100 text-green-800`;
    case 'sold':
      return `${baseClasses} bg-blue-100 text-blue-800`;
    case 'damaged':
      return `${baseClasses} bg-red-100 text-red-800`;
    case 'retrieved_ho':
      return `${baseClasses} bg-yellow-100 text-yellow-800`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`;
  }
};

// Audio feedback
const playSuccessSound = () => {
  const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzKT2va+hDQHHnbM9duPMw');
  audio.play().catch(() => {});
};

const playErrorSound = () => {
  const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzKT2va+hDQHHnbM9duPMw');
  audio.playbackRate = 0.5;
  audio.play().catch(() => {});
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>
