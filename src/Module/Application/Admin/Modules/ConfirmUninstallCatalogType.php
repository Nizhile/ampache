<?php
/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
 * Copyright 2001 - 2020 Ampache.org
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

namespace Ampache\Module\Application\Admin\Modules;

use Ampache\Config\ConfigContainerInterface;
use Ampache\Module\Application\ApplicationActionInterface;
use Ampache\Module\System\Core;
use Ampache\Module\Util\Ui;
use Ampache\Module\Util\UiInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ConfirmUninstallCatalogType implements ApplicationActionInterface
{
    public const REQUEST_KEY = 'confirm_uninstall_catalog_type';

    private UiInterface $ui;

    private ConfigContainerInterface $configContainer;

    public function __construct(
        UiInterface $ui,
        ConfigContainerInterface $configContainer
    ) {
        $this->ui              = $ui;
        $this->configContainer = $configContainer;
    }

    public function run(ServerRequestInterface $request): ?ResponseInterface
    {
        if (!Core::get_global('user')->has_access(100)) {
            Ui::access_denied();

            return null;
        }

        $this->ui->showHeader();

        $type  = (string) scrub_in(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS));
        $url   = sprintf(
            '%s/admin/modules.php?action=uninstall_catalog_type&amp;type=%s',
            $this->configContainer->getWebPath(),
            $type
        );
        $title = T_('Are You Sure?');
        $body  = T_('This will disable the Catalog module');
        show_confirmation($title, $body, $url, 1);

        $this->ui->showQueryStats();
        $this->ui->showFooter();

        return null;
    }
}
