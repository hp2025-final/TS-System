<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">QR Code Sticker Generator</h1>
      <p class="mt-2 text-sm text-gray-600">Upload a CSV file to generate printable QR code stickers for label thermal printer (2" × 1")</p>
    </div>

    <!-- Upload Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload CSV File</h2>

      <!-- CSV Format Instructions -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">CSV File Format</h3>
        <p class="text-sm text-blue-800 mb-2">Your CSV file must have the following columns in this exact order:</p>
        <div class="bg-white rounded p-3 font-mono text-xs overflow-x-auto">
          <code>Collection, Dress, SKU, Size, Sale_Price, QR_Code_Number</code>
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
            'border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer',
            dragOver ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 bg-gray-50'
          ]"
          @click="$refs.fileInput.click()"
        >
          <input
            ref="fileInput"
            type="file"
            accept=".csv"
            @change="handleFileSelect"
            class="hidden"
          />

          <div v-if="!selectedFile">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9.75m.75-9H9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-600 mb-2">Drag and drop your CSV file here, or</p>
            <span class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
              Browse Files
            </span>
          </div>

          <div v-else class="space-y-2" @click.stop>
            <div class="flex items-center justify-center space-x-3">
              <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</p>
                <p class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
              </div>
            </div>
            <button
              @click="clearFile"
              class="text-sm text-red-600 hover:text-red-800 font-medium"
            >
              Remove File
            </button>
          </div>
        </div>
      </div>

      <!-- Upload Button -->
      <div class="flex justify-end">
        <button
          @click="uploadFile"
          :disabled="!selectedFile || uploading"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="uploading" class="mr-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          {{ uploading ? 'Processing...' : 'Upload & Parse CSV' }}
        </button>
      </div>
    </div>

    <!-- Parse Results & Errors -->
    <div v-if="parseErrors.length > 0" class="bg-white shadow rounded-lg p-6 mb-8">
      <h2 class="text-xl font-semibold text-red-900 mb-4">Parsing Errors ({{ parseErrors.length }})</h2>
      <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-48 overflow-y-auto">
        <ul class="space-y-1">
          <li v-for="(error, i) in parseErrors" :key="i" class="text-sm text-red-800">{{ error }}</li>
        </ul>
      </div>
    </div>

    <!-- Sticker Preview & Download -->
    <div v-if="stickerItems.length > 0" class="bg-white shadow rounded-lg p-6 mb-8">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Sticker Preview</h2>
          <p class="text-sm text-gray-500 mt-1">{{ stickerItems.length }} stickers ready — {{ Math.ceil(stickerItems.length / 2) }} rows</p>
        </div>
        <button
          @click="downloadPDF"
          :disabled="generatingPdf"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors shadow-sm"
        >
          <span v-if="generatingPdf" class="mr-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          <svg v-else class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
          </svg>
          {{ generatingPdf ? 'Generating PDF...' : 'Download PDF' }}
        </button>
      </div>

      <!-- PDF Generation Progress -->
      <div v-if="generatingPdf" class="mb-6">
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-indigo-800">Generating stickers...</span>
            <span class="text-sm text-indigo-600">{{ pdfProgress }}%</span>
          </div>
          <div class="w-full bg-indigo-200 rounded-full h-2">
            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="{ width: pdfProgress + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Preview Grid -->
      <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 overflow-x-auto">
        <div class="space-y-3" style="min-width: 500px;">
          <div
            v-for="(row, rowIdx) in previewRows"
            :key="rowIdx"
            class="flex gap-3 justify-center"
          >
            <div
              v-for="(item, colIdx) in row"
              :key="colIdx"
              class="sticker-preview flex border border-gray-300 rounded bg-white shadow-sm"
              style="width: 288px; height: 144px;"
            >
              <!-- Left Side 55% -->
              <div class="flex flex-col justify-center px-3 py-2" style="width: 55%;">
                <p class="font-bold text-gray-900 leading-tight truncate" style="font-size: 11px;">Tasneem Shamim</p>
                <p class="font-semibold text-gray-800 leading-tight truncate mt-1" style="font-size: 10px;">{{ item.dress }} | {{ item.size }}</p>
                <p class="font-bold text-gray-900 leading-tight truncate mt-1" style="font-size: 10px;">Rs. {{ item.sale_price }}</p>
                <p class="text-gray-500 leading-tight truncate mt-0.5" style="font-size: 8px;">{{ item.collection }} | {{ item.sku }}</p>
                <p class="text-gray-500 leading-tight truncate mt-0.5" style="font-size: 8px;">{{ item.qr_code_number }}</p>
              </div>
              <!-- Right Side 45% -->
              <div class="flex items-center justify-center" style="width: 45%;">
                <canvas :ref="el => setPreviewCanvas(el, rowIdx, colIdx)" class="qr-preview"></canvas>
              </div>
            </div>
          </div>

          <!-- Show more indicator -->
          <div v-if="stickerItems.length > 10" class="text-center py-3">
            <p class="text-sm text-gray-500">
              Showing {{ Math.min(stickerItems.length, 10) }} of {{ stickerItems.length }} stickers in preview.
              All stickers will be included in the PDF.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import QRCode from 'qrcode';
