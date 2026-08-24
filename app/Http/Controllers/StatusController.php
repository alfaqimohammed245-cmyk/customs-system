<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('مشاهدة الحالات');
        $statuses = Status::latest()->paginate(10);
        return view('statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission(['إضافة', 'إدارة الإعدادات']);
        return view('statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission(['إضافة', 'إدارة الإعدادات']);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $status = null;

        DB::transaction(function () use ($request, &$status) {
            // حفظ الحالة الجديدة
            $status = Status::create([
                'name' => $request->name,
                'type' => 'default', 
            ]);

            // توثيق الإضافة في سجل العمليات
            DB::table('audit_logs')->insert([
                'user_id'     => Auth::id() ?? 1,
                'action'      => 'إضافة سجل جديد',
                'description' => 'تم إضافة حالة جديدة: ' . $status->name,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return redirect()->route('statuses.index')->with('success', 'تم إضافة الحالة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission(['تعديل', 'إدارة الإعدادات']);
        $status = Status::findOrFail($id);
        return view('statuses.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission(['تعديل', 'إدارة الإعدادات']);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $status = Status::findOrFail($id);

        DB::transaction(function () use ($request, $status) {
            $status->update([
                'name' => $request->name,
            ]);

            // توثيق التعديل في سجل العمليات
            DB::table('audit_logs')->insert([
                'user_id'     => Auth::id() ?? 1,
                'action'      => 'تعديل سجل',
                'description' => 'تم تحديث الحالة: ' . $status->name,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return redirect()->route('statuses.index')->with('success', 'تم تعديل الحالة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission(['حذف', 'إدارة الإعدادات']);
        $status = Status::findOrFail($id);
        $statusName = $status->name;

        DB::transaction(function () use ($status, $statusName) {
            $status->delete();

            // توثيق الحذف في سجل العمليات
            DB::table('audit_logs')->insert([
                'user_id'     => Auth::id() ?? 1,
                'action'      => 'حذف سجل',
                'description' => 'تم حذف الحالة: ' . $statusName,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return redirect()->route('statuses.index')->with('success', 'تم حذف الحالة بنجاح');
    }
}