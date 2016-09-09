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

use oat\oatbox\service\ConfigurableService;
use taoTests_models_classes_TestModel;
use common_ext_ExtensionsManager;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;
use oat\taoTestLinear\model\storage\LinearTestStorage;

/**
 * the linear TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTestLinear

 */
class TestModel extends ConfigurableService
	implements taoTests_models_classes_TestModel
{

    const SERVICE_ID = 'taoTestLinear/TestModel';
    
    const OPTION_STORAGE = 'storage';

    /**
     * TestModel constructor.
     * @param array $options
     */
	public function __construct($options = array()) {
	    common_ext_ExtensionsManager::singleton()->getExtensionById('taoTestLinear'); // loads the extension
        parent::__construct($options);
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
        $itemUris = array();
        foreach ($items as $item) {
            $itemUris[] = $item->getUri();
        }
        $this->save($test, array('itemUris' => $itemUris));
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function deleteContent( core_kernel_classes_Resource $test) {
        return $this->getStorage()->deleteContent($test);
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getItems()
     */
    public function getItems( core_kernel_classes_Resource $test) {
        $decoded = $this->load($test);
        $items = array();
        foreach ($decoded['itemUris'] as $uri) {
            $items[] = new core_kernel_classes_Resource($uri);
        }
        return $items;
    }
    
    public function getConfig( core_kernel_classes_Resource $test) {
        $decoded = $this->load($test);
        $config = array();
        if (isset($decoded['config']) && is_array($decoded['config'])) {
            foreach ($decoded['config'] as $key => $value) {
                $config[$key] = $value;
            }
        } else if(!is_array($decoded)){
            \common_Logger::w('Unable to decode item Uris');
        }

        return $config;
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::cloneContent()
     */
    public function cloneContent( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination) {
        $content = $this->getStorage()->load($source);
        return $this->getStorage()->save($destination, $content);
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
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getPackerClass()
     */
    public function getPackerClass() {
        throw new \common_exception_NotImplemented("The packer isn't yet implemented for Linear tests");
    }
    
    /**
     * @return LinearTestStorage
     */
    protected function getStorage()
    {
        return $this->getServiceLocator()->get($this->getOption(self::OPTION_STORAGE));
    }

    /**
     *
     * @param core_kernel_classes_Resource $test
     * @param array $itemUris
     * @return boolean
     */
    public function save(core_kernel_classes_Resource $test, array $definition) {
        return $this->getStorage()->save($test, $definition);
    }
    
    protected function load(core_kernel_classes_Resource $test) {
        return $this->getStorage()->load($test);
    }
}
