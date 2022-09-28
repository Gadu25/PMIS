import axios from "axios";

const state = {
    projects: [],
    sortedprojects: []
}

const getters = {
    getProjects: (state) => state.projects,
    getSortedProjects: (state) => state.sortedprojects,
}

const actions = {
    async fetchProjects({commit}){
        const response = await axios.get('/api/project').then(res => {
            commit('setProjects', res.data)
            return res.data
        })
        return response
    },
    async saveProject({commit}, form){
        const response = form.id == '' ? await axios.post('/api/project/multiple', form) : await axios.put('/api/project/'+form.id, form)
        if(response.data.projects){
            commit('setProjects', response.data.projects)
        }
        return response.data
    },
    async fetchSortedProjects({commit}, selected){
        const response = await axios.get('/api/project/sort/'+selected).then(res => {
            commit('setSortedProjects', res.data)
            return res.data
        })
        return response
    }
}

const mutations = {
    setProjects: (state, data) => state.projects = data,
    setSortedProjects: (state, data) => state.sortedprojects = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}