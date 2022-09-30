<template>
    <div class="px-2 py-2" v-if="!loading">
        <h3 class="text-center">
            {{project.title}}
        </h3>
        <div class="row flex-row-reverse px-3" v-if="tab == ''">
            <div class="mb-2">
                <button class="btn btn-sm btn-outline-secondary" @click="$router.back()"><i class="far fa-arrow-left"></i> Back</button>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 shadow mb-3" id="btn" @click="tab = 'libs'">
                    <div class="card-body">
                        <h6 class="text-center">Line-Item Budgets (LIBs) & Project Proposals</h6>
                    </div>
                </div>
                <div class="card border-0 shadow mb-3" id="btn">
                    <div class="card-body">
                        <h6 class="text-center">Project Forms</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="card shadow border-0">
                    <div class="card-body" v-if="!loading">
                        <button class="btn btn-sm btn-primary float-end bg-gradient shadow-none" @click="editForm()" data-bs-toggle="modal" data-bs-target="#description"><i class="far fa-pencil-alt"></i></button>
                        <h4 class="text-center border-bottom pb-2 mb-2">Portfolio</h4>
                        <div id="portfolio">
                            <strong>Brief Description:</strong><br>
                            <p>{{project.description}}</p>
                            <div class="mb-2">
                                <strong>Project Staff/s</strong>
                                <li> {{setName(project.leader.profile.user)}} (Leader)</li>
                                <li v-for="encoder in project.encoders" :key="encoder.id">
                                    {{setName(encoder.profile.user)}} (Encoder)
                                </li>
                            </div>
                            <!-- <strong>General Objective</strong><br>
                            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Id distinctio aliquam reprehenderit iste amet quisquam accusamus, aperiam accusantium illo! Dolore facere vitae, consequatur nostrum cumque non fuga dolores culpa odit nihil quas consequuntur illum quos autem vero praesentium sequi quaerat quod alias saepe recusandae tenetur a? Consequatur veniam maxime minus voluptate vitae, sunt tempora autem voluptatem aliquam repellat enim laudantium?</p>
                            <strong>Specific Objective/s</strong>
                            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Minus commodi odio iste, atque quo vero. Placeat, nesciunt velit dolore cumque obcaecati autem molestiae facere ipsam repellat totam, nemo debitis dolor? Velit ducimus delectus earum non a facilis, quis nemo numquam sit deleniti adipisci obcaecati porro laudantium cum enim at. Nemo iure, sapiente suscipit eum tempora veritatis quos fugit odit excepturi ad eius, placeat est, neque accusamus commodi natus vitae! Voluptatum, delectus perspiciatis praesentium ipsum commodi fugit dolores possimus sequi aliquam.</p> -->
                            <strong>Benefeciaries</strong>
                            <table class="table table-sm table-bordered mb-2">
                                <thead class="text-center">
                                    <tr>
                                        <th></th>
                                        <th colspan="3" v-for="num in 3" :key="num">{{2020+num-1}}</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td class="text-start fw-bold">No. of Benefeciaries</td>
                                        <td>32</td><td>53</td><td>85</td>
                                        <td>40</td><td>62</td><td>102</td>
                                        <td>44</td><td>65</td><td>109</td>
                                        <td rowspan="3">286</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Gender</td>
                                        <td v-for="num in 9" :key="num">
                                            {{num%3 != 0 ? (num == 1 || num == 4 || num == 7 ? 'Male' : 'Female') : ''}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Type</td>
                                        <td v-for="num in 9" :key="num">
                                            {{num%3 != 0 ? 'Teacher' : ''}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <strong>Annual Budget</strong>
                            <table class="table table-sm table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th></th>
                                        <th v-for="num in 3" :key="num+'ab'">{{2020+num-1}}</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-end">
                                        <td class="text-start fw-bold">Budget</td>
                                        <td>P10,000,000.00</td>
                                        <td>P15,000,000.00</td>
                                        <td>P18,000,000.00</td>
                                        <td>P43,000,000.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Libs v-if="tab == 'libs'" @clicked="tab = ''" />
        <!-- Modal -->
        <div class="modal fade" id="description" tabindex="-1" v-if="!loading">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{project.title}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ref="Close"></button>
                    </div>
                    <div class="modal-body">
                        <strong>Brief Description</strong>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="floatingInput" placeholder=" " style="height: 200px" v-model="form.description"></textarea>
                            <label for="floatingInput">Brief Description</label>
                        </div>
                        <button @click="addEncoder()" class="btn btn-sm btn-success float-end"><i class="fas fa-plus"></i> Encoder</button>
                        <div class="mb-3"><strong>Project Leader</strong></div>
                        <div class="form-floating mb-3">
                            <select class="form-control" id="floatingInput" placeholder=" " v-model="form.leader.profile_id">
                                <template v-for="unithead in unitheads" :key="unithead.id">
                                    <option v-if="userProjectMatch(unithead)" :value="unithead.active_profile.id">{{setName(unithead)}} (Unit Head)</option>
                                </template>
                                <template v-for="leader in leaders" :key="leader.id">
                                    <option v-if="userProjectMatch(leader)" :value="leader.active_profile.id">{{setName(leader)}} (Project Leader)</option>
                                </template>
                            </select>
                            <label for="floatingInput">Project Leader</label>
                        </div>
                        <template v-if="form.encoders.length > 0">
                            <div class="encoders-container">
                                <strong>Project Encoder/s</strong>
                                <div class="form-floating mb-2 position-relative" v-for="fencoder, key in form.encoders" :key="key">
                                    <button @click="removeEncoder(fencoder)" class="btn btn-sm btn-outline-danger shadow-none position-absolute end-0 border-0 "><i class="fas fa-times"></i></button>
                                    <select class="form-control" id="floatingInput" placeholder=" ">
                                        <template v-for="encoder in encoders" :key="encoder.id">
                                            <option :value="encoder.active_profile.id">{{setName(encoder)}} (Encoder)</option>
                                        </template>
                                    </select>
                                    <label for="floatingInput">Project Encoder</label>
                                </div>
                            </div>
                        </template>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary rounded-pill" @click="submitForm()">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1 class="text-center m-5 p-5" v-else>Loading Portfolio <i class="far fa-spinner fa-spin" ></i></h1> 

</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import Libs from './Libs.vue'
export default {
    name: 'Porfolio',
    components: {
        Libs
    },
    data(){
        return {
            loading: true,
            tab: '',
            form: {
                id: '',
                description: '',
                leader: {},
                encoders: []
            }
        }
    },
    methods: {
        ...mapActions('project', ['fetchProject', 'updatePortfolio']),
        ...mapActions('user', ['fetchStaffs']),
        editForm(){
            var project = this.project
            this.form.id = project.id
            this.form.description = project.description

            this.form.leader = {
                id: project.leader.id,
                type: 'Leader',
                profile_id: project.leader.profile.id
            }

            for(let i = 0; i < project.encoders.length; i++){
                var encoder = project.encoders[i]
                var object = {
                    id: encoder.id,
                    type: 'Encoder',
                    profile_id: encoder.profile.id
                }
                this.form.encoders.push(object)
            }

        },
        submitForm(){
            if(this.form.description == ''){
                this.toastMsg('warning', 'Brief Description required');
                return false
            }
            for(let i = 0; i < this.form.encoders.length; i++){
                var encoder = this.form.encoders[i]
                if(encoder.profile_id == ''){
                    this.toastMsg('warning', 'Encoder required');
                    return false
                }
            }
            this.updatePortfolio(this.form).then(res => {
                this.toastMsg('success', 'Portfolio saved')
                this.$refs.Close.click()
            })
        },
        addEncoder(){
            this.form.encoders.push({
                id: '',
                type: 'Encoder',
                profile_id: ''
            })
        },
        removeEncoder(encoder){
            this.form.encoders.remove(encoder)
        },
        userProjectMatch(user){
            var userIds = {
                division_id: user.division_id,
                unit_id:     (user.unit_id)    ? user.unit_id    : 0,
                subunit_id:  (user.subunit_id) ? user.subunit_id : 0,
            }
            var project = this.project
            var projIds = {
                division_id: project.division_id,
                unit_id:     (project.unit_id)    ? project.unit_id    : 0,
                subunit_id:  (project.subunit_id) ? project.subunit_id : 0
            }

            var userStr = JSON.stringify(userIds)
            var projStr = JSON.stringify(projIds)

            return userStr == projStr
        },
        setName(user){
            return user.firstname + ' ' + user.lastname
        },
        // Toast
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
    },
    computed: {
        ...mapGetters('project', ['getProject']),
        project(){ return this.getProject },
        ...mapGetters('user', ['getStaffs']),
        unitheads(){ return (this.getStaffs['Unit Head'])       ? this.getStaffs['Unit Head']       : [] },
        leaders(){   return (this.getStaffs['Project Leader'])  ? this.getStaffs['Project Leader']  : [] },
        encoders(){  return (this.getStaffs['Staff / Encoder']) ? this.getStaffs['Staff / Encoder'] : [] },
    },
    created(){
        this.fetchProject(this.$route.params.id).then(res => {
            this.fetchStaffs().then(res => {
                this.loading = false
            })
        })
    }
}
</script>
<style scoped>
#portfolio{
    height: calc(100vh - 250px);
    padding-right: 10px;
    overflow: auto;
}
#btn:hover{
    background-color: rgba(0,0,0,0.02) !important;
    cursor: pointer;
    transform: scale(1.02);
    transition: width 0.3s;
}
.encoders-container{
    max-height: 30vh;
    overflow: auto;
    padding-right: 12px;
    margin-bottom: 1em;
}
</style>