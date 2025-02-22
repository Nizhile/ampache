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
 */ ?>
            <tr>
                <td class="edit_dialog_content_header"><?php echo T_('TV Show Season') ?></td>
                <td><?php show_tvshow_season_select('tvshow_season', $libitem->season); ?></td>
            </tr>
            <tr>
                <td class="edit_dialog_content_header"><?php echo T_('Original Name') ?></td>
                <td><input type="text" name="original_name" value="<?php echo scrub_out($libitem->original_name); ?>" /></td>
            </tr>
            <tr>
                <td class="edit_dialog_content_header"><?php echo T_('Summary') ?></td>
                <td><textarea name="summary" cols="44" rows="4"><?php echo scrub_out($libitem->summary); ?></textarea></td>
            </tr>
            <tr>
                <td class="edit_dialog_content_header"><?php echo T_('Episode Number') ?></td>
                <td><input type="number" name="tvshow_episode" value="<?php echo scrub_out($libitem->episode_number); ?>" /></td>
            </tr>
