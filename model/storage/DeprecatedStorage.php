<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestLinear\model\storage;

use core_kernel_classes_Resource;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\PhpSerializeStateless;
use oat\oatbox\service\ConfigurableService;

class DeprecatedStorage extends ConfigurableService implements LinearTestStorage
{
    use OntologyAwareTrait, PhpSerializeStateless;

    /**
     *
     * @param core_kernel_classes_Resource $test
     * @param array $itemUris
     * @return boolean
     */
    public function save(core_kernel_classes_Resource $test, array $itemUris)
    {
        $directoryId = $test->getOnePropertyValue($this->getProperty(TEST_TESTCONTENT_PROP));
        //null so create one
        if(is_null($directoryId)){
            $directory = \tao_models_classes_service_FileStorage::singleton()->spawnDirectory(true);
        } else {
            //get the real directory (or the encoded items if an old test)
            $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($directoryId->literal);
            if(!$directory->has('content.json')) {
                //create a new directory if items are stored in content
                $directory = \tao_models_classes_service_FileStorage::singleton()->spawnDirectory(true);
            }
        }
        $directory->getFile('content.json')->put('content.json', json_encode($itemUris));
        return $test->editPropertyValues($this->getProperty(TEST_TESTCONTENT_PROP), $directory->getId());
    }
    
    public function load(core_kernel_classes_Resource $test)
    {
        $directoryId = $test->getOnePropertyValue($this->getProperty(TEST_TESTCONTENT_PROP));
        if(is_null($directoryId)){
            throw new \common_exception_FileSystemError(__('Unknown test directory'));
        }
        $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($directoryId->literal);
    
        $items = array();
        if ($directory->has('content.json')) {
            $json = $directory->read('content.json');
        } else {
            $json = $directoryId;
        }
        $decoded = json_decode($json, true);
        if ($decoded === false || !is_array($decoded)) {
            throw new \common_exception_FileSystemError('Unable to decode linear test');
        }
        if (!isset($decoded['itemUris'])) {
            $decoded = array('itemUris' => $decoded);
        }
        return $decoded;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::deleteContent()
     */
    public function deleteContent( core_kernel_classes_Resource $test)
    {
        /** @var \core_kernel_classes_Literal $directoryId */
        $directoryId = $test->getOnePropertyValue($this->getProperty(TEST_TESTCONTENT_PROP));
        if(is_null($directoryId)){
            throw new \common_exception_FileSystemError(__('Unknown test directory'));
        }

        $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($directoryId->literal);
        $directory->deleteSelf();

		$test->removePropertyValues($this->getProperty(TEST_TESTCONTENT_PROP));
    }

}
