export const getFotoUrl = (path) => {
  return path ? `/storage/${path.replace('public/', '')}` : 'https://placehold.co/300x200/e2e8f0/64748b?text=No+Image';
};

export const getFotoPendukungUrls = (fotoPendukung) => {
  if (!fotoPendukung || typeof fotoPendukung !== 'string' || fotoPendukung.trim() === '') {
    return [];
  }
  try {
    const parsed = JSON.parse(fotoPendukung);
    return Array.isArray(parsed) ? parsed.map(path => `/storage/${path.replace('public/', '')}`) : [];
  } catch (e) {
    console.error('Failed to parse foto_pendukung:', fotoPendukung, e);
    return [];
  }
};

export const formatDate = (dateString) => {
  const options = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    timeZone: 'Asia/Jakarta',
  };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

export const statusClass = (status) => {
  switch (status) {
    case 'disetujui':
      return 'bg-green-100 text-green-800 border-green-200';
    case 'ditolak':
      return 'bg-red-100 text-red-800 border-red-200';
    default:
      return 'bg-yellow-100 text-yellow-800 border-yellow-200';
  }
};

export const formatStatus = (status) => {
  const statusMap = {
    pending: 'Menunggu Verifikasi',
    disetujui: 'Disetujui',
    ditolak: 'Ditolak',
  };
  return statusMap[status] || status;
};