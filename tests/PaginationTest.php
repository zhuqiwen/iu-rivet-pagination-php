<?php

namespace Edu\IU\VPCM\Rivet;
require_once __DIR__ . '/../src/Pagination.php';

use Edu\IU\VPCM\Rivet\Pagination;
use PHPUnit\Framework\TestCase;


class PaginationTest extends TestCase{

    public function testGetCurrentPage()
    {
        $p = new Pagination(30);
        self::assertSame(1, $p->getCurrentPage());
        $_GET['page'] = 5;
        self::assertSame(5, $p->getCurrentPage());
        unset($p);

        $p = new Pagination(30, ['pageKey' => 'myPage']);
        self::assertSame(1, $p->getCurrentPage());
        $_GET['myPage'] = 5;
        self::assertSame(5, $p->getCurrentPage());
    }
}