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
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Xliff2Bundle extends AbstractPimcoreBundle implements PimcoreBundleInterface
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'asioso/pimcore-xliff2_0-module';

    protected function getComposerPackageName()
    {
        return self::PACKAGE_NAME;
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new Xliff20CompilerPass());
    }

    public function getNiceName()
    {
        return 'Asioso - Xliff 2.0 Bundle';
    }

    public static function getSolutionVersion()
    {
        return $this->getVersion();
    }
}
