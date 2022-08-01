import './bootstrap'

import { createApp } from '@vue/runtime-dom'
import router from './router'
import store from './store/index.js'

import App from './components/App.vue'

createApp(App)
    .use(router)
    .use(store)
    .mount("#app")