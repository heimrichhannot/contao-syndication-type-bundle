<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use Contao\Config;
use Contao\CoreBundle\Exception\ResponseException;
use DateTime;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Exception;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\AbstractSyndicationType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Contracts\Translation\TranslatorInterface;

class IcsExportSyndication extends AbstractSyndicationType
{
    const PARAM = 'ical';

    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * IcalExportSyndication constructor.
     */
    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public static function getType(): string
    {
        return 'ics';
    }

    public function getCategory(): string
    {
        return static::CATEGORY_EXPORT;
    }

    public function getPalette(): string
    {
        return 'syndicationIcsLocationField,syndicationIcsStartDateField,syndicationIcsEndDateField,syndicationIcsAddTime';
    }

    public function generate(SyndicationLinkContext $context): SyndicationLink
    {
        if ($context->getConfiguration()['id'] == $this->requestStack->getMasterRequest()->get(static::PARAM)) {
            $data = [
                'title' => $context->getTitle(),
                'description' => $context->getContent(),
                'location' => $this->getValueByFieldOption($context, 'syndicationIcsLocationField'),
                'startDate' => $this->getValueByFieldOption($context, 'syndicationIcsStartDateField'),
                'endDate' => $this->getValueByFieldOption($context, 'syndicationIcsEndDateField'),
            ];

            if (isset($context->getConfiguration()['syndicationIcsAddTime']) && true === (bool) $context->getConfiguration()['syndicationIcsAddTime']) {
                if (!isset($context->getConfiguration()['syndicationIcsAddTimeField']) || empty($context->getConfiguration()['syndicationIcsAddTimeField'])) {
                    $data['addTime'] = true;
                } else {
                    $data['addTime'] = (bool) $this->getValueByFieldOption($context, 'syndicationIcsAddTimeField', false);
                }
                $data['startTime'] = $this->getValueByFieldOption($context, 'syndicationIcsStartTimeField');
                $data['endTime'] = $this->getValueByFieldOption($context, 'syndicationIcsEndTimeField');
            }

            $this->exportIcsFile($this->generateIcalFile($context->getUrl(), $data));
        }

        return new SyndicationLink(
            ['application ics'],
            $this->appendGetParameterToUrl($context->getUrl(), static::PARAM, (string) $context->getConfiguration()['id']),
            $this->translator->trans('huh.syndication_type.types.ical.title'),
            [
                'class' => 'ics',
                'rel' => 'nofollow',
                'title' => $this->translator->trans('huh.syndication_type.types.ical.title'),
            ]
        );
    }

    public function generateIcalFile(string $url, array $data): string
    {
        if (!class_exists('Eluceo\iCal\Component\Calendar')) {
            throw new Exception('The composer package eluceo/ical in not installed or not installed in it\'s correct version.');
        }

        $addTime = $data['addTime'] ?? false;
        $end = null;

        if ($addTime && isset($data['startTime']) && $data['startTime']) {
            $start = (new DateTime())->setTimestamp($data['startTime']);
        } else {
            $start = (new DateTime())->setTimestamp($data['startDate']);
            $start->setTime(0, 0, 0);
        }

        if (isset($data['endDate']) && $data['endDate']) {
            // workaround for allday events
            $end = (new DateTime())->setTimestamp($data['endDate']);
            $end->setTime(0, 0, 0);
        }

        if ($addTime && isset($data['endTime']) && $data['endTime']) {
            $end = (new DateTime())->setTimestamp($data['endTime']);
        }

        // create an event
        $event = new Event();

        $event->setNoTime(!$addTime);
        $event->setDtStart($start);

        if (null !== $end) {
            $event->setDtEnd($end);
        }

        if (isset($data['title']) && $data['title']) {
            $event->setSummary(strip_tags($data['title']));
        }

        if (isset($data['description']) && $data['description']) {
            // preserve linebreaks
            $description = preg_replace('@<br\s*/?>@i', "\n", $data['description']);
            $description = preg_replace('@</p>\s*<p>@i', "\n\n", $description);
            $description = str_replace(['<p>', '</p>'], '', $description);

            $event->setDescription(strip_tags($description));
        }

        // compose location out of various fields
        $locationData = [];

        if (isset($data['location']) && $data['location']) {
            $locationData['location'] = $data['location'];
        }

        if (isset($data['street']) && $data['street']) {
            $locationData['street'] = $data['street'];
        }

        if (isset($data['postal']) && $data['postal']) {
            $locationData['postal'] = $data['postal'];
        }

        if (isset($data['city']) && $data['city']) {
            $locationData['city'] = $data['city'];
        }

        if (isset($data['country']) && $data['country']) {
            $locationData['country'] = $data['country'];
        }

        if (!empty($locationData)) {
            $result = [];

            if (isset($locationData['location'])) {
                $result[] = $locationData['location'];
            }

            if (isset($locationData['street'])) {
                $result[] = $locationData['street'];
            }

            if (isset($locationData['postal']) && isset($locationData['city'])) {
                $result[] = $locationData['postal'].' '.$locationData['city'];
            } elseif (isset($locationData['city'])) {
                $result[] = $locationData['city'];
            }

            if (isset($locationData['country'])) {
                $result[] = $locationData['country'];
            }

            $event->setLocation(implode(', ', $result));
        }

        if (isset($data['url']) && $data['url']) {
            $event->setUrl(strip_tags($data['url']));
        }

        // create a calendar
        $calendar = new Calendar($url);

        $calendar->setTimezone(Config::get('timeZone'));
        $calendar->addComponent($event);

        return $calendar->render();
    }

    protected function exportIcsFile(string $icsSource)
    {
        $response = new Response($icsSource);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'cal.ics'
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');

        throw new ResponseException($response);
    }
}
