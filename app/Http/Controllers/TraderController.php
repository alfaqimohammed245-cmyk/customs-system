<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trader;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class TraderController extends Controller
{
    public function index()
    {
        $this->checkPermission('مشاهدة التجار');

        $traders = Trader::latest()->paginate(10);
        return view('traders.index', compact('traders'));
    }

    public function create()
    {
        $this->checkPermission(['مشاهدة التجار', 'إضافة']);

        return view('traders.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission(['مشاهدة التجار', 'إضافة']);

        $request->validate([
            'name' => 'required|string|max:255|unique:traders,name',
            'phone' => 'nullable|string|max:50',
        ]);

        $trader = Trader::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'إضافة تاجر',
            'description' => 'تم إضافة تاجر جديد: ' . $trader->name,
        ]);

        return redirect()->route('traders.index')->with('success', 'تم إضافة التاجر بنجاح');
    }

    public function edit(Trader $trader)
    {
        $this->checkPermission(['تعديل', 'إدارة الإعدادات']);

        return view('traders.edit', compact('trader'));
    }

    public function update(Request $request, Trader $trader)
    {
        $this->checkPermission(['تعديل', 'إدارة الإعدادات']);

        $request->validate([
            'name' => 'required|string|max:255|unique:traders,name,' . $trader->id,
            'phone' => 'nullable|string|max:50',
        ]);

        $oldName = $trader->name;
        $trader->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'تعديل تاجر',
            'description' => 'تم تعديل بيانات التاجر من (' . $oldName . ') إلى (' . $trader->name . ')',
        ]);

        return redirect()->route('traders.index')->with('success', 'تم تحديث بيانات التاجر بنجاح');
    }

    public function destroy(Trader $trader)
    {
        $this->checkPermission(['حذف', 'إدارة الإعدادات']);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'حذف تاجر',
            'description' => 'تم حذف التاجر: ' . $trader->name,
        ]);

        $trader->delete();
        return redirect()->route('traders.index')->with('success', 'تم حذف التاجر بنجاح');
    }

    // Removed custom authorizeAdminOrPermission
}
