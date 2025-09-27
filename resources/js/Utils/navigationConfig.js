export const getNavigationSections = (user) => {
    const sections = [
        {
            id: 'main',
            type: 'main',
            title: 'Menu Utama',
            color: 'bg-gradient-to-r from-blue-400 to-blue-500',
            items: [
                {
                    name: 'Dashboard',
                    route: 'dashboard',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>`,
                },
                {
                    name: 'Layanan',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>`,
                    children: [
                        {
                            name: 'Pengajuan Surat',
                            route: 'pengajuan.*',
                            routeLink: 'pengajuan.index',
                        },
                        {
                            name: 'Pengaduan Warga',
                            route: 'pengaduan.*',
                            routeLink: 'pengaduan.index',
                        },
                        { 
                            name: 'Pendaftaran UMKM',
                            route: 'warga.umkm.*',
                            routeLink: 'warga.umkm.index',
                        },
                    ],
                },
            ],
        },
        {
            id: 'profile',
            type: 'profile',
            title: 'Info Desa',
            color: 'bg-gradient-to-r from-emerald-400 to-emerald-500',
            items: [
                {
                    name: 'Profil Desa',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>`,
                    children: [
                        { name: 'Sejarah Desa', route: ['profil.show', 'sejarah'] },
                        { name: 'Visi & Misi', route: ['profil.show', 'visi-misi'] },
                        { name: 'Struktur Organisasi', route: ['profil.show', 'struktur-organisasi'] },
                    ],
                },
                {
                    name: 'Direktori UMKM',
                    route: 'public.umkm.*',
                    routeLink: 'public.umkm.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>`,
                },
                {
                    name: 'Berita Desa',
                    route: 'public.berita.*',
                    routeLink: 'public.berita.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />`
                },
                // ✨ TAMBAHAN: Menu untuk Pengumuman Publik
                {
                    name: 'Pengumuman Desa',
                    route: 'public.pengumuman.index',
                    routeLink: 'public.pengumuman.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.561-1.25-1.25 0-.688.562-1.25 1.25-1.25s1.25.562 1.25 1.25c0 .688-.562 1.25-1.25 1.25Zm0 0a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM10.34 4.34v1.25m6.25 2.5h-1.25m-10-1.25-1.25 1.25m12.5 0 1.25 1.25M4.34 11.59l-1.25-1.25m1.25 11.25 1.25-1.25m10 1.25 1.25-1.25m-1.25-10-1.25-1.25" />`
                },
            ],
        },
    ];

    if (user?.role === 'admin') {
        sections.push({
            id: 'admin',
            type: 'main',
            title: 'Menu Admin',
            color: 'bg-gradient-to-r from-orange-400 to-red-500 animate-pulse',
            items: [
                {
                    name: 'Pengajuan Surat',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>`,
                    children: [
                        { name: 'Jenis Surat', route: 'admin.jenis-surat.*', routeLink: 'admin.jenis-surat.index' },
                        { name: 'Pengajuan Aktif', route: 'admin.proses.index', routeLink: 'admin.proses.index' },
                        { name: 'Riwayat Pengajuan', route: 'admin.proses.riwayat', routeLink: 'admin.proses.riwayat' },
                    ],
                },
                {
                    name: 'Pengaduan Warga',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>`,
                    children: [
                        {
                            name: 'Pengaduan Aktif',
                            route: 'admin.pengaduan.index',
                        },
                        {
                            name: 'Riwayat Pengaduan',
                            route: 'admin.pengaduan.riwayat',
                        },
                    ],
                },
                {
                    name: 'Manajemen UMKM',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>`,
                    route: 'admin.umkm.*',
                    children: [
                        {
                            name: 'Verifikasi Pendaftaran',
                            route: 'admin.umkm.index',
                            routeLink: 'admin.umkm.index'
                        },
                        {
                            name: 'Riwayat UMKM',
                            route: 'admin.umkm.history',
                            routeLink: 'admin.umkm.history'
                        }
                    ]
                },
                // ✨ PERUBAHAN: Nama menu diubah menjadi lebih umum
                {
                    name: 'Manajemen Postingan',
                    route: 'admin.berita.index', // Route tetap sama karena controllernya satu
                    routeLink: 'admin.berita.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />`
                },
                {
                    name: 'Manajemen Desa',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`,
                    children: [
                        {
                            name: 'Profil Desa',
                            route: 'admin.profil-desa.*',
                            routeLink: 'admin.profil-desa.index',
                        },
                        {
                            name: 'Perangkat Desa',
                            route: 'admin.perangkat-desa.*',
                            routeLink: 'admin.perangkat-desa.index',
                        },
                        {
                            name: 'Kelola Pengguna',
                            route: 'admin.users.*',
                            routeLink: 'admin.users.index',
                        },
                    ]
                }
            ],
        });
    }

    return sections;
};
