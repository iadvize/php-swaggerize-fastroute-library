<?php return array (
  0 => 
  array (
    'method' => 'POST',
    'uri' => '/button',
    'action' => 
    array (
      'uses' => 'IadvizeTargetingApiServiceButtonController@create',
      'as' => 'createButton',
    ),
  ),
  1 => 
  array (
    'method' => 'POST',
    'uri' => '/invitation',
    'action' => 
    array (
      'uses' => 'IadvizeTargetingApiServiceInvitationController@create',
      'as' => 'createInvitation',
    ),
  ),
  2 => 
  array (
    'method' => 'POST',
    'uri' => '/targetingRule',
    'action' => 
    array (
      'uses' => 'IadvizeTargetingApiServiceTargetingRuleController@create',
      'as' => 'createTargetingRule',
    ),
  ),
  3 => 
  array (
    'method' => 'POST',
    'uri' => '/visitorAttribute',
    'action' => 
    array (
      'uses' => 'IadvizeTargetingApiServiceVisitorAttributeController@create',
      'as' => 'createVisitorAttribute',
    ),
  ),
);