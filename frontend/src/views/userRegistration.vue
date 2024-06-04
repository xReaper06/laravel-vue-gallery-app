<template>
  <div class="container mt-5 mb-5">
    <div class="card">
      <div class="card-header">
        <div class="fs-4 text-center">
          <strong> Sign Up </strong>
        </div>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleRegistration">
          <div v-if="showFile" class="mb-3">
            <img :src="filePrev.preview" alt="" class="h-80 w-80 img-fluid" />
          </div>
          <div v-else>
            <img src="../assets/vue.svg" alt="" class="h-80 w-80 img-fluid" />
          </div>

          <!-- Image Input -->
          <div class="mb-3">
            <label for="imageInput" class="form-label">Profile Image</label>
            <input
              type="file"
              class="form-control"
              id="imageInput"
              accept="image/jpg,image/png,image/jpeg"
              @change="handleImageChange"
            />
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input
              type="email"
              class="form-control"
              id="email"
              placeholder="Enter email"
              v-model="formdata.email"
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
              @keyup="isStrongPassword"
              v-model="formdata.password"
            />
            <div class="text-danger" v-if="passmsgVisible">
              {{ passmsg }}
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label for="confirmPassword" class="form-label"
              >Confirm Password</label
            >
            <input
              type="password"
              class="form-control"
              id="confirmPassword"
              placeholder="Confirm Password"
              v-model="formdata.cpassword"
            />
          </div>

          <!-- First Name -->
          <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input
              type="text"
              class="form-control"
              id="firstName"
              placeholder="First Name"
              v-model="formdata.first_name"
            />
          </div>

          <!-- Last Name -->
          <div class="mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input
              type="text"
              class="form-control"
              id="lastName"
              placeholder="Last Name"
              v-model="formdata.last_name"
            />
          </div>

          <!-- Middle Name -->
          <div class="mb-3">
            <label for="middleName" class="form-label">Middle Name</label>
            <input
              type="text"
              class="form-control"
              id="middleName"
              placeholder="Middle Name"
              v-model="formdata.middle_name"
            />
          </div>
          <!-- Gender -->
          <div class="mb-3">
            <label class="form-label me-2">Gender :</label>
            <div class="form-check form-check-inline">
              <input
                type="radio"
                id="male"
                name="gender"
                class="form-check-input"
                value="male"
                v-model="formdata.gender"
              />
              <label class="form-check-label" for="male">Male</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                type="radio"
                id="female"
                name="gender"
                class="form-check-input"
                value="female"
                v-model="formdata.gender"
              />
              <label class="form-check-label" for="female">Female</label>
            </div>
          </div>

          <!-- Birthday -->
          <div class="mb-3">
            <label for="birthday" class="form-label">Birthday</label>
            <input
              type="date"
              class="form-control"
              id="birthday"
              v-model="formdata.birthday"
              @change="updateAge"
            />
          </div>

          <!-- Age -->
          <div class="mb-3">
            <label for="age" class="form-label">Age</label>
            <input
              type="number"
              class="form-control"
              id="age"
              placeholder="Age"
              disabled
              v-model="formdata.age"
            />
          </div>

          <div class="new-group mb-3">
            <label for="" class="form-label">Sitio/Street</label>
            <input type="text" class="form-control" v-model="formdata.street" />
          </div>
          <div class="new-group mb-3">
            <label for="" class="form-label">Baranggay</label>
            <input
              type="text"
              class="form-control"
              v-model="formdata.baranggay"
            />
          </div>
          <div class="new-group mb-3">
            <label for="" class="form-label">City</label>
            <input type="text" class="form-control" v-model="formdata.city" />
          </div>
          <div class="new-group mb-3">
            <label for="" class="form-label">Province</label>
            <input
              type="text"
              class="form-control"
              v-model="formdata.province"
            />
          </div>
          <div class="form-check mb-3">
            <input
              class="form-check-input"
              type="checkbox"
              value=""
              id="invalidCheck"
              required
            />
            <label class="form-check-label" for="invalidCheck">
              Agree to terms and conditions
            </label>
          </div>

          <button type="submit" class="btn btn-primary">
            <span>Sign up</span>
            <i class="mdi mdi-chevron-right"></i>
          </button>
        </form>
        <div class="text-center">
          I Already have an <router-link to="/">account</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  width: 500px;
  background-color: beige;
}
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.img-thumbnail {
  max-width: 100px;
  max-height: 100px;
}

.text-danger {
  color: #dc3545;
}
</style>

