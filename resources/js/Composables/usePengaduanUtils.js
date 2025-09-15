import { format, parseISO } from 'date-fns';
import { id } from 'date-fns/locale';

export function usePengaduanUtils() {
    
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return format(parseISO(dateString), 'd MMMM yyyy, HH:mm', { locale: id });
    };

    const getStatusClass = (status) => {
        const statusMap = {
            'Dikirim': 'border-yellow-400 bg-yellow-50 text-yellow-700',
            'Diterima': 'border-blue-400 bg-blue-50 text-blue-700',
            'Diproses': 'border-indigo-400 bg-indigo-50 text-indigo-700',
            'Selesai': 'border-green-400 bg-green-50 text-green-700',
        };
        return statusMap[status] || 'border-gray-400 bg-gray-50 text-gray-700';
    };

    const getStatusIcon = (status) => {
        const iconMap = {
            'Dikirim': 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', // Jam
            'Diterima': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', // Ceklis
            'Diproses': 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', // Refresh
            'Selesai': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', // Ceklis
        };
        return iconMap[status] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Info
    };

    return { formatDate, getStatusClass, getStatusIcon };
}