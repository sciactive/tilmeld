<?php
/**
 * Verify a newly registered user's e-mail address.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$user = user::factory((int) $_REQUEST['id']);

if (!isset($user->guid)) {
	pines_notice('The specified user id is not available.');
	Tilmeld::print_login();
	return;
}

switch ($_REQUEST['type']) {
	case 'register':
	default:
		// Verify new user's email address.
		if (!isset($user->secret) || $_REQUEST['secret'] != $user->secret) {
			$module = new module('com_user', 'note_bad_verification', 'content');
			return;
		}

		if (Tilmeld::$config->unverified_access['value'])
			$user->groups = (array) $_->nymph->getEntities(array('class' => group, 'skip_ac' => true), array('&', 'tag' => array('com_user', 'group'), 'data' => array('default_secondary', true)));
		$user->enable();
		unset($user->secret);
		break;
	case 'change':
		// Email address change.
		if (!isset($user->new_email_secret) || $_REQUEST['secret'] != $user->new_email_secret)
			punt_user('The secret code given does not match this user.');

		if (Tilmeld::$config->email_usernames['value']) {
			$un_check = Tilmeld::check_username($user->new_email_address, $user->guid);
			if (!$un_check['result']) {
				$user->print_form();
				pines_notice($un_check['message']);
				return;
			}
		}
		$test = $_->nymph->getEntity(
				array('class' => user, 'skip_ac' => true),
				array('&',
					'tag' => array('com_user', 'user'),
					'match' => array('email', '/^'.preg_quote($user->new_email_address, '/').'$/i'),
					'!guid' => $user->guid
				)
			);
		if (isset($test)) {
			$user->print_form();
			pines_notice('There is already a user with that email address. Please use a different email.');
			return;
		}

		$user->email = $user->new_email_address;
		unset($user->new_email_address, $user->new_email_secret);
		break;
	case 'cancelchange':
		// Cancel an email address change.
		if (!isset($user->cancel_email_secret) || $_REQUEST['secret'] != $user->cancel_email_secret)
			punt_user('The secret code given does not match this user.');

		$user->email = $user->cancel_email_address;
		unset($user->new_email_address, $user->new_email_secret, $user->cancel_email_address, $user->cancel_email_secret);
		break;
}

if ($user->save()) {
	switch ($_REQUEST['type']) {
		case 'register':
		default:
			pines_log('Validated user ['.$user->username.']');
			Tilmeld::login($user);
			$notice = new module('com_user', 'note_welcome', 'content');
			if ( !empty($_REQUEST['url']) ) {
				pines_notice('Thank you. Your account has been verified.');
				pines_redirect(urldecode($_REQUEST['url']));
				return;
			}
			break;
		case 'change':
			pines_notice('Thank you. Your new email address has been verified.');
			pines_redirect(pines_url());
			break;
		case 'cancelchange':
			pines_notice('The email address change has been canceled.');
			pines_redirect(pines_url());
			break;
	}
} else {
	pines_error('Error saving user.');
}