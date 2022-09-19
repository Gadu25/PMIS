import axios from "axios";

const state = {
    sidebaritems: [],
}

const getters = {
    getSidebarItems: (state) => state.sidebaritems,
}

const actions = {
    async fetchSidebarItems({commit}){
        const response = await axios.get('/api/user/role/sidebaritems').then(res => {
            commit('setSidebarItems', res.data)
            return res.data
        })
        return response
    },
    async saveRoles({commit}, form){
        const response = form.id ? await axios.put('/api/user/role/'+form.id, form) : await axios.post('/api/user/role', form)
        if(!response.data.errors){
            commit('setSidebarItems', response.data.roles)
        }
        return response.data
    },
    async removeRoles({commit}, id){
        const response = await axios.delete('/api/user/role/'+id).then(res => {
            if(!res.data.errors){
                commit('setSidebarItems', res.data.roles)
            }
            return res.data
        })
        return response
    }
}

const mutations = {
    setSidebarItems: (state, data) => state.sidebaritems = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}