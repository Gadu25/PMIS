import './bootstrap'

import { createApp } from '@vue/runtime-dom'
import router from './router'
import store from './store/index.js'

import swal from 'sweetalert2'
window.swal = swal;
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

window.toast = toast;

import App from './components/App.vue'

createApp(App)
    .use(router)
    .use(store)
    .mount("#app")
    
// app.mixin({
//     methods: {
//       globalHelper: function () {
//         alert("Hello world")
//       },
//     },
//   })