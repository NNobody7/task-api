<?php

namespace App\Http\Filters;
class NoteFilter extends ApiFilter{
    protected $allowed = [
        'title' => ['li']
    ];
    protected $operatorMap = [
        'li' => 'ilike'
    ];
}