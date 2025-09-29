import { ref, onMounted } from 'vue';
import axios from 'axios';

export function useNotifications() {
    const notifications = ref([]);
    const notificationCount = ref(0);
    const isLoading = ref(false);

    const fetchNotifications = async () => {
        isLoading.value = true;
        try {
            const response = await axios.get(route('notifikasi.index'));
            notifications.value = response.data.notifications;
            notificationCount.value = response.data.count;
        } catch (error) {
            console.error('Gagal mengambil notifikasi:', error);
            // Anda bisa menambahkan notifikasi error untuk user di sini
        } finally {
            isLoading.value = false;
        }
    };

    const markAsRead = async (notificationId) => {
        // Optimistic update: langsung kurangi jumlah notifikasi di UI
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification && !notification.read_at) {
             notificationCount.value = Math.max(0, notificationCount.value - 1);
        }
       
        try {
            // Kirim request ke server di background
            await axios.patch(route('notifikasi.read'), { id: notificationId });
            // Refresh data setelah berhasil
            await fetchNotifications();
        } catch (error) {
            console.error('Gagal menandai notifikasi sebagai terbaca:', error);
            // Jika gagal, refresh lagi untuk mengembalikan state
            await fetchNotifications();
        }
    };

    // Ambil notifikasi saat composable ini pertama kali digunakan
    onMounted(fetchNotifications);

    return {
        notifications,
        notificationCount,
        isLoading,
        fetchNotifications,
        markAsRead,
    };
}
