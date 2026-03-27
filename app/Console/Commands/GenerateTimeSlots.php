<?php

namespace App\Console\Commands;

use App\Models\Barber;
use App\Services\Barber\TimeSlotService;
use Illuminate\Console\Command;

class GenerateTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slots:generate
                            {--days=7 : Số ngày tới cần generate slots}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate time slots cho tất cả barber active trong N ngày tới (mặc định 7 ngày)';

    /**
     * Execute the console command.
     */
    public function handle(TimeSlotService $timeSlotService): int
    {
        $days = (int) $this->option('days');
        $barbers = Barber::where('is_active', true)->get();

        if ($barbers->isEmpty()) {
            $this->warn('Không có barber nào đang active.');
            return self::SUCCESS;
        }

        $this->info("Đang generate time slots cho {$barbers->count()} barber, {$days} ngày tới...");
        $this->newLine();

        $bar = $this->output->createProgressBar($barbers->count());
        $bar->start();

        foreach ($barbers as $barber) {
            $timeSlotService->generateForBarberRange($barber->id, $days);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('✅ Time slots generated successfully!');
        $this->table(
            ['Thông tin', 'Giá trị'],
            [
                ['Số barber', $barbers->count()],
                ['Số ngày', $days],
                ['Từ ngày', now()->format('d/m/Y')],
                ['Đến ngày', now()->addDays($days - 1)->format('d/m/Y')],
            ]
        );

        return self::SUCCESS;
    }
}

