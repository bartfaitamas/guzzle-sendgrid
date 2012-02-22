<?php

namespace Guzzle\SendGrid\Command;

/**
 * Dealing with POST request against the sendgrid API.
 */
class SendGridPostCommand extends SendGridCommand
{

    protected function process()
    {
        parent::process();
        if ("SUCCESS" !== strtoupper($this->result['message'])) {
            throw new \RuntimeException($this->result['message']);
        }
    }

    protected function build()
    {
        parent::build();
        // sendgrid requires content-length even if there is no content
        if (!$this->request->hasHeader('Content-Length')) {
            $this->request->setHeader('Content-Length', 0);
        }
    }

}