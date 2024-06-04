// Api.js
import axios from 'axios';
import { localAPI } from './myAPI';
export default () => {
  const instance = axios.create({
    baseURL: localAPI(), // Make sure your server is running on this URL
  });

  instance.interceptors.request.use(
    (config) => {
      const token = localStorage.getItem('accessToken'); // Assuming you store the token in your store
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    },
    (error) => {
      return Promise.reject(error);
    }
  );
  return instance; // Return the Axios instance with the interceptor
};
