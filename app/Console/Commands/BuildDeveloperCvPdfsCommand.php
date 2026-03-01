<?php

namespace App\Console\Commands;

use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BuildDeveloperCvPdfsCommand extends Command
{
    protected $signature = 'developers:build-cv-pdfs';

    protected $description = 'Build CV PDFs and upload them to storage for developers that do not have a cv_path.';

    public function handle(): int
    {
        $developers = Developer::withoutGlobalScope(ApprovedScope::class)
            ->whereNull('cv_path')
            ->orWhere('cv_path', '')
            ->with([
                'jobTitle',
                'skills',
                'companies' => ['jobTitle'],
                'projects',
            ])
            ->get();

        if ($developers->isEmpty()) {
            $this->info('No developers without a CV path.');

            return self::SUCCESS;
        }

        $disk = 's3';
        $bar = $this->output->createProgressBar($developers->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        /** @var Developer $developer */
        foreach ($developers as $developer) {
            try {
                $filename = str($developer->name)->slug()->append('-cv.pdf')->toString();
                $path = "cvs/developer-{$developer->id}/{$filename}";

                $pdfContent = Pdf::loadView('developer-cv', ['developer' => $developer])
                    ->setPaper('a4', 'portrait')
                    ->output();

                Storage::disk($disk)->put($path, $pdfContent);
                $developer->update(['cv_path' => $path]);

                $success++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Developer {$developer->id} ({$developer->name}): {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info(sprintf('Done. Built and uploaded %d CV(s).', $success));
        if ($failed > 0) {
            $this->warn(sprintf('%d developer(s) failed.', $failed));
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
