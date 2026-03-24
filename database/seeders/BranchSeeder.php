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
                'description' => 'Chi nhánh trung tâm, không gian sang trọng và hiện đại.',
                'is_active'   => true,
                'seed_id'     => 1,
            ],
            [
                'name'        => 'BarberBook Quận 3',
                'address'     => '456 Võ Văn Tần, Phường 5, Quận 3, TP.HCM',
                'phone'       => '028-2345-6789',
                'description' => 'Chi nhánh phong cách vintage, ấm cúng và thân thiện.',
                'is_active'   => true,
                'seed_id'     => 2,
            ],
            [
                'name'        => 'BarberBook Thủ Đức',
                'address'     => '789 Võ Văn Ngân, Phường Linh Chiểu, TP. Thủ Đức, TP.HCM',
                'phone'       => '028-3456-7890',
                'description' => 'Chi nhánh mới, phục vụ khu vực phía Đông thành phố.',
                'is_active'   => true,
                'seed_id'     => 3,
            ],
        ];

        foreach ($branches as $branchData) {
            $seedId = $branchData['seed_id'];
            unset($branchData['seed_id']);

            $branch = Branch::firstOrCreate(
                ['name' => $branchData['name']],
                $branchData
            );

            // Tải ảnh chi nhánh từ picsum (ảnh đẹp random, fixed seed cho mỗi branch)
            $this->downloadBranchImage($branch, $seedId);
        }
    }

    /**
     * Tải ảnh chi nhánh từ picsum.photos
     */
    private function downloadBranchImage(Branch $branch, int $seedId): void
    {
        try {
            // picsum.photos với seed cố định → ảnh giống nhau mỗi lần seed
            $url = "https://picsum.photos/seed/barbershop{$seedId}/800/400";

            $context = stream_context_create([
                'http' => [
                    'follow_location' => true,
                    'max_redirects'   => 5,
                    'timeout'         => 10,
                ],
            ]);

            $imageContent = @file_get_contents($url, false, $context);
            if ($imageContent) {
                $path = 'branches/' . $branch->id . '.jpg';
                Storage::disk('public')->put($path, $imageContent);
                $branch->update(['image' => $path]);
            }
        } catch (\Exception $e) {
            // Bỏ qua lỗi tải ảnh
        }
    }
}
