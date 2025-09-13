<script setup>
import { computed } from 'vue';
import PersonCard from './PerangkatDesaCard.vue';

const props = defineProps({
    perangkatDesa: {
        type: Array,
        default: () => [],
    },
});

// Utility functions
const getInitials = (nama) => {
    if (!nama || nama === 'Belum diisi') return '?';
    return nama.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

const getGradientColor = (index) => {
    const gradients = [
        'from-blue-500 to-purple-600',
        'from-green-500 to-teal-600',
        'from-purple-500 to-pink-600',
        'from-orange-500 to-red-600',
        'from-teal-500 to-cyan-600',
        'from-indigo-500 to-blue-600',
        'from-pink-500 to-rose-600',
        'from-yellow-500 to-orange-600'
    ];
    return gradients[index % gradients.length];
};

// Enhanced perangkat data with computed properties
const enhancedPerangkatDesa = computed(() => 
    props.perangkatDesa.map((perangkat, index) => ({
        ...perangkat,
        initials: getInitials(perangkat.nama),
        gradientColor: getGradientColor(index),
    }))
);
</script>

<template>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-8 rounded-3xl">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-slate-700 to-blue-700 bg-clip-text text-transparent mb-4">
                Struktur Organisasi Desa
            </h2>
            <div class="w-32 h-1.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 mx-auto rounded-full shadow-lg"></div>
            <div class="w-16 h-0.5 bg-gradient-to-r from-blue-300 to-purple-300 mx-auto rounded-full mt-2 opacity-60"></div>
        </div>
        
        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <PersonCard
                v-for="(perangkat, index) in enhancedPerangkatDesa"
                :key="`perangkat-${perangkat.jabatan}-${index}`"
                :perangkat="perangkat"
                :index="index"
            />
        </div>
        
        <!-- Footer Section -->
        <div class="text-center mt-12">
            <div class="inline-flex items-center space-x-3 text-sm text-slate-500">
                <div class="w-12 h-0.5 bg-gradient-to-r from-transparent to-blue-300 rounded-full"></div>
                <span class="font-medium">Tim Profesional Desa</span>
                <div class="w-12 h-0.5 bg-gradient-to-l from-transparent to-indigo-300 rounded-full"></div>
            </div>
        </div>
    </div>
</template>