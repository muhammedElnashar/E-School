<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Scopes;
use App\Http\Controllers\Controller;
use App\Models\EducationStage;
use App\Models\MarketplaceItem;
use App\Enums\MarketplaceItemType;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index()
    {
        $packages = MarketplaceItem::where('type', MarketplaceItemType::Package->value)
            ->with(['subject', 'educationStage'])->latest()
            ->paginate(5);
        $subjects = Subject::all();
        $educationStages = EducationStage::all();

        return view('admin.package.index', compact('packages', 'subjects', 'educationStages'));
    }



    public function create()
    {
        $subjects = Subject::all();
        $educationStages = EducationStage::all();

        return view('admin.package.create', compact('subjects', 'educationStages'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'package_scope' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],
            'name' => 'required|string',
            'price' => 'required|numeric',
            'lecture_credits' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $validated['type'] = MarketplaceItemType::Package->value;

        $exists = MarketplaceItem::where('type', $validated['type'])
            ->where('subject_id', $validated['subject_id'])
            ->where('package_scope', $validated['package_scope']);

        if (is_null($validated['education_stage_id'])) {
            $exists->whereNull('education_stage_id');
        } else {
            $exists->where('education_stage_id', $validated['education_stage_id']);
        }

        if ($exists->exists()) {
            return back()->withErrors(['duplicate' => 'A package with the same subject, education stage, and scope already exists.'])->withInput();
        }

        MarketplaceItem::create($validated);

        return redirect()->route('package.index')->with('success', 'Package created successfully');
    }

    public function edit($id)
    {
       //
    }

    public function update(Request $request, MarketplaceItem $package)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'package_scope' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],
            'name' => 'required|string',
            'price' => 'required|numeric',
            'lecture_credits' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $validated['type'] = MarketplaceItemType::Package->value;

        $exists = MarketplaceItem::where('type', $validated['type'])
            ->where('subject_id', $validated['subject_id'])
            ->where('package_scope', $validated['package_scope'])
            ->where('id', '!=', $package->id);

        if (is_null($validated['education_stage_id'])) {
            $exists->whereNull('education_stage_id');
        } else {
            $exists->where('education_stage_id', $validated['education_stage_id']);
        }

        if ($exists->exists()) {
            return back()->withErrors(['duplicate' => 'A package with the same subject, education stage, and scope already exists.'])->withInput();
        }


        $package->update($validated);

        return redirect()->route('package.index')->with('success', 'Package updated successfully');
    }
    public function destroy($id)
    {
        MarketplaceItem::findOrFail($id)->delete();
        return back()->with('success', 'Deleted successfully');
    }
}
