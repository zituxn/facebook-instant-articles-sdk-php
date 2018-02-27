<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Settings;

use Facebook\InstantArticles\Validators\Type;

class AdSettings
{
    /**
     * @var string $audienceNetworkPlacementId AN representational ID
     */
    private $audienceNetworkPlacementId;

    /**
     * @var string $rawHTML The raw HTML content for ADS to be inserted into Instant Articles
     */
    private $rawHTML;

    public function __construct($audienceNetworkPlacementId = "", $rawHTML = "")
    {
        if (Type::enforce($audienceNetworkPlacementId, Type::STRING) &&
            !Type::isTextEmpty($audienceNetworkPlacementId)) {
            $this->audienceNetworkPlacementId = $audienceNetworkPlacementId;
        }
        if (Type::enforce($rawHTML, Type::STRING) &&
            !Type::isTextEmpty($rawHTML)) {
            $this->rawHTML = $rawHTML;
        }
    }

    /**
     * @return string Returns the previous set raw HTML content;
     */
    public function getRawHTML()
    {
        return $this->rawHTML;
    }

    /**
     * @return string Returns the previous set AN ID.
     */
    public function getAudienceNetworkPlacementId()
    {
        return $this->audienceNetworkPlacementId;
    }
}
