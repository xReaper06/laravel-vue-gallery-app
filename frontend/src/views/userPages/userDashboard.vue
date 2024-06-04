<template>
  <div
    class="btn btn-success mb-3 justify-content-end mt-2 ms-2"
    @click="openImageModal"
  >
    Add Images
  </div>
  <div class="gallery-container">
    <div
      class="gallery-item"
      v-for="(image, index) in allImages"
      :key="image.id"
    >
      <img :src="getImage(image.image_path)" :alt="`Image ${index}`" />
      <div class="actions">
        <button @click="openEditModal(image.id)">🖊️</button>
        <button @click="verifyDeleteFunc(image.id)">🗑️</button>
      </div>
    </div>
  </div>
  <addImageModal
    :openModal="ImageModal"
    @closeModal="closeImageModal"
    @getImages="getGallery"
  />
  <editImageModal
    :openModal="EditModal"
    :image_id="edit_id"
    @closeModal="closeEditModal"
    @getImages="getGallery"
  />
</template>

<script setup>
import { onMounted, ref } from "vue";
import AuthenticationService from "../../services/AuthenticationService";
import addImageModal from "../../components/addImageModal.vue";
import editImageModal from "../../components/editImageModal.vue";
import { errorRes, getImage } from "../../services/newService";
import Swal from "sweetalert2";

const allImages = ref([]);
const EditModal = ref(false);
const edit_id = ref(0);
const openEditModal = (id) => {
  EditModal.value = true;
  edit_id.value = id;
};
const closeEditModal = () => {
  EditModal.value = false;
};
const ImageModal = ref(false);
const openImageModal = () => {
  ImageModal.value = true;
};
const closeImageModal = () => {
  ImageModal.value = false;
};
const getGallery = async () => {
  try {
    const response = await AuthenticationService.getImages();
    if (response) {
      allImages.value = response.data.images;
    }
  } catch (error) {
    errorRes(error.response.message);
  }
};

onMounted(() => {
  getGallery();
});

const verifyDeleteFunc = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      handDeleteFunc(id);
    }
  });
};

const handDeleteFunc = async (id) => {
  try {
    const response = await AuthenticationService.deleteImage({
      image_id: id,
    });
    if (response) {
      Swal.fire({
        title: "Deleted!",
        text: response.data.message,
        icon: "success",
      });
      getGallery();
    }
  } catch (error) {
    console.log(error);
  }
};
</script>

<style scoped>
@media screen and (min-width: 800px) {
  .gallery-container {
    display: grid;
    grid-template-columns: repeat(4, minmax(200px, 1fr));
    gap: 20px;
  }
}

.gallery-item {
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.gallery-item img {
  width: 100%;
  height: auto;
  display: block;
}

.gallery-item:hover {
  transform: scale(1.05);
}
.actions {
  position: absolute;
  top: 0;
  right: 0;
  display: flex;
  flex-direction: column;
  opacity: 0;
  transition: opacity 0.2s;
}

.actions button {
  background: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  cursor: pointer;
  padding: 5px;
  margin: 2px;
}

.actions button:hover {
  background: rgba(0, 0, 0, 0.7);
}

.gallery-item:hover .actions {
  opacity: 1;
}
@media screen and (max-width: 750px) {
  .gallery-container {
    display: grid;
    grid-template-columns: repeat(auto, minmax(1%, 1fr));
    gap: 2%;
  }
}
</style>
