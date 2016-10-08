<?php

namespace Hottab\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class IonicUpgrade extends Angular
{
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

    /**
     * store new version of app
     * @var string
     */
    protected $child;

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
        $this->project = '../' . $projectName;
        $this->child = $this->project . '/' . $projectName;

        // define variable
        $oldAppDir = $this->project . '/app';
        $oldPagesDir = $oldAppDir . '/pages';
        $oldServicesDir = $oldAppDir . '/services';
        $oldAppFile = $oldAppDir . '/app.ts';
        $oldAppHtmlFile = $oldAppDir . '/app.html';
        $oldAppCSSFile = $oldAppDir . '/theme/app.core.scss';
        $oldVariableCSSFile = $oldAppDir . '/theme/app.variables.scss';
        $newSrcDir = $this->child . '/src';
        $newAppDir = $newSrcDir . '/app';
        $newPagesDir = $newSrcDir . '/pages';
        $newServicesDir = $newSrcDir . '/services';
        $appModuleFile = $newAppDir . '/app.module.ts';
        $appComponentFile = $newAppDir . '/app.component.ts';
        $newAppHtmlFile = $newAppDir . '/app.html';
        $newAppCSSFile = $newAppDir . '/app.scss';
        $newVariableCSSFile = $newSrcDir . '/theme/variables.scss';

        $pageClasses = [];
        $importPages = [];

        // clear sample page
        $this->_clearOutput($newPagesDir);

        // copy pages
        $pageDirs = $this->scanDir($oldPagesDir);
        foreach ($pageDirs as $pageDir) {
            $pagePath = $oldPagesDir . '/' . $pageDir;
            $newPagePath = $newPagesDir . '/' . $pageDir;
            $pageFiles = $this->scanDir($pagePath);
            $selector = 'page-' . $pageDir;
            // set input and output dir

            foreach ($pageFiles as $file) {
                $oldFile = $pagePath . '/' . $file;
                $newFile = $newPagePath . '/' . $file;
                if (strpos($file, '.ts') !== false) {
                    // get page class
                    preg_match_all('/export\s*class\s*([a-z,A-Z]*)/', file_get_contents($oldFile), $matches);
                    $pageClasses[] = $matches[1][0];
                    $importPages[] = 'import { ' . $matches[1][0] . '} from \'../pages/' . $pageDir . '/' . $pageDir . '\';';

                    $this->transformFileWithRegex($oldFile, $newFile, [
                        '/(\@Component\(\{).*(\n\}\))/s' => '$1' . PHP_EOL . '  selector: \'' . $selector . '\',' .
                            PHP_EOL . '  templateUrl: \'' . $pageDir . '.html\'$2',
                        '/private/' => 'public',
                        '/img\//' => 'assets/img/'
                    ]);
                } elseif (strpos($file, '.html') !== false) {
                    $this->transformFileWithRegex($oldFile, $newFile, [
                        '/(ion-content\s+class=\"\s*)' . $pageDir . '/' => '$1',
                        '/\s*class=\"\"/' => '',
                        '/(<button)/' => '$1 ion-button ',
                        '/\s(primary|secondary|danger|favorite|gray|green|yellow|dark|light|white|green|organge)(\s|\>)/' => ' color="$1" $2',
//                      '/(<ion-navbar\s*)([a-zA-Z]*)/' => '$1color="$2"'
                    ]);
                } elseif (strpos($file, '.scss') !== false) {
                    $this->transformFileWithRegex($oldFile, $newFile, [
                        '/^(\.' . $pageDir . ')/' => $selector . ',' . PHP_EOL . '$1',
                        '/scroll-content/' => '.scroll-content',
                    ]);
                }
                // copy to new page dir
                // $this->_transformFile($oldPagesDir . '/' . $pageDir . '/' . $file, $newPagesDir . '/' . $pageDir . '/' . $file, []);
            }
        }

        // read old app file
        $oldAppContent = file_get_contents($oldAppFile);
        preg_match_all('/\/\/\s*import\s+services(.*)\/\/\s*import\s+page/s', $oldAppContent, $matches);
        var_dump($matches);
        $importServices = str_replace('./services', '../services', $matches[1][0]);
        preg_match_all('/MyApp,\s*\[([a-z,A-Z\,\s]*)\]/', $oldAppContent, $matches);
        var_dump($matches);
        $serviceNames = str_replace(' ', PHP_EOL . '    ', $matches[1][0]);

        // write module file
        $importPageStr = '$1' . PHP_EOL . PHP_EOL .
            '// import services' . PHP_EOL .
            trim($importServices) . PHP_EOL .
            '// end import services' . PHP_EOL . PHP_EOL .
            '// import pages' . PHP_EOL .
            implode(PHP_EOL, $importPages) . PHP_EOL .
            '// end import pages' . PHP_EOL . PHP_EOL .
            '$2';
        $this->transformFileWithRegex($appModuleFile, $appModuleFile, [
            // import pages and services
            '/(app\.component\';).*(\@NgModule)/s' => $importPageStr,
            // define pages
            '/(MyApp,)[A-Z,a-z\,\n\s]*(],)/' => '$1' . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $pageClasses) . PHP_EOL . '$2',
            // define services
            '/(providers:\s*\[)(\])/s' => '$1' . PHP_EOL . '    ' . $serviceNames . PHP_EOL . '    /* import services */' . PHP_EOL . '$2'
        ]);

        // write component file
        $this->transformFileWithRegex($oldAppFile, $appComponentFile, [
            '/,\s*ionicBootstrap/' => '',
            '/\/\/\s*import\s*services.*(\/\/\s*import\s*page)/s' => '$1',
            '/\.\/page/' => '../page',
            '/private/' => 'public',
            //'/this\.rootPage/' => 'rootPage',
            '/ionicBootstrap\(.*/' => '',
            '/build\/app\.html/' => 'app.html'
        ]);

        // write app.html
        copy($oldAppHtmlFile, $newAppHtmlFile);

        // copy services
        mkdir($newServicesDir);
        $serviceFiles = $this->scanDir($oldServicesDir);
        foreach ($serviceFiles as $service) {
            $this->transformFileWithRegex($oldServicesDir . '/' . $service, $newServicesDir . '/' . $service, [
                '/img\//' => 'assets/img/'
            ]);
        }

        // write css files
        preg_match_all('/\$colors:\s*\(.*\);/s', file_get_contents($oldVariableCSSFile), $matches);
        $this->transformFileWithRegex($newVariableCSSFile, $newVariableCSSFile, [
            '/\$colors:\s*\(.*\);/s' => $matches[0][0]
        ]);
        $this->transformFileWithRegex($oldAppCSSFile, $newAppCSSFile, [
            '/@import[a-zA-Z0-9\.\"\'\-\/\s]*;/' => '',
            '/scroll-content/' => '.scroll-content',
            '/img\//' => 'assets/img/'
        ]);

        echo PHP_EOL . "Almost done; Copy img folder, please.";
    }
}
