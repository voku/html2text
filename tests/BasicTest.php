<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class BasicTest
 *
 * @internal
 */
final class BasicTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    public $inputLink = '<a href="http://example.com">Link text</a>';

    /**
     * @return array
     */
    public function basicDataProvider(): array
    {
        return [
            'Readme usage' => [
                'html'     => 'Hello, &quot;<b>world</b>&quot;',
                'expected' => 'Hello, "WORLD"',
            ],
            'No stripslashes on HTML content' => [
                // HTML content does not escape slashes, therefore nor should we.
                'html'     => 'Hello, \"<b>world</b>\"',
                'expected' => 'Hello, \"WORLD\"',
            ],
            'Empty b tag in HTML content' => [
                'html'     => 'Hello, <b></b>',
                'expected' => 'Hello,',
            ],
            'Zero is not empty' => [
                'html'     => '0',
                'expected' => '0',
            ],
            'Paragraph with whitespace wrapping it' => [
                'html'     => 'Foo <p>Bar</p> Baz',
                'expected' => "Foo\n\nBar\n\nBaz",
            ],
            'Paragraph text with linebreak flat' => [
                'html'     => '<p>Foo<br/>Bar</p>',
                'expected' => "Foo\nBar",
            ],
            'Paragraph text with linebreak formatted with newline' => [
                'html'     => "\n<p>\n    Foo<br/>\n    Bar\n</p>\n",
                'expected' => "Foo\nBar",
            ],
            'Paragraph text with linebreak formatted whth newline, but without whitespace' => [
                'html'     => "<p>Foo<br/>\nBar</p>\n\n<p>lall</p>\n",
                'expected' => "Foo\nBar\n\nlall",
            ],
            'Paragraph text with linebreak formatted with indentation' => [
                'html'     => "\n<p>\n    Foo<br/>Bar\n</p>\nlall\n",
                'expected' => "Foo\nBar\n\nlall",
            ],
            '<br /> within <strong> prevents <strong> from being converted' => [
                'html'     => '<strong>This would<br />not be converted.</strong>&nbsp;<strong>But this would, though</strong>',
                'expected' => "THIS WOULD\nNOT BE CONVERTED. BUT THIS WOULD, THOUGH",
            ],
        ];
    }

    /**
     * @dataProvider basicDataProvider
     *
     * @param string $html
     * @param string $expected
     */
    public function testBasic($html, $expected)
    {
        $html2Text = new Html2Text($html);
        static::assertSame($expected, $html2Text->getText());
    }

    public function testBasicUsageInReadme()
    {
        $html = new Html2Text('Hello, &quot;<b>world</b>&quot;');

        static::assertSame('Hello, "WORLD"', $html->getText());
    }

    public function testDel()
    {
        $html = 'My <del>Résumé</del> Curriculum Vitæ';
        $expected = 'My ~~Résumé~~ Curriculum Vitæ';
        $html2text = new Html2Text($html);

        static::assertSame($expected, $html2text->getText());
    }

    /**
     * testDoLinksInline
     */
    public function testDoLinksInline()
    {
        $expected_output = <<<EOT
Link text [http://example.com]
EOT;

        $html2text = new Html2Text($this->inputLink, ['do_links' => 'inline']);
        $output = $html2text->getText();

        static::assertSame($expected_output, $output);
    }

    /**
     * testDoLinksNone
     */
    public function testDoLinksNone()
    {
        $expected_output = <<<EOT
Link text
EOT;

        $html2text = new Html2Text($this->inputLink, ['do_links' => 'none']);
        $output = $html2text->getText();

        static::assertSame($output, $expected_output);
    }

    public function testIns()
    {
        $html = 'This is <ins>inserted</ins>';
        $expected = 'This is _inserted_';
        $html2text = new Html2Text($html);

        static::assertSame($expected, $html2text->getText());
    }

    public function testNewLines()
    {
        $html = <<<EOT
<p>Between this and</p>
<p>foo&zwnj;bar</p>
<p>this paragraph there should be only one newline</p>
<h1>and this also goes for headings</h1>
<h1 style="color: red;">test</h1>
test
<br>
lall
EOT;
        $expected = <<<EOT
Between this and

foo‌bar

this paragraph there should be only one newline

AND THIS ALSO GOES FOR HEADINGS

TEST

test
lall
EOT;
        $html2text = new Html2Text($html);
        $output = $html2text->getText();
        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected), $output);
    }

    public function testIssue17()
    {
        $html = '
        <div class="container wide-1366 full-width-mobile px-lg-0 position-relative fh mt-80 pb-5">
            <div class="row mw-100">
                <div class="d-none d-sm-block col-sm-6 col-xl-8 text-center">
                    <img style="max-width: 200%" src="img/pluma.png">
                </div>
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="floating-text right-side">
                        <h1 class="display-3 mb-5 pb-60 border-b-2">
                            <span class="strong-text-red">What</span> is<br>Writing Lab?
                        </h1>
                        <p>
                            Writing Lab is an initiative within TecLabs that is dedicated to the development of the culture of research in Educational Innovation and to the enhancement of the academic production of the faculty members at Tecnologico de Monterrey.
                        </p>
                    </div>
                </div>
            </div>
            <!-- figures divider -->
            <div class="row mw-100 position-relative">
                <div class="bg-figures-wrapper">
                    <img src="img/bg_figures.png">
                </div>
            </div>
        </div>
        ';

        $expected = <<<EOT
WHAT IS
WRITING LAB?

Writing Lab is an initiative within TecLabs that is dedicated to the development of the culture of research in Educational Innovation and to the enhancement of the academic production of the faculty members at Tecnologico de Monterrey.
EOT;

        $html2text = new Html2Text($html);
        $output = $html2text->getText();
        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected), $output);
    }
}
