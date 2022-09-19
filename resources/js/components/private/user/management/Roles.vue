<template>
    <form @submit.prevent="submitForm()">
        <template v-if="formshow">
            <button v-if="!editmode" class="float-end btn btn-sm btn-success" @click.prevent="addRole()"><i class="fas fa-plus"></i></button>
            <h5 class="mb-3">{{!editmode ? 'New' : 'Edit'}} Role</h5>
            <div class="form-floating mb-3 px-2">
                <select class="form-control" id="Tab" v-model="form.tab">
                    <option value="" selected disabled hidden>Select Sidebar Tab</option>
                    <option :value="item.id" v-for="item in sidebaritems" :key="item.id+'-sidebaritem'">{{item.name}}</option>
                </select>
                <label for="Tab">Sidebar Tab</label>
            </div>
            <div class="row px-2 role-container">
                <div :class="!editmode ? 'col-sm-6' : 'col-sm-12'" class="p-2 position-relative" v-for="role, key in form.roles" :key="key+'_role-form'">
                    <button v-if="!editmode" class="btn btn-sm btn-outline-danger position-absolute end-0 me-2 rounded-0 border-0 shadow-none" @click.prevent="removeRole(role)" style="z-index: 999"><i class="fas fa-times"></i></button>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" :id="'Title'+key" v-model="role.title" placeholder=" ">
                        <label :for="'Title'+key">Title</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" :id="'Code'+key" v-model="role.code" placeholder=" ">
                        <label :for="'Code'+key">Code</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" :id="'Description'+key" v-model="role.description" placeholder=" " style="resize: none; height: 100px"></textarea>
                        <label :for="'Description'+key">Description</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end flex-wrap">
                <button class="btn rounded-pill min-100 me-2 mb-1 btn-secondary" @click="formshow = false">Cancel</button>
                <button class="btn rounded-pill min-100 me-2 mb-1" type="submit" :class="!editmode ? 'btn-success' : 'btn-primary'">{{!editmode? 'Submit' : 'Save Changes'}}</button>
            </div>
        </template>
    </form>
    <div v-if="!formshow">
        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-sm btn-success" @click="resetForm()"><i class="fas fa-plus"></i></button>
        </div>
        <div class="table-responsive display">
            <table class="table table-sm table-bordered">
                <thead class="align-middle text-center bg-warning position-sticky top-0">
                    <tr>
                        <th>Title</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th style="width: 100px !important;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="item in sidebaritems" :key="item.id+'-display'">
                        <tr class="fw-bold bg-success text-white bg-gradient text-center" v-if="item.roles.length > 0">
                            <td colspan="4">{{item.name}}</td>
                        </tr>
                        <tr v-for="role in item.roles" :key="role.id+'-role_display'">
                            <td>{{role.title}}</td>
                            <td>{{role.code}}</td>
                            <td>{{role.description}}</td>
                            <td class="align-middle text-center">
                                <button class="btn btn-sm btn-primary mb-1 me-1" @click="editForm(role)"><i class="far fa-pencil-alt"></i></button>
                                <button class="btn btn-sm btn-danger mb-1 me-1" @click="deleteRole(role.id)"><i class="far fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    import { mapActions, mapGetters } from 'vuex'
    export default {
        name: 'Roles',
        data(){
            return {
                editmode: false,
                formshow: false,
                form: {
                    tab: '',
                    roles: [{
                        title: '',
                        code: '',
                        description: ''
                    }],
                }
            }
        },
        methods: {
            ...mapActions('roles', ['fetchSidebarItems', 'saveRoles', 'removeRoles']),
            resetForm(){
                this.form.tab = ''
                this.form.id = ''
                this.form.roles = [{
                    title: '',
                    code: '',
                    description: ''
                }]
                this.editmode = false
                this.formshow = true
            },
            submitForm(){
                if(this.formValidated()){
                    this.saveRoles(this.form).then(res => {
                        var icon = res.errors ? 'error' : 'success'
                        if(!res.errors){
                            this.formshow = false
                        }
                        this.toastMsg(icon, res.message)
                    })
                }
            },
            editForm(role){
                this.resetForm()
                this.editmode  = true
                this.form.tab  = role.sidebar_item_id
                this.form.id   = role.id
                var roleform   = this.form.roles[0]
                roleform.title = role.title
                roleform.code  = role.code
                roleform.description = role.description
            },
            deleteRole(id){
                this.swalConfirmCancel('This is irreversable!', 'Are you sure?').then(result => {
                    if(result == 'confirm'){
                        this.removeRoles(id).then(res => {
                            var icon = res.errors ? 'error' : 'success'
                            this.toastMsg(icon, res.message)
                        })
                    }
                })
            },
            formValidated(){
                var form = this.form
                if(form.tab == ''){ return this.throwWarning('Sidebar Tab required') }
                for(let i = 0; i < form.roles.length; i++){
                    var role = form.roles[i]
                    if(role.title == ''){ return this.throwWarning('Title required') }
                    if(role.code == ''){  return this.throwWarning('Code required') }
                    if(role.description == ''){ return this.throwWarning('Description required') }
                }
                return true
            },
            throwWarning(message){
                this.toastMsg('warning', message)
                return false
            },
            addRole(){
                this.form.roles.push({
                    title: '',
                    code: '',
                    description: ''
                })
            },
            removeRole(role){
                if(this.form.roles.length > 1){
                    this.form.roles.remove(role)
                }
            },
            toastMsg(icon, message){
                toast.fire({
                    icon: icon,
                    title: message
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
            ...mapGetters('roles', ['getSidebarItems']),
            sidebaritems(){ return this.getSidebarItems }
        },
        created(){
            if(this.sidebaritems.length == 0){
                this.fetchSidebarItems()
            }
        }
    }
</script>
<style scoped>
.min-100{
    min-width: 100px;
}
.role-container{
    max-height: calc(100vh - 310px);
    overflow: auto;
    margin-bottom: 0.5em;
}
</style>