<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Matcher;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Criteria\Criteria;

final class ArticleCriteriaMatcher implements ArticleCriteriaMatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function match(ArticleInterface $article, Criteria $criteria)
    {
        if (0 === $criteria->count()) {
            return false;
        }

        if ($criteria->has('route')) {
            foreach ($criteria->get('route') as $value) {
                if (null !== $article->getRoute() && (int) $value !== $article->getRoute()->getId()) {
                    return false;
                }
            }
        }

        if ($criteria->has('author') && is_array($criteria->get('author'))) {
            foreach ($criteria->get('author') as $key => $value) {
                if ($value !== $article->getMetadataByKey('byline')) {
                    return false;
                }
            }
        }

        if ($criteria->has('publishedBefore')) {
            $publishedBefore = new \DateTime($criteria->get('publishedBefore'));
            if ($article->getPublishedAt() > $publishedBefore) {
                return false;
            }
        }

        if ($criteria->has('publishedAfter')) {
            $publishedAfter = new \DateTime($criteria->get('publishedAfter'));
            if ($article->getPublishedAt() < $publishedAfter) {
                return false;
            }
        }

        if ($criteria->has('publishedAt')
            && (!$criteria->has('publishedBefore') || !$criteria->has('publishedAfter'))) {
            $publishedAt = new \DateTime($criteria->get('publishedAt'));
            if ($publishedAt->format('d-m-Y') !== $article->getPublishedAt()->format('d-m-Y')) {
                return false;
            }
        }

        if ($criteria->has('metadata') && empty($criteria->get('metadata'))) {
            return false;
        }

        foreach ((array) $criteria->get('metadata') as $key => $value) {
            if ($value !== $article->getMetadataByKey($key)) {
                return false;
            }
        }

        return true;
    }
}
