import axios from "axios";

const state = {
    projects: [],
}

const getters = {
    getProjects: (state) => state.projects,
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
    }
}

const mutations = {
    setProjects: (state, data) => state.projects = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}