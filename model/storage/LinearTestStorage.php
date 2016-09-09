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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestLinear\model\storage;

use core_kernel_classes_Resource;

interface LinearTestStorage
{
    /**
     * Saves an array representing the structure of the test
     *
     * @param core_kernel_classes_Resource $test
     * @param array $itemUris
     * @return boolean
     */
    public function save(core_kernel_classes_Resource $test, array $content);
    
    /**
     * Returns an array representing the structure of the test
     * 
     * @param core_kernel_classes_Resource $test
     * @return array
     */
    public function load(core_kernel_classes_Resource $test);
    
    /**
     * Deletes the content of a test
     * 
     * @param core_kernel_classes_Resource $test
     * @see taoTests_models_classes_TestModel::deleteContent()
     */
    public function deleteContent( core_kernel_classes_Resource $test);

}
