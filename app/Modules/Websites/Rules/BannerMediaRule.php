<?php

namespace App\Modules\Websites\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class BannerMediaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png'];

        // Support for single or multiple uploads
        $files = is_array($value) ? $value : [$value];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                if (! in_array($file->getMimeType(), $allowedMimeTypes)) {
                    $fail('The :attribute must be a valid JPG or PNG image (BR06).');

                    return;
                }
            }
        }
    }
}
