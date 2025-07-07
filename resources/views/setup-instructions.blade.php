<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TS-POS-V1 Setup Instructions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">TS-POS-V1</h1>
            <p class="text-xl text-gray-600">Point of Sale System for Dress Retail Store</p>
            <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                ✅ Backend Ready - Frontend Setup Required
            </div>
        </div>

        <!-- Quick Setup Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">🚀 Quick Setup Required</h2>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Vite Assets Need to be Built</h3>
                        <p class="mt-1 text-sm text-yellow-700">Run the commands below to complete the frontend setup.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">1. Install Frontend Dependencies</h3>
                    <div class="bg-gray-900 rounded-md p-4">
                        <code class="text-green-400 font-mono">npm install</code>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">2. Build Assets (Choose One)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Development Mode:</h4>
                            <div class="bg-gray-900 rounded-md p-4">
                                <code class="text-green-400 font-mono">npm run dev</code>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Production Build:</h4>
                            <div class="bg-gray-900 rounded-md p-4">
                                <code class="text-green-400 font-mono">npm run build</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backend Status -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">✅ Backend Status</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">Database</p>
                        <p class="text-xs text-green-600">8 tables migrated</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">API Routes</p>
                        <p class="text-xs text-green-600">15+ endpoints</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">Sample Data</p>
                        <p class="text-xs text-green-600">3 collections, 13 items</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Testing -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">🧪 Test API Endpoints</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Authentication</h3>
                    <div class="bg-gray-50 rounded-md p-4">
                        <p class="text-sm text-gray-700 mb-2"><strong>Login:</strong></p>
                        <code class="text-sm">POST /api/login</code><br>
                        <code class="text-sm text-blue-600">{"email": "admin@tspos.com", "password": "password"}</code>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Collections</h3>
                    <div class="bg-gray-50 rounded-md p-4">
                        <code class="text-sm">GET /api/collections</code>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Barcode Lookup</h3>
                    <div class="bg-gray-50 rounded-md p-4">
                        <code class="text-sm">GET /api/dress-items/barcode/2503071</code><br>
                        <span class="text-xs text-gray-500">Try: 2503071, 2503076, 2503080</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demo Credentials -->
        <div class="bg-blue-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-blue-900 mb-4">🔑 Demo Credentials</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-medium text-blue-900">Admin User</h3>
                    <p class="text-sm text-blue-700">admin@tspos.com</p>
                    <p class="text-sm text-blue-700">password</p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-medium text-blue-900">Staff User</h3>
                    <p class="text-sm text-blue-700">staff@tspos.com</p>
                    <p class="text-sm text-blue-700">password</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 pt-8 border-t border-gray-200">
            <p class="text-gray-500 text-sm">
                <a href="/docs" class="text-blue-600 hover:text-blue-800">View Detailed Documentation</a> | 
                Built with Laravel 11 + Vue 3 + Tailwind CSS
            </p>
        </div>
    </div>
</body>
</html>
