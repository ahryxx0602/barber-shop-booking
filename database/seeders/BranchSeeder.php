<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name'        => 'BarberBook Quận 1',
                'address'     => '123 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP.HCM',
                'phone'       => '028-1234-5678',
                'description' => 'Chi nhánh trung tâm sang trọng.',
                'is_active'   => true,
                'seed_id'     => 1,
            ],
            [
                'name'        => 'BarberBook Quận 3',
                'address'     => '456 Võ Văn Tần, Phường 5, Quận 3, TP.HCM',
                'phone'       => '028-2345-6789',
                'description' => 'Chi nhánh phong cách vintage ấm cúng.',
                'is_active'   => true,
                'seed_id'     => 2,
            ],
        ];

        foreach ($branches as $branchData) {
            $seedId = $branchData['seed_id'];
            unset($branchData['seed_id']);

            $branch = Branch::firstOrCreate(
                ['name' => $branchData['name']],
                $branchData
            );

            // Use Unsplash source image
            $this->downloadBranchImage($branch, $seedId);
        }
    }

    private function downloadBranchImage(Branch $branch, int $seedId): void
    {
        try {
            // Using a realistic unsplash search URL for barbershop
            $url = "https://source.unsplash.com/800x400/?barbershop,interior,sig={$seedId}";
            
            // As source.unsplash.com is deprecated, we will use images.unsplash.com via an alternative if needed, or fallback.
            // A more reliable random image for testing:
            $url = "https://picsum.photos/seed/barbershop_real_{$seedId}/800/400";
            
            $context = stream_context_create([
                'http' => ['follow_location' => true, 'max_redirects' => 5, 'timeout' => 10],
            ]);

            $imageContent = @file_get_contents($url, false, $context);
            if ($imageContent) {
                $path = 'branches/' . $branch->id . '.jpg';
                Storage::disk('public')->put($path, $imageContent);
                $branch->update(['image' => $path]);
            }
        } catch (\Exception $e) { }
    }
}
