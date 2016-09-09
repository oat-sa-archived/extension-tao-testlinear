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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoTestLinear\scripts\install;

use oat\tao\model\ClientLibConfigRegistry;
use oat\taoTestLinear\model\TestModel;
use oat\oatbox\filesystem\FileSystemService;
use oat\taoTestLinear\model\storage\FlyStorage;

class SetupLinearModel extends \common_ext_action_InstallAction
{
    public function __invoke($params)
    {
        $fsService = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
        $source = $fsService->createFileSystem('taoTestLinear');
        $this->registerService(FileSystemService::SERVICE_ID, $fsService);
        
        $this->registerService('taoTestLinear/storage', new FlyStorage([
                FlyStorage::OPTION_FILESYSTEM => 'taoTestLinear'
        ]));
        
        $service = new TestModel([
            TestModel::OPTION_STORAGE => 'taoTestLinear/storage' 
        ]);
        
        $this->registerService(TestModel::SERVICE_ID, $service);
        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, 'Linear Test model setup correctly');
    }
}
