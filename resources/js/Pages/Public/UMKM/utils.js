// utils.js - Utility functions for Public UMKM

/**
 * Get foto URL with fallback
 */
export const getFotoUrl = (fotoPath) => {
    if (!fotoPath) return 'https://placehold.co/400x300/e2e8f0/64748b?text=No+Image';
    
    // If it's already a full URL, return as is
    if (fotoPath.startsWith('http')) return fotoPath;
    
    // Remove 'public/' prefix if it exists and add storage URL
    const cleanPath = fotoPath.replace('public/', '');
    return `/storage/${cleanPath}`;
};

/**
 * Get foto pendukung URLs array
 */
export const getFotoPendukungUrls = (fotoPendukung) => {
    if (!fotoPendukung) return [];
    
    let photos = [];
    
    // If it's already an array, use it
    if (Array.isArray(fotoPendukung)) {
        photos = fotoPendukung;
    } else if (typeof fotoPendukung === 'string' && fotoPendukung !== '') {
        try {
            photos = JSON.parse(fotoPendukung);
            if (!Array.isArray(photos)) photos = [];
        } catch (e) {
            // If it's not JSON, treat as single photo path
            photos = [fotoPendukung];
        }
    }
    
    // Convert paths to full URLs
    return photos.map(photo => {
        if (!photo) return 'https://placehold.co/200x150/e2e8f0/64748b?text=No+Image';
        if (photo.startsWith('http')) return photo;
        
        const cleanPath = photo.replace('public/', '');
        return `/storage/${cleanPath}`;
    });
};

/**
 * Format date to readable string
 */
export const formatDate = (dateString) => {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Asia/Jakarta'
    };
    
    return date.toLocaleDateString('id-ID', options);
};

/**
 * Format phone number for display
 */
export const formatPhoneNumber = (phoneNumber) => {
    if (!phoneNumber) return '';
    
    // Remove any non-digit characters
    const digits = phoneNumber.replace(/\D/g, '');
    
    // If starts with 62, format as +62
    if (digits.startsWith('62')) {
        return `+${digits}`;
    }
    
    // If starts with 08, convert to +628
    if (digits.startsWith('08')) {
        return `+62${digits.substring(1)}`;
    }
    
    // If starts with 8, add +62
    if (digits.startsWith('8')) {
        return `+62${digits}`;
    }
    
    // Otherwise return with + prefix
    return `+${digits}`;
};

/**
 * Generate WhatsApp URL
 */
export const generateWhatsAppUrl = (phoneNumber, message = '') => {
    if (!phoneNumber) return '#';
    
    const formattedPhone = formatPhoneNumber(phoneNumber).replace('+', '');
    const encodedMessage = encodeURIComponent(message);
    
    return `https://wa.me/${formattedPhone}?text=${encodedMessage}`;
};

/**
 * Generate default WhatsApp message
 */
export const generateDefaultMessage = (namaUsaha, namaPemilik) => {
    return `Halo ${namaPemilik || 'Bapak/Ibu'}, saya tertarik dengan usaha ${namaUsaha || 'Anda'}. Boleh saya tanya lebih lanjut mengenai produk/layanan yang ditawarkan?`;
};