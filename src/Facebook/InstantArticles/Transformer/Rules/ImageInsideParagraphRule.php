<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;
use Facebook\InstantArticles\Elements\Paragraph;

class ImageInsideParagraphRule extends ImageRule
{
    public function getContextClass()
    {
        return Paragraph::getClassName();
    }
}
