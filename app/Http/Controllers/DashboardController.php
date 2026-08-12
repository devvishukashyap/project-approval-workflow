<?php

namespace App\Http\Controllers;

use App\Mail\ProjectStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Approval;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    
    private function getStats()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $projects = Project::all();
        } else {
            $projects = $user->projects;
        }

        $total = $projects->count();
        $pending = $projects->where('status', 'pending')->count();
        $approved = $projects->where('status', 'approved')->count();
        $rejected = $projects->where('status', 'rejected')->count();

        return [
            'total'    => $total,
            'pending'  => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
        ];
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $projects = Project::latest()->get();
        } else {
            $projects = $user->projects()->latest()->get();
        }

        $total = $projects->count();
        $pending = $projects->where('status', 'pending')->count();
        $approved = $projects->where('status', 'approved')->count();
        $rejected = $projects->where('status', 'rejected')->count();

        $approvedPercent = $total > 0 ? round(($approved / $total) * 100) : 0;
        $pendingPercent = $total > 0 ? round(($pending / $total) * 100) : 0;
        $rejectedPercent = $total > 0 ? round(($rejected / $total) * 100) : 0;

        $users = [];
        if ($user->role === 'admin') {
            $users = \App\Models\User::where('role', 'user')->get();
        }

        return view('dashboard', compact(
            'total', 'pending', 'approved', 'rejected',
            'approvedPercent', 'pendingPercent', 'rejectedPercent',
            'users'
        ));
    }

    public function projects(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $query = Project::with('user')->select('projects.*');
        } else {
            $query = $user->projects()->with('user')->select('projects.*');
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('submitter') && $request->submitter != 'all') {
            $query->where('user_id', $request->submitter);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('submitter', function ($project) {
                if ($project->user_id == Auth::id()) {
                    return '<strong>You</strong>';
                }
                return $project->user->name;
            })
            ->editColumn('status', function ($project) {
                if ($project->status === 'pending') {
                    return '<span class="badge bg-warning text-dark">⏳ Pending</span>';
                } elseif ($project->status === 'approved') {
                    return '<span class="badge bg-success">✅ Approved</span>';
                } else {
                    return '<span class="badge bg-danger">❌ Rejected</span>';
                }
            })
            ->editColumn('submitted_at', function ($project) {
                return $project->submitted_at ? $project->submitted_at->format('d M Y, h:i A') : '-';
            })
            ->editColumn('updated_at', function ($project) {
                return $project->updated_at ? $project->updated_at->format('d M Y, h:i A') : '-';
            })
            ->addColumn('actions', function ($project) use ($user) {
                if ($user->role === 'admin') {
                    return '
                        <button class="btn btn-sm btn-success approve-btn" data-id="' . $project->id . '">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                        <button class="btn btn-sm btn-danger reject-btn" data-id="' . $project->id . '" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                        <button class="btn btn-sm btn-outline-secondary history-btn" data-id="' . $project->id . '" data-bs-toggle="modal" data-bs-target="#historyModal">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    ';
                } else {
                    return '
                        <button class="btn btn-sm btn-outline-primary view-btn" data-id="' . $project->id . '">
                            <i class="bi bi-eye"></i> View
                        </button>
                    ';
                }
            })
            ->rawColumns(['submitter', 'status', 'actions'])
            ->make(true);
    }

    public function showProject($id)
    {
        $project = Project::with('user')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $project->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'status' => $project->status,
            'submitter_name' => $project->user->name,
            'submitter_email' => $project->user->email,
            'submitted_at' => $project->submitted_at ? $project->submitted_at->format('d M Y, h:i A') : '-',
            'updated_at' => $project->updated_at ? $project->updated_at->format('d M Y, h:i A') : '-',
            'file_path' => $project->file_path ? asset('storage/' . $project->file_path) : null,
        ]);
    }

    public function approve($id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $project = Project::findOrFail($id);

        if ($project->status === 'approved') {
            return response()->json(['error' => 'Project already approved.'], 400);
        }

        try {
            DB::statement("CALL sp_approve_project(?, ?, @status)", [$id, $user->id]);

            $status = DB::select("SELECT @status as status")[0]->status;

            if ($status === 'SUCCESS') {
                Approval::create([
                    'project_id' => $id,
                    'admin_id' => $user->id,
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);

                $project->load('user');

                Mail::to($project->user->email)
                    ->queue(new ProjectStatusMail($project, 'approved'));

                return response()->json([
                    'success' => true,
                    'message' => 'Project approved successfully.',
                    'stats' => $this->getStats()
                ]);
            }

            return response()->json(['error' => 'Stored procedure failed.'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $project = Project::findOrFail($id);

        if ($project->status === 'rejected') {
            return response()->json(['error' => 'Project already rejected.'], 400);
        }

        try {
            DB::beginTransaction();

            $project->status = 'rejected';
            $project->save();

            Approval::create([
                'project_id' => $id,
                'admin_id' => $user->id,
                'status' => 'rejected',
                'reason' => $request->reason,
                'approved_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'project_id' => $id,
                'action' => 'rejected',
                'details' => json_encode(['reason' => $request->reason]),
                'created_at' => now(),
            ]);

            DB::commit();

            $project->load('user');

            Mail::to($project->user->email)
                ->queue(new ProjectStatusMail($project, 'rejected', $request->reason));

            return response()->json([
                'success' => true,
                'message' => 'Project rejected.',
                'stats' => $this->getStats()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ids = $request->ids;

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['error' => 'No projects selected.'], 400);
        }

        $success = 0;
        $failed = 0;

        foreach ($ids as $id) {
            try {
                $project = Project::find($id);

                if (!$project || $project->status === 'approved') {
                    $failed++;
                    continue;
                }

                DB::statement("CALL sp_approve_project(?, ?, @status)", [$id, $user->id]);

                $status = DB::select("SELECT @status as status")[0]->status;

                if ($status === 'SUCCESS') {
                    Approval::create([
                        'project_id' => $id,
                        'admin_id' => $user->id,
                        'status' => 'approved',
                        'approved_at' => now(),
                    ]);

                    $project->load('user');

                    Mail::to($project->user->email)
                        ->queue(new ProjectStatusMail($project, 'approved'));

                    $success++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Approved: $success, Failed: $failed",
            'stats' => $this->getStats()
        ]);
    }

    public function bulkReject(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'ids' => 'required|array|min:1',
            'reason' => 'required|string|max:500'
        ]);

        $ids = $request->ids;
        $reason = $request->reason;

        $success = 0;
        $failed = 0;

        foreach ($ids as $id) {
            try {
                $project = Project::find($id);

                if ($project && $project->status !== 'rejected') {
                    DB::beginTransaction();

                    $project->status = 'rejected';
                    $project->save();

                    Approval::create([
                        'project_id' => $id,
                        'admin_id' => $user->id,
                        'status' => 'rejected',
                        'reason' => $reason,
                        'approved_at' => now(),
                    ]);

                    AuditLog::create([
                        'user_id' => $user->id,
                        'project_id' => $id,
                        'action' => 'rejected',
                        'details' => json_encode(['reason' => $reason]),
                        'created_at' => now(),
                    ]);

                    DB::commit();

                    $project->load('user');

                    Mail::to($project->user->email)
                        ->queue(new ProjectStatusMail($project, 'rejected', $reason));

                    $success++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Rejected: $success, Failed: $failed",
            'stats' => $this->getStats()
        ]);
    }

    public function history($id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = AuditLog::where('project_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $history = $logs->map(function ($log) {
            return [
                'action' => $log->action,
                'user' => $log->user ? $log->user->name : 'Unknown User',
                'time' => $log->created_at->format('d M Y, h:i A'),
                'details' => $log->details,
            ];
        });

        return response()->json($history);
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|max:2048',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
            'user_id' => auth()->id(),
            'submitted_at' => now(),
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('projects', 'public');
        }

        $project = Project::create($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'action' => 'submitted',
            'created_at' => now(),
        ]);

        if ($project) {
            Mail::to($project->user->email)
                ->queue(new ProjectStatusMail($project, 'submitted'));

            return redirect()
                ->route('dashboard')
                ->with('success', 'Project submitted successfully.');
        }

        return redirect()->back()->with('failed', 'Something went wrong.');
    }
}