<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2018
 *
 * Generated from xml/schema/CRM/Activity/ActivityContact.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:7a410e20f48318deeb83f3dd1900983a)
 */

/**
 * Database access object for the ActivityContact entity.
 */
class CRM_Activity_DAO_ActivityContact extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  static $_tableName = 'civicrm_activity_contact';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  static $_log = TRUE;

  /**
   * Activity contact id
   *
   * @var int unsigned
   */
  public $id;

  /**
   * Foreign key to the activity for this record.
   *
   * @var int unsigned
   */
  public $activity_id;

  /**
   * Foreign key to the contact for this record.
   *
   * @var int unsigned
   */
  public $contact_id;

  /**
   * Nature of this contact's role in the activity: 1 assignee, 2 creator, 3 focus or target.
   *
   * @var int unsigned
   */
  public $record_type_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_activity_contact';
    parent::__construct();
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static ::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'activity_id', 'civicrm_activity', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'contact_id', 'civicrm_contact', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Activity Contact ID'),
          'description' => 'Activity contact id',
          'required' => TRUE,
          'table_name' => 'civicrm_activity_contact',
          'entity' => 'ActivityContact',
          'bao' => 'CRM_Activity_BAO_ActivityContact',
          'localizable' => 0,
        ],
        'activity_id' => [
          'name' => 'activity_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Activity ID'),
          'description' => 'Foreign key to the activity for this record.',
          'required' => TRUE,
          'table_name' => 'civicrm_activity_contact',
          'entity' => 'ActivityContact',
          'bao' => 'CRM_Activity_BAO_ActivityContact',
          'localizable' => 0,
          'FKClassName' => 'CRM_Activity_DAO_Activity',
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Contact ID (match to contact)'),
          'description' => 'Foreign key to the contact for this record.',
          'required' => TRUE,
          'import' => TRUE,
          'where' => 'civicrm_activity_contact.contact_id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => 'civicrm_activity_contact',
          'entity' => 'ActivityContact',
          'bao' => 'CRM_Activity_BAO_ActivityContact',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ],
        'record_type_id' => [
          'name' => 'record_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Record Type ID'),
          'description' => 'Nature of this contact\'s role in the activity: 1 assignee, 2 creator, 3 focus or target.',
          'table_name' => 'civicrm_activity_contact',
          'entity' => 'ActivityContact',
          'bao' => 'CRM_Activity_BAO_ActivityContact',
          'localizable' => 0,
          'html' => [
            'type' => 'Select',
          ],
          'pseudoconstant' => [
            'optionGroupName' => 'activity_contacts',
            'optionEditPath' => 'civicrm/admin/options/activity_contacts',
          ]
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'activity_contact', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'activity_contact', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [
      'UI_activity_contact' => [
        'name' => 'UI_activity_contact',
        'field' => [
          0 => 'contact_id',
          1 => 'activity_id',
          2 => 'record_type_id',
        ],
        'localizable' => FALSE,
        'unique' => TRUE,
        'sig' => 'civicrm_activity_contact::1::contact_id::activity_id::record_type_id',
      ],
      'index_record_type' => [
        'name' => 'index_record_type',
        'field' => [
          0 => 'activity_id',
          1 => 'record_type_id',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_activity_contact::0::activity_id::record_type_id',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}