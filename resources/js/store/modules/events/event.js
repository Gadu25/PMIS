import axios from "axios";

const state = {
    events: [],
}

const getters = {
    getEvents: (state) => state.events,
}

const actions = {
    async fetchEvents({commit}, obj){
        const response = await axios.get('/api/project/profile/event/'+obj.year+'/'+obj.start+'/'+obj.end).then(res => {
            commit('setEvents', res.data)
            return res.data
        })
        return response
    },
}

const mutations = {
    setEvents: (state, data) => state.events = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}