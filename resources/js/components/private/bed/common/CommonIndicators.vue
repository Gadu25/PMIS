<template>
    <div class="px-3 py-4">
        <router-link :to="{ name: 'Workshops' }" class="btn btn-sm btn-light float-start"><i class="fas fa-arrow-left"></i></router-link>
        <h2 class="text-center">Common Indicators</h2>
        <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small><hr>
        <div class="d-flex justify-content-center flex-wrap" v-if="form.program_id == ''">
            <div class="col-sm-4 program" v-for="program in programs" :key="program.id+'_program'">
                <div class="py-4 px-2" @click="selectProgram(program.id)">
                    <h1 class="text-center"><i class="far fa-4x" :class="program.id == 1 ? 'fa-user-graduate' : program.id == 2 ? 'fa-microscope' : 'fa-building'"></i></h1>
                    <h3 class="text-center">{{program.title}}</h3>
                </div>
            </div>
        </div>
        <div v-else>
            <button class="btn btn-sm btn-outline-secondary float-end" @click="form.program_id = ''">Change Program</button>
            <h3><i class="far" :class="program.id == 1 ? 'fa-user-graduate' : program.id == 2 ? 'fa-microscope' : 'fa-building'"></i> {{program.title}}</h3><hr>
            <div class="d-flex flex-wrap">
                <div class="col-sm-9 px-2 mb-3">
                    <div class="d-flex justify-content-center mb-2">
                        <button class="btn shadow-none me-1" @click="changeTab('Performance')" :class="selectedTab == 'Performance' ? 'btn-dark' : 'btn-outline-dark'">Performance Indicators</button>
                        <button class="btn shadow-none" @click="changeTab('Other')" :class="selectedTab == 'Other' ? 'btn-dark' : 'btn-outline-dark'">Outcome & Output Indicators</button>
                    </div>
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-success btn-sm shadow-none" data-bs-toggle="modal" data-bs-target="#form" @click="resetForm()"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="px-3 py-2">
                        <table class="table table-sm table-bordered table-hover shadow">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th v-if="selectedTab == 'Other'">Type</th>
                                    <th>{{selectedTab == 'Other' ? 'Tags' : 'Tag'}}</th>
                                    <th style="width: 77px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="commonindicator in commonindicators[form.program_id]" :key="commonindicator.id+'_indicator'">
                                    <tr>
                                        <td>{{commonindicator.description}}</td>
                                        <td v-if="selectedTab == 'Other'">{{commonindicator.type}}</td>
                                        <td><span v-for="tag in commonindicator.tags" :key="tag.id+'_indtag'">{{tag.name}}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1"><i class="far fa-pencil-alt"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    <tr v-for="subindicator in commonindicator.subindicators" :key="subindicator.id+'_subindicator'">
                                        <td :colspan="selectedTab == 'Other' ? 2 : 1"><div class="ms-3">{{subindicator.description}}</div></td>
                                        <td :colspan="selectedTab == 'Other' ? 1 : 2"><span v-for="tag in subindicator.tags" :key="tag.id+'_subtag'">{{tag.name}}</span></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-3 px-2 ">
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <strong>{{tag.id ? 'Edit' : 'New'}} Tag</strong>
                            <form @submit.prevent="submitTag()" class="form-floating mb-1">
                                <input type="text" class="form-control" id="tagname" placeholder="tag" v-model="tag.name">
                                <label for="tagname">Tag Name</label>
                            </form>
                            <div class="d-flex justify-content-end mb-3 pb-2 border-bottom">
                                <button tabindex="-1" class="btn shadow-none btn-outline-secondary btn-sm me-1" @click="tag.name = '', tag.id = ''">Clear Field</button>
                                <button tabindex="-1" class="btn shadow-none btn-sm" :class="tag.id ? 'btn-primary' : 'btn-success'" @click="submitTag()">{{tag.id ? 'Save changes' : 'Submit'}}</button>
                            </div>
                            <strong>Tags</strong>
                            <div class="d-flex justify-content-between p-2 border-bottom tag-wrap" v-for="tag in tags[form.program_id]" :key="tag.id+'_tag'">
                                <div class="w-75">{{tag.name}}</div>
                                <div class="w-25">
                                    <i class="far fa-pencil-alt" @click="editTag(tag)"></i> |
                                    <i class="far fa-trash-alt" @click="removeTag(tag.id)"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <strong>Total</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{selectedTab}} Indicators</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="Close"></button>
                </div>
                <div class="modal-body">
                    <strong>General Information</strong>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="IndicatorType" v-model="form.type">
                            <option value="" selected hidden disabled>Select Indicator Type</option>
                            <option v-if="selectedTab == 'Performance'" value="Performance">Performance Indicator</option>
                            <option v-if="selectedTab == 'Other'" value="Outcome">Outcome Indicator</option>
                            <option v-if="selectedTab == 'Other'" value="Output">Output Indicator</option>
                        </select>
                        <label for="IndicatorType">Indicator Type</label>
                    </div>
                    <div class="form-group row mb-2">
                        <div :class="clusters.length > 0 ? 'col-sm-6' : 'col-sm-12'">
                            <div class="form-floating mb-2">
                                <select class="form-select" id="Subprogram" v-model="form.subprogram_id" @change="subpChange()">
                                    <option value="" selected hidden disabled>Select Sub-Program</option>
                                    <option :value="subprogram.id" v-for="subprogram in program.subprograms" :key="subprogram.id+'_optSubprogram'">{{(program.id == 1) ? subprogram.title_short : subprogram.title}}</option>
                                    <option :value="0">N/A</option>
                                </select>
                                <label for="Subprogram">Sub-Program</label>
                            </div>
                        </div>
                        <div class="col-sm-6" v-if="clusters.length > 0">
                            <div class="form-floating mb-2">
                                <select class="form-select" id="Cluster" v-model="form.cluster_id">
                                    <option value="" selected hidden disabled>Select Cluster</option>
                                    <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id+'_optCluster'">{{cluster.title}}</option>
                                    <option :value="0">N/A</option>
                                </select>
                                <label for="Cluster">Cluster</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-success float-end" @click="addIndicator()"><i class="fas fa-plus"></i> Indicator</button>
                    <strong>Indicators</strong><hr>
                    <div class="indicator-form-container">
                        <div class="form-group row pb-2 mb-2" v-for="indicator, key in form.indicators" :key="'indicator_'+key">
                            <div class="col-sm-9">
                                <div class="form-floating mb-1">
                                    <textarea class="form-control shadow-none" v-model="indicator.description" placeholder="description" id="IndicatorDescription" style="min-height: 100px"></textarea>
                                    <label for="IndicatorDescription">Indicator Description</label>
                                </div>
                                <button @click="addSub(key)" class="btn btn-sm btn-success float-end shadow-none" style="height: 23px !important; padding: 0px 7px;"><i class="far fa-plus"></i> Sub-Indicator</button>
                                <strong v-if="indicator.subs.length > 0">Sub-Indicators</strong>
                                <div class="form-group row pb-2 mb-2 border-bottom" v-for="sub, skey in indicator.subs" :key="skey+'_sub_'+key">
                                    <div class="col-sm-8">
                                        <div class="form-floating mb-1">
                                            <textarea class="form-control shadow-none" v-model="sub.description" placeholder="description" id="SubIndicatorDescription" style="min-height: 100px"></textarea>
                                            <label for="SubIndicatorDescription">Sub-Indicator Description</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Tag/s: </strong>
                                        <div class="form-check" v-for="tag in tags[form.program_id]" :key="tag.id+'_tagCheck'">
                                            <input v-if="selectedTab != 'Performance'"  class="form-check-input shadow-none" type="checkbox" :value="tag.id" :id="tag.id+'_subtag_'+key" v-model="sub.tags">
                                            <input v-else class="shadow-none form-check-input" type="radio" :value="tag.id" :id="key+'_'+tag.id+'_subtag_'+skey" v-model="sub.tag">
                                            <label class="form-check-label" :for="key+'_'+tag.id+'_subtag_'+skey">{{tag.name}} </label>
                                        </div>
                                        <button @click="removeSub(key, sub)" class="btn btn-sm btn-danger float-end shadow-none mt-1" style="height: 23px !important; padding: 0px 7px;"><i class="far fa-trash-alt"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button @click="removeIndicator(indicator)" class="btn btn-sm btn-danger float-end shadow-none mb-1" style="height: 23px !important; padding: 0px 7px;"><i class="far fa-trash-alt"></i> Remove</button>
                                <strong>Tag/s: </strong>
                                <div class="form-check" v-for="tag in tags[form.program_id]" :key="tag.id+'_tagCheck'">
                                    <input v-if="selectedTab != 'Performance'"  class="form-check-input shadow-none" type="checkbox" :value="tag.id" :id="tag.id+'_tag_'+key" v-model="indicator.tags">
                                    <input v-else class="shadow-none form-check-input" type="radio" :value="tag.id" :id="tag.id+'_tag_'+key" v-model="indicator.tag">
                                    <label class="form-check-label" :for="tag.id+'_tag_'+key">{{tag.name}} </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" style="min-width: 100px;" class="btn rounded-pill btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button @click="submitForm()" type="button" style="min-width: 100px;" class="btn rounded-pill" :class="form.id ? 'btn-primary' : 'btn-success'">{{form.id ? 'Save changes' : 'Submit'}}</button>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'RequiredIndicators',
    data(){
        return {
            loading: true,
            form: {
                id: '',
                program_id: '',
                subprogram_id: '',
                cluster_id: '',
                type: 'Performance',
                workshop_id: this.$route.params.workshopId,
                indicators: [{ id: '', description: '', subs: [], tags: [], tag: 0 }]
            },
            tag: {
                id: '',
                name: '',
                program_id: '',
                type: 'workshop'
            },
            program: [],
            clusters: [],
            selectedTab: 'Performance'
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('program', ['fetchPrograms']),
        ...mapActions('tag', ['fetchWorkshopTags', 'saveTag', 'deleteTag']),
        ...mapActions('common', ['fetchCommonIndicators', 'saveCommonIndicator']),
        selectProgram(id){
            this.form.program_id = id
            this.tag.program_id = id
            this.program = this.programs.find(elem => elem.id == id)
        },
        submitForm(){
            if(this.formValidated()){
                this.saveCommonIndicator(this.form).then(res =>{
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.$refs.Close.click()
                    }
                })
            }
        },
        formValidated(){
            if(this.form.type == ''){ this.toastMsg('warning', 'Indicator Type Required'); return false }
            if(this.form.subprogram_id === ''){ this.toastMsg('warning', 'Sub-Program Required'); return false }
            if(this.clusters.length > 0){
                if(this.form.cluster_id === ''){ this.toastMsg('warning', 'Cluster Required'); return false }
            }
            for(let i = 0; i < this.form.indicators.length; i++){
                var indicator = this.form.indicators[i]
                if(indicator.description == ''){ this.toastMsg('warning', 'Description Required'); return false }
                if(this.selectedTab == 'Performance'){
                    if(indicator.tag == 0){ this.toastMsg('warning', 'Tag Required'); return false }
                }
                else{
                    if(indicator.tags.length == 0){ this.toastMsg('warning', 'Tag Required (at least one Tag)'); return false }
                }
                for(let j = 0; j < indicator.subs.length; j++){
                    var sub = indicator.subs[j]
                    if(sub.description == ''){ this.toastMsg('warning', 'Description Required (Sub-Indicator)'); return false }
                    if(this.selectedTab == 'Performance'){
                        if(sub.tag == 0){ this.toastMsg('warning', 'Tag Required (Sub-Indicator)'); return false }
                    }
                    else{
                        if(sub.tags.length == 0){ this.toastMsg('warning', 'Tag Required (at least one Tag: Sub-Indicator)'); return false }
                    }
                }
            }
            return true
        },

        // Form behavior
        addIndicator(){
            this.form.indicators.push({id: '', description: '', subs: [], tags: [], tag: 0})
        },
        removeIndicator(indicator){
            if(this.form.indicators.length > 1){
                this.form.indicators.remove(indicator)
            }
        },
        addSub(key){
            this.form.indicators[key].subs.push({id: '', description: '', tag: 0, tags: []})
        },
        removeSub(key, sub){
            this.form.indicators[key].subs.remove(sub)
        },
        resetForm(){
            this.form.id = ''
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
            this.clusters = []
            this.form.indicators = [{ id: '', description: '', subs: [], tags: [], tag: 0 }]
            this.form.type = (this.selectedTab == 'Performance') ? this.selectedTab : ''
        },
        changeTab(tab){
            this.selectedTab = tab
        },
        subpChange(){
            this.form.cluster_id = ''
            var clusters = []
            if(this.form.subprogram_id != 0){
                var subprogram = this.program.subprograms.find(elem => elem.id == this.form.subprogram_id)
                clusters = subprogram.clusters
            }
            this.clusters = clusters
        },
        // Tags
        editTag(tag){
            this.tag.id = tag.id
            this.tag.name = tag.name
        },
        submitTag(){
            if(this.tag.name == ''){
                this.toastMsg('warning', 'Tag name required')
            }
            else{
                this.saveTag(this.tag).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    this.tag.name = ''
                })
            }
        },
        removeTag(id){
            this.swalConfirmCancel('This is irreversible', 'Continue?').then(res => {
                if(res == 'confirm'){
                    this.deleteTag(id).then(res => {
                        var icon = res.errors ? 'error' : 'success'
                        this.toastMsg(icon, res.message)
                    })
                }
            })
        },
        // Toasts
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
        async swalConfirmCancel(title, message){
            const res = await swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Continue!',
                reverseButtons: true
                }).then((result) => {
                if (result.isConfirmed) {
                    return 'confirm'
                }
                else{
                    return 'cancel'
                }
            })
            return res
        },
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        ...mapGetters('common', ['getCommonIndicators']),
        commonindicators(){ return this.getCommonIndicators },
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
        ...mapGetters('tag', ['getTags']),
        tags(){ return this.getTags },
    },
    created(){
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            this.fetchWorkshopTags()
            this.fetchCommonIndicators(this.$route.params.workshopId).then(res =>{
                this.loading = false
            })
        })
        if(this.programs.length == 0){
            this.fetchPrograms()
        }
    }
}
</script>
<style scoped>
.program:hover>div{
    color: #006aff ;
    cursor: pointer;
}
.program:hover>div>h3,
.program:hover>div>h1>i{
    transform: scale(1.1);
}

.tag-wrap:hover{
    background: lightgray;
}
.tag-wrap>.w-25{
    cursor: pointer;
}
.indicator-form-container{
    max-height: 50vh;
    overflow-y: auto;
    overflow-x: hidden;
}
</style>