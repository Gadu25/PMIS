<template>
    <div class="p-2">
        <h2 class="text-center pb-2 mb-2 border-bottom">Resources and Publications</h2>
        <div class="publications-container">
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-sm btn-success bg-gradient" @click="newForm()" data-bs-toggle="modal" data-bs-target="#modal"><i class="fas fa-plus"></i></button>
            </div>
            <p class="fw-bold"><i class="fas fa-cog fa-spin"></i> Under Construction</p>
            <ul>
                <li v-for="division in data" :key="division.name">
                    <span @click="division.show = !division.show">{{division.name}} <i class="far " :class="division.show ? 'fa-caret-up' : 'fa-caret-down'"></i></span>
                    <ul v-if="division.show">
                        <li v-for="year in division.years" :key="division.name+'_'+year.year">
                            <span @click="year.show = !year.show">{{year.year}} <i class="far " :class="year.show ? 'fa-caret-up' : 'fa-caret-down'"></i></span>
                            <ul v-if="year.show">
                                <li v-for="title in year.titles" :key="division.name+'_'+year.year+'_'+title.title">
                                    <span @click="title.show = !title.show">{{title.title}} <i class="far " :class="title.show ? 'fa-caret-up' : 'fa-caret-down'"></i></span>
                                    <ul v-if="title.show">
                                        <li v-for="category in title.categories" :key="division.name+'_'+year.year+'_'+title.title+'_'+category.name">
                                            <span @click="category.show = !category.show">{{category.name}} <i class="far " :class="category.show ? 'fa-caret-up' : 'fa-caret-down'"></i></span>
                                            <ul v-if="category.show">
                                                <li v-for="file in category.files" :key="division.name+'_'+year.year+'_'+title.title+'_'+category.name+'_'+file.name">
                                                    {{file.name}}
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{editmode ? 'Update' : 'New'}} Files</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-sm-8">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="floatingSelect" v-model="form.division_id">
                                        <option value="" hidden disabled selected>Select Division</option>
                                        <option :value="division.id" v-for="division in divisions" :key="division.id">{{division.name}}</option>
                                    </select>
                                    <label for="floatingSelect">Division</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="floatingSelect" v-model="form.year">
                                        <option value="" hidden disabled selected>Select Year</option>
                                        <option :value="year+1950" v-for="year in 200" :key="year">{{year+1950}}</option>
                                    </select>
                                    <label for="floatingSelect">Year</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" placeholder=" " v-model="form.title">
                            <label for="floatingInput">Title</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="floatingSelect" v-model="form.category" @change="categoryChange()">
                                <option value="" hidden disabled selected>Select Category</option>
                                <option value="Books / Publications">Books / Publications</option>
                                <option value="AVPs and Photos">AVPs and Photos</option>
                                <option value="Issuances (Internal)">Issuances (Internal)</option>
                                <option value="Issuances (External)">Issuances (External)</option>
                            </select>
                            <label for="floatingSelect">Category</label>
                        </div>
                        <div class="mb-2">
                            Upload Options: 
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="device" id="device" value="device" v-model="form.uploadoption">
                                <label class="form-check-label" for="device">From device</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="link" id="link" value="link" v-model="form.uploadoption">
                                <label class="form-check-label" for="link">From link</label>
                            </div>
                        </div>
                        <div class="mb-3" v-if="form.uploadoption == 'device'">
                            <label for="formFile" class="form-label">Files</label>
                            <input class="form-control" type="file" id="formFile" multiple ref="files" @change="fileSelect">
                        </div>
                        <div class="form-floating mb-3" v-else>
                            <input type="text" class="form-control" id="floatingInput" placeholder=" " v-model="form.link">
                            <label for="floatingInput">Link</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" @click="submitForm()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Publications',
    data(){
        return {
            editmode: false,
            form: {
                id: '',
                division_id: '',
                category: '',
                title: '',
                year: '',
                files: [],
                link: '',
                uploadoption: 'device'
            },
            data: [
                {
                    name: 'Office of the Director (OD)',
                    show: false,
                    years: [
                        { year: 2020, show: false, titles: [{title: 'od folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                        ]}]},
                        { year: 2021, show: false, titles: [{title: 'od folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (Internal)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                        { year: 2022, show: false, titles: [{title: 'od folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (External)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                    ]
                },
                {
                    name: 'Science and Technology Scholarship Division (STSD)',
                    show: false,
                    years: [
                        { year: 2020, show: false, titles: [{title: 'stsd folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                        ]}]},
                        { year: 2021, show: false, titles: [{title: 'stsd folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (Internal)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                    ]
                },
                {
                    name: 'Science Education and Innovations Division (SEID)',
                    show: false,
                    years: [
                        { year: 2020, show: false, titles: [{title: 'seid folder 1', show: false, categories: [
                            {name: 'Books / Publications', files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                        ]}]},
                        { year: 2021, show: false, titles: [{title: 'seid folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (Internal)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                        { year: 2022, show: false, titles: [{title: 'seid folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (External)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                    ]
                },
                {
                    name: 'S&T Manpower Education Research and Promotions Division (STMERPD)',
                    show: false,
                    years: [
                        { year: 2020, show: false, titles: [{title: 'stmerpd folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                        ]}]},
                        { year: 2021, show: false, titles: [{title: 'stmerpd folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (Internal)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                    ]
                },
                {
                    name: 'Finance and Administrative Division (FAD)',
                    show: false,
                    years: [
                        { year: 2020, show: false, titles: [{title: 'fad folder 1', show: false, categories: [
                            {name: 'Books / Publications', files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                        ]}]},
                        { year: 2021, show: false, titles: [{title: 'fad folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (Internal)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                        { year: 2022, show: false, titles: [{title: 'fad folder 1', show: false, categories: [
                            {name: 'Books / Publications', show: false, files: [{name: 'book 1'}, {name: 'book 2'}, {name: 'publication 1'}]},
                            {name: 'AVPs & Photos',        show: false, files: [{name: 'image 1'}, {name: 'image 2'}, {name: 'video 1'}]},
                            {name: 'Issuances (External)', show: false, files: [{name: 'issuance 1'}]},
                        ]}]},
                    ]
                },
            ]
        }
    },
    methods: {
        ...mapActions('division', ['fetchDivisions']),
        ...mapActions('publication', ['fetchPublications']),
        newForm(){
            var date = new Date
            this.editmode = false
            this.form.id = ''
            this.form.division_id = ''
            this.form.title = ''
            this.form.year = date.getFullYear()
            this.form.files = []
            this.$refs.files.value = null
        },
        fileSelect(e){
            if(this.form.category == ''){
                this.toastMsg('warning', 'Please select a Category first')
                this.$refs.files.value = null
                return false
            }
            var types = this.form.category == 'AVPs and Photos' ?
                ['image', 'video', 'presentation'] : ['pdf']
            this.form.files = (e.target.files) ? e.target.files : []
            for(let file of this.form.files){
                var state = false
                for(let type of types){
                    if(!state){
                        state = file.type.includes(type)
                    }
                }
                if(!state){
                    var message = this.form.category + ' accepted file types: ' + types
                    this.toastMsg('warning', message)
                    this.$refs.files.value = null
                    return false
                }
            }
        },
        categoryChange(){
            this.form.files = []
            this.$refs.files.value = null
        },
        submitForm(){
            if(this.checkForm()){
                alert('submitted')
            }
        },
        checkForm(){
            var icon = 'warning'
            if(this.form.division_id == ''){ this.toastMsg(icon, 'Division required'); return false }
            if(this.form.year == ''){ this.toastMsg(icon, 'Year required'); return false }
            if(this.form.title == ''){ this.toastMsg(icon, 'Title required'); return false }
            if(this.form.category == ''){ this.toastMsg(icon, 'Category required'); return false }
            if(this.form.files.length == 0){ this.toastMsg(icon, 'Files required'); return false }
            return true
        },
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
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions },
        ...mapGetters('publication', ['getPublications']),
        publications(){ return this.getPublications },
    },
    created(){
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
        this.fetchPublications().then(res => {
            console.log(res)
        })
    }
}
</script>
<style scoped>
.publications-container{
    padding: 6px;
    max-height: calc(100vh - 120px);
    overflow: auto;
}
ul {
    list-style-type: none;
    margin: 6px 0;
}
li {
    margin-bottom: 12px;
}
li > span {
    font-weight: bold;
    padding: 6px 12px;
    border-radius: 0.25em;
    width: 100%;
    margin: 6px 0;
}
li > span:hover {
    cursor: pointer;
    background: rgba(0,0,0,0.1);
}
</style>