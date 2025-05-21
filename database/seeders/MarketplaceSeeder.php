<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\EducationStage;
use App\Models\MarketplaceItem;
use App\Enums\Scopes;
use App\Enums\MarketplaceItemType;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $stages = ['Primary', 'Middle', 'High School'];
        foreach ($stages as $stageName) {
            EducationStage::firstOrCreate(['name' => $stageName]);
        }

        // 2. إنشاء المواد الدراسية بصورة واحدة لكل مادة
        $commonImage = 'assets/admin.jpg';
        $subjects = ['Math', 'Science', 'English', 'History'];
        foreach ($subjects as $subjectName) {
            Subject::firstOrCreate(
                ['name' => $subjectName],
                ['image' => $commonImage]
            );
        }

        // 3. إنشاء عناصر Marketplace لكل مادة وكل مرحلة
        $commonFile = 'Eschool_ERD.pdf'; // تأكد من وجوده في public/files/assets/
        foreach (EducationStage::all() as $stage) {
            foreach (Subject::all() as $subject) {
                // باقة
                MarketplaceItem::create([
                    'education_stage_id' => $stage->id,
                    'subject_id' => $subject->id,
                    'name' => 'Starter Package for ' . $subject->name,
                    'description' => 'Basic package for ' . $subject->name,
                    'type' => MarketplaceItemType::Package->value,
                    'package_scope' => Scopes::Individual->value,
                    'price' => 49.99,
                    'lecture_credits' => 10,
                    'file_path' => null,
                ]);

                // مادة رقمية بملف مشترك
                MarketplaceItem::create([
                    'education_stage_id' => $stage->id,
                    'subject_id' => $subject->id,
                    'name' => 'PDF Guide for ' . $subject->name,
                    'description' => 'Downloadable PDF for ' . $subject->name,
                    'type' => MarketplaceItemType::DigitalAsset->value,
                    'package_scope' => null,
                    'price' => 9.99,
                    'lecture_credits' => 0,
                    'file_path' => $commonFile,
                ]);
            }
        }
    }
}
