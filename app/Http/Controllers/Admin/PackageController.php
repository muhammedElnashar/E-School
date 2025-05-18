<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Scopes;
use App\Http\Controllers\Controller;
use App\Models\EducationStageSubject;
use App\Models\MarketplaceItem;
use App\Enums\MarketplaceItemType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index()
    {
        $packages = MarketplaceItem::where('type', MarketplaceItemType::Package->value)->paginate(5);
        $educationStageSubjects = EducationStageSubject::with(['educationStage', 'subject'])->get();
        return view('admin.package.index', compact('packages', 'educationStageSubjects'));
    }

    public function create()
    {
        $educationStageSubjects = EducationStageSubject::with(['educationStage', 'subject'])->get();

        return view('admin.package.create', compact('educationStageSubjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'education_stage_subject_id' => [
                'required',
                'exists:education_stage_subjects,id',
                Rule::unique('marketplace_items')->where(fn($query) => $query->where('type', MarketplaceItemType::Package->value)
                ),
            ], 'name' => 'required|string',
            'price' => 'required|numeric',
            'lecture_credits' => 'required|integer',
            'description' => 'nullable|string',
            'package_scope' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],
        ]);
        $validated['type'] = MarketplaceItemType::Package->value;
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
            'education_stage_subject_id' => [
                'required',
                'exists:education_stage_subjects,id',
                Rule::unique('marketplace_items', 'education_stage_subject_id')
                    ->where(fn ($query) =>
                    $query->where('type', MarketplaceItemType::Package->value)
                    )
                    ->ignore($package->id),
            ],
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'lecture_credits' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'package_scope' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],
        ]);
        $validated['type'] = MarketplaceItemType::Package->value;

        $package->update($validated);

        return redirect()->back()->with('success', __('Package updated successfully.'));
    }
    public function destroy($id)
    {
        MarketplaceItem::findOrFail($id)->delete();
        return back()->with('success', 'Deleted successfully');
    }
}
