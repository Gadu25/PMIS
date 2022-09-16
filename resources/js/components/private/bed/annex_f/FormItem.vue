<template>
    <template v-for="item in items" :key="item.id+'-item'">
        <tr>
            <td :colspan="checkUserDivision(item.projects) ? '1' : '2'"><div class="ms-3"><i class="fas" :class="setStatusIcon(item.status)"></i> {{setItemTitle(item.projects)}}</div></td>
            <td class="btns" v-if="checkUserDivision(item.projects)">
                <button class="btn btn-sm btn-outline-secondary mb-1" @click="childClick(item, setItemTitle(item.projects), 'editform')"><i class="far" :class="statusNewDraft(item.status) ? 'fa-pencil-alt' : 'fa-search'"></i> Details</button>    
                <button class="btn btn-sm btn-outline-secondary mb-1" @click="childClick(item, setItemTitle(item.projects), 'history')" v-if="item.histories.length > 0" data-bs-toggle="modal" data-bs-target="#history"><i class="far fa-clipboard-list"></i> Logs</button>
                <!-- <button class="btn btn-sm btn-danger" v-if="statusNewDraft(item.status)"><i class="far fa-trash-alt"></i> Remove</button> -->
            </td>
        </tr>
        <tr v-for="sub in item.subs" :key="sub.id+'sub-item'">
            <td colspan="2"><div class="fs-14 fst-italic ms-4">{{sub.subproject.title}}</div></td>
        </tr>
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
    },
    computed: {
        ...mapGetters('user', ['getAuthUser']),
        authuser(){ return this.getAuthUser },
    },
    props: {
        items: Object,
        saving: Boolean,
    }
}
</script>
<style scoped>
.btns{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

}
.btns>button{
    width: 90px;
}
</style>