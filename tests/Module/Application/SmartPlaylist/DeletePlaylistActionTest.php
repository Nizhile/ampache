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

declare(strict_types=1);

namespace Ampache\Module\Application\SmartPlaylist;

use Ampache\Config\ConfigContainerInterface;
use Ampache\MockeryTestCase;
use Ampache\Model\ModelFactoryInterface;
use Ampache\Model\Search;
use Ampache\Module\Authorization\GuiGatekeeperInterface;
use Ampache\Module\Util\UiInterface;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;

class DeletePlaylistActionTest extends MockeryTestCase
{
    /** @var UiInterface|MockInterface|null */
    private ?MockInterface  $ui;

    /** @var ResponseFactoryInterface|MockInterface|null */
    private ?MockInterface $responseFactory;

    /** @var ConfigContainerInterface|MockInterface|null */
    private ?MockInterface $configContainer;

    /** @var ModelFactoryInterface|MockInterface|null */
    private ?MockInterface $modelFactory;

    private ?DeletePlaylistAction $subject;

    public function setUp(): void
    {
        $this->ui              = $this->mock(UiInterface::class);
        $this->responseFactory = $this->mock(ResponseFactoryInterface::class);
        $this->configContainer = $this->mock(ConfigContainerInterface::class);
        $this->modelFactory    = $this->mock(ModelFactoryInterface::class);

        $this->subject = new DeletePlaylistAction(
            $this->ui,
            $this->responseFactory,
            $this->configContainer,
            $this->modelFactory
        );
    }

    public function testRunReturnsAccessDeniedIfIdIsMissing(): void
    {
        $request    = $this->mock(ServerRequestInterface::class);
        $gatekeeper = $this->mock(GuiGatekeeperInterface::class);

        $request->shouldReceive('getQueryParams')
            ->withNoArgs()
            ->once()
            ->andReturn([]);

        $this->ui->shouldReceive('accessDenied')
            ->withNoArgs()
            ->once();

        $this->assertNull(
            $this->subject->run($request, $gatekeeper)
        );
    }

    public function testRunReturnsAccessDeniedIfNotAccessible(): void
    {
        $request    = $this->mock(ServerRequestInterface::class);
        $gatekeeper = $this->mock(GuiGatekeeperInterface::class);
        $search     = $this->mock(Search::class);

        $playlistId = 666;

        $request->shouldReceive('getQueryParams')
            ->withNoArgs()
            ->once()
            ->andReturn(['playlist_id' => (string) $playlistId]);

        $this->modelFactory->shouldReceive('createSearch')
            ->with($playlistId)
            ->once()
            ->andReturn($search);

        $search->shouldReceive('has_access')
            ->withNoArgs()
            ->once()
            ->andReturnFalse();

        $this->ui->shouldReceive('accessDenied')
            ->withNoArgs()
            ->once();

        $this->assertNull(
            $this->subject->run($request, $gatekeeper)
        );
    }

    public function testRunDeletesAndReturnsResponse(): void
    {
        $request    = $this->mock(ServerRequestInterface::class);
        $gatekeeper = $this->mock(GuiGatekeeperInterface::class);
        $search     = $this->mock(Search::class);
        $respone    = $this->mock(ResponseInterface::class);

        $playlistId = 666;
        $webPath    = 'some-path';

        $request->shouldReceive('getQueryParams')
            ->withNoArgs()
            ->once()
            ->andReturn(['playlist_id' => (string) $playlistId]);

        $this->modelFactory->shouldReceive('createSearch')
            ->with($playlistId)
            ->once()
            ->andReturn($search);

        $search->shouldReceive('has_access')
            ->withNoArgs()
            ->once()
            ->andReturnTrue();
        $search->shouldReceive('delete')
            ->withNoArgs()
            ->once();

        $this->responseFactory->shouldReceive('createResponse')
            ->with(StatusCode::FOUND)
            ->once()
            ->andReturn($respone);

        $respone->shouldReceive('withHeader')
            ->with(
                'Location',
                sprintf(
                    '%s/browse.php?action=smartplaylist',
                    $webPath
                )
            )
            ->once()
            ->andReturnSelf();

        $this->configContainer->shouldReceive('getWebPath')
            ->withNoArgs()
            ->once()
            ->andReturn($webPath);

        $this->assertSame(
            $respone,
            $this->subject->run($request, $gatekeeper)
        );
    }
}
