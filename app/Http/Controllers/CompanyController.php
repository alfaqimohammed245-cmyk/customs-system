<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        $this->checkPermission('مشاهدة الشركات');

        $companies = Company::latest()->paginate(10);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission(['مشاهدة الشركات', 'إضافة']);

        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
        ]);

        $company = Company::create([
            'name' => $request->name,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'إضافة شركة',
            'description' => 'تم إضافة شركة شحن جديدة: ' . $company->name,
        ]);

        return redirect()->route('companies.index')->with('success', 'تم إضافة الشركة بنجاح');
    }

    public function edit(Company $company)
    {
        $this->checkPermission(['تعديل', 'إدارة الإعدادات']);

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $this->checkPermission(['تعديل', 'إدارة الإعدادات']);

        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
        ]);

        $oldName = $company->name;
        $company->update([
            'name' => $request->name,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'تعديل شركة',
            'description' => 'تم تعديل اسم الشركة من (' . $oldName . ') إلى (' . $company->name . ')',
        ]);

        return redirect()->route('companies.index')->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }

    public function destroy(Company $company)
    {
        $this->checkPermission(['حذف', 'إدارة الإعدادات']);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'حذف شركة',
            'description' => 'تم حذف الشركة: ' . $company->name,
        ]);

        $company->delete();
        return redirect()->route('companies.index')->with('success', 'تم حذف الشركة بنجاح');
    }

    // Removed custom authorizeAdminOrPermission
}
