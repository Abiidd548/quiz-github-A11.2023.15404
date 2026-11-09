<x-partials.layouts.app title="Pendaftaran Poli">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Pendaftaran Pemeriksaan</h1>
            <p class="mt-1 text-sm text-slate-500">Pilih poli dan jadwal pemeriksaan yang tersedia.</p>
        </div>

        @if (session('message'))
            <div class="alert alert-{{ session('type', 'success') }} mb-4 rounded-xl shadow-sm" role="alert">
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error mb-4 rounded-xl shadow-sm" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pasien.daftar.submit') }}" method="POST"
            class="card bg-base-100 rounded-xl border shadow-md">
            @csrf
            <input type="hidden" name="id_pasien" value="{{ $user->id }}">

            <div class="card-body gap-5">
                <div>
                    <label for="id_poli" class="mb-2 block text-sm font-semibold text-slate-700">Poli</label>
                    <select id="id_poli" name="id_poli" required
                        class="select select-bordered w-full @error('id_poli') select-error @enderror">
                        <option value="">Pilih poli</option>
                        @foreach ($polis as $poli)
                            <option value="{{ $poli->id }}" @selected(old('id_poli') == $poli->id)>
                                {{ $poli->nama_poli }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_poli')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_jadwal" class="mb-2 block text-sm font-semibold text-slate-700">Jadwal Dokter</label>
                    <select id="id_jadwal" name="id_jadwal" required
                        class="select select-bordered w-full @error('id_jadwal') select-error @enderror">
                        <option value="">Pilih poli terlebih dahulu</option>
                        @foreach ($jadwals as $jadwal)
                            @if ($jadwal->dokter && $jadwal->dokter->poli)
                                <option value="{{ $jadwal->id }}"
                                    data-poli="{{ $jadwal->dokter->id_poli }}"
                                    @selected(old('id_jadwal') == $jadwal->id)>
                                    {{ $jadwal->dokter->nama }} — {{ $jadwal->dokter->poli->nama_poli }}
                                    ({{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                    - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('id_jadwal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keluhan" class="mb-2 block text-sm font-semibold text-slate-700">Keluhan</label>
                    <textarea id="keluhan" name="keluhan" rows="4"
                        class="textarea textarea-bordered w-full @error('keluhan') textarea-error @enderror"
                        placeholder="Jelaskan keluhan yang dirasakan">{{ old('keluhan') }}</textarea>
                    @error('keluhan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn border-none bg-[#2d4499] text-white hover:bg-[#1e2d6b]">
                        <i class="fas fa-calendar-check"></i>
                        Daftar Pemeriksaan
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const poliSelect = document.getElementById('id_poli');
            const jadwalSelect = document.getElementById('id_jadwal');

            function filterJadwal() {
                const selectedPoli = poliSelect.value;
                let hasSchedule = false;

                for (const option of jadwalSelect.options) {
                    if (!option.dataset.poli) continue;

                    option.hidden = option.dataset.poli !== selectedPoli;
                    option.disabled = option.hidden;
                    if (!option.hidden) hasSchedule = true;
                }

                if (!selectedPoli) {
                    jadwalSelect.value = '';
                    jadwalSelect.options[0].text = 'Pilih poli terlebih dahulu';
                    jadwalSelect.disabled = true;
                } else {
                    if (jadwalSelect.selectedOptions[0]?.hidden) jadwalSelect.value = '';
                    jadwalSelect.options[0].text = hasSchedule
                        ? 'Pilih jadwal dokter'
                        : 'Belum ada jadwal untuk poli ini';
                    jadwalSelect.disabled = !hasSchedule;
                }
            }

            poliSelect.addEventListener('change', filterJadwal);
            filterJadwal();
        </script>
    @endpush
</x-partials.layouts.app>
