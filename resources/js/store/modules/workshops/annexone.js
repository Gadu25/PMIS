import axios from "axios";

const state = {
    annexones: [],
}

const getters = {
    getAnnexOnes: (state) => state.annexones,
}

const actions = {
    async fetchAnnexOnes({commit}, workshopId){
        const response = await axios.get('/api/workshop/annex-one/'+workshopId).then(res => {
            commit('setAnnexOnes', res.data)
            return res.data
        })
        return response
    },
    async saveAnnexOne({commit}, form){
        const response = form.id == '' ? await axios.post('/api/workshop/annex-one', form) : await axios.put('/api/workshop/annex-one/'+form.id, form)
        if(!response.data.errors){
            commit('setAnnexOnes', response.data.annexones)
        }
        return response.data
    }
}

const mutations = {
    setAnnexOnes: (state, data) => state.annexones = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}