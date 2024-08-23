<?php

declare(strict_types=1);

namespace Workbench\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:10', 'max:200'],
            'content' => ['required', 'min:10'],
            'short' => ['required', 'size:8'],
        ];
    }
}
