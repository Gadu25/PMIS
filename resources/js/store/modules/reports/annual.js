import axios from "axios";

const state = {
    annualreports: [],
}

const getters = {
    getAnnualReports: (state) => state.annualreports,
}

const actions = {
    async fetchAnnualReports({commit}){
        const response = await axios.get('/api/report/annual').then(res => {
            commit('setAnnualReports', res.data)
            return res.data
        })
        return response
    },
    async saveAnnualReport({commit}, form){
        var url = '/api/report/annual'
        url = form.id ? url+'/'+form.id : url
        const formData = new FormData();
        formData.append('id', form.id)
        formData.append('year', form.year)
        formData.append('description', form.description)
        formData.append('thumbnail', form.thumbnail)
        formData.append('pdf', form.pdffile)

        const response = await axios.post(url, formData).then(res => {
            if(res.data.reports){
                commit('setAnnualReports', res.data.reports)
            }
            return res.data
        })
        return response
    },
    async deleteAnnualReport({commit}, id){
        const response = await axios.delete('/api/report/annual/'+id).then(res => {
            if(res.data.reports){
                commit('setAnnualReports', res.data.reports)
            }
            return res.data
        })
        return response
    }
}

const mutations = {
    setAnnualReports: (state, data) => state.annualreports = data,
}

export default {
    namespaced: true,
    state, getters, actions, mutations
}