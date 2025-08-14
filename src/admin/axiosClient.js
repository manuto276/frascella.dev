// src/axiosClient.js
import axios from 'axios';

const REFRESH_URL = '/admin/refresh';
let refreshingPromise = null;

const http = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: { Accept: 'application/json, text/html;q=0.9' },
});

// Interceptor 401 → refresh → retry
http.interceptors.response.use(
  (r) => r,
  async (error) => {
    const { config, response } = error;
    if (!response) return Promise.reject(error);

    const is401 = response.status === 401;
    const isRefresh = config?.url?.includes(REFRESH_URL);
    const alreadyRetried = config._retry === true;

    if (!is401 || isRefresh || alreadyRetried) {
      return Promise.reject(error);
    }

    config._retry = true;

    if (!refreshingPromise) {
      refreshingPromise = http.post(REFRESH_URL).finally(() => {
        setTimeout(() => (refreshingPromise = null), 0);
      });
    }

    try {
      await refreshingPromise;
      return http(config); // retry
    } catch (e) {
      return Promise.reject(error);
    }
  }
);

export default http;

/**
 * Builds an Axios client with a response interceptor that handles 401 Unauthorized errors by attempting to refresh the session.
 * If the refresh is successful, it retries the original request.
 *
 * Usage:
 * npx esbuild src/axiosClient.js \
 * --bundle --format=esm --platform=browser --minify \
 * --outfile=public/js/axiosClient.js
 * 
 * Note: The REFRESH_URL should be defined according to your backend API endpoint for refreshing the session.
 */