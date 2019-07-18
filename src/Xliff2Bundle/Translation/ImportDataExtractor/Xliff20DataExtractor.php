<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle\Translation\ImportDataExtractor;


use Xliff2Bundle\Translation\Escaper\Xliff20Escaper;
use Xliff2Bundle\Translation\ExportService\Exporter\Xliff20Exporter;
use Pimcore\Tool;
use Pimcore\Translation\AttributeSet\AttributeSet;
use Pimcore\Translation\ExportService\Exporter\Xliff12Exporter;
use Pimcore\Translation\ImportDataExtractor\ImportDataExtractorInterface;
use Pimcore\Translation\ImportDataExtractor\TranslationItemResolver\TranslationItemResolver;
use Pimcore\Translation\ImportDataExtractor\TranslationItemResolver\TranslationItemResolverInterface;

class Xliff20DataExtractor implements ImportDataExtractorInterface
{

    /**
     * @var Xliff20Escaper
     */
    protected $xliffEscaper;

    /**
     * @var TranslationItemResolver
     */
    protected $translationItemResolver;

    public function __construct(Xliff20Escaper $xliffEscaper, TranslationItemResolverInterface $translationItemResolver)
    {
        $this->xliffEscaper = $xliffEscaper;
        $this->translationItemResolver = $translationItemResolver;
    }


    /**
     * @param string $importId
     * @param int $stepId
     *
     * @return ?AttributeSet
     *
     * @throws \Exception
     */
    public function extractElement(string $importId, int $stepId): AttributeSet
    {
        $xliff = $this->loadFile($importId);
        $file = $xliff->file[$stepId];

        $target = $xliff['trgLang'];

        // see https://en.wikipedia.org/wiki/IETF_language_tag
        $target = str_replace('-', '_', $target);
        if (!Tool::isValidLanguage($target)) {
            $target = \Locale::getPrimaryLanguage($target);
        }
        if (!Tool::isValidLanguage($target)) {
            throw new \Exception(sprintf('invalid language %s', $file['target-language']));
        }

        list($type, $id) = explode('-', $file['original']);

        $translationItem = $this->translationItemResolver->resolve($type, $id);


        if (empty($translationItem)) {
            throw new \Exception('Could not resolve element ' . $file['original']);
        }

        $attributeSet = new AttributeSet($translationItem);
        $attributeSet->setTargetLanguages([$target]);
        if (!empty($xliff['srcLang'])) {
            $attributeSet->setSourceLanguage($xliff['srcLang']);
        }


        foreach ($file->unit as $transUnit) {
            list($type, $name) = explode(Xliff20Exporter::DELIMITER, $transUnit['id']);
            $allContent = "";
            $this->xliffEscaper->setUpOriginalData($transUnit->originalData->asXML());
            foreach ($transUnit->children() as $child) {

                if ($child->getName() == 'segment') {
                    $content = $child->target->asXml();
                } elseif ($child->getName() == 'ignorable') {
                    $content = $child->source->asXml();
                } else {
                    continue;
                }

                $content = $this->xliffEscaper->unescapeXliff($content);
                $allContent = $allContent . "\r\n" . $content;
            }

            $attributeSet->addAttribute($type, $name, $allContent);
        }

        return $attributeSet;
    }

    /**
     * @param string $importId
     *
     * @return int
     *
     * @throws \Exception
     */
    public function countSteps(string $importId): int
    {
        $xliff = $this->loadFile($importId);

        return count($xliff->file);
    }

    /**
     * @param string $importId
     *
     * @return \SimpleXMLElement
     *
     * @throws \Exception
     */
    private function loadFile(string $importId): \SimpleXMLElement
    {
        return simplexml_load_file($this->getImportFilePath($importId), null, LIBXML_NOCDATA);
    }

    /**
     * @param string $importId
     *
     * @return string
     */
    public function getImportFilePath(string $importId): string
    {
        return PIMCORE_SYSTEM_TEMP_DIRECTORY . '/' . $importId . '.xliff';
    }
}