<script setup>
import { ref, onMounted } from "vue";
import AuthenticationService from "../services/AuthenticationService";
import imageCompression from "browser-image-compression";
import { successRes, errorRes } from "../services/newService";
import { localAPI } from "../services/myAPI";
import router from "../router";

const formdata = ref({
  email: "",
  password: "",
  cpassword: "",
  image: null,
  first_name: "",
  last_name: "",
  middle_name: "",
  birthday: "",
  gender: "",
  street: "",
  baranggay: "",
  city: "",
  age: 0,
  province: "",
});
const updateAge = () => {
  calculateAge(formdata.value.birthday);
};

const calculateAge = (birthdate) => {
  // Get the current date
  var currentDate = new Date();

  // Convert the birthdate string to a Date object
  var birthDate = new Date(birthdate);

  // Calculate the age
  formdata.value.age = currentDate.getFullYear() - birthDate.getFullYear();

  // Check if birthday has occurred this year
  if (
    currentDate.getMonth() < birthDate.getMonth() ||
    (currentDate.getMonth() === birthDate.getMonth() &&
      currentDate.getDate() < birthDate.getDate())
  ) {
    formdata.value.age--;
  }

  return formdata.value.age;
};
const filePrev = ref({ preview: "" });
const showFile = ref(false);

const MAX_FILE_SIZE_MB = 2;

const handleImageChange = async (e) => {
  const file = e.target.files;
  if (file.length > 0) {
    const options = {
      maxSizeMB: MAX_FILE_SIZE_MB,
      maxWidthOrHeight: 1920,
      useWebWorker: true,
      webkitRelativePath: "",
    };

    try {
      const compressedFile = await imageCompression(file[0], options);
      console.log("Original File size:", file[0].size / (1024 * 1024), "MB");
      console.log(
        "Compressed file size:",
        compressedFile.size / (1024 * 1024),
        "MB"
      );

      // Create a new File object with the compressed file data and MIME type
      const compressedFileObject = new File([compressedFile], file[0].name, {
        type: file[0].type,
      });

      // Assign the compressed file to formData.value.image
      formdata.value.image = compressedFileObject;

      console.log(compressedFileObject);
      console.log(file[0]);

      const reader = new FileReader();
      reader.onload = (event) => {
        filePrev.value = {
          name: compressedFile.name,
          preview: event.target.result,
        };
        showFile.value = true;
      };
      reader.readAsDataURL(compressedFile);
    } catch (error) {
      console.error("Image compression error:", error);
    }
  }
};

const handleRegistration = async () => {
  if (
    formdata.value.image == null ||
    formdata.value.email == "" ||
    formdata.value.password == "" ||
    formdata.value.first_name == "" ||
    formdata.value.middle_name == "" ||
    formdata.value.last_name == "" ||
    formdata.value.birthday == "" ||
    formdata.value.gender == "" ||
    formdata.value.street == "" ||
    formdata.value.baranggay == "" ||
    formdata.value.city == "" ||
    formdata.value.province == ""
  ) {
    errorRes("Please Fill in Empty fields");
  } else {
    try {
      let data = new FormData();
      data.append("image", formdata.value.image);
      data.append("email", formdata.value.email);
      data.append("password", formdata.value.password);
      data.append("c_password", formdata.value.cpassword);
      data.append("firstname", formdata.value.first_name);
      data.append("middlename", formdata.value.middle_name);
      data.append("lastname", formdata.value.last_name);
      data.append("birthday", formdata.value.birthday);
      data.append("gender", formdata.value.gender);
      data.append("street", formdata.value.street);
      data.append("baranggay", formdata.value.baranggay);
      data.append("city", formdata.value.city);
      data.append("province", formdata.value.province);
      const response = await AuthenticationService.register(data);
      if (response) {
        successRes(response.data.message);
        formdata.value.image == null;
        formdata.value.email = "";
        formdata.value.password = "";
        formdata.value.cpassword = "";
        formdata.value.first_name = "";
        formdata.value.middle_name = "";
        formdata.value.last_name = "";
        formdata.value.birthday = "";
        formdata.value.gender = "";
        formdata.value.street = "";
        formdata.value.baranggay = "";
        formdata.value.city = "";
        formdata.value.province = "";
        filePrev.value = { preview: "" };
        showFile.value = false;
        router.push("/");
      } else {
        errorRes(response.data.message);
      }
    } catch (error) {
      console.log(error);
      errorRes(error.response.data.message);
    }
  }
};
onMounted(() => {
  console.log(localAPI());
});
</script>
