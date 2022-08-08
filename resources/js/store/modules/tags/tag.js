import axios from "axios";

const state = {
    tags: [],
}

const getters = {
    getTags: (state) => state.tags,
}

const actions = {
    async fetchWorkshopTags({commit}){
        // tag type = workshop
        const response = await axios.get('/api/tag/workshop').then(res => {
            commit('setTags', res.data)
            return res.data
        })
        return response
    },
    async saveTag({commit}, form){
        const response = form.id ? await axios.put('/api/tag/'+form.id, form) : await axios.post('/api/tag', form)
        if(!response.data.errors){
            commit('setTags', response.data.tags)
        }
        return response.data
    },
    async deleteTag({commit}, id){
        const response = await axios.delete('/api/tag/'+id).then(res => {
            if(!res.data.errors){
                commit('setTags', res.data.tags)
            }
            return res.data
        })
        return response
    }
}

const mutations = {
    setTags: (state, data) => state.tags = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}