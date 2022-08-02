import axios from "axios";

const state = {
    user: [],
    authUser: []
}

const getters = {
    getUser: (state) => state.user,
    getAuthUser: (state) => state.authUser
}

const actions = {
    // User functions

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
}

const mutations = {
    setUser: (state, data) => state.user = data,
    setAuthUser: (state, data) => state.authUser = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}