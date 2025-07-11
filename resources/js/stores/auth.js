import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('auth_token'));

  const isAuthenticated = computed(() => !!token.value);

  const setAuth = (userData, authToken) => {
    user.value = userData;
    token.value = authToken;
    localStorage.setItem('auth_token', authToken);
    localStorage.setItem('user', JSON.stringify(userData));
    
    // Set axios default header
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
  };

  const clearAuth = () => {
    user.value = null;
    token.value = null;
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    delete window.axios.defaults.headers.common['Authorization'];
  };

  const login = async (email, password) => {
    try {
      const response = await window.axios.post('/api/login', {
        email,
        password
      });

      const { user: userData, token: authToken } = response.data;
      setAuth(userData, authToken);
      
      return { success: true };
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed' 
      };
    }
  };

  const register = async (name, email, password, password_confirmation) => {
    try {
      const response = await window.axios.post('/api/register', {
        name,
        email,
        password,
        password_confirmation
      });

      const { user: userData, token: authToken } = response.data;
      setAuth(userData, authToken);
      
      return { success: true };
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Registration failed',
        errors: error.response?.data?.errors || {}
      };
    }
  };

  const logout = async () => {
    try {
      if (token.value) {
        await window.axios.post('/api/logout');
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      clearAuth();
    }
  };

  const checkAuth = async () => {
    if (token.value) {
      try {
        const response = await window.axios.get('/api/user');
        user.value = response.data;
      } catch (error) {
        // Token is invalid, clear auth
        clearAuth();
      }
    }
  };

  // Initialize user from localStorage if token exists
  if (token.value) {
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
      user.value = JSON.parse(savedUser);
    }
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    checkAuth,
    setAuth,
    clearAuth
  };
});
