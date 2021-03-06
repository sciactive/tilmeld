<?php namespace Tilmeld\Entities\Mail;

// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * Recover Account Password
 *
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @see http://tilmeld.org/
 */
class RecoverPassword extends \uMailPHP\Definition {
  public static $cname = 'Recover Password';
  public static $description = 'This email is sent when a user can\'t access their account so they can recover their password.';
  public static $expectsRecipient = true;
  public static $macros = [
    'recover_code' => 'The code to reset their password.',
    'time_limit' => 'How long a recovery request is valid.',
    'to_phone' => 'The recipient\'s phone number.',
    'to_fax' => 'The recipient\'s fax number.',
    'to_timezone' => 'The recipient\'s timezone.',
  ];

  public static function getMacro($name) {
  }

  public static function getSubject() {
    return 'Hey #to_first_name#, here\'s an account recovery code for #site_name#.';
  }

  public static function getHTML() {
    return file_get_contents(__DIR__.'/html/RecoverPassword.html');
  }
}
