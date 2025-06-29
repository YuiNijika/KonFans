<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const iframeRef = ref<HTMLIFrameElement | null>(null);
const iframeSrc = 'https://messages.miomoe.cn/?KonFans';

interface Comment {
    coid: string;
    author: string;
    text: string;
    created: number;
    mailHash: string;
}

interface ResponseData {
    status: string;
    message: string;
    data: {
        page: number;
        pageSize: number;
        pages: number;
        count: number;
        dataSet: Comment[];
    };
}

const comments = ref<Comment[]>([]);
const currentPage = ref(1);
const totalPages = ref(0);
const loading = ref(false);

const fetchComments = async (isAppend = false) => {
    try {
        loading.value = true;
        const res = await axios.get<ResponseData>(
            `https://messages.miomoe.cn/api/comments?cid=4&page=${currentPage.value}`
        );

        if (res.data.status === 'success') {
            if (isAppend) {
                comments.value.push(...res.data.data.dataSet);
            } else {
                comments.value = res.data.data.dataSet;
            }
            totalPages.value = res.data.data.pages;
        }
    } catch (error) {
        console.error('获取评论失败:', error);
    } finally {
        loading.value = false;
    }
};

const loadMore = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchComments(true);
    }
};

// 初始化加载第一页
fetchComments();
</script>

<template>
    <a-card size="small" :bordered="false" style="margin-bottom: 20px;">
        <iframe ref="iframeRef" :src="iframeSrc" height="280px" style="width: 100%; border: none;"></iframe>
    </a-card>

    <a-card size="small" :bordered="false" v-for="comment in comments" :key="comment.coid" style="margin-bottom: 10px;">
    
    <a-comment>
        <template #avatar>
            <a-avatar :src="`https://cravatar.cn/avatar/${comment.mailHash}`" />
        </template>
        <template #author>
            {{ comment.author }}
        </template>
        <template #datetime>
            {{ new Date(comment.created * 1000).toLocaleString() }}
        </template>
        <template #content>
            <p>{{ comment.text }}</p>
        </template>
    </a-comment>
</a-card>

    <div v-if="currentPage < totalPages" style="text-align:center;margin-top: 20px;">
        <a-button type="primary" @click="loadMore" :loading="loading">
            {{ loading ? '加载中...' : '加载更多' }}
        </a-button>
    </div>

    <a-card size="small" :bordered="false" v-else-if="loading">
        <a-skeleton active :paragraph="{ rows: 4 }" />
    </a-card>

    <div v-else styule="text-align:center;">
        没有更多评论了
    </div>
</template>

<style scoped>

</style>