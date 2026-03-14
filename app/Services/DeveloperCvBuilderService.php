<?php

namespace App\Services;

use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DeveloperCvBuilderService
{
    public function __construct(
        protected string $disk = 's3'
    ) {}

    /**
     * Build CV PDF for the developer and update cv_path.
     * Returns the new path on success, null on failure.
     */
    public function buildAndUpdate(Developer $developer): ?string
    {
        $developer = Developer::withoutGlobalScope(ApprovedScope::class)
            ->with(['jobTitle', 'skills', 'companies' => ['jobTitle'], 'projects'])
            ->find($developer->id);

        if (! $developer) {
            return null;
        }

        try {
            $filename = str($developer->name)->slug()->append('-cv.pdf')->toString();
            $path = "cvs/developer-{$developer->id}/{$filename}";

            $pdfContent = Pdf::loadView('developer-cv', ['developer' => $developer])
                ->setPaper('a4', 'portrait')
                ->output();

            if ($developer->cv_path) {
                Storage::disk($this->disk)->delete($developer->cv_path);
            }

            Storage::disk($this->disk)->put($path, $pdfContent);

            $developer->updateQuietly(['cv_path' => $path]);

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }
}
