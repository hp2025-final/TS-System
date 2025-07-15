<template>
  <div id="app" class="min-h-screen bg-stone-50 font-sans">
    <!-- Mobile & Tablet Header -->
    <header v-if="authStore.isAuthenticated" class="lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-between bg-white border-b border-stone-200 px-4 py-3 shadow-sm">
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-stone-600 hover:text-stone-900 p-2 rounded-md touch-manipulation">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <div class="flex items-center space-x-2">
        <img src="/company-logo.png" alt="Company Logo" class="h-16 w-auto max-w-none" style="height: 48px !important; width: auto !important;">
      </div>
      <div class="h-9 w-9 rounded-full bg-stone-200 flex items-center justify-center text-stone-700 font-bold text-base">
        {{ (authStore.user?.name || 'U')[0].toUpperCase() }}
      </div>
    </header>

    <!-- Mobile & Tablet Sidebar Overlay -->
    <transition name="fade">
      <div v-if="mobileMenuOpen" class="fixed inset-0 z-40 flex lg:hidden">
        <div class="fixed inset-0 bg-black bg-opacity-30" @click="mobileMenuOpen = false"></div>
        <aside class="relative flex flex-col w-64 sm:w-72 md:w-80 max-w-full bg-white border-r border-stone-200 shadow-lg z-50 h-full">
          <div class="flex items-center h-20 px-6 border-b border-stone-100">
            <img src="/company-logo.png" alt="Company Logo" class="h-16 w-auto max-w-none" style="height: 64px !important; width: auto !important;">
          </div>
          <nav class="flex-1 py-6 px-4 space-y-1 overflow-y-auto">
            <router-link
              v-for="item in navigationItems"
              :key="item.name"
              :to="item.path"
              @click="mobileMenuOpen = false"
              :class=" [
                $route.path === item.path
                  ? 'bg-stone-100 text-stone-900 font-semibold shadow-sm'
                  : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900 active:bg-stone-100',
                'flex items-center px-4 py-3 rounded-lg transition-all duration-150 text-base touch-manipulation min-h-[48px]',
              ]"
            >
              <span class="mr-3 h-6 w-6 flex-shrink-0" v-html="getIcon(item.icon)"></span>
              <span class="truncate">{{ item.label }}</span>
            </router-link>
          </nav>
          <div class="mt-auto px-6 py-4 border-t border-stone-100">
            <div class="flex items-center">
              <div class="h-10 w-10 rounded-full bg-stone-200 flex items-center justify-center text-stone-700 font-bold text-base flex-shrink-0">
                {{ (authStore.user?.name || 'U')[0].toUpperCase() }}
              </div>
              <div class="ml-3 min-w-0 flex-1">
                <div class="text-base font-medium text-stone-800 truncate">{{ authStore.user?.name || 'User' }}</div>
                <div class="text-xs text-stone-500">Staff</div>
              </div>
            </div>
            <button
              @click="logout"
              class="mt-4 w-full flex items-center justify-center px-4 py-3 text-base font-medium text-stone-600 hover:text-stone-900 hover:bg-stone-100 active:bg-stone-200 rounded-lg transition-all duration-150 touch-manipulation min-h-[48px]"
            >
              <span class="mr-2 h-5 w-5" v-html="getIcon('logout')"></span>
              Logout
            </button>
          </div>
        </aside>
      </div>
    </transition>

    <!-- Desktop Layout -->
    <div v-if="authStore.isAuthenticated" class="flex h-screen">
      <!-- Desktop Sidebar -->
      <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-stone-200 shadow-sm">
        <div class="flex items-center h-20 px-6 border-b border-stone-100">
          <img src="/company-logo.png" alt="Company Logo" class="h-16 w-auto max-w-none" style="height: 64px !important; width: auto !important;">
        </div>
        <nav class="flex-1 py-6 px-4 space-y-1 overflow-y-auto">
          <router-link
            v-for="item in navigationItems"
            :key="item.name"
            :to="item.path"
            :class=" [
              $route.path === item.path
                ? 'bg-stone-100 text-stone-900 font-semibold shadow-sm'
                : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900',
              'flex items-center px-4 py-3 rounded-lg transition-all duration-150 text-base',
            ]"
          >
            <span class="mr-3 h-6 w-6 flex-shrink-0" v-html="getIcon(item.icon)"></span>
            <span class="truncate">{{ item.label }}</span>
          </router-link>
        </nav>
        <div class="mt-auto px-6 py-4 border-t border-stone-100">
          <div class="flex items-center">
            <div class="h-10 w-10 rounded-full bg-stone-200 flex items-center justify-center text-stone-700 font-bold text-base flex-shrink-0">
              {{ (authStore.user?.name || 'U')[0].toUpperCase() }}
            </div>
            <div class="ml-3 min-w-0 flex-1">
              <div class="text-base font-medium text-stone-800 truncate">{{ authStore.user?.name || 'User' }}</div>
              <div class="text-xs text-stone-500">Staff</div>
            </div>
          </div>
          <button
            @click="logout"
            class="mt-4 w-full flex items-center justify-center px-4 py-2 text-base font-medium text-stone-600 hover:text-stone-900 hover:bg-stone-100 rounded-lg transition-all duration-150"
          >
            <span class="mr-2 h-5 w-5" v-html="getIcon('logout')"></span>
            Logout
          </button>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Mobile content with top padding for header -->
        <div class="flex-1 lg:pt-0 pt-16 overflow-y-auto">
          <div class="h-full p-4 sm:p-6 lg:p-8">
            <router-view />
          </div>
        </div>
      </main>
    </div>

    <!-- Login Layout -->
    <div v-if="!authStore.isAuthenticated" class="min-h-screen flex items-center justify-center p-4">
      <div class="w-full max-w-md">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const mobileMenuOpen = ref(false);

