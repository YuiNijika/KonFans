<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import Isotope from 'isotope-layout';
import imagesLoaded from 'imagesloaded';

import { Image as AImage, Spin as ASpin, Pagination as APagination, Skeleton as ASkeleton, Card as ACard } from 'ant-design-vue';

const props = defineProps({
    clas: { type: String, default: 'All' },
    pageSize: { type: Number, default: 20 }
});

// 响应式状态
const displayedWallpapers = ref([]);
const meta = ref({ current_page: 1, per_page: props.pageSize, total_items: 0, total_pages: 0 });
let iso = null;
const isLoading = ref(false);
const gridElement = ref(null);
let layoutRAF = null;

// 使用 requestAnimationFrame 节流
function safeLayout() {
    if (layoutRAF) return;
    layoutRAF = requestAnimationFrame(() => {
        iso && iso.layout();
        layoutRAF = null;
    });
}

// 获取数据
async function fetchWallpapers(page, perPage) {
    isLoading.value = true;
    try {
        const response = await axios.get(
            `https://api-v2.x-x.work/web/kon/wallpaper?${props.clas}&number=${perPage}&page=${page}`
        );
        const { data, meta: m } = response.data;
        return { data, meta: m };
    } catch (error) {
        console.error('Error fetching wallpapers:', error);
        return { data: [], meta: { current_page: page, per_page: perPage, total_items: 0, total_pages: 0 } };
    } finally {
        isLoading.value = false;
    }
}

// 图片加载时布局
function onImageLoad() {
    safeLayout();
}

// 更新布局
function updateLayout(newItemsCount) {
    if (!iso) return;
    const items = gridElement.value.querySelectorAll('.Wallpaper-item');
    const newItems = Array.from(items).slice(-newItemsCount);
    iso.appended(newItems);
    safeLayout();
    imagesLoaded(newItems, () => {
        iso.shuffle();
        safeLayout();
    });
}

// 初始化 Isotope
function initIsotope() {
    if (!gridElement.value) return;
    iso = new Isotope(gridElement.value, {
        itemSelector: '.Wallpaper-item',
        masonry: { columnWidth: '.Wallpaper-sizer', gutter: 15 },
        percentPosition: true,
        transitionDuration: '0.4s'
    });
    imagesLoaded(gridElement.value, () => {
        iso.shuffle();
        safeLayout();
        gridElement.value.style.opacity = 1;
    });
}

// 加载指定页
async function loadPage(page, perPage) {
    const { data, meta: m } = await fetchWallpapers(page, perPage);
    displayedWallpapers.value = data;
    meta.value = m;
    await nextTick();
    // 重置并重新初始化布局
    iso?.destroy();
    initIsotope();
}

// 分页变更
function handlePageChange(page, pageSize) {
    loadPage(page, pageSize);
}

function handlePageSizeChange(page, pageSize) {
    loadPage(1, pageSize);
}

onMounted(() => {
    loadPage(meta.value.current_page, meta.value.per_page);
});

onBeforeUnmount(() => {
    iso?.destroy();
});
</script>

<template>
    <a-card size="small" class="card" :bordered="false">
        <div ref="gridElement" class="Wallpaper" style="opacity: 0;">
            <div class="Wallpaper-sizer"></div>
            <a-image-preview-group>
                <div v-for="(wallpaper, index) in displayedWallpapers" :key="`wallpaper-${index}`"
                    class="Wallpaper-item">
                    <a-image :src="wallpaper.Url" lazy @load="onImageLoad">
                        <template #placeholder>
                            <div class="image-placeholder">
                                <a-spin />
                            </div>
                        </template>
                    </a-image>
                </div>
            </a-image-preview-group>
        </div>

        <div class="pagination-container">
            <a-pagination :current="meta.current_page" :page-size="meta.per_page" :total="meta.total_items"
                :page-size-options="['10', '20', '50', '100']" show-size-changer @change="handlePageChange"
                @showSizeChange="handlePageSizeChange" />
        </div>
    </a-card>
</template>

<style scoped>
.Wallpaper {
    position: relative;
    transition: opacity 0.3s ease;
}

.Wallpaper-sizer {
    position: absolute;
    width: calc(25% - 15px);
    visibility: hidden;
}

.Wallpaper-item {
    margin-bottom: 15px;
    width: calc(25% - 15px);
}

.image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 150px;
    background-color: #f5f5f5;
}

.pagination-container {
    text-align: center;
    margin: 16px 0;
}

@media (max-width: 1200px) {

    .Wallpaper-sizer,
    .Wallpaper-item {
        width: calc(33.333% - 15px);
    }
}

@media (max-width: 768px) {

    .Wallpaper-sizer,
    .Wallpaper-item {
        width: calc(50% - 15px);
    }
}

@media (max-width: 480px) {

    .Wallpaper-sizer,
    .Wallpaper-item {
        width: 100%;
    }
}
</style>
