<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle\Translation\Escaper;



class Xliff20Escaper
{
    const SELFCLOSING_TAGS = ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
    const CONTENT = "content";
    const ORIGINAL_DATA = "originalData";
    const SEGMENTATION = "segmentation";
    /**
     * @var array
     */
    private $dataRef;


    /**
     * @param string $content
     *
     * @param $unitId
     * @return array
     */
    public function escapeXliff(string $content, $unitId): array
    {
        $count = 1;
        $openTags = [];
        $final = [];

        $originalData = array();
        $originalRefCounter = 0;
        $segmentation = array();

        // remove nasty device control characters
        $content = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        $replacement = ['%_%_%lt;%_%_%', '%_%_%gt;%_%_%'];
        $content = str_replace(['&lt;', '&gt;'], $replacement, $content);
        $content = html_entity_decode($content, null, 'UTF-8');

        if (!preg_match_all('/<([^>]+)>([^<]+)?/', $content, $matches)) {
            // return original content if it doesn't contain HTML tags
            return [
                self::CONTENT => $this->toCData($content),
                self::ORIGINAL_DATA => $originalData,
                self::SEGMENTATION => array(array('ignorable' => false, "part" => $this->toCData($content))),
            ];
        }

        // Handle text before the first HTML tag
        $firstTagPosition = strpos($content, '<');
        $preText = ($firstTagPosition > 0) ? $this->toCData(substr($content, 0, $firstTagPosition)) : '';


        foreach ($matches[0] as $match) {

            $parts = explode('>', $match);
            $parts[0] .= '>';
            foreach ($parts as $part) {

                if (!empty(trim($part))) {
                    if (preg_match("/<([a-z0-9\/]+)/", $part, $tag)) {
                        $tagName = str_replace('/', '', $tag[1]);
                        if (in_array($tagName, self::SELFCLOSING_TAGS)) {
                            $originalData[$unitId . '_' . $originalRefCounter] = $part;
                            $part = '<ph id="' . $count . '" dataRef="' . $unitId . '_' . $originalRefCounter . '"/>';

                            $originalRefCounter++;
                            $count++;
                            $segmentation[] = array('ignorable' => true, "part" => $part);

                        } elseif (strpos($tag[1], '/') === false) {
                            $openTags[$count] = ['tag' => $tagName, 'id' => $count];
                            $originalData[$unitId . '_' . $originalRefCounter] = $part;

                            $part = '<sc id="' . $count . '" dataRef="' . $unitId . '_' . $originalRefCounter . '"/>';
                            $originalRefCounter++;

                            $count++;
                            $segmentation[] = array('ignorable' => true, "part" => $part);
                        } else {
                            $closingTag = array_pop($openTags);
                            $originalData[$unitId . '_' . $originalRefCounter] = $part;

                            $part = '<ec startRef="' . $closingTag['id'] . '" dataRef="' . $unitId . '_' . $originalRefCounter . '"/>';
                            $originalRefCounter++;
                            $segmentation[] = array('ignorable' => true, "part" => $part);
                        }
                    } else {

                        $part = str_replace($replacement, ['<', '>'], $part);
                        $part = $this->toCData($part);
                        if (!empty($part)) {
                            $segmentation[] = array('ignorable' => false, "part" => $part);
                        }
                    }

                    if (!empty($part)) {
                        $final[] = $part;
                    }
                }
            }
        }

        $content = $preText . implode('', $final);

        return [self::CONTENT => $content, self::ORIGINAL_DATA => $originalData, self::SEGMENTATION => $segmentation];
    }


    /**
     * @param string $content
     *
     * @param $originalData
     * @return string
     */
    public function unescapeXliff(string $content): string
    {
        $content = preg_replace("/<\/?(target|source)([^>.]+)?>/i", '', $content);
        // we have to do this again but with html entities because of CDATA content
        $content = preg_replace("/&lt;\/?(target|source)((?!&gt;).)*&gt;/i", '', $content);

        if (preg_match("/<\/?(ec|sc|ph)/", $content)) {
            include_once(PIMCORE_PATH . '/lib/simple_html_dom.php');
            $xml = str_get_html($content);
            if ($xml) {
                $els = $xml->find('sc,ec,ph');
                foreach ($els as $el) {
                    $outer = $this->dataRef[$el->attr['dataref']];
                    $el->outertext = $outer;
                }
            }
            $content = $xml->save();
        }

        return $content;
    }

    private function toCData(string $data): string
    {
        return sprintf('<![CDATA[%s]]>', $data);
    }

    public function setUpOriginalData($originalData)
    {
        $this->dataRef = array();
        if (preg_match("/<\/?(data|originalData)/", $originalData)) {
            include_once(PIMCORE_PATH . '/lib/simple_html_dom.php');
            $xml = str_get_html($originalData);
            if ($xml) {
                $els = $xml->find('data');
                foreach ($els as $el) {
                    $this->dataRef[$el->attr['id']] = html_entity_decode($el->innertext, null, 'UTF-8');

                }
            }
        }
    }
}
