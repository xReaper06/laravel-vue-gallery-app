<template>
  <div>
    <div class="modal" v-if="props.showChangePassModal">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">
              Change Password
            </h1>
            <span class="close" @click="closeChangePassModal"
              ><svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon icon-tabler icon-tabler-x"
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
                <path d="M18 6l-12 12" />
                <path d="M6 6l12 12" /></svg
            ></span>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="form-group">
                <label for="newPassword" class="form-label">New Password</label>
                <input
                  type="password"
                  id="newPassword"
                  class="form-control"
                  v-model="newPassword"
                  required
                  @keyup="handleStrongVerification"
                />
                <div v-if="showPassmsg">
                  <p class="invalid-password">
                    {{ passmsg }}
                  </p>
                </div>
                <label for="confirmPassword" class="form-label"
                  >Confirm New Password</label
                >
                <input
                  type="password"
                  id="confirmPassword"
                  class="form-control"
                  v-model="confirmPassword"
                  required
                  @keyup="checkifThesame"
                />
              </div>
              <div v-if="showCheckPass">
                <p :class="texterror">
                  {{ checkmsg }}
                </p>
              </div>
              <div v-if="showSubmitButton">
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="submitChangePassword"
                  @touchstart="submitChangePassword"
                  @touchend="submitChangePassword"
                >
                  <span>Change Password</span>
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
import { ref, defineProps, defineEmits, computed } from "vue";
import AuthenticationService from "../services/AuthenticationService";
import { successRes, errorRes } from "../services/newService";

const props = defineProps({
  showChangePassModal: Boolean, // Whether to show the modal
});

const emits = defineEmits(["closeChangePassModal"]);

const closeChangePassModal = () => {
  emits("closeChangePassModal");
};
const loading = ref(false);
const confirmPassword = ref("");
const newPassword = ref("");
const showPassmsg = ref(false);
const passmsg = ref(null);
const checkmsg = ref(null);
const showCheckPass = ref(false);
const showSubmitButton = ref(false);

const handleStrongVerification = () => {
  const minLength = 8;
  const hasUppercase = /[A-Z]/.test(newPassword.value);
  const hasLowercase = /[a-z]/.test(newPassword.value);
  const hasNumber = /\d/.test(newPassword.value);

  // Check if the newPassword meets the criteria
  if (
    newPassword.value.length >= minLength &&
    hasUppercase &&
    hasLowercase &&
    hasNumber
  ) {
    showPassmsg.value = false;
  } else if (newPassword.value == "") {
    showPassmsg.value = false;
  } else {
    showPassmsg.value = true;
    passmsg.value = "newPassword is Weak";
  }
};
const checkifThesame = () => {
  if (
    newPassword.value === confirmPassword.value &&
    confirmPassword.value != ""
  ) {
    showCheckPass.value = true;
    showSubmitButton.value = true;
    checkmsg.value = "Password is valid";
  } else if (confirmPassword.value == "") {
    showCheckPass.value = false;
  } else {
    showSubmitButton.value = false;
    showCheckPass.value = true;
    checkmsg.value = "Password is not The Same";
  }
};
const texterror = computed(() => {
  return {
    "valid-password":
      newPassword.value === confirmPassword.value &&
      confirmPassword.value != "",
    "invalid-password":
      confirmPassword.value != "" && newPassword.value != confirmPassword.value,
  };
});

const submitChangePassword = async () => {
  try {
    const response = await AuthenticationService.changePassword({
      password: newPassword.value,
      cpassword: confirmPassword.value,
    });
    loading.value = true;
    if (response) {
      setTimeout(() => {
        newPassword.value = "";
        confirmPassword.value = "";
        showCheckPass.value = false;
        showPassmsg.value = false;
        successRes(response.data.message);
        loading.value = false;
      }, 1000);
      setTimeout(() => {
        closeChangePassModal();
      }, 1000);
    }
  } catch (error) {
    console.log(error);
    errorRes(error.response.data.message);
  }
};
</script>

<style scoped>
.modal-body {
  overflow-y: scroll;
}
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.unread {
  background-color: #f7f7f7;
  border-left: 4px solid #2196f3;
}
.modal-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  position: relative;
}

.close {
  cursor: pointer;
}
.invalid-password {
  color: red;
}
.valid-password {
  color: rgb(10, 206, 10);
}
</style>
