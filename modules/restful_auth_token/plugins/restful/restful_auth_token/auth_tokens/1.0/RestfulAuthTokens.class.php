<?php

/**
 * @file
 * Contains RestfulAuthTokens.
 */

class RestfulAuthTokens extends RestfulEntityBase {

  /**
   * Overrides RestfulEntityBase::getQueryForList().
   *
   * Keep only the "token" property.
   */
  public function getPublicFields() {
    $public_fields['token'] = array(
      'property' => 'token',
    );
    return $public_fields;
  }

  /**
   * Overrides \RestfulEntityBase::controllers
   *
   * @var array
   */
  protected $controllers = array(
    '' => array(
      // Get or create a new token.
      'get' => 'getOrCreateToken',
    ),
  );

  /**
   * Create a token for a user, and return its value.
   */
  public function getOrCreateToken($request = NULL, stdClass $account = NULL) {
    // Check if there is a token that did not expire yet.
    $query = new EntityFieldQuery();
    $result = $query
      ->entityCondition('entity_type', $this->entityType)
      ->propertyCondition('uid', $account->uid)
      ->range(0, 1)
      ->execute();

    $token_exists = FALSE;

    if (!empty($result['restful_auth_token'])) {
      $id = key($result['restful_auth_token']);
      $auth_token = entity_load_single('restful_auth_token', $id);

      if (!empty($auth_token->expire) && $auth_token->expire < REQUEST_TIME) {
        // Token has expired, so we can delete this token.
        $auth_token->delete();
        $token_exists = FALSE;
      }
      else {
        $token_exists = TRUE;
      }
    }

    if (!$token_exists) {
      // Create a new token.
      $values = array(
        'uid' => $account->uid,
        'type' => 'restful_auth_token',
        'created' => REQUEST_TIME,
        'name' => 'self',
        'token' => md5(time()),
      );
      $auth_token = entity_create('restful_auth_token', $values);
      $auth_token->save();
      $id = $auth_token->id;
    }

    return $this->viewEntity($id, $request, $account);
  }
}
