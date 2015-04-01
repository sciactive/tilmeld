<?php
/**
 * Show the currently logged in user.
 *
 * @package Tilmeld
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */

// Don't show it if the user isn't logged in.
if (!gatekeeper()) {
	$this->detach();
	return;
}

$this->title = 'Current User';

// Make sure options are safe.
switch ($this->text_align) {
	case 'left':
	case 'right':
	case 'center':
	case 'justify':
		break;
	default:
		$this->text_align = 'inherit';
}
switch ($this->font_style) {
	case 'normal':
	case 'italic':
	case 'oblique':
		break;
	default:
		$this->font_style = 'inherit';
}
$this->margin_top = (int) $this->margin_top;
$this->margin_bottom = (int) $this->margin_bottom;
$this->margin_left = (int) $this->margin_left;
$this->margin_right = (int) $this->margin_right;
$this->padding_top = (int) $this->padding_top;
$this->padding_bottom = (int) $this->padding_bottom;
$this->padding_left = (int) $this->padding_left;
$this->padding_right = (int) $this->padding_right;

if (empty($this->text))
	$this->text = 'Logged in as #name# [#username#].';

$this->text = h(str_replace(['#name#', '#username#'], [$_SESSION['tilmeld_user']->name, $_SESSION['tilmeld_user']->username], $this->text));

?>
<div style="text-align: <?php echo $this->text_align; ?>; font-style: <?php echo $this->font_style; ?>; margin: <?php echo "{$this->margin_top}px {$this->margin_right}px {$this->margin_bottom}px {$this->margin_left}px"; ?>; padding: <?php echo "{$this->padding_top}px {$this->padding_right}px {$this->padding_bottom}px {$this->padding_left}px"; ?>;">
	<?php echo $this->text; ?>
</div>