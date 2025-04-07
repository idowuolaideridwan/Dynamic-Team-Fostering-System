<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeInterface extends Command
{
    protected $signature = 'make:interface {name}';
protected $description = 'Create a new interface';

public function handle()
{
    $name = $this->argument('name');
    // Convert backslashes to forward slashes for consistency
    $name = str_replace('\\', '/', $name);
    // Prepare the file path
    $path = app_path('Interfaces/' . $name . '.php');
    
    // Check and create the directory if it doesn't exist
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);  // true for recursive creation
    }

    // Check if the interface already exists
    if (file_exists($path)) {
        $this->error('Interface already exists!');
        return;
    }

    // Prepare the interface content
    $namespace = 'App\Interfaces';
    if (strpos($name, '/') !== false) {
        $namespace .= '\\' . str_replace('/', '\\', dirname($name));
    }
    $className = basename($name);
    
    $content = "<?php\n\nnamespace $namespace;\n\ninterface $className\n{\n    // Methods\n}\n";

    // Write the file
    file_put_contents($path, $content);
    $this->info('Interface created successfully.');
}


}
