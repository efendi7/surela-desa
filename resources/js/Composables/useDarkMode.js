import { ref, watch, onMounted } from 'vue';

export function useDarkMode() {
    // Gunakan nilai dari localStorage sebagai nilai awal, default ke false (light mode)
    const isDark = ref(localStorage.getItem('darkMode') === 'true');

    // Fungsi untuk mengubah state
    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
    };

    // Watcher untuk mereaksikan perubahan pada isDark
    // Ini akan mengubah class pada <html> dan menyimpan ke localStorage
    watch(isDark, (newVal) => {
        if (newVal) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('darkMode', newVal);
    });

    // onMounted untuk memastikan class pada <html> sudah sesuai saat komponen pertama kali dimuat
    onMounted(() => {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        }
    });

    // Kembalikan state dan fungsi agar bisa digunakan di komponen
    return {
        isDark,
        toggleDarkMode,
    };
}
