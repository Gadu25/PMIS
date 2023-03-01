<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-outline-secondary mt-2 float-start" @click="$router.push({name: 'Reports'})" title="Back"><i class="fas fa-arrow-left"></i></button>
        <button class="btn btn-sm btn-success bg-gradient mt-2 float-end" @click="newReport()" data-bs-target="#modal" data-bs-toggle="modal" title="Add"><i class="fas fa-plus"></i></button>
        <h2 class="text-center">Annual Reports</h2><hr>
        <template v-if="!loading">
            <div class="row justify-content-center px-3">
                <div id="main-container">
                    <div class="card mb-3 border-0 shadow p-4" v-for="report in reports" :key="report.id">
                        <div class="card-body">
                            <div class="border-bottom pb-2 mb-2" id="details" @click="pdfshow = true, pdffile = report.pdf_file, year = report.year" data-bs-target="#modal" data-bs-toggle="modal">
                                <div id="img-container">
                                    <img :src="'/annual/thumbnails/'+report.thumbnail" width="180" height="200" alt="" class="border" v-bind:style="{'border-radius': '8px'}">
                                </div>
                                {{report.description}}
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold">Annual Report {{report.year}}</div>
                                <div>
                                    <button class="btn btn-primary bg-gradient me-1 border" @click="editForm(report)" data-bs-toggle="modal" data-bs-target="#modal" title="Edit"><i class="far fa-pencil-alt"></i></button>
                                    <button class="btn btn-danger bg-gradient border" @click="deleteForm(report.id)" title="Delete"><i class="far fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modal" tabindex="-1" :data-bs-backdrop="!pdfshow ? 'static' : 'none'">
                <div class="modal-dialog modal-dialog-centered" :class="pdfshow ? 'modal-xl' : 'modal-lg'">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" v-if="!pdfshow">{{editmode ? 'Edit' : 'New'}} Annual Report</h5>
                            <h5 class="modal-title" v-if="pdfshow">Annual Report {{year}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" ref="Close"></button>
                        </div>
                        <div class="modal-body" :class="pdfshow? 'p-0' : ''">
                            <div class="form-group row flex-wrap-reverse" v-if="!pdfshow">
                                <div class="col-sm-8">
                                    <div class="form-floating mb-2">
                                        <textarea class="form-control" placeholder=" " id="floatingTextarea2" style="height: 300px" v-model="form.description"></textarea>
                                        <label for="floatingTextarea2">Brief Description</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-floating mb-2">
                                        <select class="form-select" id="floatingSelect" v-model="form.year">
                                            <option value="" disabled hidden selected>Select Year</option>
                                            <option :value="year+1950" v-for="year in 200" :key="year">{{year+1950}}</option>
                                        </select>
                                        <label for="floatingSelect">Year</label>
                                    </div>
                                    <div class="mb-2">
                                        <label for="thumbnail" class="form-label">Thumbnail</label>
                                        <input class="form-control form-control-sm" id="thumbnail" accept="image/*" ref="thumbnail" @change="thumbnailChange" type="file">
                                    </div>
                                    <div class="mb-3">
                                        <label for="pdffile" class="form-label">PDF File</label>
                                        <input class="form-control form-control-sm" id="pdffile" accept="application/pdf" ref="pdf" @change="pdfChange" type="file">
                                    </div>
                                </div>
                            </div>
                            <div class="pdf-container" v-else style="height: 80vh">
                                <iframe :src="'/annual/pdfs/'+pdffile" height="100%" width="100%" frameborder="0"></iframe>
                            </div>
                        </div>
                        <div class="modal-footer" v-if="!pdfshow">
                            <button type="button" style="min-width: 100px;" class="btn rounded-pill btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" style="min-width: 100px;" class="btn rounded-pill" @click="submitForm()" :class="editmode ? 'btn-primary' : 'btn-success'">{{editmode ? 'Save changes' : 'Submit'}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <h1 v-else class="text-center p-5 m-5">Loading Annual Reports <i class="fas fa-spinner fa-spin"></i></h1>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AnnualReport',
    data(){
        return {
            form: {
                id: '',
                year: '',
                description: '',
                thumbnail: [],
                pdffile: []
            },
            editmode: false,
            loading: true,
            pdfshow: false,
            pdffile: '',
            year: ''
        }
    },
    methods: {
        ...mapActions('annual', ['fetchAnnualReports', 'saveAnnualReport', 'deleteAnnualReport']),
        newReport(){
            this.form.id = ''
            this.form.year = ''
            this.form.description = ''
            this.form.thumbnail = []
            this.form.pdffile = []
            this.$refs.thumbnail.value=null
            this.$refs.pdf.value=null
            this.editmode = false
            this.pdfshow = false
        },
        editForm(report){
            this.newReport()
            this.form.id = report.id
            this.form.year = report.year
            this.form.description = report.description
            this.editmode = true
        },
        submitForm(){
            if(this.checkForm()){
                this.saveAnnualReport(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.$refs.Close.click()
                    }
                })
            }
        },
        deleteForm(id){
            this.swalConfirmCancel('This is irreversible', 'Are you sure').then(res => {
                if(res == 'confirm'){
                    this.deleteAnnualReport(id).then(res =>{
                        this.toastMsg('success', 'Annual Report deleted!')
                    })
                }
            })
        },
        checkForm(){
            if(this.form.year == ''){ this.toastMsg('warning', 'Year required'); return false }
            if(this.form.description == ''){ this.toastMsg('warning', 'Brief Description required'); return false }
            if(!this.editmode){
                if(!this.form.thumbnail.name){ this.toastMsg('warning', 'Thumbnail required'); return false }
                if(!this.form.pdffile.name){ this.toastMsg('warning', 'PDF file required'); return false }
            }
            return true
        },
        thumbnailChange(e){
            this.form.thumbnail = (e.target.files[0]) ? e.target.files[0] : []
            if(e.target.files[0]){
                let type = e.target.files[0].type
                let condtype = type.split('/')
                if(condtype[0] != 'image' ){
                    this.form.thumbnail = []
                    this.$refs.thumbnail.value=null
                    this.toastMsg('warning', 'Please select an image file')
                }
            }
        },
        pdfChange(e){
            this.form.pdffile = (e.target.files[0]) ? e.target.files[0] : []
            if(e.target.files[0]){
                if(e.target.files[0].type != 'application/pdf' ){
                    this.form.pdffile = []
                    this.$refs.pdf.value=null
                    this.toastMsg('warning', 'Please select a PDF file')
                }
            }
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
        ...mapGetters('annual', ['getAnnualReports']),
        reports(){ return this.getAnnualReports }
    },
    created(){
        this.fetchAnnualReports().then(res => {
            this.loading = false
        })
    }
}
</script>
<style scoped>
.card{
    border-radius: 8px;
}
.card-body>img{
    border-radius: 10px;
}
#main-container{
    height: calc(100vh - 130px);
    overflow: auto;
    padding: 20px;
}
#img-container{
    float: left;
    margin-right: 10px;
}
#details{
    min-height: 220px;
}
#details:hover{
    cursor: pointer;
}
</style>