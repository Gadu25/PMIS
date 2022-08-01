import { createWebHistory, createRouter } from "vue-router";
import Home from './components/public/Home.vue'
import Login from './components/public/Login.vue'
import Dashboard from './components/private/Dashboard.vue'
import Divisions from './components/private/divisions/Divisions.vue'

const routes = [
    { path: '/', name: 'Home', component: Home, meta: { requiresAuth: false} },
    { path: '/login', name: 'Login', component: Login, meta: { requiresAuth: false } },
    { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/divisions-and-units', name: 'Divisions', component: Divisions, meta: { requiresAuth: true } },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from) => {
    if(to.meta.requiresAuth && !localStorage.getItem('token')){
        return { name: 'Login' }
    }
    if(to.meta.requiresAuth == false && localStorage.getItem('token')){
        return { name: 'Dashboard' }
    }
})

export default router