<?php

declare(strict_types=1);

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Tests targeting code paths that were not previously covered by the test suite.
 *
 * @internal
 */
final class MiscCoverageTest extends \PHPUnit\Framework\TestCase
{
    // -------------------------------------------------------------------------
    // setHtml() and getText() caching
    // -------------------------------------------------------------------------

    public function testGetTextIsCached(): void
    {
        $html2text = new Html2Text('<b>Hello</b>');
        $first = $html2text->getText();
        $second = $html2text->getText(); // should be served from cache
        static::assertSame($first, $second);
        static::assertSame('HELLO', $first);
    }

    public function testSetHtmlResetsConversionCache(): void
    {
        $html2text = new Html2Text('<b>Hello</b>');
        static::assertSame('HELLO', $html2text->getText());

        $html2text->setHtml('<b>World</b>');
        static::assertSame('WORLD', $html2text->getText()); // re-converted after setHtml
    }

    // -------------------------------------------------------------------------
    // width option (wordwrap)
    // -------------------------------------------------------------------------

    public function testWordwrapOption(): void
    {
        $html = '<p>Hello World this is a long sentence.</p>';
        $html2text = new Html2Text($html, ['width' => 10]);
        $output = $html2text->getText();

        foreach (\explode("\n", $output) as $line) {
            static::assertLessThanOrEqual(10, \strlen($line), "Line exceeds width: '{$line}'");
        }
    }

    // -------------------------------------------------------------------------
    // setPrefixForLinks
    // -------------------------------------------------------------------------

    public function testSetPrefixForLinks(): void
    {
        $html = '<a href="http://example.com">One</a> <a href="http://other.com">Two</a>';
        $html2text = new Html2Text($html, ['do_links' => 'table']);
        $html2text->setPrefixForLinks("\n\nReferences:\n-----------\n");

        $output = $html2text->getText();

        static::assertStringContainsString('References:', $output);
        static::assertStringNotContainsString('Links:', $output);
        static::assertStringContainsString('http://example.com', $output);
        static::assertStringContainsString('http://other.com', $output);
    }

    // -------------------------------------------------------------------------
    // Images
    // -------------------------------------------------------------------------

    public function testImageWithCidSrcShowsAltOnly(): void
    {
        // cid: src → $useSrc is false; alt exists → shows alt without URL
        $html = '<img src="cid:part1.abc@example.com" alt="Embedded image">';
        $html2text = new Html2Text($html);
        static::assertSame('Image: "Embedded image"', $html2text->getText());
    }

    public function testImageWithRelativeSrcShowsAltOnly(): void
    {
        // Relative src (not http/https//) → $useSrc is false; alt exists → shows alt without URL
        $html = '<img src="images/photo.jpg" alt="A photo">';
        $html2text = new Html2Text($html);
        static::assertSame('Image: "A photo"', $html2text->getText());
    }

    public function testImageWithProtocolRelativeSrcShowsAltAndUrl(): void
    {
        // Protocol-relative URL (//) → $useSrc is true
        $html = '<img src="//cdn.example.com/img.jpg" alt="CDN image">';
        $html2text = new Html2Text($html);
        static::assertSame('Image: "CDN image" [//cdn.example.com/img.jpg]', $html2text->getText());
    }

    public function testImageAltBeforeSrc(): void
    {
        // Attribute order: alt first, src second (exercises the first img regex pattern)
        $html = '<img alt="Logo" src="http://example.com/logo.png">';
        $html2text = new Html2Text($html);
        static::assertSame('Image: "Logo" [http://example.com/logo.png]', $html2text->getText());
    }

    // -------------------------------------------------------------------------
    // Links: relative URL without leading slash + baseUrl
    // -------------------------------------------------------------------------

    public function testBaseUrlWithRelativeLinkNoLeadingSlash(): void
    {
        // Link without leading '/' → baseUrl + '/' + link
        $html = '<a href="relative/page.html">Page</a>';
        $html2text = new Html2Text($html, ['do_links' => 'inline']);
        $html2text->setBaseUrl('http://example.com');
        static::assertSame('Page [http://example.com/relative/page.html]', $html2text->getText());
    }

    // -------------------------------------------------------------------------
    // Carriage return handling
    // -------------------------------------------------------------------------

    public function testWindowsCarriageReturnIsNormalized(): void
    {
        // \r\n → \n, then [\n\t]+ → space
        $html = "Line one\r\nLine two";
        $html2text = new Html2Text($html);
        static::assertSame('Line one Line two', $html2text->getText());
    }

    public function testBareCarriageReturnIsRemoved(): void
    {
        // \r alone → ''
        $html = "Line one\rLine two";
        $html2text = new Html2Text($html);
        static::assertSame('Line oneLine two', $html2text->getText());
    }

    // -------------------------------------------------------------------------
    // HTML element stripping
    // -------------------------------------------------------------------------

    public function testScriptTagIsStripped(): void
    {
        $html = 'Before<script>var x = 1; alert("XSS");</script>After';
        $html2text = new Html2Text($html);
        static::assertSame('BeforeAfter', $html2text->getText());
    }

    public function testScriptTagWithAttributesIsStripped(): void
    {
        $html = 'Before<script type="text/javascript" src="evil.js"></script>After';
        $html2text = new Html2Text($html);
        static::assertSame('BeforeAfter', $html2text->getText());
    }

    public function testStyleTagIsStripped(): void
    {
        $html = 'Before<style>body { color: red; }</style>After';
        $html2text = new Html2Text($html);
        static::assertSame('BeforeAfter', $html2text->getText());
    }

    public function testHeadTagIsStripped(): void
    {
        $html = '<head><title>Page Title</title><style>body{}</style></head>Content';
        $html2text = new Html2Text($html);
        static::assertSame('Content', $html2text->getText());
    }

    public function testHtmlCommentIsStripped(): void
    {
        $html = 'Before<!-- this is a comment -->After';
        $html2text = new Html2Text($html);
        static::assertSame('BeforeAfter', $html2text->getText());
    }

    // -------------------------------------------------------------------------
    // convertElement: replace option with custom delimiter
    // -------------------------------------------------------------------------

    public function testReplaceOptionWithCustomDelimiter(): void
    {
        // $options['replace'][2] specifies the regex delimiter (default '@')
        $html = '<h1>Hello/World</h1>';
        $html2text = new Html2Text(
            $html,
            [
                'elements' => [
                    'h1' => [
                        'case'    => Html2Text::OPTION_NONE,
                        'replace' => ['/', '-', '#'],
                    ],
                ],
            ]
        );
        static::assertSame('Hello-World', $html2text->getText());
    }
}
