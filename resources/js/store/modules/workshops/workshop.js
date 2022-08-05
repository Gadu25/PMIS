import axios from "axios";

const state = {
    workshops: [],
    workshop: [],
    // options: programs, divisions, projects
    options: []
}

const getters = {
    getWorkshops: (state) => state.workshops,
    getWorkshop: (state) => state.workshop,
    getOptions: (state) => state.options
}

const actions = {
    async fetchWorkshops({commit}){
        const response = await axios.get('/api/workshop').then(res => {
            commit('setWorkshops', res.data)
            return res.data
        })
        return response
    },
    async fetchWorkshop({commit}, id){
        const response = await axios.get('/api/workshop/'+id).then(res =>{
            commit('setWorkshop', res.data)
            return res.data
        })
        return response
    },
    async saveWorkshop({commit}, form){
        const response = form.id == '' ? await axios.post('/api/workshop', form) : await axios.put('/api/workshop/'+form.id, form)
        if(!response.data.errors){
            commit('setWorkshops', response.data.workshops)
        }
        return response.data
    },
    async deleteWorkshop({commit}, id){
        const response = await axios.delete('/api/workshop/'+id).then(res => {
            if(!res.data.errors){
                commit('setWorkshops', res.data.workshops)
            }
            return res.data
        })
        return response
    },
    async fetchOptions({commit}, query){
        const response = await axios.get('/api/workshop/options/'+query.workshopId+'/'+query.annex).then(res =>{
            commit('setOptions', res.data)
            return res.data
        })
        return response
    }
}

const mutations = {
    setWorkshops: (state, data) => state.workshops = data,
    setWorkshop: (state, data) => state.workshop = data,
    setOptions: (state, data) => state.options = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}