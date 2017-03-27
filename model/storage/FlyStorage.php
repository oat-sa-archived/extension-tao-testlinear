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
use oat\oatbox\service\ConfigurableService;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\oatbox\filesystem\FileSystemService;
use taoTests_models_classes_TestsService as TestService;

class FlyStorage extends ConfigurableService implements LinearTestStorage
{
    use OntologyAwareTrait;
    
    const OPTION_FILESYSTEM = 'fs';

    /**
     *
     * @param core_kernel_classes_Resource $test
     * @param array $itemUris
     * @return boolean
     */
    public function save(core_kernel_classes_Resource $test, array $content)
    {
        $serializer = $this->getServiceLocator()->get(FileReferenceSerializer::SERVICE_ID);
        $serial = $test->getOnePropertyValue($this->getProperty(TestService::TEST_TESTCONTENT_PROP));
        
        if(!is_null($serial)){
            $directory = $serializer->unserializeDirectory($serial);
        } else {
            // null so create one
            $fss = $this->getServiceLocator()->get(FileSystemService::SERVICE_ID);
            $base = $fss->getDirectory($this->getOption(self::OPTION_FILESYSTEM));
            
            $directory = $base->getDirectory(\tao_helpers_Uri::getUniqueId(\common_Utils::getNewUri()));
            $test->editPropertyValues($this->getProperty(TestService::TEST_TESTCONTENT_PROP), $serializer->serialize($directory));
        }
        return $directory->getFile('content.json')->put(json_encode($content));
    }
    
    public function load(core_kernel_classes_Resource $test)
    {
        $serializer = $this->getServiceLocator()->get(FileReferenceSerializer::SERVICE_ID);
        $serial = $test->getOnePropertyValue($this->getProperty(TestService::TEST_TESTCONTENT_PROP));
        if(is_null($serial)){
            throw new \common_exception_FileSystemError(__('Unknown test directory'));
        }
        $directory = $serializer->unserializeDirectory($serial);
        $json = $directory->getFile('content.json')->read();
        $decoded = json_decode($json, true);
        if ($decoded === false) {
            throw new \common_exception_FileSystemError('Unable to decode linear test');
        }
        return $decoded;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::deleteContent()
     */
    public function deleteContent( core_kernel_classes_Resource $test)
    {
        $serializer = $this->getServiceLocator()->get(FileReferenceSerializer::SERVICE_ID);
        $serial = $test->getOnePropertyValue($this->getProperty(TestService::TEST_TESTCONTENT_PROP));
        $serializer->cleanUp($serial);
		$test->removePropertyValues($this->getProperty(TestService::TEST_TESTCONTENT_PROP));
    }

}
