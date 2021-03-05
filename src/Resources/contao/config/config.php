<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use HeimrichHannot\SyndicationTypeBundle\EventListener\Contao\CompileArticleListener;
use HeimrichHannot\SyndicationTypeBundle\EventListener\Contao\LoadDataContainerListener;
use HeimrichHannot\SyndicationTypeBundle\EventListener\Contao\ParseTemplateListener;

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = [LoadDataContainerListener::class, '__invoke'];
$GLOBALS['TL_HOOKS']['compileArticle'][] = [CompileArticleListener::class, '__invoke'];
$GLOBALS['TL_HOOKS']['parseTemplate'][] = [ParseTemplateListener::class, '__invoke'];
