<script setup>
const route = useRoute()
const memberId = route.params.id
const baseUrl = `/apiService/member?id=${memberId}`

const { data: member, pending: loadingMember, error } = await useAsyncData(
    `member-${memberId}`,
    async () => {
        try {
            // 确保传递ID参数
            const response = await $fetch(`${baseUrl}`, {
                method: 'GET',
                retry: 3,
                retryDelay: 1000
            })

            if (!response?.success) {
                throw new Error(response?.error?.message || '获取成员信息失败')
            }

            // 格式化日期
            const formattedData = {
                ...response.data,
                created_at: formatDate(response.data.created_at)
            }

            return formattedData
        } catch (err) {
            throw new Error(err.data?.message || err.message || '获取成员信息时发生错误')
        }
    }
)

// 日期格式化函数
function formatDate(dateString) {
    if (!dateString) return '未知时间'
    try {
        const date = new Date(dateString)
        return date.toLocaleString('zh-CN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).replace(/\//g, '-')
    } catch {
        return dateString
    }
}

// SEO设置
useSeoMeta({
    title: () => `${member.value?.name || '未知用户'}的个人空间`,
    titleTemplate: '%s - KonFans(轻音小窝)',
    description: () => member.value?.bio || '轻音部成员个人主页',
    keywords: () => [member.value?.name, '轻音部', '成员', '个人主页'].filter(Boolean).join(', '),
    ogImage: () => member.value?.avatar
})

// 设置页面主题色
useHead({
    bodyAttrs: {
        class: 'min-h-screen bg-base-200'
    }
})
</script>

<template>
    <div class="min-h-screen">
        <div class="mx-auto p-4 max-w-4xl">

            <div v-if="loadingMember" class="flex justify-center items-center py-12">
                <div class="flex w-full md:w-120 flex-col gap-6">
                    <div class="flex items-center gap-4">
                        <div class="skeleton h-16 w-16 shrink-0 rounded-full"></div>
                        <div class="flex flex-col gap-3 flex-1">
                            <div class="skeleton h-5 w-1/3"></div>
                            <div class="skeleton h-4 w-1/2"></div>
                        </div>
                    </div>
                    <div class="skeleton h-32 w-full"></div>
                    <div class="flex gap-4">
                        <div class="skeleton h-10 w-24"></div>
                        <div class="skeleton h-10 w-24"></div>
                    </div>
                </div>
            </div>


            <div v-else-if="error" class="alert alert-error shadow-lg mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-bold">加载失败!</h3>
                    <div class="text-xs">{{ error.message }}</div>
                </div>
                <button class="btn btn-sm" @click="refreshNuxtData()">重试</button>
            </div>


            <template v-else-if="member">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex flex-col md:flex-row gap-6">

                            <div class="flex flex-col items-center gap-2">
                                <div class="avatar">
                                    <div class="w-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                        <img :src="member.avatar" :alt="member.name" class="object-cover" width="128"
                                            height="128" loading="lazy" />
                                    </div>
                                </div>
                                <p class="text-sm opacity-70 tooltip" :data-tip="`注册时间: ${member.created_at}`">
                                    加入于 {{ member.created_at.split(' ')[0] }}
                                </p>
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                    <h1 class="card-title text-2xl md:text-3xl">{{ member.name }}</h1>
                                    <span class="badge gap-2" :class="{
                                        'badge-info': member.gender === 'secret',
                                        'badge-primary': member.gender === 'male',
                                        'badge-secondary': member.gender === 'female'
                                    }">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path v-if="member.gender === 'male'" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M9.5 11c1.933 0 3.5-1.567 3.5-3.5S11.433 4 9.5 4 6 5.567 6 7.5 7.567 11 9.5 11z" />
                                            <path v-if="member.gender === 'female'" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M9.5 11c1.933 0 3.5-1.567 3.5-3.5S11.433 4 9.5 4 6 5.567 6 7.5 7.567 11 9.5 11zm0 1c-2.209 0-4 1.343-4 3v1h8v-1c0-1.657-1.791-3-4-3z" />
                                            <path v-if="member.gender === 'secret'" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        {{
                                            member.gender === 'male' ? '男' :
                                                member.gender === 'female' ? '女' : '保密'
                                        }}
                                    </span>
                                </div>

                                <div class="divider my-2"></div>

                                <section class="space-y-4">
                                    <div>
                                        <h3 class="font-bold text-lg">个人简介</h3>
                                        <p class="mt-1 text-gray-700 dark:text-gray-300">
                                            {{ member.bio || '还没有留下任何介绍...' }}
                                        </p>
                                    </div>

                                </section>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">个人主页</h2>
                            <div class="space-y-3">
                                <div v-if="member.url" class="mt-4">
                                    <a :href="member.url" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center link link-primary hover:link-accent">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        <span class="truncate max-w-xs">{{ member.url }}</span>
                                    </a>
                                </div>

                                <div v-else class="alert alert-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>暂未提交个人主页链接</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<style scoped>
.text-gray-700 {
    word-break: break-all;
    overflow-wrap: break-word;
    max-width: 100%;
}

.avatar {
    transition: transform 0.3s ease;
}

.avatar:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
}
</style>