<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function create()
    {
        $employees = Employee::all();
        return view('attendance.create', compact('employees'));
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        return response()->json([
            'foto' => $attendance->foto ? asset('storage/' . $attendance->foto) : asset('images/default-user.jpg'),
            'foto_checkout' => $attendance->foto_checkout ? asset('storage/' . $attendance->foto_checkout) : null,
            'nama' => $attendance->nama,
            'lokasi' => $attendance->lokasi,
            'hadir_untuk' => $attendance->hadir_untuk,
            'tanggal' => $attendance->tanggal->format('Y-m-d'),
            'check_in' => $attendance->check_in ? date('H:i:s', strtotime($attendance->check_in)) : null,
            'check_out' => $attendance->check_out ? date('H:i:s', strtotime($attendance->check_out)) : null,
            'work_hours' => $attendance->work_hours,
            'early_leave_reason' => $attendance->early_leave_reason,
            'attendance_percentage' => $attendance->attendance_percentage
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        try {
            // Hapus file foto check-in dari storage
            if ($attendance->foto) {
                Storage::disk('public')->delete($attendance->foto);
                Log::info('Foto check-in berhasil dihapus: ' . $attendance->foto);
            }
            
            // Hapus file foto check-out dari storage (jika ada)
            if ($attendance->foto_checkout) {
                Storage::disk('public')->delete($attendance->foto_checkout);
                Log::info('Foto check-out berhasil dihapus: ' . $attendance->foto_checkout);
            }
            
            // Hapus record dari database
            $attendance->delete();
            
            // Buat notifikasi untuk penghapusan presensi
            Notification::create([
                'user_id' => Auth::id(),
                'title' => 'Presensi Dihapus',
                'message' => 'Data presensi ' . $attendance->nama . ' telah dihapus.',
                'type' => 'warning'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data presensi dan foto berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus presensi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAttendance(Employee $employee)
    {
        $checkedIn = Attendance::where('nama', $employee->nama)
            ->whereDate('tanggal', now()->toDateString())
            ->whereNull('check_out')
            ->exists();

        return response()->json(['checkedIn' => $checkedIn]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'lokasi' => 'required|string',
            'hadir_untuk' => 'required|string|max:255',
            'is_checkout' => 'required|boolean',
            'early_leave_reason' => 'required_if:is_checkout,true,current_hour,<,17'
        ]);
    
        try {
            $fotoPath = $request->file('foto')->store('attendances', 'public');
            
            if ($request->is_checkout) {
                // Update existing attendance record for check-out
                $attendance = Attendance::where('nama', $request->nama)
                    ->whereDate('tanggal', now()->toDateString())
                    ->whereNull('check_out')
                    ->firstOrFail();

                $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                $checkOutTime = now();
                $workHours = $checkOutTime->diffInMinutes($checkInTime);
                
                $attendance->update([
                    'check_out' => $checkOutTime->format('H:i:s'),
                    'foto_checkout' => $fotoPath,
                    'early_leave_reason' => $request->early_leave_reason,
                    'work_hours' => $workHours,
                    'attendance_percentage' => $this->calculateAttendancePercentage($workHours)
                ]);

                // Create notification for check-out
                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => 'Check Out',
                    'message' => $request->nama . ' telah melakukan check out ' . 
                        ($request->early_leave_reason ? '(Pulang Awal)' : ''),
                    'type' => 'info'
                ]);
            } else {
                // Create new attendance record for check-in
                $attendance = Attendance::create([
                    'nama' => $request->nama,
                    'foto' => $fotoPath,
                    'lokasi' => $request->lokasi,
                    'hadir_untuk' => $request->hadir_untuk,
                    'tanggal' => now()->format('Y-m-d'),
                    'check_in' => now()->format('H:i:s'),
                    'work_hours' => 0,
                    'attendance_percentage' => 0
                ]);

                // Create notification for check-in
                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => 'Check In',
                    'message' => $request->nama . ' telah melakukan check in untuk ' . $request->hadir_untuk,
                    'type' => 'info'
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil ' . ($request->is_checkout ? 'di-update!' : 'disimpan!'),
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving attendance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateAttendancePercentage($workMinutes)
    {
        // Assuming 8 hours (480 minutes) is 100%
        $targetMinutes = 480;
        return min(100, ($workMinutes / $targetMinutes) * 100);
    }
}