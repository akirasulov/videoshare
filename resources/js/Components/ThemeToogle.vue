<script setup>
import { useDark, useToggle } from "@vueuse/core";
import { MoonIcon, SunIcon } from "@heroicons/vue/24/solid";

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <button type="button" @click.prevent="toggleDark()">
        <MoonIcon
            v-if="!isDark"
            class="size-6"
            :class="{ 'text-gray-800': !isDark }"
        />
        <SunIcon
            v-if="isDark"
            class="size-6"
            :class="{ 'text-white': isDark }"
        />
    </button>
</template>
