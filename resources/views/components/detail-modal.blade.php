<div id="detailModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0" id="detailModalContent">
        <!-- Close Button -->
        <button id="closeModalBtn" class="absolute top-4 right-4 p-3 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Modal Content -->
        <div class="p-4">
            <!-- Header -->
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-4 rounded-xl mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Detail Presensi</h3>
            </div>

            <!-- Content Sections -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Check-in Section -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-3 rounded-xl border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Check In</h4>
                    <div class="relative rounded-xl overflow-hidden mb-3">
                        <img id="detailFotoCheckIn" src="" alt="Foto Check In" class="w-full h-48 object-cover bg-gray-100">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/80 p-4 rounded-lg">
                            <label class="block text-sm text-gray-500 mb-0.5">Waktu Check In</label>
                            <p id="detailCheckInTime" class="text-base font-medium text-gray-800">-</p>
                        </div>
                        <div class="bg-white/80 p-4 rounded-lg">
                            <label class="block text-sm text-gray-500 mb-0.5">Status</label>
                            <p id="detailCheckInStatus" class="text-base font-medium text-green-600">-</p>
                        </div>
                    </div>
                </div>

                <!-- Check-out Section -->
                <div id="checkoutInfo" class="bg-gradient-to-r from-blue-50 to-purple-50 p-3 rounded-xl border border-gray-200 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Check Out</h4>
                    <div class="relative rounded-xl overflow-hidden mb-3">
                        <img id="detailFotoCheckOut" src="" alt="Foto Check Out" class="w-full h-48 object-cover bg-gray-100">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/80 p-4 rounded-lg">
                            <label class="block text-sm text-gray-500 mb-0.5">Waktu Check Out</label>
                            <p id="detailCheckOutTime" class="text-base font-medium text-gray-800">-</p>
                        </div>
                        <div class="bg-white/80 p-4 rounded-lg">
                            <label class="block text-sm text-gray-500 mb-0.5">Durasi Kerja</label>
                            <p id="detailWorkHours" class="text-base font-medium text-gray-800">-</p>
                        </div>
                    </div>
                    <div id="earlyLeaveInfo" class="mt-3 bg-yellow-50 p-4 rounded-lg hidden">
                        <label class="block text-sm text-yellow-600 mb-0.5">Alasan Pulang Awal</label>
                        <p id="detailEarlyLeaveReason" class="text-base font-medium text-gray-800">-</p>
                    </div>
                </div>

                <!-- Employee Info Section -->
                <div class="col-span-2 grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 p-3 rounded-xl">
                        <label class="block text-sm text-gray-500 mb-0.5 uppercase tracking-wider">Nama</label>
                        <p id="detailNama" class="text-base font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-xl">
                        <label class="block text-sm text-gray-500 mb-0.5 uppercase tracking-wider">Hadir Untuk</label>
                        <p id="detailHadirUntuk" class="text-base font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-xl">
                        <label class="block text-sm text-gray-500 mb-0.5 uppercase tracking-wider">Lokasi</label>
                        <p id="detailLokasi" class="text-base font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-xl">
                        <label class="block text-sm text-gray-500 mb-0.5 uppercase tracking-wider">Tanggal</label>
                        <p id="detailTanggal" class="text-base font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-xl">
                        <label class="block text-sm text-gray-500 mb-0.5 uppercase tracking-wider">Persentase Kehadiran</label>
                        <p id="detailAttendancePercentage" class="text-base font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>