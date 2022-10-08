import { createWebHistory, createRouter } from "vue-router";
import Home from './components/public/Home.vue'
import Login from './components/public/Login.vue'
import Dashboard from './components/private/Dashboard.vue'

import Divisions from './components/private/divisions/Divisions.vue'

import Programs from './components/private/programs/Programs.vue'
import Projects from './components/private/programs/Projects.vue'
import Portfolio from './components/private/programs/Portfolio.vue'

import Events from './components/private/events/Events.vue'

import Workshops from './components/private/bed/Workshops.vue'
import AnnexE from './components/private/bed/annex_e/AnnexE.vue'
import AnnexF from './components/private/bed/annex_f/AnnexF.vue'
import AnnexOne from './components/private/bed/annexone/AnnexOne.vue'
import CommonIndicators from './components/private/bed/common/CommonIndicators.vue'
// import NEP from './components/private/bed/nep/NationalExpediture.vue'
import Nep from './components/private/bed/nep/NationalExpenditure.vue'

import Publications from './components/private/publications/Publications.vue'

import Reports from './components/private/reports/Reports.vue'
import AnnexEReport from './components/private/reports/reports/AnnexE.vue'
import AnnexFReport from './components/private/reports/reports/AnnexF.vue'
import AnnualReport from './components/private/reports/reports/Annual.vue'
import MonthlyReport from './components/private/reports/reports/Monthly.vue'
import QuarterlyReport from './components/private/reports/reports/Quarterly.vue'
import BedReport from './components/private/reports/reports/Bed.vue'

import StrategicPlanning from './components/private/strategic_planning/StrategicPlanning.vue'

import Users from './components/private/user/management/Users.vue'
import Profile from './components/private/user/profile/Profile.vue'

import About from './components/private/about/About.vue'

const routes = [
    { path: '/', name: 'Home', component: Home, meta: { requiresAuth: false, title: 'Home'} },
    { path: '/login', name: 'Login', component: Login, meta: { requiresAuth: false, title: 'Login' } },
    { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true, title: 'Dashboard' } },
    { path: '/divisions-and-units', name: 'Divisions', component: Divisions, meta: { requiresAuth: true, title: 'Divisions and Units' } },
    { 
        path: '/programs-and-projects', name: 'Programs', component: Programs, meta: { requiresAuth: true, title: 'Programs and Projects' },
        children: [
            { path: '/programs-and-projects/projects/:selected', name: 'Projects', component: Projects, meta: { requiresAuth: true, title: 'Projects' } },
            { path: '/programs-and-projects/projects/portfolio/:id', name: 'Portfolio', component: Portfolio, meta: { requiresAuth: true, title: 'Project Portfolio' } },
        ]
    },
    { path: '/events-management', name: 'Events', component: Events, meta: { requiresAuth: true, title: 'Events Management' } },
    { 
        path: '/budget-executive-documents', name: 'Workshops', component: Workshops, meta: { requiresAuth: true, title: 'Budget Executive Documents' },
        children: [
            { path: '/budget-executive-documents/annex-e/:workshopId', name: 'AnnexE', component: AnnexE, meta: { requiresAuth: true, title: 'Annex E' } },
            { path: '/budget-executive-documents/annex-f/:workshopId', name: 'AnnexF', component: AnnexF, meta: { requiresAuth: true, title: 'Annex F' } },
            { path: '/budget-executive-documents/annex-one/:workshopId', name: 'AnnexOne', component: AnnexOne, meta: { requiresAuth: true, title: 'Annex One' } },
            { path: '/budget-executive-documents/common-indicators/:workshopId', name: 'CommonIndicators', component: CommonIndicators, meta: { requiresAuth: true, title: 'Common Performance Indicators' } },
            { path: '/budget-executive-documents/national-expenditure-program/:workshopId', name: 'Nep', component: Nep, meta: { requiresAuth: true, title: 'National Expenditure Program' } },
        ]
    },
    { path: '/resources-and-publications', name: 'Publications', component: Publications, meta: { requiresAuth: true, title: 'Resources and Publications' } },
    { 
        path: '/reports', name: 'Reports', component: Reports, meta: { requiresAuth: true, title: 'Reports' },
        children: [
            { path: '/reports/annex-e', name: 'AnnexEReport', component: AnnexEReport, meta: { requiresAuth: true, title: 'Annex E' } },
            { path: '/reports/annex-f', name: 'AnnexFReport', component: AnnexFReport, meta: { requiresAuth: true, title: 'Annex F' } },
            { path: '/reports/annual-report', name: 'AnnualReport', component: AnnualReport, meta: { requiresAuth: true, title: 'Annual Report' } },
            { path: '/reports/monthly-report', name: 'MonthlyReport', component: MonthlyReport, meta: { requiresAuth: true, title: 'Monthly Report' } },
            { path: '/reports/quarterly-report', name: 'QuarterlyReport', component: QuarterlyReport, meta: { requiresAuth: true, title: 'BAR Report' } },
            { path: '/reports/bed-report', name: 'BedReport', component: BedReport, meta: { requiresAuth: true, title: 'BEDs Report' } },
        ]
    },

    { path: '/strategic-planning', name: 'StrategicPlanning', component: StrategicPlanning, meta: { requiresAuth: true, title: 'Strategic Planning' } },
    { path: '/users-management', name: 'Users', component: Users, meta: { requiresAuth: true, title: 'User Management' } },
    { path: '/profile', name: 'Profile', component: Profile, meta: { requiresAuth: true, title: 'Profile'} },
    { path: '/about-system', name: 'About', component: About, meta: { requiresAuth: true, title: 'About System'} }
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