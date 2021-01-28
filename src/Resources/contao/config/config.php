<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use HeimrichHannot\SyndicationTypeBundle\EventListener\Contao\LoadDataContainerListener;

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = [LoadDataContainerListener::class, '__invoke'];
