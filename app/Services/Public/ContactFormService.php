<?php

namespace App\Services\Public;

use App\Mail\ContactFormSubmitted;
use Illuminate\Support\Facades\Mail;

class ContactFormService
{
    /**
     * @param  array{name: string, email: string, company: ?string, message: string}  $data
     */
    public function send(array $data): void
    {
        Mail::to(config('public.contact.to'))
            ->send(new ContactFormSubmitted($data));
    }
}
