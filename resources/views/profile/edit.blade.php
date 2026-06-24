<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <style>
        /* Sembunyikan top navigation bar bawaan layout app */
        nav[x-data] { display: none !important; }
        
        /* Buat header menyatu dengan background halaman */
        header.bg-white.shadow {
            background-color: var(--app-bg) !important;
            box-shadow: none !important;
        }
        
        @media (min-width: 1024px) {
            header.bg-white.shadow { padding-left: 16rem !important; }
        }
        
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #C8C5BE; border-radius: 99px; }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <x-sidebar />

    <div class="py-8 lg:ml-64" style="background-color: var(--app-bg); min-height: calc(100vh - 64px);">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white rounded-2xl shadow-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white rounded-2xl shadow-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white rounded-2xl shadow-sm">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
