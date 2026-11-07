<?php

namespace App\Models;

use ImranDev\UniversalSlug\Attributes\Slug;

#[Slug(from: 'name')]
class Supplier extends \App\Modules\suppliers\Models\Supplier {}
