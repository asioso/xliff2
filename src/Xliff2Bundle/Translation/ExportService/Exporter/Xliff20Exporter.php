<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle\Translation\ExportService\Exporter;

use Xliff2Bundle\Translation\AttributeSet\NotedAttributeSet;
use Xliff2Bundle\Translation\Escaper\Xliff20Escaper;
use Pimcore\File;
use Pimcore\Translation\AttributeSet\AttributeSet;
use Pimcore\Translation\ExportService\Exporter\ExporterInterface;

/**
 * Class Xliff20Exporter
 * @package Xliff2Bundle\Translation\ExportService\Exporter
 */
class Xliff20Exporter implements ExporterInterface
{

    const DELIMITER = '---';

    /**
     * @var Xliff20Escaper
     */
    private $xliff20Escaper;

    public function __construct(Xliff20Escaper $xliff20Escaper)
    {
        $this->xliff20Escaper = $xliff20Escaper;
    }


    /**
     * @param AttributeSet $attributeSet
     * @param string|null $exportId
     *
     * @return string
     */
    public function export(AttributeSet $attributeSet, string $exportId = null): string
    {
        $files = array();

        $exportId = $exportId ?: uniqid();

        foreach ($attributeSet->getTargetLanguages() as $targetLanguage) {


            $exportFile = $this->getTmpFilePath($exportId);

            if ($attributeSet->isEmpty()) {
                $files[] = $exportFile;
                continue;
            }

            $xliff = simplexml_load_file($exportFile, null, LIBXML_NOCDATA);

            try {
                $xliff->addAttribute('srcLang', $attributeSet->getSourceLanguage());
                //should be only one!
                $xliff->addAttribute('trgLang', $targetLanguage);

            } catch (\Exception $e) {
                //
            }

            $file = $xliff->addChild('file');
            //$file->addAttribute('origin', 'pimcore');
            //$file->addAttribute('category', $attributeSet->getTranslationItem()->getType());
            $file->addAttribute('original', $attributeSet->getTranslationItem()->getType() . '-' . $attributeSet->getTranslationItem()->getId());
            $file->addAttribute('id', $attributeSet->getTranslationItem()->getType() . '-' . $attributeSet->getTranslationItem()->getId());


            foreach ($attributeSet->getAttributes() as $attribute) {
                if ($attribute->isReadonly()) {
                    continue;
                }
                $this->addUnitNode($file, $attribute->getType() . self::DELIMITER . $attribute->getName(), $attribute->getContent(), $attributeSet->getSourceLanguage(), uniqid(), $attributeSet);
            }

            $xliff->asXML($exportFile);

            $files[] = $exportFile;
        }

        return $files[0];
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param $name
     * @param $content
     * @param $source
     * @param int $unitId
     * @param $attributeSet
     */
    protected function addUnitNode(\SimpleXMLElement $xml, $name, $content, $source, $unitId, $attributeSet)
    {
        $unit = $xml->addChild('unit');
        $unit->addAttribute('id', htmlentities($name));

        $escaped = $this->xliff20Escaper->escapeXliff($content, $unitId);
        $this->appendOriginalData($unit, $escaped[Xliff20Escaper::ORIGINAL_DATA]);
        $c = 0;
        foreach ($escaped[Xliff20Escaper::SEGMENTATION] as $segment) {
            if ($segment['ignorable'] === false) {
                $parent = $unit->addChild('segment');
            } else {
                $parent = $unit->addChild('ignorable');
            }

            $parent->addAttribute('id', "s_" . $unitId . '_' . $c++);
            $sourceNode = $parent->addChild('source');
            $sourceNode->addAttribute('xmlns:xml:lang', $source);

            $node = dom_import_simplexml($sourceNode);
            $no = $node->ownerDocument;
            $f = $no->createDocumentFragment();
            $f->appendXML($segment['part']);

            @$node->appendChild($f);
        }

        if ($attributeSet instanceof NotedAttributeSet) {
            $notes = $attributeSet->getNotes();
            if (!empty($notes)) {
                $this->appendNotes($unit, $notes);
            }
        }

    }


    /**
     * @param string $exportId
     *
     * @return string
     */
    public function getExportFilePath(string $exportId): string
    {
        $exportFile = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/' . $exportId . '.xliff';

        return $exportFile;
    }

    private function getTmpFilePath(string $exportId): string
    {
        $exportFile = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/' . $exportId . '.xliff';
        if (!is_file($exportFile)) {
            // create initial xml file structure
            File::put($exportFile, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0"></xliff>');
        }

        return $exportFile;
    }


    /**
     * @param $files
     * @param string $exportId
     * @return string
     */
    public function zipFiles($files, string $exportId)
    {

        $zip = new \ZipArchive();
        $path = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/' . $exportId . '.zip';
        if ($zip->open($path, \ZipArchive::CREATE) === TRUE) {
            // Add files to the zip file
            foreach ($files as $file) {
                $zip->addFile($file);
            }

            // All files are added, so close the zip file.
            $zip->close();
        }

        return $path;

    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        //return 'application/x-xliff+xml';
        return 'application/zip';

    }

    private function appendOriginalData(\SimpleXMLElement $unit, array $dataList)
    {
        if (!empty($dataList)) {
            $original = $unit->addChild('originalData');
            foreach ($dataList as $id => $data) {
                $this->appendData($original, $id, $data);
            }
        }

    }

    private function appendData(\SimpleXMLElement $original, $id, $data)
    {
        $node = $original->addChild('data');
        $node->addAttribute('id', $id);
        $node = dom_import_simplexml($node);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($data));

    }

    private function appendNotes(\SimpleXMLElement $unit, array $notes)
    {
        $notesNode = $unit->addChild('notes');

        foreach ($notes as $note) {
            $node = $notesNode->addChild('note');
            $node->addAttribute('appliesTo', 'source');
            $node = dom_import_simplexml($node);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection($note));
        }

    }
}