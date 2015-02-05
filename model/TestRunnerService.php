<?php
/**
 * Created by Antoine on 27/01/15
 * at 15:42
 */

namespace oat\taoTestLinear\model;


use core_kernel_classes_Class;

class TestRunnerService extends \tao_models_classes_ClassService{

    //volatile
    private $itemDataCache = null;

    private $previousCache = null;

    public function getItemData($compilationId) {
        if (!isset($this->itemDataCache[$compilationId])) {
            $filePath = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($compilationId)->getPath().'data.json';
            $json = file_get_contents($filePath);
            $items = json_decode($json, true);
            if (!is_array($items)) {
                throw new \common_exception_Error('Unable to load compilation data for '.$compilationId);
            }

            if(isset($items['items'])){
                $items = $items['items'];
            }

            $this->itemDataCache[$compilationId] = $items;
        }
        return $this->itemDataCache[$compilationId];
    }

    public function getPrevious($compilationId){
        if(!isset($this->previousCache[$compilationId])){
            $previous = false;

            $filePath = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($compilationId)->getPath().'data.json';
            $json = file_get_contents($filePath);
            $config = json_decode($json, true);
            if (!is_array($config)) {
                throw new \common_exception_Error('Unable to load compilation data for '.$compilationId);
            }
            if(isset($config['previous'])){
                $previous = $config['previous'];
            }

            $this->previousCache[$compilationId] = $previous;
        }
        return $this->previousCache[$compilationId];
    }


    /**
     * Returns the root class of this service
     *
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
        return new core_kernel_classes_Class(CLASS_SIMPLE_DELIVERYCONTENT);
    }
}