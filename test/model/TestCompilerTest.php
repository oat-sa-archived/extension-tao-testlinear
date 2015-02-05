<?php

namespace oat\taoMediaManager\test\model;



use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTestLinear\model\TestCompiler;
use oat\taoTestLinear\model\TestModel;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class TestCompilerTest extends TaoPhpUnitTestRunner {

    /**
     * @var \taoDelivery_models_classes_TrackedStorage
     */
    private $storage = null;

    /**
     * @var \core_kernel_classes_Resource
     */
    private $test = null;

    /**
     * @var \core_kernel_classes_Resource
     */
    private $item = null;

    /**
     * @var TestModel
     */
    private $testModel = null;

    public function setup(){
        TaoPhpUnitTestRunner::initTest();
        $this->test = new \core_kernel_classes_Resource('http://myFancyDomain.com/myGreatResourceUriForTest');
        $this->item = new \core_kernel_classes_Resource('http://myFancyDomain.com/myGreatResourceUriForItem');
        $this->item->setPropertyValue(new \core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel'), 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI');
        $this->testModel = new TestModel();
        $this->storage = new \taoDelivery_models_classes_TrackedStorage();

        $this->testModel->save($this->test, array());
    }

    public function testCompileEmpty() {

        //test without items
        $testCompiler = new TestCompiler($this->test, $this->storage);
        $waitingReport = new \common_report_Report(\common_report_Report::TYPE_ERROR, __("A Test must contain at least one item to be compiled."));
        $report = $testCompiler->compile();

        $this->assertEquals($waitingReport, $report, 'No items in sample test');
    }

    public function testCompile(){
        //test with items
        $this->testModel->save($this->test, array($this->item->getUri()));
        $waitingReport = new \common_report_Report(\common_report_Report::TYPE_SUCCESS);


        $testCompiler = $this->getMockBuilder('oat\taoTestLinear\model\TestCompiler')
            ->setConstructorArgs(array($this->test, $this->storage))
            ->setMethods(array('subCompile'))
            ->getMock();

        $testCompiler->expects($this->once())
            ->method('subCompile')
            ->willReturn($waitingReport);

        $report = $testCompiler->compile();

        $this->assertEquals('test', $report->getMessage(),__('Compilation should work'));


    }




}
 