<template>
  <div class="audit-page-container min-h-screen bg-stone-50 pb-20">
    <!-- Compact Header -->
    <div class="bg-white border-b border-stone-200 px-4 py-3 sm:px-6">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Inventory Audit</h1>
      <p class="mt-1 text-xs sm:text-sm text-gray-600">Scan QR codes to track dress items</p>
    </div>

    <!-- Compact Barcode Search Section -->
    <div class="bg-white border-b border-stone-200 px-4 py-3 sm:px-6">
      <div class="flex items-center gap-2">
        <div class="relative flex-1">
          <input 
            ref="barcodeInput"
            v-model="manualBarcode"
            type="text" 
            placeholder="Scan or enter barcode..."
            class="w-full px-3 py-2 sm:py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base"
            @input="handleBarcodeInput"
            :disabled="scanning"
          />
          <div class="absolute right-2 top-2 sm:top-2.5">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z" />
            </svg>
          </div>
        </div>
        <button 
          @click="startScanner" 
          class="px-3 py-2 sm:py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-1.5"
        >
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="hidden sm:inline text-sm font-medium">Camera</span>
        </button>
      </div>
      <p class="mt-2 text-xs text-gray-500">
        ⚡ Type or scan barcode - auto-submits after 500ms
      </p>
    </div>

    <!-- Success/Error Message (Compact Mobile) -->
    <div v-if="lastScanMessage" class="mx-4 mt-4 sm:mx-6 animate-fade-in">
      <div
        :class="[
          'rounded-lg p-3 sm:p-4 flex items-start border-2',
          lastScanSuccess ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300'
        ]"
      >
        <div
          :class="[
            'flex-shrink-0 rounded-full p-1.5 sm:p-2',
            lastScanSuccess ? 'bg-green-500' : 'bg-red-500'
          ]"
        >
          <svg
            class="h-4 w-4 sm:h-5 sm:w-5 text-white"
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
        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
          <h3 :class="['text-sm sm:text-base font-bold', lastScanSuccess ? 'text-green-900' : 'text-red-900']">
            {{ lastScanSuccess ? '✓ Scan Successful!' : '✗ Scan Failed' }}
          </h3>
          <p :class="['mt-1 text-xs sm:text-sm font-medium', lastScanSuccess ? 'text-green-800' : 'text-red-800']">
            {{ lastScanMessage }}
          </p>
        </div>
        <button 
          @click="clearMessage" 
          :class="[
            'ml-2 p-1 rounded-full transition-colors flex-shrink-0',
            lastScanSuccess ? 'text-green-600 hover:text-green-800 hover:bg-green-100' : 'text-red-600 hover:text-red-800 hover:bg-red-100'
          ]"
        >
          <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Scanner Modal (Mobile-Optimized) -->
    <div v-if="showScanner" class="scanner-modal fixed inset-0 bg-black bg-opacity-95 flex items-center justify-center z-50 p-0 sm:p-4">
      <div class="bg-white rounded-none sm:rounded-lg w-full h-full sm:h-auto sm:max-w-2xl sm:max-h-[90vh] overflow-y-auto flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b flex items-center justify-between bg-white sticky top-0 z-10">
          <h3 class="text-base sm:text-lg font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            </svg>
            Scanner
            <span v-if="isScanning" class="ml-auto text-green-600 text-xs sm:text-sm flex items-center gap-1">
              <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              Active
            </span>
          </h3>
          <button 
            @click="stopScanner" 
            class="p-2 hover:bg-gray-100 rounded-full transition-colors"
          >
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Scanner Area -->
        <div class="flex-1 flex items-center justify-center bg-black p-2 sm:p-4">
          <div class="scanner-container relative w-full max-w-full rounded-lg overflow-hidden">
            <video ref="scannerVideo" class="w-full h-auto min-h-[300px] sm:min-h-[400px] object-cover" autoplay playsinline muted></video>
            <canvas ref="scannerCanvas" class="hidden"></canvas>
            
            <!-- Optimized scanning overlay -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div class="relative w-4/5 max-w-sm h-32 sm:h-40">
                <!-- Corner indicators -->
                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-green-400"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-green-400"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-green-400"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-green-400"></div>
                
                <!-- Scanning line animation -->
                <div v-if="isScanning" class="absolute inset-0 overflow-hidden">
                  <div class="scan-line w-full h-0.5 bg-green-400 opacity-80"></div>
                </div>
                
                <!-- Center line guide -->
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-full h-0.5 bg-green-300 opacity-40"></div>
                </div>
              </div>
            </div>
            
            <!-- Status indicators -->
            <div v-if="isScanning" class="absolute top-3 left-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs sm:text-sm flex items-center gap-2">
              <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
              Scanning...
            </div>
            
            <!-- Torch button -->
            <div class="absolute top-3 right-3 flex gap-2">
              <button 
                v-if="canUseTorch"
                @click="toggleTorch"
                :class="torchEnabled ? 'bg-yellow-500' : 'bg-gray-700'"
                class="text-white px-2 py-1 rounded-full text-xs flex items-center gap-1"
              >
                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
            
            <!-- Instructions -->
            <div class="absolute bottom-3 left-3 right-3 bg-black bg-opacity-70 text-white p-2 sm:p-3 rounded text-center text-xs sm:text-sm">
              <div class="font-medium">Scan any barcode or QR code</div>
              <div class="text-xs opacity-80 mt-1">Position code within the frame</div>
            </div>
          </div>
        </div>
        
        <!-- Cancel Button -->
        <div class="p-4 bg-white border-t">
          <button 
            @click="stopScanner" 
            class="w-full px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Audit Statistics (Mobile Responsive) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 px-4 mt-4 sm:px-6">
      <div class="bg-white shadow rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center">
          <div class="flex-shrink-0 bg-indigo-500 rounded-md p-2 sm:p-3 mb-2 sm:mb-0">
            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z" />
            </svg>
          </div>
          <div class="sm:ml-3">
            <p class="text-xs font-medium text-gray-500">Total Scans</p>
            <p class="text-lg sm:text-xl font-semibold text-gray-900">{{ stats.total_scans || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center">
          <div class="flex-shrink-0 bg-green-500 rounded-md p-2 sm:p-3 mb-2 sm:mb-0">
            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="sm:ml-3">
            <p class="text-xs font-medium text-gray-500">Today</p>
            <p class="text-lg sm:text-xl font-semibold text-gray-900">{{ stats.today_scans || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center">
          <div class="flex-shrink-0 bg-blue-500 rounded-md p-2 sm:p-3 mb-2 sm:mb-0">
            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5" />
            </svg>
          </div>
          <div class="sm:ml-3">
            <p class="text-xs font-medium text-gray-500">Unique</p>
            <p class="text-lg sm:text-xl font-semibold text-gray-900">{{ stats.unique_items || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center">
          <div class="flex-shrink-0 bg-purple-500 rounded-md p-2 sm:p-3 mb-2 sm:mb-0">
            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
          </div>
          <div class="sm:ml-3">
            <p class="text-xs font-medium text-gray-500">Top</p>
            <p class="text-sm sm:text-base font-semibold text-gray-900 truncate">
              {{ stats.top_scanner?.scanned_by?.name?.split(' ')[0] || 'N/A' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Scans (Mobile Responsive) -->
    <div class="bg-white shadow rounded-lg mx-4 mt-4 sm:mx-6 mb-6">
      <div class="p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center gap-2">
            Recent Scans
            <span class="flex items-center text-xs text-gray-500">
              <span class="relative flex h-2 w-2 mr-1">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
              </span>
              Live
            </span>
          </h2>
          <div class="flex items-center gap-2">
            <button
              @click="exportToCSV"
              :disabled="recentScans.length === 0"
              class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 py-2 text-xs sm:text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
            >
              <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
              </svg>
              <span class="hidden sm:inline">Export CSV</span>
              <span class="sm:hidden">CSV</span>
            </button>
            <button
              @click="loadRecentScans"
              class="inline-flex items-center px-3 py-2 text-xs sm:text-sm text-indigo-600 hover:text-indigo-700 font-medium"
            >
              <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
              <span class="hidden sm:inline">Refresh</span>
            </button>
          </div>
        </div>

        <!-- Mobile Cards / Desktop Table -->
        <div class="space-y-3 sm:space-y-0">
          <!-- Mobile Card View -->
          <div class="block sm:hidden space-y-3">
            <div 
              v-for="scan in recentScans" 
              :key="scan.id"
              class="bg-stone-50 border border-stone-200 rounded-lg p-3"
            >
              <div class="flex justify-between items-start mb-2">
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ scan.dress_name }}</p>
                  <p class="text-xs text-gray-600 truncate">{{ scan.collection_name }}</p>
                </div>
                <button
                  @click="deleteAudit(scan.id)"
                  class="ml-2 p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors flex-shrink-0"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
              <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                  <span class="text-gray-500">Barcode:</span>
                  <span class="font-mono ml-1">{{ scan.barcode }}</span>
                </div>
                <div>
                  <span class="text-gray-500">Size:</span>
                  <span class="ml-1">{{ scan.size }}</span>
                </div>
                <div>
                  <span class="text-gray-500">Status:</span>
                  <span :class="getStatusBadgeClass(scan.status)" class="ml-1 px-1.5 py-0.5 rounded-full">
                    {{ scan.status }}
                  </span>
                </div>
                <div>
                  <span class="text-gray-500">Time:</span>
                  <span class="ml-1">{{ formatDateTime(scan.scan_date) }}</span>
                </div>
              </div>
              <div class="mt-2 pt-2 border-t border-stone-200 text-xs text-gray-600">
                <span class="text-gray-500">By:</span> {{ scan.scanned_by?.name || 'Unknown' }}
              </div>
            </div>
            <div v-if="recentScans.length === 0" class="text-center py-8 text-gray-500 text-sm">
              No scans yet. Start scanning to see results here.
            </div>
          </div>

          <!-- Desktop Table View -->
          <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dress</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scanned By</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useAuthStore } from '../stores/auth';
import { 
  BrowserMultiFormatReader, 
  NotFoundException, 
  DecodeHintType, 
  BarcodeFormat 
} from '@zxing/library';

const authStore = useAuthStore();

// Scanner state
const scannerVideo = ref(null);
const scannerCanvas = ref(null);
const barcodeInput = ref(null);
const showScanner = ref(false);
const scanning = ref(false);
const manualBarcode = ref('');
const codeReader = ref(null);
const isScanning = ref(false);
const torchEnabled = ref(false);
const canUseTorch = ref(false);
const currentStream = ref(null);
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
  
  // Focus barcode input on mount
  if (barcodeInput.value) {
    barcodeInput.value.focus();
  }
});

onBeforeUnmount(() => {
  stopScanner();
});

// Start scanner with ZXing
const startScanner = async () => {
  showScanner.value = true;
  isScanning.value = true;
  
  try {
    // Initialize ZXing code reader with optimized settings
    if (!codeReader.value) {
      codeReader.value = new BrowserMultiFormatReader();
      
      // Set decode hints for Code128 and QR codes
      const hints = new Map();
      hints.set(DecodeHintType.POSSIBLE_FORMATS, [
        BarcodeFormat.QR_CODE,
        BarcodeFormat.CODE_128
      ]);
      hints.set(DecodeHintType.TRY_HARDER, true);
      hints.set(DecodeHintType.ALSO_INVERTED, true);
      
      codeReader.value.hints = hints;
    }
    
    // Get available video devices
    const videoInputDevices = await codeReader.value.listVideoInputDevices();
    console.log('Available cameras:', videoInputDevices.length);
    
    // Prefer back camera for mobile devices
    let selectedDeviceId = undefined;
    if (videoInputDevices.length > 1) {
      const backCamera = videoInputDevices.find(device => 
        device.label.toLowerCase().includes('back') || 
        device.label.toLowerCase().includes('environment') ||
        device.label.toLowerCase().includes('rear')
      );
      if (backCamera) {
        selectedDeviceId = backCamera.deviceId;
        console.log('Using back camera:', backCamera.label);
      }
    }
    
    // Configure video constraints optimized for mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    const constraints = {
      video: {
        deviceId: selectedDeviceId ? { exact: selectedDeviceId } : undefined,
        width: isMobile ? { ideal: 1920, max: 4096 } : { ideal: 1280, max: 1920 },
        height: isMobile ? { ideal: 1080, max: 2160 } : { ideal: 720, max: 1080 },
        facingMode: selectedDeviceId ? undefined : { ideal: 'environment' },
        focusMode: { ideal: 'continuous' },
        exposureMode: { ideal: 'continuous' },
        zoom: { ideal: 1.0 },
        torch: false
      }
    };
    
    console.log('Using constraints:', constraints);
    
    // Start decoding
    await codeReader.value.decodeFromConstraints(
      constraints,
      scannerVideo.value,
      (result, err) => {
        if (result) {
          // Successfully scanned
          const scannedCode = result.getText();
          const format = result.getBarcodeFormat();
          console.log('Scanned code:', scannedCode, 'Format:', format);
          
          // Validate scanned code
          if (scannedCode && scannedCode.trim() && scannedCode.length >= 1) {
            let cleanCode = scannedCode.trim();
            
            // Set the cleaned code to manual input
            manualBarcode.value = cleanCode;
            
            // Provide haptic feedback on mobile
            if (navigator.vibrate) {
              navigator.vibrate([200, 100, 200]);
            }
            
            // Stop scanning and close modal
            stopScanner();
            
            // Trigger scan
            handleScan(cleanCode);
            
            // Show success message
            const formatName = format ? format.toString().replace('_', '-') : 'Code';
            console.log(`${formatName} scanned:`, cleanCode);
          }
          
        } else if (err && !(err instanceof NotFoundException)) {
          console.warn('Scanning error:', err);
        }
      }
    );
    
    // Store the stream for torch control
    if (scannerVideo.value && scannerVideo.value.srcObject) {
      currentStream.value = scannerVideo.value.srcObject;
      
      // Check if torch is available
      const track = currentStream.value.getVideoTracks()[0];
      if (track && track.getCapabilities && track.getCapabilities().torch) {
        canUseTorch.value = true;
      }
    }
    
    console.log('Scanner started successfully');
    
  } catch (err) {
    console.error('Error starting scanner:', err);
    let errorMessage = 'Failed to start camera. ';
    
    if (err.name === 'NotAllowedError') {
      errorMessage += 'Please allow camera access and try again.';
    } else if (err.name === 'NotFoundError') {
      errorMessage += 'No camera found on this device.';
    } else if (err.name === 'NotSupportedError') {
      errorMessage += 'Camera not supported in this browser.';
    } else if (err.name === 'NotReadableError') {
      errorMessage += 'Camera is being used by another application.';
    } else {
      errorMessage += 'Please ensure you have camera permissions.';
    }
    
    alert(errorMessage);
    showScanner.value = false;
    isScanning.value = false;
  }
};

// Stop scanner
const stopScanner = () => {
  showScanner.value = false;
  isScanning.value = false;
  torchEnabled.value = false;
  canUseTorch.value = false;
  
  // Stop ZXing scanner
  if (codeReader.value) {
    codeReader.value.reset();
  }
  
  // Stop current stream
  if (currentStream.value) {
    currentStream.value.getTracks().forEach(track => track.stop());
    currentStream.value = null;
  }
  
  // Clear video source
  if (scannerVideo.value) {
    scannerVideo.value.srcObject = null;
  }
};

// Toggle torch
const toggleTorch = async () => {
  if (!currentStream.value) return;
  
  const track = currentStream.value.getVideoTracks()[0];
  if (!track || !track.getCapabilities || !track.getCapabilities().torch) return;
  
  try {
    torchEnabled.value = !torchEnabled.value;
    await track.applyConstraints({
      advanced: [{ torch: torchEnabled.value }]
    });
    console.log('Torch toggled:', torchEnabled.value);
  } catch (err) {
    console.error('Failed to toggle torch:', err);
    torchEnabled.value = false;
  }
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

      // Auto-clear message after 3 seconds
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

// Format date time for mobile
const formatDateTime = (dateString) => {
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

/* Scanning line animation */
@keyframes scan {
  0% {
    transform: translateY(0);
  }
  100% {
    transform: translateY(100%);
  }
}

.scan-line {
  animation: scan 2s linear infinite;
}

/* Mobile optimizations */
@media (max-width: 640px) {
  /* Improve touch targets */
  button {
    min-height: 44px;
    touch-action: manipulation;
  }
  
  /* Prevent zoom on input focus */
  input {
    font-size: 16px !important;
  }
  
  /* Smooth scrolling */
  * {
    -webkit-overflow-scrolling: touch;
  }
}
</style>
