<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\ListElement;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\Pullquote;
use Facebook\InstantArticles\Elements\RelatedArticles;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Footer;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromPropertiesThrowsException()
    {
        $this->setExpectedException(
            'Exception',
            'All Rule class extensions should implement the Rule::createFrom($configuration) method'
        );

        Rule::createFrom(array());
    }

    public function testCreateThrowsException()
    {
        $this->setExpectedException(
            'Exception',
            'All Rule class extensions should implement the Rule::create() method'
        );

        Rule::create();
    }

    public function testMatchSimple()
    {
        $configJSON =
            '{'.
                '"class": "ParagraphRule",'.
                '"selector" : "p"'.
            '}';
        $config = json_decode($configJSON, true);
        $rule = ParagraphRule::createFrom($config);

        $document = new \DOMDocument('utf-8');
        $paragraph = $document->createElement('p');
        $paragraph->appendChild($document->createTextNode('simple paragraph'));

        $this->assertTrue($rule->matchesContext(InstantArticle::create()));
        $this->assertTrue($rule->matchesNode($paragraph));
    }

    public function testDoesntMatchContext()
    {
        $configJSON =
            '{'.
                '"class": "ParagraphRule",'.
                '"selector" : "p"'.
            '}';
        $config = json_decode($configJSON, true);
        $rule = ParagraphRule::createFrom($config);

        $document = new \DOMDocument('utf-8');
        $paragraph = $document->createElement('p');
        $paragraph->appendChild($document->createTextNode('simple paragraph'));

        $this->assertFalse($rule->matchesContext(Header::create()));
        $this->assertTrue($rule->matchesNode($paragraph));
    }

    public function testDoesntMatchNode()
    {
        $configJSON =
            '{'.
                '"class": "ParagraphRule",'.
                '"selector" : "p"'.
            '}';
        $config = json_decode($configJSON, true);
        $rule = ParagraphRule::createFrom($config);

        $document = new \DOMDocument('utf-8');
        $paragraph = $document->createElement('b');
        $paragraph->appendChild($document->createTextNode('simple bold content'));

        $this->assertTrue($rule->matchesContext(InstantArticle::create()));
        $this->assertFalse($rule->matchesNode($paragraph));
    }

    public function testMatchingContexts()
    {
        $this->assertTrue(AdRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(H1Rule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(ListItemRule::create()->matchesContext(ListElement::createOrdered()));
        $this->assertTrue(AnalyticsRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(H2Rule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(MapRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(AnchorRule::create()->matchesContext(Paragraph::create()));
        $this->assertTrue(HeaderAdRule::create()->matchesContext(Header::create()));
        $this->assertTrue(ParagraphFooterRule::create()->matchesContext(Footer::create()));
        $this->assertTrue(AudioRule::create()->matchesContext(Image::create()));
        $this->assertTrue(HeaderImageRule::create()->matchesContext(Header::create()));
        $this->assertTrue(ParagraphRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(AuthorRule::create()->matchesContext(Header::create()));
        $this->assertTrue(HeaderKickerRule::create()->matchesContext(Header::create()));
        $this->assertTrue(PassThroughRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(BlockquoteRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(HeaderRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(PullquoteCiteRule::create()->matchesContext(Pullquote::create()));
        $this->assertTrue(BoldRule::create()->matchesContext(Paragraph::create()));
        $this->assertTrue(HeaderSubTitleRule::create()->matchesContext(Header::create()));
        $this->assertTrue(PullquoteRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(CaptionCreditRule::create()->matchesContext(Caption::create()));
        $this->assertTrue(HeaderTitleRule::create()->matchesContext(Header::create()));
        $this->assertTrue(RelatedArticlesRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(CaptionRule::create()->matchesContext(Image::create()));
        $this->assertTrue(IgnoreRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(RelatedItemRule::create()->matchesContext(RelatedArticles::create()));
        $this->assertTrue(ImageRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(SlideshowImageRule::create()->matchesContext(Slideshow::create()));
        $this->assertTrue(EmphasizedRule::create()->matchesContext(Paragraph::create()));
        $this->assertTrue(InstantArticleRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(SlideshowRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(FooterRelatedArticlesRule::create()->matchesContext(Footer::create()));
        $this->assertTrue(InteractiveInsideParagraphRule::create()->matchesContext(Paragraph::create()));
        $this->assertTrue(SponsorRule::create()->matchesContext(Header::create()));
        $this->assertTrue(FooterRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(InteractiveRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(FooterSmallRule::create()->matchesContext(Footer::create()));
        $this->assertTrue(ItalicRule::create()->matchesContext(Paragraph::create()));
        $this->assertTrue(TimeRule::create()->matchesContext(Header::create()));
        $this->assertTrue(GeoTagRule::create()->matchesContext(Image::create()));
        $this->assertTrue(LineBreakRule::create()->matchesContext(Paragraph::create()));
        $this->assertTrue(VideoRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(GlobalRule::create()->matchesContext(InstantArticle::create()));
        $this->assertTrue(ListElementRule::create()->matchesContext(InstantArticle::create()));
    }
}
