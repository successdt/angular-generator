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
        //$this->updateAppComponent();
        //$this->updateTemplates();
        //$this->updateIndexFile();
    }

    /**
     * update package.json
     */
    public function updatePackage()
    {
        $updateAvailable = ['dependencies', 'devDependencies'];
        $newContent = json_decode(file_get_contents(storage_path('app/ionic/package.json')), true);
        $oldContent = file_get_contents($this->project . '/package.json');
        
//        $oldContent = str_replace('"ionic-native"', '"@ionic-native/core"', $oldContent);
//        $oldContent = str_replace('    "@angular/platform-server": "2.4.8",',"    \"@ionic-native/splash-screen\": \"3.12.1\",\n    \"@ionic-native/status-bar\": \"3.12.1\",", $oldContent);

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
        $oldContent = str_replace("'@angular/platform-browser';", "'@angular/platform-browser';\nimport {StatusBar} from '@ionic-native/status-bar';\nimport {SplashScreen} from '@ionic-native/splash-screen';", $oldContent);
        $oldContent = str_replace("providers: [", "providers: [\n    StatusBar,\n    SplashScreen,", $oldContent);
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

    public function updateIndexFile() {
        $filePath = $this->project . '/src/index.html';
        $oldContent = file_get_contents($filePath);
        $oldContent = str_replace('<!-- cordova.js required for cordova apps -->', "", $oldContent);
        $oldContent = str_replace('<script src="cordova.js"></script>', "", $oldContent);
        $oldContent = str_replace('<script src="build/polyfills.js"></script>', "<script src=\"build/polyfills.js\"></script>\n\n  <!-- The vendor js is generated during the build process\n       It contains all of the dependencies in node_modules -->\n  <script src=\"build/vendor.js\"></script>", $oldContent);
        $oldContent = str_replace('<meta name="theme-color" content="#4e8ef7">', "<meta name=\"theme-color\" content=\"#4e8ef7\">\n\n  <!-- cordova.js required for cordova apps -->\n  <script src=\"cordova.js\"></script>", $oldContent);
        file_put_contents($filePath, $oldContent);

        echo "Finished updating index.html" . PHP_EOL;
    }

    public function updateAppComponent()
    {
        $filePath = $this->project . '/src/app/app.component.ts';
        $oldContent = file_get_contents($filePath);
        $oldContent = str_replace("import {StatusBar} from 'ionic-native';", "import {StatusBar} from '@ionic-native/status-bar';\nimport {SplashScreen} from '@ionic-native/splash-screen';", $oldContent);
        $oldContent = str_replace("platform: Platform", "platform: Platform, statusBar: StatusBar, splashScreen: SplashScreen", $oldContent);
        $oldContent = str_replace("public platform: Platform", "platform: Platform", $oldContent);
        $oldContent = str_replace("StatusBar.styleDefault();", "statusBar.styleDefault();\n      splashScreen.hide();", $oldContent);
        file_put_contents($filePath, $oldContent);

        echo "Finished updating app.module.ts" . PHP_EOL;
    }
}
