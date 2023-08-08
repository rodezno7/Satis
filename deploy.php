<?php
namespace Deployer;

require 'recipe/laravel.php';

/** Number of releases kept on the server */
set('keep_releases', 5);

// Config git
set('repository', 'git@github.com:rodezno7/Satis.git');

add('shared_files', []);
add('shared_dirs',
    [
        'public/uploads/business_logos',
        'public/uploads/documents',
        'public/uploads/img',
        'public/uploads/slides',
        'public/uploads/employee_photo',
        'public/uploads/employee_documents',
        'public/uploads/employee_contracts',
        'public/uploads/employee_personnel_actions',
    ]);
add('writable_dirs', []);

// Hosts
import('inventory.yaml');

/** Override vendors task */
task('deploy:vendors', function () {
    cd('{{release_or_current_path}}');
    run('unzip vendor.zip');
    run('composer dump-autoload');
});

/** Override artisan config:cache task */
task('artisan:config:cache', function () {
    /** Copy .env file */
    cd('{{deploy_path}}');
    run('cp ../.env shared/');
    
    artisan('config:cache');
});

/** Migrations */
task('artisan:migrate', function () {
    cd('{{release_or_current_path}}');

    // desc('Migrating Optics tables');
    // $log = run('php artisan migrate --force --path=database/optics_migrations');
    // info($log);

    desc('Migrating RRHH tables');
    $log = run('php artisan migrate --force --path=database/rrhh');
    info($log);

    desc('Migrating');
    $log = run('php artisan migrate --force');
    info($log);
});

/** Disabled artisan route:cache task */
task('artisan:route:cache')
    ->disable();

// Hooks
after('deploy:failed', 'deploy:unlock');