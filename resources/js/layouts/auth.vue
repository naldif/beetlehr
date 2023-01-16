<script setup>
import VNotification from "@/components/VNotification/Index.vue";
</script>

<template>
    <VNotification></VNotification>
    <main class="bg-white">
        <div class="relative flex">
            <!-- Content -->
            <div class="w-full md:w-3/4 flex justify-center md:justify-end">
                <slot />
            </div>
            <!-- Image -->
            <div class="hidden md:block absolute top-0 bottom-0 left-0 md:w-1/4" aria-hidden="true">
                <div class="slider">
                    <div class="feature"></div>
                    <div class="overlay"></div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
.slider {
    background-repeat: no-repeat;
    background-size: cover;
    width: 100%;
    height: 100%;
    transition: all 0.1s ease-in-out;
    animation: slide 30s infinite;
}

.overlay {
    color: #fff;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    transition: all 0.1s ease-in-out;
}

@keyframes slide {
    0% {
        opacity: .5;
        background-image: url("../../assets/images/1.webp");
    }
    10% {
        opacity: 1;
        background-image: url("../../assets/images/1.webp");
    }
    12.5% {
        opacity: .5;
        background-image: url("../../assets/images/1.webp");
    }
    12.6% {
        opacity: .5;
        background-image: url("../../assets/images/2.webp");
    }
    23% {
        opacity: 1;
        background-image: url("../../assets/images/2.webp");
    }
    25% {
        opacity: .5;
        background-image: url("../../assets/images/2.webp");
    }
    25.1% {
        opacity: .5;
        background-image: url("../../assets/images/3.webp");
    }
    35% {
        opacity: 1;
        background-image: url("../../assets/images/3.webp");
    }
    37.5% {
        opacity: .5;
        background-image: url("../../assets/images/3.webp");
    }
    37.6% {
        opacity: .5;
        background-image: url("../../assets/images/4.webp");
    }
    50% {
        opacity: 1;
        background-image: url("../../assets/images/4.webp");
    }
    50.1% {
        opacity: .5;
        background-image: url("../../assets/images/4.webp");
    }
    60% {
        opacity: .5;
        background-image: url("../../assets/images/5.webp");
    }
    62.5% {
        opacity: 1;
        background-image: url("../../assets/images/5.webp");
    }
    62.6% {
        opacity: .5;
        background-image: url("../../assets/images/5.webp");
    }
    72.5% {
        opacity: .5;
        background-image: url("../../assets/images/6.webp");
    }
    75% {
        opacity: 1;
        background-image: url("../../assets/images/6.webp");
    }
    75.1% {
        opacity: .5;
        background-image: url("../../assets/images/6.webp");
    }
    85% {
        opacity: .5;
        background-image: url("../../assets/images/7.webp");
    }
    87.5% {
        opacity: 1;
        background-image: url("../../assets/images/7.webp");
    }
    87.6% {
        opacity: .5;
        background-image: url("../../assets/images/7.webp");
    }
    95% {
        opacity: .5;
        background-image: url("../../assets/images/8.webp");
    }
    97.5% {
        opacity: 1;
        background-image: url("../../assets/images/8.webp");
    }
    100% {
        opacity: .5;
        background-image: url("../../assets/images/8.webp");
    }
}
</style>

