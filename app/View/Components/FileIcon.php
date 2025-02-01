<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FileIcon extends Component
{
    public $extension;

    public function __construct($extension)
    {
        $this->extension = strtolower($extension);
    }

    public function render()
    {
        $iconMap = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            '7z' => 'fa-file-archive',
        ];

        $iconClass = $iconMap[$this->extension] ?? 'fa-file';

        return view('components.file-icon', ['iconClass' => $iconClass]);
    }
}

