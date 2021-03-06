<?php

/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 * $Id$
 *
 */


namespace api\v4\Action;

use api\v4\UnitTestCase;
use Civi\Api4\MockBasicEntity;

/**
 * @group headless
 */
class BasicActionsTest extends UnitTestCase {

  public function testCrud() {
    MockBasicEntity::delete()->addWhere('id', '>', 0)->execute();

    $id1 = MockBasicEntity::create()->addValue('foo', 'one')->execute()->first()['id'];

    $result = MockBasicEntity::get()->execute();
    $this->assertCount(1, $result);

    $id2 = MockBasicEntity::create()->addValue('foo', 'two')->execute()->first()['id'];

    $result = MockBasicEntity::get()->selectRowCount()->execute();
    $this->assertEquals(2, $result->count());

    MockBasicEntity::update()->addWhere('id', '=', $id2)->addValue('foo', 'new')->execute();

    $result = MockBasicEntity::get()->addOrderBy('id', 'DESC')->setLimit(1)->execute();
    $this->assertCount(1, $result);
    $this->assertEquals('new', $result->first()['foo']);

    $result = MockBasicEntity::save()
      ->addRecord(['id' => $id1, 'foo' => 'one updated', 'weight' => '5'])
      ->addRecord(['id' => $id2, 'group:label' => 'Second'])
      ->addRecord(['foo' => 'three'])
      ->addDefault('color', 'pink')
      ->setReload(TRUE)
      ->execute()
      ->indexBy('id');

    $this->assertTrue(5 === $result[$id1]['weight']);
    $this->assertEquals('new', $result[$id2]['foo']);
    $this->assertEquals('two', $result[$id2]['group']);
    $this->assertEquals('three', $result->last()['foo']);
    $this->assertCount(3, $result);
    foreach ($result as $item) {
      $this->assertEquals('pink', $item['color']);
    }

    $ent1 = MockBasicEntity::get()->addWhere('id', '=', $id1)->execute()->first();
    $this->assertEquals('one updated', $ent1['foo']);
    $this->assertFalse(isset($ent1['group:label']));

    $ent2 = MockBasicEntity::get()->addWhere('group:label', '=', 'Second')->addSelect('group:label', 'group')->execute()->first();
    $this->assertEquals('two', $ent2['group']);
    $this->assertEquals('Second', $ent2['group:label']);
    // We didn't select this
    $this->assertFalse(isset($ent2['group:name']));

    // With no SELECT, all fields should be returned but not suffixy stuff like group:name
    $ent2 = MockBasicEntity::get()->addWhere('group:label', '=', 'Second')->execute()->first();
    $this->assertEquals('two', $ent2['group']);
    $this->assertFalse(isset($ent2['group:name']));
    // This one wasn't selected but did get used by the WHERE clause; ensure it isn't returned
    $this->assertFalse(isset($ent2['group:label']));

    MockBasicEntity::delete()->addWhere('id', '=', $id2);
    $result = MockBasicEntity::get()->execute();
    $this->assertEquals('one updated', $result->first()['foo']);
  }

  public function testReplace() {
    MockBasicEntity::delete()->addWhere('id', '>', 0)->execute();

    $objects = [
      ['group' => 'one', 'color' => 'red'],
      ['group' => 'one', 'color' => 'blue'],
      ['group' => 'one', 'color' => 'green'],
      ['group' => 'two', 'color' => 'orange'],
    ];

    foreach ($objects as &$object) {
      $object['id'] = MockBasicEntity::create()->setValues($object)->execute()->first()['id'];
    }

    // Keep red, change blue, delete green, and add yellow
    $replacements = [
      ['color' => 'red', 'id' => $objects[0]['id']],
      ['color' => 'not blue', 'id' => $objects[1]['id']],
      ['color' => 'yellow'],
    ];

    MockBasicEntity::replace()->addWhere('group', '=', 'one')->setRecords($replacements)->execute();

    $newObjects = MockBasicEntity::get()->addOrderBy('id', 'DESC')->execute()->indexBy('id');

    $this->assertCount(4, $newObjects);

    $this->assertEquals('yellow', $newObjects->first()['color']);

    $this->assertEquals('not blue', $newObjects[$objects[1]['id']]['color']);

    // Ensure group two hasn't been altered
    $this->assertEquals('orange', $newObjects[$objects[3]['id']]['color']);
    $this->assertEquals('two', $newObjects[$objects[3]['id']]['group']);
  }

