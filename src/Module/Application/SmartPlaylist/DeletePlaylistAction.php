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

namespace Ampache\Module\Application\SmartPlaylist;

use Ampache\Config\AmpConfig;
use Ampache\Config\ConfigContainerInterface;
use Ampache\Model\Search;
use Ampache\Module\Application\ApplicationActionInterface;
use Ampache\Module\Util\Ui;
use Ampache\Module\Util\UiInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DeletePlaylistAction implements ApplicationActionInterface
{
    public const REQUEST_KEY = 'delete_playlist';

    private UiInterface $ui;

    private ResponseFactoryInterface $responseFactory;

    private ConfigContainerInterface $configContainer;

    public function __construct(
        UiInterface $ui,
        ResponseFactoryInterface $responseFactory,
        ConfigContainerInterface $configContainer
    ) {
        $this->ui              = $ui;
        $this->responseFactory = $responseFactory;
        $this->configContainer = $configContainer;
    }

    public function run(ServerRequestInterface $request): ?ResponseInterface
    {
        // Check rights
        $playlist = new Search((int) $_REQUEST['playlist_id'], 'song');
        if ($playlist->has_access()) {
            $playlist->delete();

            // Go elsewhere
            return $this->responseFactory
                ->createResponse()
                ->withHeader(
                    'Location',
                    sprintf(
                        '%s/browse.php?action=smartplaylist',
                        $this->configContainer->getWebPath()
                    )
                );
        }

        $this->ui->showHeader();

        Ui::access_denied();

        $this->ui->showQueryStats();
        $this->ui->showFooter();

        return null;
    }
}
