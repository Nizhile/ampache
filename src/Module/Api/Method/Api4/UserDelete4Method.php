<?php

/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 *  LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
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

declare(strict_types=0);

namespace Ampache\Module\Api\Method\Api4;

use Ampache\Repository\Model\User;
use Ampache\Module\Api\Api4;

/**
 * Class UserDelete4Method
 */
final class UserDelete4Method
{
    public const ACTION = 'user_delete';

    /**
     * user_delete
     * MINIMUM_API_VERSION=400001
     *
     * Delete an existing user.
     * Takes the username in parameter.
     *
     * @param array $input
     * @param User $user
     * username = (string) $username)
     * @return boolean
     */
    public static function user_delete(array $input, User $user): bool
    {
        if (!Api4::check_access('interface', 100, $user->id, 'user_delete', $input['api_format'])) {
            return false;
        }
        if (!Api4::check_parameter($input, array('username'), self::ACTION)) {
            return false;
        }
        $username = $input['username'];
        $del_user = User::get_from_username($username);
        // don't delete yourself or admins
        if ($del_user instanceof User && $del_user->username !== $user->username && $del_user->access < 100 && $del_user->delete()) {
            Api4::message('success', 'successfully deleted: ' . $username, null, $input['api_format']);

            return true;
        }
        Api4::message('error', 'failed to delete: ' . $username, '400', $input['api_format']);

        return false;
    } // user_delete
}
