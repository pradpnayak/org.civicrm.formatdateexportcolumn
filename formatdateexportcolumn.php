<?php

require_once 'formatdateexportcolumn.civix.php';
use CRM_Formatdateexportcolumn_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function formatdateexportcolumn_civicrm_config(&$config) {
  _formatdateexportcolumn_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function formatdateexportcolumn_civicrm_xmlMenu(&$files) {
  _formatdateexportcolumn_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function formatdateexportcolumn_civicrm_install() {
  _formatdateexportcolumn_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function formatdateexportcolumn_civicrm_postInstall() {
  _formatdateexportcolumn_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function formatdateexportcolumn_civicrm_uninstall() {
  _formatdateexportcolumn_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function formatdateexportcolumn_civicrm_enable() {
  _formatdateexportcolumn_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function formatdateexportcolumn_civicrm_disable() {
  _formatdateexportcolumn_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function formatdateexportcolumn_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _formatdateexportcolumn_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function formatdateexportcolumn_civicrm_managed(&$entities) {
  _formatdateexportcolumn_civix_civicrm_managed($entities);
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
function formatdateexportcolumn_civicrm_caseTypes(&$caseTypes) {
  _formatdateexportcolumn_civix_civicrm_caseTypes($caseTypes);
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
function formatdateexportcolumn_civicrm_angularModules(&$angularModules) {
  _formatdateexportcolumn_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function formatdateexportcolumn_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _formatdateexportcolumn_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_export().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_export
 */
function formatdateexportcolumn_civicrm_export($exportTempTable, $headerRows, $sqlColumns, $exportMode) {
  $sql = "UPDATE {$exportTempTable} ";
  $updateDateFields = [];
  $allFields = formatdateexportcolumn_getExportableFields();
  $dateFormat = [
    'activityDate' => civicrm_api3('Setting', 'getvalue', [
      'domain_id' => CRM_Core_Config::domainID(),
      'name' => 'exportFormatDate'
    ]),
    'activityDateTime' => civicrm_api3('Setting', 'getvalue', [
      'domain_id' => CRM_Core_Config::domainID(),
      'name' => 'exportFormatDateTime'
    ]),
  ];
  foreach ($sqlColumns as $columnName => $ignore) {
    if (array_key_exists($columnName, $allFields)
      && !empty($allFields[$columnName]['type'])
      && in_array(
        $allFields[$columnName]['type'],
        [
          CRM_Utils_Type::T_DATE,
          CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          CRM_Utils_Type::T_TIMESTAMP
        ]
      )
    ) {
      if ($allFields[$columnName]['type'] == CRM_Utils_Type::T_TIMESTAMP) {
        $format = $dateFormat['activityDateTime'];
      }
      else {
        $format = CRM_Utils_Array::value(
          CRM_Utils_Array::value(
            'formatType',
            CRM_Utils_Array::value(
              'html',
              $allFields[$columnName]
            )
          ),
          $dateFormat,
          ''
        );
      }
      $updateDateFields[] = " {$columnName} = IF({$columnName} = '' OR {$columnName} IS NULL,
          {$columnName},
          DATE_FORMAT({$columnName}, '{$format}')
        )
      ";
    }
  }
  if (!empty($updateDateFields)) {
    $sql .= 'SET ' . implode(', ', $updateDateFields);
    $totalExportBatchCount = CRM_Core_DAO::singleValueQuery("SELECT count(id) FROM {$exportTempTable}");
    $batchSize = 5000;
    for ($startId = 1; $startId <= $totalExportBatchCount; $startId += $batchSize) {
      $endId = $startId + $batchSize - 1;
      $query = $sql . " WHERE id BETWEEN {$startId} AND {$endId}";
      CRM_Core_DAO::executeQuery($query);
    }
  }
}

/**
 * Implements hook_civicrm_alterSettingsMetaData().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsMetaData
 *
 */
function formatdateexportcolumn_civicrm_alterSettingsMetaData(&$settingsMetadata, $domainID, $profile) {
  $settingsMetadata['exportFormatDate'] = [
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'exportFormatDate',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '%m/%d/%Y',
    'add' => '4.7',
    'title' => 'Short date Month Day Year',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
  ];
  $settingsMetadata['exportFormatDateTime'] = [
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'exportFormatDateTime',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '%m/%d/%Y %l:%M %P',
    'add' => '4.7',
    'title' => 'Complete Date and Time',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
  ];
}

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
 */
function formatdateexportcolumn_civicrm_preProcess($formName, &$form) {
  if ('CRM_Admin_Form_Setting_Date' == $formName) {
    $settings = $form->getVar('_settings');
    $settings['exportFormatDate'] = CRM_Core_BAO_Setting::LOCALIZATION_PREFERENCES_NAME;
    $settings['exportFormatDateTime'] = CRM_Core_BAO_Setting::LOCALIZATION_PREFERENCES_NAME;
    $form->setVar('_settings', $settings);
  }
}
/**
 * Get all exportable fields.
 *
 */
function formatdateexportcolumn_getExportableFields() {
  $contactFields = CRM_Contact_BAO_Contact::exportableFields('All');

  $fields = CRM_Core_Component::getQueryFields();
  unset($fields['note']);
  $fields = array_merge($contactFields, $fields);

  $fields = array_merge($fields, CRM_Activity_BAO_Activity::exportableFields());

  $fields = array_merge($fields, CRM_Contact_BAO_Query_Hook::singleton()->getFields());
  return $fields;
}
