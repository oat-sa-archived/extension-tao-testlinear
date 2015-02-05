<?php

namespace oat\taoMediaManager\test\model;



use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTestLinear\model\TestRunnerService;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class TestRunnerServiceTest extends TaoPhpUnitTestRunner {

    /**
     * @var TestRunnerService
     */
    private $service = null;

    private $storageMock = null;

    private $directoryMock = null;


    public function setup(){
        TaoPhpUnitTestRunner::initTest();
        $this->service = TestRunnerService::singleton();

        $this->storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('getDirectoryById'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $this->storageMock);

        $this->directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();

    }

    public function tearDown() {
        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        $this->storageMock = null;
        $this->directoryMock = null;
        $this->service = null;
    }

    public function testGetItemDataWithoutConfig() {

        $compilationId = "MyFirstCompilationID";
        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withoutConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $itemData = $this->service->getItemData($compilationId);

        $arrayKeys = array(
            "http://tao.localdomain:8888/tao.rdf#i142142605577127",
            "http://tao.localdomain:8888/tao.rdf#i142142605349615",
            "http://tao.localdomain:8888/tao.rdf#i142142605618879",
            "http://tao.localdomain:8888/tao.rdf#i1421426057643811"
        );

        $this->assertInternalType('array', $itemData, __('Get item Data should return an array'));
        $this->assertEquals($arrayKeys, array_keys($itemData), __('Keys of return value are wrong'));

    }

    public function testGetItemDataWithConfig() {


        $compilationId = "MySecondCompilationID";
        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withoutConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $itemData = $this->service->getItemData($compilationId);

        $arrayKeys = array(
            "http://tao.localdomain:8888/tao.rdf#i142142605577127",
            "http://tao.localdomain:8888/tao.rdf#i142142605349615",
            "http://tao.localdomain:8888/tao.rdf#i142142605618879",
            "http://tao.localdomain:8888/tao.rdf#i1421426057643811"
        );

        $this->assertInternalType('array', $itemData, __('Get item Data should return an array'));
        $this->assertEquals($arrayKeys, array_keys($itemData), __('Keys of return value are wrong'));

    }

    public function testGetPreviousWithoutConfig() {

        $compilationId = "MyCompilationID#3";

        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withoutConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $previous = $this->service->getPrevious($compilationId);


        $this->assertInternalType('boolean', $previous, __('Get previous should return a boolean'));
        $this->assertFalse($previous, __('Previous should be false'));

    }

    public function testGetPreviousWithConfig() {

        $compilationId = "MyCompilationID#4";
        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $previous = $this->service->getPrevious($compilationId);


        $this->assertInternalType('boolean', $previous, __('Get previous should return a boolean'));
        $this->assertTrue($previous, __('Previous should be true'));

    }


}
 