import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  id: 'auth',
  state: () => ({
    accessToken: localStorage.getItem('accessToken') || '',
    user: localStorage.getItem('user') || '',

  }),
  getters: {
    isAuthenticated: (state) => !!state.accessToken,
  },
  actions: {
    async getUser() {
      this.user;
    },
    setTokens(accessToken) {
      this.accessToken = accessToken;
      localStorage.setItem('accessToken', accessToken);
    },
    clearTokens() {
      this.accessToken = '';
      this.refreshToken = '';
      localStorage.removeItem('accessToken');
    },
    updateAccessToken(accessToken) {
      this.accessToken = accessToken
      localStorage.setItem('accessToken', accessToken);
    },
    setUser(user) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user));
    },
    updateLocalUser(user) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user));
    },

    async logOut() {
      this.clearTokens();
      this.user = ''
      localStorage.removeItem('user');
    },

  },
});
