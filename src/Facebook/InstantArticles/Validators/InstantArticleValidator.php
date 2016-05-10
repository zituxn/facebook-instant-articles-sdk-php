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
 * Class that navigates thru InstantArticle object tree to validate it and report
 * warnings related to each object tree.
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
        $children = $article->getContainerChildren();
        var_dump($children);
    }
}
