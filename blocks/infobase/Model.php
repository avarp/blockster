<?php
namespace blocks\admin\infobase;

class Model
{
    protected $info = array();
    protected $structure = array();
    protected $dbh;
    public function __construct()
    {
        $this->dbh = new \services\database\Database;
        if (isset($_GET['basename'])) {
            if ($this->info = $this->dbh->row('SELECT * FROM infobases WHERE url=? LIMIT 1', array($_GET['basename']))) {
                $this->structure = json_decode($this->info['structure'], true);
            }
        }
    }

    public function getStructure()
    {
        return $this->structure;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function newRecord($type)
    {
        if (!isset($this->structure[$type])) return array();
        $record = array('id' => 0, 'recordType' => $type);
        foreach ($this->structure[$type]['fields'] as $fieldName => $field) {
            switch ($field['type']) {
                case 'integer':
                case 'hiddenInteger':
                case 'timestamp':
                $record[$fieldName] = isset($field['initial']) ? $field['initial'] : 0;
                break;

                case 'real':
                case 'hiddenReal':
                $record[$fieldName] = isset($field['initial']) ? $field['initial'] : 0.0;
                break;

                case 'string':
                case 'code':
                case 'files':
                case 'options':
                case 'hiddenText':
                $record[$fieldName] = isset($field['initial']) ? $field['initial'] : '';
                break;

                case 'color':
                $record[$fieldName] = isset($field['initial']) ? $field['initial'] : '#000000';
                break;

                case 'foreignKey':
                case 'hash':
                $record[$fieldName] = '';
                break;
            }
        }
        return record;
    }




    public function saveRecord($record)
    {
        if (empty($record) || !isset($this->structure[$record['recordType']])) return false;

        $record = $this->prepareRecordForSave($record);

        if (isset($this->structure[$record['recordType']]['onSave'])) {
            $handler = explode('::', $this->structure[$record['recordType']]['onSave']);
            if (count($handler) == 2 && class_exists($handler[0]) && method_exists($handler[0], $handler[1])) {
                $class = $handler[0];
                $method = $handler[1];
                $record = $class::$method($record);
            }
        }

        $table = $this->info['prefix'].$record['recordType'];
        unset($record['recordType']);
        if ($record['id'] == 0) {
            unset($record['id']);
            $k = array_keys($record);
            $sql = 'INSERT INTO '.$table.' ('.implode(',', $k).') VALUES (:'.implode(',:', $k).')';
            if ($this->dbh->exec($sql, $record)) return $this->dbh->lastInsertId();
        } else {
            $k = array();
            foreach ($record as $fieldName => $value) if ($fieldName != 'id') $k[] = $fieldName.'=:'.$fieldName;
            $sql = 'UPDATE '.$table.' SET '.implode(',', $k)." WHERE id='".$record['id']."'";
            if ($this->dbh->exec($sql, $record)) return $record['id'];
        }
        return 0;
    }




    protected function prepareRecordForSave($record)
    {
        foreach ($this->structure[$record['recordType']]['fields'] as $fieldName => $field) {
            switch ($field['type']) {
                case 'hash':
                if (in_array($field['method'], hash_algos())) {
                    $record[$fieldName] = hash($field['method'], $record[$fieldName]);
                } else {
                    die('INFOBASE FATAL ERROR: Hash algorithm '.$field['method'].' is not supported.');
                }
                break;

                case 'files':
                $filesToSave = explode(',', $record[$fieldName]);
                if ($record['id'] == 0) {
                    $existingFiles = array();
                } else {
                    $table = $this->info['prefix'].$record['recordType'];
                    $existingFiles = $this->dbh->value("SELECT $fieldName FROM $table WHERE id=$record[id]");
                    $existingFiles = explode(',', $existingFiles);
                }
                $filesToDelete = array_diff($existingFiles, $filesToSave);
                foreach ($filesToDelete as $file) {
                    @unlink(ROOT_DIR.'/uploads/infobase/'.$record['recordType'].'/'.$file);
                    if (isset($field['makeThumbnail']))
                    @unlink(ROOT_DIR.'/uploads/infobase/'.$record['recordType'].'/thumbnails/'.$file);
                }
                foreach ($filesToSave as $file) {
                    $dst = ROOT_DIR.'/uploads/infobase/'.$record['recordType'].'/'.$file;
                    if (!file_exists($dst)) {
                        $src = ROOT_DIR.'/uploads/infobase/temp/'.$file;
                        if (isset($field['cropTo']) || isset($field['makeThumbnail'])) {
                            $this->imageHelper = new \services\ImageHelper;
                            if (isset($field['cropTo'])) {
                                $this->imageHelper->resize($src, $dst, $field['cropTo'][0], $field['cropTo'][1]);
                            }
                            if (isset($field['makeThumbnail']) && $field['makeThumbnail'][0]*$field['makeThumbnail'][1] > 0) {
                                $dstThumb = ROOT_DIR.'/uploads/infobase/'.$record['recordType'].'/thumbnails/'.$file;
                                $this->imageHelper->resize($src, $dstThumb, $field['makeThumbnail'][0], $field['makeThumbnail'][1]);
                            }
                        } else {
                            rename($src, $dst);
                        }
                    }
                }
            }

            if ($fieldName == 'published') {
                $this->adjustPublicationDateOfChilds($record);
            }
        }
        return $record;
    }




    protected function parseFilter($type, $filter)
    {
        if (empty($filter) || !isset($this->structure[$type])) return '';
        $where = array();
        foreach ($filter as $fieldName => $conditions) {
            if (isset($this->structure[$type]['fields'][$fieldName])) {
                $field = $this->structure[$type]['fields'][$fieldName];
                switch ($field['type']) {
                    case 'integer':
                    case 'hiddenInteger':
                    case 'real':
                    case 'hiddenReal':
                    case 'timestamp':
                    if (isset($conditions['='])) $where[] = $fieldName."='".$conditions['=']."'";
                    if (isset($conditions['!='])) $where[] = $fieldName."!='".$conditions['!=']."'";
                    if (isset($conditions['>'])) $where[] = $fieldName.">'".$conditions['>']."'";
                    if (isset($conditions['<'])) $where[] = $fieldName."<'".$conditions['<']."'";
                    if (isset($conditions['>='])) $where[] = $fieldName.">='".$conditions['>=']."'";
                    if (isset($conditions['<='])) $where[] = $fieldName."<='".$conditions['<=']."'";
                    break;

                    case 'string':
                    case 'code':
                    case 'files':
                    case 'options':
                    case 'hiddenText':
                    if (isset($conditions['='])) $where[] = $fieldName."='".$conditions['=']."'";
                    if (isset($conditions['!='])) $where[] = $fieldName."!='".$conditions['!=']."'";
                    if (isset($conditions['LIKE'])) $where[] = $fieldName." LIKE '%".$conditions['LIKE']."%'";
                    if (isset($conditions['NOT LIKE'])) $where[] = $fieldName." NOT LIKE '%".$conditions['NOT LIKE']."%'";
                    break;

                    case 'color':
                    case 'foreignKey':
                    case 'hash':
                    if (isset($conditions['='])) $where[] = $fieldName."='".$conditions['=']."'";
                    if (isset($conditions['!='])) $where[] = $fieldName."!='".$conditions['!=']."'";
                    break;
                }
            }
        }
        if (empty($where)) return '';
        $where = 'WHERE '.implode(' AND ', $where);
        return $where;
    }




    public function fetchRecords($type, $offset, $limit, $filter=array(), $order='')
    {
        if (!isset($this->structure[$type])) return array();
        $order = !empty($order) ? 'ORDER BY '.$order : '';
        $where = $this->parseFilter($type, $filter);
        $table = $this->info['prefix'].$type;
        $records = $this->dbh->table("SELECT * FROM $table $where $order LIMIT $offset,$limit");
        if (!records) return array();
        foreach ($records as $n => $record) $records[$n]['recordType'] = $type;
        return $records; 
    }




    public function countRecords($type, $filter)
    {
        if (!isset($this->structure[$type])) return 0;
        $where = $this->parseFilter($filter);
        $table = $this->info['prefix'].$type;
        $count = $this->dbh->value("SELECT COUNT(id) FROM $table $where");
        return $count ? $count : 0; 
    }




    public function getRecordById($type, $id)
    {
        if (!isset($this->structure[$type])) return array();
        $table = $this->info['prefix'].$type;
        $record = $this->dbh->row("SELECT * FROM ? WHERE id=? LIMIT 1", array($table, $id));
        if (!record) return array();
        $record['recordType'] = $type;
        return $record;
    }




    public function deleteRecord($record)
    {
        if (empty($record) || !isset($this->structure[$record['recordType']])) return false;
        foreach ($this->structure[$record['recordType']]['fields'] as $fieldName => $field) {
            if ($field['type'] == 'files' && !empty($record[$fieldName])) {
                $files = explode(',', $record[$fieldName]);
                foreach ($files as $file) {
                    @unlink(ROOT_DIR.'/uploads/infobase/'.$record['recordType'].'/'.$file);
                    if (isset($field['makeThumbnail'])) @unlink(ROOT_DIR.'/uploads/infobase/'.$record['recordType'].'/thumbnails/'.$file);
                }
            }
        }
        $isSuccess = true;
        if (isset($this->structure[$record['recordType']]['childs'])) {
            foreach ($this->structure[$record['recordType']]['childs'] as $childType) {
                $table = $this->info['prefix'].$childType;
                $childRecords = $this->dbh->table("SELECT * FROM $table WHERE parent='$record[recordType]-$record[id]'");
                foreach ($childRecords as $childRecord) {
                    $childRecord['recordType'] = $childType;
                    $isSuccess = $isSuccess && $this->deleteRecord($childRecord);
                }
            }
        }
        return $isSuccess;
    }




    public function checkRecursion($record)
    {
        //проверка поля parent на рекурсивную вложенность
    }

    public function checkPublicationDate($record)
    {
        //проверка корректности даты публикации
    }

    public function adjustPublicationDateOfChilds($record)
    {
        //корректировка даты публикации у вложенных записей
    }

    public static function createInfobase($structure)
    {
        return $structure;
    }

    public static function purgeInfobase($structure)
    {
        return array();
    }
}