<template>
  <div v-if="props.openModal" class="modal">
    <div class="modal-content">
      <span class="close" @click="closeModal">&times;</span>
      <div v-if="showFile" class="image-container mb-3">
        <img
          :src="filePrev.preview"
          alt="Selected Image"
          class="preview-image img-fluid"
          style="width: 50dvw; height: 50dvh"
        />
      </div>
      <label for="">project Images:</label>
      <div class="form-group mt-3">
        <input
          type="file"
          class="form-control"
          name="image"
          @change="handleImageChange"
          accept="image/jpeg image/png image/jpg"
        />
      </div>
      <hr />
      <button type="button" class="btn btn-primary" @click="updateImage">
        Save
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import imageCompression from "browser-image-compression";
import { errorRes, successRes } from "../services/newService";
import AuthenticationService from "../services/AuthenticationService";

const props = defineProps({
  openModal: Boolean,
  image_id: Number,
});
const emit = defineEmits(["closeModal", "getImages"]);
const closeModal = () => {
  emit("closeModal");
  filePrev.value.preview = "";
  imageData.value = null;
};
const getImages = () => {
  emit("getImages");
};

const imageData = ref(null);

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

      console.log(compressedFileObject);
      console.log(file[0]);
      // Assign the compressed file to formData.value.image
      imageData.value = compressedFileObject;

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

const updateImage = async () => {
  try {
    const data = new FormData();
    data.append("image", imageData.value);
    data.append("image_id", props.image_id);
    const response = await AuthenticationService.updateImage(data);
    if (response) {
      getImages();
      closeModal();
      successRes(response.data.message);
    }
  } catch (error) {
    errorRes(error.response.data.message);
  }
};
</script>

<style scoped>
.modal {
  display: block;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100dvw;
  height: 100dvh;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: #fff;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 90dvw;
  max-width: 80dvw;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>
