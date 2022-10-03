import axios from "axios";

const state = {
    profile: [],
    project: [],
    projects: [],
    sortedprojects: []
}

const getters = {
    getProfile: (state) => state.profile,
    getProject: (state) => state.project,
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
    },
    async fetchProject({commit}, id){
        const response = await axios.get('/api/project/'+id).then(res => {
            commit('setProject', res.data)
            return res.data
        })
        return response
    },
    async updatePortfolio({commit}, form){
        const response = await axios.put('/api/project/portfolio/'+form.id, form).then(res => {
            commit('setProject', res.data)
            return res.data
        })
        return response
    },
    // Project Profile
    async saveProfile({commit}, form){
        const response = form.id ? await axios.put('/api/project/profile/'+form.id, form) : await axios.post('/api/project/profile', form)
        if(!response.data.errors){
            commit('setProject', response.data.project)
        }
        return response.data
    },
    async fetchProfile({commit}, id){
        const response = await axios.get('/api/project/profile/'+id).then(res => {
            commit('setProfile', res.data)
            return res.data
        })
        return response
    }
}

const mutations = {
    setProfile: (state, data) => state.profile = data,
    setProject: (state, data) => state.project = data,
    setProjects: (state, data) => state.projects = data,
    setSortedProjects: (state, data) => state.sortedprojects = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}