import { jsPDF } from 'jspdf';

const router = useRouter();
const authStore = useAuthStore();

// Check admin access
onMounted(async () => {
  await authStore.checkAuth();
  if (authStore.user?.email !== 'admin@tspos.com') {
    router.push('/');
  }
});

// State
const selectedFile = ref(null);
const uploading = ref(false);
const dragOver = ref(false);
const fileInput = ref(null);
const stickerItems = ref([]);
const parseErrors = ref([]);
const generatingPdf = ref(false);
const pdfProgress = ref(0);

// Preview canvases registry
const previewCanvases = {};

const setPreviewCanvas = (el, rowIdx, colIdx) => {
  if (el) {
    const key = `${rowIdx}-${colIdx}`;
    previewCanvases[key] = el;
  }
};

// Computed: preview rows (max 10 items = 5 rows for preview)
const previewRows = computed(() => {
  const items = stickerItems.value.slice(0, 10);
  const rows = [];
  for (let i = 0; i < items.length; i += 2) {
    rows.push(items.slice(i, i + 2));
  }
  return rows;
});

// Watch for sticker items change to render QR codes in preview
watch(stickerItems, async (items) => {
  if (items.length > 0) {
    await nextTick();
    renderPreviewQRCodes();
  }
});

// Render QR codes into preview canvases
const renderPreviewQRCodes = async () => {
  const previewItems = stickerItems.value.slice(0, 10);
  let rowIdx = 0;
  let colIdx = 0;

  for (let i = 0; i < previewItems.length; i++) {
    rowIdx = Math.floor(i / 2);
    colIdx = i % 2;
    const key = `${rowIdx}-${colIdx}`;
    const canvas = previewCanvases[key];

    if (canvas) {
      try {
        await QRCode.toCanvas(canvas, String(previewItems[i].qr_code_number), {
          width: 100,
          margin: 1,
          color: {
            dark: '#000000',
            light: '#ffffff'
          },
          errorCorrectionLevel: 'H'
        });
      } catch (err) {
        console.error('QR preview error:', err);
      }
    }
  }
};

// File handling
const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file && (file.name.endsWith('.csv') || file.name.endsWith('.txt'))) {
    selectedFile.value = file;
    stickerItems.value = [];
    parseErrors.value = [];
  } else {
    alert('Please select a valid CSV file');
  }
};

const handleDrop = (event) => {
  dragOver.value = false;
  const file = event.dataTransfer.files[0];
  if (file && (file.name.endsWith('.csv') || file.name.endsWith('.txt'))) {
    selectedFile.value = file;
    stickerItems.value = [];
    parseErrors.value = [];
  } else {
    alert('Please drop a valid CSV file');
  }
};

