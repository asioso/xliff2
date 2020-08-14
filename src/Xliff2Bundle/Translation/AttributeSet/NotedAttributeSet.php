<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle\Translation\AttributeSet;

use Pimcore\Model\Element\ElementInterface;
use Pimcore\Translation\AttributeSet\Attribute;
use Pimcore\Translation\AttributeSet\AttributeSet;
use Pimcore\Translation\TranslationItemCollection\TranslationItem;

/**
 * Class NotedAttributeSet
 * @package Xliff2Bundle\Translation\AttributeSet
 */
class NotedAttributeSet extends AttributeSet
{

    private $notes = [];
    /**
     * @var AttributeSet
     */
    private $set;

    /**
     * DataExtractorResult constructor.
     *
     * @param AttributeSet $set
     */
    public function __construct(AttributeSet $set)
    {
        parent::__construct($set->getTranslationItem());

        $this->set = $set;
    }

    /**
     * @return TranslationItem
     */
    public function getTranslationItem(): TranslationItem
    {
        return $this->set->getTranslationItem();
    }

    /**
     * @param ElementInterface $translationItem
     *
     * @return AttributeSet
     */
    public function setTranslationItem(ElementInterface $translationItem): AttributeSet
    {
        $this->set->setTranslationItem($translationItem);

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceLanguage(): string
    {
        return $this->set->getSourceLanguage();
    }

    /**
     * @param string $sourceLanguage
     *
     * @return AttributeSet
     */
    public function setSourceLanguage(string $sourceLanguage): AttributeSet
    {
        $this->set->setSourceLanguage($sourceLanguage);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTargetLanguages(): array
    {
        return $this->set->getTargetLanguages();
    }

    /**
     * @param string[] $targetLanguages
     *
     * @return AttributeSet
     */
    public function setTargetLanguages(array $targetLanguages): AttributeSet
    {
        $this->set->setTargetLanguages($targetLanguages);

        return $this;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->set->getAttributes();
    }

    public function isEmpty(): bool
    {
        return $this->set->isEmpty();
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $content
     * @param bool $isReadonly
     *
     * @param array $targetContent
     * @return AttributeSet
     */
    public function addAttribute(string $type, string $name, string $content, bool $isReadonly = false, array $targetContent = []): AttributeSet
    {
        $this->set->addAttribute($type, $name, $content, $isReadonly, $targetContent);

        return $this;
    }

    /**
     * @return array
     */
    public function getNotes(): array
    {
        return $this->notes;
    }

    /**
     * @param string $note
     */
    public function addNote(string $note)
    {
        $this->notes[] = $note;
    }
}
