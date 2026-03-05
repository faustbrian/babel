<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Cline\CodingStandard\Rector\Factory;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
use RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector;
use RectorLaravel\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector;
use RectorLaravel\Rector\If_\ThrowIfRector;
use RectorLaravel\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector;

return Factory::create(
    paths: [__DIR__.'/src', __DIR__.'/tests'],
    skip: [
        RemoveUnreachableStatementRector::class => [__DIR__.'/tests'],
        PrivatizeFinalClassMethodRector::class => [__DIR__.'/src/Encodings/ForceUtf8.php'],
        PrivatizeFinalClassPropertyRector::class => [__DIR__.'/src/Encodings/ForceUtf8.php'],
        SubStrToStartsWithOrEndsWithStaticMethodCallRector::class => [__DIR__.'/src/Encodings/ForceUtf8.php'],
        ThrowIfAndThrowUnlessExceptionsToUseClassStringRector::class => [__DIR__.'/tests/Unit/ForceUtf8CompatibilityTest.php'],
        ThrowIfRector::class => [__DIR__.'/tests/Unit/ForceUtf8CompatibilityTest.php'],
        ContainerBindConcreteWithClosureOnlyRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
    ],
);
