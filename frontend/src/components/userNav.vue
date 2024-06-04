<template>
  <div>
    <nav class="navbar navbar-expand-lg bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">
          <!-- <img src="@/assets/vevsLogo.png" alt="" class="img-fluid rounded float-start sm-5 ms-5" height="70" width="70"/> -->
          <div class="fs-3 text-light">
            <strong>Gallery App</strong>
          </div>
        </a>
        <button
          class="navbar-toggler btn btn-dark bg-light"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
          @click="clickshowCollapse"
        >
          <span class="navbar-toggler-icon text-light"></span>
        </button>
        <div
          class="collapse navbar-collapse"
          :class="{ show: showCollapse }"
          id="navbarNav"
        >
          <ul class="navbar-nav">
            <li
              class="nav-item"
              :class="{ 'active-link': isActive('/user/userDashboard') }"
            >
              <router-link to="/user/userDashboard" class="nav-link text-white"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="icon icon-tabler icon-tabler-home"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                  stroke="currentColor"
                  fill="none"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                  <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                  <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>
                Home</router-link
              >
            </li>
            <li
              class="nav-item"
              :class="{ 'active-link': isActive('/user/profile') }"
            >
              <router-link to="/user/profile" class="nav-link text-white"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="icon icon-tabler icon-tabler-id-badge-2"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                  stroke="currentColor"
                  fill="none"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M7 12h3v4h-3z" />
                  <path
                    d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6"
                  />
                  <path
                    d="M10 3m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"
                  />
                  <path d="M14 16h2" />
                  <path d="M14 12h4" />
                </svg>
                Profile</router-link
              >
            </li>
          </ul>
          <ul class="navbar-nav ms-auto me-5 pe-3">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle text-white"
                @click="clickShowNav"
                href="#"
                role="button"
                aria-expanded="false"
              >
                <img
                  :src="getImage(name.image)"
                  alt=""
                  class="img-fluid"
                  style="height: 50px; width: 50px; border-radius: 50%"
                />
                {{ name.email }}
              </a>
              <ul class="dropdown-menu mt-1" :class="{ show: showNav }">
                <li>
                  <a class="dropdown-item" href="#" @click="logout"
                    ><svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="icon icon-tabler icon-tabler-power"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      stroke-width="2"
                      stroke="currentColor"
                      fill="none"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M7 6a7.75 7.75 0 1 0 10 0" />
                      <path d="M12 4l0 8" /></svg
                    >Log Out</a
                  >
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
  <router-view></router-view>

  <hr class="my-4 mt-5 text-dark" />
</template>

<script setup>
// eslint-disabled
import { ref } from "vue";
import { useRouter } from "vue-router";
import { getImage } from "../services/newService";
import { useAuthStore } from "../store";
import AuthenticationService from "../services/AuthenticationService";

const router = useRouter();
const user = ref("");
const name = ref("");
const username = ref("");
const authStore = useAuthStore();

const showNav = ref(false);
const showCollapse = ref(false);

user.value = localStorage.getItem("user");
username.value = JSON.parse(user.value);
name.value = username.value;

const logout = async () => {
  try {
    const response = await AuthenticationService.logout();
    if (!response) {
      console.log("Cannot Logout");
    }
    authStore.logOut();
    authStore.$reset();
    resetAllUserData();
    router.push("/");
  } catch (err) {
    console.error("Error Logout");
  }
};

const resetAllUserData = () => {
  authStore.accessToken = "";
  authStore.refreshToken = "";
  authStore.user = ""; //
};

const clickshowCollapse = () => {
  showCollapse.value = !showCollapse.value;
};

const clickShowNav = () => {
  showNav.value = !showNav.value;
};
const isActive = (route) => router.currentRoute.value.path === route;
</script>

<style scoped>
/* You can add custom styles here if needed */
.active-link {
  background-color: #341442;
}
.nav-link:hover {
  background-color: rgb(26, 20, 20);
}
.dropdown-item:hover {
  background-color: rgb(22, 21, 21);
  color: white;
}
</style>
