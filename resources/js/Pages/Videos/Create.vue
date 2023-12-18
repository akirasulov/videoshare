<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, reactive, ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Textarea from "@/Components/Textarea.vue";
import dayjs from "dayjs";
import { createUpload } from "@mux/upchunk";
const form = useForm({
    title: "",
    description: "",
    video: null,
});

const initialState = reactive({
    file: null,
    uploader: null,
    uploading: false,
    progress: 0,
    error: null,
});

const state = reactive({
    ...initialState,
    stream: null,
    audioStream: null,
    recorder: null,
    blob: null,
    formattedProgress: computed(() => Math.round(state.progress)),
    blobUrl: computed(() =>
        state.blob ? URL.createObjectURL(state.blob) : null,
    ),
    streamActive: computed(() => state.stream?.active),
    isRecording: computed(() =>
        state.recorder ? state.recorder.state === "recording" : false,
    ),

    reset: () => {
        Object.assign(state, initialState);
    },
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
        console.log("url", url);
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

const handleFileUpload = (event) => {
    state.file = event.target.files[0];

    videoPreview.value.src = URL.createObjectURL(state.file);

    state.uploader = createUpload({
        endpoint: route("files.store"),
        headers: {
            "X-CSRF-TOKEN": usePage().props.csrf_token,
        },
        method: "post",
        file: state.file,
        chunkSize: 10 * 1024, // 10mb
    });

    state.uploader.on("attempt", () => {
        state.error = null;
        state.uploading = true;
    });

    state.uploader.on("progress", (progress) => {
        state.progress = progress.detail;
    });

    state.uploader.on("success", (progress) => {
        state.reset();
        state.uploading = false;
        // router.reload({
        //     only: ["files"],
        //     preserveScroll: true,
        // });
    });

    state.uploader.on("error", (error) => {
        state.error = error.detail.message;
    });
};

const cancel = () => {
    state.uploader.abort();
    state.reset();
};
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

                            <section
                                v-if="!state.streamActive"
                                class="space-y-6"
                            >
                                <div
                                    class="flex w-full items-center justify-center"
                                >
                                    <label
                                        for="dropzone-file"
                                        class="dark:hover:bg-bray-800 flex h-64 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pb-6 pt-5"
                                        >
                                            <svg
                                                class="mb-4 h-8 w-8 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 20 16"
                                            >
                                                <path
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                                />
                                            </svg>

                                            <span class="semibold"
                                                >Click to upload</span
                                            >
                                            or drag and drop
                                        </div>
                                        <input
                                            @change="handleFileUpload"
                                            id="dropzone-file"
                                            type="file"
                                            class="hidden"
                                            accept="video/mp4, video/webm, video/x-mkv"
                                        />
                                    </label>
                                </div>
                                <div v-if="state.uploading">
                                    <div class="mb-1 flex justify-center">
                                        <span
                                            class="text-sm font-medium text-blue-700 dark:text-white"
                                            >{{
                                                state.formattedProgress
                                            }}%</span
                                        >
                                    </div>
                                    <div
                                        style="width: 100%"
                                        class="h-2.5 rounded-full bg-gray-200 dark:bg-gray-700"
                                    >
                                        <div
                                            class="h-2.5 rounded-full bg-blue-600 transition-all duration-200"
                                            v-bind:style="{
                                                width: state.progress + '%',
                                            }"
                                        ></div>
                                    </div>

                                    <div
                                        class="mt-2 flex items-center space-x-3"
                                    >
                                        <PrimaryButton
                                            v-if="!state.uploader.paused"
                                            class="!px-2 !py-1"
                                            @click="state.uploader.pause()"
                                        >
                                            Pause</PrimaryButton
                                        >
                                        <PrimaryButton
                                            v-if="state.uploader.paused"
                                            class="!px-2 !py-1"
                                            @click="state.uploader.resume()"
                                        >
                                            Resume</PrimaryButton
                                        >
                                        <PrimaryButton
                                            class="!px-2 !py-1"
                                            @click="cancel"
                                            >Cancel</PrimaryButton
                                        >
                                    </div>
                                    <InputError
                                        class="mt-2"
                                        :message="state.error"
                                    />
                                </div>

                                <div
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
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
