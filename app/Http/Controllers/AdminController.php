<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function dashboard()
    {
        $totalAbsensi = Attendance::count();
        $absensiHariIni = Attendance::whereDate('tanggal', today())->count();
        $absensiBulanIni = Attendance::whereMonth('tanggal', now()->month)->count();
        $totalKaryawan = Employee::count();
        
        // Get perPage from request or default to 5
        $perPage = request('per_page', 5);
        $attendances = Attendance::latest()->paginate($perPage);
        
        $employees = Employee::all();
    
        return view('admin.dashboard', compact(
            'totalAbsensi',
            'absensiHariIni',
            'absensiBulanIni',
            'totalKaryawan',
            'attendances',
            'employees'
        ));
    }

    public function index()
    {
        $query = Attendance::query();
        
        if(request('date')) {
            $query->whereDate('tanggal', request('date'));
        }
        
        $attendances = $query->latest()->paginate(10);
        $recentAttendances = Attendance::latest()->take(5)->get();
        
        return view('admin.attendances.index', compact('attendances', 'recentAttendances'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'hadir_untuk' => 'required|string|max:255',
        ]);

        try {
            $employee = Employee::create([
                'nama' => $request->nama,
                'hadir_untuk' => $request->hadir_untuk,
            ]);

            // Create notification for new employee
            Notification::create([
                'user_id' => Auth::id(),
                'title' => 'Karyawan Baru Ditambahkan',
                'message' => 'Karyawan ' . $request->nama . ' telah ditambahkan ke sistem.',
                'type' => 'success'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan!',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyEmployee(Employee $employee)
    {
        try {
            $employee->delete();
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete employee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateEmployee(Request $request, Employee $employee)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'hadir_untuk' => 'required|string|max:255',
        ]);

        try {
            $employee->update([
                'nama' => $request->nama,
                'hadir_untuk' => $request->hadir_untuk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diperbarui!',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error('Update employee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchEmployees(Request $request)
    {
        try {
            $search = trim($request->input('search', ''));
    
            Log::info('Search employees query: ' . $search);
    
            if (empty($search)) {
                Log::info('Search query is empty, returning empty array');
                return response()->json([]);
            }
    
            // Pecah query menjadi kata-kata terpisah
            $keywords = array_filter(explode(' ', $search));
    
            $query = Employee::query();
    
            // Tambahkan kondisi untuk setiap kata kunci
            foreach ($keywords as $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%")
                      ->orWhere('hadir_untuk', 'like', "%{$keyword}%");
                });
            }
    
            $employees = $query->limit(10)
                ->get()
                ->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'nama' => $employee->nama,
                        'hadir_untuk' => $employee->hadir_untuk,
                        'foto' => $employee->foto ? Storage::url($employee->foto) : null,
                    ];
                });
    
            Log::info('Search employees results: ' . $employees->count() . ' records found');
            return response()->json($employees);
    
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian karyawan',
            ], 500);
        }
    }
    
    public function searchAttendances(Request $request)
    {
        try {
            $search = trim($request->input('search', ''));
            $sort = $request->input('sort', 'latest'); // Default to latest
            $date = $request->input('date');
    
            $query = Attendance::query();
    
            // Apply search filter
            if (!empty($search)) {
                $keywords = array_filter(explode(' ', $search));
                foreach ($keywords as $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%")
                          ->orWhere('hadir_untuk', 'like', "%{$keyword}%")
                          ->orWhere('lokasi', 'like', "%{$keyword}%");
                    });
                }
            }
    
            // Apply date filter
            if ($date) {
                $query->whereDate('tanggal', $date);
            }
    
            // Apply sorting
            if ($sort === 'oldest') {
                $query->oldest();
            } else {
                $query->latest();
            }
    
            $attendances = $query->take(10)
                ->get()
                ->map(function ($attendance) {
                    $duration = $attendance->check_out 
                        ? floor($attendance->work_hours / 60).' jam '.($attendance->work_hours % 60).' menit'
                        : 'Masih bekerja';
                    
                    return [
                        'id' => $attendance->id,
                        'nama' => $attendance->nama,
                        'hadir_untuk' => $attendance->hadir_untuk,
                        'lokasi' => $attendance->lokasi,
                        'tanggal' => $attendance->tanggal,
                        'check_in' => $attendance->check_in ?? '-',
                        'check_out' => $attendance->check_out ?? '-',
                        'duration' => $duration,
                        'foto' => $attendance->foto,
                    ];
                });
    
            Log::info('Search attendances results: ' . $attendances->count() . ' records found');
            return response()->json($attendances);
    
        } catch (\Exception $e) {
            Log::error('Search attendances error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian presensi',
            ], 500);
        }
    }
}
