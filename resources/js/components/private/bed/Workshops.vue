<template>
    <div class="p-2 workshop" v-if="!childSelected">
        <h2 class="text-center">Budget Executive Documents</h2><hr>
        <div class="container" v-if="!loading">
            <form @submit.prevent="submitForm()" class="d-flex justify-content-center border-bottom mb-4" v-if="formshow">
                <div class="col-sm-8 shadow p-3 mb-3">
                    <div class="mb-2"><h4> Workshop Form</h4></div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" v-model="form.start" id="start" placeholder="start">
                                <label for="start">Start</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" v-model="form.end" id="end" placeholder="end">
                                <label for="end">End</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                    <button style="min-width: 100px;" class="btn btn-outline-secondary rounded-pill me-1" @click="formshow = false">Cancel</button>
                    <button style="min-width: 100px;" class="btn btn-primary rounded-pill" type="submit">Submit</button></div>
                </div>
            </form>
            <template v-else>
            <button v-if="inUserRole('workshop_add')" class="btn btn-sm btn-success float-end" @click="resetForm()"><i class="fas fa-plus"></i></button>
            <h4><strong>Planning Workshops</strong></h4><hr>
            <div class="d-flex justify-content-center workshop-accordions">
                <div class="col-sm-8">
                    <div class="accordion shadow-lg" id="accordionExample">
                        <div class="accordion-item" v-for="workshop in workshops" :key="workshop.id+'_workshop'">
                            <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button shadow-none collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#workshop'+workshop.id" expanded="false" aria-controls="collapseOne">
                                <strong>{{workshop.date}}</strong>
                            </button>
                            </h2>
                            <div :id="'workshop'+workshop.id" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="">
                                        <!-- <ul class="items" :style="!inUserRole('workshop_edit') && !inUserRole('workshop_delete') ? 'width: 100% !important' : ''"> -->
                                        <ul class="items" style="width: 100% !important">
                                            <router-link @click="childSelected = true" :to="{ name: 'AnnexE', params: { workshopId: workshop.id } }"><li>Annex E</li></router-link>
                                            <router-link @click="childSelected = true" :to="{ name: 'AnnexF', params: { workshopId: workshop.id } }"><li>Annex F</li></router-link>
                                            <router-link @click="childSelected = true" :to="{ name: 'AnnexOne', params: { workshopId: workshop.id } }"><li>Annex 1</li></router-link>
                                            <router-link v-if="isUserPlanningUnit()" @click="childSelected = true" :to="{ name: 'CommonIndicators', params: { workshopId: workshop.id } }"><li>Common Performance Indicators</li></router-link>
                                            <router-link v-if="isUserPlanningUnit()" @click="childSelected = true" :to="{ name: 'Nep', params: { workshopId: workshop.id } }"><li>National Expenditure Program</li></router-link>
                                        </ul>
                                        <div class="d-flex justify-content-end">
                                            <div class="" v-if="inUserRole('workshop_edit') || inUserRole('workshop_delete')">
                                                <button style="min-width: 32px;" v-if="inUserRole('workshop_edit')" @click="editForm(workshop)" class="btn btn-sm btn-primary me-1"><i class="far fa-pencil-alt"></i> </button>
                                                <button style="min-width: 32px;" v-if="inUserRole('workshop_delete')" @click="removeWorkshop(workshop.id)" class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </template>
        </div>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
    <div v-else>
        <router-view></router-view>
    </div>

</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Workshops',
    data(){
        return {
            form: {
                id: '',
                start: '',
                end: ''
            },
            formshow: false,
            loading: true,
            childSelected: false
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshops', 'saveWorkshop', 'deleteWorkshop']),
        resetForm(){
            this.formshow = true
            this.form.id = ''
            this.form.start = ''
            this.form.end = ''
        },
        editForm(workshop){
            this.formshow = true
            this.form.id = workshop.id
            this.form.start = workshop.start
            this.form.end = workshop.end
        },
        submitForm(){
            if(this.formValidated()){
                this.saveWorkshop(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    this.formshow = res.errors
                })
            }
        },
        formValidated(){
            if(this.form.start == ''){ this.toastMsg('warning', 'Please select a Start Date'); return false }
            if(this.form.end == ''){ this.toastMsg('warning', 'Please select a End Date'); return false }
            var start = new Date(this.form.start)
            var end = new Date(this.form.end)
            if(start.getFullYear() !== end.getFullYear()){ this.toastMsg('warning', 'Start and End Date must be from the same year'); return false }
            if(start > end){ this.toastMsg('warning', 'Start Date must be set before the End Date'); return false }
            return true
        },
        removeWorkshop(id){
            swal.fire({
                title: 'This is irreversible!',
                text: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Continue!',
                reverseButtons: true
                }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteWorkshop(id).then(res => {
                        var icon = res.errors ? 'error' : 'success'
                        this.toastMsg(icon, res.message)
                    })
                }
            })
        },
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
        // Roles
        inUserRole(code){
            var role = this.authuser.active_profile.roles.find(elem => elem.code == code)
            return (role)
        },
        isUserPlanningUnit(){
            var unit = this.authuser.unit
            if(unit){
                return unit.name.includes('Planning')
            }
        }
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshops']),
        workshops(){ return this.getWorkshops },
        ...mapGetters('user', ['getAuthUser']),
        authuser(){ return this.getAuthUser }
    },
    created(){
        this.fetchWorkshops().then(res => {
            this.loading = false
        })
    },
    watch: {
        $route: {
            immediate: true,
            handler(to, from) {
                this.childSelected = (this.$route.name != 'Workshops')
            }
        },
    },
}
</script>
<style scoped>
.according-items{
    display: flex;
    
}
ul.items{
    width: 75%;
    padding: 0;
}
ul.items>a>li{
    list-style-type: none;
    cursor: pointer;
    font-size: 18px;
    padding: 4px 6px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.25);
}
ul.items>a>li:hover{
    background: rgba(0, 0, 0, 0.03);
    font-weight: bold;
    /* width: 75%; */
}
a{
    text-decoration: none;
    color: black;
}
.actions{
    width: 25%;
    align-self: flex-end;
    margin: 0 0 16px 16px;
}
.workshop{
    height: calc(100vh - 45px);
}
.workshop-accordions{
    overflow: auto;
    height: calc(100vh - 215px);
}
@media only screen and (max-width: 600px) {
    .according-items{ flex-wrap: wrap; }
    ul.items {width: 100%;}
    .actions {
        width: 100%;
        margin: 0;
    }
    .actions>button{
        width: 100%;
    }
}
</style>