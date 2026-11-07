<?php

namespace App\Filament\App\Resources\Comments\Pages;

use App\Filament\App\Resources\Comments\CommentResource;
use Filament\Resources\Pages\ListRecords;

class ManageComments extends ListRecords
{
    protected static string $resource = CommentResource::class;
}
