<?php
/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 *  LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
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

declare(strict_types=1);

namespace Ampache\Module\Application\Update;

use Ampache\Config\ConfigContainerInterface;
use Ampache\Gui\GuiFactoryInterface;
use Ampache\Gui\TalFactoryInterface;
use Ampache\Module\Application\ApplicationActionInterface;
use Ampache\Module\Authorization\Access;
use Ampache\Module\System\AutoUpdate;
use Ampache\Module\System\Update;
use Ampache\Module\Util\Ui;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class UpdateAction implements ApplicationActionInterface
{
    public const REQUEST_KEY = 'update';
    
    private TalFactoryInterface $talFactory;

    private GuiFactoryInterface $guiFactory;

    private ResponseFactoryInterface $responseFactory;

    private ConfigContainerInterface $configContainer;

    private StreamFactoryInterface $streamFactory;

    public function __construct(
        TalFactoryInterface $talFactory,
        GuiFactoryInterface $guiFactory,
        ResponseFactoryInterface $responseFactory,
        ConfigContainerInterface $configContainer,
        StreamFactoryInterface $streamFactory
    ) {
        $this->talFactory      = $talFactory;
        $this->guiFactory      = $guiFactory;
        $this->responseFactory = $responseFactory;
        $this->configContainer = $configContainer;
        $this->streamFactory   = $streamFactory;
    }

    public function run(ServerRequestInterface $request): ?ResponseInterface
    {
        if ((string) filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS) == 'sources') {
            if (!Access::check('interface', 100)) {
                Ui::access_denied();

                return null;
            }

            set_time_limit(300);
            AutoUpdate::update_files();
            AutoUpdate::update_dependencies();

            return $this->responseFactory->createResponse()
                ->withHeader(
                    'Location',
                    $this->configContainer->getWebPath()
                );
        } else {
            Update::run_update();
        }

        $result = $this->talFactory->createTalView()
            ->setTemplate('update.xhtml')
            ->setContext(
                'UPDATE',
                $this->guiFactory->createUpdateViewAdapter()
            )
            ->render();
        
        return $this->responseFactory
            ->createResponse()
            ->withBody(
                $this->streamFactory->createStream($result)
            );
    }
}
