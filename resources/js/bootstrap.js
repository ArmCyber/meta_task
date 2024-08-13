import axios from 'axios';
import('bootstrap')
import jQuery from 'jquery'

window.$ = jQuery;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