  public function testBatchFrobnicate() {
    MockBasicEntity::delete()->addWhere('id', '>', 0)->execute();

    $objects = [
      ['group' => 'one', 'color' => 'red', 'number' => 10],
      ['group' => 'one', 'color' => 'blue', 'number' => 20],
      ['group' => 'one', 'color' => 'green', 'number' => 30],
      ['group' => 'two', 'color' => 'blue', 'number' => 40],
    ];
    foreach ($objects as &$object) {
      $object['id'] = MockBasicEntity::create()->setValues($object)->execute()->first()['id'];
    }

    $result = MockBasicEntity::batchFrobnicate()->addWhere('color', '=', 'blue')->execute();
    $this->assertEquals(2, count($result));
    $this->assertEquals([400, 1600], \CRM_Utils_Array::collect('frobnication', (array) $result));
  }

  public function testGetFields() {
    $getFields = MockBasicEntity::getFields()->execute()->indexBy('name');

    $this->assertCount(6, $getFields);
    $this->assertEquals('Id', $getFields['id']['title']);
    // Ensure default data type is "String" when not specified
    $this->assertEquals('String', $getFields['color']['data_type']);

    // Getfields should default to loadOptions = false and reduce them to bool
    $this->assertTrue($getFields['group']['options']);
    $this->assertFalse($getFields['id']['options']);

    // Now load options
    $getFields = MockBasicEntity::getFields()
      ->addWhere('name', '=', 'group')
      ->setLoadOptions(TRUE)
      ->execute()->indexBy('name');

    $this->assertCount(1, $getFields);
    $this->assertArrayHasKey('one', $getFields['group']['options']);
  }

  public function testItemsToGet() {
    $get = MockBasicEntity::get()
      ->addWhere('color', 'NOT IN', ['yellow'])
      ->addWhere('color', 'IN', ['red', 'blue'])
      ->addWhere('color', '!=', 'green')
      ->addWhere('group', '=', 'one')
      ->addWhere('size', 'LIKE', 'big')
      ->addWhere('shape', 'LIKE', '%a');

    $itemsToGet = new \ReflectionMethod($get, '_itemsToGet');
    $itemsToGet->setAccessible(TRUE);

    $this->assertEquals(['red', 'blue'], $itemsToGet->invoke($get, 'color'));
    $this->assertEquals(['one'], $itemsToGet->invoke($get, 'group'));
    $this->assertEquals(['big'], $itemsToGet->invoke($get, 'size'));
    $this->assertEmpty($itemsToGet->invoke($get, 'shape'));
    $this->assertEmpty($itemsToGet->invoke($get, 'weight'));
  }

  public function testFieldsToGet() {
    $get = MockBasicEntity::get()
      ->addWhere('color', '!=', 'green');

    $isFieldSelected = new \ReflectionMethod($get, '_isFieldSelected');
    $isFieldSelected->setAccessible(TRUE);

    // If no "select" is set, should always return true
    $this->assertTrue($isFieldSelected->invoke($get, 'color'));
    $this->assertTrue($isFieldSelected->invoke($get, 'shape'));
    $this->assertTrue($isFieldSelected->invoke($get, 'size', 'color', 'shape'));

    // With a non-empty "select" fieldsToSelect() will return fields needed to evaluate each clause.
    $get->addSelect('id');
    $this->assertTrue($isFieldSelected->invoke($get, 'color', 'shape', 'size'));
    $this->assertTrue($isFieldSelected->invoke($get, 'id'));
    $this->assertFalse($isFieldSelected->invoke($get, 'shape', 'size', 'weight'));
    $this->assertFalse($isFieldSelected->invoke($get, 'group'));

    $get->addClause('OR', ['shape', '=', 'round'], ['AND', [['size', '=', 'big'], ['weight', '!=', 'small']]]);
    $this->assertTrue($isFieldSelected->invoke($get, 'color'));
    $this->assertTrue($isFieldSelected->invoke($get, 'id'));
    $this->assertTrue($isFieldSelected->invoke($get, 'shape'));
    $this->assertTrue($isFieldSelected->invoke($get, 'size'));
    $this->assertTrue($isFieldSelected->invoke($get, 'group', 'weight'));
    $this->assertFalse($isFieldSelected->invoke($get, 'group'));

    $get->addOrderBy('group');
    $this->assertTrue($isFieldSelected->invoke($get, 'group'));
  }

  public function testWildcardSelect() {
    MockBasicEntity::delete()->addWhere('id', '>', 0)->execute();

    $records = [
      ['group' => 'one', 'color' => 'red', 'shape' => 'round', 'size' => 'med', 'weight' => 10],
      ['group' => 'two', 'color' => 'blue', 'shape' => 'round', 'size' => 'med', 'weight' => 20],
    ];
    MockBasicEntity::save()->setRecords($records)->execute();

    foreach (MockBasicEntity::get()->addSelect('*')->execute() as $result) {
      ksort($result);
      $this->assertEquals(['color', 'group', 'id', 'shape', 'size', 'weight'], array_keys($result));
    }

    $result = MockBasicEntity::get()
      ->addSelect('*e', 'weig*ht')
      ->execute()
      ->first();
    $this->assertEquals(['shape', 'size', 'weight'], array_keys($result));
  }

}
