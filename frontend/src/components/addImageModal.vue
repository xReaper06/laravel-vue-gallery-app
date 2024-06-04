<template>
  <div v-if="props.openModal" class="modal">
    <div class="modal-content">
      <span class="close" @click="closeModal">&times;</span>
      <div v-if="showFile">
        <Swiper
          :modules="modules"
          :slides-per-view="1"
          :space-between="10"
          :navigation="true"
          :pagination="{ clickable: true }"
          :autoplay="{
            delay: 1000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
          }"
          @swiper="onSwiper"
          @slideChange="onSlideChange"
        >
          <template v-for="file in filePrev.file" :key="file.name">
            <SwiperSlide class="backGround">
              <img
                :src="file.preview"
                style="width: 100%; height: auto; object-fit: contain"
                alt=""
              />
            </SwiperSlide>
          </template>
        </Swiper>
      </div>
      <label for="">project Images:</label>
      <div class="form-group mt-3">
        <input
          type="file"
          class="form-control"
          multiple
          name="image"
          @change="handleImageChange"
          accept="image/jpeg image/png image/jpg"
        />
      </div>
      <hr />
      <button type="button" class="btn btn-primary" @click="addImages">
        Save
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import imageCompression from "browser-image-compression";
import SwiperCore from "swiper";
import { Navigation, Pagination, Scrollbar, Autoplay } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/scrollbar";
import { errorRes, successRes } from "../services/newService";
import AuthenticationService from "../services/AuthenticationService";
SwiperCore.use([Navigation, Pagination, Scrollbar, Autoplay]);

const onSwiper = (swiper) => {
  console.log(swiper);
};

const onSlideChange = () => {
  console.log("slide change");
};

const modules = ref([Navigation, Pagination, Scrollbar, Autoplay]);

const props = defineProps({
  openModal: Boolean,
});
const emit = defineEmits(["closeModal", "getImages"]);
const closeModal = () => {
  emit("closeModal");
  filePrev.value.file = [];
  formData.value.image = [];
};
const getImages = () => {
  emit("getImages");
};

const formData = ref({
  image: [],
});

const filePrev = ref({ file: [], OtherProperty: "Images" });
const showFile = ref(false);

const MAX_FILE_SIZE_MB = 2;

const handleImageChange = async (e) => {
  const file = e.target.files;
  if (file.length > 0) {
    const options = {
      maxSizeMB: MAX_FILE_SIZE_MB,
      maxWidthOrHeight: 1920,
      useWebWorker: true,
    };

    try {
      filePrev.value.file = [];

      for (let i = 0; i < file.length; i++) {
        const compressedFile = await imageCompression(file[i], options);
        console.log("original File size", file[0].size / (1024 * 1024), "MB");
        console.log(
          "Compressed file size:",
          compressedFile.size / (1024 * 1024),
          "MB"
        );

        const compressedFileObject = new File([compressedFile], file[i].name, {
          type: file[i].type,
        });

        formData.value.image.push(compressedFileObject);

        const reader = new FileReader();
        reader.onload = (event) => {
          filePrev.value.file.push({
            name: compressedFile.name,
            preview: event.target.result,
          });
          showFile.value = true;
        };
        reader.readAsDataURL(compressedFile);
      }
    } catch (error) {
      console.error("Image compression error:", error);
    }
  }
};

const addImages = async () => {
  let data = new FormData();
  for (let i = 0; i < formData.value.image.length; i++) {
    data.append("images[]", formData.value.image[i]);
  }
  try {
    const response = await AuthenticationService.insertImages(data);
    if (response) {
      successRes(response.data.message);
      formData.value.image = [];
      getImages();
      closeModal();
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
