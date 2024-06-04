<template>
  <div class="container mt-5">
    <div class="fs-2 text-center">
      Welcome to: <br /><strong>Gallery App</strong>
    </div>
    <div class="card">
      <div class="card-header">
        <div class="fs-4 text-center">
          <strong>Sign In</strong>
        </div>
      </div>
      <div class="card-body">
        <div class="col-md-6 offset-md-3">
          <form @submit.prevent="handleLogin">
            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="text"
                class="form-control"
                id="email"
                placeholder="Enter email"
                v-model="email"
              />
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input
                type="password"
                class="form-control"
                id="password"
                placeholder="Password"
                v-model="password"
              />
            </div>

            <!-- Remember Me -->
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe" />
              <label class="form-check-label" for="rememberMe"
                >Remember me</label
              >
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn btn-primary btn-block">
              <span v-if="!loginloading">Login</span>
              <span v-else>loading...</span>
            </button>
          </form>
          <div class="mt-3 text-end">
            <router-link to="/userRegistration" class="btn btn-success"
              >Sign Up</router-link
            >
          </div>
          <!-- Forgot Password Link -->
          <div class="mt-3 text-center">
            <router-link to="/forgotPassword">Forgot password?</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import AuthenticationService from "../services/AuthenticationService";
import { useAuthStore } from "../store";
import { successRes, errorRes } from "../services/newService";
import router from "../router";

const email = ref("");
const password = ref("");
const loginloading = ref(false);
const user = ref("");
const authStore = useAuthStore();

const getUser = localStorage.getItem("user");
user.value = JSON.parse(getUser);

const handleLogin = async () => {
  if (email.value === "" || password.value === "") {
    errorRes("please fill empty fields");
    return; // Exit function early if empty fields
  }

  try {
    const response = await AuthenticationService.login({
      email: email.value,
      password: password.value,
    });

    if (response.data.success) {
      // Check for success in response
      successRes(response.data.message);
      authStore.setTokens(response.data.data.accessToken);
      authStore.setUser(response.data.data.user);
      router.push("/user/userDashboard");
    } else {
      errorRes(response.data.message); // Handle login failure
    }
  } catch (error) {
    errorRes(error.response.data.message); // Handle errors
  }
};

const checkIfNotSignOut = () => {
  let userString = localStorage.getItem("user");
  const user = userString ? JSON.parse(userString) : {}; // Parse if not null, otherwise empty object
  let accessToken = localStorage.getItem("accessToken");

  // Check if accessToken exists or user is not an empty object
  if (accessToken || Object.keys(user).length > 0) {
    router.push("/user/userDashboard");
  }
};

onMounted(() => {
  checkIfNotSignOut();
});
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.card {
  width: 100%;
  max-width: 400px;
  margin: auto;
  margin-top: 20px;
  background-color: beige;
}
</style>
