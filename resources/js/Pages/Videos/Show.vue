<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Textarea from "@/Components/Textarea.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    video: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.video.title,
    description: props.video.description,
});
</script>
<template>
    <Head :title="video.title"></Head>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto mb-6 max-w-3xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-6 shadow-sm dark:bg-gray-800 sm:rounded-lg"
                >
                    <InputLabel for="share_url" value="Share Link" />
                    <input
                        @click.prevent="$event.target.select()"
                        type="text"
                        :value="video.share_url"
                        readonly
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    />
                </div>
            </div>
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form
                            class="space-y-6"
                            @submit.prevent="
                                form.patch(route('videos.update', video), {
                                    preserveScroll: true,
                                })
                            "
                        >
                            <video
                                ref="videoPreview"
                                :src="`${video.video_path}`"
                                controls
                            ></video>
                            <div>
                                <InputLabel for="title" value="Title" />

                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.title"
                                />
                            </div>
                            <div>
                                <InputLabel
                                    for="description"
                                    value="Description"
                                />

                                <Textarea
                                    id="description"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.title"
                                />
                            </div>
                            <div class="space-x-4">
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Update Video
                                </PrimaryButton>
                            </div>

                            <p v-if="form.recentlySuccessful">Video Updated</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
