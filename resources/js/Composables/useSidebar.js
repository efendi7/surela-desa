import { ref, computed, onMounted, onUnmounted } from 'vue';

export function useSidebar() {
    // State management
    const isCollapsed = ref(false);
    const isMobile = ref(false);
    const sidebarOpen = ref(false);

    // Check mobile view
    const checkMobile = () => {
        const wasIsMobile = isMobile.value;
        isMobile.value = window.innerWidth < 768;
        
        // Auto collapse pada tablet
        if (window.innerWidth < 1024 && window.innerWidth >= 768) {
            isCollapsed.value = true;
        }

        // Jika berubah dari mobile ke desktop, tutup mobile sidebar
        if (wasIsMobile && !isMobile.value && sidebarOpen.value) {
            sidebarOpen.value = false;
        }
    };

    // Handle window resize dengan debouncing
    let resizeTimeout;
    const handleResize = () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            checkMobile();
        }, 100);
    };

    // Toggle sidebar collapse (desktop)
    const toggleCollapse = () => {
        if (!isMobile.value) {
            isCollapsed.value = !isCollapsed.value;
            
            // Save preference to localStorage
            try {
                localStorage.setItem('sidebar-collapsed', isCollapsed.value.toString());
            } catch (error) {
                console.warn('Could not save sidebar preference:', error);
            }
        }
    };

    // Toggle mobile sidebar
    const toggleMobile = () => {
        if (isMobile.value) {
            sidebarOpen.value = !sidebarOpen.value;
        }
    };

    // Close mobile sidebar
    const closeMobile = () => {
        if (isMobile.value) {
            sidebarOpen.value = false;
        }
    };

    // Load saved preference
    const loadPreferences = () => {
        try {
            const savedCollapsed = localStorage.getItem('sidebar-collapsed');
            if (savedCollapsed !== null && !isMobile.value) {
                isCollapsed.value = savedCollapsed === 'true';
            }
        } catch (error) {
            console.warn('Could not load sidebar preference:', error);
        }
    };

    // Handle escape key to close mobile sidebar
    const handleEscapeKey = (event) => {
        if (event.key === 'Escape' && isMobile.value && sidebarOpen.value) {
            closeMobile();
        }
    };

    // Handle click outside to close mobile sidebar
    const handleClickOutside = (event) => {
        if (isMobile.value && sidebarOpen.value) {
            const sidebar = document.querySelector('[class*="sidebar"]');
            const menuButton = document.querySelector('button[aria-label*="Toggle"], button[title*="Toggle"]');
            
            if (sidebar && 
                !sidebar.contains(event.target) && 
                menuButton && 
                !menuButton.contains(event.target)) {
                closeMobile();
            }
        }
    };

    // Computed properties
    const sidebarClasses = computed(() => {
        return {
            'sidebar-collapsed': isCollapsed.value && !isMobile.value,
            'sidebar-mobile-open': sidebarOpen.value && isMobile.value,
            'sidebar-mobile-closed': !sidebarOpen.value && isMobile.value,
            'sidebar-mobile': isMobile.value,
            'sidebar-desktop': !isMobile.value
        };
    });

    const overlayVisible = computed(() => {
        return isMobile.value && sidebarOpen.value;
    });

    const sidebarWidth = computed(() => {
        if (isMobile.value) {
            return sidebarOpen.value ? '288px' : '0px'; // w-72 = 288px
        }
        return isCollapsed.value ? '64px' : '288px'; // w-16 = 64px, w-72 = 288px
    });

    // Prevent body scroll when mobile sidebar is open
    const toggleBodyScroll = (disable) => {
        if (typeof document !== 'undefined') {
            if (disable) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    };

    // Watch for mobile sidebar changes to control body scroll
    const watchSidebarOpen = () => {
        if (isMobile.value) {
            toggleBodyScroll(sidebarOpen.value);
        }
    };

    // Lifecycle hooks
    onMounted(() => {
        // Initial setup
        checkMobile();
        loadPreferences();

        // Event listeners
        window.addEventListener('resize', handleResize, { passive: true });
        document.addEventListener('keydown', handleEscapeKey);
        document.addEventListener('click', handleClickOutside, { passive: true });
    });

    onUnmounted(() => {
        // Cleanup
        clearTimeout(resizeTimeout);
        toggleBodyScroll(false); // Restore body scroll
        
        // Remove event listeners
        window.removeEventListener('resize', handleResize);
        document.removeEventListener('keydown', handleEscapeKey);
        document.removeEventListener('click', handleClickOutside);
    });

    // Watch untuk perubahan sidebarOpen
    const stopWatcher = computed(() => {
        watchSidebarOpen();
        return sidebarOpen.value;
    });

    // Public API
    return {
        // State
        isCollapsed,
        isMobile,
        sidebarOpen,
        
        // Computed
        sidebarClasses,
        overlayVisible,
        sidebarWidth,
        
        // Methods
        toggleCollapse,
        toggleMobile,
        closeMobile,
        checkMobile,
        
        // Utility methods
        forceCollapse: () => { 
            if (!isMobile.value) {
                isCollapsed.value = true; 
            }
        },
        forceExpand: () => { 
            if (!isMobile.value) {
                isCollapsed.value = false; 
            }
        },
        forceMobileClose: () => { 
            if (isMobile.value) {
                sidebarOpen.value = false; 
            }
        },
        forceMobileOpen: () => { 
            if (isMobile.value) {
                sidebarOpen.value = true; 
            }
        }
    };
}