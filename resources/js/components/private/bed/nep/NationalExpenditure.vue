<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-light float-start" @click="this.$router.push({name: 'Workshops'})"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center">National Expenditure Program</h2>
        <small>Planning Workshop  <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span> </small><hr>
        <template v-if="!loading">
            <div class="row justify-content-center">
                <div class="col-sm-10">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-sm bg-gradient shadow-none" @click="editmode = !editmode" :class="editmode ? 'btn-primary' : 'btn-success'">{{!editmode ? 'Edit' : 'View'}} mode</button>
                        <!-- <button class="btn btn-sm btn-success bg-gradient shadow-none" @click="addProject()"><i class="fas fa-plus"></i> Project</button> -->
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="text-center align-middle bg-warning position-sticky top-0">
                                <tr class="">
                                    <th>Programs / Projects / Activities</th>
                                    <th>NEP</th>
                                    <th style="width: 140px" v-if="editmode" class="p-0">
                                        <button class="btn btn-sm btn-success bg-gradient h-100 w-100 border-0 shadow-none rounded-0" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="input" v-for="project, key in form.projects" :key="key">
                                    <td>
                                        <select class="form-control">
                                            <option value="" disabled selected hidden>Select Project</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control text-end"></td>
                                    <td v-if="editmode">
                                        <button class="btn btn-sm btn-outline-primary w-75"><i class="far fa-save fa-lg"></i> <strong> Save</strong></button>
                                        <button class="btn btn-sm btn-outline-danger w-25"><i class="far fa-trash-alt fa-lg"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{editmode ? 'Edit' : 'New'}} Project/s</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-sm btn-success bg-gradient" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'NationalExpediture',
    data(){
        return {
            loading: true,
            editmode: false,
            form: {
                projects: [{
                    id: '',
                    project_id: '',
                    amount: 0,
                    workshop_id: this.$route.params.workshopId,
                    isAdded: false
                }]
            }
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        newForm(){
            this.editmode = false
            this.form.projects = [{
                id: '',
                project_id: '',
                amount: 0,
                workshop_id: this.$route.params.workshopId,
                isAdded: false
            }]
        },
        editForm(){

        },
        addProject(){
            this.form.projects.push({
                id: '',
                project_id: '',
                amount: 0,
                workshop_id: this.$route.params.workshopId,
                isAdded: false
            })
        }
    },
    created(){
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            this.loading = false
        })
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop }
        
    }
}
</script>
<style scoped>
tr>th{
    height: 60px
}
tr#input>td{
    padding: 0px !important;
    height: 1px;
}
tr#input>td>.form-control{
    border-radius: 0;
    border: 0;
    box-shadow: none;
}
tr#input>td>.form-control:focus{
    background: lightblue;
}
tr#input>td>button{
    border: 0;
    box-shadow: none;
    border-radius: 0;
    height: 100%;
    /* height: 40px */
}
.table-responsive{
    overflow: auto;
    height: calc(100vh - 250px)
}
</style>