<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default filesystem disk
    |--------------------------------------------------------------------------
    |
    | Filament's own default is env('FILESYSTEM_DISK', 'local'), and every file
    | upload in the admin inherits it, including the Media Library ones. On a
    | deployment where FILESYSTEM_DISK is 'local' that puts uploaded images
    | outside the web root with no symlink to them, so they save successfully
    | and then never load: the record is right, the file is on disk, and the
    | browser spins forever on an address that serves nothing.
    |
    | Media Library's own default is already 'public'. This makes Filament
    | agree with it instead of quietly overriding it.
    |
    | It is deliberately not tied to FILESYSTEM_DISK. That still governs
    | Livewire's temporary upload disk, which should stay private: a half
    | uploaded, unvalidated file has no business being publicly readable.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

];
