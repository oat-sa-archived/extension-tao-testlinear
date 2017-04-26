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
use oat\taoTestLinear\scripts\install\SetupLinearModel;

return array(
    'name' => 'taoTestLinear',
    'label' => 'Linear Test Model',
    'description' => 'A simple linear test model',
    'license' => 'GPL-2.0',
    'version' => '3.0.0',
    'author' => 'Open Assessment Technologies SA',
    'requires' => array(
        'tao' => '>=9.0.0',
        'taoTests' => '>=4.0.0'
    ),
    'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoTestLinearManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoTestLinearManager', array('ext'=>'taoTestLinear')),
        array('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#TestAuthor', array('ext'=>'taoTestLinear', 'mod' => 'Authoring')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', array('ext'=>'taoTestLinear', 'mod' => 'TestRunner'))
    ),
    'install' => array(
        'rdf' => array(
                dirname(__FILE__). '/scripts/install/test.rdf'
        ),
        'php' => array(
            SetupLinearModel::class
        )
    ),
    'update' => "oat\\taoTestLinear\\scripts\\update\\Updater",
    'routes' => array(
        '/taoTestLinear' => 'oat\\taoTestLinear\\controller'
    ),    
    'constants' => array(
        # views directory
        "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
        
        #BASE URL (usually the domain root)
        'BASE_URL' => ROOT_URL.'taoTestLinear/',
    )
);