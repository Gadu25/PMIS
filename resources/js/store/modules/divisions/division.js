import axios from "axios";

const state = {
    divisions: [],
}

const getters = {
    getDivisions: (state) => state.divisions,
}

const actions = {
    async fetchDivisions({commit}){
        const response = await axios.get('/api/division').then(res => {
            commit('setDivisions', res.data)
            return res.data
        })
        return response
    },
}

const mutations = {
    setDivisions: (state, data) => state.divisions = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}