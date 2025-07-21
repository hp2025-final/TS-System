import './bootstrap';

import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';

// Import components
import App from './components/App.vue';
import Login from './components/auth/Login.vue';
import Dashboard from './components/DashboardEnhanced.vue';
import Collections from './components/collections/CollectionsPage.vue';
import Dresses from './components/dresses/DressesPage.vue';
import AdvancedPOS from './components/pos/AdvancedPOS.vue';
import BarcodeSalesReport from './components/BarcodeSalesReport.vue';
import BarcodeReturnsReport from './components/BarcodeReturnsReport.vue';
import Returns from './components/returns/ReturnsPageNew.vue';
import ResaleableItems from './components/inventory/ResaleableItemsPage.vue';

// Import stores
import { useAuthStore } from './stores/auth';

// Configure router
const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/collections',
        name: 'collections',
        component: Collections,
        meta: { requiresAuth: true }
    },
    {
        path: '/dresses',
        name: 'dresses',
        component: Dresses,
        meta: { requiresAuth: false } // Temporarily disabled for testing
    },
    {
        path: '/pos',
        name: 'pos',
        component: AdvancedPOS,
        meta: { requiresAuth: false } // Allow access without auth for testing
    },
    {
        path: '/reports/barcode-sales',
        name: 'barcode-sales-report',
        component: BarcodeSalesReport,
        meta: { requiresAuth: true }
    },
    {
        path: '/reports/barcode-returns',
        name: 'barcode-returns-report',
        component: BarcodeReturnsReport,
        meta: { requiresAuth: true }
    },
    {
        path: '/returns',
        name: 'returns',
        component: Returns,
        meta: { requiresAuth: true }
    },
    {
        path: '/inventory',
        name: 'inventory',
        component: ResaleableItems,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

// Create Vue app
const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);

app.mount('#app');
