import axios from "axios";

const state = {
    annexfs: [],
}

const getters = {
    getAnnexFs: (state) => state.annexfs,
}

const actions = {
    async fetchAnnexFs({commit}, filter){
        const response = await axios.post('/api/workshop/annex-f/display', filter).then(res => {
            commit('setAnnexFs', res.data)
            return res.data
        })
        return response
    },
    async saveAnnexF({commit}, form){
        const response = form.id ? await axios.put('/api/workshop/annex-f/'+form.id, form) : await axios.post('/api/workshop/annex-f', form)
        return response.data
    },
    async deleteAnnexF({commit}, id){
        const response = await axios.delete('/api/workshop/annex-f/'+id).then(res => {
            if(!res.data.errors){
                commit('setAnnexFs', res.data.annexfs)
            }
            return res.data
        })
        return response
    },
    async fetchAnnexF({commit}, id){
        const response = await axios.get('/api/workshop/annex-f/'+id).then(res => {
            return res.data
        })
        return response
    }
}

const mutations = {
    setAnnexFs: (state, data) => state.annexfs = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}