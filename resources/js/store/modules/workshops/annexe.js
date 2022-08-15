import axios from "axios";

const state = {
    annexes: [],
}

const getters = {
    getAnnexEs: (state) => state.annexes,
}

const actions = {
    async fetchAnnexEs({commit}, options){
        const response = await axios.post('/api/workshop/annex-e/display', options).then(res => {
            commit('setAnnexEs', res.data)
            return res.data
        })
        return response
    },
    async saveAnnexE({commit}, form){
        const response = form.id ? await axios.put('/api/workshop/annex-e/'+form.id, form) : await axios.post('/api/workshop/annex-e', form)
        if(!response.data.errors){
            commit('setAnnexEs', response.data.annexes)
        }
        return response.data
    },
    async deleteAnnexE({commit}, id){
        const response = await axios.delete('/api/workshop/annex-e/'+id).then(res => {
            if(!res.data.errors){
                commit('setAnnexEs', res.data.annexes)
            }
            return res.data
        })
        return response
    }
}

const mutations = {
    setAnnexEs: (state, data) => state.annexes = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}