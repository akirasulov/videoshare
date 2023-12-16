<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, reactive, ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Textarea from "@/Components/Textarea.vue";
import dayjs from "dayjs";
const form = useForm({
    title: "",
    description: "",
    video: null,
});

const state = reactive({
    stream: null,
    audioStream: null,
    recorder: null,
    blob: null,
    blobUrl: computed(() =>
        state.blob ? URL.createObjectURL(state.blob) : null,
    ),
    streamActive: computed(() => state.stream?.active),
    isRecording: computed(() =>
        state.recorder ? state.recorder.state === "recording" : false,
    ),
});

const player = ref(null);
const videoPreview = ref(null);
const shouldCaptureAudio = ref(true);
const currentDay = computed(() => dayjs().format("YYYY-MM-DD"));

const startRecording = () => {
    let chunks = [];

    const stream = new MediaStream([
        ...state.stream.getTracks(),
        ...(state.audioStream ? state.audioStream.getTracks() : []),
    ]);

    state.recorder = new MediaRecorder(stream);

    state.recorder.ondataavailable = (event) => {
        if (!event.data || event.data.size <= 0) {
            return;
        }

        chunks.push(event.data);
    };

    state.recorder.onstop = () => {
        const blob = new Blob(chunks, {
            type: "video/mp4",
        });
        state.blob = blob;
        chunks = [];
    };

    state.recorder.start(300);
};

const stopRecording = () => {
    state.recorder.stream.getTracks().forEach((track) => track.stop());
    state.stream = null;
    state.recorder = null;
};

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

watch(
    () => state.blobUrl,
    (url) => {
        videoPreview.value.src = url;
    },
);
watch(
    () => state.blob,
    (blob) => {
        form.video = new File([blob], "video.mp4", {
            type: "video/mp4",
        });

        form.title = currentDay.value;
        form.description = `A video captured on ${currentDay.value}`;
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
                        <form
                            v-show="state.blobUrl"
                            @submit.prevent="
                                form.post(route('videos.store'), {
                                    preserveScroll: true,
                                })
                            "
                            class="space-y-6"
                        >
                            <video ref="videoPreview" controls></video>
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
                                    Save Video
                                </PrimaryButton>
                                <PrimaryButton
                                    @click.prevent="
                                        form.reset('title', 'description')
                                    "
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Reset
                                </PrimaryButton>
                                <PrimaryButton
                                    @click="form.cancel()"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Cancel
                                </PrimaryButton>
                            </div>
                        </form>
                        <div v-show="!state.blobUrl">
                            <div v-show="state.streamActive" class="space-y-6">
                                <video ref="player" autoplay></video>
                                <div
                                    class="flex items-center justify-center space-x-4"
                                >
                                    <PrimaryButton
                                        v-if="!state.isRecording"
                                        @click="startRecording"
                                        >Start Recording</PrimaryButton
                                    >
                                    <DangerButton
                                        v-if="state.isRecording"
                                        @click="stopRecording"
                                        >Stop Recording</DangerButton
                                    >
                                </div>
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
        </div>
    </AuthenticatedLayout>
</template>
