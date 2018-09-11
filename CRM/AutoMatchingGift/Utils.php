<?php

class CRM_AutoMatchingGift_Utils {

  /**
   * Create/Update matching gift.
   *
   * @param int $contributionId
   * @param array $params
   *
   */
  public static function createMatchingGift($contributionId, $params) {
    $cancelContributionId = $contributionSoftId = $contactID = NULL;
    $contributionStatus = 'Pending';

    foreach ($params as $values) {
      if ($values['column_name'] == MATCHING_GIFT_CUSTOM_FIELD_NAME && !empty($values['value'])) {
        $contactID = $values['value'];
      }
    }

    if (empty($contactID)) {
      return;
    }

    $matchingFieldId = civicrm_api3('CustomField', 'getvalue', [
      'return' => "id",
      'custom_group_id' => MATCHING_GIFT_CONTRI_CUSTOM_GROUP_NAME,
      'name' => MATCHING_GIFT_CONTRI_CUSTOM_FIELD_NAME,
    ]);

    try {
      $contributionSoft = civicrm_api3('ContributionSoft', 'getsingle', [
        'return' => [
          "contribution_id",
          'contribution_id.contact_id',
          'contribution_id.contribution_status_id',
        ],
        "custom_{$matchingFieldId}" => $contributionId,
      ]);
      if ($contributionSoft['contribution_id.contact_id'] == $contactID) {
        return;
      }
      $cancelContributionId = $contributionSoft['contribution_id'];
      $contributionStatus = $contributionSoft['contribution_id.contribution_status_id'];
      $contributionSoftId = $contributionSoft['id'];
    }
    catch (CiviCRM_API3_Exception $e) {
      // IGNORE NOT FOUND ERROR
    }

    $contributionDetails = civicrm_api3('Contribution', 'getsingle', [
      'id' => $contributionId,
      'return' => [
        'total_amount',
        'financial_type_id',
        'contact_id',
      ],
    ]);

    if ($cancelContributionId) {
      civicrm_api3('Contribution', 'create', [
        'id' => $cancelContributionId,
        'contribution_status_id' => 'Cancelled',
      ]);
    }

    $contributionParams = [
      'total_amount' => $contributionDetails['total_amount'],
      'financial_type_id' => $contributionDetails['financial_type_id'],
      'contact_id' => $contactID,
      'contribution_status_id' => $contributionStatus,
      'is_pay_later' => TRUE,
    ];
    $contribution = civicrm_api3('Contribution', 'create', $contributionParams);

    civicrm_api3('ContributionSoft', 'create', [
      'contact_id' => $contributionDetails['contact_id'],
      'amount' => $contributionDetails['total_amount'],
      'soft_credit_type_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_ContributionSoft', 'soft_credit_type_id', 'matched_gift'),
      "custom_{$matchingFieldId}" => $contributionId,
      'contribution_id' => $contribution['id'],
      'id' => $contributionSoftId,
    ]);

  }

}
