<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\EducationStage;
use App\Models\EducationStageSubject;
use App\Models\MarketplaceItem;
use App\Enums\Scopes;
use App\Enums\MarketplaceItemType;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء المراحل الدراسية
        $stages = [
            'Primary',
            'Middle',
            'High School',
        ];

        foreach ($stages as $stageName) {
            EducationStage::firstOrCreate(['name' => $stageName]);
        }

        // إنشاء المواد الدراسية
        $subjects = [
            'Math',
            'Science',
            'English',
            'History',
        ];

        foreach ($subjects as $subjectName) {
            Subject::firstOrCreate(['name' => $subjectName]);
        }

        // ربط المراحل الدراسية بالمواد
        $stageSubjects = [];

        foreach (EducationStage::all() as $stage) {
            foreach (Subject::all() as $subject) {
                $ess = EducationStageSubject::firstOrCreate([
                    'education_stage_id' => $stage->id,
                    'subject_id' => $subject->id,
                ]);
                $stageSubjects[] = $ess;
            }
        }

        // إنشاء عناصر Marketplace (باقات ومواد رقمية)
        foreach ($stageSubjects as $ess) {
            // باقة
            MarketplaceItem::create([
                'education_stage_subject_id' => $ess->id,
                'name' => 'Starter Package for ' . $ess->subject->name,
                'description' => 'Basic package for ' . $ess->subject->name,
                'type' => MarketplaceItemType::Package->value,
                'package_scope' => Scopes::Individual->value,
                'price' => 49.99,
                'lecture_credits' => 10,
                'file_path' => null,
            ]);

            // مادة رقمية
            MarketplaceItem::create([
                'education_stage_subject_id' => $ess->id,
                'name' => 'PDF Guide for ' . $ess->subject->name,
                'description' => 'Downloadable PDF for ' . $ess->subject->name,
                'type' => MarketplaceItemType::DigitalAsset->value,
                'package_scope' => null,
                'price' => 9.99,
                'lecture_credits' => 0,
                'file_path' => 'files/pdf_guide_example.pdf',
            ]);
        }
    }
}
