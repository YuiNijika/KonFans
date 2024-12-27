<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const wallpapers = ref([]);

async function fetchWallpapers() {
  try {
    const response = await axios.get('https://api.x-x.work/get/Wallpaper/KON?Mobile');
    wallpapers.value = response.data;
  } catch (error) {
    console.error('Error fetching wallpapers:', error);
  }
}

onMounted(() => {
  fetchWallpapers();
  window.ViewImage && ViewImage.init('.Wallpaper img');
});
document.title = '轻音少女手机壁纸';
</script>

<template>
  <div class="Wallpaper">
    <div v-for="(wallpaper, index) in wallpapers" :key="index" class="Wallpaper-item">
      <div class="mdui-m-b-2">
        <a href="javascript:;">
          <img :src="wallpaper.Url" :alt="wallpaper.File" />
        </a>
      </div>
    </div>
  </div>
</template>
