import axios from "axios";

const state = {
    user: [],
    users: [],
    authUser: [],
    titles: [],
    staffs: [],
}

const getters = {
    getUser: (state) => state.user,
    getUsers: (state) => state.users,
    getAuthUser: (state) => state.authUser,
    getTitles: (state) => state.titles,
    getStaffs: (state) => state.staffs,
}

const actions = {
    // User Manager functions
    async fetchUsers({commit}){
        const response = await axios.get('/api/user').then(res => {
            commit('setUsers', res.data)
            return res.data
        })
        return response
    },
    async fetchUsersByDivision({commit}, id){
        const response = await axios.get('/api/user/division/'+id).then(res => {
            commit('setUsers', res.data)
            return res.data
        })
        return response
    },
    async fetchTitles({commit}){
        const response = await axios.get('/api/user/title').then(res => {
            commit('setTitles', res.data)
            return res.data
        })
        return response
    },
    async saveUser({commit}, form){
        const response = form.id ? await axios.put('/api/user/'+form.id, form) : await axios.post('/api/user', form)
        if(!response.data.errors){
            commit('setUsers', response.data.users)
        }
        return response.data
    },
    // User functions
    async updateNotification({commit}, id){
        const response = await axios.put('/api/user/notification/'+id).then(res => {
            return res.data
        })
        return response
    },

    // Auth functions
    async fetchAuthUser({commit}){
        const response = await axios.get('/api/user/auth').then(res => {
            commit('setAuthUser', res.data)
            return res.data
        })
        return response
    },
    async login({commit}, form){
        const response = await axios.post('/api/login', form).then(res => {
            if(res.data.success){
                const now = new Date()
                // 12 hours (43200 seconds) (43200000 ms)
                var ttl = 43200000
                const authItem = {
                    value: res.data.data.token,
                    expiry: now.getTime() + ttl
                }
                localStorage.setItem('token', JSON.stringify(authItem))
                window.location.replace('/dashboard')
            }
            return res
        })
        return response
    },
    logout({commit}){
        localStorage.removeItem('token')
        commit('setUser', [])
        window.location.replace('/login')
    },
    // Staff functions
    async fetchStaffs({commit}){
        const response = await axios.get('/api/user/staff').then(res => {
            commit('setStaffs', res.data)
            return res.data
        })
        return response
    }
}

const mutations = {
    setUser: (state, data) => state.user = data,
    setUsers: (state, data) => state.users = data,
    setAuthUser: (state, data) => state.authUser = data,
    setTitles: (state, data) => state.titles = data,
    setStaffs: (state, data) => state.staffs = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}