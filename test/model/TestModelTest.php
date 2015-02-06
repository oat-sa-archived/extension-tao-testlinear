<?php

namespace oat\taoMediaManager\test\model;



use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTestLinear\model\TestModel;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class TestModelTest extends TaoPhpUnitTestRunner {

    /**
     * @var TestModel
     */
    private $testModel = null;

    private $test = null;

    private $uri = "";

    public function setup(){
        TaoPhpUnitTestRunner::initTest();
        $this->testModel = new TestModel();
        $this->uri = "MyGreatTestUri#123";
        $this->test = new \core_kernel_classes_Resource($this->uri);
    }

    public function cleanup() {
        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, null);
    }

    public function testGetAuthoringUrl() {
        $expectedUrl = \tao_helpers_Uri::getRootUrl() . "taoTestLinear/Authoring/index?uri=" . urlencode($this->uri);


        $url = $this->testModel->getAuthoringUrl($this->test);
        $this->assertEquals($expectedUrl, $url, __('The authoring url is malformed'));
    }

    public function testPrepareContent() {

        $testModelMock = $this->getMockBuilder('oat\taoTestLinear\model\TestModel')
            ->setMethods(array('save'))
            ->getMock();

        $itemUris = array("MyFirstItem#123", "MySecondItem#456");
        $firstItem= new \core_kernel_classes_Resource($itemUris[0]);
        $secondItem= new \core_kernel_classes_Resource($itemUris[1]);

        $items = array($firstItem, $secondItem);

        $testModelMock->expects($this->once())
            ->method('save')
            ->with($this->test, $itemUris);

        $testModelMock->prepareContent($this->test, $items);
    }

    public function testDeleteContent(){

        $testMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue', 'removePropertyValues'))
            ->getMock();
        $propInstanceContent = new \core_kernel_classes_Property(TEST_TESTCONTENT_PROP);


        //create the tree to delete
        if(!is_dir(dirname(__FILE__). '/../sample/test')){
            mkdir(dirname(__FILE__). '/../sample/test');
        }
        $file = dirname(__FILE__). '/../sample/test/content.json';
        file_put_contents($file, 'content');

        //Get directory to remove (new method)
        $directoryId = "MyDirectoryId";
        $testMock->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn(new \core_kernel_classes_Literal($directoryId));

        $testMock->expects($this->once())
            ->method('removePropertyValues')
            ->with($propInstanceContent)
            ->willReturn(true);


        //will del a directory
        $storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('getDirectoryById'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $storageMock);


        $directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();

        $directoryMock->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/test');


        $storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($directoryId)
            ->willReturn($directoryMock);


        $this->testModel->deleteContent($testMock);


        $this->assertFalse(file_exists(dirname(__FILE__). '/../sample/test/content.json'), __('content.json should be delete'));
        $this->assertFalse(is_dir(dirname(__FILE__). '/../sample/test'), __('directory tree should be delete'));


        $this->cleanup();



    }

    /**
     * @expectedException \common_exception_FileSystemError
     */
    public function testDeleteContentException(){

        $testMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue'))
            ->getMock();
        $propInstanceContent = new \core_kernel_classes_Property(TEST_TESTCONTENT_PROP);

        //Get directory to remove (new method)
        $testMock->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn(null);

        $this->testModel->deleteContent($testMock);


    }

    public function testGetItems(){

        $testMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue'))
            ->getMock();
        $propInstanceContent = new \core_kernel_classes_Property(TEST_TESTCONTENT_PROP);


        //Get directory to get Items
        $directoryId = "MyDirectoryId";
        $testMock->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn(new \core_kernel_classes_Literal($directoryId));


        //will get directory and path<
        $storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('getDirectoryById'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $storageMock);


        $directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();

        $directoryMock->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/source/');



        $storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($directoryId)
            ->willReturn($directoryMock);

        $items = $this->testModel->getItems($testMock);

        $itemUris = json_decode(file_get_contents(dirname(__FILE__). '/../sample/source/content.json'));
        foreach($items as $item){
            $this->assertContains($item->getUri(), $itemUris);
        }


    }

    public function testCloneContent(){

        $testMockSource = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue'))
            ->getMock();

        $testMockDest = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue'))
            ->getMock();
        $propInstanceContent = new \core_kernel_classes_Property(TEST_TESTCONTENT_PROP);


        //Get directory to get Items
        $directoryIdSource = "MyDirectoryIdSource";
        $directoryIdDest = "MyDirectoryIdDest";
        $testMockSource->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn(new \core_kernel_classes_Literal($directoryIdSource));

        $testMockDest->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn(new \core_kernel_classes_Literal($directoryIdDest));


        //will get directory and path
        $storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('getDirectoryById'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $storageMock);


        $directoryMockSource = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();

        $directoryMockSource->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/source/');

        $directoryMockDest = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();

        $directoryMockDest->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/dest/');


        $storageMock->expects($this->at(0))
            ->method('getDirectoryById')
            ->with($directoryIdSource)
            ->willReturn($directoryMockSource);

        $storageMock->expects($this->at(1))
            ->method('getDirectoryById')
            ->with($directoryIdDest)
            ->willReturn($directoryMockDest);

        $this->testModel->cloneContent($testMockSource, $testMockDest);

        $this->assertEquals(file_get_contents(dirname(__FILE__). '/../sample/source/content.json'), file_get_contents(dirname(__FILE__). '/../sample/dest/content.json'));


    }

    public function testGetCompilerClass() {
        $this->assertEquals('oat\\taoTestLinear\\model\\TestCompiler', $this->testModel->getCompilerClass(), __('it isn\t the right compiler class'));
    }

    public function testSaveFormer() {
        $testMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue', 'editPropertyValues'))
            ->getMock();
        $propInstanceContent = new \core_kernel_classes_Property(TEST_TESTCONTENT_PROP);

        $itemUris = array("http://tao.localdomain:8888/tao.rdf#i1421426057643811", "http://tao.localdomain:8888/tao.rdf#i1421426059534113");

        //former stock method (in ontology)
        $directoryId = "MyGreatDirectoryId";
        $returnValue = new \core_kernel_classes_Literal(json_encode($itemUris));
        $testMock->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn($returnValue);

        $testMock->expects($this->once())
            ->method('editPropertyValues')
            ->with($propInstanceContent, $directoryId)
            ->willReturn(true);


        //will spawn a new directory and store the content file
        $storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('spawnDirectory', 'getDirectoryById'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $storageMock);

        $falseDirectoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();
        $falseDirectoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn('not/a/dir');

        $directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath', 'getId'))
            ->getMock();

        $directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/');

        $directoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($directoryId);


        $storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with(json_encode($itemUris))
            ->willReturn($falseDirectoryMock);

        $storageMock->expects($this->once())
            ->method('spawnDirectory')
            ->with(true)
            ->willReturn($directoryMock);

        $edit = $this->testModel->save($testMock, $itemUris);

        $file = json_decode(file_get_contents(dirname(__FILE__). '/../sample/content.json'));


        $this->assertEquals(true, $edit, __('Should edit the property value'));
        $this->assertEquals($itemUris, $file, __('The content file doesn\'t contain the right items'));

        $this->cleanup();
    }


    public function testSaveNull() {
        $testMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue', 'editPropertyValues'))
            ->getMock();
        $propInstanceContent = new \core_kernel_classes_Property(TEST_TESTCONTENT_PROP);

        $itemUris = array("http://tao.localdomain:8888/tao.rdf#i1421426057890756", "http://tao.localdomain:8888/tao.rdf#i0099886059534113");

        //null item content property
        $testMock->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn(null);

        //will spawn a new directory and store the content file
        $directoryId = "MyGreatDirectoryId";
        $storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('spawnDirectory'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $storageMock);


        $directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath', 'getId'))
            ->getMock();

        $directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/');

        $directoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($directoryId);


        $storageMock->expects($this->once())
            ->method('spawnDirectory')
            ->with(true)
            ->willReturn($directoryMock);


        $testMock->expects($this->once())
            ->method('editPropertyValues')
            ->with($propInstanceContent, $directoryId)
            ->willReturn(true);


        $edit = $this->testModel->save($testMock, $itemUris);

        $file = json_decode(file_get_contents(dirname(__FILE__). '/../sample/content.json'));

        $this->assertEquals(true, $edit, __('Should edit the property value'));
        $this->assertEquals($itemUris, $file, __('The content file doesn\'t contain the right items'));

        $this->cleanup();
    }

    public function testSaveNew() {
        $testMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getOnePropertyValue', 'editPropertyValues'))
            ->getMock();
        $propInstanceContent = new \core_kernel_classes_Property(TEST_TESTCONTENT_PROP);

        $itemUris = array("http://tao.localdomain:8888/tao.rdf#i9988776057890756", "http://tao.localdomain:8888/tao.rdf#i0099886059556677");

        //new stock method (in file)
        $directoryId = "MyGreatDirectoryId";
        $returnValue = new \core_kernel_classes_Literal($directoryId);
        $testMock->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($propInstanceContent)
            ->willReturn($returnValue);

        $testMock->expects($this->once())
            ->method('editPropertyValues')
            ->with($propInstanceContent, $directoryId)
            ->willReturn(true);


        //will spawn a new directory and store the content file
        $storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('getDirectoryById'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $storageMock);


        $directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath', 'getId'))
            ->getMock();

        $directoryMock->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/');

        $directoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($directoryId);


        $storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($directoryId)
            ->willReturn($directoryMock);

        $edit = $this->testModel->save($testMock, $itemUris);

        $file = json_decode(file_get_contents(dirname(__FILE__). '/../sample/content.json'));


        $this->assertEquals(true, $edit, __('Should edit the property value'));
        $this->assertEquals($itemUris, $file, __('The content file doesn\'t contain the right items'));

        $this->cleanup();
    }



}
 