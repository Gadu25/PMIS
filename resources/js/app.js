import './bootstrap'

import { createApp } from '@vue/runtime-dom'
import router from './router'
import store from './store/index.js'
import print from 'vue3-print-nb'

import swal from 'sweetalert2'
window.swal = swal;
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

window.toast = toast;

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

import money from 'v-money'
import App from './components/App.vue'

const app = createApp(App)
app.use(router)
app.use(store)
app.use(print)
app.use(money)
app.mount("#app")
