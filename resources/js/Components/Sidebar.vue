<template>
  <aside
    :class="[
      'fixed left-0 top-0 h-screen flex flex-col transition-all duration-300 ease-in-out',
      'bg-gradient-to-br from-slate-900/95 via-gray-900/95 to-slate-800/95 backdrop-blur-xl',
      'border-r border-slate-700/50 shadow-2xl will-change-transform',
      // Desktop styles
      !isMobile && 'w-72 z-20',
!isMobile && (isCollapsed ? '-translate-x-full' : 'translate-x-0'),
      // Mobile styles
      isMobile && 'w-72 transform z-30',
      isMobile && (sidebarOpen ? 'translate-x-0' : '-translate-x-full')
    ]"
    style="backface-visibility: hidden; transform-style: preserve-3d;"
  >
    <!-- HEADER -->
    <SidebarHeader
      :is-collapsed="isCollapsed"
      :is-mobile="isMobile"
      :profil-desa="profilDesa"
      @close-mobile="emit('close-mobile')"
      @link-click="handleLinkClick"
    />

    <!-- NAVIGATION -->
    <nav class="flex-1 min-h-0 overflow-hidden">
      <div
        class="h-full overflow-y-auto smooth-scroll"
        :class="[isCollapsed && !isMobile ? 'px-2' : 'px-4']"
      >
        <div class="py-6 space-y-8">
          <template v-for="section in navigationSections" :key="section.id">
            <!-- wrap NavigationSection with Tooltip automatically when collapsed -->
            <NavigationSection
              :section="section"
              :is-collapsed="isCollapsed"
              :is-mobile="isMobile"
              :hovered-item="hoveredItem"
              :tooltip-position="tooltipPosition"
              @item-hover="handleItemHover"
              @link-click="handleLinkClick"
            />
          </template>
        </div>
      </div>
    </nav>

    <!-- FOOTER -->
    <SidebarFooter :is-collapsed="isCollapsed" :is-mobile="isMobile" />
  </aside>
</template>

<script setup>
import SidebarHeader from '@/Components/Sidebar/SidebarHeader.vue'
import NavigationSection from '@/Components/Sidebar/NavigationSection.vue'
import SidebarFooter from '@/Components/Sidebar/SidebarFooter.vue'
import { getNavigationSections } from '@/Utils/navigationConfig'
import { usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

// Props dari parent
const props = defineProps({
  isCollapsed: Boolean,
  isMobile: Boolean,
  sidebarOpen: Boolean
})

// Events untuk parent
const emit = defineEmits(['close-mobile'])

// Data dari Inertia
const profilDesa = computed(() => usePage().props.profilDesa)
const user = computed(() => usePage().props.auth.user)

// State hover dan tooltip
const hoveredItem = ref(null)
const tooltipPosition = ref(0)

// Ambil sections dari config
const navigationSections = computed(() => getNavigationSections(user.value))

// Hover item
const handleItemHover = (itemName, event) => {
  hoveredItem.value = itemName
  if (event && event.target) {
    tooltipPosition.value = event.target.offsetTop
  }
}

// Klik link di mobile auto close
const handleLinkClick = () => {
  if (props.isMobile) {
    emit('close-mobile')
  }
}
</script>

<style scoped>
/* Smooth scrolling */
.smooth-scroll {
  scroll-behavior: smooth;
  scrollbar-width: thin;
  scrollbar-color: #475569 #1e293b;
  overscroll-behavior: contain;
  scroll-padding-top: 60px;
}

.smooth-scroll::-webkit-scrollbar {
  width: 4px;
}

.smooth-scroll::-webkit-scrollbar-track {
  background: #1e293b;
  border-radius: 2px;
}

.smooth-scroll::-webkit-scrollbar-thumb {
  background: #475569;
  border-radius: 2px;
  transition: background 0.2s ease;
}

.smooth-scroll::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}

.will-change-transform {
  will-change: transform;
}

.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Optimasi mobile */
@media (max-width: 768px) {
  aside {
    -webkit-overflow-scrolling: touch;
  }
}
</style>
