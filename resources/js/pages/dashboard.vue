<template>
    <div>
        dashboard <br>
        <button @click="logout">Logout</button>
    </div>
</template>
<script>
import { useRouter } from 'vue-router'
    export default {
        setup(){
            const router = useRouter()
            function logout(){
                localStorage.removeItem('token')
                router.push({name: 'Home'})
            }

            return {
                logout
            }
        }
    }
</script>