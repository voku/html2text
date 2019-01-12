<?php

namespace Html2Text;

use voku\Html2Text\Html2Text;

/**
 * Class SpaceTest
 *
 * @internal
 */
final class SpaceTest extends \PHPUnit\Framework\TestCase
{
    public function testSpaces()
    {
        $html = new Html2Text('This&nbsp;is&nbsp;a&nbsp;text&nbsp;with&nbsp;a&nbsp;lot&nbsp;of&nbsp;&nbsp;spaces.');

        static::assertSame('This is a text with a lot of  spaces.', $html->getText());
    }
}
