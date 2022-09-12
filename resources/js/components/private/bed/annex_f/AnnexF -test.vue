<template>
    <div class="annexf-container">
        <div class="header">
            <button class="btn btn-sm btn-light float-start" @click="this.$router.back()"><i class="fas fa-arrow-left"></i> Back</button>
            <h4>Annex F</h4>
            <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small>
        </div>
        <div class="controls">
            <div class="print-container">
                <template v-if="!editmode">
                    <button :class="printmode? 'green' :'print'" @click="printmode = !printmode">{{printmode ? 'Display' : 'Print'}} View</button>
                    <button class="btn-print" v-if="printmode"><i class="far fa-print"></i> Print</button>
                </template>
            </div>
            <button @click="editmode = !editmode, printmode = false" :class="editmode ? 'green' : 'view'">{{!editmode ? 'Edit' : 'View'}} mode</button>
        </div>
        <button class="btn-show" v-if="!filtershow" @click="filtershow = true"><i class="far fa-arrow-left"></i></button>
        <div class="main-content">
            <div class="display-container" :style="!filtershow ? 'width: 100% !important' :''">
                <div v-if="!editmode">default display</div>
                <div v-else class="form-display">
                    <div class="header">
                        <span>icon status</span>
                    </div>
                    <div class="table-container">
                        <table class="myTable">
                            <thead>
                                <tr>
                                    <th>Project Name / Activity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="num in 100" :key="num">
                                    <td>test</td>
                                    <td class="btns">
                                        <button class="btn-edit"><i class="far fa-pencil-alt"></i></button>
                                        <button class="btn-delete"><i class="far fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="filter-container" v-if="filtershow">
                <div class="filter">
                    <div class="title">
                        <span class="text">Filters</span>
                        <button class="btn-hide" @click="filtershow = false"><i class="far fa-arrow-right"></i></button>
                    </div>
                    <div class="body">
                        <div class="custom-input">
                            <select v-model="filter.status" :class="filter.status ? 'withval' : ''">
                                <option value="" selected hidden disabled></option>
                                <option value="New">New</option>
                                <option value="Draft">Draft</option>
                                <option value="For Review">For Review</option>
                                <option value="For Approval">For Approval</option>
                                <option value="Approved">Approved</option>
                                <option value="Submitted">Submitted</option>
                            </select>
                            <label class="label">Status</label>
                        </div>
                        <div class="custom-input">
                            <select v-model="filter.type" :class="filter.type ? 'withval' : ''">
                                <option value="" selected hidden disabled></option>
                                <option value="Program">Program</option>
                                <option value="Division">Division</option>
                            </select>
                            <label class="label">Type</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex'
