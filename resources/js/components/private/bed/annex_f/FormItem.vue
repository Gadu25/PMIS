<template>
    <template v-for="item in items" :key="item.id+'-item'">
        <template v-if="checkUserDivision(item.projects) || isAdmin">
            <template v-if="isUserProjectLeader(item.projects) || isHead || isChief || isAdmin">
                <tr>
                    <td><div class="ms-3"><i class="fas" :class="setStatusIcon(item.status)"></i> {{setItemTitle(item.projects)}}</div></td>
                    <td v-if="!isAdmin">{{getProjectLeader(item.projects)}}</td>
                    <td v-if="!isAdmin">{{getCluster(item.projects)}}</td>
                    <td :class="checkUserDivision(item.projects) ? 'btns' : ''">
                        <button class="btn btn-sm btn-primary bg-gradient mb-1" @click="childClick(item, setItemTitle(item.projects), 'editform')" 
                        v-if="(inUserRole('annex_f_edit_details') && checkUserDivision(item.projects) && (item.status == 'Draft' ? isUserProjectLeader(item.projects) : true )) || isAdmin"><i class="far" :class="statusNewDraft(item.status) ? 'fa-pencil-alt' : 'fa-search'"></i> Details</button><br> 
                        <!-- <button class="btn btn-sm btn-danger" v-if="statusNewDraft(item.status)"><i class="far fa-trash-alt"></i> Remove</button> -->
                        <button class="btn btn-sm btn-warning bg-gradient mb-1" @click="childClick(item, setItemTitle(item.projects), 'history')"
                        v-if="item.histories.length > 0 | isAdmin" data-bs-toggle="modal" data-bs-target="#history"><i class="far fa-clipboard-list"></i> Logs</button>
                    </td>
                </tr>
                <tr v-for="sub in item.subs" :key="sub.id+'sub-item'">
                    <td :colspan="isAdmin ? 2 : 4"><div class="fs-14 fst-italic ms-4">{{sub.subproject.title}}</div></td>
                </tr>
            </template>
        </template>
    </template>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    name: 'FormItem',
    emits: ['clicked'],
    setup({emit}){},
    methods: {
        childClick(item, title, type){
            this.$emit('clicked', item, title, type)
        },
        checkUserDivision(projects){
            var user = this.authuser
            var project = projects[0]
            var userObject = {
                division_id: user.division_id,
                unit_id:     user.unit_id,
                subunit_id:  user.subunit_id
            }
            var projectObject = {
                division_id: project.division_id,
                unit_id:     user.unit_id === null   ? null : project.unit_id,
                subunit_id:  user.subunit_id == null ? null : project.subunit_id
            }
            var userStr = JSON.stringify(userObject)
            var projStr = JSON.stringify(projectObject)

            return (userStr === projStr || user.active_profile.title.name == 'Superadmin')
        },
        setStatusIcon(status){
            return status == 'New' ? 'fa-sparkles text-warning' : 
            status == 'Draft' ? 'fa-edit' :
            status == 'For Review' ? 'fa-search' : 
            status == 'For Approval' ? 'fa-clipboard-list-check' : 
            status == 'Approved' ? 'fa-clipboard-check text-success' : 'fa-badge-check text-info'
        },
        setItemTitle(projects){
            var project = projects[0]
            var title = project.title
            if(projects.length > 1){
                title = project.subprogram.title
            }
            return title
        },
        statusNewDraft(status){
            return (status == 'New' || status == 'Draft') && !this.saving
        },
        isUserProjectLeader(projects){
            var state = false
            var profileId = this.authuser.active_profile.id
            for(let project of projects){
                if(!state && project.leader){
                    state = profileId == project.leader.profile_id
                }
                if(!state && project.encoders){
                    for(let encoder of project.encoders){
                        if(!state){
                            state = profileId == encoder.profile_id
                        }
                    }
                }
            }
            return state
        },
        inUserRole(code){
            var role = this.authuser.active_profile.roles.find(elem => elem.code == code)
            return (role)
        },
        getProjectLeader(projects){
            var prev = ''
            var fullname = ''
            for(let i = 0; i < projects.length; i++){
                var project = projects[i]
                var leader = project.leader.profile.user
                var name = leader.firstname+' '+leader.lastname
                fullname  = prev && prev != name ? fullname+' & '+name : name
                prev = name
            }
            return fullname
        },
        getCluster(projects){
            var cluster = ''
            for(let project of projects){
                if(!cluster){
                    cluster = project.cluster_id ? project.cluster.title : project.subprogram.title_short
                }
            }
            return cluster
        }
    },
    computed: {
        ...mapGetters('user', ['getAuthUser']),
        authuser(){ return this.getAuthUser },
        isAdmin(){ return this.authuser.active_profile.title.name == 'Superadmin' },
        isHead(){  return this.authuser.active_profile.title.name == 'Unit Head' },
        isChief(){ return this.authuser.active_profile.title.name == 'Division Chief' },

    },
    props: {
        items: Object,
        saving: Boolean,
    }
}
</script>
<style scoped>
.btns{
    text-align: center;
    /* display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%; */

}
.btns>button{
    width: 90px;
}
</style>