const getIcon = (iconName) => {
  const icons = {
    dashboard: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 018.25 20.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6A2.25 2.25 0 0115.75 3.75h2.25A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM13.5 15.75A2.25 2.25 0 0115.75 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z'/></svg>`,
    pos: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z'/></svg>`,
    collections: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 7.125A2.25 2.25 0 014.5 4.875h15A2.25 2.25 0 0121.75 7.125v1.562a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 8.687V7.125zM12 1.5a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0112 1.5zM12 12.75a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5a.75.75 0 01.75-.75zM5.25 12.75a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5a.75.75 0 01.75-.75zM18.75 12.75a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5a.75.75 0 01.75-.75z'/></svg>`,
    dresses: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.433 2.433c-1.211.06-2.36-.94-2.36-2.164 0-1.223 1.043-2.223 2.36-2.223h1.088c.482 0 .964.07 1.423.209m6.19-1.5-1.088-1.088a1.5 1.5 0 010-2.121l1.088-1.088a1.5 1.5 0 012.121 0l1.088 1.088a1.5 1.5 0 010 2.121l-1.088 1.088a1.5 1.5 0 01-2.121 0z'/><path stroke-linecap='round' stroke-linejoin='round' d='M11.25 5.25v1.5m0 0v1.5m0-1.5h1.5m-1.5 0h-1.5'/></svg>`,
    reports: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z'/></svg>`,
    returns: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3'/></svg>`,
    inventory: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M6 20.25h12m-7.5-3v3m3-3v3m-10.5-3h15M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'/></svg>`,
    logout: `<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9'/></svg>`,
  };
  return icons[iconName] || '';
};

const navigationItems = [
  { name: 'dashboard', path: '/', label: 'Dashboard', icon: 'dashboard' },
  { name: 'pos', path: '/pos', label: 'Advanced POS', icon: 'pos' },
  { name: 'collections', path: '/collections', label: 'Collections', icon: 'collections' },
  { name: 'dresses', path: '/dresses', label: 'Dresses', icon: 'dresses' },
  { name: 'reports', path: '/reports', label: 'Reports', icon: 'reports' },
  { name: 'returns', path: '/returns', label: 'Returns', icon: 'returns' },
  { name: 'inventory', path: '/inventory', label: 'Inventory', icon: 'inventory' },
];

const logout = async () => {
  await authStore.logout();
  mobileMenuOpen.value = false;
  router.push('/login');
};

onMounted(() => {
  authStore.checkAuth();
});
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Mobile-specific improvements */
@media (max-width: 640px) {
  /* Improve touch targets */
  .touch-manipulation {
    touch-action: manipulation;
  }
  
  /* Prevent zoom on input focus */
  input, select, textarea {
    font-size: 16px !important;
  }
  
  /* Improve tap highlights */
  button, a {
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
  }
  
  /* Smooth scrolling for mobile */
  * {
    -webkit-overflow-scrolling: touch;
  }
}

/* Tablet-specific improvements */
@media (min-width: 641px) and (max-width: 1279px) {
  /* Larger touch targets for tablets */
  .touch-manipulation {
    min-height: 44px;
    min-width: 44px;
  }
}

/* Prevent horizontal scroll on small screens */
@media (max-width: 768px) {
  #app {
    overflow-x: hidden;
  }
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
