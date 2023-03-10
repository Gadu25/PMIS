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
    async fetchFilteredAnnexOne({commit}, form){
        const response = await axios.post('/api/workshop/annex-one/filtered', form).then(res => {
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
    },
    async updateAnnexOne({commit}, form){
        const response = await axios.put('/api/workshop/annex-one/table/'+form.id, form).then(res => {
            if(res.data.annexones){
                commit('setAnnexOnes', res.data.annexones)
            }
            return res.data
        })
        return response
    },
    async deleteAnnexOne({commit}, id){
        const response = await axios.delete('/api/workshop/annex-one/'+id).then(res => {
            if(!res.data.errors){
                commit('setAnnexOnes', res.data.annexones)
            }
            return res.data
        })
        return response
    },
    async publishProjects({commit}, form){
        const response = await axios.post('/api/workshop/annex-one/publish/'+form.workshop_id, form).then(res => {
            return res.data
        })
        return response
    }
}

const mutations = {
    setAnnexOnes: (state, data) => state.annexones = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}