<?php
namespace blocks\backend\infobase;

class Controller extends \proto\Controller
{
    protected $structure = array();
    protected $info = array();
    protected $isReadOnly = false;
    public function __construct($view, $model)
    {
        $this->view = $view;
        $this->model = $model;
        $this->structure = $model->getStructure();
        $this->info = $model->getInfo();
        restrictAccessLevel($this->info['readAccessLevel']);
        $this->isReadOnly = !checkAccessLevel($this->info['changeAccessLevel']);
    }

    public function actionIndex()
    {
        return $this->view->render();
    }

    public function actionShowTable()
    {
        $this->view->setTemplate('table.tpl');
        return $this->view->render();
    }




    protected function applyUserInput($record)
    {
        if (!isset($this->structure[$record['recordType']])) return $record;
        
        foreach ($this->structure[$record['recordType']]['fields'] as $fieldName => $field) {
            if (isset($_POST[$fieldName])) switch ($field['type']) {
                case 'integer':
                case 'timestamp':
                $record[$fieldName] = intval(trim($_POST[$fieldName]));
                break;

                case 'real':
                $record[$fieldName] = floatval(trim(str_replace(',', '.', $_POST[$fieldName])));
                break;

                case 'string':
                case 'hash':
                $record[$fieldName] = strip_tags($_POST[$fieldName]);
                break;

                case 'code':
                case 'foreignKey':
                case 'color':
                $record[$fieldName] = trim($_POST[$fieldName]);
                break;

                case 'files':
                $record[$fieldName] = implode(',', $_POST[$fieldName]);
                break;

                case 'options':
                $separator = isset($field['separator']) ? $field['separator'] : ',';
                $record[$fieldName] = implode($separator, $_POST[$fieldName]);
                break;
            }

            //автозаполнение зарезервированных полей
            switch ($fieldName) {
                case 'url':
                if (empty($record['url']) && isset($record['name'])) $record['url'] = toUrl($record['name']);
                break;

                case 'created':
                if ($record['id'] == 0) $record['created'] = time();
                break;

                case 'modified':
                $record['modified'] = time();
                break;
            }
        }

        return $record;
    }




