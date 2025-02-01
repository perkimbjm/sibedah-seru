<?php

if (!function_exists('getFileIconClass')) {
    function getFileIconClass($extension) {

        $iconMap = [
            'pdf' => 'fa-file-pdf icon-file-pdf',
            'doc' => 'fa-file-word icon-file-word',
            'docx' => 'fa-file-word icon-file-word',
            'xls' => 'fa-file-excel icon-file-excel',
            'xlsx' => 'fa-file-excel icon-file-excel',
            'zip' => 'fa-file-archive icon-file-archive',
            'rar' => 'fa-file-archive icon-file-archive',
            '7z' => 'fa-file-archive icon-file-archive',
            'jpg' => 'fa-file-image icon-file-image',
            'jpeg' => 'fa-file-image icon-file-image',
            'png' => 'fa-file-image icon-file-image',
            'gif' => 'fa-file-image icon-file-image',
            'bmp' => 'fa-file-image icon-file-image',
            'webp' => 'fa-file-image icon-file-image',
            'txt' => 'fa-file-alt icon-file-alt',
            'csv' => 'fa-file-alt icon-file-alt',
            'json' => 'fa-file-code icon-file-code',
            'php' => 'fa-file-code icon-file-code',
            'html' => 'fa-file-code icon-file-code',
        ];

        return $iconMap[strtolower($extension)] ?? 'fa-file icon-file';
    }
}
