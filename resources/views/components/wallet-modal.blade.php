<div id="walletModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" onclick="document.getElementById('walletModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div class="inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" style="background-color: var(--card-bg);">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Kelola Dompet
                        </h3>
                        <div class="mt-4 space-y-4">
                            @php
                                $wallets = auth()->check() ? auth()->user()->wallets : collect();
                            @endphp
                            
                            @foreach($wallets as $w)
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold" style="background-color: {{ $w->color ?? '#386650' }}">
                                            {{ strtoupper(substr($w->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $w->name }}</p>
                                            <p class="text-xs text-gray-500">Saldo: {{ format_rupiah($w->balance) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        {{-- We only implement Add/Delete for simplicity, or just Delete if it's not the only one --}}
                                        @if($wallets->count() > 1)
                                            <form method="POST" action="{{ route('wallets.destroy', $w->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dompet ini? Semua transaksi di dalamnya akan ikut terhapus!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            {{-- Form Tambah Dompet --}}
                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                                <h4 class="text-sm font-bold text-gray-900 mb-3">Tambah Dompet Baru</h4>
                                <form method="POST" action="{{ route('wallets.store') }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Dompet</label>
                                        <input type="text" name="name" required placeholder="Contoh: BCA, OVO, Cash" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-[#386650] focus:ring focus:ring-[#386650] focus:ring-opacity-50 input-bg">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Saldo Awal</label>
                                        <input type="number" name="initial_balance" value="0" min="0" step="1" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-[#386650] focus:ring focus:ring-[#386650] focus:ring-opacity-50 input-bg">
                                    </div>
                                    <button type="submit" class="w-full py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#386650] hover:bg-[#2d5241] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#386650] transition-colors">
                                        Simpan Dompet
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" style="background-color: var(--hover-bg);">
                <button type="button" onclick="document.getElementById('walletModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#386650] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm input-bg text-gray-900">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
