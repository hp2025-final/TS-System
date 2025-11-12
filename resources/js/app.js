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
import BulkUpload from './components/BulkUpload.vue';
import BulkRetrieve from './components/BulkRetrieve.vue';
import BackupDatabase from './components/BackupDatabase.vue';
import BarcodeList from './components/BarcodeList.vue';

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
    },
    {
        path: '/bulk-upload',
        name: 'bulk-upload',
        component: BulkUpload,
        meta: { requiresAuth: true, adminOnly: true }
    },
    {
        path: '/bulk-retrieve',
        name: 'bulk-retrieve',
        component: BulkRetrieve,
        meta: { requiresAuth: true, adminOnly: true }
    },
    {
        path: '/backup',
        name: 'backup',
        component: BackupDatabase,
        meta: { requiresAuth: true, adminOnly: true }
    },
    {
        path: '/barcode-list',
        name: 'barcode-list',
        component: BarcodeList,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    // Ensure auth state is loaded
    if (!authStore.user && authStore.isAuthenticated) {
        await authStore.checkAuth();
    }
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next('/');
    } else if (to.meta.adminOnly && authStore.user?.email !== 'admin@tspos.com') {
        // Redirect non-admin users to dashboard
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
