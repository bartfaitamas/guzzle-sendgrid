<?php

namespace Guzzle\SendGrid\Command;

use Guzzle\Service\Command\DynamicCommand;

/**
 *
 */
class SendGridCommand extends DynamicCommand
{
    public static function dateFormat($date)
    {
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }
        return $date;
    }


    /**
     * SendGrid response content type is text/html for every request, we have to figure
     * out the content type from the request uri.
     */
    protected function process()
    {
        $request = $this->getRequest();

        $this->result = $request->getResponse();

        $path = $request->getPath();
        if ('json' === substr($path, -4)) {
           $body = trim($this->result->getBody(true));
            if ($body) {
                $this->result = json_decode($body, true);
            }
        } else {
            throw new \DomainException("Unknown format");
        }
    }

}