<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Validators;

/**
 * Class that have all the typechecks and sizechecks for elements and classes
 * that needs to be well checked.
 *
 * is*() prefixed methods return boolean
 *
 * enforce*() prefixed methods return true for success and throw
 * InvalidArgumentException for the invalid cases.
 */
class InstantArticleValidation
{

    /**
     * This method navigates thru the tree structure and validates the article content.
     *
     * @param InstantArticle $article The article that will be checked
     */
    public static function check($article)
    {
        Type::enforce($article, Article::getClassName());
        $header = $article->getHeader();
        $children = $article->getChildren();
        $footer = $article->getFooter();
        var_dump($header->isValid());
    }
}
