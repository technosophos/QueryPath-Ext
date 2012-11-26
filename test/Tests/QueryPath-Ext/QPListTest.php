<?php
/**
 * Tests for the QueryPath library.
 * @author M Butcher <matt@aleph-null.tv>
 * @license The GNU Lesser GPL (LGPL) or an MIT-like license.
 */
namespace QueryPath\Tests;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../TestCase.php';
require_once 'src/QueryPath/Extension/QPList.php';

use \QueryPath\Extension\QPList;

/**
 * @ingroup querypath_tests
 * @group extension
 */
class QPListTests extends TestCase {
  public static function setUpBeforeClass() {
    \QueryPath::enable('\QueryPath\Extension\QPList');
  }
  public function testAppendList() {
    $list = array('one', 'two', 'three');
    $qp = qp(\QueryPath::HTML_STUB, 'body')->appendList($list, QPList::UL);
    $this->assertEquals(3, $qp->find('ul>li')->size());
    $this->assertEquals('one', $qp->find('ul>li:first')->text());

    $list = array('zero-one','two','three', array('four-one', 'four-two', array('four-three-one', 'four-three-two')));
    $qp = qp(\QueryPath::HTML_STUB, 'body')->appendList($list, QPList::UL);
    $this->assertEquals(4, $qp->find('.qplist>li')->size());
    // Find bottom layer of recursive tree.
    $this->assertEquals(2, $qp->find('ul>li>ul>li>ul>li')->size());

    // Assoc array tests...
    $list = array('a' => 'aa', 'b' => 'bb', 'c' => 'cc');
    $qp = qp(\QueryPath::HTML_STUB, 'body')->appendList($list, QPList::UL);
    $this->assertEquals('aa', $qp->find('.qplist>li:first')->text());

    $qp = qp(\QueryPath::HTML_STUB, 'body')->appendList($list, QPList::DL);
    $this->assertEquals('a', $qp->top('.qplist>dt:first-of-type')->text());
    $this->assertEquals('aa', $qp->top('.qplist>dd:first-of-type')->text());
    //$qp->writeXML();
  }

  public function testAppendTable() {
    $data = array(
      'headers' => array('One', 'Two', 'Three'),
      'rows' => array(
        array(1, 2, 3),
        array('Ein', 'Zwei', 'Drei'),
        array('uno', 'dos', 'tres'),
        array('uno', 'du'), // See what happens here...
      ),
    );
    $qp = qp(\QueryPath::HTML_STUB, 'body')->appendTable($data);
    $this->assertEquals(3, $qp->top()->find('th')->size());
    $this->assertEquals(11, $qp->top()->find('td')->size());
    $this->assertEquals('Zwei', $qp->eq(4)->text());

    // Test with an object instead...
    $o = new \QueryPath\Extension\QPTableData();
    $o->setHeaders($data['headers']);
    $o->setRows($data['rows']);
    $qp = qp(\QueryPath::HTML_STUB, 'body')->appendTable($o);
    $this->assertEquals(3, $qp->top()->find('th')->size());
    $this->assertEquals(11, $qp->top()->find('td')->size());
    $this->assertEquals('Zwei', $qp->eq(4)->text());
  }
}
