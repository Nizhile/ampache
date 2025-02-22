<?php
/* vim:set softtabstop=4 shiftwidth=4 expandtab: */
/**
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
 * Copyright Ampache.org, 2001-2023
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

/**
 * This page has a few tabs, as such we need to figure out which tab we are on
 * and display the information accordingly
 */

use Ampache\Config\AmpConfig;
use Ampache\Module\Util\UiInterface;
use Ampache\Repository\Model\User;
use Ampache\Module\System\Core;
use Ampache\Module\Util\Ui;

/** @var User $client */
/** @var array<string, mixed> $preferences */
/** @var UiInterface $ui */

/* HINT: Username FullName */
Ui::show_box_top(sprintf(T_('Editing %s Preferences'), $client->fullname), 'box box_preferences'); ?>
<form method="post" name="preferences" action="<?php echo AmpConfig::get('web_path'); ?>/preferences.php?action=admin_update_preferences" enctype="multipart/form-data">
<table class="tabledata striped-rows">
<colgroup>
    <col id="col_preference" />
    <col id="col_value" />
</colgroup>
<tr class="th-top">
    <th class="col_preference"><?php echo T_('Preference'); ?></th>
    <th class="col_value"><?php echo T_('Value'); ?></th>
</tr>
<?php foreach ($preferences as $pref) { ?>
        <tr>
                <td class="cel_preference"><?php echo T_($pref['description']); ?></td>
                <td class="cel_value">
                        <?php echo $ui->createPreferenceInput($pref['name'], $pref['value']); ?>
                </td>
        </tr>
<?php } ?>
<tr>
    <td>
    <div class="formValidation">
    <input class="button" type="submit" value="<?php echo T_('Update Preferences'); ?>" />
    <?php echo Core::form_register('update_preference'); ?>
    <input type="hidden" name="user_id" value="<?php echo scrub_out(Core::get_request('user_id')); ?>" />
    </div>
    </td>
    <td>&nbsp;</td>
</tr>
</table>
</form>

<?php Ui::show_box_bottom(); ?>
