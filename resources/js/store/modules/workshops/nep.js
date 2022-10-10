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
        console.log(options)
        // const response = options.type == 'multiple' ?
        //     await axios.put('/api/workshop/nep/multiple', options) : options.id ?
        //     await axios.put('/api/workshop/nep/'+options.id, options) : 
        //     await axios.post('/api/workshop/nep', options)
            
        // if(!response.data.errors){
        //     commit('setNationalExpenditures', response.data.neps)
        // }
        // return response.data
    },
    // async deleteCommonIndicator({commit}, id){
    //     const response = await axios.delete('/api/workshop/common-indicator/'+id).then(res => {
    //         if(!res.data.errors){
    //             commit('setCommonIndicators', res.data.commonindicators)
    //         }
    //         return res.data
    //     })
    //     return response
    // }
}

const mutations = {
    setNationalExpenditures: (state, data) => state.neps = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}