<template>
  <div class="max-w-xl">
    <h1 class="text-2xl font-semibold text-stone-800 mb-4">Backup Database</h1>
    <p class="text-stone-600 mb-6">Click the button below to generate and download a full database backup.</p>

    <button
      @click="downloadBackup"
      :disabled="loading"
      class="inline-flex items-center px-4 py-2 rounded-md bg-stone-800 text-white hover:bg-stone-900 disabled:opacity-60"
    >
      <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
      </svg>
      <span v-if="!loading">Backup Now</span>
      <span v-else>Backing up...</span>
    </button>

    <p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>
    <p v-if="success" class="mt-4 text-sm text-green-700">Backup started. If download didn't start, please try again.</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const loading = ref(false)
const error = ref('')
const success = ref(false)

const downloadBackup = async () => {
  error.value = ''
  success.value = false
  loading.value = true
  try {
    const response = await window.axios.get('/api/backup/download', { responseType: 'blob' })

    // Determine filename from header
    let filename = 'TS_Backup.sql'
    const disposition = response.headers['content-disposition']
    if (disposition) {
      const match = /filename\*=UTF-8''([^;]+)|filename="?([^";]+)"?/i.exec(disposition)
      const extracted = decodeURIComponent(match?.[1] || match?.[2] || '')
      if (extracted) filename = extracted
    }

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    success.value = true
  } catch (e) {
    error.value = e?.response?.data?.message || 'Backup failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
