<?php

namespace Hottab\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class IonicUpgrade extends Angular
{
    const ROOT_DIR = 'C:\xampp\htdocs';
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ionic:upgrade {project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example: php artisan ionic:upgrade ion_social';

    /**
     * Project name
     * @var string
     */
    protected $project;


    public function __construct()
    {
        parent::__construct();

        $this->tplUrl = '';
        $this->outPut = '';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // handle input
        $projectName = $this->argument('project');
        $this->project = $this::ROOT_DIR . '/' . $projectName;

        $this->updatePackage();
    }

    /**
     * update package.json
     */
    public function updatePackage()
    {
        $updateAvailable = ['dependencies', 'devDependencies'];
        $newContent = json_decode(file_get_contents(storage_path('app/ionic/package.json')), true);
        $oldContent = file_get_contents($this->project . '/package.json');
        $arrOldContent = json_decode($oldContent, true);

        echo "Updating package.json" . PHP_EOL;

        foreach ($updateAvailable as $key) {
            foreach ($newContent[$key] as $package => $version) {
                if (isset($arrOldContent[$key][$package])) {
                    $oldContent = preg_replace('/\"' . preg_quote($package, '/') . '\":\s\\"([^\"]+)\"/', '"' . $package . '": "' . $version . '"', $oldContent);
                }
            }
        }

        file_put_contents($this->project . '/package.json', $oldContent);

        echo "Finished updating package.json" . PHP_EOL;
    }
}
