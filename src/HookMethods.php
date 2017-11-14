<?php
namespace Tilmeld;

use SciActive\Hook;

class HookMethods {
  public static function setup() {
    // Check for the skip access control option and add AC selectors.
    $GetEntitiesHook = function (&$array, $name, &$object, &$function, &$data) {
      if (isset($array[0]['skip_ac']) && $array[0]['skip_ac']) {
        $data['Tilmeld_skip_ac'] = true;
      } else {
        // Add access control selectors
        Tilmeld::addAccessControlSelectors($array);
      }
    };

    // Filter entities being deleted for user permissions.
    $CheckPermissionsDeleteHook = function (&$array) {
      $entity = $array[0];
      if ((int) $entity === $entity) {
        $entity = \Nymph\Nymph::getEntity($array[0]);
      }
      if ((object) $entity !== $entity) {
        $array = false;
        return;
      }
      // Test for permissions.
      if (!Tilmeld::checkPermissions($entity, Tilmeld::DELETE_ACCESS)) {
        $array = false;
      }
    };

    // TODO(hperrin): Is this necessary, after adding AC selectors?
    // Filter entities being returned for user permissions.
    // $CheckPermissionsReturnHook = function (&$array, $name, &$object, &$function, &$data) {
    //   if (isset($data['Tilmeld_skip_ac']) && $data['Tilmeld_skip_ac']) {
    //     return;
    //   }
    //   if (is_array($array[0])) {
    //     $is_array = true;
    //     $entities = &$array[0];
    //   } else {
    //     $is_array = false;
    //     $entities = &$array;
    //   }
    //   foreach ($entities as $key => &$curEntity) {
    //     // Test for permissions.
    //     if (!Tilmeld::checkPermissions($curEntity, Tilmeld::READ_ACCESS)) {
    //       unset($entities[$key]);
    //     }
    //   }
    //   unset($curEntity);
    // };

    // Filter entities being saved for user permissions.
    $CheckPermissionsSaveHook = function (&$array) {
      $entity = $array[0];
      if ((object) $entity !== $entity) {
        $array = false;
        return;
      }
      if (is_callable([$array[0], 'tilmeldSaveSkipAC']) && $array[0]->tilmeldSaveSkipAC()) {
        return;
      }
      // Test for permissions.
      if (!Tilmeld::checkPermissions($entity, Tilmeld::WRITE_ACCESS)) {
        $array = false;
      }
    };

    /*
     * Add the current user's "user", "group", and access control to a new entity.
     *
     * This occurs right before an entity is saved. It only alters the entity if:
     * - There is a user logged in.
     * - The entity is new (doesn't have a GUID.)
     * - The entity is not a user or group.
     *
     * If you want a new entity to have a different user and/or group than the
     * current user, you must first save it to the database, then change the
     * user/group, then save it again.
     *
     * Default access control is
     * - ac_user = Tilmeld::DELETE_ACCESS
     * - ac_group = Tilmeld::READ_ACCESS
     * - ac_other = Tilmeld::NO_ACCESS
     */
    $AddAccessHook = function (&$array) {
      $user = Entities\User::current();
      if (
          $user !== null
          && !isset($array[0]->guid)
          && !is_a($array[0], '\Tilmeld\Entities\User')
          && !is_a($array[0], '\Tilmeld\Entities\Group')
          && !is_a($array[0], '\SciActive\HookOverride_Tilmeld_Entities_User')
          && !is_a($array[0], '\SciActive\HookOverride_Tilmeld_Entities_Group')
        ) {
        $array[0]->user = $user;
        if (isset($user->group) && isset($user->group->guid)) {
      		$array[0]->group = $user->group;
        }
        if (!isset($array[0]->ac_user)) {
          $array[0]->ac_user = Tilmeld::DELETE_ACCESS;
        }
        if (!isset($array[0]->ac_group)) {
          $array[0]->ac_group = Tilmeld::READ_ACCESS;
        }
        if (!isset($array[0]->ac_other)) {
          $array[0]->ac_other = Tilmeld::NO_ACCESS;
        }
      }
    };

    Hook::addCallback('Nymph->getEntity', -10, $GetEntitiesHook);
    Hook::addCallback('Nymph->getEntities', -10, $GetEntitiesHook);
    // Hook::addCallback('Nymph->getEntity', 10, $CheckPermissionsReturnHook);
    // Hook::addCallback('Nymph->getEntities', 10, $CheckPermissionsReturnHook);

    Hook::addCallback('Nymph->saveEntity', -100, $AddAccessHook);
    Hook::addCallback('Nymph->saveEntity', -99, $CheckPermissionsSaveHook);

    Hook::addCallback('Nymph->deleteEntity', -99, $CheckPermissionsDeleteHook);
    Hook::addCallback('Nymph->deleteEntityById', -99, $CheckPermissionsDeleteHook);
  }
}