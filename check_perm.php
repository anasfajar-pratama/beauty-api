<?php
require __DIR__ . '/vendor/autoload.php';
\ = require __DIR__ . '/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

\ = App\Models\Role::where('slug', 'super-admin')->first();
if (\) {
    \ = \->permissions()->where('slug', 'view_heroes')->exists();
    echo 'super-admin: ' . (\ ? 'HAS' : 'MISSING') . ' view_heroes' . PHP_EOL;
}
\ = App\Models\Role::where('slug', 'admin')->first();
if (\) {
    \ = \->permissions()->where('slug', 'view_heroes')->exists();
    echo 'admin: ' . (\ ? 'HAS' : 'MISSING') . ' view_heroes' . PHP_EOL;
}
echo 'Permission exists: ' . App\Models\Permission::where('slug', 'view_heroes')->count() . PHP_EOL;
