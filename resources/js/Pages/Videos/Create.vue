<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { computed, reactive, ref, watch } from "vue";

const state = reactive({
    stream: null,
    streamActive: computed(() => state.stream?.active),
});
const player = ref(null);

const captureWebcam = () => {
    navigator.mediaDevices
        .getUserMedia({
            video: true,
            audio: false,
        })
        .then((stream) => {
            state.stream = stream;
            console.log(stream);
        });
};

const captureScreen = () => {
    navigator.mediaDevices
        .getDisplayMedia({
            video: true,
            audio: false,
        })
        .then((stream) => {
            state.stream = stream;
            console.log(stream);
        });
};

watch(
    () => state.stream,
    (stream) => {
        player.value.srcObject = stream;
    },
);
</script>

<template>
    <Head title="Capture" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Videos
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-show="state.streamActive">
                            <video ref="player" autoplay></video>
                        </div>
                        <div
                            v-if="!state.streamActive"
                            class="flex items-center justify-center space-x-4"
                        >
                            <PrimaryButton @click="captureWebcam"
                                >Capture WebCam</PrimaryButton
                            >
                            <PrimaryButton @click="captureScreen"
                                >Capture Screen</PrimaryButton
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
