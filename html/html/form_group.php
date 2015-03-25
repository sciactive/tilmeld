<?php
/**
 * Provides a form for the user to edit a group.
 *
 * @package Tilmeld
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
$this->title = (!isset($this->entity->guid)) ? 'Editing New Group' : 'Editing ['.h($this->entity->groupname).']';
$this->note = 'Provide group details in this form.';
$_->com_pgrid->load();
//$_->com_jstree->load();
$_->uploader->load();
?>
<script type="text/javascript">
	$_(function(){
		// Attributes
		var attributes = $("#p_muid_tab_attributes input[name=attributes]");
		var attributes_table = $("#p_muid_tab_attributes .attributes_table");
		var attribute_dialog = $("#p_muid_tab_attributes .attribute_dialog");

		attributes_table.pgrid({
			pgrid_paginate: false,
			pgrid_toolbar: true,
			pgrid_toolbar_contents : [
				{
					type: 'button',
					text: 'Add Attribute',
					extra_class: 'picon picon-list-add',
					selection_optional: true,
					click: function(){
						attribute_dialog.dialog('open');
					}
				},
				{
					type: 'button',
					text: 'Remove Attribute',
					extra_class: 'picon picon-list-remove',
					click: function(e, rows){
						rows.pgrid_delete();
						update_attributes();
					}
				}
			],
			pgrid_view_height: "300px"
		});

		// Attribute Dialog
		attribute_dialog.dialog({
			bgiframe: true,
			autoOpen: false,
			modal: true,
			width: 500,
			buttons: {
				"Done": function(){
					var cur_attribute_name = $("#p_muid_cur_attribute_name").val();
					var cur_attribute_value = $("#p_muid_cur_attribute_value").val();
					if (cur_attribute_name == "" || cur_attribute_value == "") {
						alert("Please provide both a name and a value for this attribute.");
						return;
					}
					var new_attribute = [{
						key: null,
						values: [
							$_.safe(cur_attribute_name),
							$_.safe(cur_attribute_value)
						]
					}];
					attributes_table.pgrid_add(new_attribute);
					$(this).dialog('close');
				}
			},
			close: function(){
				update_attributes();
			}
		});

		var update_attributes = function(){
			$("#p_muid_cur_attribute_name").val("");
			$("#p_muid_cur_attribute_value").val("");
			attributes.val(JSON.stringify(attributes_table.pgrid_get_all_rows().pgrid_export_rows()));
		};

		update_attributes();
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_user', 'savegroup')); ?>">
	<ul class="nav nav-tabs" style="clear: both;">
		<li class="active"><a href="#p_muid_tab_general" data-toggle="tab">General</a></li>
		<li><a href="#p_muid_tab_logo" data-toggle="tab">Logo</a></li>
		<li><a href="#p_muid_tab_location" data-toggle="tab">Address</a></li>
		<?php if ($this->display_abilities) { ?>
		<li><a href="#p_muid_tab_abilities" data-toggle="tab">Abilities</a></li>
		<?php } if (\Tilmeld\Tilmeld::$config->conditional_groups['value'] && $this->display_conditions) { ?>
		<li><a href="#p_muid_tab_conditions" data-toggle="tab">Conditions</a></li>
		<?php } ?>
		<li><a href="#p_muid_tab_attributes" data-toggle="tab">Attributes</a></li>
	</ul>
	<div id="p_muid_tabs" class="tab-content">
		<div class="tab-pane active" id="p_muid_tab_general">
			<?php if (isset($this->entity->guid)) { ?>
			<div class="date_info" style="float: right; text-align: right;">
				<div>Created: <span class="date"><?php e(format_date($this->entity->cdate, 'full_short')); ?></span></div>
				<div>Modified: <span class="date"><?php e(format_date($this->entity->mdate, 'full_short')); ?></span></div>
			</div>
			<?php } ?>
			<?php if ($this->display_username) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Group Name</span>
					<input class="pf-field form-control" type="text" name="groupname" size="24" value="<?php e($this->entity->groupname); ?>" /></label>
			</div>
			<?php } ?>
			<div class="pf-element">
				<label><span class="pf-label">Display Name</span>
					<input class="pf-field form-control" type="text" name="name" size="24" value="<?php e($this->entity->name); ?>" /></label>
			</div>
			<?php if ($this->display_enable) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Enabled</span>
					<input class="pf-field" type="checkbox" name="enabled" value="ON"<?php echo $this->entity->hasTag('enabled') ? ' checked="checked"' : ''; ?> /></label>
			</div>
			<?php } ?>
			<div class="pf-element">
				<label><span class="pf-label">Email</span>
					<input class="pf-field form-control" type="email" name="email" size="24" value="<?php e($this->entity->email); ?>" /></label>
			</div>
			<?php if (isset($_->com_mailer)) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Mailing List</span>
					<input class="pf-field" type="checkbox" name="mailing_list" value="ON"<?php echo $_->com_mailer->unsubscribe_query($this->entity->email) ? '' : ' checked="checked"'; ?> /> Subscribe to the mailing list.</label>
			</div>
			<?php } ?>
			<div class="pf-element">
				<label><span class="pf-label">Phone 1</span>
					<input class="pf-field form-control" type="tel" name="phone" size="24" value="<?php e(format_phone($this->entity->phone)); ?>" onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Phone 2</span>
					<input class="pf-field form-control" type="tel" name="phone2" size="24" value="<?php e(format_phone($this->entity->phone2)); ?>" onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Fax</span>
					<input class="pf-field form-control" type="tel" name="fax" size="24" value="<?php e(format_phone($this->entity->fax)); ?>" onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" /></label>
			</div>
			<div class="pf-element">
				<label>
					<span class="pf-label">Timezone</span>
					<span class="pf-note">Users in this group will inherit this timezone. Primary group has priority over secondary groups.</span>
					<select class="pf-field form-control" name="timezone">
						<option value="">--System Default--</option>
						<?php
						$tz = DateTimeZone::listIdentifiers();
						sort($tz);
						foreach ($tz as $cur_tz) {
							?><option value="<?php e($cur_tz); ?>"<?php echo $this->entity->timezone == $cur_tz ? ' selected="selected"' : ''; ?>><?php e($cur_tz); ?></option><?php
						} ?>
					</select>
				</label>
			</div>
			<div class="pf-element">
				<label>
					<span class="pf-label">Parent</span>
					<select class="pf-field form-control" name="parent">
						<option value="none">--No Parent--</option>
						<?php
						\Tilmeld\Tilmeld::groupSort($this->group_array, 'name');
						foreach ($this->group_array as $cur_group) {
							?><option value="<?php e($cur_group->guid); ?>"<?php echo $cur_group->is($this->entity->parent) ? ' selected="selected"' : ''; ?>><?php e(str_repeat('->', $cur_group->get_level())." {$cur_group->name} [{$cur_group->groupname}]"); ?></option><?php
						} ?>
					</select>
				</label>
			</div>
			<?php if ($this->display_default) { ?>
			<div class="pf-element">
				<label><span class="pf-label">New User Primary Group</span>
					<span class="pf-note">Default primary group for newly registered users.</span>
					<input class="pf-field" type="checkbox" name="default_primary" value="ON"<?php echo $this->entity->default_primary ? ' checked="checked"' : ''; ?> /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">New User Secondary Group</span>
					<span class="pf-note">Default secondary group for newly registered users.</span>
					<input class="pf-field" type="checkbox" name="default_secondary" value="ON"<?php echo $this->entity->default_secondary ? ' checked="checked"' : ''; ?> /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Unverified User Secondary Group</span>
					<span class="pf-note">Default secondary group for newly registered users who haven't verified their email. See config for details.</span>
					<input class="pf-field" type="checkbox" name="unverified_secondary" value="ON"<?php echo $this->entity->unverified_secondary ? ' checked="checked"' : ''; ?> /></label>
			</div>
			<?php } ?>
			<br class="pf-clearing" />
		</div>
		<div class="tab-pane" id="p_muid_tab_logo">
			<div class="pf-element">
				<span class="pf-label"><?php echo (isset($this->entity->logo)) ? 'Currently Set Logo' : 'Inherited Logo'; ?></span>
				<div class="pf-group">
					<span class="pf-field"><img src="<?php e($this->entity->get_logo()); ?>" alt="Group Logo" /></span>
					<?php if (isset($this->entity->logo)) { ?>
					<br />
					<label><span class="pf-field"><input class="pf-field" type="checkbox" name="remove_logo" value="ON" />Remove this logo.</span></label>
					<?php } ?>
				</div>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Change Logo</span>
					<input class="pf-field form-control puploader" type="text" name="image" /></label>
			</div>
			<br class="pf-clearing" />
		</div>
		<div class="tab-pane" id="p_muid_tab_location">
			<div class="pf-element">
				<script type="text/javascript">
					$_(function(){
						var address_us = $("#p_muid_address_us");
						var address_international = $("#p_muid_address_international");
						$("#p_muid_form [name=address_type]").change(function(){
							var address_type = $(this);
							if (address_type.is(":checked") && address_type.val() == "us") {
								address_us.show();
								address_international.hide();
							} else if (address_type.is(":checked") && address_type.val() == "international") {
								address_international.show();
								address_us.hide();
							}
						}).change();
					});
				</script>
				<span class="pf-label">Address Type</span>
				<label><input class="pf-field" type="radio" name="address_type" value="us"<?php echo ($this->entity->address_type == 'us') ? ' checked="checked"' : ''; ?> /> US</label>
				<label><input class="pf-field" type="radio" name="address_type" value="international"<?php echo $this->entity->address_type == 'international' ? ' checked="checked"' : ''; ?> /> International</label>
			</div>
			<div id="p_muid_address_us" style="display: none;">
				<div class="pf-element">
					<label><span class="pf-label">Address 1</span>
						<input class="pf-field form-control" type="text" name="address_1" size="24" value="<?php e($this->entity->address_1); ?>" /></label>
				</div>
				<div class="pf-element">
					<label><span class="pf-label">Address 2</span>
						<input class="pf-field form-control" type="text" name="address_2" size="24" value="<?php e($this->entity->address_2); ?>" /></label>
				</div>
				<div class="pf-element">
					<span class="pf-label">City, State</span>
					<input class="pf-field form-control" type="text" name="city" size="15" value="<?php e($this->entity->city); ?>" />
					<select class="pf-field form-control" name="state">
						<option value="">None</option>
						<?php foreach ([
								'AL' => 'Alabama',
								'AK' => 'Alaska',
								'AZ' => 'Arizona',
								'AR' => 'Arkansas',
								'CA' => 'California',
								'CO' => 'Colorado',
								'CT' => 'Connecticut',
								'DE' => 'Delaware',
								'DC' => 'DC',
								'FL' => 'Florida',
								'GA' => 'Georgia',
								'HI' => 'Hawaii',
								'ID' => 'Idaho',
								'IL' => 'Illinois',
								'IN' => 'Indiana',
								'IA' => 'Iowa',
								'KS' => 'Kansas',
								'KY' => 'Kentucky',
								'LA' => 'Louisiana',
								'ME' => 'Maine',
								'MD' => 'Maryland',
								'MA' => 'Massachusetts',
								'MI' => 'Michigan',
								'MN' => 'Minnesota',
								'MS' => 'Mississippi',
								'MO' => 'Missouri',
								'MT' => 'Montana',
								'NE' => 'Nebraska',
								'NV' => 'Nevada',
								'NH' => 'New Hampshire',
								'NJ' => 'New Jersey',
								'NM' => 'New Mexico',
								'NY' => 'New York',
								'NC' => 'North Carolina',
								'ND' => 'North Dakota',
								'OH' => 'Ohio',
								'OK' => 'Oklahoma',
								'OR' => 'Oregon',
								'PA' => 'Pennsylvania',
								'RI' => 'Rhode Island',
								'SC' => 'South Carolina',
								'SD' => 'South Dakota',
								'TN' => 'Tennessee',
								'TX' => 'Texas',
								'UT' => 'Utah',
								'VT' => 'Vermont',
								'VA' => 'Virginia',
								'WA' => 'Washington',
								'WV' => 'West Virginia',
								'WI' => 'Wisconsin',
								'WY' => 'Wyoming',
								'AA' => 'Armed Forces (AA)',
								'AE' => 'Armed Forces (AE)',
								'AP' => 'Armed Forces (AP)'
							] as $key => $cur_state) { ?>
						<option value="<?php echo $key; ?>"<?php echo $this->entity->state == $key ? ' selected="selected"' : ''; ?>><?php echo $cur_state; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="pf-element">
					<label><span class="pf-label">Zip</span>
						<input class="pf-field form-control" type="text" name="zip" size="24" value="<?php e($this->entity->zip); ?>" /></label>
				</div>
			</div>
			<div id="p_muid_address_international" style="display: none;">
				<div class="pf-element pf-full-width">
					<label><span class="pf-label">Address</span>
						<span class="pf-group pf-full-width">
							<span class="pf-field" style="display: block;">
								<textarea style="width: 100%;" rows="3" cols="35" name="address_international"><?php e($this->entity->address_international); ?></textarea>
							</span>
						</span></label>
				</div>
			</div>
			<br class="pf-clearing" />
		</div>
		<?php if ( $this->display_abilities ) { ?>
		<div class="tab-pane" id="p_muid_tab_abilities">
			<style type="text/css" scoped="scoped">
				#p_muid_tab_abilities .abilities_accordion {
					margin-bottom: .2em;
				}
				#p_muid_tab_abilities .abilities_accordion .panel-heading .component {
					float: right;
				}
			</style>
			<script type="text/javascript">
				$_(function(){
					var sections = $("#p_muid_tab_abilities .abilities_accordion .panel-collapse");
					$("#p_muid_tab_abilities").on("click", "button.expand_all", function(){
						sections.collapse("show");
					}).on("click", "button.collapse_all", function(){
						sections.collapse("hide");
					});
				});
			</script>
			<div class="pf-element pf-full-width ui-helper-clearfix">
				<div class="btn-group" style="float: right; clear: both;">
					<button type="button" class="expand_all btn btn-default">Expand All</button>
					<button type="button" class="collapse_all btn btn-default">Collapse All</button>
				</div>
			</div>
			<br class="pf-clearing" />
			<?php foreach ($this->sections as $cur_section) {
				if ($cur_section == 'system')
					$section_abilities = (array) $_->info->abilities;
				else
					$section_abilities = (array) $_->info->$cur_section->abilities;
				if (!$section_abilities) continue; ?>
			<div class="abilities_accordion panel-group">
				<div class="panel panel-default">
					<a class="panel-heading ui-helper-clearfix" href="javascript:void(0);" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
						<big class="panel-title"><?php echo ($cur_section == 'system') ? h($_->info->name) : h($_->info->$cur_section->name); ?> <span class="component"><?php e($cur_section); ?></span></big>
					</a>
					<div class="panel-collapse collapse">
						<div class="panel-body clearfix">
							<div class="pf-element">
								<?php foreach ($section_abilities as $cur_ability) { ?>
								<label>
									<input type="checkbox" name="<?php e($cur_section); ?>[]" value="<?php e($cur_ability[0]); ?>" <?php echo (array_search("{$cur_section}/{$cur_ability[0]}", $this->entity->abilities) !== false) ? 'checked="checked" ' : ''; ?>/>
									<span title="<?php e("{$cur_section}/{$cur_ability[0]}"); ?>" class="label label-info"><?php e($cur_ability[1]); ?></span>&nbsp;<small><?php e($cur_ability[2]); ?></small>
								</label>
								<br class="pf-clearing" />
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<br class="pf-clearing" />
		</div>
		<?php } if (\Tilmeld\Tilmeld::$config->conditional_groups['value'] && $this->display_conditions) { ?>
		<div class="tab-pane" id="p_muid_tab_conditions">
			<div class="pf-element pf-heading">
				<h3>Ability Conditions</h3>
				<p>Users will only inherit abilities from this group if these conditions are met.</p>
			</div>
			<div class="pf-element pf-full-width">
				<?php
				$module = new module('system', 'conditions');
				$module->conditions = $this->entity->conditions;
				echo $module->render();
				unset($module);
				?>
			</div>
			<br class="pf-clearing" />
		</div>
		<?php } ?>
		<div class="tab-pane" id="p_muid_tab_attributes">
			<div class="pf-element pf-full-width">
				<table class="attributes_table">
					<thead>
						<tr><th>Name</th><th>Value</th></tr>
					</thead>
					<tbody>
						<?php foreach ($this->entity->attributes as $cur_attribute) { ?>
						<tr><td><?php e($cur_attribute['name']); ?></td><td><?php e($cur_attribute['value']); ?></td></tr>
						<?php } ?>
					</tbody>
				</table>
				<input type="hidden" name="attributes" />
			</div>
			<div class="attribute_dialog" style="display: none;" title="Add an Attribute">
				<div class="pf-form">
					<div class="pf-element">
						<label><span class="pf-label">Name</span>
							<input class="pf-field form-control" type="text" id="p_muid_cur_attribute_name" size="24" /></label>
					</div>
					<div class="pf-element">
						<label><span class="pf-label">Value</span>
							<input class="pf-field form-control" type="text" id="p_muid_cur_attribute_value" size="24" /></label>
					</div>
				</div>
				<br style="clear: both; height: 1px;" />
			</div>
			<br class="pf-clearing" />
		</div>
	</div>

	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_user', 'listgroups'))); ?>);" value="Cancel" />
	</div>
</form>