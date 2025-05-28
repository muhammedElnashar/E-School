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

        $subjects = ['Math', 'Science', 'English', 'History'];
        foreach ($subjects as $subjectName) {
            Subject::firstOrCreate(
                ['name' => $subjectName],
            );
        }

    }
}
