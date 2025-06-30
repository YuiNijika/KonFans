<script setup>

useSeoMeta({
    title: '留言',
    description: '留言板',
    keywords: '留言板'
});

const baseURL = '/apiService/message'

// 留言数据
const comments = ref([]);
const loading = ref(false);
const submitting = ref(false);
const pagination = ref({
    page: 1,
    size: 10,
    total: 0
});

// 表单数据
const formData = ref({
    name: '',
    email: '',
    content: '',
    parentId: null
});

// 回复状态
const replyingTo = ref(null);

// 设置回复
const setReply = (comment) => {
    replyingTo.value = comment;
    formData.value.parentId = comment.id;
    formData.value.content = `@${comment.name} `;
    document.getElementById('comment-form')?.scrollIntoView({ behavior: 'smooth' });
};

// 取消回复
const cancelReply = () => {
    replyingTo.value = null;
    formData.value.parentId = null;
    formData.value.content = '';
};

// 提交留言或回复
const submitComment = async () => {
    if (!formData.value.name.trim()) {
        alert('请输入昵称');
        return;
    }

    if (!formData.value.email.trim()) {
        alert('请输入邮箱');
        return;
    }

    if (!formData.value.content.trim()) {
        alert('请输入留言内容');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.value.email)) {
        alert('请输入有效的邮箱地址');
        return;
    }

    submitting.value = true;

    try {
        const response = await fetch(baseURL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: formData.value.name,
                email: formData.value.email,
                content: formData.value.content,
                parent_id: formData.value.parentId
            })
        });

        const data = await response.json();

        if (data.success) {
            alert(formData.value.parentId ? '回复成功' : '留言提交成功');
            formData.value = {
                name: '',
                email: '',
                content: '',
                parentId: null
            };
            replyingTo.value = null;
            await fetchComments();
        } else {
            alert(data.error || (formData.value.parentId ? '回复失败' : '留言提交失败'));
        }
    } catch (error) {
        alert('提交失败: ' + error.message);
    } finally {
        submitting.value = false;
    }
};

// 格式化日期
const formatDate = (dateString) => {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('zh-CN', options);
};

// 分页变化
const handlePageChange = (page) => {
    pagination.value.page = page;
    fetchComments();
};

// 获取留言列表
const fetchComments = async () => {
    loading.value = true;
    try {
        const response = await $fetch(baseURL, {
            method: 'GET',
            params: {
                page: pagination.value.page,
                size: pagination.value.size
            }
        });

        if (response.success) {
            comments.value = response.data;
            pagination.value.total = response.pagination.total;
        } else {
            console.error('获取留言失败:', response.error);
        }
    } catch (error) {
        console.error('获取留言失败:', error.data?.message || error.message);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchComments();
});
</script>

<template>
    <div class="mx-auto px-4 py-8 max-w-4xl">
        <div id="comment-form" class="card bg-base-100 shadow-md mb-8">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">
                    {{ replyingTo ? `回复 ${replyingTo.name}` : '发表留言' }}
                    <button v-if="replyingTo" @click="cancelReply" class="btn btn-sm btn-ghost">
                        取消回复
                    </button>
                </h2>
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">昵称</span>
                        </label>
                        <input v-model="formData.name" type="text" placeholder="请输入您的昵称"
                            class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">邮箱</span>
                        </label>
                        <input v-model="formData.email" type="email" placeholder="请输入您的邮箱"
                            class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">留言内容</legend>
                            <textarea v-model="formData.content" class="textarea h-24 w-full"
                                placeholder="请不要发布违规内容哦~"></textarea>
                        </fieldset>
                    </div>

                    <div class="card-actions justify-end">
                        <button @click="submitComment" :disabled="submitting" class="btn btn-primary">
                            <span v-if="submitting" class="loading loading-spinner"></span>
                            {{ replyingTo ? '提交回复' : '提交留言' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <div v-if="loading" class="text-center py-8">
                    <span class="loading loading-spinner loading-lg"></span>
                    <p class="mt-4">正在加载留言...</p>
                </div>

                <div v-else-if="comments.length === 0" class="text-center py-8">
                    <p>暂无留言，快来发表第一条留言吧！</p>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="comment in comments" :key="comment.id" class="space-y-4">
                        <div class="chat chat-start">
                            <div class="chat-image avatar">
                                <div class="w-10 rounded-full">
                                    <img :src="comment.avatar" :alt="comment.name" />
                                </div>
                            </div>
                            <div class="chat-header">
                                {{ comment.name }}
                                <time class="text-xs opacity-50 ml-2">{{ formatDate(comment.created_at) }}</time>
                            </div>
                            <div class="chat-bubble">{{ comment.content }}</div>
                            <div class="chat-footer opacity-50">
                                <button @click="setReply(comment)" class="link link-hover">回复</button>
                            </div>
                        </div>

                        <div v-if="comment.replies?.length" class="ml-12 pl-2 border-l-2 border-base-300 space-y-3">
                            <div v-for="reply in comment.replies" :key="reply.id" class="chat chat-start">
                                <div class="chat-image avatar">
                                    <div class="w-8 rounded-full">
                                        <img :src="reply.avatar" :alt="reply.name" />
                                    </div>
                                </div>
                                <div class="chat-header">
                                    {{ reply.name }}
                                    <time class="text-xs opacity-50 ml-2">{{ formatDate(reply.created_at) }}</time>
                                </div>
                                <div class="chat-bubble chat-bubble-secondary">{{ reply.content }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 分页控件 -->
                <div class="flex justify-center mt-6">
                    <div class="btn-group">
                        <button 
                            v-for="page in Math.ceil(pagination.total / pagination.size)" 
                            :key="page"
                            @click="handlePageChange(page)"
                            class="btn"
                            :class="{ 'btn-active': page === pagination.page }"
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* 自定义样式调整 */
.chat-bubble {
    max-width: 100%;
    word-break: break-word;
    white-space: pre-wrap;
}

/* 回复区域的边框样式 */
.ml-12 {
    margin-left: 3rem;
}
.pl-2 {
    padding-left: 0.5rem;
}
.border-l-2 {
    border-left-width: 2px;
}

/* 头像图片样式 */
.avatar img {
    object-fit: cover;
}
</style>