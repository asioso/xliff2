<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle\Translation\ExportDataExtractorService\DataExtractor;

use Pimcore\Model\Document;
use Pimcore\Model\Element;
use Xliff2Bundle\Translation\AttributeSet\NotedAttributeSet;
use Pimcore\Translation\AttributeSet\AttributeSet;
use Pimcore\Translation\ExportDataExtractorService\DataExtractor\DataObjectDataExtractor;
use Pimcore\Translation\ExportDataExtractorService\DataExtractor\DocumentDataExtractor;
use Pimcore\Translation\TranslationItemCollection\TranslationItem;

/**
 * Class NotedDocumentDataExtractor
 * @package Xliff2Bundle\Translation\ExportDataExtractorService\DataExtractor
 */
class NotedDocumentDataExtractor extends DocumentDataExtractor
{


    /**
     * @param Document $document
     * @param string $sourceLanguage
     * @param string[] $targetLanguages
     *
     * @return AttributeSet
     *
     * @throws \Exception
     */
    public function extract(TranslationItem $translationItem, string $sourceLanguage, array $targetLanguages): AttributeSet
    {
        $document = $translationItem->getElement();

        $result = parent::extract($translationItem, $sourceLanguage, $targetLanguages);

        if (!$document instanceof Document) {
            throw new \Exception('only documents allowed');
        }

        $this
            ->addDoumentTags($document, $result)
            ->addSettings($document, $result);

        $result = new NotedAttributeSet($result);
        $this->getNotes($translationItem->getElement(), $result);

        return $result;
    }


    /**
     * @param $object
     * @param NotedAttributeSet $result
     * @return DataObjectDataExtractor
     * @throws \Exception
     */
    private function getNotes($object, NotedAttributeSet $result)
    {

        if (!$object instanceof Document) {
            throw new \Exception('only documents allowed');
        }

        $notes = $this->getNotesForElement($object->getId());
        foreach ($notes as $note) {
            $result->addNote($this->noteToText($note));
        }

    }

    /**
     * @param $id
     * @return array
     */
    private function getNotesForElement($id): array
    {
        $noteList = new Element\Note\Listing();
        $noteList->addConditionParam('(cid = ?) AND (type = ?)', [$id, 'translation']);
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');
        $noteList->setLimit(5);

        $notes = $noteList->load();

        $translationNotes = array();
        foreach ($notes as $note) {
            $translationNotes[] = $note;
        }

        return $translationNotes;
    }

    /**
     * @param Element\Note $note
     * @return string
     */
    private function noteToText(Element\Note $note)
    {
        return date("Y-m-d H:i:s", $note->getDate()) . ": " . $note->getDescription();
    }
}