export default {
    name: 'AnnexF',
    data(){
        return {
            editmode: false,
            printmode: false,
            loading: true,
            filtershow: true,
            filter: {
                status:        '', type: '',
                program_id:    '', division_id: '',
                subprogram_id: '', unit_id: '',
                cluster_id:    '', subunit_id: '',
            },
            test: ''
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('annexf', ['fetchAnnexFs']),
        
    },
    computed: {
        
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
    },
    created(){
        
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            this.loading = false
        })
    }
}
</script>
<style>
    .annexf-container{
        padding: 1rem;
        height: calc(100vh - 45px);
        overflow: auto;
    }
    /* Header */
    .annexf-container>.header{
        padding: 9px 3px 0px 3px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        margin-bottom: 0.5rem;
    }
    .annexf-container>.header>h4{
        text-align: center;
    }
    /* Controls */
    .controls{
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .controls>button,
    .print-container>button{
        border: 1px solid rgba(0,0,0,0.25);
        border-radius: 0.25em;
        margin-right: 0.75em;
        padding: 3px 6px;
        min-width: 120px;
    }
    .controls>button:hover,
    .print-container>.btn-print:hover,
    .print-container>.print:hover{
        background: rgba(0,0,0,0.25);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    .controls>.green,
    .print-container>.green{
        background: rgba(0, 128, 0, 0.8);
        color: white;
    }
    .controls>.green:hover,
    .print-container>.green:hover{
        background: green;
        color: white;
    }
    /* Hide Filter Container Button */
    .btn-show{
        position: fixed;
        right: 17px;
        border-radius: 0.25em;
        border: 1px solid rgba(0,0,0,0.25);
        box-shadow: 2px 2px 3px 1px rgba(0,0,0,0.15);
        padding: 2px 8px;
    }
    /* Main Content */
    .main-content{
        display: flex;
        gap: 12px;
    }
    .main-content>div{
        padding: 4px 6px;
        transition: all .3s;
    }
    /* Display */
    .display-container{
        width: 75%;
        background: white;
        height: calc(100vh - 196px);
        border-radius: 0.25em;
        box-shadow: 0 1rem 1.5rem rgba(0,0,0,0.25);
    }
    .form-display{
        padding-right: 6px;
    }
    .form-display>.header{
        padding: 0.5rem;
        font-weight: bold;
    }
    .form-display>.table-container{
        height: calc(100vh - 250px);;
        overflow: auto;
        padding-right: 8px;
    }
    .myTable{
        width: 100%;
    }
    .myTable thead{
        position: sticky;
        top: 0;
    }
    .myTable th{
        background: green;
        color: white;
        font-size: 22px;
        text-align: center;
    }
    .myTable td{
        font-size: 18px;
    }
    .myTable th,
    .myTable td{
        border: 1px solid rgba(0,0,0,0.25);
        padding: 4px 6px;
    }
    .myTable .btns{
        text-align: center;
    }
    .myTable td>button{
        padding: 3px 9px;
        margin-right: 0.25em;
        outline: none;
        border-radius: 0.20em;
        border: 1px solid rgba(0,0,0,0.25);
        min-width: 40px;
    }
    .btn-edit{
        background: rgb(0, 0, 255);
        color: white;
    }
    .btn-delete{
        background: rgb(255, 0, 0);
        color: white;
    }
    .btn-edit:hover{
        background: rgb(0, 0, 150);
    }
    .btn-delete:hover{
        background: rgb(180, 0, 0);
    }
    /* Filter Container */
    .filter-container{
        width: 25%;
    }
    .filter{
        border: 1px solid rgba(0,0,0,0.25);
        border-radius: 0.25em;
        padding: 4px 6px;
        /* height: 400px; */
        box-shadow: 4px 8px 10px 2px rgba(0,0,0,0.25);
    }
    .filter>.title{
        padding: 2px 2px 6px 2px;
        border-bottom: 1px solid rgba(0,0,0,0.25);
        position: relative;
    }
    .filter>.title>.text{
        font-weight: bold;
        font-size: 22px;
    }
    .filter>.title>.btn-hide{
        position: absolute;
        right: 0;
        border-radius: 0.25em;
        border: 1px solid rgba(0,0,0,0.25);
        padding: 2px 9px;
        box-shadow: 2px 2px 3px 1px rgba(0,0,0,0.15);
    }
    .filter>.body{
        /* background: red; */
        padding: 6px 8px;
        /* height: 200px; */
    }
    .btn-show:hover,
    .filter>.title>.btn-hide:hover{
        background: rgba(0,0,0,0.25);
    }

    /* Custom Floating Label */
    .custom-input{
        position: relative;
        width: 100%;
        margin-bottom: 0.75em;
    }
    .custom-input>select {
        width: 100%;
        height: 60px;
        border-radius: 0.25em;
        outline: none !important;
        font-size: 18px;
        padding-left: 4px;
    }
    .custom-input>label {
        position: absolute;
        left: 12px;
        top: 18px;
        transition: all .3s
    }
    .custom-input>select:focus ~label,
    .custom-input>select.withval~label{
        /* transform: translateY(-10px); */
        top: 0;
        left: 5px;
        color: gray;
    }

    @media only screen and (max-width: 600px) {
        .main-content { flex-wrap: wrap-reverse; }
        .display-container {width: 100%;}
        .filter-container {width: 100%;}
    }
</style>