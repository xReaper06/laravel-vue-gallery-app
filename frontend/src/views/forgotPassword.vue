<template>
    <div>
        <div class="container mt-5">
    <div class="col-md-6 offset-md-3">
        <form>
            <!-- Email -->
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" placeholder="Enter your email" v-model="email">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block" @click="sendForgotPassword">
                <span v-if="!loading">Reset Password</span>
                <span v-else>loading...</span></button>
        </form>

        <!-- Back to Login Link -->
        <div class="mt-3 text-center">
            <p>Remember your password? <router-link to="/">Login here</router-link></p>
        </div>
    </div>
</div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios'

const email = ref('');
const msg = ref('')
const loading = ref(false)

const sendForgotPassword = async()=>{
    loading.value = true;
    try {
        const response = await axios.post('http://localhost:8081/api/forgot-password',{
            email:email.value
        })
        if(response){
            setTimeout(() => {
           loading.value = false     
                email.value = null;
                msg.value = response.data.msg;
            }, 1000);
        }
    } catch (error) {
        setTimeout(() => {
            console.log(error)
           loading.value = false     
            }, 1000);
    }
}

</script>

<style scoped>

</style>