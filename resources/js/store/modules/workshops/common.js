import axios from "axios";

const state = {
    commonindicators: [],
}

const getters = {
    getCommonIndicators: (state) => state.commonindicators,
}

const actions = {
    async fetchCommonIndicators({commit}, workshopId){
        const response = await axios.get('/api/workshop/common-indicator/'+workshopId).then(res => {
            commit('setCommonIndicators', res.data)
            return res.data
        })
        return response
    },
    async saveCommonIndicator({commit}, form){
        const response = form.id ? await axios.put('/api/workshop/common-indicator/'+form.id, form) : await axios.post('/api/workshop/common-indicator', form)
        if(!response.data.errors){
            commit('setCommonIndicators', response.data.commonindicators)
        }
        return response.data
    },
    async deleteCommonIndicator({commit}, id){
        const response = await axios.delete('/api/workshop/common-indicator/'+id).then(res => {
            if(!res.data.errors){
                commit('setCommonIndicators', res.data.commonindicators)
            }
            return res.data
        })
        return response
    }
}

const mutations = {
    setCommonIndicators: (state, data) => state.commonindicators = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}