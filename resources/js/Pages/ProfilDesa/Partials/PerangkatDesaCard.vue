<script setup>
defineProps({
    perangkat: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="group relative transform hover:scale-105 transition-all duration-500">
        <!-- Decorative background elements -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-200 via-indigo-200 to-purple-200 rounded-3xl opacity-20 group-hover:opacity-40 transition-opacity duration-300 blur-sm"></div>
        
        <!-- Main card -->
        <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 border border-white/50">
            
            <!-- Decorative header with frame motif -->
            <div class="relative h-24 bg-gradient-to-br from-slate-600 via-blue-600 to-indigo-600 overflow-hidden">
                <!-- Frame pattern -->
                <div class="absolute inset-0 opacity-20">
                    <!-- Corner decorations -->
                    <div class="absolute top-2 left-2 w-8 h-8 border-l-2 border-t-2 border-white/40 rounded-tl-lg"></div>
                    <div class="absolute top-2 right-2 w-8 h-8 border-r-2 border-t-2 border-white/40 rounded-tr-lg"></div>
                    <div class="absolute bottom-2 left-2 w-8 h-8 border-l-2 border-b-2 border-white/40 rounded-bl-lg"></div>
                    <div class="absolute bottom-2 right-2 w-8 h-8 border-r-2 border-b-2 border-white/40 rounded-br-lg"></div>
                    
                    <!-- Center ornament -->
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <div class="w-12 h-12 border-2 border-white/30 rounded-full flex items-center justify-center">
                            <div class="w-6 h-6 bg-white/20 rounded-full"></div>
                        </div>
                    </div>
                    
                    <!-- Geometric patterns -->
                    <div class="absolute top-4 left-1/2 transform -translate-x-1/2 w-16 h-0.5 bg-white/30 rounded-full"></div>
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 w-20 h-0.5 bg-white/30 rounded-full"></div>
                </div>
                
                <!-- Subtle overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-black/10"></div>
            </div>
            
            <!-- Content section -->
            <div class="relative -mt-14 px-6 pb-8">
                <!-- Profile picture with elegant frame -->
                <div class="relative mx-auto w-28 h-28 mb-6">
                    <!-- Outer decorative ring -->
                    <div class="absolute -inset-2 bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 rounded-full shadow-lg"></div>
                    
                    <!-- Inner white ring -->
                    <div class="absolute -inset-1 bg-white rounded-full shadow-md"></div>
                    
                    <!-- Profile image/initial -->
                    <img 
                        v-if="perangkat.foto_url"
                        :src="perangkat.foto_url"
                        :alt="`Foto ${perangkat.nama}`"
                        class="relative w-full h-full rounded-full object-cover border-3 border-white shadow-lg"
                    >
                    <div 
                        v-else 
                        class="relative w-full h-full rounded-full bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100 border-3 border-white shadow-lg flex items-center justify-center"
                    >
                        <span class="text-2xl font-bold bg-gradient-to-br from-slate-600 to-blue-600 bg-clip-text text-transparent">
                            {{ perangkat.initials }}
                        </span>
                    </div>
                    
                    <!-- Status indicator -->
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-gradient-to-br from-emerald-400 to-green-500 border-4 border-white rounded-full shadow-lg flex items-center justify-center">
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                    
                    <!-- Decorative elements around photo -->
                    <div class="absolute -top-2 -left-2 w-3 h-3 bg-blue-300 rounded-full opacity-60 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute -bottom-2 -right-2 w-2 h-2 bg-indigo-400 rounded-full opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>
                
                <!-- Name and position -->
                <div class="text-center space-y-3">
                    <h3 class="font-bold text-xl text-slate-800 leading-tight group-hover:text-blue-700 transition-colors duration-300">
                        {{ perangkat.nama }}
                    </h3>
                    
                    <!-- Position badge with frame -->
                    <div class="relative inline-block">
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-200 to-indigo-200 rounded-xl opacity-50"></div>
                        <p class="relative text-sm font-semibold text-slate-700 bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-2.5 rounded-lg border border-blue-100 shadow-sm">
                            {{ perangkat.jabatan }}
                        </p>
                    </div>
                </div>
                
                <!-- Decorative bottom elements -->
                <div class="flex justify-center items-center space-x-2 mt-6">
                    <div class="w-1 h-1 rounded-full bg-blue-300 opacity-60"></div>
                    <div class="w-8 h-0.5 bg-gradient-to-r from-blue-200 to-indigo-200 rounded-full"></div>
                    <div class="w-2 h-2 rounded-full bg-gradient-to-br from-blue-400 to-indigo-400 shadow-sm"></div>
                    <div class="w-8 h-0.5 bg-gradient-to-r from-indigo-200 to-purple-200 rounded-full"></div>
                    <div class="w-1 h-1 rounded-full bg-purple-300 opacity-60"></div>
                </div>
            </div>
            
            <!-- Hover overlay with frame effect -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-all duration-500 rounded-2xl">
                <!-- Animated frame on hover -->
                <div class="absolute inset-2 border border-blue-300/30 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            </div>
        </div>
        
        <!-- Floating decorative elements -->
        <div class="absolute -z-10 top-6 -left-2 w-4 h-4 bg-blue-200/60 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 animate-pulse"></div>
        <div class="absolute -z-10 bottom-8 -right-3 w-3 h-3 bg-indigo-300/60 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 animate-bounce"></div>
        <div class="absolute -z-10 top-12 -right-1 w-2 h-2 bg-purple-200/60 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 animate-ping"></div>
    </div>
</template>

<style scoped>
/* Border utilities */
.border-3 {
    border-width: 3px;
}

/* Enhanced shadow effects */
.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 20px 20px -10px rgba(0, 0, 0, 0.1);
}

/* Hover glow effect */
.group:hover .shadow-2xl {
    box-shadow: 
        0 25px 50px -12px rgba(59, 130, 246, 0.15), 
        0 20px 20px -10px rgba(99, 102, 241, 0.1),
        0 0 0 1px rgba(59, 130, 246, 0.05);
}

/* Subtle backdrop blur enhancement */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
</style>