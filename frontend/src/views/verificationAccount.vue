<template>
    <div>
        <div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 mt-5">
      <!-- Add 'card-deck' class for responsive card layout -->
      <div class="card-deck mt-5">
        <div class="card">
          <div class="card-body">
            <h2 class="text-center">Account Verification</h2>
            
              <button type="submit" class="btn btn-primary btn-block mt-3" @click="handleVerificationEmail">
                <span v-if="!loading">Verify Account</span>
                <span v-else>loading...</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios'
const token = ref('')
const route = useRoute();
const router = useRouter();
const loading = ref(false)
token.value = route.params.token;
const handleVerificationEmail = async () => {
    loading.value = true
    const headers = {
            'Content-Type': 'application/json', // Specifies the content type as JSON
            'Authorization': `Bearer ${token.value}`, // An example of an authorization header
            'X-Custom-Header': 'CustomValue', // A custom header with a custom value
        };
    
        const config = { headers }
    try {
        const response = await axios.post('http://localhost:8081/api/verify-acc',
        {},config)
            
        if (response) {
            setTimeout(() => {
                loading.value = false
                router.push('/');
            }, 1000);
        }
    } catch (error) {
        setTimeout(() => {
                loading.value = false
                console.log(error);
            }, 1000);
    }
}

</script>

<style scoped>

</style>