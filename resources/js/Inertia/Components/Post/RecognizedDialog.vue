<script setup>
import { usePage } from '@inertiajs/vue3';

const props = defineProps({ toggle: Function, users: Array });
const page = usePage();
</script>

<template>
    <Dialog modal :style="{ width: '25rem' }">
        <template #header>
            <div class="flex flex-row items-center">
                <h2>🎉 Recognized Employee ✨🎉</h2>
            </div>
        </template>
        <div class="flex w-full">
            <div class="flex flex-col gap-4">
                <div v-for="_user in props.users" :key="_user.id" class="flex items-center">
                    <div class="flex flex-row justify-center gap-4">
                        <Avatar :image="_user?.avatar ?? $ninshiki.uiAvatar(_user?.name)" :alt="_user?.name" shape="circle" />
                        <div class="flex flex-col">
                            <div class="flex text-base">
                                <div v-if="_user?.id === page.props.auth.user.id">You</div>
                                <div v-else>
                                    {{ _user?.name }}
                                </div>
                            </div>
                            <div class="flex text-xs italic text-gray-400">
                                {{ _user.email }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>

<style scoped></style>
