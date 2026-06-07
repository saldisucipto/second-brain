import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const loadingStart = () => window.dispatchEvent(new CustomEvent('app:loading:start'));
const loadingStop = () => window.dispatchEvent(new CustomEvent('app:loading:stop'));

window.axios.interceptors.request.use(
    (config) => {
        loadingStart();
        return config;
    },
    (error) => {
        loadingStop();
        return Promise.reject(error);
    }
);

window.axios.interceptors.response.use(
    (response) => {
        loadingStop();
        return response;
    },
    (error) => {
        loadingStop();
        return Promise.reject(error);
    }
);
