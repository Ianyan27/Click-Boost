<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
    $path = app_path("Services/{$name}.php");

    if (file_exists($path)) {
        $this->error('Service already exists!');
        return;
    }

    if (!file_exists(app_path('Services'))) {
        mkdir(app_path('Services'), 0755, true);
    }

    file_put_contents($path, <<<PHP
<?php

namespace App\Services;

class {$name}
{
    //
}
PHP
    );

    $this->info("Service {$name} created successfully.");
    }
}
