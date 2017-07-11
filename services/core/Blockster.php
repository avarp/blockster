<?php
namespace services\core;

class Blockster
{
  	private static $instance;
	private function __clone() {}
    private function __wakeup() {}
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function resetOb()
    {
        for ($i=ob_get_level(); $i>0; $i--) ob_get_clean();
    }

	protected $eventor;
    protected $preparedBlocks;
    private function __construct()
	{
        $this->resetOb();
        $this->preparedBlocks = json_decode(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'preparedBlocks.json'), true);
        $this->eventor = new \services\eventor\Eventor('blockEvents.json');
        $this->eventor->fire('onBlocksterStart');
	}

	protected $loadedModels = array();
    protected $controllerStack = array();

	public function loadBlock($blockName, $params=array(), $template='')
    {
        $funcArgs = compact('blockName', 'params', 'template');
        $this->eventor->fire('onLoadBlock', $funcArgs);
        extract($funcArgs);

        $a = explode('::', $blockName);
        $blockDir = DIRECTORY_SEPARATOR.'blocks'.DIRECTORY_SEPARATOR.$a[0];
        $cacheDir = DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$a[0];
        $action = isset($a[1])? $a[1] : 'default';
        $action = 'action_'.$action;
        $cachedAction = 'cachedAction_'.$action;
        $namespace = str_replace('/', '\\', $blockDir);
        $controllerClass = $namespace.'\\Controller';

        //test on fatal errors
        if (!class_exists($controllerClass)) {
            trigger_error('The Controller class of block "'.$blockName.'" is not defined ' , E_USER_WARNING);
            return false;
        }

        if (!method_exists($controllerClass, $action)) {
            trigger_error('The '.$action.' method is not defined in Controller class of block "'.$blockName.'"' , E_USER_WARNING);
            return false;
        }

        //get info about cache for this block
        if (class_exists($controllerClass) && method_exists($controllerClass, $cachedAction)) {
            $cache = $controllerClass::$cachedAction($params, $template);
            if (is_array($cache)) $cache['file'] = ROOT_DIR.$cacheDir.DIRECTORY_SEPARATOR.$cache['file'];
        } else {
            $cache = false;
        }
        if (!$cache) {
            $cache = array(
                'file' => '',
                'lifetime' => 0,
                'use' => false,
                'invalidate' => false,
                'update' => false
            );
        }
        
        //read cache file
        if ($cache['use'] && file_exists($cache['file'])) {
            if (time() + $cache['lifetime'] > filectime($cache['file'])) {
                unlink($cache['file']);
            } else {
                $output = file_get_contents($cache['file']);
            }
        }

        //delete cache file
        if ($cache['invalidate']) {
            if (file_exists($cache['file'])) unlink($cache['file']);
        }

        //execute module
        if (!isset($output)) {
            $viewClass = $namespace.'\\View';
            if (!class_exists($viewClass)) $viewClass = '\\blocks\\View';
            $view = new $viewClass($blockDir.'/templates');
            if (!empty($template)) $view->setTemplate($template);
            
            $modelClass = $namespace.'\\Model';
            if (isset($this->loadedModels[$modelClass])) {
                $model = $this->loadedModels[$modelClass];
            } elseif (class_exists($modelClass)) {
                $model = $this->loadedModels[$modelClass] = new $modelClass();
            } else {
                $model = null;
            }

            $parentController = empty($this->controllerStack) ? null : end($this->controllerStack);
            $rootController = empty($this->controllerStack) ? null : $this->controllerStack[0];
            $controller = $this->controllerStack[] = new $controllerClass($view, $model, $parentController, $rootController);
            $output = $controller->$action($params);

            $view = null;
            $controller = null;
            array_pop($this->controllerStack);
        }

        $this->eventor->fire('onBlockReturnOutput', $output);

        //write cache
        if ($cache['update']) {
            if (!is_dir(pathinfo($cache['file'], PATHINFO_DIRNAME))) {
                mkdir(pathinfo($cache['file'], PATHINFO_DIRNAME), 0700, true);
            }
            file_put_contents($cache['file'], $output);
        }

        return $output;
    }

    public function loadPreparedBlock($blockName)
    {
        if (isset($this->preparedBlocks[$blockName])) {
            return $this->loadBlock(
                $this->preparedBlocks[$blockName]['name'],
                $this->preparedBlocks[$blockName]['params'],
                $this->preparedBlocks[$blockName]['template']
            );
        }
    }

    protected $positions = array();
    public function loadPosition($posName)
    {
        if (isset($this->positions[$posName])) {
            $blocks = $this->positions[$posName];
            $output = '';
            foreach ($blocks as $block) {
                if (is_array($block)) {
                    $output .= $this->loadBlock(
                        $block['name'],
                        $block['params'],
                        $block['template']
                    );
                } else {
                    $output .= $this->loadPreparedBlock($block);
                }
            }
            return $output;
        }
    }

    public function addPosition($posName, $blocks)
    {
        if (!isset($this->positions[$posName])) {
            $this->positions[$posName] = $blocks;
        } else {
            foreach ($blocks as $block) $this->positions[$posName][] = $block;
        }
    }

    public function addPositions($positions) {
        foreach ($positions as $posName => $blocks) $this->addPosition($posName, $blocks);
    }

    public function resetOutput()
    {
        $this->resetOb();
        $this->controllerStack = array();
    }
}