<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use AuthorizesRequests;
     public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

    $data = [
        'title' => $validated['title'],
        'description' => $validated['description'],
        'status' => 'pending',
        'user_id' => auth()->id(),
        'submitted_at' => now(),
    ];

    if ($request->hasFile('file')) {
        $data['file_path'] = $request->file('file')
            ->store('projects', 'public');
    }

    $project = Project::create($data);

    return new ProjectResource($project);
}
    public function approve(Request $request, $id)
{
    $adminId = Auth::id(); // Currently logged-in admin ID
    $remarks = $request->input('remarks', 'Approved by Admin'); // Remarks / Comment

    // 1 parameter ke bajaye 3 parameters pass karein:
    DB::statement('CALL sp_approve_project(?, ?, ?)', [
        $id,        // Parameter 1: Project ID
        $adminId,   // Parameter 2: Admin/User ID
        $remarks    // Parameter 3: Remarks or Action Note
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Project successfully approved.'
    ]);
}
}
