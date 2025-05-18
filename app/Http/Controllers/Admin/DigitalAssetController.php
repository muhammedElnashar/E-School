<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Scopes;
use App\Http\Controllers\Controller;
use App\Models\EducationStageSubject;
use App\Models\MarketplaceItem;
use App\Enums\MarketplaceItemType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class DigitalAssetController extends Controller
{
    public function index()
    {
        $digitalAssets = MarketplaceItem::where('type', MarketplaceItemType::DigitalAsset)->paginate(15);
        $educationStageSubjects = EducationStageSubject::with(['educationStage', 'subject'])->get();

        return view('admin.digital_assets.index', compact('digitalAssets', 'educationStageSubjects'));
    }

    public function create()
    {
        $educationStageSubjects = EducationStageSubject::with(['educationStage', 'subject'])->get();
        return view('admin.digital_assets.create', compact('educationStageSubjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'education_stage_subject_id' => ['required', 'exists:education_stage_subjects,id'],
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv|max:20480',
        ]);

        $filePath = $request->file('file')->store('digital_assets', 'public');
        $validated['type'] = MarketplaceItemType::DigitalAsset->value;
        $validated['file_path'] = $filePath;

        MarketplaceItem::create($validated);

        return redirect()->route('digital-assets.index')->with('success', 'Digital Asset created successfully.');
    }

    public function edit(MarketplaceItem $digitalAsset)
    {
       //
    }

    public function update(Request $request, $id)
    {
        $digitalAsset = MarketplaceItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'education_stage_subject_id' => 'required|exists:education_stage_subjects,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv|max:20480', // 20MB max
        ]);

        $digitalAsset->name = $request->input('name');
        $digitalAsset->price = $request->input('price');
        $digitalAsset->education_stage_subject_id = $request->input('education_stage_subject_id');
        if ($request->hasFile('file')) {
            if ($digitalAsset->file_path && Storage::disk('public')->exists($digitalAsset->file_path)) {
                Storage::disk('public')->delete($digitalAsset->file_path);
            }

          $digitalAsset->file_path = $request->file('file')->store('digital_assets', 'public');
        }

        $digitalAsset->save();

        return redirect()->route('digital-assets.index')->with('success', __('Digital asset updated successfully.'));
    }

    public function destroy($id)
    {
        $digitalAsset = MarketplaceItem::findOrFail($id);
        if ($digitalAsset->type->value !== MarketplaceItemType::DigitalAsset->value) {
            abort(404);
        }

        if ($digitalAsset->file_path) {
            Storage::disk('public')->delete($digitalAsset->file_path);
        }

        $digitalAsset->delete();

        return back()->with('success', 'Digital Asset deleted successfully.');
    }
}
