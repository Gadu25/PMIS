import axios from "axios";

const state = {
    neps: [],
}

const getters = {
    getNationalExpenditures: (state) => state.neps,
}

const actions = {
    async fetchNationalExpenditures({commit}, workshopId){
        const response = await axios.get('/api/workshop/nep/'+workshopId).then(res => {
            commit('setNationalExpenditures', res.data)
            return res.data
        })
        return response
    },
    async saveNationalExpenditure({commit}, form){
        var options = form.savingOptions
        const response = options.type == 'multiple' ?
            await axios.put('/api/workshop/nep/multiple', form) : (options.project.id ?
            await axios.put('/api/workshop/nep/'+options.project.id, options) : 
            await axios.post('/api/workshop/nep', options))
            
        if(!response.data.errors){
            commit('setNationalExpenditures', response.data.neps)
        }
        return response.data
    },
    async deleteNationalExpenditure({commit}, id){
        const response = await axios.delete('/api/workshop/nep/'+id).then(res => {
            commit('setNationalExpenditures', res.data.neps)
            return res.data
        })
        return response
    }
}

const mutations = {
    setNationalExpenditures: (state, data) => state.neps = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}