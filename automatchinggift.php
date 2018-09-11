<?php

require_once 'automatchinggift.civix.php';
use CRM_Automatchinggift_ExtensionUtil as E;

define('MATCHING_GIFT_CUSTOM_GROUP_NAME', 'gift_details');
define('MATCHING_GIFT_CUSTOM_FIELD_NAME', 'matching_gift');
define('MATCHING_GIFT_CONTRI_CUSTOM_FIELD_NAME', 'matching_gift_contribution');
define('MATCHING_GIFT_CONTRI_CUSTOM_GROUP_NAME', 'matching_gift_contributions');

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function automatchinggift_civicrm_config(&$config) {
  _automatchinggift_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function automatchinggift_civicrm_xmlMenu(&$files) {
  _automatchinggift_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function automatchinggift_civicrm_install() {
  _automatchinggift_civix_civicrm_install();
  civicrm_api3('Group', 'create', [
    'title' => "Matching Gift Organizations",
    'name' => "Matching Gift Organizations",
  ]);
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function automatchinggift_civicrm_postInstall() {
  _automatchinggift_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function automatchinggift_civicrm_uninstall() {
  _automatchinggift_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function automatchinggift_civicrm_enable() {
  _automatchinggift_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function automatchinggift_civicrm_disable() {
  _automatchinggift_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function automatchinggift_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _automatchinggift_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function automatchinggift_civicrm_managed(&$entities) {
  _automatchinggift_civix_civicrm_managed($entities);
  $entities[] = [
    'module' => 'com.megaphonetech.automatchinggift',
    'name' => 'contributionSoftcustomfield',
    'update' => 'never',
    'entity' => 'OptionValue',
    'params' => [
      'label' => ts('Contribution Soft'),
      'name' => 'civicrm_contribution_soft',
      'value' => 'ContributionSoft',
      'option_group_id' => 'cg_extend_objects',
      'is_active' => TRUE,
      'version' => 3,
    ],
  ];
  $groupId = civicrm_api3('Group', 'getvalue', [
    'return' => "id",
    'name' => "Matching Gift Organizations",
  ]);

  $entities[] = [
    'module' => 'com.megaphonetech.automatchinggift',
    'name' => MATCHING_GIFT_CONTRI_CUSTOM_GROUP_NAME,
    'entity' => 'CustomGroup',
    'params' => [
      'version' => 3,
      'name' => MATCHING_GIFT_CONTRI_CUSTOM_GROUP_NAME,
      'title' => ts('Matching Gift'),
      'extends' => 'ContributionSoft',
      'style' => 'Inline',
      'collapse_display' => TRUE,
      'is_active' => TRUE,
      'is_multiple' => FALSE,
      'collapse_adv_display' => FALSE,
    ],
  ];
  $entities[] = [
    'module' => 'com.megaphonetech.automatchinggift',
    'name' => MATCHING_GIFT_CONTRI_CUSTOM_FIELD_NAME,
    'entity' => 'CustomField',
    'params' => [
      'version' => 3,
      'name' => MATCHING_GIFT_CONTRI_CUSTOM_FIELD_NAME,
      'label' => ts('Matching Contribution ID'),
      'data_type' => 'Integer',
      'html_type' => 'Text',
      'is_required' => FALSE,
      'is_searchable' => FALSE,
      'is_search_range' => FALSE,
      'is_active' => TRUE,
      'text_length' => 255,
      'column_name' => MATCHING_GIFT_CONTRI_CUSTOM_FIELD_NAME,
      'custom_group_id' => MATCHING_GIFT_CONTRI_CUSTOM_GROUP_NAME,
    ],
  ];
  $entities[] = [
    'module' => 'com.megaphonetech.automatchinggift',
    'name' => MATCHING_GIFT_CUSTOM_GROUP_NAME,
    'entity' => 'CustomGroup',
    'params' => [
      'version' => 3,
      'name' => MATCHING_GIFT_CUSTOM_GROUP_NAME,
      'title' => ts('Gift Details'),
      'extends' => 'Contribution',
      'style' => 'Inline',
      'collapse_display' => FALSE,
      'is_active' => TRUE,
      'is_multiple' => FALSE,
      'collapse_adv_display' => TRUE,
    ],
  ];
  $entities[] = [
    'module' => 'com.megaphonetech.automatchinggift',
    'name' => MATCHING_GIFT_CUSTOM_FIELD_NAME,
    'entity' => 'CustomField',
    'params' => [
      'version' => 3,
      'name' => MATCHING_GIFT_CUSTOM_FIELD_NAME,
      'label' => ts('Matching Gift'),
      'data_type' => 'ContactReference',
      'html_type' => 'Autocomplete-Select',
      'is_required' => FALSE,
      'is_searchable' => TRUE,
      'is_search_range' => FALSE,
      'is_active' => TRUE,
      'text_length' => 255,
      'filter' => "action=lookup&group={$groupId}",
      'column_name' => MATCHING_GIFT_CUSTOM_FIELD_NAME,
      'custom_group_id' => MATCHING_GIFT_CUSTOM_GROUP_NAME,
      'help_post' => 'Specifying a matching gift organization will cause a pending contribution to be created for that organization, soft crediting this contact.',
    ],
  ];
  $entities[] = [
    'module' => 'com.megaphonetech.automatchinggift',
    'name' => 'Matching Gift Organizations',
    'entity' => 'Group',
    'params' => [
      'version' => 3,
      'title' => ts("Matching Gift Organizations"),
      'name' => "Matching Gift Organizations",
      'options' => ['match' => ['name']],
    ],
  ];
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function automatchinggift_civicrm_caseTypes(&$caseTypes) {
  _automatchinggift_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function automatchinggift_civicrm_angularModules(&$angularModules) {
  _automatchinggift_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function automatchinggift_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _automatchinggift_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function automatchinggift_civicrm_entityTypes(&$entityTypes) {
  _automatchinggift_civix_civicrm_entityTypes($entityTypes);
}

function automatchinggift_civicrm_custom($op, $groupID, $entityID, &$params) {
  $customGroupName = civicrm_api3('CustomGroup', 'getvalue', [
    'return' => "name",
    'id' => $groupID,
  ]);
  if ($customGroupName == MATCHING_GIFT_CUSTOM_GROUP_NAME) {
    CRM_AutoMatchingGift_Utils::createMatchingGift($entityID, $params);
  }
}
