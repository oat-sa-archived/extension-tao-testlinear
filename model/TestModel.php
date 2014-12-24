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

namespace oat\taoTestLinear\model;

use taoTests_models_classes_TestModel;
use common_ext_ExtensionsManager;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;

/**
 * the linear TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTestLinear
 
 */
class TestModel
	implements taoTests_models_classes_TestModel
{
    
	/**
     * (non-PHPdoc)
	 * @see taoTests_models_classes_TestModel::__construct()
	 */
	public function __construct() {
	    common_ext_ExtensionsManager::singleton()->getExtensionById('taoTestLinear'); // loads the extension
	}

    /**
     * @see taoTests_models_classes_TestModel::getAuthoringUrl()
     */
    public function getAuthoringUrl( core_kernel_classes_Resource $test) {
        return _url('index', 'Authoring', 'taoTestLinear', array('uri' => $test->getUri()));
    }
 
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array()) {
        $test->editPropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), json_encode(array()));
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function deleteContent( core_kernel_classes_Resource $test) {
		$test->removePropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getItems()
     */
    public function getItems( core_kernel_classes_Resource $test) {
        $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        $json = $test->getOnePropertyValue($propInstanceContent);
        $items = array();
        if (!is_null($json)) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                foreach ($decoded as $uri) {
                    $items[] = new core_kernel_classes_Resource($uri);
                }
            } else {
                \common_Logger::w('Unable to decode item Uris');
            }
        }
        
        return $items;
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::cloneContent()
     */
    public function cloneContent( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination) {
		$propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        $value = $source->getOnePropertyValue($propInstanceContent);
        $destination->editPropertyValues($propInstanceContent, $value);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeTestLabel( core_kernel_classes_Resource $test) {
        // do nothing
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getCompilerClass()
     */
    public function getCompilerClass() {
        return 'oat\\taoTestLinear\\model\\TestCompiler';
    }
    
    /**
     * 
     * @param core_kernel_classes_Resource $test
     * @param array $itemUris
     * @return boolean
     */
    public function save(core_kernel_classes_Resource $test, array $itemUris) {
        $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        return $test->editPropertyValues($propInstanceContent, json_encode($itemUris));
    }
}
