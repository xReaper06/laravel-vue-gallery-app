<template>
    <div>
        <div class="authentication">

<!-- v-alert -->
<div v-if="error" class="alert alert-danger">
  {{ error }}
</div>

<!-- v-container -->
<div class="container-fluid mt-3 pt-5 px-3">

  <!-- v-card -->
  <div class="card mx-auto pt-5 mt-5 registration shadow-lg text-center" style="max-width: 344px;">

    <!-- Title -->
    <p class="font-weight-bold" style="font-size: 24px; color: #007BFF;">Account is Not verified</p>

    <!-- v-container for button -->
    <div class="container">
      <button class="btn btn-primary btn-block" @click="sendEmailtoVerify">
        <span v-if="!loading">Send to your Email</span>
        <span v-else>loading...</span></button>
    </div>

    <!-- v-divider -->
    <hr class="my-4">

    <!-- v-container for logout button -->
    <div class="container">
      <!-- v-card-actions -->
      <div class="card-actions mb-3">
        <!-- v-btn with Bootstrap classes -->
        <router-link to="/">
        <button class="btn btn-danger btn-block">
            Sign in
          </button>
        </router-link>
        </div>
      </div>

  </div> <!-- end v-card -->

</div> <!-- end v-container -->

</div> <!-- end authentication div -->

    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import {ref} from 'vue'
import axios from 'axios';

const token = ref('')
const loading = ref(false)
const router = useRouter();
const routes = useRoute();
token.value = routes.params.token;

const sendEmailtoVerify = async()=>{
    loading.value = true
  const headers = {
            'Content-Type': 'application/json', // Specifies the content type as JSON
            'Authorization': `Bearer ${token.value}`, // An example of an authorization header
            'X-Custom-Header': 'CustomValue', // A custom header with a custom value
        };
        const config = { headers }

    try {
        const response = await axios.post('http://localhost:8081/api/send-email-verification',{},config)
        if(response){
            setTimeout(() => {
                loading.value = false
                console.log(response.data.msg)
                router.push('/')
            }, 1000);
        }
    } catch (error) {
        setTimeout(() => {
                loading.value = false
                console.log(error)
            }, 1000);
    }
}


</script>

<style scoped>

</style>