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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define(['jquery', 'lodash',  'i18n', 'helpers', 'iframeNotifier', 'serviceApi/ServiceApi', 'serviceApi/UserInfoService', 'serviceApi/StateStorage'], 
		function($, _, __, helpers, iframeNotifier, ServiceApi, UserInfoService, StateStorage) {
    'use strict';

    var Controller = {
        
        testContext: {},
        testServiceApi: null,
        testId : null,
        currentItemApi: null,
            
        updateItem: function(serviceApi, loading) {
            
        	this.currentItemApi = serviceApi;
        	
            if (loading === true) {
                iframeNotifier.parent('loading');
            }
            
            // Markup clean-up.
            $('#item').remove();
            
            // Create new item iframe.
            var $item = $('<iframe id="item" frameborder="0" scrolling="auto" style="width: 100%"></iframe>').prependTo('body');
            
            // Adjust frame height.
            this.adjustFrame();
            
            // Inject API instance in item + serviceLoaded event callback.
            // only supported by QTI?
            $(document).on('serviceloaded', function() {
                iframeNotifier.parent('unloading');
            });
            
            var that = this;
            serviceApi.onFinish(function() {
                iframeNotifier.parent('loading');
                that.doNext();
                iframeNotifier.parent('unloading');
            });
            
            serviceApi.loadInto($item[0]);
            console.log('x');
        },
        
        nextItem: function() {
            
            iframeNotifier.parent('loading');
            var that = this;
            
            this.currentItemApi.kill(function(signal) {
            	that.doNext();
                iframeNotifier.parent('unloading');
            });
        },
        
        doNext: function() {
            
        	var data = 
            $.ajax({
                context: this,
                url: helpers._url('next', 'TestRunner', 'taoTestLinear'),
                data: { serviceCallId: this.testServiceApi.getServiceCallId() },
                accepts: 'application/json',
                cache: false,
                type: 'POST',
                success: function(data, textStatus, jqXhr) {
                	if (data.api !== null) {
                		var itemServiceApi = eval(data.api);
                        this.updateItem(itemServiceApi, false);
                    }
                    else {
                        this.testServiceApi.finish();
                    }
                }
            });

        },
        
        adjustFrame: function() {
            var windowHeight = window.innerHeight ? window.innerHeight : $(window).height();
            var navHeight = $('#navigation').height();
            $('#item').height(windowHeight - navHeight);
        }
    };
    
    return {
        start: function(testId, itemApi) {
            
            var itemServiceApi = eval(itemApi);
            // Controller.testContext = context;
            
            iframeNotifier.parent('loading');
            
            window.onServiceApiReady = function onServiceApiReady(serviceApi) {
                Controller.testServiceApi = serviceApi;
                Controller.testId = testId;
                Controller.updateItem(itemServiceApi, false);
                
                // Bindings.
                $(window).bind('resize', function() {
                    Controller.adjustFrame();
                });
                
                $('#next').bind('click', function() {
                    Controller.nextItem(); 
                });
            };
            
            
            // Notify the parent that everything is fine and ready.
            iframeNotifier.parent('serviceready');
        }
    };
});