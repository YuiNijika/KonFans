<script setup>
import md5 from 'md5'

const route = useRoute()
const memberId = route.params.id
const baseUrl = '/apiService/member'

// 生成头像url
const getGravatar = (email, size = 80) => {
    if (!email) return `https://www.cravatar.cn/avatar/?s=${size}&d=retro`
    const trimmedEmail = email.trim().toLowerCase()
    const hash = md5(trimmedEmail)
    return `https://www.cravatar.cn/avatar/${hash}?s=${size}&d=retro`
}

const { data: member, pending: loadingMember, error } = await useAsyncData(
    'member',
    async () => {
        try {
            const response = await $fetch(baseUrl, {
                params: { id: memberId }
            })
            if (response.success) {
                return response.data
            } else {
                throw new Error(response?.error || '获取成员信息失败')
            }
        } catch (err) {
            throw new Error(err.data?.message || err.message)
        }
    }
)

// SEO设置
useSeoMeta({
    title: () => member.value?.name + '的个人空间' || 'Null',
    description: () => member.value?.bio || '轻音部成员个人主页',
    keywords: () => member.value?.name || '轻音部, 成员, 个人主页'
})
</script>

<template>
    <div class="mx-auto p-4 max-w-4xl">
        <div v-if="!member && !error" class="flex justify-center items-center">
            <div class="flex w-120 flex-col gap-4">
                <div class="flex items-center gap-4">
                    <div class="skeleton h-16 w-16 shrink-0 rounded-full"></div>
                    <div class="flex flex-col gap-4">
                        <div class="skeleton h-4 w-20"></div>
                        <div class="skeleton h-4 w-28"></div>
                    </div>
                </div>
                <div class="skeleton h-32 w-full"></div>
            </div>
        </div>

        <div v-if="error" class="alert alert-error mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>加载失败: {{ error }}</span>
        </div>

        <template v-if="member">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex flex-col items-center">
                            <div class="avatar">
                                <div class="w-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                    <img :src="getGravatar(member.email, 160)" :alt="member.name" />
                                </div>
                            </div>
                            <p class="mt-2 text-sm opacity-70 tooltip tooltip-bottom" :data-tip="member.created_at">
                                加入于 {{ member.created_at.split(' ')[0] }}
                            </p>
                        </div>

                        <div class="flex-1">
                            <h2 class="card-title text-2xl">{{ member.name }}</h2>
                            <div class="flex items-center mt-1">
                                <span class="badge gap-2" :class="{
                                    'badge-info': member.gender === 'secret',
                                    'badge-primary': member.gender === 'male',
                                    'badge-secondary': member.gender === 'female'
                                }">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <!-- 男性 -->
                                        <path v-if="member.gender === 'male'" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M9.5 11c1.933 0 3.5-1.567 3.5-3.5S11.433 4 9.5 4 6 5.567 6 7.5 7.567 11 9.5 11z" />
                                        <!-- 女 -->
                                        <path v-if="member.gender === 'female'" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M9.5 11c1.933 0 3.5-1.567 3.5-3.5S11.433 4 9.5 4 6 5.567 6 7.5 7.567 11 9.5 11zm0 1c-2.209 0-4 1.343-4 3v1h8v-1c0-1.657-1.791-3-4-3z" />
                                        <!-- 保密 -->
                                        <path v-if="member.gender === 'secret'" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{
                                        member.gender === 'male' ? '男' :
                                            member.gender === 'female' ? '女' : '保密'
                                    }}
                                </span>
                            </div>
                            <div class="divider"></div>
                            <h3 class="font-bold mt-2">个人简介</h3>
                            <p class="mt-1">{{ member.bio || '667这个入很神秘...' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl mt-6">
                <div class="card-body">
                    <h2 class="card-title">联系信息</h2>
                    <div class="space-y-3 mt-2">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 opacity-70" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a :href="`mailto:${member.email}`" class="link link-hover">{{ member.email }}</a>
                        </div>

                        <div v-if="member.url" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 opacity-70" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <a :href="member.url" target="_blank" class="link link-hover">{{ member.url }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<style scoped>
.container {
    padding-bottom: 4rem;
}
</style>