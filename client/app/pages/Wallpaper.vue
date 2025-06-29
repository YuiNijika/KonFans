<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'

useSeoMeta({
    title: '轻音少女壁纸',
    titleTemplate: '%s - KonFans(轻音小窝)',
    description: '轻音少女壁纸',
    keywords: '轻音部, 壁纸, 表情包'
})

const props = defineProps({
    pageSize: { type: Number, default: 20 },
    page: { type: Number, default: 1 }
})

const categories = [
    "Meme",
    "All"
]

const selectedCategory = ref("All")
const pictures = ref([])
const currentPage = ref(props.page)
const isLoading = ref(false)
const hasMore = ref(true)
const showSkeleton = ref(false) // 新增控制骨架屏显示的变量

const baseUrl = computed(() =>
    `https://konfans-api.x-x.work/?${selectedCategory.value}&size=${props.pageSize}&page=`
)

// 获取图片数据
const fetchPictures = async (page) => {
    try {
        const res = await fetch(baseUrl.value + page)
        const json = await res.json()
        return json.data || json
    } catch (error) {
        console.error('Error fetching pictures:', error)
        return []
    }
}

// 图片加载处理
const handleImageLoad = (event) => {
    event.target.style.opacity = 1
    resizeAllGridItems()
}

// 加载更多数据
const loadMore = async () => {
    if (!hasMore.value || isLoading.value) return

    isLoading.value = true
    currentPage.value++

    try {
        const newData = await fetchPictures(currentPage.value)
        if (newData.length) {
            pictures.value = [...pictures.value, ...newData]
            await nextTick()
            resizeAllGridItems()
        }
        hasMore.value = newData.length === props.pageSize
    } finally {
        isLoading.value = false
    }
}

// 切换分类 - 添加移除动画和骨架屏显示
const changeCategory = async (category) => {
    // 先添加移除动画
    const gridItems = document.querySelectorAll('.masonry-grid-item')
    gridItems.forEach(item => {
        item.style.transition = 'all 0.3s ease'
        item.style.opacity = '0'
        item.style.transform = 'translateY(20px)'
    })

    // 等待动画完成
    await new Promise(resolve => setTimeout(resolve, 300))

    // 显示骨架屏
    showSkeleton.value = true
    selectedCategory.value = category
    currentPage.value = 1
    isLoading.value = true
    pictures.value = [] // 清空当前图片

    try {
        const newData = await fetchPictures(currentPage.value)
        pictures.value = newData
        await nextTick()
        resizeAllGridItems()
    } finally {
        isLoading.value = false
        showSkeleton.value = false // 隐藏骨架屏
    }
}

// 计算网格项高度
const resizeGridItem = (item) => {
    const grid = document.querySelector('.masonry-grid')
    if (!grid) return

    const rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap'))
    const rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'))
    const content = item.querySelector('.card')
    if (!content) return

    const rowSpan = Math.ceil((content.getBoundingClientRect().height + rowGap) / (rowHeight + rowGap))
    item.style.gridRowEnd = 'span ' + rowSpan
}

const resizeAllGridItems = () => {
    const allItems = document.querySelectorAll('.masonry-grid-item')
    allItems.forEach(item => {
        item.style.opacity = '1'
        item.style.transform = 'translateY(0)'
        resizeGridItem(item)
    })
}

onMounted(async () => {
    isLoading.value = true
    showSkeleton.value = true
    try {
        pictures.value = await fetchPictures(currentPage.value)
        await nextTick()
        resizeAllGridItems()
        window.addEventListener('resize', resizeAllGridItems)
    } finally {
        isLoading.value = false
        showSkeleton.value = false
    }
})

onUnmounted(() => {
    window.removeEventListener('resize', resizeAllGridItems)
})
</script>

<template>

    <div role="alert" class="alert alert-info alert-soft sm:alert-horizontal mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="font-bold">欢迎来到KonFans</h3>
            <div class="text-xs">作为轻音少女粉丝的你可以点击右侧按钮加入轻音部! QQ群: 574888080</div>
        </div>
        <NuxtLink to="/Member" class="btn btn-sm">入部</NuxtLink>
    </div>

    <div class="category-selector mb-4">
        <div class="tabs tabs-boxed">
            <a v-for="category in categories" :key="category" class="tab"
                :class="{ 'tab-active': selectedCategory === category }" @click="changeCategory(category)">
                {{ category }}
            </a>
        </div>
    </div>

    <div v-if="showSkeleton" class="masonry-grid">
        <div v-for="n in pageSize" :key="'skeleton-' + n" class="masonry-grid-item">
            <div class="card w-full bg-base-100 shadow-xl">
                <div class="skeleton h-48 w-full"></div>
                <div class="card-body p-4 space-y-4">
                    <div class="skeleton h-6 w-3/4"></div>
                    <div class="skeleton h-4 w-1/2"></div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="mx-auto py-4">
        <div class="masonry-grid">
            <div v-for="(item, index) in pictures" :key="index" class="masonry-grid-item transition-all duration-300">
                <div class="card w-full bg-base-100 shadow-xl">
                    <figure class="relative">
                        <img :src="item.Url" :alt="item.File" loading="lazy"
                            class="w-full h-auto opacity-0 transition-opacity duration-300" @load="handleImageLoad">
                    </figure>
                    <div class="card-body p-4 tooltip" :data-tip="item.File">
                        <p>Size: {{ (item.Size / 1024).toFixed(2) }} KB</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="hasMore" class="text-center mt-4">
            <button @click="loadMore" class="btn btn-primary" :disabled="isLoading">
                <span v-if="isLoading" class="loading loading-spinner"></span>
                {{ isLoading ? '加载中...' : '加载更多' }}
            </button>
        </div>
    </div>
</template>

<style scoped>
.masonry-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    grid-auto-rows: 10px;
    gap: 16px;
}

.masonry-grid-item {
    break-inside: avoid;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* 分类选择样式 */
.category-selector {
    overflow-x: auto;
    padding: 0 16px;
}

.tabs {
    display: inline-flex;
    white-space: nowrap;
}

/* 文件名省略 */
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

@media (max-width: 1024px) {
    .masonry-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .masonry-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}

@media (max-width: 480px) {
    .masonry-grid {
        grid-template-columns: 1fr;
    }
}
</style>