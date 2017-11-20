<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\Util;

abstract class BaseHTMLTestCase extends \PHPUnit_Framework_TestCase
{
    protected function assertEqualsHtml(string $expected, string $actual): void
    {
        $from = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s'];
        $to = ['>', '<', '\\1', '><'];
        $this->assertEquals(
            str_replace('><', ">".PHP_EOL."<", preg_replace($from, $to, $expected)),
            str_replace('><', ">".PHP_EOL."<", preg_replace($from, $to, $actual))
        );
    }
}
