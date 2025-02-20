<template>
    <v-card class="Wallpaper" flat style="margin-top: 20px;">
        <div v-for="(wallpaper, index) in displayedWallpapers" :key="`wallpaper-${index}`" class="Wallpaper-item">
            <img v-lazy="wallpaper.Url" :alt="wallpaper.File" @load="handleImageLoad" class="wallpaper-image" />
        </div>
    </v-card>
    <div v-if="hasMore" style="text-align:center;margin-top: 10px;">
        <v-btn color="primary" @click="loadMore" :loading="isLoading">加载更多</v-btn>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import Isotope from 'isotope-layout';
import imagesLoaded from 'imagesloaded';

const displayedWallpapers = ref([]);
let iso = null;
const clas = 'Mobile';
const pageSize = 20;
const currentPage = ref(1);
const isLoading = ref(false);
const hasMore = ref(true);

// 获取数据
async function fetchWallpapers(page) {
    try {
        const response = await axios.get(
            `https://api-v2.x-x.work/web/kon/wallpaper?${clas}&number=${pageSize}&page=${page}`
        );
        return response.data;
    } catch (error) {
        console.error('Error fetching wallpapers:', error);
        return [];
    }
}

// 加载更多数据
async function loadMore() {
    if (!hasMore.value || isLoading.value) return;

    isLoading.value = true;
    currentPage.value++;

    try {
        const newData = await fetchWallpapers(currentPage.value);

        if (newData.length > 0) {
            displayedWallpapers.value = [...displayedWallpapers.value, ...newData];
            updateLayout(newData.length);
        }

        // 检查是否还有更多数据
        hasMore.value = newData.length === pageSize;
    } finally {
        isLoading.value = false;
    }
}

// 更新Isotope布局
function updateLayout(newItemsCount) {
    nextTick(() => {
        if (iso) {
            imagesLoaded(document.querySelector('.Wallpaper'), () => {
                const items = document.querySelectorAll('.Wallpaper-item');
                iso.appended(Array.from(items).slice(-newItemsCount));
                iso.layout();
            });
        }
    });
}

// 初始化Isotope
function initIsotope() {
    const grid = document.querySelector('.Wallpaper');
    if (!grid) return;

    iso = new Isotope(grid, {
        itemSelector: '.Wallpaper-item',
        masonry: {
            columnWidth: '.Wallpaper-item',
            gutter: 15
        },
        percentPosition: true,
        transitionDuration: '0.4s'
    });

    imagesLoaded(grid).on('always', () => {
        iso.layout();
        grid.style.opacity = 1;
    });
}

// 图片加载处理
function handleImageLoad() {
    imagesLoaded(document.querySelector('.Wallpaper'), () => {
        iso?.layout();
    });
}

// 初始加载
onMounted(async () => {
    isLoading.value = true;
    try {
        const initialData = await fetchWallpapers(1);
        displayedWallpapers.value = initialData;
        hasMore.value = initialData.length === pageSize;

        await nextTick();
        initIsotope();
    } finally {
        isLoading.value = false;
    }
});

onBeforeUnmount(() => {
    iso?.destroy();
});
</script>