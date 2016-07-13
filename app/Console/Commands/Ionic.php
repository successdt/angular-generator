<?php

namespace Hottab\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class Ionic extends Angular
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ionic:generate {type} {items} {--reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example: php artisan ionic:generate page Home:User|Message,Login';


    public function __construct()
    {
        parent::__construct();

        $this->tplUrl = base_path('resources/template/ionic/');
        $this->outPut = base_path('resources/ionic-output/');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // generate file type
        $type = $this->argument('type');
        // items
        $items = $this->argument('items');
        // reset option
        $reset = $this->option('reset');

        if ($reset) {
            $this->_clearOutput($this->outPut);
            $this->_transformFile('app.html', 'app.html', []);
            $this->_transformFile('app.ts', 'app.ts', []);
            $this->_transformFile('theme/app.core.scss', 'theme/app.core.scss', []);
        }

        // process list items
        $arrayItems = explode(',', $items);
        foreach ($arrayItems as $item) {
            if ($type == 'page') {
                $this->generatePage($item);
            } elseif ($type == 'service') {
                $this->generateService($item);
            }
        }

        $this->info('Done');
    }

    /**
     * Generate page
     * @param $pageNames
     */
    protected function generatePage($pageNames)
    {
        // explode to get page name and service name
        $services = explode(':', $pageNames);
        // page name is first item of array
        $pageName = $services[0];
        // page file name
        $pageFileName = $this->_convertFileName($pageName);
        unset($services[0]);
        // services text
        $serviceText = '';
        // service vars
        $serviceVars = '';

        foreach ($services as $service) {
            $serviceText .= "import {" . $service . "Service} from '../../services/" . $this->_convertFileName($service) . "-service';" . PHP_EOL;
            $serviceVars .= ", private " . lcfirst($service) . "Service: " . $service . "Service";
        }

        // templates
        $pageController = 'pages/page.ts';
        $pageHtml = 'pages/page.html';
        $pageStyle = 'pages/page.scss';
        $pageDir = 'pages/' . $pageFileName . '/';

        // generate output
        $this->_transformFile($pageController, $pageDir . $pageFileName . '.ts', [
            '{page-name}' => $pageFileName,
            '{PageName}' => $pageName,
            '// import services' => $serviceText,
            '/* define services */' => $serviceVars
        ]);
        $this->_transformFile($pageHtml, $pageDir . $pageFileName . '.html', [
            '{page-name}' => $pageFileName
        ]);
        $this->_transformFile($pageStyle, $pageDir . $pageFileName . '.scss', [
            '{page-name}' => $pageFileName
        ]);
        
        // add to global file
        $tmpTplDir = $this->tplUrl;
        $this->tplUrl = $this->outPut;
        $this->_transformFile('app.ts', 'app.ts', [
            '// end import pages' => "import {{$pageName}Page} from './pages/$pageFileName/$pageFileName';" . PHP_EOL . '// end import pages',
            '// import menu' => "
                {
                  title: '{$pageName}',
                  icon: 'ios-home-outline',
                  count: 0,
                  component: {$pageName}Page
                },
                // import menu
            "
        ]);
        $this->_transformFile('theme/app.core.scss', 'theme/app.core.scss', [
            '// end import pages css' => '@import "../' . $pageDir . $pageFileName . '";' . PHP_EOL . '// end import pages css'
        ]);
        // restore value
        $this->tplUrl = $tmpTplDir;
    }

    protected function generateService($serviceName) {
        $names = explode(':', $serviceName);
        $serviceName = $names[0];
        $serviceNamePlural = isset($names[1]) ? $names[1] : $names[0] . 's';
        $mockVarName = strtoupper($this->_camelToSnake($serviceNamePlural));
        $serviceFileName = $this->_convertFileName($serviceName);
        $serviceFileNamePlural = $this->_convertFileName($serviceNamePlural);

        // generate output
        $this->_transformFile('services/service.ts', 'services/' . $serviceFileName . '-service.ts', [
            '{SERVICE_NAMES}' => $mockVarName,
            '{service-name}' => $serviceFileName,
            '{serviceNames}' => lcfirst($serviceNamePlural),
            '{ServiceName}' => $serviceName
        ]);

        $this->_transformFile('services/mock.ts', 'services/mock-' . $serviceFileNamePlural . '.ts', [
            '{SERVICE_NAMES}' => $mockVarName
        ]);

        // add to global file
        $tmpTplDir = $this->tplUrl;
        $this->tplUrl = $this->outPut;
        $this->_transformFile('app.ts', 'app.ts', [
            '// end import services' => "import {{$serviceName}Service} from './service/{$serviceFileName}-service';" . PHP_EOL . '// end import services',
            '/* import services */' => $serviceName . 'Service, /* import services */'
        ]);
        // restore value
        $this->tplUrl = $tmpTplDir;
    }
}
