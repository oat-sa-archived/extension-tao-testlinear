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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestLinear\scripts\update;


use oat\tao\scripts\update\OntologyUpdater;
use oat\taoTestLinear\model\TestModel;
use oat\taoTestLinear\model\storage\DeprecatedStorage;

class Updater extends \common_ext_ExtensionUpdater
{

	/**
     * 
     * @param string $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {
        
		$this->skip('0.1','0.1.5');

		if ($this->isVersion('0.1.5')) {
			OntologyUpdater::syncModels();
			$testModelService = new TestModel();
			$testModelService->setServiceManager($this->getServiceManager());

			$this->getServiceManager()->register(TestModel::SERVICE_ID, $testModelService);
			$this->setVersion('0.2.0');
		}

		if ($this->isVersion('0.2.0')) {
		    $this->getServiceManager()->register('taoTestLinear/storage', new DeprecatedStorage());

		    $testModelService = $this->getServiceManager()->get(TestModel::SERVICE_ID);
		    $testModelService->setOption(TestModel::OPTION_STORAGE, 'taoTestLinear/storage');

		    $this->getServiceManager()->register(TestModel::SERVICE_ID, $testModelService);
		    $this->setVersion('1.0.0');
		}

		$this->skip('1.0.0','3.0.0');
	}
}