const clearFile = () => {
  selectedFile.value = null;
  stickerItems.value = [];
  parseErrors.value = [];
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

// Upload and parse CSV
const uploadFile = async () => {
  if (!selectedFile.value) return;

  uploading.value = true;
  stickerItems.value = [];
  parseErrors.value = [];

  const formData = new FormData();
  formData.append('file', selectedFile.value);

  try {
    const response = await window.axios.post('/api/qr-stickers/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data.success) {
      stickerItems.value = response.data.results.items || [];
      parseErrors.value = response.data.results.errors || [];
    }
  } catch (error) {
    console.error('Upload error:', error);
    if (error.response?.data?.message) {
      alert(error.response.data.message);
    } else {
      alert('Upload failed. Please try again.');
    }
  } finally {
    uploading.value = false;
  }
};

// Download template
const downloadTemplate = async () => {
  try {
    const response = await window.axios.get('/api/qr-stickers/template', {
      responseType: 'blob'
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'qr_sticker_template.csv');
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Error downloading template:', error);
    alert('Failed to download template');
  }
};

// Generate and download PDF
const downloadPDF = async () => {
  if (stickerItems.value.length === 0) return;

  generatingPdf.value = true;
  pdfProgress.value = 0;

  try {
    // Sticker dimensions in mm (2" x 1") - for Zebra TLP 2844
    // Each label: 2" wide × 1" tall. Two labels fit side-by-side per page row.
    const stickerWidthMM  = 50.8;  // 2 inches
    const stickerHeightMM = 25.4;  // 1 inch

    // Page = exactly 2 labels wide × 1 label tall
    const pageWidthMM  = stickerWidthMM * 2;  // 101.6 mm = 4 inches
    const pageHeightMM = stickerHeightMM;       // 25.4  mm = 1 inch

    const doc = new jsPDF({
      orientation: 'landscape',
      unit: 'mm',
      format: [pageWidthMM, pageHeightMM]
    });

    const items      = stickerItems.value;
    const totalItems = items.length;
    const totalPages = Math.ceil(totalItems / 2);

    let itemIndex = 0;

    for (let page = 0; page < totalPages; page++) {
      if (page > 0) {
        doc.addPage([pageWidthMM, pageHeightMM]);
      }

      for (let col = 0; col < 2 && itemIndex < totalItems; col++) {
        const item = items[itemIndex];
        const x = col * stickerWidthMM;  // 0 or 50.8
        const y = 0;

        // ── Text zone: left 55% of label ──────────────────────────────
        const textZoneWidth = stickerWidthMM * 0.55; // ~27.9 mm
        const textX = x + 2.5;                        // 2.5 mm left padding
        let   textY = y + 5.0;                        // start 5.0 mm from top
        const lineH = 3.8;                            // line height (tighter to fit 5 lines @ 7pt+)

        // Company Name  (bold, 8pt)
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(8);
        doc.setTextColor(0, 0, 0);
        doc.text('Tasneem Shamim', textX, textY);
        textY += lineH;

        // Dress | Size  (bold, 7pt) — pure black for thermal clarity
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(7);
        doc.setTextColor(0, 0, 0);
        const dressSize      = `${item.dress} | ${item.size}`;
        const dressSizeTrunc = dressSize.length > 22 ? dressSize.substring(0, 22) + '..' : dressSize;
        doc.text(dressSizeTrunc, textX, textY);
        textY += lineH;

        // Rs. Price  (bold, 7pt)
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(7);
        doc.setTextColor(0, 0, 0);
        const priceStr = item.sale_price ? `Rs. ${item.sale_price}` : '';
        doc.text(priceStr, textX, textY);
        textY += lineH;

        // Collection | SKU  (normal, 7pt, pure black)
        // Raised from 5.5pt gray → 7pt black: thermal needs min 7pt to print sharply
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(7);
        doc.setTextColor(0, 0, 0);
        const collSku      = `${item.collection} | ${item.sku}`;
        const collSkuTrunc = collSku.length > 22 ? collSku.substring(0, 22) + '..' : collSku;
        doc.text(collSkuTrunc, textX, textY);
        textY += lineH;

        // QR Code Number  (bold helvetica, 7pt, pure black — courier is harder to read on thermal)
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(7);
        doc.setTextColor(0, 0, 0);
        doc.text(String(item.qr_code_number), textX, textY);

        // ── QR Code zone: right side of label ─────────────────────────
        // Anchor QR from each label's RIGHT edge so it never touches the gap.
        const qrSize       = stickerHeightMM - 6;    // 19.4 mm square
        const rightMargin  = 2.5;                     // 2.5 mm from label's right edge
        const qrX = x + stickerWidthMM - rightMargin - qrSize;   // anchored from right
        const qrY = y + (stickerHeightMM - qrSize) / 2;          // vertically centered

        try {
          // High-resolution source image (400 px) → renders crisply on thermal
          const qrDataUrl = await QRCode.toDataURL(String(item.qr_code_number), {
            width: 400,          // 400 px source – sharp when scaled to ~21 mm
            margin: 1,           // minimal quiet zone (kept for scannability)
            color: {
              dark:  '#000000',
              light: '#ffffff'
            },
            errorCorrectionLevel: 'H'  // highest (30% restore) – essential for thermal
          });

          doc.addImage(qrDataUrl, 'PNG', qrX, qrY, qrSize, qrSize);
        } catch (qrErr) {
          console.error('QR generation error for item:', item.qr_code_number, qrErr);
        }

        itemIndex++;

        // Progress
        pdfProgress.value = Math.round((itemIndex / totalItems) * 100);
      }
    }

    // Save PDF
    const timestamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
    doc.save(`QR_Stickers_${timestamp}.pdf`);

    pdfProgress.value = 100;

  } catch (error) {
    console.error('PDF generation error:', error);
    alert('Failed to generate PDF. Please try again.');
  } finally {
    setTimeout(() => {
      generatingPdf.value = false;
      pdfProgress.value = 0;
    }, 1000);
  }
};
</script>

<style scoped>
.sticker-preview {
  transition: box-shadow 0.2s;
}
.sticker-preview:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.qr-preview {
  max-width: 80px;
  max-height: 80px;
}
</style>
