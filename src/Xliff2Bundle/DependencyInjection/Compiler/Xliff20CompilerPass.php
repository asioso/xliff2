<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class Xliff20CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('Pimcore\Translation\ExportService\Exporter\ExporterInterface');
        $definition->setClass('Xliff2Bundle\Translation\ExportService\Exporter\Xliff20Exporter');

        $definition = $container->getDefinition('Pimcore\Translation\ImportDataExtractor\ImportDataExtractorInterface');
        $definition->setClass('Xliff2Bundle\Translation\ImportDataExtractor\Xliff20DataExtractor');

        $definition = $container->getDefinition('Pimcore\Translation\ExportDataExtractorService\DataExtractor\DataObjectDataExtractor');
        $definition->setClass('Xliff2Bundle\Translation\ExportDataExtractorService\DataExtractor\NotedDataObjectDataExtractor');

        $definition = $container->getDefinition('Pimcore\Translation\ExportDataExtractorService\DataExtractor\DocumentDataExtractor');
        $definition->setClass('Xliff2Bundle\Translation\ExportDataExtractorService\DataExtractor\NotedDocumentDataExtractor');

        $definition = $container->getDefinition('Pimcore\Translation\ExportDataExtractorService\ExportDataExtractorServiceInterface');
        $definition->setClass('Xliff2Bundle\Translation\ExportDataExtractorService\NotedExportDataExtractorServiceInterface');

    }
}
