<template>

    <v-card style="margin-bottom: 20px;">
        <iframe ref="iframeRef" :src="iframeSrc" @load="adjustIframeHeight" style="width: 100%; border: none;"></iframe>
    </v-card>

    <v-card v-for="comment in comments" :key="comment.coid" class="mx-auto text-white mb-4">
        <v-card-text class="text-h5 py-2">
            "{{ comment.text }}"
    </v-card-text>

        <v-card-actions>
            <v-list-item class="w-100">
                <template v-slot:prepend>
                    <v-avatar color="grey-darken-3" :image="`https://cravatar.cn/avatar/${comment.mailHash}`" />
                </template>

                <v-list-item-title>{{ comment.author }}</v-list-item-title>

                <v-list-item-subtitle>
                    {{ new Date(comment.created * 1000).toLocaleString() }}
                </v-list-item-subtitle>
            </v-list-item>
        </v-card-actions>
    </v-card>

    <div v-if="currentPage < totalPages" class="text-center mt-6">
        <v-btn color="primary" @click="loadMore" :disabled="loading">
            {{ loading ? '加载中...' : '加载更多' }}
        </v-btn>
    </div>

    <div v-else class="text-center mt-6">
        没有更多评论了
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const iframeRef = ref<HTMLIFrameElement | null>(null);
const iframeSrc = 'https://messages.miomoe.cn/?KonFans';

// 动态调整iframe高度
const adjustIframeHeight = () => {
    if (iframeRef.value) {
        const iframeDoc = iframeRef.value.contentDocument || iframeRef.value.contentWindow?.document;
        if (iframeDoc) {
            iframeRef.value.height = iframeDoc.body.scrollHeight + 'px';
        }
    }
};

// 监听窗口大小变化
onMounted(() => {
    window.addEventListener('resize', adjustIframeHeight);
});

onUnmounted(() => {
    window.removeEventListener('resize', adjustIframeHeight);
});


interface Comment {
    coid: string
    author: string
    text: string
    created: number
    mailHash: string
}

interface ResponseData {
    status: string
    message: string
    data: {
        page: number
        pageSize: number
        pages: number
        count: number
        dataSet: Comment[]
    }
}

const comments = ref<Comment[]>([])
const currentPage = ref(1)
const totalPages = ref(0)
const loading = ref(false)

const fetchComments = async (isAppend = false) => {
    try {
        loading.value = true
        const res = await axios.get<ResponseData>(
            `https://messages.miomoe.cn/api/comments?cid=4&page=${currentPage.value}`
        )

        if (res.data.status === 'success') {
            if (isAppend) {
                comments.value.push(...res.data.data.dataSet)
            } else {
                comments.value = res.data.data.dataSet
            }
            totalPages.value = res.data.data.pages
        }
    } catch (error) {
        console.error('获取评论失败:', error)
    } finally {
        loading.value = false
    }
}

const loadMore = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
        fetchComments(true)
    }
}

// 初始化加载第一页
fetchComments();



</script>