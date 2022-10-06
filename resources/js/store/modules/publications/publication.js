import axios from "axios";

const state = {
    publications: [],
}

const getters = {
    getPublications: (state) => state.publications,
}

const actions = {
    async fetchPublications({commit}){
        const response = await axios.get('/api/publication').then(res => {
            return res.data
        })
        return response
        // const response = await axios.get('/api/user/role/sidebaritems').then(res => {
        //     commit('setSidebarItems', res.data)
        //     return res.data
        // })
        // return response
    },
}

const mutations = {
    setPublications: (state, data) => state.publications = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}