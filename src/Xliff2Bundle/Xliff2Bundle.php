<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace Xliff2Bundle;

use Xliff2Bundle\DependencyInjection\Compiler\Xliff20CompilerPass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AmazonMarketplaceBundle
 * @package AmazonMarketplaceBundle
 */
class Xliff2Bundle extends AbstractPimcoreBundle implements PimcoreBundleInterface
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new Xliff20CompilerPass());
    }

    public function getNiceName()
    {
        return 'Asioso - Xliff 2.0 Bundle';
    }

    /**
     * Bundle description as shown in extension manager
     *
     * @return string
     */
    public function getDescription()
    {
        return "";
    }

    public function getVersion()
    {
        return 'v1.0';
    }

    public static function getSolutionVersion()
    {
        return "v1.0";
    }

}
