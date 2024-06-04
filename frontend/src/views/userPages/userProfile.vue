<template>
  <div>
    <div class="container mt-2">
      <div class="card">
        <div class="card-body">
          <div class="fs-3 text-center">
            <strong> Profile Picture </strong>
          </div>
          <hr />
          <div
            style="display: flex; justify-content: center; align-items: center"
          >
            <img
              :src="getImage(info.image_path)"
              alt=""
              class="img-fluid"
              style="height: 100px; width: 100px; border-radius: 50%"
            />
          </div>
          <div
            class="align-items-center justify-content-center text-center mt-2"
          >
            <button
              type="button"
              class="btn btn-primary"
              @click="clickShowProfileModal"
            >
              updateProfile
            </button>
          </div>

          <hr />
          <div class="fs-3 text-center">
            <strong> Personal Information </strong>
          </div>
          <div
            class="align-items-center justify-content-center text-center mt-2"
          >
            <button
              type="button"
              class="btn btn-primary"
              @click="clickShowInfoModal"
            >
              updateInfo
            </button>
          </div>
          <hr />
          <div class="text-center">
            <div class="d-flex flex-row justify-content-center">
              <div class="d-flex flex-column me-3">
                <div class="smalltext">
                  Last Name
                  <div class="fs-6">
                    <strong>
                      {{ info.lastname }}
                    </strong>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  First Name
                  <div class="fs-6">
                    <strong>
                      {{ info.firstname }}
                    </strong>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column me-3">
                <div class="smalltext">
                  Middle Name
                  <div class="fs-6">
                    <strong>
                      {{ info.middlename }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>
            <hr />
            <div class="d-flex flex-row mt-3 justify-content-center">
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  Gender
                  <div class="fs-6">
                    <strong>
                      {{ info.gender }}
                    </strong>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  Birthday
                  <div class="fs-6">
                    <strong>
                      {{ formattedDate }}
                    </strong>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  Age
                  <div class="fs-6">
                    <strong>
                      {{ calculateAge(formattedDate) }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div>
            <hr />
            <div class="d-flex flex-row mt-3 justify-content-center">
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  Sitio
                  <div class="fs-6">
                    <strong>
                      {{ info.street }}
                    </strong>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  Baranggay
                  <div class="fs-6">
                    <strong>
                      {{ info.baranggay }}
                    </strong>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  City
                  <div class="fs-6">
                    <strong>
                      {{ info.city }}
                    </strong>
                  </div>
                </div>
              </div>
              <div class="d-flex flex-column">
                <div class="smalltext me-3">
                  Province
                  <div class="fs-6">
                    <strong>
                      {{ info.province }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>
            <hr />
            <hr />
            <div class="fs-3 text-center">
              <strong> Change Password </strong>
            </div>
            <div
              class="align-items-center justify-content-center text-center mt-2"
            >
              <button
                type="button"
                class="btn btn-primary"
                @click="clickShowChangePassModal"
              >
                Change Password
              </button>
            </div>
            <hr />
          </div>
        </div>
      </div>
      <change-pass-modal
        @closeChangePassModal="closeChangePassModal"
        :show-change-pass-modal="showChangePassModal"
      />
      <change-profile
        @closeProfileModal="closeProfileModal"
        @restartInfo="restartInfo"
        :show-profile-modal="showProfileModal"
      />
      <change-info
        @closeInfoModal="closeInfoModal"
        :info="info"
        @restartInfo="restartInfo"
        :show-info-modal="showInfoModal"
      />
    </div>
  </div>
</template>

<script setup>
import AuthenticationService from "../../services/AuthenticationService";
import changePassModal from "../../components/changePassModal.vue";
import changeProfile from "../../components/changeProfile.vue";
import changeInfo from "../../components/changeInfo.vue";
import { ref, onMounted, watchEffect } from "vue";
import { getImage } from "../../services/newService";

const info = ref([]);
const showInfoModal = ref(false);
const showProfileModal = ref(false);
const showChangePassModal = ref(false);
const clickShowInfoModal = () => {
  restartInfo();
  showInfoModal.value = true;
};
const clickShowProfileModal = () => {
  showProfileModal.value = true;
};
const clickShowChangePassModal = () => {
  showChangePassModal.value = true;
};
const closeInfoModal = () => {
  showInfoModal.value = false;
};
const closeProfileModal = () => {
  showProfileModal.value = false;
};
const closeChangePassModal = () => {
  showChangePassModal.value = false;
};
const getInfo = async () => {
  try {
    const response = await AuthenticationService.getInfo();
    if (response) {
      info.value = response.data.info;
      formatDate(info.value.birthday);
    }
  } catch (error) {
    console.log(error);
  }
};

const calculateAge = (birthdate) => {
  // Get the current date
  var currentDate = new Date();

  // Convert the birthdate string to a Date object
  var birthDate = new Date(birthdate);

  // Calculate the age
  let birth = currentDate.getFullYear() - birthDate.getFullYear();

  // Check if birthday has occurred this year
  if (
    currentDate.getMonth() < birthDate.getMonth() ||
    (currentDate.getMonth() === birthDate.getMonth() &&
      currentDate.getDate() < birthDate.getDate())
  ) {
    birth--;
  }

  return birth;
};
watchEffect(() => {
  getInfo();
});
const restartInfo = onMounted(() => {
  getInfo();
});
let formattedDate;
function formatDate(inputDate) {
  const date = new Date(inputDate);

  // Get the month, day, and year
  const month = date.getMonth() + 1; // Months are zero-indexed, so add 1
  const day = date.getDate();
  const year = date.getFullYear();

  // Format the date as mm/dd/yyyy
  formattedDate = `${padZero(month)}/${padZero(day)}/${year}`;

  return formattedDate;
}
function padZero(number) {
  return number < 10 ? `0${number}` : `${number}`;
}
onMounted(async () => {
  await getInfo();
});
</script>

<style scoped>
.smalltext {
  font-size: 12px;
}
.card {
  background-color: beige;
}
</style>
