<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import axios from 'axios'
import Isotope from 'isotope-layout'
import imagesLoaded from 'imagesloaded'
import VueEasyLightbox from 'vue-easy-lightbox'

const props = defineProps({
    clas: {
        type: String,
        default: 'All'
    },
    pageSize: {
        type: Number,
        default: 20
    }
})

// 数据状态
const displayedWallpapers = ref([])
const currentPage = ref(1)
const isLoading = ref(false)
const hasMore = ref(true)
const gridElement = ref(null)
let iso = null

// 灯箱状态
const lightboxVisible = ref(false)
const lightboxIndex = ref(0)
const lightboxImages = ref([])

// 获取壁纸数据
async function fetchWallpapers(page) {
    try {
        const response = await axios.get(
            `https://api-v2.x-x.work/web/kon/wallpaper?${props.clas}&number=${props.pageSize}&page=${page}`
        )
        return response.data.data
    } catch (error) {
        console.error('Error fetching wallpapers:', error)
        return []
    }
}

// 加载更多数据
async function loadMore() {
    if (!hasMore.value || isLoading.value) return

    isLoading.value = true
    currentPage.value++

    try {
        const newData = await fetchWallpapers(currentPage.value)

        if (newData.length > 0) {
            displayedWallpapers.value = [...displayedWallpapers.value, ...newData]
            await nextTick()
            updateLayout(newData.length)
            updateLightboxImages()
        }

        hasMore.value = newData.length === props.pageSize
    } finally {
        isLoading.value = false
    }
}

// 更新灯箱图片数据
function updateLightboxImages() {
    lightboxImages.value = displayedWallpapers.value.map(w => ({
        src: w.HDUrl || w.Url
        // 已移除 title 显示
    }))
}

// 显示灯箱
function showLightbox(index) {
    lightboxIndex.value = index
    lightboxVisible.value = true
}

// 更新Isotope布局
function updateLayout(newItemsCount) {
    if (!iso) return

    imagesLoaded(gridElement.value, () => {
        const items = gridElement.value.querySelectorAll('.Wallpaper-item')
        iso.appended(Array.from(items).slice(-newItemsCount))
        iso.layout()
    })
}

// 图片加载完成处理
function handleImageLoad() {
    nextTick(() => {
        if (iso) {
            imagesLoaded(gridElement.value, () => {
                iso.layout()
            })
        }
    })
}

// 初始化Isotope
function initIsotope() {
    if (!gridElement.value) return

    iso = new Isotope(gridElement.value, {
        itemSelector: '.Wallpaper-item',
        masonry: {
            columnWidth: '.Wallpaper-sizer',
            gutter: 15
        },
        percentPosition: true,
        transitionDuration: '0.4s'
    })

    imagesLoaded(gridElement.value).on('always', () => {
        iso.layout()
        gridElement.value.style.opacity = 1
    })
}

// 洗牌功能
function shuffleItems() {
    if (!iso) return

    // 随机排序数组
    const shuffled = [...displayedWallpapers.value]
        .map(value => ({ value, sort: Math.random() }))
        .sort((a, b) => a.sort - b.sort)
        .map(({ value }) => value)

    displayedWallpapers.value = shuffled

    nextTick(() => {
        iso.layout()
    })
}

// 初始加载
onMounted(async () => {
    isLoading.value = true
    try {
        const initialData = await fetchWallpapers(1)
        displayedWallpapers.value = initialData
        hasMore.value = initialData.length === props.pageSize
        updateLightboxImages()

        await nextTick()
        initIsotope()
    } finally {
        isLoading.value = false
    }
})

// 数据变化时更新灯箱图片
watch(displayedWallpapers, () => {
    updateLightboxImages()
})

onBeforeUnmount(() => {
    iso?.destroy()
})
</script>

<template>
    <a-card size="small" class="card" :bordered="false">
        <div ref="gridElement" class="Wallpaper" style="opacity: 0;">
            <!-- 隐藏的尺寸参考元素 -->
            <div class="Wallpaper-sizer"></div>

            <div v-for="(wallpaper, index) in displayedWallpapers" :key="`wallpaper-${wallpaper.id || index}`"
                class="Wallpaper-item">
                <img v-lazy="wallpaper.Url" :alt="wallpaper.File" @load="handleImageLoad" class="wallpaper-image"
                    @click="showLightbox(index)" />
            </div>
        </div>

        <div v-if="isLoading" style="margin-top: 20px;">
            <a-skeleton active :paragraph="{ rows: 4 }" />
        </div>
        <div style="text-align:center;margin: 20px 0 10px;">
            <a-space>
                <a-button v-if="hasMore" type="primary" @click="loadMore" :loading="isLoading">加载更多</a-button>
                <a-button @click="shuffleItems" type="primary" ghost>
                    <template #icon><icon-refresh /></template>
                    随机洗牌
                </a-button>
            </a-space>
        </div>

        <!-- vue-easy-lightbox 组件 -->
        <vue-easy-lightbox :visible="lightboxVisible" :imgs="lightboxImages" :index="lightboxIndex"
            @hide="lightboxVisible = false" :move-disabled="false" :loop="true" />
    </a-card>
</template>

<style scoped>
.card {
    background-color: rgb(255 255 255 / 50%);
}

.Wallpaper {
    position: relative;
    transition: opacity 0.3s ease;
    margin: 0 -8px;
}

.Wallpaper-sizer {
    position: absolute;
    width: calc(25% - 26px);
    visibility: hidden;
}

.Wallpaper-item {
    margin-bottom: 16px;
    padding: 0 8px;
    width: calc(25% - 16px);
    box-sizing: border-box;
    transition: transform 0.2s ease;
}

.Wallpaper-item:hover {
    transform: translateY(-4px);
}

.wallpaper-image {
    width: 100%;
    height: auto;
    border-radius: 8px;
    cursor: zoom-in;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    aspect-ratio: 16/9;
    object-fit: cover;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background-color: #f5f5f5;
}

.wallpaper-image:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@media (max-width: 1200px) {
    .Wallpaper-sizer,
    .Wallpaper-item {
        width: calc(33.333% - 16px);
    }
}

@media (max-width: 768px) {
    .Wallpaper-sizer,
    .Wallpaper-item {
        width: calc(50% - 16px);
    }
}

@media (max-width: 480px) {
    .Wallpaper-sizer,
    .Wallpaper-item {
        width: calc(100% - 16px);
    }
}
</style>