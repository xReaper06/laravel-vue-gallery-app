import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from "../store";
import { checkIfTheresUserLogin } from '../services/newService';
import userLogin from '../views/userLogin.vue'
import userRegistration from '../views/userRegistration.vue'
import changeForgotPass from '../views/changeForgotPass.vue';
import verifyAccount from '../views/verifyAccount.vue'
import verificationAccount from '../views/verificationAccount.vue';
import forgotPassword from '../views/forgotPassword.vue';
import userNav from '../components/userNav.vue'
import userDashboard from '../views/userPages/userDashboard.vue'
import userProfile from '../views/userPages/userProfile.vue'

const routes = [
  {
    path: '/',
    name: 'userLogin',
    component: userLogin
  },
  {
    path: '/userRegistration',
    name: 'userRegistration',
    component: userRegistration
  },
  {
    path: '/forgotPassword',
    name: 'forgotPassword',
    component: forgotPassword
  },
  {
    path: '/changeForgotPass/:token',
    name: 'changeForgotPass',
    component: changeForgotPass,
    props: true,
    params: true
  },
  {
    path: '/verifyAccount/:token',
    name: 'verifyAccount',
    component: verifyAccount,
    props: true,
    params: true
  },
  {
    path: '/verificationAccount/:token',
    name: 'verificationAccount',
    component: verificationAccount,
    props: true,
    params: true
  },
  {
    path: '/user/',
    name: 'userNav',
    component: userNav,
    meta: { requiresAuth: true },
    beforeEnter: async (to, from, next) => {
      const data = await checkIfTheresUserLogin();
      if (data == true) {
        next("/");
      } else {
        next();
      }
    },
    children: [
      {
        path: 'userDashboard',
        name: 'userDashboard',
        component: userDashboard,
      },
      {
        path: 'profile',
        name: 'userProfile',
        component: userProfile,
      }
    ]
  }

]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})


router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  // Check if the user is authenticated
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Redirect to the login page or an unauthorized page
    next('/'); // Adjust the destination route as needed
  } else {
    // No specific role or authentication requirement, proceed
    next();
  }
});


export default router
