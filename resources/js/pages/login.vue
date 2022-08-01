<template>
    <div>
        <p v-if="error">{{error}}</p>
        <form @submit.prevent="login">
            <label for="email">Email Address</label>
            <input type="email" id="email" v-model="form.email"><br>
            <label for="password">Password</label>
            <input type="password" id="password" v-model="form.password"><br>
            <button type="submit">login</button>
        </form>
    </div>
</template>
<script>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { reactive, ref } from 'vue'
import { useStore } from 'vuex'
export default {
    setup(){
        const router = useRouter()
        const store = useStore()
        
        let form = reactive({
            email: '',
            password: ''
        })

        let error = ref('')

        const login = async() => {
            await axios.post('/api/login', form).then(res => {
                if(res.data.success){
                    localStorage.setItem('token', res.data.data.token)
                    router.push({name: 'Dashboard'})
                }
                else{
                    error.value = res.data.message
                }
            })
        }

        return {
            form,
            login,
            error
        }
    }
}
</script>
<style>
input{
    border: 1px solid
}
</style>