    protected function checkRecord($record)
    {
        if (!isset($record['recordType'])) return array('Запись не имеет данных о своем типе.');
        if (!isset($this->structure[$record['recordType']])) return array('Запись имеет неизвестный тип "'.$record['recordType'].'".');
        
        $errors = array();
        if (isset($this->structure[$record['recordType']]['onCheck'])) {
            $handler = explode('::', $this->structure[$record['recordType']]['onCheck']);
            if (count($handler) == 2 && class_exists($handler[0]) && method_exists($handler[0], $handler[1])) {
                $class = $handler[0];
                $method = $handler[1];
                $errors = $class::$method($record);
            }
        }

        foreach ($this->structure[$record['recordType']]['fields'] as $fieldName => $field) {
            if (!isset($field['label'])) $field['label'] = $fieldName;
            if (!isset($field['error'])) $field['error'] = 'Поле "'.$field['label'].'" имеет неверное значение';
            if (!isset($record[$fieldName])) {
                $errors[$fieldName] = 'Отсутствует поле "'.$field['label'].'"';
                break;
            }

            switch ($field['type']) {
                case 'integer':
                case 'real':
                if ((isset($field['required']) && $field['required'] && empty($record[$fieldName])) || 
                    (isset($field['max']) && $record[$fieldName] > $field['max']) ||
                    (isset($field['min']) && $record[$fieldName] < $field['min']))
                $errors[$fieldName] = $field['error'];
                break;

                case 'string':
                case 'hash':
                if ((isset($field['required']) && $field['required'] && empty($record[$fieldName])) || 
                    (isset($field['max']) && mb_strlen($record[$fieldName]) > $field['max']) ||
                    (isset($field['min']) && mb_strlen($record[$fieldName]) < $field['min']) ||
                    (isset($field['pattern']) && !preg_match($field['pattern'], $record[$fieldName])))
                $errors[$fieldName] = $field['error'];
                break;

                case 'code':
                if ((isset($field['required']) && $field['required'] && empty($record[$fieldName])) || 
                    (isset($field['max']) && mb_strlen($record[$fieldName]) > $field['max']) ||
                    (isset($field['min']) && mb_strlen($record[$fieldName]) < $field['min']))
                $errors[$fieldName] = $field['error'];
                break;

                case 'files':
                $files = explode(',', $record[$fieldName]);
                if ((isset($field['required']) && $field['required'] && empty($record[$fieldName])) || 
                    (isset($field['min']) && count($files) < $field['min']) ||
                    (isset($field['max']) && count($files) > $field['max'])) {
                    $errors[$fieldName] = $field['error'];
                    break;
                }
                foreach ($files as $file) {
                    if (isset($field['extensions']) &&
                        !in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $field['extensions']))
                    {
                        $errors[$fieldName] = $field['error'];
                        break;
                    }
                    if (isset($field['maxSize']) &&
                        file_exists(ROOT_DIR.'/uploads/infobase/temp/'.$file) &&
                        filesize(ROOT_DIR.'/uploads/infobase/temp/'.$file) > $field['maxSize'])
                    {
                        $errors[$fieldName] = $field['error'];
                        break;
                    }
                }
                break;

                case 'timestamp':
                if ((isset($field['required']) && $field['required'] && empty($record[$fieldName])) || 
                    (isset($field['relMax']) && $record[$fieldName] > time() + $field['relMax']) ||
                    (isset($field['relMin']) && $record[$fieldName] < time() - $field['relMin']) ||
                    (isset($field['absMax']) && $record[$fieldName] > $field['absMax']) ||
                    (isset($field['absMin']) && $record[$fieldName] < $field['absMin']))
                $errors[$fieldName] = $field['error'];
                break;

                case 'color':
                if (!preg_match('/^#[0-9A-Fa-f]{3}(?:[0-9A-Fa-f]{3})?$/', $record[$fieldName]))
                $errors[$fieldName] = $field['error'];
                break;

                case 'options':
                if ($field['method'] == 'checkbox') {
                    $separator = isset($field['separator']) ? $field['separator'] : ',';
                    $options = explode($separator, $record[$fieldName]);
                    if ((isset($field['min']) && count($options) < $field['min']) ||
                        (isset($field['max']) && count($options) > $field['max'])) {
                        $errors[$fieldName] = $field['error'];
                    } elseif (isset($field['deprecated'])) {
                        foreach ($field['deprecated'] as $deprecated) {
                            $deprecated = explode($separator, deprecated);
                            if (empty(array_diff($deprecated, $record[$fieldName]))) {
                                $errors[$fieldName] = $field['error'];
                                break;
                            }
                        }
                    }
                } else {
                    if (isset($field['required']) && $field['required'] && empty($record[$fieldName]))
                    $errors[$fieldName] = $field['error'];
                }
                break;

                case 'foreignKey':
                if (empty($record[$fieldName]) && $this->structure[$record['recordType']]['isRoot'] == false) {
                    $errors[$fieldName] = 'Поле "'.$field['label'].'" имеет неверный формат.';
                    break;
                }
                $f = explode('-', $record[$fieldName]);
                if (count($f) != 2) {
                    $errors[$fieldName] = 'Поле "'.$field['label'].'" имеет неверный формат.';
                    break;
                }
                $foreignRecordType = $f[0];
                if (!in_array(foreignRecordType, $field['availableRecordTypes'])) {
                    $errors[$fieldName] = $field['error'];
                }
                break;
            }
            
            //доп. проверка зарезервированных полей
            switch ($fieldName) {
                case 'url':
                if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $record['url'])) {
                    $errors['url'] = 'Поле "'.$field['label'].'" пустое, или имеет неверный формат.';
                }
                break;

                case 'parent':
                if (empty($record['parent'])) {
                    if ($this->structure[$record['recordType']]['isRoot'] == false) {
                        $errors[$fieldName] = 'Поле "'.$field['label'].'" должно быть заполнено.';
                        break;
                    }
                } elseif (!$this->model->checkRecursion($record)) {
                    $errors['parent'] = 'Поле "'.$field['label'].'" (ссылка на родительскую запись) порождает рекурсивную вложенность.';
                }
                break;

                case 'published':
                if (!$this->model->сheckPublicationDate($record)) {
                    $errors['published'] = 'Поле "'.$field['label'].'" заполнено неверно. Дата публикации записи получается раньше, чем у родительской.';
                }
                break;
            }
        }
        return $errors;
    }
}