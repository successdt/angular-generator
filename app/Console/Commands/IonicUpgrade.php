<?php

namespace Hottab\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Storage;

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
        //$this->updateAppModule();
        //$this->updateTemplates();
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

    public function updateAppModule()
    {
        $filePath = $this->project . '/src/app/app.module.ts';
        $oldContent = file_get_contents($filePath);
        $oldContent = str_replace("'./app.component';", "'./app.component';\nimport { BrowserModule } from '@angular/platform-browser';", $oldContent);
        $oldContent = str_replace("IonicModule.forRoot(MyApp)", "BrowserModule,\n    IonicModule.forRoot(MyApp)", $oldContent);
        file_put_contents($filePath, $oldContent);

        echo "Finished updating app.module.ts" . PHP_EOL;
    }

    public function updateTemplates()
    {
        $path = $this->project . '/src/pages/';
        $pages = scandir($path);

        foreach ($pages as $page) {
            if ($page != '.' && $page != '..') {
                $file = $path . $page . '/' . $page . '.html';
                $content = file_get_contents($file);
                // add ion-text to h1->h6, span, p
                $content = preg_replace('/(<(h[\d]|span|p|i|a)\s+[^>]*)(color)/i', '$1ion-text $3', $content);
                // update grid
                $content = str_replace('width-50', 'col-6', $content);
                $content = str_replace('width-33', 'col-4', $content);

                file_put_contents($file, $content);
            }
        }

        echo "Finished updating templates" . PHP_EOL;
    }
}
