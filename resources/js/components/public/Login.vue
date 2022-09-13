<template>
    <div class="login-container">
        <div class="col-sm-8" style="height: 100%; background: rgba(0,0,0,0.5); padding: 70px">
            <router-link class="btn btn-sm btn-light" :to="{name: 'Home'}"><i class="far fa-home"></i> Home</router-link>
            <div class="text-center"><img :src="'/images/Logo.png'" alt="Logo" width="150"></div>
            <h2 class="text-center text-white"><strong>DOST-SEI Project Management Information System</strong></h2>
            <div class="d-flex justify-content-center">
                <div class="col-sm-8">
                    <div class="card">
                        <div class="card-body">
                            <p>Sign in here!</p>
                            <div class="alert alert-warning" role="alert" v-if="error">
                                <strong><i class="far fa-exclamation-triangle"></i></strong> {{error}}
                            </div>
                            <form @submit.prevent="submit()">
                                <div class="form-floating mb-3 mt-3">
                                    <input type="email" class="form-control" id="email" placeholder="email" v-model="form.email">
                                    <label for="email">Email address</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="password" placeholder="password" v-model="form.password">
                                    <label for="password">Password</label>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button v-if="!loading" type="submit" class="btn btn-primary rounded-pill" style="min-width: 100px">Login</button>
                                    <button v-else class="btn btn-secondary rounded-pill"><i class="fas fa-spinner fa-spin"></i> Loading</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    name: 'Login',
    data(){
        return {
            form: {
                email: '',
                password: ''
            },
            loading: false,
            error: ''
        }
    },
    methods: {
        ...mapActions('user', ['login']),
        submit(){
            this.loading = true
            this.login(this.form).then(res => {
                if(!res.data.success){
                    this.error = res.data.message
                    this.form.password = ''
                }
                this.loading = false
            })
        }
    }
}
</script>
<style scoped>
.login-container{
    height: 100vh;
    /* padding: 20px; */
    background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
