<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    video: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="video.title"></Head>
    <GuestLayout class="space-y-6 sm:max-w-3xl">
        <video
            ref="videoPreview"
            :src="`${video.video_path}`"
            controls
            class="w-full"
        ></video>
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ video.title }}
            </h2>
            <p
                v-if="video.description"
                class="text-gray-900 dark:text-gray-100"
            >
                {{ video.description }}
            </p>
        </div>
    </GuestLayout>
</template>
