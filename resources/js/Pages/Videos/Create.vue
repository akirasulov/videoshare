<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { computed, reactive, ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";

const state = reactive({
    stream: null,
    audioStream: null,
    streamActive: computed(() => state.stream?.active),
});
const player = ref(null);
const shouldCaptureAudio = ref(true);

const captureAudio = () => {
    navigator.mediaDevices
        .getUserMedia({
            video: false,
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true,
            },
        })
        .then((stream) => {
            state.audioStream = stream;
        });
};

const captureWebcam = () => {
    if (shouldCaptureAudio.value === true) {
        captureAudio();
    }
    navigator.mediaDevices
        .getUserMedia({
            video: true,
            audio: false,
        })
        .then((stream) => {
            state.stream = stream;
        });
};

const captureScreen = () => {
    if (shouldCaptureAudio.value === true) {
        captureAudio();
    }
    navigator.mediaDevices
        .getDisplayMedia({
            video: true,
            audio: false,
        })
        .then((stream) => {
            state.stream = stream;
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
        <center class="py-4 dark:text-white">
            {{ state }}
        </center>
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

                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="audio"
                                    v-model:checked="shouldCaptureAudio"
                                />
                                <InputLabel
                                    for="audio"
                                    class="text-sm font-medium"
                                    >Enable audio</InputLabel
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
