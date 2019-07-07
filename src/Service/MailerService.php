<?php
/**
 * Mailer service.
 *
 * @package    InformaticsGe
 * @subpackage Service
 * @author     Squiz Pty Ltd <products@squiz.net>\
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Mailer.
 */
class MailerService
{

    /**
     * Website mailer.
     *
     * @var Swift_Mailer.
     */
    private $mailer;

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Template engine.
     *
     * @var Environment
     */
    private $twig;

    /**
     * Mailer constructor.
     *
     * @param Swift_Mailer           $mailer Site mailer.
     * @param EntityManagerInterface $em     Entity Manager.
     * @param Environment            $twig   Template engine.
     */
    public function __construct(Swift_Mailer $mailer, EntityManagerInterface $em, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * Send email.
     *
     * @param array       $to        Recipient email(s).
     * @param string      $subject   Email subject.
     * @param string      $template  Twig template name.
     * @param string|null $from      Receiver email.
     * @param array|null  $variables Variables for template engine.
     *
     * @return boolean Sending status.
     * @throws LoaderError  Twig LoaderError.
     * @throws RuntimeError Twig RuntimeError.
     * @throws SyntaxError  Twig SyntaxError.
     */
    public function sendMessage(
        array $to,
        string $subject,
        string $template,
        array $variables = [],
        string $from = null
    ): bool
    {
        $message = new Swift_Message($subject);
        $fromEmail = $from ?: getenv('SITE_MAIL');
        $message->setFrom($fromEmail)->setTo($to)->setBody(
            $this->twig->render($template, $variables),
            'text/html'
        );
        $status = $this->mailer->send($message);

        return $status;
    }

}