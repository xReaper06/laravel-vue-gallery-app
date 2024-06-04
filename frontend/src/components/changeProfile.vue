<template>
  <div>
    <div class="modal mb-5 pb-5" v-if="props.showProfileModal">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-header">
          <div class="modal-content">
            <div class="alert alert-success" v-if="sucss">{{ sucss }}</div>
            <div class="alert alert-danger" v-if="err">{{ err }}</div>
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                Change Profile Picture
              </h1>
              <span class="close" @click="closeModal"
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
              <div>
                <div v-if="showFile" class="image-container mb-3">
                  <img
                    :src="filePrev.preview"
                    alt="Selected Image"
                    class="preview-image img-fluid"
                    style="
                      width: 100px;
                      height: 100px;
                      border-radius: 50%;
                      align-items: center;
                    "
                  />
                </div>
                <div class="file-upload mb-3">
                  <input
                    type="file"
                    id="file"
                    @change="handleImageChange"
                    accept="image/png, image/jpeg, image/jpg"
                  />
                </div>

                <button
                  class="change-profile-button btn btn-primary"
                  type="button"
                  @click="ChangeProfilePic"
                  @touchstart="ChangeProfilePic"
                  @touchend="ChangeProfilePic"
                >
                  <span v-if="!loading">Change Profile Picture</span>
                  <span v-else>loading....</span>
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
import { ref, defineProps, defineEmits } from "vue";
import imageCompression from "browser-image-compression";
import AuthenticationService from "../services/AuthenticationService";

const props = defineProps({
  showProfileModal: Boolean,
});

const emits = defineEmits(["closeProfileModal", "restartInfo"]);

const closeModal = () => {
  emits("closeProfileModal");
};
const restartInfo = () => {
  emits("restartInfo");
};
const loading = ref(false);
const imageData = ref(null);
const filePrev = ref({ preview: "" });
const showFile = ref(false);
const profile = ref("");
const sucss = ref("");
const err = ref("");

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
      imageData.value = compressedFileObject;

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

const ChangeProfilePic = async () => {
  try {
    const data = new FormData();
    data.append("image", imageData.value);
    const response = await AuthenticationService.updateProfile(data);
    if (response) {
      loading.value = true;
      setTimeout(() => {
        profile.value = "";
        showFile.value = false;
        sucss.value = response.data.msg;
        loading.value = false;
        restartInfo();
      }, 2000);
      setTimeout(() => {
        closeModal();
      }, 2000);
    }
  } catch (error) {
    console.log(error);
    err.value = error.response.data.msg;
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
</style>
