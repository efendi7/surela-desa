import { computed } from 'vue';

// Konfigurasi terpusat untuk setiap status
const statusConfig = {
    pending: {
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        bgColor: 'bg-yellow-500',
        textColor: 'text-yellow-500',
        badgeClass: 'bg-yellow-100 text-yellow-800',
    },
    diproses: {
        icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        bgColor: 'bg-blue-500',
        textColor: 'text-blue-500',
        badgeClass: 'bg-blue-100 text-blue-800',
    },
    selesai: {
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        bgColor: 'bg-green-500',
        textColor: 'text-green-500',
        badgeClass: 'bg-green-100 text-green-800',
    },
    ditolak: {
        icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        bgColor: 'bg-red-500',
        textColor: 'text-red-500',
        badgeClass: 'bg-red-100 text-red-800',
    },
    default: {
        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        bgColor: 'bg-gray-500',
        textColor: 'text-gray-500',
        badgeClass: 'bg-gray-100 text-gray-800',
    }
};

export function useStatusStyles() {
    /**
     * Mendapatkan konfigurasi style untuk status tertentu.
     * @param {string} status - Status pengajuan ('pending', 'diproses', dll.)
     * @returns {object} Objek konfigurasi style.
     */
    const getStatusStyles = (status) => {
        return statusConfig[status] || statusConfig.default;
    };

    /**
     * Mendapatkan kelas badge lengkap.
     * @param {string} status 
     * @returns {string} String kelas CSS.
     */
    const getStatusBadgeClass = (status) => {
        const baseClasses = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize';
        const style = getStatusStyles(status);
        return `${baseClasses} ${style.badgeClass}`;
    };

    return { getStatusStyles, getStatusBadgeClass };
}