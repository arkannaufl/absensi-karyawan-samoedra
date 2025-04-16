<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <title>Absensi Digital | Samoedra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fredericka+the+Great&family=Fredoka:wght@300..700&family=Fuzzy+Bubbles:wght@400;700&family=Onest:wght@100..900&display=swap"
        rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>

<body class="bg-gradient-to-br from-teal-50 to-blue-50 min-h-screen" style="font-family: 'Fredoka', sans-serif;">
    <div class="container mx-auto px-4 py-12">
        <!-- Card utama -->
        <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <!-- Header card -->
            <div class="bg-[#3E5467] px-6 py-4 space-y-1">
                <h2 class="text-2xl font-semibold text-white">Formulir Presensi</h2>
                <p class="text-indigo-100 text-sm">Silakan lengkapi data presensi Anda</p>
            </div>

            <!-- Body card -->
            <div class="p-6">
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                <form id="attendanceForm" class="space-y-6">
                    <!-- Input Nama (Dropdown) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <select id="nama" name="nama" required
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-[#7BA5B0] focus:ring-[#7BA5B0] py-3 px-4 border">
                                <option value="" disabled selected>Pilih karyawan</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->nama }}" data-hadir-untuk="{{ $employee->hadir_untuk }}" data-id="{{ $employee->id }}">
                                    {{ $employee->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="alreadyCheckedInWarning" class="hidden mt-2 text-yellow-600 text-sm">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <span>Anda sudah melakukan check-in hari ini. Apakah Anda ingin melakukan check-out?</span>
                        </div>
                    </div>

                    <!-- Input Hadir Untuk (Autofill) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hadir Untuk</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="text" id="hadir_untuk" name="hadir_untuk" readonly
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 py-3 px-4 border">
                        </div>
                    </div>

                    <!-- Foto Selfie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Selfie</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center bg-gray-50 transition hover:border-[#7BA5B0]">
                            <div class="relative">
                                <video id="video" width="320" height="240" autoplay class="mx-auto rounded-lg shadow mb-2"></video>
                                <canvas id="canvas" width="320" height="240" class="hidden mx-auto rounded-lg shadow mb-2"></canvas>
                                <div id="flash" class="flash-effect"></div>
                                <div id="countdown" class="countdown"></div>

                                <button type="button" id="captureBtn"
                                    class="mt-4 inline-flex items-center px-4 py-2 hover:bg-[#7BA5B0] border border-transparent rounded-lg font-medium text-white bg-[#3E5467] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7BA5B0] transition">
                                    <i class="fas fa-camera mr-2"></i>
                                    <span id="captureBtnText">Ambil Foto</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Pastikan wajah terlihat jelas dalam frame</p>
                        </div>
                    </div>

                    <!-- Informasi Presensi -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="text" id="lokasi" name="lokasi" readonly
                                    class="pl-10 w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 py-3 px-4 border">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="far fa-calendar text-gray-400"></i>
                                    </div>
                                    <input type="text" id="tanggal" name="tanggal" readonly
                                        class="pl-10 w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 py-3 px-4 border">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="far fa-clock text-gray-400"></i>
                                    </div>
                                    <input type="text" id="jam" name="jam" readonly
                                        class="pl-10 w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 py-3 px-4 border">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Early Leave Reason (akan muncul jika checkout sebelum jam 5) -->
                    <div id="earlyLeaveContainer" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Check Out Awal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-comment-alt text-gray-400"></i>
                            </div>
                            <textarea id="earlyLeaveReason" name="early_leave_reason" rows="2"
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-[#7BA5B0] focus:ring-[#7BA5B0] py-3 px-4 border"
                                placeholder="Jelaskan alasan Anda Check Out sebelum jam 5"></textarea>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-4">
                        <button type="submit" id="submitBtn"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-gradient-to-r from-[#7BA5B0] to-[#3E5467] hover:from-[#3E5467] hover:to-[#7BA5B0] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7BA5B0] transition">
                            <i class="fas fa-paper-plane mr-2"></i>
                            <span id="submitBtnText">Simpan Data Presensi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer card -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    <i class="fas fa-lock mr-1"></i> Data presensi Anda aman dan terenkripsi
                </p>
            </div>
        </div>
    </div>
    @include('components.alert-modal')
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>