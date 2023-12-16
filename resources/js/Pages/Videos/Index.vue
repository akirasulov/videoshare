<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    videos: {
        type: Array,
        required: false,
    },
});
</script>

<template>
    <Head title="Videos" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Videos
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl space-y-3 sm:px-6 lg:px-8">
                <template v-if="videos.length">
                    <Link
                        :href="route('videos.show', video)"
                        class="block overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg"
                        v-for="video in videos"
                        :key="video.id"
                    >
                        <div
                            class="flex items-start space-x-6 p-6 text-gray-900 dark:text-gray-100"
                        >
                            <div
                                class="h-24 w-32 rounded-lg bg-black bg-cover"
                                :style="`background-image: url(${video.still_path})`"
                            ></div>

                            <div>
                                <h2
                                    class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    {{ video.title }}
                                </h2>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ video.description ?? "No description" }}
                                </p>
                                <p
                                    class="mt-6 text-sm text-gray-600 dark:text-gray-200"
                                >
                                    Recorded on {{ video.created_at }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </template>
                <template v-else>
                    <div
                        class="dark: text-center font-medium text-gray-800 dark:text-gray-200"
                    >
                        Nothing here.
                        <Link
                            class="text-indigo-500 underline transition-opacity hover:opacity-80"
                            :href="route('videos.create')"
                            >Ready to capture your first video</Link
                        >
                        ?
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
