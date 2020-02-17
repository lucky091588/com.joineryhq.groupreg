<?php

class CRM_Groupreg_APIWrappers_Contact {

  /**
   * Change parameters so that output is limited to relationship-permissioned contacts.
   */
  public function fromApiInput($apiRequest) {
    // Note that while building our "isGroupregPrefillContact" entityRef field,
    // CiviCRM will call the contact.get api with our isGroupregPrefill=true param
    // AND with a specified contact ID, if the entityRef field has a default value
    // (as on some form reloads after validation failure). In this case we should
    // not change the parameters, and should ignore our isGroupregPrefill=true
    // param. In such cases, overwriting it with our 'id IN []' param will cause
    // the field to be build wrongly (not sure exactly how or why), leading to endless
    // reload of the entityRef field via AJAX api. Only when `id` is missing
    // should we bother to insert our 'id IN []' param.
    if ($apiRequest['params']['id']) {
      return $apiRequest;
    }
    $userCid = CRM_Core_Session::singleton()->getLoggedInContactID();
    if ($userCid) {
      $related = CRM_Contact_BAO_Relationship::getRelationship($userCid, 3, 25, NULL, NULL, NULL, NULL, TRUE);
      $relatedCids = CRM_Utils_Array::collect('cid', $related);
      $apiRequest['params']['id'] = ['IN' => $relatedCids];
    }
    return $apiRequest;
  }

  /**
   * Munges the result before returning it to the caller.
   */
  public function toApiOutput($apiRequest, $result) {
    // Nothing to do here.
    return $result;
  }

}
