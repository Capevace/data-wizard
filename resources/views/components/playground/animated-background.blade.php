@props([
    /** @var \App\Models\ExtractionRun\RunStatus $status */
    'status' => null,
])

<style>
    @keyframes smooth-random-movement-1 {
        0% {
            transform: translate(0, 0);
        }
        15% {
            transform: translate(200%, -100%);
        }
        35% {
            transform: translate(-150%, 250%);
        }
        55% {
            transform: translate(100%, -200%);
        }
        75% {
            transform: translate(-50%, 150%);
        }
        100% {
            transform: translate(0, 0);
        }
    }

    @keyframes smooth-random-movement-2 {
        0% {
            transform: translate(0, 0);
        }
        20% {
            transform: translate(-250%, 150%);
        }
        40% {
            transform: translate(300%, -100%);
        }
        60% {
            transform: translate(-100%, 200%);
        }
        80% {
            transform: translate(150%, -50%);
        }
        100% {
            transform: translate(0, 0);
        }
    }

    @keyframes smooth-random-movement-3 {
        0% {
            transform: translate(0, 0);
        }
        10% {
            transform: translate(100%, 100%);
        }
        30% {
            transform: translate(-200%, -150%);
        }
        50% {
            transform: translate(250%, 200%);
        }
        70% {
            transform: translate(-100%, -50%);
        }
        100% {
            transform: translate(0, 0);
        }
    }

    @keyframes smooth-random-movement-4 {
        0% {
            transform: translate(0, 0);
        }
        25% {
            transform: translate(150%, -200%);
        }
        50% {
            transform: translate(-300%, 100%);
        }
        75% {
            transform: translate(200%, 250%);
        }
        100% {
            transform: translate(0, 0);
        }
    }

    @keyframes smooth-random-movement-5 {
        0% {
            transform: translate(0, 0);
        }
        18% {
            transform: translate(-100%, 150%);
        }
        42% {
            transform: translate(200%, -250%);
        }
        68% {
            transform: translate(-150%, 100%);
        }
        90% {
            transform: translate(50%, -50%);
        }
        100% {
            transform: translate(0, 0);
        }
    }

    .circle-1 {
        position: absolute;
        width: 80px;
        height: 80px;
        background-color: rgba(255, 99, 132, 0.5);
        border-radius: 50%;
        animation: smooth-random-movement-1 30s infinite ease-in-out;
    }

    .circle-2 {
        position: absolute;
        width: 120px;
        height: 120px;
        background-color: rgba(54, 162, 235, 0.5);
        border-radius: 50%;
        animation: smooth-random-movement-2 30s infinite ease-in-out;
    }

    .circle-3 {
        position: absolute;
        width: 200px;
        height: 200px;
        background-color: rgba(85, 204, 112, 0.5);
        border-radius: 50%;
        animation: smooth-random-movement-3 30s infinite ease-in-out;
    }

    .circle-4 {
        position: absolute;
        width: 350px;
        height: 350px;
        background-color: rgba(26, 188, 156, 0.5);
        border-radius: 50%;
        animation: smooth-random-movement-4 30s infinite ease-in-out;
    }

    .circle-5 {
        position: absolute;
        width: 250px;
        height: 250px;
        background-color: rgba(255, 159, 64, 0.5);
        border-radius: 50%;
        animation: smooth-random-movement-5 30s infinite ease-in-out;
    }

    .svg-shape-1 {
        position: absolute;
        width: 400px;
        height: 400px;
        animation: smooth-random-movement-3 60s infinite ease-in-out;
    }

    .svg-shape-2 {
        position: absolute;
        width: 120px;
        height: 120px;
        animation: smooth-random-movement-4 60s infinite ease-in-out;
    }
</style>

<div class="pointer-events-none blur-[100px] opacity-40 filter saturate-100">
    <div class="blur-3xl absolute left-0 top-0 ml-96 -mb-10 circle-1"></div>
    <div class="blur-3xl absolute right-0 top-0 mr-32 -mb-10 circle-2"></div>
    <div class="blur-3xl absolute left-0 bottom-0 -ml-12 -mb-10 circle-3"></div>
    <div class="blur-3xl absolute left-0 top-0 -ml-64 mt-64 circle-4"></div>
    <div class="blur-3xl absolute right-0 bottom-0 -ml-24 -mb-10 circle-5"></div>

    <svg class="blur-3xl  svg-shape-1" viewBox="0 0 100 100">
        <path d="M50 0 C70 20, 80 40, 50 100 C20 40, 30 20, 50 0 Z" fill="rgba(255, 159, 64, 0.5)" />
    </svg>

    <svg class="blur-3xl svg-shape-2" viewBox="0 0 100 100">
        <path d="M50 0 Q75 50, 50 100 Q25 50, 50 0 Z" fill="rgba(201, 203, 207, 0.5)" />
    </svg>

</div>

<div
    x-data="{ status: '{{ $status?->value ?? 'pending' }}'}"
    @class([
        'pointer-events-none absolute inset-0 transition-colors duration-500 ease-in-out',
        'bg-green-600/20' => $status?->value === 'completed',
        'bg-red-600/20' => $status?->value === 'failed',
        'bg-blue-600/20' => $status?->value === 'running',
        'bg-gray-600/20' => $status?->value === 'pending',
    ])
></div>
