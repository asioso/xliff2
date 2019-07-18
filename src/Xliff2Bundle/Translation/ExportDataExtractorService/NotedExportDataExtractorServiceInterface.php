<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle\Translation\ExportDataExtractorService;


use Pimcore\Translation\AttributeSet\AttributeSet;
use Pimcore\Translation\ExportDataExtractorService\DataExtractor\DataExtractorInterface;
use Pimcore\Translation\ExportDataExtractorService\ExportDataExtractorServiceInterface;
use Pimcore\Translation\TranslationItemCollection\TranslationItem;

class NotedExportDataExtractorServiceInterface implements ExportDataExtractorServiceInterface
{
    /**
     * @var DataExtractorInterface[]
     */
    private $dataExtractors;

    public function extract(TranslationItem $translationItem, string $sourceLanguage, array $targetLanguages): AttributeSet
    {
        return $this->getDataExtractor($translationItem->getType())->extract($translationItem, $sourceLanguage, $targetLanguages);
    }

    /**
     * @param DataExtractorInterface $dataExtractor
     *
     * @return DataExtractorInterface
     *
     * @throws \Exception
     */
    public function getDataExtractor(string $type): DataExtractorInterface
    {
        if (isset($this->dataExtractors[$type])) {
            return $this->dataExtractors[$type];
        }

        throw new \Exception(sprintf('no data extractor for type "%s" registered', $type));
    }

    /**
     * @param DataExtractorInterface $dataExtractor
     *
     * @return $this
     */
    public function registerDataExtractor(string $type, DataExtractorInterface $dataExtractor): ExportDataExtractorServiceInterface
    {
        $this->dataExtractors[$type] = $dataExtractor;

        return $this;
    }
}