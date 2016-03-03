<?php

namespace Hottab\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class AngularResource extends Angular
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'angular:resource {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example: php artisan angular:resource ql-dao-tao.ts-thac-si.hoc_phan';

	public function __construct() {
		parent::__construct();
	}


    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $fullState = $this->argument('name');
		$states = explode('.', $fullState);
	    $length = count($states);
	    $state = $states[$length - 1];
	    $controllerName = $this->_snakeToCamel(str_replace('-', '_', $states[$length - 2]))
		    . $this->_snakeToCamel($states[$length - 1]);

	    // clean output
	    $this->_clearOutput($this->outPut);

	    // index
	    $indexControllerName = $controllerName;
	    $indexCtrlOutput = 'js/controllers/' . implode('/', $states);
	    $indexTplOutput = 'tpl/' . implode('/', $states) . '.html';


	    // item
	    $itemTplOutput = 'tpl/' . implode('/', $states) . '_create.html';
	    $viewState = '';
	    for($i = 0; $i < $length - 1; $i++) {
		    $viewState .= $states[$i] . '.';
	    }
	    $createState = $viewState . 'tao_moi_' . $states[$length - 1];
	    $viewState .= 'xem_' . $states[$length - 1];

	    // process router
	    $url = str_replace('_', '-', $state);
	    $this->_transformFile('router.js', 'router.js', compact('fullState', 'indexControllerName', 'indexTplOutput',
		    'state', 'itemTplOutput', 'createState', 'viewState', 'url'));

	    // process permissions file
	    $this->_transformFile('permissions.js', 'permissions.js', compact('state'));

	    // process service file
	    $constant = $this->ask('Enter the constant for service');
	    $this->_transformFile('service.js', 'js/services/' . $states[0] . '/' . $controllerName . 'Service.js', compact('controllerName', 'constant'));

	    // process controller
	    $permissionLink = implode("', '", $states);
	    $indexControllerInput = 'list/controller.js';
	    $itemControllerInput = 'item/controller.js';
	    $usePaginate = $this->confirm('Use paginate?');
	    if ($usePaginate) {
			$indexControllerInput = 'list/controller.paginate.js';
	    }

	    $this->_transformFile($indexControllerInput, $indexCtrlOutput . '.js', compact('indexControllerName', 'permissionLink'));
	    $this->_transformFile($itemControllerInput, $indexCtrlOutput . '_create.js', compact('indexControllerName', 'permissionLink'));


	    // process template
		$fields = $this->ask('Enter list of fields for index (separated by comma)');
	    $fields = explode(',', $fields);
	    $listColumnLength = count($fields) + 2;
	    $listHeaderColumn = '';
	    $listColumns = '';
	    foreach($fields as $field) {
			$listHeaderColumn .= "
				<th>
					<span>$field</span>
				</th>";
		    $listColumns .= "
				<td>
					<span>{{ item.$field }}</span>
				</td>";
	    }

	    $indexTplInput = 'list/tpl.html';
		if ($usePaginate) {
			$indexTplInput = 'list/tpl.paginate.html';
		}
        $this->_transformFile($indexTplInput, $indexTplOutput, compact('listHeaderColumn', 'listColumns', 'listColumnLength', 'viewState'));


	    // create item form
	    $fields = $this->ask('Enter list of fields for creating item (separated by comma)');
	    $fields = explode(',', $fields);
	    $itemFields = '';
	    foreach($fields as $field) {
		    $itemFields .= '
		       <div class="form-group">
		          <label class="col-lg-2 control-label"></label>
		          <div class="col-lg-3">
		            <input type="text" class="form-control" name="' . $field . '" ng-model="data.item.' . $field . '" ng-disabled="data.isDisabled">
		          </div>
		        </div>' . PHP_EOL;
	    }
	    $this->_transformFile('item/tpl.html', $itemTplOutput, compact('itemFields'));

		$this->info('Don\'t forget to add controller, service files to the index');
    }
}
