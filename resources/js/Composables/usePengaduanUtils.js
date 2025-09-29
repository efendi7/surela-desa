// resources/js/Composables/usePengaduanUtils.js

export function usePengaduanUtils() {
    // === Badge / Status ===
    const getStatusClass = (status) => {
        const classes = {
            pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
            diproses: 'bg-blue-100 text-blue-800 border-blue-200',
            selesai: 'bg-green-100 text-green-800 border-green-200',
            ditolak: 'bg-red-100 text-red-800 border-red-200',
        };
        return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200';
    };

    const getStatusIcon = (status) => {
        const icons = {
            pending: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            diproses: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            selesai: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            ditolak: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        };
        return icons[status] || 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    };

    // === Slugify untuk teks ===
    const slugify = (text) => {
        return text
            .toLowerCase()
            .replace(/\s+/g, '_')
            .replace(/[^\w-]+/g, '');
    };

    // === Format Tanggal Ringkas ===
    // contoh output: 27 Sep 25 22:26
    const formatDate = (dateString) => {
        if (!dateString) return '';

        const dateObj = new Date(dateString);

        // tanggal ringkas
        const datePart = dateObj.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: '2-digit',
        });

        // jam ringkas
        const timePart = dateObj.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        });

        return `${datePart} ${timePart}`;
    };

    // === Priority Class ===
    const getPriorityClass = (prioritas) => {
        const priorityMap = {
            'Darurat': 'text-red-700',
            'Tinggi': 'text-orange-600',
            'Sedang': 'text-yellow-600',
            'Rendah': 'text-blue-600',
        };
        return priorityMap[prioritas] || 'text-gray-600';
    };

    return {
        getStatusClass,
        getStatusIcon,
        slugify,
        formatDate,
        getPriorityClass,
    };
}