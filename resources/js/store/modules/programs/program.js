import axios from "axios";

const state = {
    programs: [],
}

const getters = {
    getPrograms: (state) => state.programs,
}

const actions = {
    async fetchPrograms({commit}){
        const response = await axios.get('/api/program').then(res => {
            commit('setPrograms', res.data)
            return res.data
        })
        return response
    },
}

const mutations = {
    setPrograms: (state, data) => state.programs = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}