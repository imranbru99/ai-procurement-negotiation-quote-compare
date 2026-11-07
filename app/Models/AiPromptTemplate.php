<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPromptTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'system_prompt',
        'user_prompt_template',
        'output_schema',
    ];

    protected function casts(): array
    {
        return [
            'output_schema' => 'array',
        ];
    }

    public function interpolateUserPrompt(array $vars): string
    {
        $template = $this->user_prompt_template;

        foreach ($vars as $key => $value) {
            $template = str_replace('{'.$key.'}', (string) $value, $template);
        }

        return $template;
    }
}
